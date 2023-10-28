<?php

namespace App\Controller;


use App\Entity\Users;
use App\Form\RegistrationFormType;
use App\Repository\UsersRepository;
use App\Service\JWTService;
use App\Service\SendMailService;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, JWTService $jwt, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SendMailService $mail): Response
    {
        $user = new Users();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $date = new DateTimeImmutable;

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            $user->setCreatedAt($date);

            $header = ['type' => 'JWT', 'alg' => 'RS256'];

            $payload = ['user_id' => $user->getId()];

            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send('no-reply@mail.com', $user->getEmail(), 'Activation de votre compte sur le site e-commerce', 'register', compact('user', 'token'));

            $this->addFlash('warning', 'un mail de confirmation vous a été envoyé');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UsersRepository $usersRepo, EntityManagerInterface $entitymanager): Response
    {
        if ($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))) 
        {
            $payload = $jwt->getPayload($token);
            $user = $usersRepo->find($payload['user_id']);

            if($user && !$user->getIsVerified())
            {
                $user->setIsVerified(true);
                $entitymanager->flush($user);

                $this->addFlash('success', 'Votre compte est validé, vous pouvez désormais vous connecter');

                return $this->redirectToRoute('app_login');
            }
        }

        $this->addFlash('danger', 'le token est invalide ou a expiré');

        return $this->redirectToRoute('app_login');
    }

    #[Route('/renvoiverif', name:'resend_verif')]
    public function resendVerif(JWTService $jwt, SendMailService $mail, UsersRepository $usersRepository): Response 
    {
        $user = $this->getUser();

        if(!$user){

            $this->addFlash('danger', 'Vous devez etre connecté pour acceder à cette page');

            return $this->redirectToRoute('app_login');
        
        }

        if($user->getIsVerified()){
            
            $this->addFlash('warning', 'Cet utilisateur est deja activé');

            return $this->redirectToRoute('app_login');

        }

        $header = ['typ' => 'JWT', 'alg' => 'HS256'];

        $payload = ['user_id' => $user->getId()];

        $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

        $mail->send('no-reply@mail.com', $user->getEmail(), 'Activation de votre compte sur le site e-commerce', 'register', compact('user', 'token'));

        $this->addFlash('success', 'Email de verification renvoyé');

        return $this->redirectToRoute('app_login');
    }   

}
