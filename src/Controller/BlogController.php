<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Form\ArticleType;
use App\Entity\Comment;
use App\Form\CommentType;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Common\Persistence\ObjectManager;

class BlogController extends Controller
{
    /**
     * @Route("/blog", name="blog")
     */
    public function index(ArticleRepository $repo){
        
        $articles = $repo->findAll();
        
        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }

    /**
    * @Route("/", name="home")
    */
    public function home(){
        
        return $this->render('blog/home.html.twig', [
                            'title' => 'Présentation du site :',
                            'subtitle' => 'Bonjour à tous et bienvenue sur ce blog !',
                            'content' => '
                            Les articles de ce blog ont pour sujet les métiers de l\'informatique. Ce blog a donc pour but de renseigner ses visiteurs sur ces différents métiers et à donc pour but de répondre aux questions suivantes :

- Quelles sont les études requises pour pouvoir trouver une activité professionnel dans ce domaine ?
- Quelles sont les différents langages de programmations ou notions à connaître pour exercer ?

De plus, afin de faciliter vos recherches, une plateforme d\'annonces et également disponible dans la barre de navigation du site.

Je vous souhaites à tous une bonne lecture des différents articles et n\'hésitez pas à les commenter !
'
        ]);
    }
    
    /**
    * @Route("/blog/new", name="blog_create")
    * @Route("/blog/{id}/edit", name="blog_edit")
    */
    public function form(Article $article = null, Request $request, ObjectManager $manager){
        
        if(!$article){
            $article = new Article();
        }
        
        $article->setTitle("Titre d'exemple")
                ->setContent("Le contenu de l'article");
        
        $form = $this->createForm(ArticleType::class, $article);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            if(!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }
            
            $manager->persist($article);
            $manager->flush();
            
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }
        
        return $this->render('blog/create.html.twig', [
            'formArticle' => $form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }
    
    /**
    * @Route("/blog/{id}", name="blog_show")
    */
    public function show(Article $article, Request $request, ObjectManager $manager){
        
        $comment = new Comment();
        
        $form = $this->createForm(CommentType::class, $comment);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()){
            $comment->setCreatedAt(new \DateTime())
                    ->setArticle($article);
            
            $manager->persist($comment);
            $manager->flush();
            
            return $this->redirectToRoute('blog_show', ['id' => $article->getId()]);
        }
        
        return $this->render('blog/show.html.twig', [
            'article' => $article,
            'commentForm' => $form->createView()
        ]);
    }
}
