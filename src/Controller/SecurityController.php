<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Form\ResetPassType;
use App\Form\NewPassType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ResetPassMailer;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgottenPassword', name: 'app_forgotten_password')]
    public function forgotPass(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        ResetPassMailer $mailer,
    ): Response {
        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneByEmail($email);
            if (!$user) {
                $this->addFlash('danger', 'Adresse e-mail inconnue');
                
                return $this->redirectToRoute('app_login');
            }
            $token = md5(random_bytes(15));
            $user->setResetToken($token);
            $entityManager->persist($user);
            $entityManager->flush();
            $mailer->send($token, $user);
            $this->addFlash('success', 'L\'e-mail de réinitialisation a bien été envoyé');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/forgotten_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/resetPassword/{token}', name: 'app_reset_password')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
    ): Response {
        $user = $userRepository->findOneByResetToken($token);
        $form = $this->createForm(NewPassType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if (!$user) {
                $this->addFlash('danger', 'Token invalide');
                
                return $this->redirectToRoute('app_login');
            }
            $user->setResetToken(null);
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Mot de passe mis à jour');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
