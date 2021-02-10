<?php
namespace App\Controller;

use App\Service\CronService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CronController extends AbstractController
{
    /**
     * @Route("/cron")
     *
     * @param CronService $cron
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cron(CronService $cron) {
        $cron->doCron();
        return $this->redirectToRoute('app_index_index');
    }
}