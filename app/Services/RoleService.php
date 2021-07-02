<?php


namespace App\Services;



use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class RoleService
 * @package App\Services
 * @property Role $model
 */
class RoleService extends BaseService
{
    /**
     * RoleService constructor.
     * @param Role $role
     */
    public function __construct(Role $role)
    {
        $this->model = $role;
    }

    /**
     * @param Request $request
     * @param int $size
     * @return LengthAwarePaginator
     */
    public function getRoles(Request $request, int $size = 30): LengthAwarePaginator
    {
        return $this->model->without("permission")
            ->orderBy('id', 'desc')
            ->paginate($size, ['*'], 'data_current_page');
    }

    /**
     * @param string $name
     * @return ?Model
     */
    public function name(string $name): ?Model
    {
        try {
            return $this->where("name", $name)->first();
        }catch (\Exception $exception) {
            return null;
        }
    }

    /**
     * @param array $idArray
     * @return Collection
     */
    public function idArray(array $idArray)
    {
        return $this->without("permission")->whereIn("id", $idArray)->get();
    }
}
