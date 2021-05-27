<?php
declare(strict_types=1);


namespace App\Http\Controllers\Backend;


use App\Http\Requests\InstallingRequest;
use App\Http\Result;
use App\Services\Auth\JwtGuard;
use App\Services\OpenService;
use Corcel\Model\Meta\UserMeta;
use Corcel\Model\User;
use Corcel\Services\PasswordService;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Auth\GenericUser;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

/**
 * Class AuthorizeController
 * @package App\Http\Controllers\Backend
 */
class AuthorizeController
{
    /**
     * @param InstallingRequest $request
     * @return Application|ResponseFactory|Response
     * @throws \Exception
     */
    public function register(InstallingRequest $request): Response
    {
        $validated = $request->validated();
        if ($validated['password'] != $validated['confirmPassword']) {
            return response(Result::err(500, "两次密码不一致"));
        }
        $exist = User::where('user_email', $validated['email'])->first();
        if ($exist) {
            return response(Result::err(500, "该邮箱已被注册"));
        }
        $user = new User();
        $user->user_login = $validated['fullName'];
        $user->user_pass = (new PasswordService())->makeHash($validated['password']);
        $user->user_nicename = $validated['fullName'];
        $user->user_email = $validated['email'];
        $user->user_activation_key = "";
        $user->display_name = $validated['fullName'];
        if (!$user->save()) {
            return response(Result::err(500, "注册失败"));
        }
        return response(Result::ok());
    }

    /**
     * @param Request $request
     * @return Result
     * @throws \Exception
     */
    public function authenticate(Request $request): Result
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
            return Result::err(404, "用户不存在");
        }
        $isValid = $guard->getProvider()->validateCredentials($user, $credentials);
        if (!$isValid) {
            return Result::err(600, "密码匹配失败");
        }
        $token = $this->getToken($user->ID, $body['email'], $user->avatar);
        return Result::ok(['token' => $token->toString()]);
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
     * @return \Lcobucci\JWT\Token\Plain
     * @throws \Exception
     */
    private function getToken(int $id, string $email, ?string $avatar): \Lcobucci\JWT\Token\Plain
    {
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            Key\InMemory::file(base_path() . '/key.pem')
        );
        $now = new DateTimeImmutable("now", new DateTimeZone(config('app.timezone')));
        $token = $config->builder()
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
        // ckfinder 需要使用 cookie来认证
        return $token;
    }
}
