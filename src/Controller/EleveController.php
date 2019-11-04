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

use App\Form\EleveType;
use App\Entity\Eleve;

    class EleveController extends AbstractController
    {
        //Page du formulaire d'ajout d'un élève
        /**
         * @Route("/formEleve", name="formEleve")
         */
        public function formEleve(Request $request)
        {
            // Initialisation d'un élève
            $eleve = new Eleve();
            // Création d'un formulaire eleve à partir d'éleveType
            $form = $this->createForm(EleveType::class, $eleve);
            $form->handleRequest($request);

            // Vérification de la validité du formulaire
            if($form->isSubmitted() && $form->isValid())
            {
                $file = $eleve->getPhoto();
                $filename = md5(uniqid()).'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('images_directory'),
                        $filename
                    );
                }
                catch (FileException $e) {

                }
                $doctrine = $this->getDoctrine()->getManager();
                $eleve->setPhoto($filename);
                $doctrine->persist($eleve);
                $doctrine->flush();
                $this->addFlash('success', 'Un élève a bien été crée, vous êtes redirigé vers l\'accueil !');
                return $this->redirectToRoute('admin');
            }

            return $this->render('admin/formEleve.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Page d'affichage de tous les élèves
        /**
         * @Route("/affichageEleve", name="affichageEleve")
         */
        public function affichageEleve()
        {
            // on recupère tous les élèves
            $eleve = $this->getDoctrine()->getRepository(Eleve::class)->findAll();
            return $this->render('admin/affichageEleve.html.twig', ['eleves' => $eleve]);
        }

        // Page d'affichage d'un élève
        /**
         * @Route("/affichageEleve/{id}", name="affichageEleve")
         */
        public function affichageEleveId($id)
        {
            // on recupère un élève grâce à son id dans la liste d'élève
            $eleve = $this->getDoctrine()->getRepository(Eleve::class)->find($id);
            return $this->render('admin/affichageEleveId.html.twig', ['eleve' => $eleve]);
        }

        // Page d'édition d'un élève
        /**
         * @Route("/affichageEleve/edit/{id}", name="editEleve")
         */
        public function editEleve(Request $request, $id)
        {
            
            $eleve = new Eleve();
            $eleve = $this->getDoctrine()->getRepository(Eleve::class)->find($id);

            $form = $this->createFormBuilder($eleve)
            ->add('nom')
            ->add('prenom')
            ->add('photo', FileType::class, array('data_class' => null))
            ->add('envoyer', SubmitType::class)
                ->getForm();

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid())
            {
                $file = $eleve->getPhoto();
                $filename = md5(uniqid()).'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('images_directory'),
                        $filename
                    );
                }
                catch (FileException $e) {

                }
                $doctrine = $this->getDoctrine()->getManager();
                $eleve->setPhoto($filename);
                $doctrine->flush();
                $this->addFlash('success', 'Un élève a bien été modifié, vous êtes redirigé vers l\'accueil !');
                return $this->redirectToRoute('affichageEleve');
            }

            return $this->render('admin/editEleve.html.twig', [
                'form' => $form->createView(),
            ]);
        }

        // Page de suppression d'un élève
        /**
         * @Route("/affichageEleve/delete/{id}", name="deleteEleve")
         */
        public function deleteEleve(Request $request, $id)
        {
            // on recupère l'id de l'élève concerné
            $eleve = $this->getDoctrine()->getRepository(Eleve::class)->find($id);
            $entityManager = $this->getDoctrine()->getManager();
            // remove = suppression
            $entityManager->remove($eleve);
            // on execute les requetes dans la base de données
            $entityManager->flush();

            $response = new Response();
            $response->send();
            $this->addFlash('success', 'Un élève a bien été supprimé, vous êtes redirigé vers l\'accueil !');
            return $this->redirectToRoute('affichageEleve');
        }
    }