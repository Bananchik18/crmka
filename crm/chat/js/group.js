
//получает группы
$('#group_chat').click(function(){
	$('#group_chat_dialog').modal('open');
	$('#is_active_group_chat_window').val('yes');

	$.ajax({
		url:"php/listUserGroups.php",
		method:"POST",
		success:function(data){
			$('#listOfUsers').html(data);
		}
	})	
	masOfUserGroup.length = 0; 
});


var masOfUserGroup = [];
$(document).on('click', '.selectUserGroup', function(){
 			// console.dir($(this).attr('data-select-user'))
 			var selector = $(this).attr('data-select-user');
 			// masOfUserGroup.push(selector);
 			masOfUserGroup = unique(masOfUserGroup,selector); 
 			console.dir(masOfUserGroup);

 		})

// $('#btnCreateGroup').click(function(){
// 	var chatName = $('#createGroupInput').val();
// 	$.ajax({
// 		url:"php/create_group_chat.php",
// 		method: "POST",
// 		data:{user:masOfUserGroup,group_name:chatName},
// 		success:function(data){
// 			console.dir(data)
// 			$('#user_group').html(data);
// 		}
// 	})
// })
//выбирает группы
function fetch_group(){
	$.ajax({
		url:"php/fetch_group.php",
		method: "POST",
		success:function(data){
			$('#user_group').html(data);
		}
	})
}

function unique(arr,number) {  
	if(arr.includes(number)){
		for(var i = 0; i < arr.length; i++){
			if(arr[i] == number){
				arr.splice(i,1);
			}
		}
	}else{
		arr.push(number);	
	}
	return arr;
}
//делает форму сообщения
function make_chat_group_dialog(to_group_id){
	var modal_content = '<div id="group_dialog_'+to_group_id+'" class="group_dialog">';
	modal_content += '<input type="text" id="searchMessageGroup" togroupid="'+to_group_id+'">';
	// modal_content += '<button id="showFile" to_group_id='+to_group_id+'>File</button>';
	// modal_content += '<button id="showMessage" to_group_id='+to_group_id+'>Показать все сообщения</button>';
	// modal_content += '<select id="selectNameGroup" to_group_id='+to_group_id+'>'+nameGroupForFilter(to_group_id)+'</select>';
	modal_content += '<div class="dropdown"><span><img src="material/point.png" alt="" /></span><div class="dropdown-content"><button id="showFile" to_group_id='+to_group_id+'>Коллекция</button><button id="showMessage" to_group_id='+to_group_id+'>Показать все сообщения</button><select id="selectNameGroup" to_group_id='+to_group_id+'>'+nameGroupForFilter(to_group_id)+'</select><button class="infoGroup" to_group_id='+to_group_id+'>О группе</button></div></div>'
	modal_content += '<div class="chat_history" data-togroupid-chat="'+to_group_id+'" id="group_chat_history_'+to_group_id+'">';
	modal_content += fetch_group_chat_history(to_group_id);
	modal_content += '</div>';
	modal_content += '<div id="btnNavidation"><button id="forwardBtn">Переслать</button><button id="noticeThisMessage" id-message="">Напомнить</button><button data-id_message="" id="fixedMessageGroup" to_group_id="">Закрепить</button><button text-message="" id="quotesBtn" id-message="">Цитировать</button></div>'
	modal_content += '<div class="blockQuotes"></div>';
	modal_content += '<div class="form-group">';
	modal_content += '<label class="fileSend" to_group_id="'+to_group_id+'" style="float:right;"><input type="file" name="picture" multiple="multiple"/></label>';
	modal_content += '<textarea name="group_chat_message_'+to_group_id+'" id="group_chat_message_'+to_group_id+'" class="form-control" style="width: 90%!important;"></textarea>';
	modal_content += '</div><div class="form-group" align="right">';
	modal_content += '</div></div>'
	modal_content+= '<button type="button" name="send_chat_group" id="'+to_group_id+'" class="btn btn-info send_chat_group">Отправить</button></div></div>';

	$('#user_chat').html(modal_content);
	checkForwardMessage = false;
	masForwardMessage.length = 0;
}

