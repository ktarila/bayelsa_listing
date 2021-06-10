<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Repository;

use App\Entity\Advert;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Advert|null find($id, $lockMode = null, $lockVersion = null)
 * @method Advert|null findOneBy(array $criteria, array $orderBy = null)
 * @method Advert[]    findAll()
 * @method Advert[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdvertRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Advert::class);
    }

    public function advancedFilter($parameterArray = [])
    {
        $qb = $this->createQueryBuilder('d');
        $qb->leftJoin('d.category', 'c')
            ->leftJoin('d.photos', 'p')
            ->addSelect('p')
            ->addSelect('c')
        ;

        // category
        if (isset($parameterArray['category'])) {
            $categories = $parameterArray['category'];
            if (\count($categories) > 0) {
                $qb->andWhere('d.category IN (:categories)');
                $qb->setParameter('categories', $categories);
            }
        }

        // text search
        if (isset($parameterArray['search_value']) && '' !== $parameterArray['search_value']) {
            $field = $parameterArray['search_value'];
            $qb->andWhere('Match (d.title, d.description) AGAINST (:searchQuery) > 0');
            // $qb->andWhere('d.title LIKE :searchQuery');
            $qb->setParameter('searchQuery', $field);
        }

        // state
        if (isset($parameterArray['state']) && '' !== $parameterArray['state']) {
            $state = $parameterArray['state'];
            $qb->andWhere('d.state = :state');
            $qb->setParameter('state', $state);
        }

        // order query
        if (isset($parameterArray['order_by']) && '' !== $parameterArray['order_by']) {
            match ($parameterArray['order_by']) {
                'likes' => $qb->orderBy('d.likes', 'DESC'),
                'most-recent' => $qb->orderBy('d.createdAt', 'DESC'),
                default => $qb->orderBy('RAND()'),
            };
        } else {
            $qb->orderBy('RAND()');
        }

        return $qb;
    }

    public function findByTag(Tag $tag)
    {
        return $this->createQueryBuilder('a')
            ->leftJoin('a.tags', 't')
            ->andWhere('t.id = :tag_id')
            ->setParameter('tag_id', $tag->getId())
            ->getQuery()
            ->getResult()
        ;
    }

    public function countComments(Advert $advert): int
    {
        $qb = $this->createQueryBuilder('a')
            ->leftJoin('a.comments', 'c')
            ->select('COUNT(c.id) as count')
            ->andWhere('a.id = :id')
            ->setParameter('id', $advert->getId())
        ;

        return $qb->getQuery()->getSingleResult()['count'];
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
