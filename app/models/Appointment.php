<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Appointment extends Eloquent  {


	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'appointments';

    //returns the group the appointment belongs to
    public function group()
    {
        return $this->belongsTo('Group');
    }

    public function appparent()
    {
        return $this->belongsTo('AppParent');
    }

}
