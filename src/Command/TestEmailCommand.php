<?php

// src/Command/TestEmailCommand.php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class TestEmailCommand extends Command
{
    protected static $defaultName = 'app:test-email';

    private $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this->setDescription('Send a test email');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $email = (new Email())
            ->from('SecurityAdmin@admin.com')
            ->to('recipient@example.com')
            ->subject('Test Email')
            ->text('This is a test email.');

        $this->mailer->send($email);

        $output->writeln('Test email sent successfully.');

        return Command::SUCCESS;
    }
}

