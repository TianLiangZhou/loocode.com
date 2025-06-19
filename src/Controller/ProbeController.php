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
                    "name" => 'PHP 版本',
                    "description" => PHP_VERSION,
                    "status" => null,
                ],
                [
                    "name" => '存储驱动',
                    "description" => substr(array_reverse(explode("\\", get_class($this->em->getConnection()->getDatabasePlatform())))[0], 0, -8),
                    "status" => null,
                ],
                [
                    "name" => '存储版本',
                    "description" => $this->em->getConnection()->getServerVersion(),
                    "status" => null,
                ],
                [
                    "name" => 'Symfony 版本',
                    "description" => \Symfony\Component\HttpKernel\Kernel::VERSION,
                    "status" => null,
                ],
                [
                    "name" => 'Redis 版本',
                    "description" =>  extension_loaded("redis") ? phpversion('redis'): '',
                    "status" => extension_loaded("redis"),
                ],
                [
                    "name" => 'cURL',
                    "description" =>  extension_loaded("curl") ? curl_version()["version"] : '',
                    "status" => extension_loaded("curl"),
                ],
                [
                    "name" => 'gd',
                    "description" =>  '',
                    "status" => extension_loaded("gd"),
                ],
                [
                    "name" => 'imagick',
                    "description" =>  extension_loaded("imgick") ? phpversion('imgick'): '',
                    "status" => extension_loaded("imagick"),
                ],
                [
                    "name" => 'FFI',
                    "description" =>  '',
                    "status" => extension_loaded("FFI"),
                ],

                [
                    "name" => 'SAPI',
                    "description" =>  PHP_SAPI,
                    "status" => null,
                ],
                
            ],
            'title' => '探针信息',
        ]);
    }
}