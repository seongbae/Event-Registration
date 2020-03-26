<?php

namespace App\Modules\Event\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Modules\Event\DataTables\EventsDataTable;
use App\Modules\Event\DataTables\RegistrationsDataTable;
use App\Modules\Event\Models\Event;
use App\Traits\UploadTrait;
use Illuminate\Support\Str;
use App\Modules\Event\Services\EventService;

class EventController extends \App\Http\Controllers\Controller
{
    use UploadTrait;

    protected $eventService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(EventService $eventService)
    {
        $this->middleware('auth');
        $this->eventService = $eventService;
    }   

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(EventsDataTable $datatable)
    {
        return $datatable->render('admin.events.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.events.create')
                ->with('event', null);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $name = $request->get('name');
        $description = $request->get('description');
        $address = $request->get('address');
        $price = $request->get('price');
        $externalLink = $request->get('external_link');
        $datetime = $request->get('datetime');
        $image = $request->file('image_url');

        $event = $this->eventService->create($name, $description, $address, $price, $externalLink, $datetime, $image);

        if ($event)
            flash()->success('Event saved');
        else
            flash()->error('Event could not be saved');

        return redirect()->to('/admin/events');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return view('events.show');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit')->with('event', $event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Event $event)
    {
        $event->name = $request->get('name');
        $event->description = $request->get('description');
        $event->address = $request->get('address');
        $event->price = $request->get('price');
        $event->external_link = $request->get('external_link');
        $event->datetime = $request->get('datetime');

        if ($request->has('image_url')) {
            // Get image file
            $image = $request->file('image_url');
            // Make a image name based on user name and current timestamp
            $name = Str::slug($request->input('name')).'_'.time();
            // Define folder path
            $folder = '/uploads/images/';
            // Make a file path where image will be stored [ folder path + file name + file extension]
            $filePath = '/storage'.$folder . $name. '.' . $image->getClientOriginalExtension();
            // Upload image
            $this->uploadOne($image, $folder, 'public', $name);
            // Set user profile image path in database to filePath
            $event->image_url = $filePath;
        }
        
        $event->save();

        if ($request->get('tags')) {
            $event->attachTags(explode(",", $request->get('tags')));
        }

        flash()->success('Event updated');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::find($id);
        $event->delete();

        return redirect()->route('events.index');
    }

    public function showRegistrations(RegistrationsDataTable $datatable)
    {
        return $datatable->render('admin.events.registrations');
    }
}
