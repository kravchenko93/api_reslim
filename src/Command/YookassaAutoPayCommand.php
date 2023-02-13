<?php
declare(strict_types=1);

namespace App\Command;

use App\Service\PaymentService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;

class YookassaAutoPayCommand extends Command
{
    /**
     * @var PaymentService
     */
    private $paymentService;

    /**
     * @var LoggerInterface
     */
    private $queueLogger;

    protected static $defaultName = 'app:yookassa:auto_pay';

    /**
     * @param PaymentService $paymentService
     * @param LoggerInterface $queueLogger
     */
    public function __construct(
        PaymentService $paymentService,
        LoggerInterface $queueLogger
    ) {
        $this->paymentService = $paymentService;
        $this->queueLogger = $queueLogger;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->queueLogger->info('foo', [
            'bar' => 'baz',
        ]);

        $this->paymentService->autoPay(1);

        return 0;
    }
}