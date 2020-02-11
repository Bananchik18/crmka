<?php
  // include "../../configs/db.php";
  include "php/showTaskCalendar.php";

$events = get_events($conn);
$events = get_json($events);
$events = json_encode($events);
// echo $events;

?>


  
  

    <link href='../chat/calendar/fullcalendar/packages/core/main.css' rel='stylesheet' />
    <link href='../chat/calendar/fullcalendar/packages/daygrid/main.css' rel='stylesheet' />

  
  
  <div id="blockForCalendar">
    <div id='calendar'></div>
    <div id="createTaskCaledar">
        <textarea name="" cols="30" rows="2" id="inputcreateTaskCalendar"></textarea>
        <input type="time" id="timeToNiticeTask">
        <button id="btncreateTaskCalendar">Создать</button>
      </div>
    <div id="fieldForBotTask">
      <ul id="fieldForMessageBot">
        
      </ul>
    </div>
    </div>
    <script>var events = <?php  echo $events; ?>;</script>
    <script src='../chat/calendar/fullcalendar/packages/core/main.js'></script>
    <script src='../chat/calendar/fullcalendar/packages/daygrid/main.js'></script>
    <script src='../chat/calendar/fullcalendar/packages/interaction/main.js'></script>
    <script src="../chat/calendar/js/scriptCalendar.js"></script>
  