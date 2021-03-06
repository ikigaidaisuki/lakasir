<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\BulkDelete;
use App\Http\Requests\User\Index;
use App\Http\Requests\User\Store;
use App\Http\Requests\User\Update;
use App\Repositories\User as UserRepository;
use App\Services\UserService;
use App\Traits\HasCrudActions;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HasCrudActions;

    protected $viewPath = 'app.user';

    protected $permission = 'user';

    protected $indexRequest = Index::class;

    protected $storeRequest = Store::class;

    protected $updateRequest = Update::class;

    protected $bulkDestroyRequest = BulkDelete::class;

    protected $redirect = '/user';

    protected $repositoryClass = UserRepository::class;

    protected $indexService = [UserService::class, 'index'];

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        app()->setLocale(optional(auth()->user() ?? 'en')->localization);
        $this->authorize("create-$this->permission");
        $roles = Role::toBase()->get()->map(function ($c) {
            return ['id' => $c->name, 'text' => $c->name];
        });

        return view("{$this->viewPath}.create", compact('roles'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $model): View
    {
        app()->setLocale(optional(auth()->user() ?? 'en')->localization);

        $data = $this->repository->find($model);

        $this->authorize("update-$this->permission");

        $roles = Role::toBase()->get()->map(function ($c) {
            return ['id' => $c->name, 'text' => $c->name];
        });

        return view("{$this->viewPath}.edit", compact('roles', 'data'));
    }
}
