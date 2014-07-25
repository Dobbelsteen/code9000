/*
 * When the page is loaded initialize the calendar
 */

$(document).ready(function () {

  // When resizing the window, get the correct view.
  $(window).resize(function () {
    getCalendarView();
  });

  // Load events out of the database via the API
  var _events = [];
  getEvents();
  function getEvents() {
    $('#addEvent').hide();
    $.ajax({
      type: "GET",
      url: "calendar/api/events",
      cache: false,
      dataType: "json",
      contentType: "application/json",
      success: function (data) {
        // Parse data
        parseEvents(data);
        // Hide preloader after events have been loaded
        $('#preloader').hide();
        // Show add Event button
        $('#addEvent').show();
        getCalendarView();
      },
      error: function (xhr, status, errorThrown) {
        console.log(status + ', ' + errorThrown);
      }
    });
  }

  // Parse the events gotten from the database and push them to global variable
  function parseEvents(events) {
    $.each(events, function (index, value) {

      // If the event is recurring, we have to loop it
      if (value['repeat_type']) {

        for (i = 0; i < value['nr_repeat']; i++) {

          // Create a new Item object, which will be added to the _events global variable
          var newItem = {};
          newItem['title'] = value['title'];

          // Calculate dates
          newItem['start'] = moment(value['start_date']).add(value['repeat_type'], i * value['repeat_freq']);

          if (moment(value['end_date']).isValid()) {
            newItem['end'] = moment(value['end_date']).add(value['repeat_type'], i * value['repeat_freq']);
          }

          newItem['id'] = value['id'];
          newItem['description'] = value['description'];
          newItem['allDay'] = (value['allday'] == 1 ? true : false);

          // Find group name
          newItem['groupName'] = value['group']['name'].replace(value['group']['school']['short']+'_','');

          // Add event to _events variable
          _events.push(newItem);
        }
      } else {
        // If the event is non-recurring, just add 1 newItem object to the _events variable
        var newItem = {};
        newItem['title'] = value['title'];

        // Make date objects with momentjs
        newItem['start'] = moment(value['start_date']);

        if (moment(value['end_date']).isValid()) {
          newItem['end'] = moment(value['end_date']);
        }

        newItem['id'] = value['id'];
        newItem['description'] = value['description'];
        newItem['allDay'] = (value['allday'] == 1 ? true : false);

        // Find group name
        newItem['groupName'] = value['group']['name'].replace(value['group']['school']['short']+'_','');

        // Add event to _events variable
        _events.push(newItem);
      }
    });
    renderEvents();
  }

  // Get the current date in the correct format
  function getDate() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
      dd = '0' + dd
    }

    if (mm < 10) {
      mm = '0' + mm
    }

    today = yyyy + '-' + mm + '-' + dd;
    return today;
  }

  // Decide the correct view depending on the window width.
  function getCalendarView() {
    if ($(window).width() < 850) {
      $('#calendar').fullCalendar('changeView', 'agendaDay');
      $('#calendar').fullCalendar('option', 'contentHeight', 5000);
    } else if ($(window).width() > 850 && $(window).width() < 1080) {
      $('#calendar').fullCalendar('changeView', 'agendaWeek');
      $('#calendar').fullCalendar('option', 'contentHeight', null);
    } else {
      $('#calendar').fullCalendar('changeView', 'month');
      $('#calendar').fullCalendar('option', 'contentHeight', null);
    }
    ;
  }

  // Render the calendar and all events on it
  function renderEvents() {
    // Full calendar plugin
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'month,agendaWeek,agendaDay',
        right: 'title'
      },
      defaultDate: getDate(),
      editable: false,
      events: _events,
      firstDay: 1,
      timeFormat: 'H(:mm)',
      theme: false,
      themeButtonIcons: {
        prev: 'circle-triangle-w',
        next: 'circle-triangle-e',
        prevYear: 'seek-prev',
        nextYear: 'seek-next'
      },
      // When clicking on a event show the detail modal.
      eventClick: function (calEvent, jsEvent, view) {

        // Add the correct values to the corresponding fields in the eventDetail modal
        $(this).attr('data-toggle', 'modal');
        $(this).attr('data-target', '#eventModal');

        // CalEvent description
        $('#eventTitle').text(calEvent.title);
        $('#eventDescription').text(calEvent.description);

        if (calEvent.allDay) {
          $('#eventStart').text((calEvent.start).format('YYYY/MM/DD') + ' (all day)');
        } else {
          $('#eventStart').text((calEvent.start).format('YYYY/MM/DD HH:mm'));
        }

        // If end-date is specified, show the part of the modal, otherwise hide it.
        if (calEvent.end) {
          $('#eventEnd').text((calEvent.end).format('YYYY/MM/DD HH:mm'));
          $('#eventEnds').show();
        } else {
          $('#eventEnds').hide();
        }

        $('#groupName').text(calEvent.groupName);

        // Fix links
        $('#editEvent').attr('href', 'calendar/event/edit/' + calEvent.id);
        $('#icalEvent').attr('href', 'export/appointment/find/' + calEvent.id);
        $('#deleteEvent').attr('data-href', 'calendar/event/delete/' + calEvent.id);
      },
      loading: function (bool) {

      }
    });
  }
});