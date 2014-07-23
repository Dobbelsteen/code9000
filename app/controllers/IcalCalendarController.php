<?php

class IcalCalendarController extends \BaseController {

	/**
	 * Find correct appointments depending on $school and $group
     * Process these appointments and render them to an .ics file which will be returned for download
	 *
     * @param  string  $school
     * @param  string  $group
	 * @return Cal.ics download file
     *
     */
	public function index($school, $group)
    {
        // Create an empty appointments array, which we will fill with appointments to render later
        $appointments = [];
        // Load appointments based on group
        $selGroup = Group::where('name', $school.'_'.$group)->first();
        $selGroup->load('appointments');

        // Set the limitations for which appointments to get
        $dsta = new DateTime();
        $dend = new DateTime();
        // In this case we set the limit to 1 year in the past until 1 year in the future
        $dsta->sub(new DateInterval("P1Y"));
        $dend->add(new DateInterval("P1Y"));

        // Add global appointments, unless only global appointments are requested
        if($group != 'global') {
            $globalGroup = Group::where('name', $school.'_global')->first();
            $globalGroup->load('appointments');

            foreach($globalGroup->appointments as $appointment) {
                $da = new DateTime($appointment->start_date);
                // Set the limits for what appointments to get (1y in past till 1y in future)
                // If the appointment is within the limits, add it to the $appointments array
                if($da > $dsta && $da < $dend)
                    array_push($appointments, $appointment);
            }
        }
        // Add group specific appointments
        foreach($selGroup->appointments as $appointment) {
            $da = new DateTime($appointment->start_date);
            // Set the limits for what appointments to get (1y in past till 1y in future)
            // If the appointment is within the limits, add it to the $appointments array
            if($da > $dsta && $da < $dend)
                array_push($appointments, $appointment);
        }
        // Compose iCal with the help of the eluceo plugin
        $calendar = self::composeIcal($appointments);
        return $calendar->render();
	}

    /**
     * This function composes the contents of the iCal file, which are eventually rendered by the index function.
     * This function makes use of the Eluceo plugin
     * @param $appointments
     * @return \Eluceo\iCal\Component\Calendar
     */
    public function composeIcal($appointments)
    {
        // Set default timezone (PHP 5.4)
        date_default_timezone_set('Europe/Berlin');

        // Create new calendar object
        $calendar = new \Eluceo\iCal\Component\Calendar('EduCal');

        // Loop through appointments and add them to the calendar.
        foreach($appointments as $appointment) {

            // Create an event
            $event = new \Eluceo\iCal\Component\Event();
            $event->setSummary($appointment['attributes']['title']);
            $event->setDescription($appointment['attributes']['description']);
            $event->setDtStart(new \DateTime($appointment['attributes']['start_date']));
            $event->setDtEnd(new \DateTime($appointment['attributes']['end_date']));
            $event->setNoTime($appointment['attributes']['allday']);
            $event->setStatus('TENTATIVE');

            // Recurrence option (e.g. New Year happens every year)
            // Set recurrence rule
            if($appointment['attributes']['repeat_type']) {

                $recurrenceRule = new \Eluceo\iCal\Property\Event\RecurrenceRule();
                switch($appointment['attributes']['repeat_type']) {
                    case 'd':
                        $recurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_DAILY);
                        break;
                    case 'w':
                        $recurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_WEEKLY);
                        break;
                    case 'M':
                        $recurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_MONTHLY);
                        break;
                    case 'y':
                        $recurrenceRule->setFreq(\Eluceo\iCal\Property\Event\RecurrenceRule::FREQ_YEARLY);
                        break;
                }
                $recurrenceRule->setInterval($appointment['attributes']['repeat_freq']);
                $recurrenceRule->setCount($appointment['attributes']['nr_repeat']);

                $event->setRecurrenceRule($recurrenceRule);
            }

            // Adding Timezone (optional)
            $event->setUseTimezone(true);
            $event->setTimeTransparency('TRANSPARENT');

            // Add event to calendar
            $calendar->addEvent($event);
        }

        // Set headers
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');

        // Output
        return $calendar;
    }

	/**
	 * Return only a single event in the .ics file
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        // Find selected appointment
        $appointment = Appointment::find($id);
        $appointments = [];
        array_push($appointments, $appointment);

        $calendar = self::composeIcal($appointments);
        return $calendar->render();
	}
}
