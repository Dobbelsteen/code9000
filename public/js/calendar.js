$(document).ready(function() {

  $(window).resize(function(){
    getCalendarView();
  });

  // Load events out of the database via the API
  var _events = [];
  getEvents();
  function getEvents()
  {
    $('#addEvent').hide();
    $.ajax({
      type:"GET",
      url: "calendar/api/events",
      cache: false,
      dataType: "json",
      contentType: "application/json",
      success:function(data){
        parseEvents(data);
        $('#preloader').hide();
        $('#addEvent').show();
        getCalendarView();
      },
      error:function(xhr, status, errorThrown) {
        alert(status + ', ' + errorThrown);
      }
    });
  }
  // Parse the events gotten from the database and push them to global variable
  function parseEvents(events)
  {
    $.each( events, function( index, value ){
      var newItem = {};
      newItem['title'] = value['title'];
      newItem['start'] = value['start_date'];
      newItem['end']   = value['end_date'];
      newItem['url']    = 'calendar/event/' + value['id'];
      _events.push(newItem);
    });
    renderEvents();
  }

  // Get the current date in the correct format
  function getDate(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();

    if(dd<10) {
      dd='0'+dd
    }

    if(mm<10) {
      mm='0'+mm
    }

    today = yyyy+'-'+mm+'-'+dd;
    return today;
  }

  function getCalendarView(){
    if ($(window).width() < 768){
      $('#calendar').fullCalendar('changeView', 'agendaDay');
      $('#calendar').fullCalendar('option', 'contentHeight', 5000);
    } else if ($(window).width() > 768 && $(window).width() < 960) {
      $('#calendar').fullCalendar('changeView', 'agendaWeek');
      $('#calendar').fullCalendar('option', 'contentHeight', null);
    } else {
      $('#calendar').fullCalendar('changeView', 'month');
      $('#calendar').fullCalendar('option', 'contentHeight', null);
    };
  }

  // Render the calendar and all events on it
  function renderEvents()
  {
    // Full calendar plugin
    $('#calendar').fullCalendar({
      header: {
        left: 'prev,next today',
        center: 'month,agendaWeek,agendaDay',
        right: 'title'
      },
      defaultDate: getDate(),
      editable: true,
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
      /*{
       url: 'php/get-events.php',
       error: function() {
       $('#script-warning').show();
       }
       }*/
      loading: function(bool) {
        //$('#loading').toggle(bool);
      }
    });
  }
});