<?php
require_once "config.php";
require_once "model/Line.php";

$Line = new Line($GLOBALS['channel_id'], $GLOBALS['channel_secret'], $GLOBALS['mid']);

if(isset($_POST['type'])){
    $type = $_POST['type'];
}
switch ($type){
    case "send":
        $text = $_POST['message'];
        break;
    default:
        $json_string = file_get_contents('php://input');
        $json_object = json_decode($json_string);
        $content = $json_object->result{0}->content;
        $text = $content->text;
        $text = '"'. $text .'"';
        $from = $content->from;
        $message_id = $content->id;
        $content_type = $content->contentType;

        // ユーザ情報取得
        $userData = $Line->api_get_user_profile_request($from);
        break;
}

// 受信メッセージに応じて返すメッセージを変更
$Line->api_send_line($GLOBALS['send_id'], $text);
?>