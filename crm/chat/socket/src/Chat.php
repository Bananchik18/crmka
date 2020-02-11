<?php
namespace MyApp;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

include dirname(__DIR__) . '/config/config.php';


class Chat implements MessageComponentInterface {
    protected $clients;
    public $usersToClients = []; 
    public $userToDisconect = [];
    public function __construct() {
        global $conn;
        $this->dbh  = $conn;
        $this->clients = new \SplObjectStorage;
    }
    
    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;

        $textMsg = json_decode($msg,true);
        // var_dump($textMsg);

        if($textMsg['type'] == "sendMessageUser"){
            var_dump($textMsg);
            $this->sendMessageUser($textMsg);
        }else if($textMsg['type'] == "setUserSocket"){
            // var_dump($textMsg['id_user']);
            $this->usersToClients[$textMsg['id_user']] = $from;
            $this->userToDisconect[$from->resourceId] = $textMsg['id_user'];
            $this->updateLast($textMsg);
            foreach($this->clients as $client){
                $this->send($client, "sendStatusUser", array("id_user" => $textMsg['id_user'],"status"=>true),$textMsg['id_user']);
            }
        }else if($textMsg['type'] == "updateLastActivity"){
            $this->updateLast($textMsg);
        }else if($textMsg['type'] == "sendForwardMessage"){
            $this->sendForwardMessage($textMsg);
        }else if($textMsg['type'] == "deleteMessage"){
            $this->deleteMessage($textMsg);
        }else if($textMsg['type'] == "fixedMessage"){
            $this->fixedMessage($textMsg);
        }else if($textMsg['type'] == "deleteFixedMessage"){
            $this->deleteFixedMessage($textMsg);
        }else if($textMsg['type'] == "sendForwardMessageAnotherUser"){
            var_dump("sendAnother");
            $this->sendForwardMessageAnotherUser($textMsg);
        }else if($textMsg['type'] == "updateMes"){
            $this->updateMes($textMsg);
        }
        //группа
        else if($textMsg['type'] == "sendMessageGroup"){
            $this->sendMessageGroup($textMsg);
        }else if($textMsg['type'] == "fixedMessageGroup"){
            $this->fixedMessageGroup($textMsg);
        }else if($textMsg['type'] == "deleteFixedMessageGroup"){
            $this->deleteFixedMessageGroup($textMsg);
        }else if($textMsg['type'] == "sendForwardMessageGroup"){
            $this->sendForwardMessageGroup($textMsg);
        }else if($textMsg['type'] == "createGroup"){
            $this->createGroup($textMsg);
        }else if($textMsg['type'] == "deleteUserFromGroup"){
            $this->deleteUserFromGroup($textMsg);
        }else if($textMsg['type'] == "addNewUserGroup"){
            $this->addNewUserGroup($textMsg);
        }else if($textMsg['type'] == "sendFileUser"){
            $this->sendFileUser($textMsg);
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
       foreach($this->clients as $client){
        $this->send($client,"sendStatusUser",array("id_user"=>$this->userToDisconect[$conn->resourceId],"status"=>false),$this->userToDisconect[$conn->resourceId]);
    }
    $this->clients->detach($conn);

    echo "Connection {$conn->resourceId} has disconnected\n";

}

public function onError(ConnectionInterface $conn, \Exception $e) {
    echo "An error has occurred: {$e->getMessage()}\n";

    $conn->close();
}
public function send($client, $type, $data,$id){
    $send = array(
        "type" => $type,
        "from_user_id" => $id,
        "data" => $data
    );
    $send = json_encode($send, true);
        // var_dump($send);
    $client->send($send);
}
public function updateMes($textMsg){
    $sql = "UPDATE chat_message SET status = '0' WHERE `chat_message_id`=".$textMsg['id_message'];
    $result = $this->dbh->query($sql);
} 
public function updateLast($textMsg){
    $sql = "UPDATE `login_details` SET `last_activity` = now() WHERE `login_details`.`user_id` = " . $textMsg['id_user'];
    $result = $this->dbh->query($sql);
}
public function sendMessageUser($textMsg){
    $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, NULL, '".$textMsg['to_user_id']."', '".$textMsg['from_user_id']."', '".$textMsg['message']."', '-1', current_timestamp(), '1');";
    $result = $this->dbh->query($sql);
    $idMessage = $this->dbh->insert_id;
    $text = $textMsg['message'];

