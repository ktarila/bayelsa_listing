<?php

/*
 * This file is part of Bayelsa Listing Symfony Project.
 *
 * (c) Patrick Kenekayoro <Patrick.Kenekayoro@outlook.com>
 * .
 */

namespace App\Repository;

use App\Entity\Advert;
use App\Entity\Upload;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Upload|null find($id, $lockMode = null, $lockVersion = null)
 * @method Upload|null findOneBy(array $criteria, array $orderBy = null)
 * @method Upload[]    findAll()
 * @method Upload[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UploadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Upload::class);
    }

    // /**
    //  * @return Upload[] Returns an array of Upload objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    public function findOneByFields(array $params): ?Upload
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.id = :id')
            ->andWhere('u.uploadToken = :token')
            ->andWhere('u.id is null')
            ->setParameter('id', $params['id'])
            ->setParameter('token', $params['token'])
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function attachAdvert(Advert $advert): void
    {
        $this->createQueryBuilder('q')
            ->update('App:Upload', 'u')
            ->set('u.advert', ':id')
            ->where('u.advert is null AND u.uploadToken = :uploadToken')
            ->setParameters([
                'uploadToken' => $advert->getUploadToken(),
                'id' => $advert->getId(),
            ])
            ->getQuery()
            ->execute()
        ;
    }
}
