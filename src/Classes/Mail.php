<?php

namespace App\Classes;


use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    /**
     * @var string
     */
    private $api_key = 'db40adc9a5c223a456ee5ca242547eee';

    /**
     * @@var string
     */
    private $api_key_secret = '347f78eaf8d144d682e28f3a6077cbb4';

    /**
     * 
     */
    public function send($to_email, $to_name, $subject, $content) //add($id) provient du controller cart
    {
        $mj = new Client($this->api_key, $this->api_key_secret, true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "am.sismondi@gmail.com",
                        'Name' => "Cocorico"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4548705,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,

                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}
