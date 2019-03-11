<?php

namespace App\Repository;

use App\Entity\Advert;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Advert::class);
    }
    
    public function getAdverts($page, $nbPerPage)
  {
    $query = $this->createQueryBuilder('a')
      ->leftJoin('a.image', 'i')
      ->addSelect('i')
      ->leftJoin('a.categories', 'c')
      ->addSelect('c')
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
  protected function whereCurrentYear(QueryBuilder $qb)
  {
    $qb
      ->andWhere('a.date BETWEEN :start AND :end')
      ->setParameter('start', new \Datetime(date('Y') . '-01-01')) // Date entre le 1er janvier de cette année
      ->setParameter('end', new \Datetime(date('Y') . '-12-31'))   // Et le 31 décembre de cette année
    ;
  }

    // /**
    //  * @return Advert[] Returns an array of Advert objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Advert
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
