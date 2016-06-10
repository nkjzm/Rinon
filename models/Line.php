<?php
namespace models;
use lib\Config;

class Line
{
    public static function api_send_line($send_id, $text){
        $text = json_encode($text);
        $post = <<< EOM
        {
            {$send_id},
            "toChannel":1383378250,
            "eventType":"138311608800106203",
            "content":{
                "toType":1,
                "contentType":1,
                "text":{$text}
            }
        }
EOM;
        Line::api_post_request("/v1/events", $post);
    }

    public static function api_post_request($path, $post) {
        $url = "https://trialbot-api.line.me{$path}";
        $headers = array(
            "Content-Type: application/json",
            "X-Line-ChannelID: " . Config::read('line.channel_id'),
            "X-Line-ChannelSecret: " . Config::read('line.channel_secret'),
            "X-Line-Trusted-User-With-ACL: " . Config::read('line.mid')
        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        error_log($output);
    }

    public static function api_get_user_profile_request($mid) {
        $url = "https://trialbot-api.line.me/v1/profiles?mids={$mid}";
        $headers = array(
            "X-Line-ChannelID: " . Config::read('line.channel_id'),
            "X-Line-ChannelSecret: " . Config::read('line.channel_secret'),
            "X-Line-Trusted-User-With-ACL: " . Config::read('line.mid')
        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        return $output;
    }

    public static function api_get_message_content_request($message_id) {
        $url = "https://trialbot-api.line.me/v1/bot/message/{$message_id}/content";
        $headers = array(
            "X-Line-ChannelID: " . Config::read('line.channel_id'),
            "X-Line-ChannelSecret: " . Config::read('line.channel_secret'),
            "X-Line-Trusted-User-With-ACL: " . Config::read('line.mid')
        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($curl);
        file_put_contents("/tmp/{$message_id}", $output);
    }
}