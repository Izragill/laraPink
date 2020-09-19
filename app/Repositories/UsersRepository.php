<?php

namespace App\Repositories;

use App\User;

class UsersRepository extends Repository
{
    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public function addUser($request)
    {
        $data = $request->all();

        $user = $this->model->create(
            [
                'name' => $data['name'],
                'login' => $data['login'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]
        );

        if($user) {
            $user->roles()->attach($data['role_id']);
        }

        return ['status' => 'Пользователь добавлен'];
    }


    public function updateUser($request, $user)
    {
        $data = $request->all();

        if ($data['password'] === null) {
            unset($data['password']);
            unset($data['password_confirmation']);
        }

        $user->fill($data)->update();

        $user->roles()->sync([$data['role_id']]);

        return ['status' => 'Пользователь изменен'];
    }

    public function deleteUser($user)
    {
        $user->roles()->detach();

        if($user->delete()) {
            return ['status' => 'Пользователь удален'];
        }
    }

}
