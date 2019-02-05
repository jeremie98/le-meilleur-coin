<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Category;
use App\Form\AdType;
use App\Form\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdController extends AbstractController
{
    /**
     * @Route("/ad/depose", name="ad")
     */
    public function create(Request $request)
    {
        $ad = new Ad();
        $adForm = $this->createForm(AdType::class, $ad);
        $adForm->handleRequest($request);

        if($adForm->isSubmitted() && $adForm->isValid()){

            // récupère l'entity manager de Doctrine
            $em = $this->getDoctrine()->getManager();
            // on sauvegarde l'instance
            $em->persist($ad);
            // exécution des requêtes
            $em->flush();

            // message flash affiché sur la page suivante
            $this->addFlash('success', 'Votre annonce a bien été posté !');

            // redirection vers la page de détails de l'annonce
            return $this->redirectToRoute('home');
        }

        return $this->render('ad/ad-depose.html.twig', [
            "adForm" => $adForm->createView(),
        ]);
    }

    /**
     * @Route("/ad/list", name="list")
     */
    public function list(Request $request)
    {
        //filtre par catégorie
        $category = new Category();
        

        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);

        $adRepository = $this->getDoctrine()->getRepository(Ad::class);

        $ads = $adRepository->findLastAds();

        if($categoryForm->isSubmitted()){

            $ads = $adRepository->findBy(
                [
                    'category' => $category
                ],
                ['dateCreated' => 'DESC'],
                30
            );
        }

        return $this->render('ad/ad-list.html.twig', [
            "ads" => $ads,
            "categoryForm" => $categoryForm->createView()
        ]);
    }

    /**
     * @Route("/ad/details/{id}", name="details", requirements={"id": "\d+"},
     *     methods={"GET"}
     *     )
     */
    public function details(int $id){
        // récupération de l'annonce en base
        $adRepository = $this->getDoctrine()->getRepository(Ad::class);
        $ad = $adRepository->find($id);
        if(!$ad){
            throw $this->createNotFoundException("Cette annonce n'existe pas !");
        }

        return $this->render('ad/ad-details.html.twig', [
            'ad' => $ad
        ]);
    }
}
