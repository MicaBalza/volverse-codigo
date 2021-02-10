<?php
namespace App\Controller;

use App\Entity\Capsule;
use App\Service\CronService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/")
     */
    public function index(CronService $cron) {
        $cron->doCron(); // Run a cron iteration on every page load
        return $this->render('index.html.twig');
    }

    /**
     * @Route("/intro", name="intro")
     */
    public function intro(CronService $cron) {
        $cron->doCron(); // Run a cron iteration on every page load
        return $this->render('intro.html.twig');
    }

    /**
     * @Route("/about", name="about")
     */
    public function about(CronService $cron) {
        $cron->doCron(); // Run a cron iteration on every page load
        return $this->render('about.html.twig');
    }


    /**
     * @Route("/capsula/{capsuleId}")
     */
    public function capsule(string $capsuleId) {
        $capsule = $this
            ->getDoctrine()
            ->getRepository(Capsule::class)
            ->findOneBy(['code' => $capsuleId]);

        return $this->render('capsule-single.html.twig', ['capsule' => $capsule]);
    }
}