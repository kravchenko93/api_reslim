<?php
declare(strict_types=1);

namespace App\Monolog;

use App\Entity\Client;
use App\Entity\Log;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Security\Core\Security;

class DatabaseHandler extends AbstractProcessingHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Security
     */
    private $security;

    public function __construct(
        EntityManagerInterface $entityManager,
        Security $security
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @param array $record
     */
    protected function write(array $record): void
    {
        $log = new Log();
        $log->setMessage($record['message']);
        $log->setContext($record['context']);
        $log->setLevel($record['level']);
        $log->setLevelName($record['level_name']);
        $log->setChannel($record['channel']);
        $log->setExtra($record['extra']);
        $log->setFormatted($record['formatted']);

        $user = $this->security->getUser();

        if ($user instanceof User) {
            $log->setUser($user);
        }

        $this->entityManager->persist($log);
        $this->entityManager->flush();
    }
}