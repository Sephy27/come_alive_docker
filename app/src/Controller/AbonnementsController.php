<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AbonnementsController extends AbstractController
{
    #[Route('/abonnements', name: 'app_abonnements')]
    public function index(): Response
    {
        return $this->render('abonnements/index.html.twig', [
            'controller_name' => 'AbonnementsController',
        ]);
    }
}
