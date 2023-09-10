<?php
// Import PHPMailer classes into the global namespace 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include library files 
require VENDOR . 'PHPMailer/Exception.php';
require VENDOR . 'PHPMailer/PHPMailer.php';
require VENDOR . 'PHPMailer/SMTP.php';


class emailSender
{


    function sendEmail($recipientEmail, $confirmationLink)
    {

        // Create an instance; Pass `true` to enable exceptions 
        $mail = new PHPMailer;


        // Server settings 
        $mail->isSMTP();                            // Set mailer to use SMTP 
        $mail->Host = 'smtp.gmail.com';           // Specify main and backup SMTP servers 
        $mail->SMTPAuth = true;                     // Enable SMTP authentication    
        $mail->Username = 'ivansuperff@gmail.com';       // SMTP username                       
        $mail->Password = 'bvecgvwdnxhygsip';         // SMTP password      vm,$}6??f7BxV%J    
        $mail->SMTPSecure = 'ssl';                  // Enable TLS encryption, `ssl` also accepted 
        $mail->Port = 465;                          // TCP port to connect to 

        // Sender info 
        $mail->setFrom('ivansuperff@gmail.com', 'usMessanger');

        // Add a recipient 
        $mail->addAddress($recipientEmail);

        // Set email format to HTML 
        $mail->isHTML(true);
        $subject = 'Подтверждение регистрации';
        // Mail subject 
        $mail->Subject = "=?utf-8?B?". base64_encode($subject)."?=";

        // Mail body content 
        $bodyContent = '<h3>Подтверждение регистрации</h3>';
        $bodyContent .= "<p>Для завершения регистрации, пожалуйста, перейдите <a href='" .$confirmationLink."'>по ссылке.</a></p>";
        $mail->Body    = $bodyContent;
        $mail->CharSet = 'UTF-8';
        // Send email 
        if (!$mail->send()) {
            $_SESSION['emailConfirm'] = 'Сообщение не было отправлено. Mailer Error: ' . $mail->ErrorInfo;
        } else {
            $_SESSION['emailConfirm'] = 'Перейдите по ссылке в письме';
        }
    }
}
