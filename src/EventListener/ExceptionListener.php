<?php

namespace App\ErrorBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use GuzzleHttp\Client;

class ExceptionListener
{
    private $client;
    private $webhookUrl;
    private $name;
    private $url;

    public function __construct(Client $client, string $webhookUrl, string $name, string $url)
    {
        $this->client = $client;
        $this->webhookUrl = $webhookUrl;
        $this->name = $name;
        $this->url = $url;
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
                "Project: %s\nError: %s\nURL: %s",
                $this->name,
                $exception->getMessage(),
                $this->url
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