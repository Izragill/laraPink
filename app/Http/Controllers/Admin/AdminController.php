<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Lavary\Menu\Menu;

class AdminController extends Controller
{
    protected $p_rep;
    protected $a_rep;
    protected $user;
    protected $template;
    protected $content = FALSE;
    protected $title;
    protected $vars;

    public function __construct()
    {
        // вот такой костыль!!!
//        $this->middleware(function () {
//            $this->user = Auth::user();
//            dd($this->user);
////            $this->user = Auth::id();
//        });

//        Auth::loginUsingId(1);
//        dd(Auth::user());
//        $this->user = Auth::user();
//
//        if (!$this->user) {
//            dd(User::all()->where('id','1'));
//           $res = Auth::user(User::all()->where('id','1'));
//            dd($res);
//            abort(403);
//        }



//        $this->user = Auth::user();
//        $this->user = Auth::id();
//        dd(Auth::id());

//        if(!$this->user) {
//            abort(403);
//        }
    }

    public function renderOutput()
    {
        $this->user = Auth::user();

        $this->vars = Arr::add($this->vars, 'title', $this->title);

        $menu = $this->getMenu();

        $navigation = view(config('settings.theme') . '.admin.navigation')->with('menu', $menu)->render();
        $this->vars = Arr::add($this->vars, 'navigation', $navigation);

        if ($this->content) {
            $this->vars = Arr::add($this->vars, 'content', $this->content);
        }

        $footer = view(config('settings.theme') . '.admin.footer')->render();
        $this->vars = Arr::add($this->vars, 'footer', $footer);

        return view($this->template)->with($this->vars);
    }

    public function getMenu()
    {
        return \Menu::make('adminMenu', function ($menu) {
            $menu->add('Статьи', ['route' => 'admin.articles.index']);
            $menu->add('Портфолио', ['route' => 'admin.articles.index']);
            $menu->add('Меню', ['route' => 'admin.menus.index']);
            $menu->add('Пользователи', ['route' => 'admin.users.index']);
            $menu->add('Привелегии', ['route' => 'admin.permissions.index']);
        });

    }
}
