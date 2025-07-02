<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Article>
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * GetPublishArticles function
     *
     * @return array
     */
    public function findPublished(): array
    {
        return $this->createQueryBuilder('a')
        ->where('a.state LIKE :state')
        ->setParameter('state','%Publie%')
        ->orderBy('a.createdAt','DESC')
        ->getQuery()
        ->getResult();
    }

    public function mainPublished(): array
    {
        return $this->createQueryBuilder('a')
        ->where('a.state LIKE :state')
        ->setParameter('state','%Publie%')
        ->orderBy('a.createdAt','DESC') 
        ->distinct()
        ->setMaxResults (9)
        ->getQuery()
        ->getResult();
    }

 
}
