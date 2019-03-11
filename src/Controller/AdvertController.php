<?php

namespace App\Controller;

use App\Entity\Advert;
use App\Form\AdvertType;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\AdvertRepository;
use App\Repository\AdvertSkillRepository;
use App\Repository\ApplicationRepository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;

class AdvertController extends Controller
{
    /**
     * @Route("/adverts", name="adverts")
     */
    public function indexAction(AdvertRepository $repo){
        
        $listAdverts = $repo->findAll();
        
        return $this->render('advert/index.html.twig', [
            'controller_name' => 'AdvertController',
            'listAdverts' => $listAdverts
        ]);
    }
    
    /**
     * @Route("/adverts/add", name="advert_add")
     * @Route("/adverts/{id}/edit", name="advert_edit")
     */
    public function addAction(Advert $advert = null, Request $request, ObjectManager $manager){
        
        if(!$advert){
            $advert = new Advert();
        }
        
        $advert->setTitle("Titre de l'annonce")
               ->setAuthor("L'auteur de l'annonce")
               ->setContent("Le contenu de l'annonce");
               
        $form = $this->createForm(AdvertType::class, $advert);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            if(!$advert->getId()){
                $advert->setCreatedAt(new \DateTime());
            }
            
            $manager->persist($advert);
            $manager->flush();
            
            return $this->redirectToRoute('adverts_view', ['id' => $advert->getId()]);
        }
        
        return $this->render('advert/add.html.twig', [
            'formAdvert' => $form->createView(),
            'editMode' => $advert->getId() !== null
        ]);
    }
    
    /**
     * @Route("/adverts/{id}", name="adverts_view")
     */
    public function viewAction($id, Advert $advert, AdvertRepository $advertRepo, ApplicationRepository $appRepo, AdvertSkillRepository $advertSkillRepo, Request $request, ObjectManager $manager){   
        // Pour récupérer une seule annonce, on utilise la méthode find($id)
        $advert = $advertRepo->find($id);
        
        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id n'existe pas, d'où ce if :
    
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
        
        // Récupération de la liste des candidatures de l'annonce
        $listApplications = $appRepo
        ->findBy(array('advert' => $advert))
        ;
    
        // Récupération des AdvertSkill de l'annonce
        $listAdvertSkills = $advertSkillRepo->findBy(array('advert' => $advert))
        ;
    
        return $this->render('advert/view.html.twig', array(
      'advert'           => $advert,
      'listApplications' => $listApplications,
      'listAdvertSkills' => $listAdvertSkills
        ));
    }
    
    /**
     * @Route("/adverts/delete/{id}", name="advert_delete")
     */
    public function deleteAction($id, Advert $advert, ObjectManager $manager){
        
        $advert = $manager->getRepository('AdvertRepository')->find($id);
    
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
    
        // On boucle sur les catégories de l'annonce pour les supprimer
        foreach ($advert->getCategories() as $categories) {
            $advert->removeCategory($categories);
        }
        $manager->flush();
    
        return $this->render('advert/delete.html.twig');
  }
    
    /**
    * @Route("/adverts/{id}/contact", name="advert_contact")
    */
    public function contactAction(Advert $advert, Request $request, ObjectManager $manager){
        
        $contact = new Contact();
        
        $form = $this->createForm(ContactType::class, $contact);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            $contact->setAdvert($advert);
            
            $this->addFlash('success', 'Votre message a bien été envoyé');
            
            $manager->persist($contact);
            $manager->flush();
            
            return $this->redirectToRoute('adverts_view', ['id' => $advert->getId()]);
        }
        
        return $this->render('advert/contact.html.twig', [ 
            'advert' => $advert,
            'formContact' => $form->createView()
        ]);
    }
}