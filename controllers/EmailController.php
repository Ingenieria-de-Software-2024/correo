<?php

namespace Controllers;

use Exception;
use MVC\Router;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class EmailController
{
    public static function email(Router $router)
    {
        $email = new PHPMailer(true);
        $email->SMTPOptions = [
            "ssl" => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
            ]
        ];
        try {
            $email->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $email->isSMTP();                                            //Send using SMTP
            $email->Host = $_ENV['MAIL_HOST'];                     //Set the SMTP server to send through
            $email->SMTPAuth = true;                                   //Enable SMTP authentication
            $email->Username = $_ENV['MAIL_USERNAME'];                     //SMTP username
            $email->Password = $_ENV['MAIL_PASSWORD'];                               //SMTP password
            $email->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $email->Port = $_ENV['MAIL_PORT'];
            $email->CharSet = "UTF-8";
            $email->AddReplyTo($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);                            //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $email->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
            $email->isHTML();
            $html = $router->load('email/saludo');
            $email->Body = $html;
            $email->Subject = "Prueba de correo";
            $email->addAddress('rcastellanos11383@gmail.com', 'Ronaldd Castellanos');

            // Ruta del PDF generado
            $pdfPath = __DIR__ . '/../public/files/reporte.pdf';

            if (file_exists($pdfPath)) {
                $email->addAttachment($pdfPath, 'reporte.pdf');
            } else {
                echo "El archivo PDF no se encuentra en la ruta: " . $pdfPath;
            }

            $email->send();
            echo "Correo enviado con PDF adjunto";

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

}