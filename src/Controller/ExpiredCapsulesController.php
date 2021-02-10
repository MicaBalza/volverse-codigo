<?php
namespace App\Controller;

use App\Entity\Capsule;
use App\Service\CronService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ExpiredCapsulesController extends AbstractController
{
    const N_RESULTS = 32 * 40; // 32 rows * 40 items per row

    /**
     * @Route("/capsulas", name="capsules")
     */
    public function capsules(CronService $cron, EntityManagerInterface $em) {
        $cron->doCron(); // Run a cron iteration on every page load.

        $rows = $em
            ->getRepository(Capsule::class)
            ->createQueryBuilder('c')
            ->select("COUNT(c.id)")
            ->where('c.expiration_date < CURRENT_TIMESTAMP()')
            ->getQuery()
            ->getSingleScalarResult();
        $offset = max(0, rand(0, $rows - self::N_RESULTS - 1));

        $ret = $em
            ->getRepository(Capsule::class)
            ->createQueryBuilder('c')
            ->select(['c.code', 'c.owner_email'])
            ->where('c.expiration_date < CURRENT_TIMESTAMP()')
            ->setFirstResult($offset)
            ->setMaxResults(self::N_RESULTS);

        $textRows = [];
        $currentRow = [];
        $colNumber = 0;
        foreach ($ret->getQuery()->getResult() as $result) {
            $currentRow[] = ['id' => $result['code'], 'letter' => $result['owner_email'][0]];
            $colNumber++;
            if ($colNumber == 40) {
                $textRows[] = $currentRow;
                $currentRow = [];
                $colNumber = 0;
            }
        }

        if ($colNumber != 40)
            $textRows[] = $currentRow;

        return $this->render('capsules-list.html.twig', [ 'capsules' => $textRows ]);
    }
}