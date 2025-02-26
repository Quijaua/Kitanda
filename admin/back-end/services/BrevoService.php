<?php
$parentDir = dirname(__DIR__, 3);
require_once($parentDir . '/vendor/autoload.php');
Dotenv\Dotenv::createImmutable($parentDir)->load();

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class BrevoService
{

    public function __construct()
    {
        // Brevo API key authorization: api-key
        $this->api_key = $_ENV['BREVO_API_KEY'];
        // Brevo API URL
        $this->api_url = $_ENV['BREVO_API_URL'];
        // Sender name and email
        $this->sender_name = $_ENV['SENDER_NAME'];
        $this->sender_email = $_ENV['SENDER_EMAIL'];
    }

    public function sendEmail($data)
    {
        $recipients = $data['recipients'];
        $client = new Client();
        $headers = [
            'Accept'        => 'application/json',
            'Content-Type'  => 'application/json',
            'api-key'       => $this->api_key
        ];

        foreach ( $recipients as $recipient ) {
            $body = [
                'sender' => [
                    'email' => $this->sender_email,
                    'name'  => $this->sender_name
                ],
                'subject'           => $data["title"],
                'htmlContent'       => '<!DOCTYPE html><html><body>' . $data["body"] . '</body></html>',
                'messageVersions'   => [
                    [
                        'to' => [
                            [
                                'email' => $recipient['email'],
                                'name'  => $recipient['nome']
                            ],
                        ],
                        'htmlContent'   => '<!DOCTYPE html><html><body>' . $data["body"] . '</body></html>',
                        'subject'       => $data["title"]
                    ]
                ]
            ];

            $body = json_encode($body, JSON_UNESCAPED_SLASHES);
            $request = new Request('POST', $this->api_url, $headers, $body);
            $res = $client->sendAsync($request)->wait();
        };

        return true;
    }
}