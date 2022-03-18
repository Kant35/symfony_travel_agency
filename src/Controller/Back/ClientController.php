<?php

namespace App\Controller\Back;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ClientController extends AbstractController
{
    /**
     * @Route("/back/client", name="app_back_client")
     */
    public function index(ClientRepository $clientRepository): Response
    {
        return $this->render('back/client/index.html.twig', [
            'clients' => $clientRepository->findAll(),
        ]);
    }
    /**
     * @Route("/back/client/{id}", name="app_back_show_client", methods={"GET"})
     */
    public function show(Client $client): Response
    {
        return $this->render('back/client/show.html.twig', [
            'client' => $client,
        ]);
    }

    /**
     * @Route("/back/client/{id}", name="app_client_delete", methods={"POST"})
     */
    public function delete(Request $request, Client $client, ClientRepository $clientRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$client->getId(), $request->request->get('_token'))) {
            $clientRepository->remove($client);
        }

        return $this->redirectToRoute('app_back_client', [], Response::HTTP_SEE_OTHER);
    }
}
