<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SendTestMailCommand extends Command
{
    protected static $defaultName = 'app:send-test-mail';

    private MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        parent::__construct();
        $this->mailer = $mailer;
    }

    protected function configure(): void
    {
        $this->setName('app:send-test-mail');
        $this->setDescription('Send a test email.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $email = (new Email())
            ->from('evgeniyStudy@yandex.ru')
            ->to('ivanovnew119@gmail.com')
            ->subject('Hello from Symfony Console')
            ->text('This is a test email sent from console command.');

        $this->mailer->send($email);

        $output->writeln('Email sent successfully.');

        return Command::SUCCESS;
    }
}
