<?php
declare(strict_types=1);

namespace App\Message;

class MailNotification
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $params;

    /**
     * @param string $email
     * @param string $subject
     * @param string $template
     * @param array $params
     */
    public function __construct(
        string $email,
        string $subject,
        string $template,
        array $params = []
    ) {
        $this->email = $email;
        $this->subject = $subject;
        $this->template = $template;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }
}