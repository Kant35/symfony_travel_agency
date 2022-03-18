<?php

namespace App\Controller\Back;

use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BackController extends AbstractController
{
    /**
     * @Route("/back", name="app_back")
     * PAge d'accueil de l'espace d'admin
     */
    public function index(ReservationRepository $reservationRepository): Response
    {
        // On envoie à cette route les réservations qui ont le statut 'En Attente'.
        return $this->render('back/index.html.twig', [
            'nouvelleReservations' => $reservationRepository->findBy(['statut' => 'En Attente']),
        ]);
    }
}
