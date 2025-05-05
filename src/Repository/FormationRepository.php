<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    public function add(Formation $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function remove(Formation $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function findAllOrderBy(string $champ, string $ordre, string $table = ""): array
    {
        $qb = $this->createQueryBuilder('f');

        if ($table === "") {
            $qb->orderBy('f.' . $champ, $ordre);
        } else {
            $qb->join('f.' . $table, 't')
                ->orderBy('t.' . $champ, $ordre);
        }

        return $qb->getQuery()->getResult();
    }

    public function findByContainValue(string $champ, string $valeur, string $table = ""): array
    {
        if ($valeur === "") {
            return $this->createQueryBuilder('f')
                ->orderBy('f.publishedAt', 'DESC')
                ->getQuery()
                ->getResult();
        }

        $qb = $this->createQueryBuilder('f');

        if ($table === "") {
            $qb->where('f.' . $champ . ' LIKE :valeur')
                ->orderBy('f.publishedAt', 'DESC');
        } else {
            $qb->join('f.' . $table, 't')
                ->where('t.' . $champ . ' LIKE :valeur')
                ->orderBy('f.publishedAt', 'DESC');
        }

        return $qb->setParameter('valeur', '%' . $valeur . '%')
            ->getQuery()
            ->getResult();
    }

    public function findAllLasted(int $nb): array
    {
        return $this->createQueryBuilder('f')
            ->orderBy('f.publishedAt', 'DESC')
            ->setMaxResults($nb)
            ->getQuery()
            ->getResult();
    }

    public function findAllForOnePlaylist(int $idPlaylist): array
    {
        return $this->createQueryBuilder('f')
            ->join('f.playlist', 'p')
            ->where('p.id = :id')
            ->setParameter('id', $idPlaylist)
            ->orderBy('f.publishedAt', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
