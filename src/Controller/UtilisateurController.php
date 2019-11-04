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
use App\Form\EleveType;
use App\Entity\Eleve;
use App\Entity\Ecole;
use App\Form\EcoleType;

class UtilisateurController extends AbstractController
{
    // Page d'affichage de tous les élèves
    /**
     * @Route("/affichageInfoEleve", name="affichageInfoEleve")
     */
    public function affichageInfoEleve()
    {
        // on recupère tous les élèves
        $eleve = $this->getDoctrine()->getRepository(Eleve::class)->findAll();
        return $this->render('user/affichageInfoEleve.html.twig', ['eleves' => $eleve]);
    }

    // Page d'affichage de toutes les écoles
    /**
     * @Route("/affichageInfoEcole", name="affichageInfoEcole")
     */
    public function affichageInfoEcole()
    {
        $ecole = $this->getDoctrine()->getRepository(Ecole::class)->findAll();
        return $this->render('user/affichageInfoEcole.html.twig', ['ecoles' => $ecole]);
    }

    // Page d'affichage de tous les professeurs
    /**
     * @Route("/affichageInfoProfesseur", name="affichageInfoProfesseur")
     */
    public function affichageInfoProfesseur()
    {
        $professeur = $this->getDoctrine()->getRepository(Professeur::class)->findAll();
        return $this->render('user/affichageInfoProfesseur.html.twig', ['professeurs' => $professeur]);
    }
}