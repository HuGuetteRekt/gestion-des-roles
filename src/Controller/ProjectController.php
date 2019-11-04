<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Form\UserType;
use App\Entity\User;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProjectController extends AbstractController
{
    //Page d'accueil du site sans être connecté
   /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('project/index.html.twig');
    }
    
    //page d'accueil du site en étant connecté
    /**
     * @Route("/admin", name="admin")
     */
    public function admin()
    {
        return $this->render('admin/admin.html.twig');
    }

    //page d'accueil du site en étant connecté en USER
    /**
     * @Route("/user", name="user")
     */
    public function user()
    {
        return $this->render('user/user.html.twig');
    }

    // Après s'être enregistré, l'utilisateur est dirigé vers cette page afin de voir ses informations
    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(User $user)
    {
        return $this->render('project/show.html.twig', [
            'user' => $user,
        ]);
    }

    // Page d'enregistrement d'un utilisateur
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('show', [
                'id' => $user->getId(),
            ]);
        }
        return $this->render('project/register.html.twig',
            array('form' => $form->createView())
        );
    }

    // Page de connexion d'un utilisateur
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils, AuthorizationCheckerInterface $authChecker)
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();


        return $this->render('project/login.html.twig', array(
            'last_username' => $lastUsername,
            'error'         => $error,
        ));
    }

    // Page de déconnexion d'un utilisateur
    /**
     * @Route("/logout", name="app_logout", methods={"GET"})
     */
    public function logout()
    {
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }

    public function affichageConnecte(AuthorizationCheckerInterface $authChecker)
    {
        if (false === $authChecker->isGranted('ROLE_ADMIN')) {
            throw new AccessDeniedException('Unable to access this page!');
        }  
    }
}
