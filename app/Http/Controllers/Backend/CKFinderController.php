<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use CKSource\CKFinder\CKFinder;
use Illuminate\Http\Request;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class CKFinderController
 * @package App\Http\Controllers\Backend
 */
#[Route(title: "设置", sort: 111, icon: "settings-2")]
class CKFinderController extends BackendController
{
    /**
     * Action that handles all CKFinder requests.
     *
     * @param ContainerInterface $container
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    #[Route(title: "文件管理", sort: 1, hidden: true)]
    public function request(ContainerInterface $container, Request $request)
    {
        /** @var CKFinder $connector */
        $connector = $container->get('ckfinder.connector');

        // If debug mode is enabled then do not catch exceptions and pass them directly to Laravel.
        $enableDebugMode = config('ckfinder.debug');

        return $connector->handle($request, HttpKernelInterface::MASTER_REQUEST, !$enableDebugMode);
    }
}
