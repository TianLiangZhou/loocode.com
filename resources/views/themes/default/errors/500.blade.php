@extends('default.errors.minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message')
    @if($exception->getMessage() == 'SQLSTATE[HY000] [2002] Connection refused')
        <p>当前数据库连接不可用.</p>
        <p>
            <p>请正确配置<span class="bg-gray-50 dark:bg-gray-700 px-1 font-medium">.env</span>文件中以下配置项的值:</p>
            <ul>
                <li>DB_CONNECTION</li>
                <li>DB_HOST</li>
                <li>DB_PORT</li>
                <li>DB_DATABASE</li>
                <li>DB_USERNAME</li>
                <li>DB_PASSWORD</li>
            </ul>
        </p>
    @elseif(stripos($exception->getMessage(), 'Installation failed') !== false)
        {{ $exception->getMessage() }}
    @else
        {{ __('Server Error') }}
    @endif
@endsection
