<?php

namespace App\Modules\Event\Services;

use App\Modules\Event\Models\Event;
use App\Modules\Event\Models\Registration;
use App\Mail\EventRegistered;
use Illuminate\Http\Request;
use App\Traits\UploadTrait;
use Illuminate\Support\Str;
use \Stripe\Stripe;
use Illuminate\Support\Facades\Mail;
use Appstract\Options\Option;
use Illuminate\Support\Facades\Log;

class EventService
{
    use UploadTrait;

	public function index()
	{
		//return Event::all();
	}

    public function create($name, $description, $address, $price, $externalLink, $datetime, $image)
	{
		$event = new Event;
        $event->name = $name;
        $event->description = $description;
        $event->address = $address;
        $event->price = $price;
        $event->external_link = $externalLink;
        $event->datetime = $datetime;

        if ($image) {
            // Make a image name based on user name and current timestamp
            $name = Str::slug($name).'_'.time();
            // Define folder path
            $folder = '/uploads/images/';
            // Make a file path where image will be stored [ folder path + file name + file extension]
            $filePath = '/storage'.$folder . $name. '.' . $image->getClientOriginalExtension();
            // Upload image
            $this->uploadOne($image, $folder, 'public', $name);
            // Set user profile image path in database to filePath
            $event->image_url = $filePath;
        }
        else
        {
            $event->image_url = '/img/event-placeholder.jpeg';
        }

        $event->save();

        return $event;
	}

	public function read($id)
	{
     return Event::find($id);
	}

	public function update(Request $request, $id)
	{
	  $attributes = $request->all();
	  
      return Event::update($id, $attributes);
	}

	public function delete($id)
	{
      return Event::delete($id);
	}

    public function createEventRegistration($eventId, $amount, $firstName, $lastName, $email, $phone, $token=null)
    {
        $event = Event::find($eventId);
        
        if (!$event->free)
        {
            if ($amount)
                $amount = $amount * 100;

            $intent = null;

            try {
                Stripe::setApiKey(option('stripe_private_key'));

                if (isset($token)) {
                  # Create the PaymentIntent
                  $intent = \Stripe\PaymentIntent::create([
                    'payment_method' => $token,
                    'amount' => $amount,
                    'currency' => 'usd',
                    'confirmation_method' => 'manual',
                    'confirm' => true,
                    'description' => 'invoice 12345'
                  ]);
                }
                
                // if (isset($token)) {
                //   $intent = \Stripe\PaymentIntent::retrieve(
                //     $token
                //   );
                //   $intent->confirm();
                // }

                Log::info('stripe success');

                // run following if transaction successful
                // -- start
                $reg = $this->createRegistration($firstName, $lastName, $email, $phone, 'paid', $event->id);

                if ($reg)
                    $this->sendEmail($email, $firstName. ' '.$lastName, $reg, $this->addressToArray(option('notification_email')));
                
            
                //$this->generateResponse($intent);
            } catch (\Stripe\Exception\ApiErrorException $e) {
                # Display error on client
                Log::info(json_encode([
                  'error' => $e->getMessage()
                ]));
            }   
        }
        else
        {
            $reg = $this->createRegistration($firstName, $lastName, $email, $phone, 'free', $event->id);

            $this->sendEmail($email, $firstName. ' '.$lastName, $reg, $this->addressToArray(option('notification_email')));
        }
    }

    private function sendEmail($email, $name, $reg, $bcc)
    {
        Mail::to([['email'=>$email,'name'=>$name]])
            ->bcc($bcc)
            ->send(new EventRegistered($reg));
    }

    private function addressToArray($emails)
    {
        if( strpos($emails, ',') !== false ) 
            return explode(",",$emails);
        elseif( strpos($emails, ';') !== false ) 
            return explode(";",$emails);
        else
            return $emails;

    }

    private function createRegistration($firstName, $lastName, $email, $phone, $status, $eventId=null)
    {
        $reg = new Registration;
        $reg->first_name = $firstName;
        $reg->last_name = $lastName;
        $reg->email = $email;
        $reg->phone = $phone;
        $reg->event_id = $eventId;
        $reg->payment_status = $status;
        $reg->save();

        return $reg;
    }

    
}