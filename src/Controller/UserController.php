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

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserController extends AbstractController
{
        // Page d'affichage de tous les utilisateurs
        /**
         * @Route("/affichageUser", name="affichageUser")
         */
        public function affichageUser()
        {
            // on recupère tous les utilisateurs
            $user = $this->getDoctrine()->getRepository(User::class)->findAll();
            return $this->render('admin/affichageUser.html.twig', ['users' => $user]);
        }

        // Page d'affichage d'un utilisateur
        /**
         * @Route("/affichageUser/{id}", name="affichageUser")
         */
        public function affichageUserId($id)
        {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
            return $this->render('admin/affichageUserId.html.twig', ['user' => $user]);
        }

        // Page d'édition d'un utilisateur
        /**
         * @Route("/affichageUser/edit/{id}", name="editUser")
         */
        public function editUser(Request $request, $id, UserPasswordEncoderInterface $passwordEncoder)
        {
            $user = new User();
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);

            $form = $this->createFormBuilder($user)
                ->add('username', TextType::class)
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                ))
                ->add('envoyer', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Un utilisateur a bien été modifié, vous êtes redirigé vers l\'accueil !');
                return $this->redirectToRoute('affichageUser');
            }

            return $this->render('admin/editUser.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Page de suppression d'un utilisateur
        /**
         * @Route("/affichageUser/delete/{id}", name="deleteUser")
         */
        public function deleteUser(Request $request, $id)
        {
            $user = $this->getDoctrine()->getRepository(User::class)->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();

            $response = new Response();
            $response->send();
            $this->addFlash('success', 'Un utilisateur a bien été supprimé, vous êtes redirigé vers l\'accueil !');
            return $this->redirectToRoute('admin');
        }
}