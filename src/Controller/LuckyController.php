<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\MessageGenerator;
use Psr\Log\LoggerInterface;
use App\Service\GreetingGenerator;

class LuckyController extends AbstractController
{
    #[Route('/lucky/number', name: 'lucky_number')]
    public function number(LoggerInterface $logger): Response
    {
        $number = random_int(0, 100);
        $logger->info("Lucky number: $number");

        return $this->render('lucky/number.html.twig', [
            'number' => $number,
        ]);
    }

    #[Route('/lucky/messages', name: 'happy_messages')]
    public function getMessage(MessageGenerator $messageGenerator): Response
    {
        $message = $messageGenerator->getHappyMessage();
        $this->addFlash('success', $message);

        return $this->render('lucky/messages.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/lucky/number/{max}', name: 'lucky_number_max')]
    public function numberMax(int $max): Response
    {
        $number = random_int(0, $max);

        return new Response(
            '<html><body>Lucky number: ' . $number . '</body></html>'
        );
    }

    #[Route('/lucky/hello/{name}', methods: ['GET'], name: 'hello')]
    public function hello(string $name, LoggerInterface $logger, GreetingGenerator $generator): Response
    {
        $greeting = $generator->getRandomGreeting();

        $logger->info("Saying $greeting to $name!");

        return new Response('<html><body>' . $greeting . ' ' . $name . '</body></html>');
    }

    #[Route('/lucky/hello-pipe/{name}', methods: ['GET'], name: 'hello_pipe')]
    public function helloPipe(string $name): Response
    {

        return $this->render('lucky/greeting.html.twig', [
            'name' => $name,
        ]);
    }
}
