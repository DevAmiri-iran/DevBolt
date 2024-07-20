<?php

namespace App\Mail;

use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Swift_SendmailTransport;
use Swift_SpoolTransport;
use Swift_FileSpool;

class Mailer
{
    protected $mailer;
    protected $logChannel;
    protected $config;

    public function __construct()
    {
        $config = config('mail');
        $this->config = $config;

        $driver = $config['default'];
        $mailerConfig = $config['mailers'][$driver];

        switch ($mailerConfig['transport']) {
            case 'smtp':
                $transport = (new Swift_SmtpTransport($mailerConfig['host'], $mailerConfig['port']))
                    ->setUsername($mailerConfig['username'])
                    ->setPassword($mailerConfig['password'])
                    ->setEncryption($mailerConfig['encryption']);
                break;

            case 'sendmail':
                $transport = new Swift_SendmailTransport($mailerConfig['path']);
                break;

            case 'log':
                $this->logChannel = $mailerConfig['channel'];
                $transport = $this->createLogTransport();
                break;

            default:
                throw new \Exception("Unsupported mail driver: {$mailerConfig['transport']}");
        }

        $this->mailer = new Swift_Mailer($transport);
    }

    public function send(Message $message): bool|string
    {
        $config = $this->config;

        $swiftMessage = (new Swift_Message($message->subject))
            ->setFrom([$config['from']['address'] => $config['from']['name']])
            ->setTo([$message->to['address'] => $message->to['name']])
            ->setBody($message->body, $message->isHtml ? 'text/html' : 'text/plain');

        if ($message->altBody) {
            $swiftMessage->addPart($message->altBody, 'text/plain');
        }

        if ($message->view) {
            $viewContent = $this->renderView($message->view, $message->viewData);
            $swiftMessage->setBody($viewContent, $message->isHtml ? 'text/html' : 'text/plain');
        }

        if (!empty($message->attachments)) {
            foreach ($message->attachments as $filePath) {
                $swiftMessage->attach(\Swift_Attachment::fromPath($filePath));
            }
        }

        try {
            $this->mailer->send($swiftMessage);
            return true;
        } catch (\Exception $e) {
            return "Message could not be sent. Mailer Error: {$e->getMessage()}";
        }
    }

    public function Message(): Message
    {
        return new Message();
    }

    protected function renderView($view, $data = []): bool|string
    {
        ob_start();
        view($view, $data);
        return ob_get_clean();
    }

    protected function createLogTransport(): Swift_SpoolTransport
    {
        $spool = new Swift_FileSpool($this->logChannel);
        return new Swift_SpoolTransport($spool);
    }
}
