
var calendarEl = document.getElementById('calendar');

  function showCalendar(events){
        var calendar = new FullCalendar.Calendar(calendarEl, {
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
  showCalendar(events)
      $('#btncreateTaskCalendar').click(function(){
        var toDate = $(this).attr("toDate");
        var taskText = $('#inputcreateTaskCalendar').val();
        var timeToNiticeTask = $('#timeToNiticeTask').val();
        console.dir(calendar)
        $.ajax({
          url:"../chat/calendar/php/createTaskCalendar.php",
          method:"POST",
          data:{taskText:taskText,timeToNiticeTask:timeToNiticeTask,toDate},
          success:function(data){
            calendarEl.innerHTML = "";  
            data = JSON.parse(data);
            showCalendar(data);
          }
        })
      })

  function botCheckTaskTime(){
    $.ajax({
      url:"../chat/calendar/php/botTaskCalendar.php",
      method:"POST",
      data:{},
     success:function(data){
        // console.dir(data);
        $('#fieldForMessageBot').html(data);
      }
    })
  }

  setInterval(botCheckTaskTime,10000);


  




 