<?php

namespace App\Controller\UserManagement;

use App\Entity\User;
use App\Form\Model\UpdatePassword;
use App\Form\UpdateEmailFormType;
use App\Form\UpdatePasswordFormType;
use App\Form\UpdateUserIdentifierFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController is used to manage the user profile
 * @package App\Controller\UserManagement
 * @author Louis Landois
 */
class ProfileController extends AbstractController
{

    private EntityManagerInterface $entityManager;
    private null|\Symfony\Component\Security\Core\User\UserInterface $user;
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @param $newIdentifier string new user identifier
     * @return void
     * Function used to update the user identifier
     */
    private function updateUserIdentifier(string $newIdentifier): void
    {
        $this->user->setUsername($newIdentifier);
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }

    /**
     * @param $newEmail string new user email
     * @return void
     * Function used to update the user email
     */
    private function updateUserEmail(string $newEmail): void
    {
        $this->user->setEmail($newEmail);
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }

    /**
     * @param $newPassword string new user password
     * @return void
     * Function used to update the user password
     */
    private function updateUserPassword(string $newPassword): void
    {
        $this->user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $this->user,
                $newPassword
            )
        );
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
    }

    /**
     * @param Request $request request
     * @param array $forms array of forms from the profile page
     * @return Response|null
     * Function used to handle the profile page forms
     */
    private function handleRequest(Request $request,array $forms): ?Response
    {
        $user = $this->getUser();
        $updatePasswordForm = $forms['updatePasswordForm'];
        $updateEmailForm = $forms['updateEmailForm'];
        $updateUserIdentifierForm = $forms['updateUserIdentifierForm'];
        $updatePasswordForm->handleRequest($request);
        $updateEmailForm->handleRequest($request);
        $updateUserIdentifierForm->handleRequest($request);

        if ($updatePasswordForm->isSubmitted() && $updatePasswordForm->isValid()) {
            $this->updateUserPassword($updatePasswordForm->getData()->getNewPassword());
            return $this->redirectToRoute('user_profile');
        }
        if ($updateEmailForm->isSubmitted() && $updateEmailForm->isValid()) {
            $this->updateUserEmail($updateEmailForm->getData()->getNewEmail());
            return $this->redirectToRoute('user_profile');
        }
        if ($updateUserIdentifierForm->isSubmitted() && $updateUserIdentifierForm->isValid()) {
            $this->updateUserIdentifier( $updateUserIdentifierForm->getData()->getUsername());
            return $this->redirectToRoute('user_profile');
        }
        return null;
    }

    /**
     * @param Request $request handle the update of the user profile from the different forms in the profile page
     * @return Response the profile page
     */
    #[Route('/profile', name: 'user_profile')]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $this->entityManager = $entityManager;
        $this->user = $this->getUser();
        $this->userPasswordHasher = $userPasswordHasher;

        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $updatePasswordForm = $this->createForm(UpdatePasswordFormType::class, new UpdatePassword());
        $updateEmailForm = $this->createForm(UpdateEmailFormType::class, $this->user);
        $updateUserIdentifierForm = $this->createForm(UpdateUserIdentifierFormType::class, $this->user);
        $response = new Response();

        $response = $this->handleRequest($request,[
            'updatePasswordForm' => $updatePasswordForm,
            'updateEmailForm' => $updateEmailForm,
            'updateUserIdentifierForm' => $updateUserIdentifierForm
        ]);
        if ($response !== null) {
            return $response;
        }

        return $this->render('UserManagement/profile.html.twig', [
            'controller_name' => 'ProfileController',
            'usernameForm' => $updateUserIdentifierForm->createView(),
            'emailForm' => $updateEmailForm->createView(),
            'passwordForm' => $updatePasswordForm->createView(),
        ]);
    }
}
