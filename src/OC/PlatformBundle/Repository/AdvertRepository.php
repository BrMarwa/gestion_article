<?php

namespace OC\PlatformBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * AdvertRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AdvertRepository extends \Doctrine\ORM\EntityRepository
{
  public function getUser()
    {
        echo $this->container->get('security.token_storage')->getToken()->getUser();
    //var_dump($userManager);  
    //die();
    }
    
  public function getAdvertsBefore(\Datetime $date)
  {
    return $this->createQueryBuilder('a')
      ->where('a.updatedAt <= :date')                      // Date de modification antérieure à :date
      ->orWhere('a.updatedAt IS NULL AND a.date <= :date') // Si la date de modification est vide, on vérifie la date de création
      ->andWhere('a.applications IS EMPTY')                // On vérifie que l'annonce ne contient aucune candidature
      ->setParameter('date', $date)
      ->getQuery()
      ->getResult()
      ;
  }
  public function getAdverts($page, $nbPerPage)
  {
    $query = $this->createQueryBuilder('a')
      ->leftJoin('a.image', 'i')
      ->addSelect('i')
      ->leftJoin('a.categories', 'c')
      ->addSelect('c')
      ->leftJoin('a.advertskills', 'adsk')
      ->addSelect('adsk') 
      ->orderBy('a.date', 'DESC')
      ->getQuery()
    ;

    $query
      // On définit l'annonce à partir de laquelle commencer la liste
      ->setFirstResult(($page-1) * $nbPerPage)
      // Ainsi que le nombre d'annonce à afficher sur une page
      ->setMaxResults($nbPerPage)
    ;

    // Enfin, on retourne l'objet Paginator correspondant à la requête construite
    // (n'oubliez pas le use correspondant en début de fichier)
    return new Paginator($query, true);
  }

  //Début mes essais
  // récupérer les advertsSkills
  public function getAdvertsSkills()
  {
    $query = $this->createQueryBuilder('a')
      ->leftJoin('a.advertskills', 'adsk')
      ->addSelect('adsk')
      ->orderBy('a.date', 'DESC')
      ->getQuery()
    ;

    return 
    $query
     ->getResult();

  }
  //Fin récupérer les advertsSkills
  // update d'un champ pour tester les callbacks
  public function updateAuthor($author, $id)
  {
    $qb = $this->createQueryBuilder('a');

    $qb 
      ->update('OCPlatformBundle:Advert', 'a') 
      ->set('a.author', '?1') 
      ->where('a.id = ?2')
      ->setParameter(1, $author)
      ->setParameter(2, $id)
      
    ;

    return 
    $qb
     ->getQuery()
     ->getResult();
  
  }
  
  public function getAdvertWithCategories(array $categoryNames)
  {
    $qb = $this->createQueryBuilder('a');

    // On fait une jointure avec l'entité Category avec pour alias « c »
    $qb
      ->innerJoin('a.categories', 'c')
      ->addSelect('c')
    ;

    // Puis on filtre sur le nom des catégories à l'aide d'un IN
    $qb->where($qb->expr()->in('c.name', $categoryNames));
    // La syntaxe du IN et d'autres expressions se trouve dans la documentation Doctrine

    // Enfin, on retourne le résultat
    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

  public function getAdvertWithApplications()
 {
  //$id=6;
  $qb = $this
    ->createQueryBuilder('a')
    ->leftJoin('a.applications', 'app')
    ->addSelect('app')
   // ->where('a.id = ?1')
    //->setParameter(1, $id)
  ;

  return $qb
    ->getQuery()
    ->getResult()
  ;
 }

 // récupérer les adverts avec une image
 public function getAdvertWithImage()
 {
   $qb = $this->createQueryBuilder('a');

   $qb
   ->innerJoin('a.image', 'i')
   ->addSelect('i')
  ;
 
   // il faut vérifier que image différent de null
   
   //$qb
     //->where('a.image != :image')
     //->setParameter('image', null)
    // ->andWhere($qb->expr()->isNull('a.image'))
  // ;

   // Enfin, on retourne le résultat
   return $qb
     ->getQuery()
     ->getResult()
   ;
 }

  //Fin mes essais
    public function whereCurrentYear(QueryBuilder $qb)
  {
    $qb
      ->andWhere('a.date BETWEEN :start AND :end')
      ->setParameter('start', new \Datetime(date('Y').'-01-01'))  // Date entre le 1er janvier de cette année
      ->setParameter('end',   new \Datetime(date('Y').'-12-31'))  // Et le 31 décembre de cette année
    ;
  }

    public function myFind()
  {
    $qb = $this->createQueryBuilder('a');

    // On peut ajouter ce qu'on veut avant
    $qb
      ->where('a.author = :author')
      ->setParameter('author', 'Marine')
    ;

    // On applique notre condition sur le QueryBuilder
    $this->whereCurrentYear($qb);

    // On peut ajouter ce qu'on veut après
    $qb->orderBy('a.date', 'DESC');

    return $qb
      ->getQuery()
      ->getResult()
    ;
  }

    public function myFindAll()
  {
    // Méthode 1 : en passant par l'EntityManager
    $queryBuilder = $this->_em->createQueryBuilder()
      ->select('a')
      ->from($this->_entityName, 'a')
    ;
    // Dans un repository, $this->_entityName est le namespace de l'entité gérée
    // Ici, il vaut donc OC\PlatformBundle\Entity\Advert

    // Méthode 2 : en passant par le raccourci (je recommande)
    $queryBuilder = $this->createQueryBuilder('a');

    // On n'ajoute pas de critère ou tri particulier, la construction
    // de notre requête est finie

    // On récupère la Query à partir du QueryBuilder
    $query = $queryBuilder->getQuery();

    // On récupère les résultats à partir de la Query
    $results = $query->getResult();

    // On retourne ces résultats
    return $results;
    /*Méthode raccourcis :
         return $this
             ->createQueryBuilder('a')
             ->getQuery()
             ->getResult()
             ;
    */
  }
}
