<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of EmailService
 *
 * @author David
 */
class EmailService extends CComponent {

    private $config;

    private function loadService() {
        $this->config = json_decode(json_encode(simplexml_load_file(Yii::app()->params['xmlconfig'])));
        $mail = Yii::app()->email;
        /**
         * @var string
         *
         * Mode of Email Delivery
         * can be of the 3 options
         * 1. 'local'
         * 2. 'gmail'
         * 3. 'custom'
         *
         * @see YiiMailer::$settings['delivery']
         */
        Yii::app()->email->delivery = $this->config->emailService->delivery;
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = $this->config->emailService->smtp->host;  // Specify main and backup server
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = $this->config->emailService->smtp->user;                            // SMTP username
        $mail->Password = $this->config->emailService->smtp->password;                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable encryption, 'ssl' also accepted

        return $mail;
    }

    public function emailUserResetPassword($user) {
        if (empty($user->email))
            return false;
        
        $mail = $this->loadService();

        $mail->From = $this->config->emailService->from->email;
        $mail->FromName = $this->config->emailService->from->name;
        $mail->addAddress($user->email);
        $mail->isHTML(true);                                  // Set email format to HTML

        $mail->Subject = 'Password Reset Request on ' . date('d-M-Y');
        $mail->Body = 'Requested Password Reset: <a href=#>Reset your password here</a>';
        $mail->AltBody = 'Password Reset Request';

        if (!$mail->send()) {
            Yii::log('Email Error:' . $mail->ErrorInfo);

            Yii::log("Message could not be sent");
            Yii::log("Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
        Yii::log('Password Reset Email sent to '.$user->email);
        return true;
    }

}
