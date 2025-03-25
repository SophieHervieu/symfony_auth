<?php

namespace App\Service;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService{

   private PHPMailer $mailer;

   public function __construct(
      private readonly string $emailUser,
      private readonly string $emailPassword,
      private readonly string $emailSmtp,
      private readonly int $emailPort
   ) {
      $this->mailer = New PhpMailer(true);
   }

   /**
    * Méthode pour envoyer des emails
    * @param string $receiver paramètre qui va recevoir l'adresse du destinataire du mail
    * @param string $subject paramètre qui va recevoir l'objet du mail
    * @param string $body paramètre qui va recevoir le corps du mail
    * @return void ne retourne rien
    */
   public function sendEmail(string $receiver, string $subject, string $body): void{
      try {
         $this->config();
         $this->mailer->setFrom($this->emailUser, 'Mailer');
         $this->mailer->addAddress($receiver);
         $this->mailer->isHTML(true);
         $this->mailer->Subject = $subject;
         $this->mailer->Body = $body;
         $this->mailer->send();
      } catch(Exception $e) {
         echo "Le mail n'a pas été envoyé : " . $this->mailer->ErrorInfo;
      }
      
   }

   private function config(): void {
      $this->mailer->SMTPDebug = SMTP::DEBUG_SERVER;
      $this->mailer->isSMTP();
      $this->mailer->Host       = $this->emailSmtp;
      $this->mailer->SMTPAuth   = true;
      $this->mailer->Username   = $this->emailUser;
      $this->mailer->Password   = $this->emailPassword;         
      $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $this->mailer->Port       = $this->emailPort;
   }
}
