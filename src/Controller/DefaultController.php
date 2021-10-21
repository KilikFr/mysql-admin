<?php

namespace App\Controller;

use App\Entity\Cluster;
use App\Entity\Server;
use App\Entity\Slave;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $data = [
            'clustersCount' => $this->getDoctrine()->getRepository(Cluster::class)->count([]),
            'serversCount' => $this->getDoctrine()->getRepository(Server::class)->count([]),
            'channelsCount' => $this->getDoctrine()->getRepository(Slave::class)->count([]),
        ];

        return $this->render('default/index.html.twig', $data);
    }

    /**
     * Top menu.
     */
    public function _header(): Response
    {
        return $this->render('default/_header.html.twig', []);
    }
}
