<?php

class SchoolController extends \BaseController {

    protected $layout = 'layout.master';

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $user = Sentry::getUser();
        // If user is logged in, redirect to calendar index
        if(Sentry::check()) {
            if ($user->hasAccess('school'))
            {
                $schools = School::get();
                $this->layout->content = View::make('school.index')->with('schools',$schools);
            }
            else
            {
                return Redirect::route('calendar.index');
            }
        } else {
            return Redirect::route('index');
        }
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $validator = Validator::make(
            array(
                'name' => Input::get('name'),
                'email' => Input::get('email'),
                'password' => Input::get('password'),
                'tos' => Input::get('tos')
            ),
            array(
                'name' => 'required|unique:schools,name',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'tos' => 'required'
            )
        );
        if ($validator->fails())
        {
            return Redirect::route('index')->withInput()->withErrors($validator);
        }
        else{
            $school = new School();
            $school->name = Input::get("name");
            $short = strtolower(Input::get("name"));
            $short = preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '', $short));
            $school->short = $short;
            $school->save();

            $group = Sentry::createGroup(array(
                'name'        => $short.'_schooladmin',
                'permissions' => array(
                    'school'    => 0,
                    'admin'     => 1,
                    'user'      => 1,
                    'groups'    => 1,
                    'events'    => 1,
                ),
                'school_id'     => $school->id,
            ));

            $user = Sentry::createUser(array(
                'email'    => Input::get("email"),
                'password' => Input::get("password"),
                'activated' => true,
                'school_id' => $school->id,
            ));

            $user->addGroup($group);
            return Redirect::route('index');
        }
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        // If user is logged in, redirect to calendar index
        if(Sentry::check()) {
            $user = Sentry::getUser();
            if ($user->hasAccess('school'))
            {
                $school = School::find($id);
                $school->load("groups");
                $this->layout->content = View::make('school.detail')->with('school',$school);
            }
            else
            {
                return Redirect::route('calendar.index');
            }
        } else {
            return Redirect::route('index');
        }
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        if(Sentry::check()) {
            $user = Sentry::getUser();
            if ($user->hasAnyAccess(array('school','school.edit')))
            {
                $school = School::find($id);
                $this->layout->content = View::make('school.detail')->with('school',$school);
            }
            else
            {
                return Redirect::route('calendar.index');
            }
        } else {
            return Redirect::route('index');
        }
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        if(Sentry::check()) {
            $user = Sentry::getUser();
            if ($user->hasAnyAccess(array('school','school.edit')))
            {
                $school = School::find($id);
                $school->name = Input::get("name");
                $school->save();
                return Redirect::route('school.detail',$id);
            }
            else
            {
                return Redirect::route('calendar.index');
            }
        } else {
            return Redirect::route('index');
        }
	}


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}


}
