<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class ContactController extends AbstractController
{
    #[Route('/contact/send', name: 'contact_send', methods: ['POST'])]
    public function send(
    Request $request,
    MailerInterface $mailer,
    CsrfTokenManagerInterface $csrf): Response {

    // CSRF
    $token = $request->request->get('_token');
    if (!$csrf->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('contact', $token))) {
        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['ok'=>false, 'message'=>'Jeton CSRF invalide'], 400);
        }
        $this->addFlash('danger','Erreur de sécurité, veuillez réessayer.');
        return $this->redirect($request->headers->get('referer') ?? '/');
    }

    $firstName = $request->request->get('first_name');
    $lastName  = $request->request->get('last_name');
    $phone     = $request->request->get('phone');
    $email     = $request->request->get('email');
    $goal      = $request->request->get('goal');
    $message   = $request->request->get('message');
    $createdAt = new \DateTime();

    $mail = (new Email())
        ->from('Come Alive Fitness <no-reply@comealivefitness.fr>')
        ->to('contact@comealivefitness.fr')
        ->replyTo($email)
        ->subject('Nouveau message depuis le formulaire de contact')
        ->html($this->renderView('emails/contact.html.twig', compact('firstName','lastName', 'phone', 'email','goal','message', 'createdAt')));

    $mailer->send($mail);

    // AJAX → JSON ; sinon → redirect classique
    if ($request->isXmlHttpRequest()) {
        return new JsonResponse(['ok'=>true, 'message'=>'Votre message a bien été envoyé. Merci !']);
    }

    $this->addFlash('success', 'Votre message a bien été envoyé. Merci !');
    return $this->redirect($request->headers->get('referer') ?? '/');
    }
}