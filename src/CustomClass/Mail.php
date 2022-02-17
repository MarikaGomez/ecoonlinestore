<?php

namespace App\CustomClass;

use Mailjet\Client;
use Mailjet\Resources;

class Mail
{
    private $api_key = '13fb7310c4cee8bfdada57880f1a7223';
    private $api_sk = 'cf8d3774e68864c924fcc3d0dd9e6695';

    public function send($to_email, $to_name, $subject, $content)
    {
        $mj = new Client($this->api_key, $this->api_sk,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "gmz.marika@gmail.com",
                        'Name' => "eco. online store"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name,
                        ]
                    ],
                    'TemplateID' => 3641753,
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