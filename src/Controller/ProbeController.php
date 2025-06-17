<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\Route as RouteAttribute;
use Symfony\Component\HttpKernel\Attribute\Method;
use Symfony\Component\HttpKernel\Attribute\NoRoute;
use Symfony\Component\HttpKernel\Attribute\IsGranted;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\HttpKernel\Attribute\Cacheable;
use Symfony\Component\HttpKernel\Attribute\CacheControl;
use Doctrine\ORM\EntityManagerInterface;
/**
 * Class ProbeController
 */
#[AsController]
class ProbeController extends Controller
{
    public function __construct(protected EntityManagerInterface $em)
    { 

    }

    /**
     * @return Response
     */
    #[Route('/probe', name: 'probe_page')]
    public function index(): Response {

        
        return $this->render('probe/index.html.twig', [
            'probes' => [
                [
                    "name" => 'PHP Version',
                    "description" => PHP_VERSION,
                    "status" => 'active',
                    "created_at" => new \DateTime(),
                    "updated_at" => new \DateTime(),
                ],
                [
                    "name" => 'Database Source',
                    "description" => substr(array_reverse(explode("\\", get_class($this->em->getConnection()->getDatabasePlatform())))[0], 0, -8),
                    "status" => 'active',
                    "created_at" => new \DateTime(),
                    "updated_at" => new \DateTime(),
                ],
                [
                    "name" => 'Database Source Version',
                    "description" => $this->em->getConnection()->getServerVersion(),
                    "status" => 'active',
                    "created_at" => new \DateTime(),
                    "updated_at" => new \DateTime(),
                ],
                [
                    "name" => 'Symfony Version',
                    "description" => \Symfony\Component\HttpKernel\Kernel::VERSION,
                    "status" => 'active',
                    "created_at" => new \DateTime(),
                    "updated_at" => new \DateTime(),
                ],
                [
                    "name" => 'Redis Version',
                    "description" =>  phpversion('redis'),
                    "status" => 'active',
                    "created_at" => new \DateTime(),
                    "updated_at" => new \DateTime(),
                ],

            ],
            'title' => 'Probe Page',
            'message' => 'This is a probe page.'
        ]);
    }
}