<?php

namespace App\Repositories;

use App\Abstracts\Repository as RepositoryAbstract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class User extends RepositoryAbstract
{
    protected string $model = 'App\Models\User';

    /**
     * @var string
     */
    private string $role = 'employee';

    public function create(Request $request)
    {
        $self = $this;
        return DB::transaction(static function () use ($request, $self) {
            if (getenv('INSTALL') == 'false') {
                $session = $request->session()->all()['user'];
                $request->merge($session);
            }
            $request->merge(['password' => bcrypt($request->password)]);
            $user = new $self->model();
            $user = $user->fill($request->all());
            $user->save();
            if ($request->role) {
                $self->role = $request->role;
            }
            $role = Role::whereName($self->role)->first();
            $user->assignRole($role);

            return $user;
        });
    }

    public function update(Request $request, $user)
    {
        $self = $this;
        return DB::transaction(static function () use ($request, $self, $user) {
            if (getenv('INSTALL') == 'false') {
                $session = $request->session()->all()['user'];
                $request->merge($session);
            }
            if ($request->password) {
                $request->merge(['password' => bcrypt($request->password)]);
            } else {
                $request->merge(['password' => $user->password]);
            }
            $user = $user->fill($request->all());
            $user->save();
            if ($request->role) {
                $self->role = $request->role;
            }
            $role = Role::whereName($self->role)->first();
            $user->syncRoles($role);

            return $user;
        });
    }

    public function updatePassword(Request $request, $user)
    {
        return $user->update($request->all());
    }


    public function role(string $role): self
    {
        $this->role = $role;
        return $this;
    }
}
