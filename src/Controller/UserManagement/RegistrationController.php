<?php

namespace App\Controller\UserManagement;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        //create a new user
        $user = new User();
        //create a new form
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        //if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            //set default user image
            //$user->setImg('defaultUserImage.webp');

            //set the user role
            $user->setRoles(['ROLE_USER']);

            //save the user
            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('home');
        }

        return $this->render('UserManagement/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
