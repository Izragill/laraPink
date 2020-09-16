<?php

namespace App\Repositories;

use App\Menu;

class MenusRepository extends Repository
{
    public function __construct(Menu $menu)
    {
        $this->model = $menu;
    }

    public function addMenu($request)
    {
        $data = $request->only('type', 'title', 'parent');

        if (empty($data)) {
            return array('error' => 'Нет даннах');
        }

        switch ($data['type']) {
            case 'customLink':
                $data['path'] = $request->input('custom_link');
            break;

            case 'blogLink':
                if ($request->input('category_alias')) {
                    if ($request->input('category_alias') == 'parent') {
                        $data['path'] = route('articles.index');
                    } else {
                        $data['path'] = route('articlesCat', ['cat_alias'=>$request->input('category_alias')]);
                    }
                } elseif ($request->input('article_alias')) {
                    $data['path'] = route('articles.show', ['alias' => $request->input('article_alias')]);
                }
            break;

            case 'portfolioLink':
                if ($request->input('filter_alias')) {
                    if ($request->input('filter_alias') == 'parent') {
                        $data['path'] = route('portfolios.index');
                    }/* else {
                        $data['path'] = route('portfolios.show', ['alias'=>$request->input('portfolio_alias')]);
                    }*/
                } elseif ($request->input('portfolio_alias')) {
                    $data['path'] = route('portfolios.show', ['alias'=>$request->input('portfolio_alias')]);
                }
            break;
        }

        unset($data['type']);

        if ($this->model->fill($data)->save()) {
            return ['status' => 'Ссылка добавленна'];
        }
    }

    public function updateMenu($request, $menu)
    {
        $data = $request->only('type', 'title', 'parent');

        if (empty($data)) {
            return array('error' => 'Нет даннах');
        }

        switch ($data['type']) {
            case 'customLink':
                $data['path'] = $request->input('custom_link');
                break;

            case 'blogLink':
                if ($request->input('category_alias')) {
                    if ($request->input('category_alias') == 'parent') {
                        $data['path'] = route('articles.index');
                    } else {
                        $data['path'] = route('articlesCat', ['cat_alias'=>$request->input('category_alias')]);
                    }
                } elseif ($request->input('article_alias')) {
                    $data['path'] = route('articles.show', ['alias' => $request->input('article_alias')]);
                }
                break;

            case 'portfolioLink':
                if ($request->input('filter_alias')) {
                    if ($request->input('filter_alias') == 'parent') {
                        $data['path'] = route('portfolios.index');
                    }/* else {
                        $data['path'] = route('portfolios.show', ['alias'=>$request->input('portfolio_alias')]);
                    }*/
                } elseif ($request->input('portfolio_alias')) {
                    $data['path'] = route('portfolios.show', ['alias'=>$request->input('portfolio_alias')]);
                }
                break;
        }

        unset($data['type']);

        if ($menu->fill($data)->update()) {
            return ['status' => 'Ссылка обновленна'];
        }
    }

    public function deleteMenu($menu)
    {
        if ($menu->delete()) {
            return ['status' => 'Ссылка удалена'];
        }
    }
}
