<?php
/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/phpmailer/src/Exception.php';
require_once __DIR__ . '/vendor/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/vendor/phpmailer/src/SMTP.php';*/

function customBCMailSender($from,$to,$subject,$message,$headers){

	/*$smtpMAIL = new PHPMailer(true);
	try {
		 //Server settings
		$fromm = "beebayads@gmail.com";
		$smtpMAIL->isSMTP();
		$smtpMAIL->Host = 'mail.cheaperdata.com.ng';
		$smtpMAIL->SMTPAuth = true;
		$smtpMAIL->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
		$smtpMAIL->Port = 25;
		
		$smtpMAIL->Username = 'notification@cheaperdata.com.ng';  //YOUR gmail email
		$smtpMAIL->Password = 'TSeKUb1f2t06'; // YOUR gmail password
		
		 //Sender and recipient settings
		$smtpMAIL->setFrom($fromm, $fromm);
		$smtpMAIL->addAddress($to, $to);
		$smtpMAIL->addReplyTo($fromm, $fromm);  //to set the reply to
		
		 //Setting the email content
		$smtpMAIL->IsHTML(true);
		$smtpMAIL->Subject = $subject;
		$smtpMAIL->Body = $message;
		$smtpMAIL->AltBody = $message;
		$smtpMAIL->send();
	} catch (Exception $e) {
		echo $e->getMessage();
		
	}*/
	
	// Inbuilt Mail Functions
	mail($to,$subject,$message,$headers);

}

?>