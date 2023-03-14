<?php

namespace App\Command;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class HashCommand extends Command
{
    protected static $defaultName = 'app:hash';
    protected static $defaultDescription = 'Hash command';

    protected function configure(): void
    {
        $this
            ->addArgument('message', InputArgument::REQUIRED, 'Message to be hashed')
            ->addOption('requests', null, InputOption::VALUE_REQUIRED, 'Number of requests', 10);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $message = $input->getArgument('message');
        $requests = $input->getOption('requests');
        $client = HttpClient::createForBaseUri("http://localhost:8000");
        $timestamp = \date('Y-m-d H:i:s');

        $data = [];

        for ($i = 1; $i <= $requests; $i++) {
            $response = $client->request('GET', '/hash', [
                'query' => [
                    'message' => $message
                ]
            ])->toArray();

            $data[] = [
                'batch' => $timestamp,
                'i' => $i,
                'input' => $message,
                'key' => $response['key'],
                'hash' => $response['hash'],
                'attempts' => $response['attempts']
            ];

            $message = $response['hash'];

        }

        $io->table(
            [
                'Batch',
                'Número do bloco',
                'String de entrada',
                'Chave encontrada',
                'Hash gerado',
                'Número de tentativas'
            ],
            $data
        );

        return Command::SUCCESS;
    }
}