    $sqlName = "SELECT * FROM `users` WHERE `id`=".$textMsg['from_user_id'];
    $resultName = $this->dbh->query($sqlName);
    var_dump($sqlName);
    $rowName = $resultName->fetch_assoc();
    foreach($this->clients as $client){
        if($this->usersToClients[$textMsg['to_user_id']] == $client || $this->usersToClients[$textMsg['from_user_id']] == $client){
            $this->send($client, "sendMessageUser", array("id_message" => $idMessage, "chat_message" => $textMsg['message'] ,"to_user_id"=>$textMsg['to_user_id'], "posted" => date("Y-m-d H:i:s"),"fromName"=>$rowName['username']),$textMsg['from_user_id']);
        }
    }
}
public function sendForwardMessage($textMsg){
    $sqlGetMessage = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];

    $resultGetMessage = $this->dbh->query($sqlGetMessage);
    var_dump($textMsg);
    $rowGetMessage = $resultGetMessage->fetch_assoc();
    if($rowGetMessage['chat_message'] == "" && $rowGetMessage['forward'] != "" && $textMsg['chat_message'] == ""){
        var_dump("!===");
        $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, NULL, '".$textMsg['to_user_id']."', '".$textMsg['from_user_id']."', '".$rowGetMessage['chat_message']."', '".$rowGetMessage['forward']."', now(), '1');";
        $result = $this->dbh->query($sql);
    }else if($rowGetMessage['chat_message'] == "" && $rowGetMessage['forward'] != "" && $textMsg['chat_message'] != ""){
        var_dump("== != !=");
        $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, NULL, '".$textMsg['to_user_id']."', '".$textMsg['from_user_id']."', '".$textMsg['chat_message']."', '".$rowGetMessage['forward']."', now(), '1');";
        $result = $this->dbh->query($sql);
    }else{
        var_dump("=====");
        $sqlToTableForward = "INSERT INTO `forward_message` (`id`,`from_user_id`,`to_user_id`,`forward_text`)
        VALUES (NULL,'".$rowGetMessage['from_user_id']."','".$rowGetMessage['to_user_id']."','".$rowGetMessage['chat_message']."')";

        $resultToTableForward = $this->dbh->query($sqlToTableForward);
        $lastIdTableForward = mysqli_insert_id($this->dbh);

        $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, NULL, '".$textMsg['to_user_id']."', '".$textMsg['from_user_id']."', '".$textMsg['chat_message']."', '".$lastIdTableForward."', now(), '1');";
        $result = $this->dbh->query($sql);
    }

    $sqlName = "SELECT * FROM `users` WHERE `id`=".$textMsg['from_user_id'];
    $resultName = $this->dbh->query($sqlName);
    $rowName = $resultName->fetch_assoc();

    $sqlTextForward = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
    $resultTextForward = $this->dbh->query($sqlTextForward);
    $rowTextForward = $resultTextForward->fetch_assoc();

    $sqlNameForward = "SELECT * FROM `users` WHERE `id`=".$rowTextForward['from_user_id'];
    $resultNameForward = $this->dbh->query($sqlNameForward);
    $rowNameForward = $resultNameForward->fetch_assoc();

    foreach($this->clients as $client){
        if($this->usersToClients[$textMsg['to_user_id']] == $client || $this->usersToClients[$textMsg['from_user_id']] == $client){
            $this->send($client, "sendForwardMessage", array("id_message" => $textMsg['id_message'], "chat_message" => $textMsg['chat_message'] ,"to_user_id"=>$textMsg['to_user_id'], "posted" => date("Y-m-d H:i:s"), "fromName"=>$rowName['username'],"textForward"=>$rowTextForward['chat_message'],"NameForward"=>$rowNameForward['username']),$textMsg['from_user_id']);
        }
    }
}
public function deleteMessage($textMsg){
    $sql = "DELETE FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
    $result = $this->dbh->query($sql);
    foreach($this->clients as $client){
        $this->send($client, "sendDeleteMessage", array("id_message" => $textMsg['id_message']),$textMsg['from_user_id']);
    }
}
public function fixedMessage($textMsg){

    $sql = "SELECT * FROM `fixed_message` WHERE `from_user_id`=".$textMsg['from_user_id']." AND `to_user_id`=".$textMsg['to_user_id']."
    OR `from_user_id`=".$textMsg['to_user_id']." AND `to_user_id`=".$textMsg['from_user_id'];

    $idFixedMessage = [];

    $resultIssetRecord = $this->dbh->query($sql);
    if($resultIssetRecord->num_rows > 0){
        $rowIssetRecord = $resultIssetRecord->fetch_assoc();
        $sqlGetMessage = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
        $resultGetMessage = $this->dbh->query($sqlGetMessage);
        $rowGetMessage = $resultGetMessage->fetch_assoc();

        $sql = "UPDATE fixed_message SET to_user_id = '".$textMsg['to_user_id']."', from_user_id = '".$textMsg['from_user_id']."', fixed_message = '".$rowGetMessage['chat_message']."', timestamp = now(),message_id = ".$rowGetMessage['chat_message_id']." 
        WHERE fixed_message_id = ".$rowIssetRecord['fixed_message_id'].";";

        $result = $this->dbh->query($sql);
                // $idFixedMessage = $rowIssetRecord['fixed_message_id'];
        array_push($idFixedMessage, $rowIssetRecord['fixed_message_id']);

    }else{
        $sql = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
        $result = $this->dbh->query($sql);
        $row = $result->fetch_assoc();
        $sql = "INSERT INTO `fixed_message` (`fixed_message_id`,`message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `fixed_message`, `timestamp`) VALUES (NULL,".$row['chat_message_id'].", NULL, '".$textMsg['to_user_id']."', '".$textMsg['from_user_id']."', '".$row['chat_message']."', now())";
        $result = $this->dbh->query($sql);
        $lastIdTableForward = mysqli_insert_id($this->dbh);
                // $idFixedMessage = $lastIdTableForward;
        array_push($idFixedMessage, $lastIdTableForward);
    }

    $sql = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
    $result = $this->dbh->query($sql);
    $row = $result->fetch_assoc();
    foreach($this->clients as $client){
        if($this->usersToClients[$textMsg['to_user_id']] == $client || $this->usersToClients[$textMsg['from_user_id']] == $client){
            $this->send($client, "fixedMessage", array("id_message" => $textMsg['id_message'],"id_fixed_message" => $idFixedMessage[0], "fixedMessage" => $row['chat_message']),$textMsg['from_user_id']);
        }
    }
}
public function deleteFixedMessage($textMsg){
    $sql = "DELETE FROM `fixed_message` WHERE `fixed_message_id`=".$textMsg['idFixedMessage'];
    $result = $this->dbh->query($sql);
    foreach($this->clients as $client){
        $this->send($client, "deleteFixedMessage", array("id_message" => $textMsg['idFixedMessage']),$textMsg['from_user_id']);
    }
}
public function sendForwardMessageAnotherUser($textMsg){

    $comentText = $textMsg['textComentForward'];

    $masAllMessage = [];
    $idMessageForward = [];
    // asort($textMsg['id_message']);
    var_dump($textMsg);
    for($i = 0; $i < count($textMsg['id_message']); $i++){
        // var_dump($textMsg['id_message']);
        $sqlGetMessage = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'][$i]." ORDER BY `chat_message`.`chat_message_id` ASC";
        $resultGetMessage = $this->dbh->query($sqlGetMessage);
        $rowGetMessage = $resultGetMessage->fetch_assoc();
        array_push($masAllMessage, $rowGetMessage);  
        if($masAllMessage[$i]['chat_message'] == "" && $masAllMessage[$i]['forward'] != ""){
            array_push($idMessageForward,$masAllMessage[$i]['forward']);
        }else{        
            $sqlToForwardMessage = "INSERT INTO `forward_message` (`id`,`to_user_id`,`from_user_id`,`forward_text`)
            VALUES (NULL,'".$masAllMessage[$i]['to_user_id']."','".$masAllMessage[$i]['from_user_id']."','".$masAllMessage[$i]['chat_message']."')";
            $resultToForwardMessage = $this->dbh->query($sqlToForwardMessage);
            $lastIdTableForward = mysqli_insert_id($this->dbh);
            array_push($idMessageForward,$lastIdTableForward);
        }

    }
    // var_dump($masAllMessage);


    $masIdUserFirstMes = [];
    $masIdUserAnotherMes = [];
    if(!empty($textMsg['to_user_id'])){
            // ".$comentText."
        if($comentText != ""){
            $sqlComentText = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, NULL, '".$textMsg['to_user_id']."', '".$textMsg['from_user_id']."', '".$comentText."', '-1', now(), '1');";
            $resultComentText = $this->dbh->query($sqlComentText);
            $idMessage = $this->dbh->insert_id;
            array_push($masIdUserFirstMes, $idMessage);
        }
        for($i = 0; $i < count($masAllMessage);$i++){

            $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, NULL, '".$textMsg['to_user_id']."', '".$textMsg['from_user_id']."', '', '".$idMessageForward[$i]."', now(), '1');";
            $result = $this->dbh->query($sql);
            $idMessage = $this->dbh->insert_id;
            array_push($masIdUserAnotherMes, $idMessage);
        }
    }else if(!empty($textMsg['to_group_id'])){
        var_dump($textMsg);
        $sqlMemberGroup = "SELECT * FROM `group_chats` WHERE `id`=".$textMsg['to_group_id'];
        $resultMemberGroup = $this->dbh->query($sqlMemberGroup);
        $rowMemberGroup = $resultMemberGroup->fetch_assoc();
        if($comentText != ""){
            $sqlComentText = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, ".$textMsg['to_group_id'].", '".$rowMemberGroup['members_chat']."', '".$textMsg['from_user_id']."', '".$comentText."', '-1', now(), '1');";
            $resultCometnText = $this->dbh->query($sqlComentText);

        }
        for($i = 0; $i < count($masAllMessage);$i++){       


            $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, ".$textMsg['to_group_id'].", '".$rowMemberGroup['members_chat']."', '".$textMsg['from_user_id']."', '', '".$idMessageForward[$i]."', now(), '1');";
            $result = $this->dbh->query($sql);
            $idMessage = $this->dbh->insert_id;
        }
    }

    $trueForFirtsMessage = true;
    if($trueForFirtsMessage){
        foreach($this->clients as $client){
            if($this->usersToClients[$textMsg['to_user_id']] == $client || $this->usersToClients[$textMsg['from_user_id']] == $client){
                $this->send($client, "sendForwardMessageAnotherUser", array("id_message" => $masIdUserFirstMes[0], "chat_message" => $textMsg['textComentForward'] ,"to_user_id"=>$textMsg['to_user_id'], "posted" => date("Y-m-d H:i:s"),"firstMes"=>true),$textMsg['from_user_id']);
            }
        }
        $trueForFirtsMessage = false;
    }  
    for($i = 0; $i < count($textMsg['id_message']); $i++){
        $sqlName = "SELECT * FROM `users` WHERE `id`=".$textMsg['from_user_id'];
        $resultName = $this->dbh->query($sqlName);
        $rowName = $resultName->fetch_assoc();

        $sqlTextForward = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'][$i];
        $resultTextForward = $this->dbh->query($sqlTextForward);
        $rowTextForward = $resultTextForward->fetch_assoc();

        $sqlNameForward = "SELECT * FROM `users` WHERE `id`=".$rowTextForward['from_user_id'];
        $resultNameForward = $this->dbh->query($sqlNameForward);
        $rowNameForward = $resultNameForward->fetch_assoc();

        if(!empty($textMsg['to_user_id'])){
            foreach($this->clients as $client){
                if($this->usersToClients[$textMsg['to_user_id']] == $client || $this->usersToClients[$textMsg['from_user_id']] == $client){
                    $this->send($client, "sendForwardMessageAnotherUser", array("id_message" => $masIdUserAnotherMes[$i], "chat_message" => $textMsg['textComentForward'] ,"to_user_id"=>$textMsg['to_user_id'], "posted" => date("Y-m-d H:i:s"), "fromName"=>$rowName['username'],"textForward"=>$rowTextForward['chat_message'],"NameForward"=>$rowNameForward['username']),$textMsg['from_user_id']);
                }
            }
            

        }else if(!empty($textMsg['to_group_id'])){
            $sql = "SELECT * FROM `group_chats` WHERE id=" . $textMsg['to_group_id'];
            $result = $this->dbh->query($sql);
            $id_group = $result->fetch_assoc()['members_chat'];
            $masGroupUser = explode(",", $id_group);
            $newarr = [];

            for($i = 0; $i < count($masGroupUser);$i++){
                if(array_key_exists($masGroupUser[$i],$this->usersToClients)){
                    array_push($newarr,$masGroupUser[$i]);
                }
            }
            foreach($this->clients as $client){
                for($i = 0;$i < count($newarr);$i++){
                    if($this->usersToClients[$newarr[$i]] == $client){
                        $this->send($client, "sendForwardMessageAnotherUser", array("id_message" => $idMessage, "chat_message" => $textMsg['textComentForward'] ,"to_user_id"=>$textMsg['to_user_id'],"to_group_id"=>$textMsg['to_group_id'], "posted" => date("Y-m-d H:i:s"), "fromName"=>$rowName['username'],"textForward"=>$rowTextForward['chat_message'],"NameForward"=>$rowNameForward['username']),$textMsg['from_user_id']);
                    }
                }
            }  
        }
    }

}
public function sendGroup($client, $type, $data,$id){
    $send = array(
        "type" => $type,
        "from_group" => $id,
        "data" => $data
    );
    $send = json_encode($send, true);
        // var_dump($send);
    $client->send($send);
}
public function sendMessageGroup($textMsg){
    $sql = "SELECT * FROM `group_chats` WHERE id=" . $textMsg['to_group_id'];
    $result = $this->dbh->query($sql);
    $id_group = $result->fetch_assoc()['members_chat'];
    $masGroupUser = explode(",", $id_group);
    $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `timestamp`, `status`) VALUES (NULL, '".$textMsg['to_group_id']."', '".$id_group."', '".$textMsg['from_user_id']."', '".$textMsg['chat_message_group']."', current_timestamp(), '1')";
    $result = $this->dbh->query($sql);
    $lastIdMessageGroup = mysqli_insert_id($this->dbh);

    $newarr = [];
    for($i = 0; $i < count($masGroupUser);$i++){
        if(array_key_exists($masGroupUser[$i],$this->usersToClients)){
            array_push($newarr,$masGroupUser[$i]);
        }
    }
    $sqlName = "SELECT * FROM `users` WHERE `id`=".$textMsg['from_user_id'];
    $resultName = $this->dbh->query($sqlName);
    $rowName = $resultName->fetch_assoc();
    var_dump(array_keys($this->usersToClients));
    // asort($newarr);
    foreach ($this->clients as $client) {
        for($i = 0;$i < count($newarr);$i++){
            if($this->usersToClients[$newarr[$i]] == $client){
                $this->sendGroup($client,"sendMessageGroup", array("chat_message_id"=>$lastIdMessageGroup, "chat_message_group"=>$textMsg['chat_message_group'],"posted" => date("Y-m-d H:i:s"),"from_user_id"=>$rowName['id'],"from_name_user"=>$rowName['username'],"to_group_id"=>$textMsg['to_group_id']), $textMsg['to_group_id']);
            }
        }
    }
}
public function fixedMessageGroup($textMsg){
    $sql = "SELECT * FROM `fixed_message` WHERE `id_chat_group`=".$textMsg['to_group_id'];
    $idFixedMessage = [];

    $resultIssetRecord = $this->dbh->query($sql);
    if($resultIssetRecord->num_rows > 0){
        $rowIssetRecord = $resultIssetRecord->fetch_assoc();
        $sqlGetMessage = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
        $resultGetMessage = $this->dbh->query($sqlGetMessage);
        $rowGetMessage = $resultGetMessage->fetch_assoc();

        $sql = "UPDATE fixed_message SET from_user_id = '".$textMsg['from_user_id']."', fixed_message = '".$rowGetMessage['chat_message']."', timestamp = now(),message_id = ".$rowGetMessage['chat_message_id']." 
        WHERE fixed_message_id = ".$rowIssetRecord['fixed_message_id'].";";

        $result = $this->dbh->query($sql);
                // $idFixedMessage = $rowIssetRecord['fixed_message_id'];
        array_push($idFixedMessage, $rowIssetRecord['fixed_message_id']);

    }else{
        $sql = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
        $result = $this->dbh->query($sql);
        $row = $result->fetch_assoc();
        $sql = "INSERT INTO `fixed_message` (`fixed_message_id`,`message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `fixed_message`, `timestamp`) VALUES (NULL,".$row['chat_message_id'].", ".$textMsg['to_group_id'].", 'NULL', '".$textMsg['from_user_id']."', '".$row['chat_message']."', now())";
        $result = $this->dbh->query($sql);
        $lastIdTableForward = mysqli_insert_id($this->dbh);
                // $idFixedMessage = $lastIdTableForward;
        array_push($idFixedMessage, $lastIdTableForward);
    }

    $sql = "SELECT * FROM `group_chats` WHERE id=" . $textMsg['to_group_id'];
    $result = $this->dbh->query($sql);
    $id_group = $result->fetch_assoc()['members_chat'];
    $masGroupUser = explode(",", $id_group);
    $newarr = [];

    for($i = 0; $i < count($masGroupUser);$i++){
        if(array_key_exists($masGroupUser[$i],$this->usersToClients)){
            array_push($newarr,$masGroupUser[$i]);
        }
    }

    $sql = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
    $result = $this->dbh->query($sql);
    $row = $result->fetch_assoc();
    foreach($this->clients as $client){
        for($i = 0;$i < count($newarr);$i++){
            if($this->usersToClients[$newarr[$i]] == $client){
                $this->sendGroup($client, "fixedMessageGroup", array("id_message" => $textMsg['id_message'],"id_fixed_message" => $idFixedMessage[0], "fixedMessage" => $row['chat_message']),$textMsg['to_group_id']);
            }
        }
    }
}
public function deleteFixedMessageGroup($textMsg){
    $sql = "DELETE FROM `fixed_message` WHERE `fixed_message_id`=".$textMsg['idFixedMessage'];
    $result = $this->dbh->query($sql);
    foreach($this->clients as $client){
        $this->send($client, "deleteFixedMessageGroup", array("id_message" => $textMsg['idFixedMessage']),$textMsg['from_user_id']);
    }
}
public function sendForwardMessageGroup($textMsg){
 var_dump($textMsg); 

 $sqlGetMessage = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];

 $resultGetMessage = $this->dbh->query($sqlGetMessage);
 $rowGetMessage = $resultGetMessage->fetch_assoc();
 var_dump($rowGetMessage);
 $sqlMemberGroup = "SELECT * FROM `group_chats` WHERE `id`=".$textMsg['to_group_id'];
 $resultMemberGroup = $this->dbh->query($sqlMemberGroup);
 $rowMemberGroup = $resultMemberGroup->fetch_assoc();
 if($rowGetMessage['chat_message'] == "" && $rowGetMessage['forward'] != "" && $textMsg['chat_message_group'] == ""){
    var_dump("---------------");

    $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, ".$textMsg['to_group_id'].", '".$rowMemberGroup['members_chat']."', '".$textMsg['from_user_id']."', '".$rowGetMessage['chat_message']."', '".$rowGetMessage['forward']."', now(), '1');";
    $result = $this->dbh->query($sql); 
    $lastIdTableForward = mysqli_insert_id($this->dbh);
}else if($rowGetMessage['chat_message'] == "" && $rowGetMessage['forward'] != "" && $textMsg['chat_message_group'] != ""){
    $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, ".$textMsg['to_group_id'].", '".$rowMemberGroup['members_chat']."', '".$textMsg['from_user_id']."', '".$textMsg['chat_message_group']."', '".$rowGetMessage['forward']."', now(), '1');";
    $result = $this->dbh->query($sql);
    // var_dump($sql);
    $lastIdTableForward = mysqli_insert_id($this->dbh);
}else{
 $sqlToTableForward = "INSERT INTO `forward_message` (`id`,`to_user_id`,`from_user_id`,`forward_text`)
 VALUES (NULL,'".$rowGetMessage['to_user_id']."','".$rowGetMessage['from_user_id']."','".$rowGetMessage['chat_message']."')";
 // var_dump($sqlToTableForward);
 $resultToTableForward = $this->dbh->query($sqlToTableForward);
 $lastIdTableForward = mysqli_insert_id($this->dbh);



 $sql = "INSERT INTO `chat_message` (`chat_message_id`, `id_chat_group`, `to_user_id`, `from_user_id`, `chat_message`, `forward`, `timestamp`, `status`) VALUES (NULL, ".$textMsg['to_group_id'].",'".$rowMemberGroup['members_chat']."', '".$textMsg['from_user_id']."', '".$textMsg['chat_message_group']."', '".$lastIdTableForward."', now(), '1');";
 // var_dump($sql);
 $result = $this->dbh->query($sql);
 $lastIdTableForward = mysqli_insert_id($this->dbh);
}

