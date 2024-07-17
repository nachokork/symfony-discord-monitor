<?php

namespace App\ErrorBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use GuzzleHttp\Client;

class ExceptionListener
{
    private $client;
    private $webhookUrl;

    public function __construct(Client $client, string $webhookUrl)
    {
        $this->client = $client;
        $this->webhookUrl = $webhookUrl;
    }

    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        $statusCode = 500;

        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();
        }

        $message = [
            'content' => sprintf(
                "Project: MyGolf\nError: %s\nURL: %s",
                $exception->getMessage(),
                'https://example.com' // Reemplazar URL
            ),
        ];

        if ($statusCode === 500) {
            $message['embeds'] = [
                [
                    'title' => 'Error 500',
                    'description' => 'Se ha producido un error 500 en el servidor.',
                    'timestamp' => (new \DateTime())->format(\DateTime::ATOM),
                    'image' => [
                        'url' => 'https://uxwing.com/wp-content/themes/uxwing/download/checkmark-cross/cancel-icon.png'
                    ]
                ]
            ];
        }

        $this->client->request('POST', $this->webhookUrl, [
            'json' => $message,
        ]);
    }
}