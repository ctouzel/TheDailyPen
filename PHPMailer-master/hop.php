<?php

    //path to PHPMailer class
    require_once('class.phpmailer.php');
    // optional, gets called from within class.phpmailer.php if not already loaded
    include("class.smtp.php"); 

    $mail = new PHPMailer();
    $mail->CharSet = "UTF-8";
    // telling the class to use SMTP
    $mail->IsSMTP();
    // enables SMTP debug information (for testing)
    // 1 = errors and messages
    // 2 = messages only
    $mail->SMTPDebug  = 0;
    // enable SMTP authentication
    $mail->SMTPAuth   = true;
    // sets the prefix to the servier
    $mail->SMTPSecure = "ssl";
    // sets GMAIL as the SMTP server
    $mail->Host       = "smtp.gmail.com";
    // set the SMTP port for the GMAIL server
    $mail->Port       = 465;
    // GMAIL username
    $mail->Username   = "larryzona966@gmail.com";
    // GMAIL password
    $mail->Password   = "cactus1976";
    //Set reply-to email this is your own email, not the gmail account 
    //used for sending emails
    $mail->SetFrom('larryzona966@gmail.com');
    $mail->FromName = "Larry Zona";
    // Mail Subject
    $mail->Subject    = "My Subject";

    //Main message
    $mail->MsgHTML("HELLO HELLO PRISE 2!");

    //Your email, here you will receive the messages from this form. 
    //This must be different from the one you use to send emails, 
    //so we will just pass email from functions arguments
    $mail->AddAddress("readlater.okt7d91epek@instapaper.com", "");
    if(!$mail->Send()) 
    {
        echo "Message not sent...";
    } 
    else 
    {
        echo "Message sent!";
    }

?>