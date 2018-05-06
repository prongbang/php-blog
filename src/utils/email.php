<?php

/**
 * Description of EmailUtil
 *
 * @author xE4
 */
class EmailUtil {

    public static function send($msg) {

        $mail_to = $msg['mail_to'];
        $subject = "Your Reset Password.";
        $header = "Content-type: text/html; charset=UTF-8\n"; // or windows-874 //
        $header .= "From: " . $msg['host'] . "\nE-mail: " . $mail_to;

        $message = "Welcome : " . $msg["name"] . "<br>";
        $message .= "Reset Password : <a href='" . $msg['token'] . "' target='_blank'>" . $msg['token'] . "</a><br>";
        $message .= "=================================<br>";
        $message .= $msg['host'] . "<br>";

        $flg_send = mail($mail_to, $subject, $message, $header);

        return $flg_send;
    }

}
