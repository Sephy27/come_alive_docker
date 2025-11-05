<?php

namespace App\Controller;

use App\Service\WeekScheduleProvider;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController
{
    #[Route('/planning', name: 'app_planning')]
    public function index(WeekScheduleProvider $provider): Response
    {
        
        return $this->render('planning/index.html.twig', [
            'events' => $provider->get(),
            // bornes affichées (end exclusif ⇒ 09..21 affichés)
            'H_START' =>$provider->startHour(),
            'H_END'   =>$provider->endHour() ,
        ]);
    }
}