<?php

namespace App\Command;

use Telegram\Bot\Api;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SendSurveyCommand extends Command
{
    protected static $defaultName = 'app:send-survey';

    private $telegram;
    private $botToken;

    public function __construct(string $botToken)
    {
        parent::__construct();
        $this->botToken = $botToken;
        $this->telegram = new Api($this->botToken);
    }

    protected function configure(): void
    {
        $this
            ->setName('app:send-survey')
            ->setDescription('Send survey link to a Telegram chat.')
            ->addArgument('chat_id', InputArgument::REQUIRED, 'Telegram chat ID to send the message to')
            ->addArgument('link', InputArgument::OPTIONAL, 'Survey link to send', 'https://example.com/survey');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $chatId = $input->getArgument('chat_id');
        $link = $input->getArgument('link');

        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => 'Здравствуйте! Вы недавно пользовались услугой. Пожалуйста пройдите опрос по этой ссылке: ' . $link,
            ]);
            $output->writeln('<info>Сообщение успешно отправлено.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Ошибка при отправке сообщения: ' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
