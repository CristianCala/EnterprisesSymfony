<?php

namespace App\Controller;

use App\Entity\Offer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class OfferController extends AbstractController
{
    /**
     * @Route("/offer", name="offers")
     */
    public function index(EntityManagerInterface $entityManager ): Response
    {
        $offers = $entityManager->getRepository(Offer::class)->findAll();

        return $this->render('offer/index.html.twig', [
            'offers' => $offers,
        ]);
    }

    /**
     * @Route("/offer/{id}/apply", name="offer_apply")
     * @isGranted("ROLE_APPLICANT")
     */
    public function apply(Offer $offer, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();

        $applicant = $entityManager->getRepository(Applicant::class)->findOneBy(
            [
                'user' => $user,
            ]
        );

        $offer->addApplicant($applicant);

        try {
            $entityManager->flush();
            $this->addFlash('Success', 'Solicitud Recibida');
        } catch (\Exception $e) {
            $this->addFlash('Danger', 'La solicitud no pudo almacenarse. Por favor, intente nuevamente');
        };

        return $this->redirectToRoute('offers');
    }
}