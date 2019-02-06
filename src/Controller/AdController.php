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
            // récupération de l'utilisateur
            $ad->setUser($this->getUser());

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
     * @Route("/ad/my-ads", name="myads")
     */
    public function myAds()
    {
        $adRepository = $this->getDoctrine()->getRepository(Ad::class);

        $myads = $adRepository->findBy([
           'user' => $this->getUser()
        ]);

        return $this->render('ad/my-ads.html.twig', [
            "myads" => $myads,
        ]);
    }

    /**
     * @Route("ad/my-favads", name="myfavads")
     */
    public function myFavoritesAds()
    {
        return $this->render('ad/my-favads.html.twig', [
            "myfavads" => null
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
        $categoryRepository = $this->getDoctrine()->getRepository(Category::class);

        $ads = $adRepository->findLastAds();

        if($categoryForm->isSubmitted()){
            // récupération de l'objet en base de données grâce à son label
            $categoryPick = $categoryRepository->findOneBy([
                'label' => $category->getLabel()
            ]);

            $ads = $adRepository->findBy(
                [
                    'category' => $categoryPick
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

        $adFavForm = $this


        return $this->render('ad/ad-details.html.twig', [
            'ad' => $ad
        ]);
    }
}
