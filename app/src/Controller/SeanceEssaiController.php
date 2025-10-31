<?php

namespace App\Controller;

use App\Entity\TrialRequest;
use App\Form\TrialRequestType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class SeanceEssaiController extends AbstractController
{
    #[Route('/seance-essai', name: 'seance_essai', methods: ['GET','POST'])]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $trial = new TrialRequest();
        $form = $this->createForm(TrialRequestType::class, $trial, [
            'attr'=>['class'=>'row g-3 g-lg-4'] // conserve ta grille Bootstrap
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // 1) persist
            $em->persist($trial);
            $em->flush();

            // 2) email interne
            $internal = (new Email())
                ->from('no-reply@comealivefitness.fr')
                ->to('contact@comealivefitness.fr') // TODO: remplace par ton mail
                ->subject('Nouvelle séance d’essai')
                ->html($this->renderView('emails/trial_request_internal.html.twig', ['t'=>$trial]))
                ->text($this->renderView('emails/trial_request_internal.txt.twig', ['t'=>$trial]));
            $mailer->send($internal);

            // 3) email confirmation prospect
            $confirm = (new Email())
                ->from('no-reply@comealivefitness.fr')
                ->to($trial->getEmail())
                ->subject('Confirmation de votre demande — Come Alive Fitness')
                ->html($this->renderView('emails/trial_request_confirm.html.twig', ['t'=>$trial]))
                ->text($this->renderView('emails/trial_request_confirm.txt.twig', ['t'=>$trial]));
            $mailer->send($confirm);

            // 4) redirect confirmation
            return $this->redirectToRoute('seance_essai_confirmation', [
                'first_name'=>$trial->getFirstName(),
                'last_name'=>$trial->getLastName(),
                'email'=>$trial->getEmail(),
                'phone'=>$trial->getPhone(),
                'goal'=>$trial->getGoal(),
                'class'=>$trial->getClass(),
            ], 303);
        }

        return $this->render('seance_essai/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/seance-essai/confirmation', name: 'seance_essai_confirmation', methods: ['GET'])]
    public function confirmation(Request $request): Response
    {
        return $this->render('seance_essai/confirmation.html.twig', [
            'first_name' => $request->query->get('first_name'),
            'last_name'  => $request->query->get('last_name'),
            'email'      => $request->query->get('email'),
            'phone'      => $request->query->get('phone'),
            'goal'       => $request->query->get('goal'),
            'class'      => $request->query->get('class'),
        ]);
    }
}

