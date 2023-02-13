<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\MailNotification;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Psr\Log\LoggerInterface;

class MailNotificationHandler implements MessageHandlerInterface
{
    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var LoggerInterface
     */
    private $queueLogger;

    /**
     * @param MailerInterface $mailer
     * @param LoggerInterface $queueLogger
     */
    public function __construct(
        MailerInterface $mailer,
        LoggerInterface $queueLogger
    ) {
        $this->mailer = $mailer;
        $this->queueLogger = $queueLogger;
    }

    public function __invoke(MailNotification $message)
    {
        $email = (new TemplatedEmail())
            ->from(new Address('stock@reslim.app', 'Reslim'))
            ->to($message->getEmail())
            ->subject($message->getSubject())
            ->htmlTemplate($message->getTemplate())
            ->context($message->getParams());

        $this->queueLogger->info('send mail', [
            'email' => $email,
            'subject' => $message->getSubject(),
            'html' => $email->getHtmlBody()
        ]);

        $this->mailer->send($email);
    }
}