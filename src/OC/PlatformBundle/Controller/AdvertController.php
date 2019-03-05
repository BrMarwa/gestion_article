<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\User;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Skill;
use OC\PlatformBundle\Entity\AdvertSkill;
use OC\PlatformBundle\Entity\Application;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\ApplicationType;
use OC\PlatformBundle\Form\AdvertEditType;
use OC\PlatformBundle\Event\PlatformEvents;
use OC\PlatformBundle\Event\MessagePostEvent;
// à utiliser avec le service d'autorisation security.authorization_checker
//use Symfony\Component\Security\Core\Exception\AccessDeniedException;
// à utiliser avec les annotations d'autorisations 
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;



class AdvertController extends Controller
{
  
  public function purgeAction($days, Request $request)
  {
     // On récupère notre service
     $purger = $this->get('oc_platform.purger.advert');

     // On purge les annonces
     $purger->purge($days);
 
     // On ajoute un message flash arbitraire
     $request->getSession()->getFlashBag()->add('info', 'Les annonces plus vieilles que '.$days.' jours ont été purgées.');
 
     // On redirige vers la page d'accueil
     return $this->redirectToRoute('oc_platform_home');
     /*
     // On récupère le service
     $purgeserv = $this->container->get('oc_platform.purger.advert');
     $em = $this->getDoctrine()->getManager();
     $purgeserv->purge($days);
   
           return $this->render('OCPlatformBundle:Advert:purge.html.twig', array(
             'listAdverts' => $listAdverts
             ));
      */
  }
  //Fonction pour faire mes test
  public function testAction(Request $request)
  {
    $application = new Application();
    $form   = $this->get('form.factory')->create(ApplicationType::class, $application);


    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      $em->persist($application);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'candidature bien enregistrée.');

      return $this->redirectToRoute('oc_platform_test', array('id' => $application->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:test.html.twig', array(
      'form' => $form->createView(),
    ));
      
    
    
    /*
    //tester validator
    {
      $advert = new Advert;
          
      $advert->setDate(new \Datetime());  // Champ « date » OK
      $advert->setTitle('abc');           // Champ « title » incorrect : moins de 10 caractères
      //$advert->setContent('blabla');    // Champ « content » incorrect : on ne le définit pas
      $advert->setAuthor('A');            // Champ « author » incorrect : moins de 2 caractères
          
      // On récupère le service validator
      $validator = $this->get('validator');
          
      // On déclenche la validation sur notre object
      $listErrors = $validator->validate($advert);
  
      // Si $listErrors n'est pas vide, on affiche les erreurs
      if(count($listErrors) > 0) {
        // $listErrors est un objet, sa méthode __toString permet de lister joliement les erreurs
        return new Response((string) $listErrors);
      } else {
        return new Response("L'annonce est valide !");
      }
    }

    //fin tester validator
    */
    //tester  getAdvertsSkills
     /*
         // tester fonction getAdvertsSkills()
         $listAdverts = $this
         ->getDoctrine()
         ->getManager()
         ->getRepository('OCPlatformBundle:Advert')
         //->getAdvertsSkills()
         ->getAdvertWithApplications()
       ;
     
       return $this->render('OCPlatformBundle:Advert:test.html.twig', array(
         'listAdverts' => $listAdverts
         ));
         //Fin  tester fonction getAdvertsSkills()
      */

