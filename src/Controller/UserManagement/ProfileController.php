<?php

namespace App\Controller\UserManagement;

use App\Entity\User;
use App\Form\Model\UpdatePassword;
use App\Form\UpdateEmailFormType;
use App\Form\UpdatePasswordFormType;
use App\Form\UpdateUserIdentifierFormType;
use App\Form\UpdateUserImgFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Vich\UploaderBundle\Form\Type\VichFileType;


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
        $this->addFlash('success', 'Votre identifiant a bien été modifié');
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
        $this->addFlash('success', 'Votre email a bien été modifié');
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
        $this->addFlash('success', 'Mot de passe modifié avec succès');
    }

    private function updateUserImg(File $img): void
    {
        $this->user->setImgFile($img);
        $this->entityManager->persist($this->user);
        $this->entityManager->flush();
        $this->addFlash('success', 'Votre image de profil a bien été modifiée');
    }

    /**
     * @param Request $request request
     * @param array $forms array of forms from the profile page
     * @return Response|null
     * Function used to handle the profile page forms
     */
    private function handleRequest(Request $request, array $forms): ?Response
    {
        $user = $this->getUser();
        $updatePasswordForm = $forms['updatePasswordForm'];
        $updateEmailForm = $forms['updateEmailForm'];
        $updateUserIdentifierForm = $forms['updateUserIdentifierForm'];
        $updateUserImgForm = $forms['updateUserImgForm'];
        $updatePasswordForm->handleRequest($request);
        $updateEmailForm->handleRequest($request);
        $updateUserIdentifierForm->handleRequest($request);
        $updateUserImgForm->handleRequest($request);

        if ($updatePasswordForm->isSubmitted()) {
            if ($updatePasswordForm->isValid()) {
                $this->updateUserPassword($updatePasswordForm->getData()->getNewPassword());
                return $this->redirectToRoute('user_profile');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de votre mot de passe');
            }
        }
        if ($updateEmailForm->isSubmitted()) {
            if ($updateEmailForm->isValid()) {
                $this->updateUserEmail($updateEmailForm->getData()->getEmail());
                return $this->redirectToRoute('user_profile');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de votre adresse email');
            }
        }
        if ($updateUserIdentifierForm->isSubmitted()) {
            if ($updateUserIdentifierForm->isValid()) {
                $this->updateUserIdentifier($updateUserIdentifierForm->getData()->getUsername());
                return $this->redirectToRoute('user_profile');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de votre identifiant');
            }
        }
        if ($updateUserImgForm->isSubmitted()) {
            if ($updateUserImgForm->isValid()) {
                $this->updateUserImg($updateUserImgForm->getData()->getImgFile());
                return $this->redirectToRoute('user_profile');
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de votre image de profil');
            }
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
        $updateUserImgForm = $this->createForm(UpdateUserImgFormType::class, $this->user);
        $response = new Response();

        $response = $this->handleRequest($request, [
            'updatePasswordForm' => $updatePasswordForm,
            'updateEmailForm' => $updateEmailForm,
            'updateUserIdentifierForm' => $updateUserIdentifierForm,
            'updateUserImgForm' => $updateUserImgForm
        ]);
        if ($response !== null) {
            return $response;
        }

        return $this->render('UserManagement/profile.html.twig', [
            'usernameForm' => $updateUserIdentifierForm->createView(),
            'emailForm' => $updateEmailForm->createView(),
            'passwordForm' => $updatePasswordForm->createView(),
            'imgForm' => $updateUserImgForm->createView(),
        ]);
    }
}
