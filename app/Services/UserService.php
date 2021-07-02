<?php


namespace App\Services;


use Corcel\Model\User;
use Corcel\Services\PasswordService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

/**
 * Class UserService
 * @package App\Services
 * @property User $model
 */
class UserService extends BaseService
{

    private PasswordService $passwordService;

    /**
     * UserService constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->model = $user;

        $this->passwordService = new PasswordService();
    }

    /**
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function getPaginator(Request $request): LengthAwarePaginator
    {
        $model = $this->model;
        if ($request->query->has("ID_like") && ($id = $request->query->getInt('ID_like'))) {
            $model->where("ID", $id);
        }
        if ($request->query->has("user_nicename_like") && ($nickname = $request->query->get('user_nicename_like'))) {
            $model->where("user_login", "like", '%' . $nickname . '%');
        }
        if ($request->query->has("email_like") && ($email = $request->request->get('email_like'))) {
            $model->where('user_email', $email);
        }
        return $model->paginate(
            $request->query->getInt("data_per_page", 30),
            ['*'],
            'data_current_page',
        );
    }

    /**
     * @param string $email
     * @return ?Model
     */
    public function email(string $email): ?Model
    {
        try {
            return $this->where("user_email", $email)->first();
        } catch (Exception $_) {
            return null;
        }
    }

    /**
     * @param array $data
     * @param bool $isManager
     * @return ?User
     */
    public function create(array $data, bool $isManager = false): ?User
    {
        /**
         * @var $user User
         */
        $user = $this->getModel();
        $user->user_login = $data['user_login'];
        $user->user_pass = $this->passwordService->makeHash($data['password']);
        $user->user_nicename = $data['user_login'];
        $user->user_email = $data['email'];
        $user->user_activation_key = $data['user_activation_key'] ?? Str::random(8);
        $user->display_name = $data['user_login'];
        if ($user->save()) {
            if ($isManager) {
                $user->saveMeta('roles', json_encode($data['roles']));
            }
            return $user;
        }
        return null;
    }

    /**
     * @param User $user
     * @param array $data
     * @param bool $isManager
     * @return bool
     */
    public function update(User $user, array $data, bool $isManager = false): bool
    {
        $user->user_login = $data['user_login'];
        if (!empty($data['password'])) {
            $user->user_pass = $this->passwordService->makeHash($data['password']);
        }
        $user->user_nicename = $data['user_login'];
        $user->user_email = $data['email'];
        $user->display_name = $data['user_login'];
        if ($isManager) {
            $user->saveMeta('roles', json_encode($data['roles']));
        }
        return $user->save();
    }

}
