<?php

namespace App\Controller;

use App\Service\EmailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TestController extends AbstractController{

    public function __construct(
        private readonly EmailService $emailService
    ){}

    #[Route('/test', name:"app_test_sendEmail")]
    public function email() {
        $template = $this->render('email/template.html.twig', [
            'subject'=>"objet du message",
            'body'=>"contenu"
        ]);
        $this->emailService->sendEmail("noxiogene@gmail.com", "example", $template->getContent());
        return new Response("Mail envoyé");
    }
}