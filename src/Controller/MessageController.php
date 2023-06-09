<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Form\MessageType;
use App\Entity\Message;
use App\Entity\Subject;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function index(): Response
    {
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    #[Route('/message/create?subject={subject}', name: 'app_message_create')]
    public function createMessage(Request $request, EntityManagerInterface $entityManager, Subject $subject): Response
    {
        
       
        $message = new Message();
    
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $message->setSubject($subject);
            $message->setUser($this->getUser());
            $message->setCreatedAt(new DateTimeImmutable('now'));

            $entityManager->persist($message);
            $entityManager->flush();
    
            return $this->redirectToRoute('subject_show', ['id' => $subject->getId()]);
        }
    
        return $this->render('message/create.html.twig', [
            'createMessage' => $form->createView(),
        ]);
    }

    
}
