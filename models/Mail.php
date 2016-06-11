<?php
namespace models;
use MimeMailParser\Parser;

class Mail
{
    public static function parseMailData($data){
        $parser = new Parser();
        $parser->setText(file_get_contents($data));
        $mailData['to'] = $parser->getHeader('to');
        $mailData['subject'] = $parser->getHeader('subject');
        $mailData['body'] = $parser->getMessageBody('text');
        file_put_contents("/tmp/mailParse.txt", $mailData['body']);
        return $mailData;
    }
}