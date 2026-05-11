<?php

namespace App\Repository;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository pour l'entité Message
 *
 * @extends ServiceEntityRepository<Message>
 *
 * @method Message|null find($id, $lockMode = null, $lockVersion = null)
 * @method Message|null findOneBy(array $criteria, array $orderBy = null)
 * @method Message[]    findAll()
 * @method Message[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    /**
     * Enregistre un message
     */
    public function save(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Supprime un message
     */
    public function remove(Message $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Trouve tous les messages non lus pour un utilisateur
     *
     * @return Message[] Les messages non lus
     */
    public function findUnreadByUser(User $user): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.destinataire = :user')
            ->andWhere('m.estLu = :estLu')
            ->setParameter('user', $user)
            ->setParameter('estLu', false)
            ->orderBy('m.dateEnvoi', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Compte le nombre de messages non lus pour un utilisateur
     */
    public function countUnreadByUser(User $user): int
    {
        return $this->createQueryBuilder('m')
            ->select('COUNT(m.id)')
            ->andWhere('m.destinataire = :user')
            ->andWhere('m.estLu = :estLu')
            ->setParameter('user', $user)
            ->setParameter('estLu', false)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Marque tous les messages non lus d'une conversation comme lus pour un utilisateur
     * Requête batch UPDATE (DQL) au lieu d'itérer en PHP
     */
    public function markAsReadByConversationAndUser(Conversation $conversation, User $user): void
    {
        $this->createQueryBuilder('m')
            ->update(Message::class, 'm')
            ->set('m.estLu', ':true')
            ->where('m.conversation = :conversation')
            ->andWhere('m.destinataire = :user')
            ->andWhere('m.estLu = :false')
            ->setParameter('conversation', $conversation)
            ->setParameter('user', $user)
            ->setParameter('true', true)
            ->setParameter('false', false)
            ->getQuery()
            ->execute();
    }

    /**
     * Trouve les nouveaux messages après un ID donné avec eager loading
     * @return Message[]
     */
    public function findNewMessagesByConversationAndUser(Conversation $conversation, User $user, int $lastMessageId): array
    {
        return $this->createQueryBuilder('m')
            ->leftJoin('m.expediteur', 'e')
            ->addSelect('e')
            ->where('m.conversation = :conversation')
            ->andWhere('m.id > :lastMessageId')
            ->setParameter('conversation', $conversation)
            ->setParameter('lastMessageId', $lastMessageId)
            ->orderBy('m.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}