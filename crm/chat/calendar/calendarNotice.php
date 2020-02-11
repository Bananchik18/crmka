<?php
  // include "../../../configs/db.php";
  include "php/showTaskCalendarNotice.php";

$events_notice = get_events_notice($conn);
$events_notice = get_json_notice($events_notice);
$events_notice = json_encode($events_notice);
// echo $events_notice;

?>



  
  
  <div id="blockForCalendar_notice">
    <div id='calendar_notice'></div>
    <div id="createTaskCaledar">
        <input type="time" id="timeToNiticeMessage">
        <button id="btnNoticeMessage">Создать</button>
      </div>
    </div>
    <script>var events = <?php  echo $events_notice; ?>;</script>
    <script src="../chat/calendar/js/calendarNotice.js"></script>
    
  