    //fin tester getAdvertsSkills
      /*
       $advert = new Advert();
       $advert->setTitle("Recherche développeur !");
       $advert->setAuthor('Alexandre');
       $advert->setContent("Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…");
       $advert->setEmail('marwabriki3@hotmail.fr');
   
       $em = $this->getDoctrine()->getManager();
       $em->persist($advert);
       $em->flush(); // C'est à ce moment qu'est généré le slug
     
       return new Response('Slug généré : '.$advert->getSlug());
    // Affiche « Slug généré : recherche-developpeur »
     */
     /* 
         //tester les callbacks
       $id      = '2';
       $author  = 'test';
       $em      = $this->getDoctrine()->getManager();
       $advert2 = $em->getRepository('OCPlatformBundle:Advert')->find($id);
       $advert2->setAuthor($author);
       $em->flush();
       return $this->render('OCPlatformBundle:Advert:test.html.twig', array(
         'listAdverts' => $advert2
         ));
       // fin de tester les callbacks
     */
     /* 
        // utilisation de la fonction getAdvertWithImage()
        $repository = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('OCPlatformBundle:Advert')
       ;
     
       $listAdverts = $repository->getAdvertWithImage();
      
       // récupérer la liste des applications avec leurs adverts
        return $this->render('OCPlatformBundle:Advert:test.html.twig', array(
        'listAdverts' => $listAdverts
        ));
        */
        //fin utilisation de la fonction getAdvertWithImage()
        /*
         /*  
        // utilisation de la fonction getApplicationsWithAdvert() du repository
        $repository = $this
        ->getDoctrine()
        ->getManager()
        ->getRepository('OCPlatformBundle:Application')
       ;
       $limit = 3;
       $listApplications = $repository->getApplicationsWithAdvert($limit);
     
       // récupérer la liste des applications avec leurs adverts
       return $this->render('OCPlatformBundle:Advert:test.html.twig', array(
         'listApplications' => $listApplications
       ));
       // Fin utilisation de la fonction getApplicationsWithAdvert() du repository
     */
     /*
     
         // tester fonction getAdvertWithApplications()
         $listAdverts = $this
         ->getDoctrine()
         ->getManager()
         ->getRepository('OCPlatformBundle:Advert')
         ->getAdvertWithApplications()
       ;
     
       return $this->render('OCPlatformBundle:Advert:test.html.twig', array(
         'listAdverts' => $listAdverts
         ));
         //Fin  tester fonction getAdvertWithApplications()
     */
      /*
        // utilisation de la fonction getAdvertWithCategories() du repository
        $repository = $this
          ->getDoctrine()
          ->getManager()
          ->getRepository('OCPlatformBundle:Advert')
        ;
      
        $listCategoris = array(
          'name' => 'Développement web',
          'name' => 'Graphisme' 
        );
      
        $listAdverts = $repository->getAdvertWithCategories($listCategoris);
      
        // récupérer la liste des adverts selon les catégories
        return $this->render('OCPlatformBundle:Advert:test.html.twig', array(
          'listAdverts' => $listAdverts
        ));
      
        // Fin utilisation de la fonction getAdvertWithCategories() du repository
      */
  }
  public function indexAction($page)
  {
    if ($page < 1) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // Ici je fixe le nombre d'annonces par page à 3
    // Mais bien sûr il faudrait utiliser un paramètre, et y accéder via $this->container->getParameter('nb_per_page')
    $nbPerPage = 3;

    // On récupère notre objet Paginator
    $listAdverts = $this->getDoctrine()
      ->getManager()
      ->getRepository('OCPlatformBundle:Advert')
      ->getAdverts($page, $nbPerPage)
    ;

    // On calcule le nombre total de pages grâce au count($listAdverts) qui retourne le nombre total d'annonces
    $nbPages = ceil(count($listAdverts) / $nbPerPage);

    // Si la page n'existe pas, on retourne une 404
    if ($page > $nbPages) {
      throw $this->createNotFoundException("La page ".$page." n'existe pas.");
    }

    // On donne toutes les informations nécessaires à la vue
    return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
      'listAdverts' => $listAdverts,
      'nbPages'     => $nbPages,
      'page'        => $page,
    ));
  }

  public function viewAction($id)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On récupère la liste des candidatures de cette annonce
    $listApplications = $em
      ->getRepository('OCPlatformBundle:Application')
      ->findBy(array('advert' => $advert))
    ;

    // On récupère maintenant la liste des AdvertSkill
     $listAdvertSkills = $em
     ->getRepository('OCPlatformBundle:AdvertSkill')
     ->findBy(array('advert' => $advert))
    ;

   return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
     'advert'           => $advert,
     'listApplications' => $listApplications,
     'listAdvertSkills' => $listAdvertSkills
    ));
  }
  
  
  
  public function addAction(Request $request)
  {
    
    
    /*
    // Récupération d'une annonce déjà existante, d'id $id.
    $id = '8';
    $advert = $this->getDoctrine()
    ->getManager()
    ->getRepository('OCPlatformBundle:Advert')
    ->find($id)
    ;
    */
    $advert = new Advert();
    /*
    renvoi le username courant sous forme de string
    //$user = $this->get('security.token_storage')->getToken()->getUser();
    $user = $this->get('security.token_storage')->getToken()->getUsername();
    $advert->setUser($user);
   echo  $advert->getUser(); 
   */
  /*
  $advert = $this->getDoctrine()
    ->getManager()
    ->getRepository('OCUserBundle:User')
    ->findAll();
    */
    // Pour récupérer le service UserManager du bundle
$userManager = $this->get('fos_user.user_manager');

// Pour charger un utilisateur
$user = $userManager->findUserBy(array('username' => 'test'));

   //var_dump($user);
    //die();
    $form   = $this->get('form.factory')->create(AdvertType::class, $advert);


    // On récupère le service validator
    $validator = $this->get('validator');
          
    // On déclenche la validation sur notre object
    $listErrors = $validator->validate($advert);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      //tester l'évènement PostMessage
      // On crée l'évènement avec ses 2 arguments
      $event = new MessagePostEvent($advert->getContent(), $user);

      // On déclenche l'évènement
      $this->get('event_dispatcher')->dispatch(PlatformEvents::POST_MESSAGE, $event);

      // On récupère ce qui a été modifié par le ou les listeners, ici le message
      $advert->setContent($event->getMessage());

      $em = $this->getDoctrine()->getManager();
      $em->persist($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
      'form' => $form->createView(),
    ));
      
    
  }

  public function editAction($id, Request $request)
  {
    //$advert = new Advert();
    // Récupération d'une annonce déjà existante, d'id $id.
    $advert = $this->getDoctrine()
    ->getManager()
    ->getRepository('OCPlatformBundle:Advert')
    ->find($id)
    ;

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    $form   = $this->get('form.factory')->create(AdvertEditType::class, $advert);

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em = $this->getDoctrine()->getManager();
      //$em->persist($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

      return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
    }

    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'form' => $form->createView(),
      'advert' => $advert,
    ));
      
    
    /*
    //tester la fonction updateAuthor
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }
    $author = 'Fabian2';
    $update = $em->getRepository('OCPlatformBundle:Advert')->updateAuthor($author,$id);
    
    $em->flush();
    // Fin tester la fonction updateAuthor
    */
    /*
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // La méthode findAll retourne toutes les catégories de la base de données
    $listCategories = $em->getRepository('OCPlatformBundle:Category')->findAll();

    // On boucle sur les catégories pour les lier à l'annonce
    foreach ($listCategories as $category) {
      $advert->addCategory($category);
    }

    // Pour persister le changement dans la relation, il faut persister l'entité propriétaire
    // Ici, Advert est le propriétaire, donc inutile de la persister car on l'a récupérée depuis Doctrine

    // Étape 2 : On déclenche l'enregistrement
    $em->flush();
   
    return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
      'advert' => $advert
    ));
    */
  }

  public function deleteAction(Request $request, $id)
  {
    $em = $this->getDoctrine()->getManager();

    // On récupère l'annonce $id
    $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

    if (null === $advert) {
      throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
    }

    // On boucle sur les catégories de l'annonce pour les supprimer
    foreach ($advert->getCategories() as $category) {
      $advert->removeCategory($category);
    }

    // On crée un formulaire vide, qui ne contiendra que le champ CSRF
    // Cela permet de protéger la suppression d'annonce contre cette faille
    $form = $this->get('form.factory')->create();

    if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
      $em->remove($advert);
      $em->flush();

      $request->getSession()->getFlashBag()->add('info', "L'annonce a bien été supprimée.");

      return $this->redirectToRoute('oc_platform_home');
    }
    
    return $this->render('OCPlatformBundle:Advert:delete.html.twig', array(
      'advert' => $advert,
      'form'   => $form->createView(),
    ));
    
  }

  public function menuAction($limit)
  {
    $em = $this->getDoctrine()->getManager();

    $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
      array(),                 // Pas de critère
      array('date' => 'desc'), // On trie par date décroissante
      $limit,                  // On sélectionne $limit annonces
      0                        // À partir du premier
    );

    return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
      'listAdverts' => $listAdverts
    ));
  }

}