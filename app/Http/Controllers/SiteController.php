<?php

namespace App\Http\Controllers;

use App\Repositories\MenusRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Menu;

class SiteController extends Controller
{
    protected $p_rep;//portfolio repository
    protected $s_rep;//slider repository
    protected $a_rep;//articles repository
    protected $m_rep;//menus repository

    protected $keywords;
    protected $meta_desc;
    protected $title;

    protected $template;//template what shows name

    protected $vars = array();//array vars to template

    protected $bar = 'no';//sidebar
    protected $contentRightBar = FALSE;
    protected $contentLeftBar = FALSE;

    public function __construct(MenusRepository $m_rep)
    {
        $this->m_rep = $m_rep;
    }

    protected function renderOutput()
    {
        $menu = $this->getMenu();

        $navigation = view(config('settings.theme').'.navigation')->with('menu',$menu)->render();
        $this->vars = Arr::add($this->vars, 'navigation', $navigation);

        if ($this->contentRightBar) {
            $rightBar = view(config('settings.theme') . '.rightBar')->with('content_rightBar',$this->contentRightBar)->render();
            $this->vars = Arr::add($this->vars, 'rightBar', $rightBar);
        }
        if ($this->contentLeftBar) {
            $leftbar = view(config('settings.theme') . '.leftBar')->with('content_leftBar',$this->contentLeftBar)->render();
            $this->vars = Arr::add($this->vars, 'leftbar', $leftbar);
        }

        $this->vars = Arr::add($this->vars, 'bar', $this->bar);//опеределяет сайдбар

        $footer = view(config('settings.theme').'.footer')->render();
        $this->vars = Arr::add($this->vars, 'footer', $footer);

        $this->vars = Arr::add($this->vars, 'keywords', $this->keywords);
        $this->vars = Arr::add($this->vars, 'meta_desc', $this->meta_desc);
        $this->vars = Arr::add($this->vars, 'title', $this->title);

        return view($this->template)->with($this->vars);
    }

    public function getMenu()
    {
        $menu = $this->m_rep->get();

        $mBilder = Menu::make('MyNav', function ($m) use ($menu) {
            foreach ($menu as $item) {
                if ($item->parent == 0) {
                    $m->add($item->title, $item->path)->id($item->id);
                }
                else {
                    if ($m->find($item->parent)) {
                        $m->find($item->parent)->add($item->title, $item->path)->id($item->id);
                    }
                }
            }
        });

//        dd($mBilder);

        return $mBilder;
    }


}
