<?php

namespace App\Service;

use App\Entity\Capsule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;

class CronService {
    private $em;
    private $email;
    private $log;

    public function __construct(EntityManagerInterface $doctrine, EmailService $email, LoggerInterface $log) {
        $this->em = $doctrine;
        $this->email = $email;
        $this->log = $log;
    }

    /**
     * Expires capsules and send e-mails.
     */
    public function doCron() {
        $q = $this->em->createQuery(
            'SELECT c  
                FROM App\Entity\Capsule c 
                WHERE c.expiration_date < CURRENT_TIMESTAMP() 
                AND c.archived = false
                ORDER BY c.expiration_date ASC'
        );
        $q->setMaxResults(1);

        try {
            $capsule = $q->getSingleResult();
            $this->email->sendExpiredEmail($capsule);
            $capsule->setArchived(true);
            $this->em->flush();
            $this->log->info("Expired capsule: " . $capsule->getId());
        } catch (NoResultException $e) {
            // No pending capsules. Nothing to do.
            $this->log->info("No pending capsules");
        } catch (NonUniqueResultException $e) {
            // This cannot happen.
            $this->log->error("More than one result returned from capsules query");
        }
    }
}