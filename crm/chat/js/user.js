var checkForwardMessage = false;

window.onload = function(){

	var id_user_socket;
	$.ajax({
		url:"php/fetch_session_id.php",
		method:"POST",
		data:{},
		success:function(data){
			// console.dir(data);
			id_user_socket = data;
			// console.dir(id_user_socket);
		},
		complete: function() {
			webSocketFunction(id_user_socket);
		}
	})

	$.ajax({
		url:"php/open_last_chat.php",
		method:"POST",
		data:{open_chat:true},
		success:function(data){
			console.dir(data);
			data = JSON.parse(data);
			console.dir(data);
			if(data.chat_user == true){
				make_chat_dialog(data.id_user,data.name_user);
			}else if(data.chat_group == true){
				make_chat_group_dialog(data.id_group);
			}

		}
	})
	// setTimeout(console.dir(id_user_socket),22000);


	function webSocketFunction(id_user){
		var socket = new WebSocket('ws://192.168.0.109:8080');
		socket.onopen = function(event) {
			// console.dir(event);
			var idSocket = {
				type:"setUserSocket",
				id_user:id_user
			}
			// console.dir(idSocket);
			socket.send(JSON.stringify(idSocket));
		};
		// console.dir(id_user);
		socket.onclose = function(event) {

		};

		socket.onerror = function(event) {
		  // status.innerHTML = "ошибка " + event.message;
		};
		//сообщение
		$(document).on('click', '.send_chat', function(){
			var to_user_id = $(this).attr('id');
			var chat_message = $('#chat_message_'+to_user_id).val();
			var id_message = $("#textQuotes").attr('id-message');
			if(checkForwardMessage == true){
				var forwardText = $('#textQuotes').text();
				let messageForward = {
					type:"sendForwardMessage",
					to_user_id:to_user_id,
					chat_message:chat_message,
					id_message:id_message,
					from_user_id:id_user
				}
				checkForwardMessage = false;
				$('.blockQuotes').html("");
				$('.blockQuotes').css("display","none");
				socket.send(JSON.stringify(messageForward));
			}else{
				let message = {
					type:"sendMessageUser",
					from_user_id:id_user,
					to_user_id:to_user_id,
					message:chat_message
				}
				socket.send(JSON.stringify(message));
			}

			return false;
		})
	 	//удаление
	 	$(document).on('click', '.deleteMessage', function(){
	 		var isAdmin = confirm("Удалить сообщение ?");
	 		var id_message = $(this).data('id-message');
	 		let messageDelet = {
	 			type:"deleteMessage",
	 			id_message:id_message,
	 			from_user_id:id_user,
	 		}
	 		if(isAdmin){
	 			socket.send(JSON.stringify(messageDelet));
	 		}
	 	})
	 	//закрепить сообщение
	 	$(document).on('click','#fixedMessage',function(){
	 		var id_message = $(this).data("id_message");
	 		var to_user_id = $('.chat_history').data("touserid");
	 		let messageFixed = {
	 			type:"fixedMessage",
	 			id_message:id_message,
	 			to_user_id:to_user_id,
	 			from_user_id:id_user
	 		}
	 		// console.dir(messageFixed);
	 		socket.send(JSON.stringify(messageFixed));
	 	})
	 	//удалить закрепленное сообщение
	 	$(document).on('click','#deleteFixed', function(){
	 		var idFixedMessage = $(this).attr("id-fixed-message");
	 		console.dir(idFixedMessage)
	 		let deleteFixed = {
	 			type:"deleteFixedMessage",
	 			from_user_id:id_user,
	 			idFixedMessage:idFixedMessage
	 		}
	 		socket.send(JSON.stringify(deleteFixed));
	 	})
	 	//переслать сообщение
	 	$(document).on('click','#sendForwardMessage',function(){
	 		$(this).toggleClass("btn-warning");
	 		var textComentForward = $('#fieldForForwardInput').val();
	 		var to_user_id = $(this).attr("to_user_id");
	 		var to_group_id = $(this).attr("to_group_id");
	 		// var id_forward_message = $
	 		// var fieldForForwardText = $('#fieldForForwardText').text();
	 		var id_message = $("#fieldForForwardText").attr("id-message");
	 		console.dir(masForwardMessage);
	 		console.dir(masForwardMessage.sort());
	 		let messageForward = {
	 			type:"sendForwardMessageAnotherUser",
	 			from_user_id:id_user,
	 			to_user_id:to_user_id,
	 			to_group_id:to_group_id,
	 			// fieldForForwardText:fieldForForwardText,
	 			// id_message:id_message,
	 			id_message:masForwardMessage,
	 			textComentForward:textComentForward
	 		}
	 		console.dir(messageForward);
	 		socket.send(JSON.stringify(messageForward));

	 	})
	 	//группа
	 	$(document).on('click', '.send_chat_group', function(){
	 		var to_group_id = $(this).attr('id');
	 		var chat_message_group = $('#group_chat_message_'+to_group_id).val();
	 		var id_message = $("#textQuotes").attr('id-message');
	 		if(checkForwardMessage == true){
	 			var forwardText = $('#textQuotes').text();
	 			let messageForward = {
	 				type:"sendForwardMessageGroup",
	 				to_group_id:to_group_id,
	 				chat_message_group:chat_message_group,
	 				id_message:id_message,
	 				from_user_id:id_user
	 			}
	 			console.dir(messageForward);
	 			checkForwardMessage = false;
	 			$('.blockQuotes').html("");
	 			$('.blockQuotes').css("display","none");
	 			socket.send(JSON.stringify(messageForward));
	 		}else{
	 			var messageGroup = {
	 				type:"sendMessageGroup",
	 				to_group_id:to_group_id,
	 				from_user_id:id_user,
	 				chat_message_group:chat_message_group
	 			}
	 			socket.send(JSON.stringify(messageGroup));
	 		}
	 	})
	 	$(document).on('click', '#fixedMessageGroup', function(){
	 		var id_message = $(this).data("id_message");
	 		var to_user_id = $('.chat_history').data("touserid");	 	
	 		var to_group_id = $(this).attr('to_group_id');
	 		var fixedMessageGroup = {
	 			type:"fixedMessageGroup",
	 			id_message:id_message,
	 			to_group_id:to_group_id,
	 			from_user_id:id_user
	 		}
	 		console.dir(fixedMessageGroup)
	 		socket.send(JSON.stringify(fixedMessageGroup));
	 		// console.dir(fixedMessageGroup);
	 	})
	 	$(document).on('click','#deleteFixedGroup', function(){
	 		var idFixedMessage = $(this).attr("id-fixed-message");
	 		console.dir(idFixedMessage)
	 		let deleteFixed = {
	 			type:"deleteFixedMessageGroup",
	 			from_user_id:id_user,
	 			idFixedMessage:idFixedMessage
	 		}
	 		socket.send(JSON.stringify(deleteFixed));
	 	})

	 	//создания группи 
	 	$('#btnCreateGroup').click(function(){
	 		var chatName = $('#createGroupInput').val();
	 		console.dir(masOfUserGroup);
	 		var createGroup = {
	 			type:"createGroup",
	 			user:masOfUserGroup,
	 			group_name:chatName,
	 			from_user_id:id_user
	 		}

	 		socket.send(JSON.stringify(createGroup));
	 	})
	 	$(document).on('click','#delete_user_group',function(){
	 		var user_delete = $(this).attr("id_user");
	 		var id_group = $(this).attr("id_group");
	 		var deleteUserGroup = {
	 			type:"deleteUserFromGroup",
	 			id_user:user_delete,
	 			id_group:id_group,
	 			from_user_id:id_user
	 		}
	 		console.dir(deleteUserGroup)
	 		socket.send(JSON.stringify(deleteUserGroup));
	 		$(this).parent().remove();
	 	})
	 	$(document).on('click', '#btnAddNewUserToGroup', function(){
	 		var to_group_id = $(this).attr('to-group-id');
	 		if(masOfUserGroupAdd.length != 0){
		 		var addNewUserGroup = {
		 			type:"addNewUserGroup",
		 			to_group_id:to_group_id,
		 			masNewUser:masOfUserGroupAdd,
		 			from_user_id:id_user
		 		}
		 		socket.send(JSON.stringify(addNewUserGroup));
		 	}
		 	for(var i = 0; i < masOfUserGroupAdd.length;i++){
		 		$('#infoAboutGroup ul li[id-user="'+masOfUserGroupAdd[i]+'"]').remove();
		 	}
		 });
	 	//отправка файла в чат юзер
	 	$(document).on('change', '.fileSend', function(){
	 		var input = $(this).children()[0];
	 		var files = input.files;
	 		var to_user_id = $(this).attr("to_user_id");
	 		var to_group_id = $(this).attr("to_group_id");
	 		console.dir(to_user_id);
	 		console.dir(to_group_id);

	 		var formData = new FormData();
	 		$.each(files, function( key, value ){
	 			formData.append( key, value );
	 		});
	 		formData.append("my_file_upload",1);
	 		if(to_user_id != undefined){
	 			formData.append("to_user_id",to_user_id);
	 		}else{
	 			formData.append("to_group_id",to_group_id);
	 		}
	 		var typeFile = input.files[0].type; 
	 		$.ajax({
	 			url:"php/addFile.php",
	 			type:"POST",
	 			data:formData,
	 			cache: false,
	 			dataType: 'json',
	 			processData: false,
	 			contentType: false,
	 			success:function(data){
	 				console.dir(data);
	 				var messageFile = {
	 					type:"sendFileUser",
	 					data:data,
	 					typeFile:typeFile
	 				}
	 				socket.send(JSON.stringify(messageFile));
	 			}
	 		})

	 	})
	 	socket.onmessage = function(event) {
	 		var test = "undefined"
	 		let message = JSON.parse(event.data);
	 		console.dir(message);
	 		if(message.type == "sendMessageUser"){
	 			if($(".chat_history").attr("data-touserid") == message.from_user_id || $(".chat_history").attr("data-touserid") == message.data.to_user_id){
	 				if(message.from_user_id == id_user){
	 					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><span>'+message.data.chat_message+'</span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
	 				}else{
	 					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span>'+message.data.chat_message+'</span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');
	 				}
	 			}
	 			if($(".chat_history").attr("data-touserid") != message.from_user_id){
	 				// notifSet();
	 				notifSet();
	 				var tr = $("#user_details ul li[data-touserid='"+message.from_user_id+"'] h6");
	 				masCountMessage[message.from_user_id]++;
	 				$(tr).children().remove();
	 				$(tr).append("<span class='badge badge-success'>"+masCountMessage[message.from_user_id]+"</span>");

	 				var tr = $("#user_details ul li[data-touserid='"+message.from_user_id+"']");
	 				var toUp = $(tr).clone();
	 				$("#user_details ul li[data-touserid='"+message.from_user_id+"']").remove();
	 				$('#user_details ul').prepend(toUp);
	 			}
	 			if(message.from_user_id == id_user){
	 				var tr = $("#user_details ul li[data-touserid='"+message.data.to_user_id+"']");
	 				var toUp = $(tr).clone();
	 				$("#user_details ul li[data-touserid='"+message.data.to_user_id+"']").remove();
	 				$('#user_details ul').prepend(toUp);
	 			}

	 		}else if(message.type == "sendForwardMessage"){
	 			console.dir(message);
	 			if($(".chat_history").attr("data-touserid") == message.from_user_id || $(".chat_history").attr("data-touserid") == message.data.to_user_id){
	 				// $('.list-unstyled').append('<li style="border-bottom:1px dotted #ccc"><p id-message="'+message.data.id_message+'">'+message.data.fromName+' - '+message.data.chat_message+'Пользователь - '+message.data.NameForward+' написал - '+message.data.textForward+''+createBtnMessage(message.data.id_message,message.data.chat_message)+'<div align="right">- <small><em>'+message.data.posted+'</em></small></div></p></li>');
	 				if(message.from_user_id == id_user){
	 					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUserForward"><span>'+message.data.chat_message+'</span><br>Переслано от '+message.data.NameForward+'<br>'+message.data.textForward+'<div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>')
	 				}else{
	 					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span>'+message.data.chat_message+'</span><br>Переслано от '+message.data.NameForward+'<br>'+message.data.textForward+'<div><small><em>'+message.data.posted+'</em></small></div></p></li>')
	 				}
	 			}

	 			if($(".chat_history").attr("data-touserid") != message.data.from_user_id || $(".chat_history").length == 0){
	 				var tr = $("#user_details ul li[data-touserid='"+message.from_user_id+"'] h6");
	 				masCountMessage[message.from_user_id]++;
	 				$(tr).children().remove();
	 				$(tr).append("<span class='badge badge-success'>"+masCountMessage[message.from_user_id]+"</span>");

	 				var tr = $("#user_details ul li[data-touserid='"+message.from_user_id+"']");
	 				var toUp = $(tr).clone();
	 				$("#user_details ul li[data-touserid='"+message.from_user_id+"']").remove();
	 				$('#user_details ul').prepend(toUp);
	 			}
	 			if(message.from_user_id == id_user){
	 				var tr = $("#user_details ul li[data-touserid='"+message.data.to_user_id+"']");
	 				var toUp = $(tr).clone();
	 				$("#user_details ul li[data-touserid='"+message.data.to_user_id+"']").remove();
	 				$('#user_details ul').prepend(toUp);
	 			}

	 		}else if(message.type == "sendDeleteMessage"){
	 			$('p[id-message="'+message.data.id_message+'"]').parent().remove();
	 		}else if(message.type == "fixedMessage"){
	 			console.dir(message);
	 			if($(".chat_history").attr("data-touserid")){
	 				$("#fixedMessageLi").remove();
	 				console.dir(message.data.fixedMessage.length);
	 				if(message.data.fixedMessage > 30){
	 					message.data.fixedMessage = message.data.fixedMessage.substring(0,30);
	 					message.data.fixedMessage += "..."
	 				}
	 				$('.chat_history').prepend("<div id='fixedMessageLi' id-message='"+message.data.id_message+"'>Закрепленное сообщение <b>"+message.data.fixedMessage+"</b><button id='deleteFixed' id-fixed-message='"+message.data.id_fixed_message+"'>&times</button></div>")
	 			}
	 		}else if(message.type == "deleteFixedMessage"){
	 			if($(".chat_history").attr("data-touserid")){
	 				$("#fixedMessageLi").remove();
	 			}
	 		}else if(message.type == "sendForwardMessageAnotherUser"){
	 			if(message.data.to_user_id != null){
	 				if($(".chat_history").attr("data-touserid") == message.from_user_id || $(".chat_history").attr("data-touserid") == message.data.to_user_id){


	 					if(message.from_user_id == id_user){
	 						if(message.data.firstMes){
	 							$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><span>'+message.data.chat_message+'</span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
	 						}else{
	 							$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUserForward"><span></span><br>Переслано от '+message.data.NameForward+'<br>'+message.data.textForward+'<div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>')	
	 						}
	 					}else{
	 						if(message.data.firstMes){
	 							$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span>'+message.data.chat_message+'</span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');
	 						}else{
	 							$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span></span><br>Переслано от '+message.data.NameForward+'<br>'+message.data.textForward+'<div><small><em>'+message.data.posted+'</em></small></div></p></li>')
	 						}
	 					}
	 				}
	 			}else{
	 				if($(".chat_history").attr("data-togroupid-chat") == message.data.to_group_id){
	 					$('.list-unstyled').append('<li style="border-bottom:1px dotted #ccc"><p id-message="'+message.data.id_message+'">'+message.data.fromName+' - '+message.data.chat_message+'Пользователь - '+message.data.NameForward+' написал - '+message.data.textForward+''+createBtnMessageGroup(message,message.data.id_message, message.data.chat_message, message.data.to_user_id, id_user)+'<div align="right">- <small><em>'+message.data.posted+'</em></small></div></p></li>')
	 				}
	 			}

	 			// if($(".chat_history").attr("data-touserid") != message.data.from_user_id || $(".chat_history").length == 0){
	 			// 	var tr = $("#user_details table tbody tr[id_user='"+message.from_user_id+"'] td");
	 			// 	$(tr[0]).append("<span class='badge badge-success'>1</span>");
	 			// }
	 			if($(".chat_history").attr("data-touserid") != message.data.from_user_id || $(".chat_history").length == 0){
	 				var tr = $("#user_details ul li[data-touserid='"+message.from_user_id+"'] h6");
	 				masCountMessage[message.from_user_id]++;
	 				$(tr).children().remove();
	 				$(tr).append("<span class='badge badge-success'>"+masCountMessage[message.from_user_id]+"</span>");

	 				var tr = $("#user_details ul li[data-touserid='"+message.from_user_id+"']");
	 				var toUp = $(tr).clone();
	 				$("#user_details ul li[data-touserid='"+message.from_user_id+"']").remove();
	 				$('#user_details ul').prepend(toUp);
	 			}
	 			if(message.from_user_id == id_user){
	 				var tr = $("#user_details ul li[data-touserid='"+message.data.to_user_id+"']");
	 				var toUp = $(tr).clone();
	 				$("#user_details ul li[data-touserid='"+message.data.to_user_id+"']").remove();
	 				$('#user_details ul').prepend(toUp);
	 			}

	 		}else if(message.type == "sendStatusUser"){
	 			if(message.data.status == true){
	 				var status = $("#user_details ul li[data-touserid="+message.data.id_user+"] .status");
	 				$(status).removeClass("badge badge-danger status"); 
	 				$(status).addClass("badge badge-success status");
	 			}else if(message.data.status == false){
	 				var status = $("#user_details ul li[data-touserid="+message.data.id_user+"] .status");
	 				$(status).removeClass("badge badge-success status"); 
	 				$(status).addClass("badge badge-danger status"); 

	 			}
	 		}
		  	//группа
		  	else if(message.type == "sendMessageGroup"){
		  		if($(".chat_history").attr("data-togroupid-chat") == message.from_group){
		  			if(message.data.from_user_id == id_user){
		  				$('.list-unstyled').append('<li><p id-message="'+message.data.chat_message_id+'" id="messageUser"><b>'+message.data.from_name_user+'</b><br><span>'+message.data.chat_message_group+'</span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
		  			}else{
		  				$('.list-unstyled').append('<li><p id-message="'+message.data.chat_message_id+'"><b>'+message.data.from_name_user+'</b><br><span>'+message.data.chat_message_group+'</span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
		  			}
		  			// $('.list-unstyled').append('<li style="border-bottom:1px dotted #ccc"><p id-message="'+message.data.chat_message_id+'">'+message.data.from_name_user+' - '+message.data.chat_message_group+''+createBtnMessageGroup(message,message.data.chat_message_id, message.data.chat_message_group)+'<div align="right">- <small><em>'+message.data.posted+'</em></small></div></p></li>');
		  		}
		  	}else if(message.type == "fixedMessageGroup"){
		  		if($(".chat_history").attr("data-togroupid-chat")){
		  			$("#fixedMessageLi").remove();
		  			if(message.data.fixedMessage > 30){
	 					message.data.fixedMessage = message.data.fixedMessage.substring(0,30);
	 					message.data.fixedMessage += "..."
	 				}
		  			$('.chat_history').prepend("<div id='fixedMessageLi' id-message='"+message.data.id_message+"'>Закрепленное сообщение <b>"+message.data.fixedMessage+"</b><button id='deleteFixedGroup' id-fixed-message='"+message.data.id_fixed_message+"'>&times</button></div>")
		  		}
		  	}else if(message.type == "deleteFixedMessageGroup"){
		  		if($(".chat_history").attr("data-togroupid-chat")){
		  			$("#fixedMessageLi").remove();
		  		}
		  	}else if(message.type == "sendForwardMessageGroup"){
		  		$('.list-unstyled').append('<li style="border-bottom:1px dotted #ccc"><p id-message="'+message.data.id_message+'">'+message.data.fromName+' - '+message.data.chat_message+'Пользователь - '+message.data.NameForward+' написал - '+message.data.textForward+''+createBtnMessage(message.data.id_message,message.data.chat_message)+'<div align="right">- <small><em>'+message.data.posted+'</em></small></div></p></li>');
		  	}else if(message.type == "createGroup"){
		  		console.dir(message);
		  		var masUserGroup = message.data.members.split(",");
		  		for(var i = 0; i < masUserGroup.length;i++){
		  			if(masUserGroup[i] == id_user){
		  				$("#user_group ul").append('<li class="start_group_chat" data-group-chat="'+message.data.id_group+'" member-of-group="'+message.data.members+'"><h6>'+message.data.name_group+'<h6></li>')
		  			}
		  		}

		  	}else if(message.type == "deleteUserFromGroup"){
		  		$('.infoGroup[data-group-chat="'+message.data.id_group+'"]').parent().parent().remove();
		  		console.dir(message.data.members);
		  		$('#btnAddMemberGroup').attr("members-of-group",message.data.members);
		  		if(message.data.who_delete == id_user){
		  			$("#list #user_group ul li[data-group-chat='"+message.data.id_group+"'").remove();
		  		}
		  	}else if(message.type == "addNewUserGroup"){
		  		// console.dir(message)
		  		// // 
		  		$("#list #user_group ul").append("<li class='start_group_chat' data-group-chat='"+message.data.id_group+"' member-of-group='"+message.data.members+"'><h6>"+message.data.name_group+"<h6></li>")

		  	}
		  	//файл юзер
		  	else if(message.type == "sendFileUser"){
		  		console.dir(message);
		  		var typeFile = message.data.groupFile.split("/");
		  		console.dir(id_user);
		  		console.dir(message.data.to_user_id);
		  		if(message.data.to_user_id){

		  			if($('.chat_history').attr("data-touserid") == message.data.to_user_id || $('.chat_history').attr("data-touserid") == message.from_user_id){
		  				if(message.from_user_id == id_user){
		  					if(typeFile[0] == "image"){
		  						$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><span><img id="imgUser" src="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
		  					}else if(typeFile[0] == "video"){
		  						$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><span><img id="clickVideo" src="material/file.png" urlvideo="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
		  					}else if(typeFile[0] == "application"){
		  						if(message.data.typeFile == "pdf"){
		  							$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><span><img id="clickPdf" src="material/pdf.png" urlPdf="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');	
		  						}else if(message.data.typeFile == "docx"){
		  							$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><span><img id="clickWorld" src="material/doc.png" urlWord="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');	
		  						}else if(message.data.typeFile == "xlsx"){
		  							$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><span><img id="clickExel" src="material/xls.png" urlExel="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');	
		  						}
		  					}
			  			// }
			  		}else{
			  			if(typeFile[0] == "image"){
			  				$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span><img id="imgUser" src="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');
			  			}else if(typeFile[0] == "video"){
	 					// $('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><span>'+message.data.chat_message+'</span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
			  				$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span><img id="clickVideo" src="material/file.png" urlvideo="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');
			  			}else if(typeFile[0] == "application"){
			  				if(message.data.typeFile == "pdf"){
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span><img id="clickPdf" src="material/pdf.png" urlPdf="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}else if(message.data.typeFile == "docx"){
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span><img id="clickWorld" src="material/doc.png" urlWord="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}else if(message.data.typeFile == "xlsx"){
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><span><img id="clickExel" src="material/xls.png" urlExel="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}
			  			}
			  		}
			  	}
			  	// var updateMes = {
			  	// 	type:"updateMes",
			  	// 	id_message:message.data.id_message
			  	// }
			  	// socket.send(JSON.stringify(updateMes))
			  }else if(message.data.to_group_id){
			  	if($('.chat_history').attr("data-togroupid-chat") == message.data.to_group_id){
			  		if(message.from_user_id == id_user){
			  			if(typeFile[0] == "image"){
			  				$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><b>'+message.data.fromName+'</b><br><span><img id="imgUser" src="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
			  			}else if(typeFile[0] == "video"){
			  				$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><b>'+message.data.fromName+'</b><br><span><img id="clickVideo" src="material/file.png" urlvideo="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
			  			}else if(typeFile[0] == "application"){
			  				if(message.data.typeFile == "pdf"){
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><b>'+message.data.fromName+'</b><br><span><img id="clickPdf" src="material/pdf.png" urlPdf="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}else if(message.data.typeFile == "docx"){
		  				// $('.list-unstyled').append('<li><p id-message="'+message.data.chat_message_id+'" id="messageUser"><b>'+message.data.from_name_user+'</b><br><span>'+message.data.chat_message_group+'</span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><b>'+message.data.fromName+'</b><br><span><img id="clickWorld" src="material/doc.png" urlWord="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}else if(message.data.typeFile == "xlsx"){
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'" id="messageUser"><b>'+message.data.fromName+'</b><br><span><img id="clickExel" src="material/xls.png" urlExel="'+message.data.chat_message+'"></span><div align="right"><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}
			  			}
			  		}else{
			  			if(typeFile[0] == "image"){
			  				$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><b>'+message.data.fromName+'</b><br><span><img id="imgUser" src="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');
			  			}else if(typeFile[0] == "video"){
			  				$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><b>'+message.data.fromName+'</b><br><span><img id="clickVideo" src="material/file.png" urlvideo="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');
			  			}else if(typeFile[0] == "application"){
			  				if(message.data.typeFile == "pdf"){
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><b>'+message.data.fromName+'</b><br><span><img id="clickPdf" src="material/pdf.png" urlPdf="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}else if(message.data.typeFile == "docx"){
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><b>'+message.data.fromName+'</b><br><span><img id="clickWorld" src="material/doc.png" urlWord="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}else if(message.data.typeFile == "xlsx"){
			  					$('.list-unstyled').append('<li><p id-message="'+message.data.id_message+'"><b>'+message.data.fromName+'</b><br><span><img id="clickExel" src="material/xls.png" urlExel="'+message.data.chat_message+'"></span><div><small><em>'+message.data.posted+'</em></small></div></p></li>');	
			  				}
			  			}			  			
			  		}
			  	}
			  	// var updateMes = {
			  	// 	type:"updateMes",
			  	// 	id_message:message.data.id_message
			  	// }
			  	// socket.send(JSON.stringify(updateMes))
			  }
			}
		}
		setInterval(function(){
			socket.send(JSON.stringify({type:"updateLastActivity",id_user:id_user}));
		},1000);
	}


}
function createBtnMessage(id_message,message,from,id_user){
	var deleteBtn = '';
	if(from == id_user){
		deleteBtn = '<button class="deleteMessage" data-id-message="'+id_message+'">&times;</button>';
	}
	var AllBtn = "<button data-id_message='"+id_message+"' id='fixedMessage'>Закрепить</button><button id='noticeThisMessage' id-message='"+id_message+"'>Напомнить</button><button text-message='"+message+"' id='quotesBtn' id-message='"+id_message+"'>Цитировать</button><button id='forwardBtn' id-message='"+id_message+"' text-message='"+message+"'>Переслать</button>"+deleteBtn;
	return AllBtn;
}

function createBtnMessageGroup(all,id_message,message,from,id_user){
	console.dir(message);
	var deleteBtn = '';
	if(from == id_user){
		deleteBtn = '<button class="deleteMessage" data-id-message="'+id_message+'">&times;</button>';
	}
	var AllBtn = "<button data-id_message='"+id_message+"' id='fixedMessageGroup' to_group_id='"+all.data.to_group_id+"'>Закрепить</button><button id='noticeThisMessage' id-message='"+id_message+"'>Напомнить</button><button text-message='"+message+"' id='quotesBtn' id-message='"+id_message+"'>Цитировать</button><button id='forwardBtn' id-message='"+id_message+"' text-message='"+message+"'>Переслать</button>"+deleteBtn;
	return AllBtn;
}




		//получить всех пользователей и показать их
		var masCountMessage = [];
		function fetch_user(){
			$.ajax({
				url:"php/fetch_user.php",
				method:"POST",
				success:function(data){
					$('#user_details').html(data);
					var allLi = $('#user_details ul li');
					for(var i = 0; i < allLi.length;i++){
						var span = $(allLi[i]).children().first().children().text();
						var li = $(allLi[i])
						if(parseInt(span)){
							console.dir(li.attr("data-touserid"));
							console.dir(span);
							masCountMessage[li.attr("data-touserid")] = span;
						}else{
							masCountMessage[li.attr("data-touserid")] = 0;
						}
					}

					// console.dir(masCountMessage);
				}
			})
		} 
		//обновить дату и время когда пользователь заходил
		function update_last_activity(){
			$.ajax({
				url:"php/update_last_activity.php",
				success:function()
				{

				}
			})
		}

//для определения пересилает ли пользователь сообщение
function make_chat_dialog(to_user_id, to_user_name){
	var modal_content = '<div id="user_dialog_'+to_user_id+'" class="user_dialog" title="You have chat with '+to_user_name+'">';
	modal_content += '<div id="name_companion">'+fetch_companion(to_user_id)+'</div>';
	modal_content += '<button id="showFile" to_user_id='+to_user_id+'>Коллекция</button>';
	modal_content += '<input type="text" id="searchMessageUser" touserid="'+to_user_id+'">';
	modal_content += '<select id="selectName"  to_user_id='+to_user_id+'>'+nameUserForFilter(to_user_id)+'</select>'
	//modal_content += '<div class="dropdown"><span><img src="material/point.png" alt="" /></span><div class="dropdown-content"><button id="showFile" to_user_id='+to_user_id+'>Коллекция</button><button id="showMessage" to_user_id='+to_user_id+'>Показать все сообщения</button><select id="selectName"  to_user_id='+to_user_id+'>'+nameUserForFilter(to_user_id)+'</select></div></div>';
	modal_content += '<div style="" class="chat_history" data-touserid="'+to_user_id+'" id="chat_history_'+to_user_id+'">';
	modal_content += fetch_user_chat_history(to_user_id);
	modal_content += '</div>';
	modal_content += '<div id="btnNavidation"><button id="forwardBtn">Переслать</button><button id="noticeThisMessage" id-message="">Напомнить</button><button data-id_message="" id="fixedMessage">Закрепить</button><button text-message="" id="quotesBtn" id-message="">Цитировать</button></div>'
	modal_content += '<div class="blockQuotes"></div>';
	modal_content += '<div class="form-group" style="display:flex;margin-bottom:6px!important;">';
	modal_content += '<textarea name="chat_message_'+to_user_id+'" id="chat_message_'+to_user_id+'" class="form-control" style="width: 90%!important;"></textarea>';
	modal_content += '<label class="fileSend" to_user_id="'+to_user_id+'"><input type="file" name="picture" multiple="multiple"/></label>';
	modal_content += '</div><div class="form-group">';
	modal_content += '<button type="button" name="send_chat" id="'+to_user_id+'" class="btn btn-info send_chat">Отправить</button>';
	modal_content += '</div></div>'
	$('#user_chat').html(modal_content);
	checkForwardMessage = false;
	masForwardMessage.length = 0;
	masCountMessage[to_user_id] = 0;
	$('.chat_history').animate({scrollTop:9999999999999},1)
	// var x = document.getElementByClassName('fixedMessageLi')
	// console.dir(x);
	
}
 		//открывает диалог
 		$(document).on('click', '.start_chat', function(){
 			var to_user_id = $(this).data('touserid');
 			var to_user_name = $(this).data('tousername');
 			if($('.chat_history').attr("data-touserid") != to_user_id){
 				make_chat_dialog(to_user_id, to_user_name);	

 			}
 			var tr = $("#user_details ul li[data-touserid='"+to_user_id+"'] h6 span");
 			$(tr).remove();
				console.dir($('.fixedMessageLi'))

 		})
 		//отправка сообщений (личный чат)
 		// $(document).on('click', '.send_chat', function(){
	 	// 	var to_user_id = $(this).attr('id');
	 	// 	var chat_message = $('#chat_message_'+to_user_id).val();
	 	// 	var id_message = $("#textQuotes").attr('id-message');
	 	// 	console.dir(id_message)
	 	// 	console.dir(id_message);
 		// 	if(checkForwardMessage == true){
 		// 		var forwardText = $('#textQuotes').text();
 		// 		$.ajax({
 		// 			url:"php/insert_chat.php",
 		// 			method:"POST",
 		// 			data:{to_user_id:to_user_id,chat_message:chat_message,id_message:id_message,forwad:true},
 		// 			success:function(data){
 		// 				$('#chat_message_'+to_user_id).val('');
	 	// 				$('#chat_history_'+to_user_id).html(data);
 		// 			}
 		// 		})
 		// 	checkForwardMessage = false;
 		// 	$('.blockQuotes').html("");
 		// 	$('.blockQuotes').css("display","none");
 		// 	}else{
	 	// 		$.ajax({
	 	// 			url:"php/insert_chat.php",
	 	// 			method:"POST",
	 	// 			data:{to_user_id:to_user_id, chat_message:chat_message},
	 	// 			success:function(data)
	 	// 			{
	 	// 				console.dir(data);
	 	// 				$('#chat_message_'+to_user_id).val('');
	 	// 				$('#chat_history_'+to_user_id).html(data);
	 	// 			}
	 	// 		})
 		// 	}
 		// });
 		function fetch_companion(to_user_id){
 			$.ajax({
 				url:"php/fetch_companion.php",
 				method:"POST",
 				data:{"fetch_companion":true,"to_user_id":to_user_id},
 				success:function(data){
 					console.dir(data);
 					$('#name_companion').html(data);
 				}
 			})
 		}

 		//обновления сообщений в откритом чате
 		function fetch_user_chat_history(to_user_id){
 			$.ajax({
 				url:"php/fetch_user_chat_history.php",
 				method: "POST",
 				data:{to_user_id:to_user_id},
 				success:function(data){
 					$('#chat_history_'+to_user_id).html(data);
 				}
 			})
 		}


 		function update_chat_history_data(){
 			$('.chat_history').each(function(){
 				var to_user_id = $(this).data('touserid');
 				var to_group_id = $(this).data('togroupid-chat');
 				//в зависимости что будет открыто личный чат или группы
 				// console.dir(to_group_id)
 				if(to_group_id != undefined){
 					fetch_group_chat_history(to_group_id);
 				}else{
 					fetch_user_chat_history(to_user_id);
 				}
 			})
 		}
 		//удалить свое сообщение
 		// $(document).on('click', '.deleteMessage', function(){
 		// 	var isAdmin = confirm("Удалить сообщение ?");
 		// 	var id_message = $(this).data('id-message');
 		// 	console.dir(id_message);
 		// 	if(isAdmin){
 		// 		$.ajax({
 		// 			url : "php/delete_message.php",
 		// 			method:"POST",
 		// 			data:{id_message:id_message},
 		// 			success:function(data){
 		// 				console.dir(data);
 		// 			}
 		// 		})
 		// 	}
 		// })


 		//отправить файл пользователю
 		// $(document).on('change', '.fileSend', function(){
 		// 	var input = $(this).children()[0];
 		// 	var files = input.files;
 		// 	var to_user_id = $(this).attr("to_user_id");
 		// 	console.dir(to_user_id);
 		// 	var formData = new FormData();
 		// 	$.each(files, function( key, value ){
 		// 		formData.append( key, value );
 		// 	});
 		// 	formData.append("my_file_upload",1);
 		// 	formData.append("to_user_id",to_user_id);
 		// 	$.ajax({
 		// 		url:"php/addFile.php",
 		// 		type:"POST",
 		// 		data:formData,
 		// 		cache: false,
 		// 		dataType: 'json',
 		// 		processData: false,
 		// 		contentType: false,
 		// 		success:function(data){
 		// 			console.dir(data);
 		// 		}
 		// 	})
 		// })


 		//при нажатии на img || video откриваеться в окне
 		$(document).on('click', '#imgUser', function(){
 			var urlImg = $(this)[0].currentSrc;
 			console.dir($('#openImg1')[0].src)
 			$('#openImg1')[0].src = urlImg;
 			$('#openImg1').modal();

 		})

 		$(document).on('click', '#closeOpenImg', function(){
 			$('#openImg').hide();
 		})

 		$(document).on('click', '#clickVideo', function(){
 			var urlVideo = $(this).attr("urlvideo");
 			$('#openVideo').children()[0].src = urlVideo;
 			$('#openVideo').modal();
 			$('#openVideo').css("max-width","800px");

 		})


		 //поиск в чате 
		 $(document).on('keyup', "#searchMessageUser", function(){
		 	var value = $(this).val();
		 	var userSearch = true;
		 	var touserid = $(this).attr("touserid");
		 	$.ajax({
		 		url:"php/searchMessage.php",
		 		type:"POST",
		 		data:{value:value,userSearch:userSearch,touserid:touserid},
		 		success:function(data){
		 			$('#resultSearch').html(data); 
		 		}
		 	})
		 	if(value == ""){
		 		$("#resultSearch").css("display", "none");
		 		$("#listUserGroup").css("display", "block");
		 	}else{
		 		$("#listUserGroup").css("display", "none");
		 		$("#resultSearch").css("display", "block");
		 		// $("#user_chat").css("float","right");
		 	}
		 })
		 //при клике на результат поиска формируються сообщения
		 $(document).on('click', "#foundMessage", function(){
		 	var showMessage = true;
		 	var to_user_id = $(this).attr("to_user_id");
		 	var id_message = $(this).attr("id_message");

		 	if($(".chat_history").attr("data-touserid") == to_user_id){
		 		var container = $('.chat_history')
		 		var scrollTo = $('.chat_history ul li p[id-message="'+id_message+'"]');
		 		$('.chat_history').animate({scrollTop:scrollTo.offset().top - container.offset().top + container.scrollTop()},1400)
		 	}else{
		 		make_chat_dialog(to_user_id);
		 		var container = $('.chat_history');
		 		function test(){
		 			var scrollTo = $('.chat_history ul li p[id-message='+id_message+']');
		 			console.dir(scrollTo);
		 			$('.chat_history').animate({scrollTop:scrollTo.offset().top - container.offset().top + container.scrollTop()},1400)
		 		}
		 		setTimeout(test,100);

		 	}
		 })

		 $(document).on('click','#foundUser',function(){
		 	console.dir("skasd");
		 	var to_user_id = $(this).attr("to_user_id");

		 	make_chat_dialog(to_user_id);	
		 })
		 $(document).on('click','#foundGroup',function(){
		 	var to_group_id = $(this).attr("to_group_id");
		 	make_chat_group_dialog(to_group_id)
		 	console.dir("FDS")
		 })
		 //глобальный поиск 
		 $(document).on('keyup', '#globalSearchInput', function(){
		 	var valueInput = $(this).val();
		 	
		 	$.ajax({
		 		url:"php/searchMessage.php",
		 		method:"POST",
		 		data:{valueInput:valueInput},
		 		success:function(data){
		 			// console.dir(data);
		 			$('#resultSearch').html(data); 
		 		}
		 	})
		 	if(valueInput == ""){
		 		$("#resultSearch").css("display", "none");
		 		$("#listUserGroup").css("display", "block");
		 	}else{
		 		$("#listUserGroup").css("display", "none");
		 		$("#resultSearch").css("display", "block");
		 		// $("#user_chat").css("float","right");
		 	}
		 })


		 	// console.dir(temporaryVariable[0]);
		 	var stopUpdateMessage = false;
		  // var temporaryVariable;
		  $(document).on('click', '#showFile', function(){
		  	var to_user_id = $(this).attr("to_user_id");
		  	var touserFile = true;
		  	stopUpdateMessage = true;
		  	$.ajax({
		  		url:"php/showFile.php",
		  		method:"POST",
		  		data:{to_user_id:to_user_id,touserFile:touserFile},
		  		success:function(data){
		  			if($('.chat_history').attr("data-touserid")){
		  				$('.chat_history').html(data); 
		  			}
		  		}
		  	})
		  })

		  $(document).on('click', '#showMessage', function(){
		  	stopUpdateMessage = false;
		  	update_chat_history_data();
		  })
		 //фильтр по сообщениям
		 function nameUserForFilter(to_user_id){
		 	var fetchName = true;
		 	
		 	
		 	$.ajax({
		 		url:"php/filterMessage.php",
		 		method:"POST",
		 		data:{fetchName:fetchName,to_user:to_user_id},
		 		success:function(data){
		 			// console.dir(data)
		 			$('#selectName').html(data);
		 		}

		 	})
		 }

		 $(document).on('change', '#selectName', function(){
			// console.dir($(this).val());
			var val = $(this).val();
			var selectName = true;
			var to_user = $(this).attr("to_user_id");
			// console.dir(to_user)
			stopUpdateMessage = true;
			
			$.ajax({
				url:"php/filterMessage.php",
				method:"POST",
				data:{val:val,selectName:selectName,to_user:to_user},
				success:function(data){
					console.dir(data);
					$(".chat_history").html(data);
				}
			})
		})


		 $(document).on('click','#fixedMessageLi',function(){
			// console.dir($(this).attr("id-message"));
			var id_message = $(this).attr("id-message");

			console.dir('p[id-message="'+id_message+'"]')
			var id = $('p[id-message="'+id_message+'"]');
			console.dir(id);


			var container = $('.chat_history'),
			scrollTo = $('p[id-message="'+id_message+'"]');

			$('.chat_history').animate({scrollTop:scrollTo.offset().top - container.offset().top + container.scrollTop() - 80},1400)
		})

		//кнопка в чате напомнить это сообщение 
		$(document).on('click','#noticeThisMessage',function(){
			$('#fieldForNiticeMessage').modal('open');
			masIdMessage.push(masForwardMessage[0]);
		})

		//цытировать
		$(document).on('click','#quotesBtn', function(){
			var textQuotes = $(this).attr("text-message");
			var id_message = $(this).attr("id-message");
			$('.blockQuotes').html("");
			$('.blockQuotes').append("<p id='textQuotes' id-message='"+id_message+"'>"+textQuotes+"</p>")
			$('.blockQuotes').css("display","block");
			checkForwardMessage = true;
		})

		//переслать 
		$(document).on('click','#forwardBtn', function(){
			var textForward = $(this).attr("text-message");
			$('#fieldForForwardText').attr("id-message",$(this).attr("id-message"));
			$('#fieldForForwardText').html(textForward);
			$('#fieldForForward').modal('open');
			var fieldForForwardOpen = true;
			$.ajax({
				url:"php/insert_chat.php",
				method:"POST",
				data:{fieldForForwardOpen:fieldForForwardOpen},
				success:function(data){
					// console.dir(data)
					$('#userGroupForwardSelect').html(data);
				}
			})
		})

		// $(document).on('click','#sendForwardMessage',function(){
		// 	$(this).toggleClass("btn-warning");
		// 	var textComentForward = $('#fieldForForwardInput').val();
		// 	var to_user_id = $(this).attr("to_user_id");
		// 	var fieldForForwardText = $('#fieldForForwardText').text();
		// 	var id_message = $("#fieldForForwardText").attr("id-message");
		// 	console.dir(id_message);
		// 	console.dir(fieldForForwardText);
		// 	console.dir(to_user_id);
		// 	console.dir(textComentForward);

		// })

		$(document).on('click','#clickPdf',function(){
			var urlPdf = $(this).attr("urlPdf");
			$('#openApplication').children()[0].src = urlPdf;
			$('#openApplication').children()[1].href = urlPdf;
			$('#openApplication').modal();
			$('#openApplication').css("max-width","800px");
		})
		$(document).on('click','#clickWorld',function(){
			// console.dir("FDS");
			var urlWord = $(this).attr("urlword");
			$('#openApplication').children()[0].src = "http://docs.google.com/gview?url="+urlWord+"&embedded=true";;
			$('#openApplication').children()[1].href = urlWord;
			$('#openApplication').modal();
			$('#openApplication').css("max-width","800px");
		})
		$(document).on('click','#clickExel',function(){
			// console.dir("FDS");
			var urlExel = $(this).attr("urlexel");
			$('#openApplication').children()[0].src = "http://docs.google.com/gview?url="+urlExel+"&embedded=true";
			$('#openApplication').children()[1].href = urlExel;
			$('#openApplication').modal();
			$('#openApplication').css("max-width","800px");
		})

		var OpenCLoseNavigation = false;
		var masForwardMessage = [];
		var id_forward_message = [];
		$(document).on('click','.list-unstyled li',function(){
			if(OpenCLoseNavigation == false){
				unique(masForwardMessage,$(this).children().first().attr("id-message"));
				id_forward_message.push($(this).children().first().attr("id-message"));
				// if(id_forward_message.indexOf($(this).children().first().attr("id-message")) != "-1"){
				// }
				// console.dir($(this).children().first().children().eq(1).text());
				// $(this).css("background","#001fff21")
				$("#quotesBtn").attr("text-message",$(this).children().first().children().eq(1).text())
				$("#quotesBtn").attr("id-message",masForwardMessage[0]);
				$("#fixedMessage").attr("data-id_message",masForwardMessage[0]);
				$('#btnNavidation').css("display","block");
				//для групи
				$('#fixedMessageGroup').attr("to_group_id",$('.chat_history').attr("data-togroupid-chat"));
				$('#fixedMessageGroup').attr("data-id_message",masForwardMessage[0]);

				// console.dir($(this).children().first());
				// var p = $('.chat_history ul li p');

				console.dir(masForwardMessage)
				for(var i = 0; i < id_forward_message.length; i++){
					if(masForwardMessage.indexOf(id_forward_message[i]) != "-1"){
						$('.chat_history ul li p[id-message='+id_forward_message[i]+']').parent().css("background","gainsboro")
					}else{
						$('.chat_history ul li p[id-message='+id_forward_message[i]+']').parent().css("background","white")
						id_forward_message.splice(i, 1);
					}
				}


				// console.dir(id_forward_message);
				if(masForwardMessage.length == 0){
					$('#btnNavidation').css("display","none");
					id_forward_message.length = 0;
				}
				else if(masForwardMessage.length > 1){
					$("#fixedMessage").css("visibility","hidden");
					$("#quotesBtn").css("visibility","hidden");
					$('#noticeThisMessage').css("visibility","hidden");
				}else if(masForwardMessage.length == 1){
					$("#fixedMessage").css("visibility","visible");
					$("#quotesBtn").css("visibility","visible");
					$('#noticeThisMessage').css("visibility","visible");
				}

			}
			// console.dir(masForwardMessage);
		})


//отобразить имя пользователя сверху
function showNameUser(){
	$.ajax({
		url:"php/fetch_self.php",
		method:"POST",
		data:{fetch_self:true},
		success:function(data){
			console.dir(data);
			$('#nameUser').html(data);
		}
	})
}
showNameUser();
//Group--------------------------------

///push notice
function notifyMe () {
	var notification = new Notification ("Все еще работаешь?", {
		tag : "ache-mail",
		body : "Пора сделать паузу и отдохнуть",
		icon : "https://itproger.com/img/notify.png"
	});
}

function notifSet () {
	console.dir(Notification)
	if (!("Notification" in window))
		alert ("Ваш браузер не поддерживает уведомления.");
	else if (Notification.permission === "granted")
		setTimeout(notifyMe, 1000);
	else if (Notification.permission !== "denied") {
		setTimeout(notifyMe, 1000);
			Notification.requestPermission (function (permission) {
				if (!('permission' in Notification))
					Notification.permission = permission;
				if (permission === "granted")
					setTimeout(notifyMe, 1000);
			});
		}
	}