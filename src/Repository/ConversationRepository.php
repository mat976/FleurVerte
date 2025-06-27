<?php

namespace App\Repository;

use App\Entity\Client;
use App\Entity\Conversation;
use App\Entity\Fleuriste;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Conversation
 *
 * @extends ServiceEntityRepository<Conversation>
 *
 * @method Conversation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Conversation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Conversation[]    findAll()
 * @method Conversation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ConversationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Conversation::class);
    }

    /**
     * Enregistre une conversation
     */
    public function save(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime une conversation
     */
    public function remove(Conversation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve toutes les conversations d'un client
     *
     * @return Conversation[] Les conversations du client
     */
    public function findByClient(Client $client): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.client = :client')
            ->setParameter('client', $client)
            ->orderBy('c.dateDerniereActivite', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve toutes les conversations d'un fleuriste
     *
     * @return Conversation[] Les conversations du fleuriste
     */
    public function findByFleuriste(Fleuriste $fleuriste): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.fleuriste = :fleuriste')
            ->setParameter('fleuriste', $fleuriste)
            ->orderBy('c.dateDerniereActivite', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Trouve une conversation entre un client et un fleuriste
     */
    public function findOneByClientAndFleuriste(Client $client, Fleuriste $fleuriste): ?Conversation
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.client = :client')
            ->andWhere('c.fleuriste = :fleuriste')
            ->setParameter('client', $client)
            ->setParameter('fleuriste', $fleuriste)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouve toutes les conversations d'un utilisateur (client ou fleuriste)
     *
     * @return Conversation[] Les conversations de l'utilisateur
     */
    public function findByUser(User $user): array
    {
        $qb = $this->createQueryBuilder('c');
        
        if ($user->isClient()) {
            $qb->andWhere('c.client = :client')
               ->setParameter('client', $user->getClient());
        } elseif ($user->isFleuriste()) {
            $qb->andWhere('c.fleuriste = :fleuriste')
               ->setParameter('fleuriste', $user->getFleuriste());
        } else {
            return [];
        }
        
        return $qb->orderBy('c.dateDerniereActivite', 'DESC')
                 ->getQuery()
                 ->getResult();
    }
}