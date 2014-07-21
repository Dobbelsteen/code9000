<?php
class CalendarController extends \BaseController {

    /**
     * Display a calendar view to logged in user
     * @return Response
     */
    public function index()
    {
        if (!Sentry::check()) {
            // User is not logged in, or is not activated
            return Redirect::route('landing');
        } else {
            return View::make('calendar.index');
        }
    }

    /**
     * Return a listing of the resource.
     *
     * @return Response
     */
    public function events()
    {
        if (!Sentry::check()) {
            // User is not logged in, or is not activated
            return Redirect::route('landing');
        } else {
            // Gets all appointments from the school
            $user = Sentry::getUser();
            if ($user->hasAccess('school')) {
                $appointments = Appointment::get()->toArray();
                // Returns JSON response of the user
                return Response::json($appointments)->setCallback(Input::get('callback'));//return View::make('calendar.events');
            } else {
                $user->load('school.groups.appointments');
                // Returns JSON response of the user
                $appointments = [];
                // Loop through groups to get all appointments
                foreach ($user->school->groups as $group) {
                    foreach($group->appointments as $appointment){
                        array_push($appointments,$appointment);
                    }
                }
                return Response::json($appointments)->setCallback(Input::get('callback'));//return View::make('calendar.events');
            }

        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        if(Sentry::check()) {
            // Find active user
            $user = Sentry::getUser();
            $groups = null;
            $schoolName = null;
            // Permission checks
            if ($user->hasAnyAccess(['school','event'])) {
                if($user->hasAccess(['school'])) {
                    $groups = Group::where('school_id','<>','')->get();
                } else {
                    $user->load('school.groups.appointments');
                    $groups = $user->school->groups;
                }
                // Transform recieved objectList (from database) into array to send with view
                $smartgroup = [];
                foreach($groups as $group){
                    $smartgroup[$group->id] = $group->name;
                }
                return View::make('calendar.create')->with('groups',$smartgroup);
            }else{
                return Redirect::route('calendar.index');
            }
        }else{
            return Redirect::route('landing');
        }
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @return Response
     */
    public function store()
    {
        if(Sentry::check()) {
            $schools = null;
            // Find active user
            $user = Sentry::getUser();
            // Permission checks
            if ($user->hasAnyAccess(['school','event'])) {

                $endDate = new DateTime();
                // Check if endDate isn't blank (____/__/__ __:__)
                if(Input::get('end') == '____/__/__ __:__') {
                    $endDate = null;
                }
                // Validate input fields
                $validator = Validator::make(
                    [
                        'group' => Input::get('group'),
                        'description' => Input::get('description'),
                        'start' => Input::get('start'),
                        'end' => $endDate,
                        'title' => Input::get('title'),
                        'day' => Input::get('day')
                    ],
                    [
                        'group' => 'required',
                        'description' => 'required',
                        'start' => 'required|date',
                        'end' => 'date',
                        'title' => 'required'
                    ]
                );

                if ($validator->fails()) {
                    return Redirect::route('event.create')->withInput()->withErrors($validator);
                } else {
                    // If inputs are valid, prepare Appointment opbject to be stored
                    $event = new Appointment();
                    $event->title = e(Input::get('title'));
                    $event->description = e(Input::get('description'));
                    $event->start_date = new DateTime(Input::get('start'));

                    if(Input::get('day')){
                        $event->allday = true;
                    }else{
                        $event->allday = false;
                        if((Input::get('end') == '____/__/__ __:__' || Input::get('end') == Input::get('start'))) {
                            $event->end_date = new DateTime(Input::get('start'));
                            $event->end_date->add(new DateInterval('PT1H'));
                        } elseif(new DateTime(Input::get('start'))>=new DateTime(Input::get('end'))) {
                            // Add an error message in the message collection (MessageBag instance)
                            $validator->getMessageBag()->add('end', Lang::get('validation.after', ['attribute ' => 'end ', 'date' => Input::get('start')]));
                            // Redirect back with inputs and validator instance
                            return Redirect::back()->withErrors($validator)->withInput();
                        }else{
                            $event->end_date = new DateTime(Input::get('end'));
                        }
                    }
                    // Recurring events handling
                    if(Input::get('repeat') && Input::get('nr_repeat')) {
                        $event->nr_repeat = Input::get('nr_repeat');
                        $event->repeat_type = e(Input::get('repeat_type'));
                        $event->repeat_freq = Input::get('repeat_freq');
                    }
                    $event->group_id = Input::get('group');
                    $event->save();
                    return Redirect::route('calendar.index');
                }
            } else {
                return Redirect::route('calendar.index');
            }
        } else {
            return Redirect::route('landing');
        }
    }

    /**
     * Show the form for editing the specified appointment.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        if(Sentry::check()) {
            // Find active user
            $user = Sentry::getUser();
            if ($user->hasAnyAccess(['school','event'])) {
                $event = Appointment::find($id);
                // Check if user is superAdmin
                if($user->hasAccess(['school'])) {
                    $groups = Group::where('school_id','<>','')->get();
                } elseif($user->school_id == $event->group->school_id) {
                    // Check if User belongs to group/school which the appointment is from
                    $user->load('school.groups.appointments');
                    $groups = $user->school->groups;
                } else {
                    return Redirect::route('calendar.index');
                }
                // Make a list of all the groups in a school to show with the view
                $smartgroup = [];
                foreach($groups as $group){
                    $smartgroup[$group->id] = $group->name;
                }

                $event = Appointment::find($id);

                return View::make('calendar.edit')->with('groups',$smartgroup)->with('event',$event);
            } else {
                return Redirect::route('calendar.index');
            }
        } else {
            return Redirect::route('landing');
        }
    }


    /**
     * Update the specified appointment in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        if(Sentry::check()) {
            // Find active user
            $user = Sentry::getUser();
            $event = Appointment::find($id);
            // Check if User belongs to group/school which the appointment is from
            if ($user->hasAccess('school') || ($user->hasAccess('event') && $user->school_id == $event->group->school_id)){
                $endDate = new DateTime();
                // Check if endDate isn't blank (____/__/__ __:__)
                if(Input::get('end') == '____/__/__ __:__') {
                    $endDate = null;
                }
                // Validate input fields
                $validator = Validator::make(
                    [
                        'group' => Input::get('group'),
                        'description' => Input::get('description'),
                        'end' => $endDate,
                        'start' => Input::get('start'),
                        'title' => Input::get('title')
                    ],
                    [
                        'group' => 'required',
                        'description' => 'required',
                        'start' => 'required|date',
                        'end' => 'date',
                        'title' => 'required'
                    ]
                );
                if ($validator->fails()) {
                    return Redirect::route('event.edit',$id)->withInput()->withErrors($validator);
                } else{
                    // If validator succeeds, prepare event object to be updated in the database
                    $event->title = e(Input::get('title'));
                    $event->description = e(Input::get('description'));
                    $event->start_date = new DateTime(Input::get('start'));
                    if(Input::get('day')) {
                        $event->allday = true;
                    } else {
                        $event->allday = false;
                        // If end-date is blank (____/__/__...), or if it's the same as the start-date/time,
                        // then end-date = start-date + 1h
                        if((Input::get('end') == '____/__/__ __:__' || Input::get('end') == Input::get('start'))) {
                            $event->end_date = new DateTime(Input::get('start'));
                            $event->end_date->add(new DateInterval('PT1H'));
                        } elseif(new DateTime(Input::get('start'))>=new DateTime(Input::get('end'))) {
                            // Add an error message in the message collection (MessageBag instance)
                            $validator->getMessageBag()->add('end', Lang::get('validation.after', ['attribute ' => 'end ', 'date' => Input::get('start')]));
                            // Redirect back with inputs and validator instance
                            return Redirect::back()->withErrors($validator)->withInput();
                        } else {
                            $event->end_date = new DateTime(Input::get('end'));
                        }
                    }
                    // Recurring events handling
                    if(Input::get('repeat') && Input::get('nr_repeat')) {
                        $event->nr_repeat = Input::get('nr_repeat');
                        $event->repeat_type = e(Input::get('repeat_type'));
                        $event->repeat_freq = Input::get('repeat_freq');
                    } else {
                        $event->nr_repeat = null;
                        $event->repeat_type = null;
                        $event->repeat_freq = null;
                    }
                    $event->group_id = Input::get('group');
                    $event->save();
                    return Redirect::route('calendar.index');
                }
            } else {
                return Redirect::route('calendar.index');
            }
        } else {
            return Redirect::route('landing');
        }
    }


    /**
     * Remove the specified appointment from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        if(Sentry::check()) {
            // Find active user
            $user = Sentry::getUser();
            $event = Appointment::find($id);
            // Check if User belongs to group/school which the appointment is from
            if ($user->hasAccess('school') || ($user->hasAccess('event') && $user->school_id == $event->group->school_id)){
                $event->delete();
            }
            return Redirect::route('calendar.index');
        } else {
            return Redirect::route('landing');
        }
    }
}
