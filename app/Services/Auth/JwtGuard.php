<?php


namespace App\Services\Auth;


use DateTimeZone;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

class JwtGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var Request
     */
    private Request $request;

    public function __construct(
        UserProvider $provider,
        Request $request
    )
    {
        $this->provider = $provider;
        $this->request = $request;
    }


    public function user()
    {
        // TODO: Implement user() method.
        if (!is_null($this->user)) {
            return $this->user;
        }
        $tokenStr = $this->getTokenForRequest();
        if (empty($tokenStr)) {
            return null;
        }
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::file(base_path() . '/key.pem')
        );
        $config->setValidationConstraints(
            new IdentifiedBy(explode(':', config('app.key'))[1]),
            new SignedWith($config->signer(), $config->signingKey()),
            new LooseValidAt(new SystemClock(new DateTimeZone("Asia/Shanghai")))
        );

        $token = $config->parser()->parse($tokenStr);
        assert($token instanceof UnencryptedToken);
        $constraints = $config->validationConstraints();
        try {
            $config->validator()->assert($token, ...$constraints);
        } catch (\Exception $e) {
            return null;
        }
        $id = $token->claims()->get('id');
        return $this->user = $this->provider->retrieveById($id);
    }

    /**
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        // TODO: Implement validate() method.
        return $this->provider->validateCredentials($this->user(), $credentials);
    }

    /**
     * Get the token for the current request.
     *
     * @return string
     */
    public function getTokenForRequest(): string
    {
        if ($this->request->hasHeader('Authorization')) {
            return $this->request->header('Authorization');
        }
        if (($token = $this->request->query('token'))) {
            return str_replace('--', '.', $token);
        }
        return "";
    }
}