$(document).on('click', '.start_group_chat' , function(){
	var to_group_id = $(this).attr('data-group-chat');
	// var member_of_group = $(this).attr('member-of-group');
	make_chat_group_dialog(to_group_id);
})
//получает сообщения групового чата 
function fetch_group_chat_history(to_group_id){
	$.ajax({
		url:"php/fetch_group_chat_history.php",
		method:"POST",
		data: {to_group_id:to_group_id},
		success:function(data){
			
			$('#group_chat_history_'+to_group_id).html(data);
		} 
	})
}
function nameGroupForFilter(to_group_id){
	$.ajax({
		url:"php/filterMessageGroup.php",
		method:"POST",
		data:{to_group_id:to_group_id,fetchName:true},
		success:function(data){
			$('#selectNameGroup').html(data);
		}
	})
}
$(document).on('change','#selectNameGroup',function(){
	var val = $(this).val();
	var to_group_id = $(this).attr("to_group_id");
	$.ajax({
		url:"php/filterMessageGroup.php",
		method:"POST",
		data:{val:val,selectName:true,to_group_id:to_group_id},
		success:function(data){
			$('.chat_history').html(data);
		}
	})
})
		 //отправка сообщений в груповой чат
		 // $(document).on('click', '.send_chat_group', function(){
		 // 	var to_group_id = $(this).attr('id');
		 // 	var chat_message_group = $('#group_chat_message_'+to_group_id).val();
		 // 	var members = $(this).attr('member-chat');
		 // 	console.dir(members);
		 // 	$.ajax({
		 // 		url: "php/insert_chat_group.php",
		 // 		method: "POST",
		 // 		data: {to_group_id:to_group_id,chat_message_group:chat_message_group},
		 // 		success:function(data){
		 // 			console.dir(data)
		 // 			$('#group_chat_message_'+to_group_id).val('');
		 // 			$('#group_chat_history_'+to_group_id).html(data);
		 // 		}
		 // 	})
		 // })

		 //при клике на кнопку получает информацию про группу
		 $(document).on('click','.infoGroup' , function(){
		 	// var memberOfGroup = $(this).attr('member-of-group');
		 	var to_group_id = $(this).attr('to_group_id');
		 	console.dir(to_group_id)
		 	masOfUserGroupAdd.length = 0;
		 	$('.openModal').modal('open');
		 	$.ajax({
		 		url:"php/modal_window_group.php",
		 		method:"POST",
		 		data:{to_group_id:to_group_id},
		 		success:function(data){
		 			// console.dir(data)
		 			$('#infoAboutGroup').html(data);
		 		}
		 	})
		 });
		 //клик на кнопку выйти из группы
		 $(document).on('click', '.leaveFromGroup', function(){
		 	var to_group_id = $(this).attr('data-group');
		 	// console.dir(to_group_id)
		 	var isAdmin = $(this).attr('isadmin'); 
		 	$('.openModal').modal('hide');
		 	if(isAdmin != 1){
		 		deleteGroup(to_group_id);
		 	}else{
		 		$('#infoAboutGroup').html('<button id="closeWindowAddUserGroup"  to_group_id="'+to_group_id+'">Назад</button><button onclick=deleteGroup('+to_group_id+') id="deleteGroup">Удалить группу и выйти</button><button onclick=appointNewAdminGroup('+to_group_id+') id="appoint">Назначить нового администратора и выйти</button>');
		 	}
		 })
		 //выход из группы
		 function deleteGroup(to_group_id,newadmin){
		 	var obj = {};
		 	if(newadmin != undefined){
		 		obj.to_group_id = to_group_id;
		 		obj.newadmin = newadmin;
		 	}else{
		 		obj.to_group_id = to_group_id;
		 	}
		 	$.ajax({
		 		url:"php/leave_from_group.php",
		 		method:"POST",
		 		data:obj,
		 		success:function(data){
		 			console.dir(data);
		 			$('#user_group').html(data);
		 		}
		 	})
		 }
		 //получаем пользовтелей которых можно назначить админисратором группы
		 function appointNewAdminGroup(to_group_id){
		 	$.ajax({
		 		url:"php/appoinAdminGroup.php",
		 		method:"POST",
		 		data:{to_group_id:to_group_id},
		 		success:function(data){
		 			// console.dir(data)
		 			$('#infoAboutGroup').html(data);
		 		}
		 	})
		 }
		 //назначат администратора
		 $(document).on('click' , '.newadmingroup', function(){
		 	deleteGroup($(this).attr('to_group_id'),$(this).attr('id-user'));
		 })
		 //создает группу
		 $(document).on('click', '#btnAddMemberGroup', function(){
		 	var members = $(this).attr('members-of-group');
		 	var to_group_id = $(this).attr('id-group');
		 	$.ajax({
		 		url:"php/user_add_group.php",
		 		method:"POST",
		 		data:{members:members,to_group_id:to_group_id},
		 		success:function(data){
		 			$('#infoAboutGroup').html(data);
		 		}
		 	})
		 	masOfUserGroupAdd.length = 0;
		 })
		 var masOfUserGroupAdd = [];
		 $(document).on('click', '#selectBtnNewUserGroup', function(){
		 	var iduser = $(this).attr('id-user');
		 	masOfUserGroupAdd = unique(masOfUserGroupAdd,iduser); 
		 	console.dir(masOfUserGroupAdd);
		 })

		 //добавляет в группу новых пользовтелей
		 // $(document).on('click', '#btnAddNewUserToGroup', function(){
		 // 	var to_group_id = $(this).attr('to-group-id');
		 // 	if(masOfUserGroupAdd.length != 0){
		 // 		$.ajax({
		 // 			url:"php/user_add_group.php",
		 // 			method:"POST",
		 // 			data:{to_group:to_group_id,masNewUser:masOfUserGroupAdd},
		 // 			success:function(data){
		 // 				console.dir(data);
		 // 			}
		 // 		})
		 // 	}
		 // });
		 //кнопка назад елси нажал случайно добавить пользователя	
		 $(document).on('click' , '#closeWindowAddUserGroup', function(){
		 	var memberOfGroup = $(this).attr('data-members');
		 	var to_group_id = $(this).attr('to_group_id');

		 	$.ajax({
		 		url:"php/modal_window_group.php",
		 		method:"POST",
		 		data:{memberOfGroup:memberOfGroup,to_group_id:to_group_id},
		 		success:function(data){
		 			console.dir(data)
		 			$('#infoAboutGroup').html(data);
		 		}
		 	})
		 })



		 // $(document).on('click', '#foundGroup', function(){
		 // 	var groupid = $(this).attr("to_group_id");
		 // 	var id_message = $(this).attr("id_message");
		 // 	$.ajax({
		 // 		url:"php/searchMessage.php",
		 // 		method:"POST",
		 // 		data:{groupid:groupid,id_message:id_message},
		 // 		success:function(data){
		 // 			$('#user_chat').html(data);
		 // 			var need = $("#toSearchMessage");
		 // 			var position = $(need).offset().top;
		 // 			console.dir(position);
		 // 			console.dir(need);
		 // 			$('#group_chat_history_'+groupid).animate({scrollTop:position-80},1100); 
		 // 		}
		 // 	})
		 // })
		 $(document).on('keyup','#searchMessageGroup',function(){
		 	var to_group_id = $(this).attr("togroupid")
		 	var val = $(this).val();

		 	$.ajax({
		 		url:"php/searchMessage.php",
		 		method:"POST",
		 		data:{to_group_id:to_group_id,value:val,groupSearchList:true},
		 		success:function(data){
		 			$('#resultSearch').html(data); 
		 		}
		 	})
		 	if(val == ""){
		 		$("#resultSearch").css("display", "none");
		 		$("#listUserGroup").css("display", "block");
		 	}else{
		 		$("#listUserGroup").css("display", "none");
		 		$("#resultSearch").css("display", "block");
		 		// $("#user_chat").css("float","right");
		 	}
		 })
		 $(document).on('click','#foundMessageGroup',function(){
		 	// var showMessage = true;
		 	var to_group_id = $(this).attr("to_group_id");
		 	var id_message = $(this).attr("id_message");

 			if($(".chat_history").attr("data-togroupid-chat") == to_group_id){
 				var container = $('.chat_history')
 				var scrollTo = $('.chat_history ul li p[id-message="'+id_message+'"]');
				$('.chat_history').animate({scrollTop:scrollTo.offset().top - container.offset().top + container.scrollTop()},1400)
			}else{
				make_chat_group_dialog(to_group_id);
				var container = $('.chat_history');
				function test(){
 					var scrollTo = $('.chat_history ul li p[id-message='+id_message+']');
 					console.dir(scrollTo);
					$('.chat_history').animate({scrollTop:scrollTo.offset().top - container.offset().top + container.scrollTop()},1400)
				}
				setTimeout(test,100);
		 	
			}
		 })