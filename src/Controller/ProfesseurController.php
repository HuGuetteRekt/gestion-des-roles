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

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use App\Form\ProfesseurType;
use App\Entity\Professeur;

    class ProfesseurController extends AbstractController
    {
        //Page du formulaire d'ajout d'un professeur
        /**
         * @Route("/formProfesseur", name="formProfesseur")
         */
        public function formProfesseur(Request $request)
        {
            $professeur = new Professeur();
            $form = $this->createForm(ProfesseurType::class, $professeur);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($professeur);
                $doctrine->flush();
                $this->addFlash('success', 'Un professeur a bien été crée, vous êtes redirigé vers l\'accueil !');
                return $this->redirectToRoute('admin');
            }
            return $this->render('admin/formProfesseur.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Page d'affichage de tous les professeurs
        /**
         * @Route("/affichageProfesseur", name="affichageProfesseur")
         */
        public function affichageProfesseur()
        {
            $professeur = $this->getDoctrine()->getRepository(Professeur::class)->findAll();
            return $this->render('admin/affichageProfesseur.html.twig', ['professeurs' => $professeur]);
        }

        // Page d'affichage d'un professeur
        /**
         * @Route("/affichageProfesseur/{id}", name="affichageProfesseur")
         */
        public function affichageProfesseurId($id)
        {
            $professeur = $this->getDoctrine()->getRepository(Professeur::class)->find($id);
            return $this->render('admin/affichageProfesseurId.html.twig', ['professeur' => $professeur]);
        }

        // Page d'édition d'un professeur
        /**
         * @Route("/affichageProfesseur/edit/{id}", name="editProfesseur")
         */
        public function editProfesseur(Request $request, $id)
        {
            $professeur = new Professeur();
            $professeur = $this->getDoctrine()->getRepository(Professeur::class)->find($id);

            $form = $this->createFormBuilder($professeur)
                ->add('nom')
                ->add('prenom')
                ->add('matiere')
                ->add('telephone')
                ->add('envoyer', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->flush();
                $this->addFlash('success', 'Un professeur a bien été modifié, vous êtes redirigé vers l\'accueil !');
                return $this->redirectToRoute('affichageProfesseur');
            }

            return $this->render('admin/editProfesseur.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Page de suppression d'un professeur
        /**
         * @Route("/affichageProfesseur/delete/{id}", name="deleteProfesseur")
         */
        public function deleteProfesseur(Request $request, $id)
        {
            $professeur = $this->getDoctrine()->getRepository(Professeur::class)->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($professeur);
            $entityManager->flush();

            $response = new Response();
            $response->send();
            $this->addFlash('success', 'Un professeur a bien été supprimé, vous êtes redirigé vers l\'accueil !');
            return $this->redirectToRoute('affichageProfesseur');
        }
    }