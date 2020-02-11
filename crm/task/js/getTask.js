var websocket = new WebSocket('ws://localhost:8080');
websocket.onopen = function() {

};

websocket.onclose = function(event) {
	if (event.wasClean) {
		alert('Соединение закрыто чисто');
	} else {
    alert('Обрыв соединения'); // например, "убит" процесс сервера
}
alert('Код: ' + event.code + ' причина: ' + event.reason);
};

websocket.onmessage = function(event) {

};

websocket.onerror = function(error) {
	alert("Ошибка " + error.message);
};

function getIdUser(){
	$.ajax({
		url:"../chat/php/fetch_session_id.php",
		method:"POST",
		data:{},
		success:function(data){
			console.dir(data)
			getTask(data);
		}
	})
}
getIdUser();

function getTask(data){
	$.ajax({
		url:"php/getTask.php",
		method:"POST",
		data:{id_user:data,getTask:true},
		success:function(data){
			// console.dir();
			$('#information_task ul').html(transformJsonTask(JSON.parse(data)));
		}
	})
}
function transformJsonTask(data){
	var output = '';
	console.dir(data);
	for(var i = 0; i < data.task.length;i++){
		output += "<li>"
		output += "<div>"
		output += "<input type='checkbox'>"
		output += "<p>"+data.task[i].info_task+"</p>"
		output += "<p>"+data.task[i].start_date+""+data.task[i].end_date+"</p>"
		output += "<p>"+data.task[i].username+" "+data.task[i].lastname+"</p>"
		output += "</div>"	
		output += "</li>"
		// console.dir(data.task[i].id_task);
	}
	// console.dir(output);
	return output;
}
$('#createNewTask').on('click',function(){
	$('#formNewTask').modal('open');
	$.ajax({
		url:"php/fetch_user.php",
		method:"POST",
		data:{fetch_user:true},
		success:function(data){
			console.dir(data);
			$('#formNewTask #selectName').html(data);
		}
	})

})