<?php


namespace App\Http\Controllers\Backend;


use App\Attributes\Route;
use App\Http\Result;

#[Route(title: "媒体", sort: 103, icon: "camera")]
class MediaController extends BackendController
{

    #[Route(title: "媒体库", parent: "媒体", link: "/app/media/library")]
    public function media(): Result
    {
        return Result::ok();
    }
}
