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

use App\Entity\Ecole;
use App\Form\EcoleType;

    class EcoleController extends AbstractController
    {
        //Page du formulaire d'ajout d'une école
        /**
         * @Route("/formEcole", name="formEcole")
         */
        public function formEcole(Request $request)
        {
            // instance d'école
            $ecole = new Ecole();

            // formulaire de l'instance ecole
            $form = $this->createForm(EcoleType::class, $ecole);

            // on recupère les données
            $form->handleRequest($request);

            // on vérifie si les données existent et si elles sont valides
            if($form->isSubmitted() &&  $form->isValid())
            {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->persist($ecole);
                $doctrine->flush();
                $this->addFlash('success', 'Une école a bien été crée, vous êtes redirigé vers l\'accueil !');
                return $this->redirectToRoute('admin');
            }

            return $this->render('admin/formEcole.html.twig', [
                'form' => $form->createView(),
            ]);  
        }

        // Page d'affichage de toutes les écoles
        /**
         * @Route("/affichageEcole", name="affichageEcole")
         */
        public function affichageEcole()
        {
            $ecole = $this->getDoctrine()->getRepository(Ecole::class)->findAll();
            return $this->render('admin/affichageEcole.html.twig', ['ecoles' => $ecole]);
        }

        // Page d'affichage d'une école
        /**
         * @Route("/affichageEcole/{id}", name="affichageEcole")
         */
        public function affichageEcoleId($id)
        {
            $ecole = $this->getDoctrine()->getRepository(Ecole::class)->find($id);
            return $this->render('admin/affichageEcoleId.html.twig', ['ecole' => $ecole]);
        }

        // Page d'édition d'une école
        /**
         * @Route("/affichageEcole/edit/{id}", name="editEcole")
         */
        public function editEcole(Request $request, $id)
        {
            $ecole = new Ecole();
            $ecole = $this->getDoctrine()->getRepository(Ecole::class)->find($id);

            $form = $this->createFormBuilder($ecole)
                ->add('nom')
                ->add('adresse')
                ->add('email', EmailType::class)
                ->add('telephone')
                ->add('ville')
                ->add('type')
                ->add('agregation', CheckboxType::class, [
                    'label' => 'Cochez cette case si l\'école est agrégé.',
                    'required' => false,
                ])
                ->add('envoyer', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $doctrine = $this->getDoctrine()->getManager();
                $doctrine->flush();
                $this->addFlash('success', 'Une école a bien été modifié, vous êtes redirigé vers l\'accueil !');
                return $this->redirectToRoute('affichageEcole');
            }

            return $this->render('admin/editEcole.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        

        // Page de suppression d'une école
        /**
         * @Route("/affichageEcole/delete/{id}", name="deleteEcole")
         */
        public function deleteEcole(Request $request, $id)
        {
            $ecole = $this->getDoctrine()->getRepository(Ecole::class)->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ecole);
            $entityManager->flush();

            $response = new Response();
            $response->send();
            $this->addFlash('success', 'Une école a bien été supprimé, vous êtes redirigé vers l\'accueil !');
            return $this->redirectToRoute('affichageEcole');
        }
    }