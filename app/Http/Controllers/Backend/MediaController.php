<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;
use CKSource\CKFinder\CKFinder;
use Illuminate\Http\Request;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;

#[Route(title: "媒体", sort: 103, icon: "camera")]
class MediaController extends BackendController
{

    #[Route(title: "媒体库", link: "/app/media/library")]
    public function media(): Result
    {
        return Result::ok();
    }

    /**
     * Action that handles all CKFinder requests.
     *
     * @param ContainerInterface $container
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    #[Route(title: "文件管理", parent: "媒体库",  sort: 1)]
    public function request(ContainerInterface $container, Request $request)
    {
        /** @var CKFinder $connector */
        $connector = $container->get('ckfinder.connector');

        // If debug mode is enabled then do not catch exceptions and pass them directly to Laravel.
        $enableDebugMode = config('ckfinder.debug');

        return $connector->handle($request, HttpKernelInterface::MASTER_REQUEST, !$enableDebugMode);
    }
}
