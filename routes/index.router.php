<?php
use models\Line;
use lib\Config;

$app->get('/callback', function(){
    $json_string = file_get_contents('php://input');
    $json_object = json_decode($json_string);
    $content = $json_object->result{0}->content;
    $text = $content->text;
    $text = '"'. $text .'"';
    $from = $content->from;
    $message_id = $content->id;
    $content_type = $content->contentType;

    // ユーザ情報取得
    $userData = Line::api_get_user_profile_request($from);

    Line::api_send_line(Config::read('line.send_id'), $text);
});

$app->post('/callback', function($text){
    Line::api_send_line(Config::read('line.send_id'), $text);
});