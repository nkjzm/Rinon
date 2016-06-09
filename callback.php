<?php
require_once "config.php";

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
        api_get_user_profile_request($from);
        break;
}

// 受信メッセージに応じて返すメッセージを変更
api_send_line($text);

function api_send_line($text){
    $post = <<< EOM
    {
        {$GLOBALS['send_id']},
        "toChannel":1383378250,
        "eventType":"138311608800106203",
        "content":{
            "toType":1,
            "contentType":1,
            "text":{$text}
        }
    }
EOM;
    file_put_contents("/tmp/test2.txt", $post);
    api_post_request("/v1/events", $post);
}

function api_post_request($path, $post) {
    $url = "https://trialbot-api.line.me{$path}";
    $headers = array(
        "Content-Type: application/json",
        "X-Line-ChannelID: {$GLOBALS['channel_id']}",
        "X-Line-ChannelSecret: {$GLOBALS['channel_secret']}",
        "X-Line-Trusted-User-With-ACL: {$GLOBALS['mid']}"
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);
    error_log($output);
}

function api_get_user_profile_request($mid) {
    $url = "https://trialbot-api.line.me/v1/profiles?mids={$mid}";
    $headers = array(
        "X-Line-ChannelID: {$GLOBALS['channel_id']}",
        "X-Line-ChannelSecret: {$GLOBALS['channel_secret']}",
        "X-Line-Trusted-User-With-ACL: {$GLOBALS['mid']}"
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);
    error_log($output);
}

function api_get_message_content_request($message_id) {
    $url = "https://trialbot-api.line.me/v1/bot/message/{$message_id}/content";
    $headers = array(
        "X-Line-ChannelID: {$GLOBALS['channel_id']}",
        "X-Line-ChannelSecret: {$GLOBALS['channel_secret']}",
        "X-Line-Trusted-User-With-ACL: {$GLOBALS['mid']}"
    );

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $output = curl_exec($curl);
    file_put_contents("/tmp/{$message_id}", $output);
}
?>