<?php

namespace App\Controller;

use App\Entity\TrialRequest;
use App\Service\WeekScheduleProvider;
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
        WeekScheduleProvider $provider,
        MailerInterface $mailer
    ): Response {

        $trial = new TrialRequest();
        $form = $this->createForm(TrialRequestType::class, $trial, [
            'attr'=>['class'=>'row g-3 g-lg-4']
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Source de vérité : l’entité mappée
            $trialDate = $trial->getTrialDate();
            $trialDateStr = $trialDate ? $trialDate->format('d/m/Y') : null;

            // trialSlot est une chaîne JSON en BDD -> on la décode pour les templates
            $slotJson = $trial->getTrialSlot();
            $slot = $slotJson ? json_decode($slotJson, true) : null;

            // 1) persist uniquement l'entité (sans les champs unmapped)
            $em->persist($trial);
            $em->flush();

            // 2) email interne (on passe l’entité + la date/slot choisis)
            $internal = (new Email())
                ->from('no-reply@comealivefitness.fr')
                ->to('contact@comealivefitness.fr') // remplace par ton mail
                ->subject('Nouvelle séance d’essai')
                ->html($this->renderView('emails/trial_request_internal.html.twig', [
                    't'            => $trial,
                    'trialDateStr' => $trialDateStr,
                    'slot'         => $slot,
                ]))
                ->text($this->renderView('emails/trial_request_internal.txt.twig', [
                    't'            => $trial,
                    'trialDateStr' => $trialDateStr,
                    'slot'         => $slot,
                ]));
            $mailer->send($internal);

            // 3) email confirmation prospect
            if ($trial->getEmail()) {
                $confirm = (new Email())
                    ->from('no-reply@comealivefitness.fr')
                    ->to($trial->getEmail())
                    ->subject('Confirmation de votre demande — Come Alive Fitness')
                    ->html($this->renderView('emails/trial_request_confirm.html.twig', [
                        't'            => $trial,
                        'trialDateStr' => $trialDateStr,
                        'slot'         => $slot,
                    ]))
                    ->text($this->renderView('emails/trial_request_confirm.txt.twig', [
                        't'            => $trial,
                        'trialDateStr' => $trialDateStr,
                        'slot'         => $slot,
                    ]));
                $mailer->send($confirm);
            }

            // 4) redirect confirmation (PRG) — on transmet aussi la date/slot
            return $this->redirectToRoute('seance_essai_confirmation', [
                'first_name'   => $trial->getFirstName(),
                'last_name'    => $trial->getLastName(),
                'email'        => $trial->getEmail(),
                'phone'        => $trial->getPhone(),
                'goal'         => $trial->getGoal(),
                'trial_date'   => $trialDateStr,
                'slot_title'   => $slot['title'] ?? null,
                'slot_start'   => $slot['start'] ?? null,
                'slot_end'     => $slot['end'] ?? null,
                'slot_day'     => $slot['day'] ?? null,
            ], 303);
        }

        // Données planning pour le JS (remplissage dynamique des créneaux selon la date)
        $events  = $provider->get();
        $H_START = $provider->startHour();
        $H_END   = $provider->endHour();

        return $this->render('seance_essai/index.html.twig', [
            'form'    => $form->createView(),
            'events'  => $events,
            'H_START' => $H_START,
            'H_END'   => $H_END,
        ]);
    }

    #[Route('/seance-essai/confirmation', name: 'seance_essai_confirmation', methods: ['GET'])]
    public function confirmation(Request $request): Response
    {
        return $this->render('seance_essai/confirmation.html.twig', [
            'first_name'   => $request->query->get('first_name'),
            'last_name'    => $request->query->get('last_name'),
            'email'        => $request->query->get('email'),
            'phone'        => $request->query->get('phone'),
            'goal'         => $request->query->get('goal'),
            'trial_date'   => $request->query->get('trial_date'),
            'slot_title'   => $request->query->get('slot_title'),
            'slot_start'   => $request->query->get('slot_start'),
            'slot_end'     => $request->query->get('slot_end'),
            'slot_day'     => $request->query->get('slot_day'),
        ],);
    }
    
}


