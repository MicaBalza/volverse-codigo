<?php

namespace App\Service;

use App\Entity\Capsule;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class EmailService {
    private $log;
    private $mailer;
    private $mailFrom;

    public function __construct(MailerInterface $mailer, LoggerInterface $log, string $mailFrom) {
        $this->log = $log;
        $this->mailer = $mailer;
        $this->mailFrom = $mailFrom;
    }

    public function sendCreatedEmail(Capsule $capsule) {
        try {
            $this->sendEmail(
                $capsule->getOwnerEmail(),
                'Volverse Código - Cápsula creada',
                'email/created_owner.html.twig',
                $capsule
            );

            $this->sendEmail(
                $capsule->getRecipients(),
                'Volverse Código - Te dedicaron una cápsula',
                'email/created_deds.html.twig',
                $capsule
            );
        } catch (TransportExceptionInterface $e) {
            $this->log->error("Could not send 'created' e-mails for capsule " . $capsule->getId(),
                ['error' => $e]);
            return;
        }

        $this->log->info("Sent 'created' e-mails for capsule " . $capsule->getId());
    }

    public function sendExpiredEmail(Capsule $capsule) {
        try {
            $this->sendEmail(
                $capsule->getOwnerEmail(),
                'Volverse Código - Cápsula expirada',
                'email/expired_owner.html.twig',
                $capsule
            );

            $this->sendEmail(
                $capsule->getRecipients(),
                'Volverse Código - Te dedicaron una cápsula',
                'email/created_deds.html.twig',
                $capsule
            );
        } catch (TransportExceptionInterface $e) {
            $this->log->error("Could not send 'expired' e-mail for capsule " . $capsule->getId(),
                ['error' => $e]);
            return;
        }

        $this->log->info("Sent 'expired' e-mail for capsule " . $capsule->getId());
    }

    /**
     * Sends a templated e-mail.
     *
     * @param $to string|array
     * @param $subject string
     * @param $template string
     * @param $capsule Capsule
     *
     * @throws TransportExceptionInterface
     */
    private function sendEmail($to, string $subject, string $template, Capsule $capsule) {
        if (is_array($to) && (count($to) == 0))
            return;

        $email = (new TemplatedEmail())
            ->to($to)
            ->from($this->mailFrom)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context(['capsule' => $capsule]);


        $this->mailer->send($email);
    }
}