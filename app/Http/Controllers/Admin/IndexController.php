<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;


class IndexController extends AdminController
{
    public function __construct()
    {
        parent::__construct();

//        if (Gate::denies('VIEW_ADMIN')) {
//            abort(403);
//        }

        $this->template = config('settings.theme') . '.admin.index';
    }

    public function index()
    {
//        dd(Auth::user());
        $this->title = 'Панель администратора';

        return $this->renderOutput();

    }

}
