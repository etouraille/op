<?php

namespace App\Controller;

use App\Entity\Thing;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SitemapController extends AbstractController
{
    #[Route('/sitemap', name: 'app_sitemap')]
    public function index(EntityManagerInterface $em): Response
    {
        $sites = $em->getRepository(Thing::class)->findAllExceptPending();

        $date = array_reduce($sites, function($a, $site)  {
            /** @var $site Thing */
            if(!$a) {
                return $site->getActivationDate();
            } else {
                /** @var $date DateTime,
                 * @var $a DateTime
                 */
                $date = $site->getActivationDate();
                if($date && (int)$date->diff($a)->format('%R%a') > 0) {
                    return $date;
                } else {
                    return $a;
                }
            }
        }, null );

        return $this->render('sitemap/index.html.twig', [
            'sites' => $sites,
            'date' => $date->format('d/m/Y'),
        ]);
    }
}