$sqlName = "SELECT * FROM `users` WHERE `id`=".$textMsg['from_user_id'];
$resultName = $this->dbh->query($sqlName);
$rowName = $resultName->fetch_assoc();

$sqlTextForward = "SELECT * FROM `chat_message` WHERE `chat_message_id`=".$textMsg['id_message'];
$resultTextForward = $this->dbh->query($sqlTextForward);
$rowTextForward = $resultTextForward->fetch_assoc();

$sqlNameForward = "SELECT * FROM `users` WHERE `id`=".$rowTextForward['from_user_id'];
$resultNameForward = $this->dbh->query($sqlNameForward);
$rowNameForward = $resultNameForward->fetch_assoc();

$sql = "SELECT * FROM `group_chats` WHERE id=" . $textMsg['to_group_id'];
$result = $this->dbh->query($sql);
$id_group = $result->fetch_assoc()['members_chat'];
$masGroupUser = explode(",", $id_group);
$newarr = [];

for($i = 0; $i < count($masGroupUser);$i++){
    if(array_key_exists($masGroupUser[$i],$this->usersToClients)){
        array_push($newarr,$masGroupUser[$i]);
    }
}

foreach($this->clients as $client){
    for($i = 0;$i < count($newarr);$i++){
        if($this->usersToClients[$newarr[$i]] == $client){
            $this->send($client, "sendForwardMessageGroup", array("id_message" => $lastIdTableForward, "chat_message" => $textMsg['chat_message_group'] ,"to_group_id"=>$textMsg['to_group_id'], "posted" => date("Y-m-d H:i:s"), "fromName"=>$rowName['username'],"textForward"=>$rowTextForward['chat_message'],"NameForward"=>$rowNameForward['username']),$textMsg['from_user_id']);
        }
    }
}
}
public function createGroup($textMsg){

    $data = array(
        "membersChat" => $textMsg['user'],
        "nameGroup" => $textMsg['group_name'],
        "from_user_id" => $textMsg['from_user_id']
    );
    array_push($data['membersChat'], $textMsg['from_user_id']); 

    $members = implode(",",$data['membersChat']);
    // var_dump($members);
    
    $sql = "INSERT INTO `group_chats` (`id`, `name_chat`, `members_chat`, `creator_group`) VALUES (NULL, '".$data['nameGroup']."', '".$members."', '".$textMsg['from_user_id']."')";
    
    $result = $this->dbh->query($sql);
    $lastIdGroup = mysqli_insert_id($this->dbh);
    $sql = "SELECT * FROM `group_chats` WHERE id=" . $lastIdGroup;
    $result = $this->dbh->query($sql);
    $id_group = $result->fetch_assoc()['members_chat'];
    $masGroupUser = explode(",", $id_group);
    $newarr = [];

    for($i = 0; $i < count($masGroupUser);$i++){
        if(array_key_exists($masGroupUser[$i],$this->usersToClients)){
            array_push($newarr,$masGroupUser[$i]);
        }
    }
    // var_dump(count($newarr));
    foreach($this->clients as $client){
        for($i = 0;$i < count($newarr);$i++){
            if($this->usersToClients[$newarr[$i]] == $client){
                $this->send($client, "createGroup", array("id_group"=>$lastIdGroup,"members"=>$id_group,"name_group"=>$textMsg['group_name']),$textMsg['from_user_id']);
            }
        }
    }


}
public function deleteUserFromGroup($textMsg){
    var_dump($textMsg);
    $sqlMemberGroup = "SELECT * FROM `group_chats` WHERE `id`=".$textMsg['id_group'];
    $resultMemberGroup = $this->dbh->query($sqlMemberGroup);
    $rowMemberGroup = $resultMemberGroup->fetch_assoc();

    $members = explode(",",$rowMemberGroup['members_chat']);
    $key=array_search($textMsg['id_user'], $members);
    unset($members[$key]);
    
    $members = implode(",", $members);
    var_dump($members);

    $sqlUpdate = "UPDATE group_chats SET `members_chat`='".$members."' WHERE `id`=".$textMsg['id_group'];
    var_dump($sqlUpdate);
    $resultUpdate = $this->dbh->query($sqlUpdate);

    foreach ($this->clients as $client) {
        if($this->usersToClients[$textMsg['id_user']] == $client || $this->usersToClients[$textMsg['from_user_id']]){
            $this->send($client,"deleteUserFromGroup",array("id_group"=>$textMsg['id_group'],"members"=>$members),$textMsg['from_user_id']);
        }
    }
}
public function addNewUserGroup($textMsg){

    $sqlGroup = "SELECT * FROM `group_chats` WHERE `id`=".$textMsg['to_group_id'];
    $resultGroup = $this->dbh->query($sqlGroup);
    $members = $resultGroup->fetch_assoc();
    $membersNew = $members['members_chat']; 
    $membersNew .= "," . implode(",", $textMsg['masNewUser']);
    // var_dump($members);
    $sql = "UPDATE `group_chats` SET `members_chat`='".$membersNew."' WHERE `id`=" . $textMsg['to_group_id'];
    $result = $this->dbh->query($sql);
    // var_dump($textMsg['masNewUser']);
    // var_dump($members['name_chat']);
    foreach($this->clients as $client){
        for($i = 0;$i < count($textMsg['masNewUser']);$i++){
            if($this->usersToClients[$textMsg['masNewUser'][$i]] == $client){
                $this->send($client, "addNewUserGroup", array("id_group"=>$textMsg['to_group_id'],"members"=>$membersNew,"name_group"=>$members['name_chat']),$textMsg['from_user_id']);
            }
        }
    }

}
public function sendFileUser($textMsg){
    var_dump($textMsg);

    $sqlName = "SELECT * FROM `users` WHERE `id`=".$textMsg['data']['from_user_id'];
    $resultName = $this->dbh->query($sqlName);
    $rowName = $resultName->fetch_assoc();

    if(isset($textMsg['data']['to_user_id'])){   
        foreach($this->clients as $client){
            if($this->usersToClients[$textMsg['data']['to_user_id']] == $client || $this->usersToClients[$textMsg['data']['from_user_id']] == $client){
                $this->send($client, "sendFileUser", array("id_message" => $textMsg['data']['id_messsgae'], "chat_message" => $textMsg['data']['src'] ,"to_user_id"=>$textMsg['data']['to_user_id'], "posted" => date("Y-m-d H:i:s"),"fromName"=>$rowName['username'],"typeFile"=>$textMsg['data']['typeFile'],"groupFile"=>$textMsg['typeFile']),$textMsg['data']['from_user_id']);
            }
        }   
    }else if(isset($textMsg['data']['to_group_id'])){
     $sql = "SELECT * FROM `group_chats` WHERE id=" . $textMsg['data']['to_group_id'];
     $result = $this->dbh->query($sql);
     $id_group = $result->fetch_assoc()['members_chat'];
     $masGroupUser = explode(",", $id_group);
     $newarr = [];

     for($i = 0; $i < count($masGroupUser);$i++){
        if(array_key_exists($masGroupUser[$i],$this->usersToClients)){
            array_push($newarr,$masGroupUser[$i]);
        }
    }

    foreach($this->clients as $client){
        for($i = 0;$i < count($newarr);$i++){
            if($this->usersToClients[$newarr[$i]] == $client){
             $this->send($client, "sendFileUser", array("id_message" => $textMsg['data']['id_messsgae'], "chat_message" => $textMsg['data']['src'] ,"to_group_id"=>$textMsg['data']['to_group_id'], "posted" => date("Y-m-d H:i:s"),"fromName"=>$rowName['username'],"typeFile"=>$textMsg['data']['typeFile'],"groupFile"=>$textMsg['typeFile']),$textMsg['data']['from_user_id']);

         }
     }
 }
}
}
}