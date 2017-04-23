<?php
require 'PHPMailer-master/PHPMailerAutoload.php';
function sendemail($author,$title,$message,$to,$from)
{
	$mail = new PHPMailer();
	$mail->CharSet = "UTF-8";
	$mail->IsSMTP();
	$mail->SMTPDebug  = 0;
	$mail->SMTPAuth   = true;
	$mail->SMTPSecure = "ssl";
	$mail->Host       = "smtp.gmail.com";
	$mail->Port       = 465;
	$mail->Username   = "larryzona966@gmail.com";
	$mail->Password   = "caribou1999";
	$mail->SetFrom($from);
	$mail->FromName   = $author;
	$mail->Subject    = $title;
	$mail->MsgHTML($message);
	$mail->AddAddress($to, "");
	return(!$mail->Send());
}
?>