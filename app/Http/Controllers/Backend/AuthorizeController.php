<?php
declare(strict_types=1);


namespace App\Http\Controllers\Backend;


use App\Http\Requests\RegisterRequest;
use App\Http\Result;
use App\Services\Auth\JwtGuard;
use App\Services\UserService;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Token\Plain;

/**
 * Class AuthorizeController
 * @package App\Http\Controllers\Backend
 */
class AuthorizeController
{

    /**
     * @var UserService
     */
    private UserService $userService;

    /**
     * AuthorizeController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param RegisterRequest $request
     * @return Response
     */
    public function register(RegisterRequest $request): Response
    {
        $validated = $request->validated();
        if ($validated['password'] != $validated['confirmPassword']) {
            return response(Result::err(500, "两次密码不一致"), 500);
        }
        $email = $this->userService->email($validated['email']);
        if ($email) {
            return response(Result::err(500, "该邮箱已被注册"), 500);
        }
        $user = $this->userService->create([
            "user_login" => $validated['fullName'],
            "password" => $validated['password'],
            "email" => $validated['email'],
            "user_activation_key" => "",
        ]);
        if ($user == null) {
            return response(Result::err(500, "注册失败"), 500);
        }
        return response(Result::ok());
    }

    /**
     * @param Request $request
     * @return Application|Response|ResponseFactory
     * @throws \Exception
     */
    public function authenticate(Request $request): Response
    {
        $body = $request->json()->all();
        /**
         * @var $guard JwtGuard
         */
        $guard = Auth::guard("backend");
        $credentials = ['email' => $body['email'], 'password' => $body['password']];

        /**
         * @var $user GenericUser
         */
        $user = $guard->getProvider()->retrieveByCredentials($credentials);
        if ($user == null) {
            return response(Result::err(404, "用户不存在"), 404);
        }
        if ($user->user_activation_key == "") {
            return response(Result::err(401, "请管理员激活此账号"), 403);
        }
        $isValid = $guard->getProvider()->validateCredentials($user, $credentials);
        if (!$isValid) {
            return response(Result::err(600, "密码匹配失败"), 500);
        }
        $token = $this->getToken($user->ID, $body['email'], $user->avatar);
        return response(Result::ok(['token' => $token->toString()], "登录成功"));
    }


    /**
     * @return Response
     */
    public function logout(): Response
    {
        $cookie = cookie('token', sameSite: 'None');
        return response(Result::ok())->cookie($cookie);
    }


    /**
     * @param int $id
     * @param string $email
     * @param string|null $avatar
     * @return Plain
     * @throws \Exception
     */
    private function getToken(int $id, string $email, ?string $avatar): Plain
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            Key\InMemory::file(base_path() . '/key.pem')
        );
        $now = new DateTimeImmutable("now", new DateTimeZone(config('app.timezone')));
        return $config->builder()
            ->issuedBy(config('app.url'))
            ->identifiedBy(explode(':', config('app.key'))[1])
            ->issuedAt($now)
            ->canOnlyBeUsedAfter($now)
            // Configures the expiration time of the token (exp claim)
            ->expiresAt($now->modify('+24 hour'))
            // Configures a new claim, called "uid"
            ->withClaim('id', $id)
            ->withClaim('email', $email)
            ->withClaim('avatar', $avatar)
            ->getToken($config->signer(), $config->signingKey());
    }
}
