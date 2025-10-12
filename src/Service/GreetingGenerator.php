<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class GreetingGenerator
{
    public function __construct(

        private LoggerInterface $logger,
    ) {}


    public function getRandomGreeting(): string
    {
        $greetings = ['Hey', 'Yo', 'Aloha', 'welcome'];
        $greeting = $greetings[array_rand($greetings)];

        $this->logger->info('Using the greeting: ' . $greeting);

        return $greeting;
    }
}
