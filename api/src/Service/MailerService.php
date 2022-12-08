<?php

namespace App\Service;

class MailerService
{

    public function __construct(private $secret) {

    }

    public function send($to, $name, $subject, $content) {

        $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', $this->secret);
        $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
            new \GuzzleHttp\Client(),
            $config
        );
        $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail(); // \SendinBlue\Client\Model\SendSmtpEmail | Values to send a transactional email

        $sendSmtpEmail['sender'] = ['email' => 'contact@queel.io', 'name' => 'queel.io'];
        $sendSmtpEmail['to'] = array(array('email'=> $to, 'name' => $name));
        //$sendSmtpEmail['headers'] = array('X-Mailin-custom'=>'custom_header_1:custom_value_1|custom_header_2:custom_value_2');
        $sendSmtpEmail['subject'] = $subject;
        $sendSmtpEmail['htmlContent'] = $content;
        try {
            $result = $apiInstance->sendTransacEmail($sendSmtpEmail);
            return [true, null];
        } catch (\Exception $e) {
            return [false, $e->getMessage()];
        }
    }
}