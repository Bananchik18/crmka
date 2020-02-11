var calendarEl = document.getElementById('calendar_notice');
var masIdMessage = [];
  function showCalendarMessage(events){
        var calendar = new FullCalendar.Calendar(calendarEl, {
          plugins: [ 'dayGrid','interaction' ],
          locale:'ru',
          theme: true,
          header: { 
            left:'prev,next',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay' 
          },
          eventLimit: true,
          events:events,
          navLinks:true,
          dateClick: function(info) {
            $('#btnNoticeMessage').attr("toDate",info.dateStr)
            console.dir(info);
          }
        
        });
        calendar.render();      
  }
  showCalendarMessage(events)
  $('#btnNoticeMessage').on('click',function(){
  var timeNotice = $('#timeToNiticeMessage').val();
  var toDate = $(this).attr("toDate");

    $.ajax({
      url:"../chat/calendar/php/createTaskCalendar.php",
      method:"POST",
      data:{noticeMessage:true,idMessage:masIdMessage[0],timeNotice:timeNotice,toDate:toDate},
      success:function(data){
            // console.dir(data);
            calendarEl.innerHTML = "";  
            data = JSON.parse(data);
            showCalendarMessage(data);
            calendarMain.innerHTML = "";
            showCalendar(data);

      }
    })
  })

  var calendarMain = document.getElementById('calendar');

  function showCalendar(events){
        var calendar = new FullCalendar.Calendar(calendarMain, {
          plugins: [ 'dayGrid','interaction' ],
          locale:'ru',
          header: { 
            left:'prev,next',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay' 
          }, // buttons for switching between views
          theme: true,
          eventLimit: 1,
          events:events,
          navLinks:true,
          dateClick: function(info) {
            $('#btncreateTaskCalendar').attr("toDate",info.dateStr)
          }
        
        });
        calendar.render();
  }
  // showCalendar(events)