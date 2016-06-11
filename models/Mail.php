<?php
namespace models;
require_once 'Mail/mimeDecode.php';

class Mail
{
    public static function parseMailData($data){

        $parser = new Parser();
        $parser->setText(file_get_contents($data));
        $mailData['to'] = $parser->getHeader('to');
        $mailData['subject'] = $parser->getHeader('subject');
        $mailData['body'] = $parser->getMessageBody('text');;
        return $mailData;
    }
}