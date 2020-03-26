<?php

namespace App\Modules\Event\Http\Controllers;

use Illuminate\Http\Request;
use \Stripe\Stripe;
use Illuminate\Support\Facades\Log;
use App\Modules\Event\Models\Event;
use App\Modules\Event\Models\Registration;
use App\Modules\Event\Services\EventService;

class RegistrationController extends \App\Http\Controllers\Controller
{
    public function store(Request $request, EventService $es)
    {
        
        //header('Content-Type: application/json');

        $token = $request->get('payment_id');
        $total = $request->get('total');
        $eventId = $request->get('event_id');
        $firstName = $request->get('reg_first_name');
        $lastName = $request->get('reg_last_name');
        $email = $request->get('reg_email');
        $phone = $request->get('reg_phone');
        //$cardName = $request->get('card_name');

		$registration = $es->createEventRegistration($eventId, $total, $firstName, $lastName, $email, $phone, $token);

		flash()->success('Registration complete. Confirmation e-mail sent to '.$email.'.');


        return redirect()->back();
    }

    private function generateResponse($intent) {
	    # Note that if your API version is before 2019-02-11, 'requires_action'
	    # appears as 'requires_source_action'.
	    if ($intent->status == 'requires_action' &&
	        $intent->next_action->type == 'use_stripe_sdk') {
	      # Tell the client to handle the action
	      echo json_encode([
	        'requires_action' => true,
	        'payment_intent_client_secret' => $intent->client_secret
	      ]);
	    } else if ($intent->status == 'succeeded') {
	      # The payment didn’t need any additional actions and completed!
	      # Handle post-payment fulfillment
	      echo json_encode([
	        "success" => true
	      ]);
	    } else {
	      # Invalid status
	      http_response_code(500);
	      echo json_encode(['error' => 'Invalid PaymentIntent status']);
	    }
	  }
}
