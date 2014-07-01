<?php

class CalendarController extends \BaseController {

    //MASTER LAYOUT THEMPLATE
    protected $layout = 'layout.master';

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{

        if ( ! Sentry::check())
        {
            // User is not logged in, or is not activated
            return Redirect::route('index');
        }
        else
        {
            // User is logged in
            $dateoffset = 0;
            //CHECK IF AN OFFSET IS DEFINED
            if (Session::has('dateoffset'))
            {
                $dateoffset = Session::get('dateoffset');
            }

            //DEFINE DISPLAYING MONTH
            $today = new DateTime(date("Y-m"));
            $displayMonth = $today->modify($dateoffset.' month');
            //print_r($displayMonth);

            //GET APPOINTMENTS


            return View::make('calendar.index');
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
		//
	}


	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}


	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}


	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
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
