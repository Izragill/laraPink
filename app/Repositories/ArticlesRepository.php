<?php

namespace App\Repositories;

use App\Article;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ArticlesRepository extends Repository
{
    public function __construct(Article $articles)
    {
        $this->model = $articles;
    }

    public function one($alias, $attr = array())
    {
        $article = parent::one($alias, $attr);

        if ($article && !empty($attr)) {
            $article->load('comment');
            $article->comment->load('user');
        }

        return $article;
    }

    public function addArticle($request)
    {
        $data = $request->except('_token', 'image');

        if (empty($data)) {
            return array('error' => 'Нет даннах');
        }

        if (empty($data['alias'])) {
            $data['alias'] = $this->transliterate($data['title']);
        }

        if ($this->one($data['alias'], FALSE)) {
            $request->merge(['alias' => $data['alias']]);
            $request->flash();

            return ['error' => 'Данный псевдоним уже используется'];
        }

        if($request->hasFile('image')) {
            $image = $request->file('image');

            if ($image->isValid()) {
                $str = Str::random(8);

                $obj = new \stdClass();
                $obj->mini = $str . '_mini.jpg';
                $obj->max = $str . '_max.jpg';
                $obj->path = $str . '.jpg';

                $img = Image::make($image);
                $img->fit(config('settings.image')['width'], config('settings.image')['height'])
                    ->save(public_path() . '/' . config('settings.theme') . '/images/articles/' . $obj->path);

                $img->fit(config('settings.articles_img')['max']['width'], config('settings.articles_img')['max']['height'])
                    ->save(public_path() . '/' . config('settings.theme') . '/images/articles/' . $obj->max);

                $img->fit(config('settings.articles_img')['mini']['width'], config('settings.articles_img')['mini']['height'])
                    ->save(public_path() . '/' . config('settings.theme') . '/images/articles/' . $obj->mini);

                $data['img'] = json_encode($obj);

                $this->model->fill($data);

                if ($request->user()->articles()->save($this->model)) {
                    return ['status' => 'Материал добавлен'];
                }
            }
        }
    }

    public function updateArticle($request, $article) {
        $article = Article::where('alias', $article)->first();

        $data = $request->except('_token','image','_method');

        if(empty($data)) {
            return array('error' => 'Нет данных');
        }

        if(empty($data['alias'])) {
            $data['alias'] = $this->transliterate($data['title']);
        }

        $result = $this->one($data['alias'],FALSE);

        if(isset($result->id) && ($result->id != $article->id)) {
            $request->merge(array('alias' => $data['alias']));
            $request->flash();

            return ['error' => 'Данный псевдоним уже успользуется'];
        }

        if($request->hasFile('image')) {
            $image = $request->file('image');

            if($image->isValid()) {

                $str = Str::random(8);

                $obj = new \stdClass;

                $obj->mini = $str.'_mini.jpg';
                $obj->max = $str.'_max.jpg';
                $obj->path = $str.'.jpg';

                $img = Image::make($image);

                $img->fit(config('settings.image')['width'], config('settings.image')['height'])
                    ->save(public_path() . '/' . config('settings.theme') . '/images/articles/' . $obj->path);

                $img->fit(config('settings.articles_img')['max']['width'], config('settings.articles_img')['max']['height'])
                    ->save(public_path() . '/' . config('settings.theme') . '/images/articles/' . $obj->max);

                $img->fit(config('settings.articles_img')['mini']['width'], config('settings.articles_img')['mini']['height'])
                    ->save(public_path() . '/' . config('settings.theme') . '/images/articles/' . $obj->mini);

                $data['img'] = json_encode($obj);
            }
        }

//        $article->fill([
//            "title" => $data['title'],
//            "keywords" => $data['keywords'],
//            "meta_desc" => $data['meta_desc'],
//            "alias" => $data['alias'],
//            "desc" => $data['desc'],
//            "text" => $data['text'],
//            "category_id" => $data['category_id']
//        ]);
//
//        if (!empty($data['img'])) {
//            $article->fill([
//               "img" => $data['img']
//            ]);
//        }
        $article->fill($data);

        if($article->update()) {
            return ['status' => 'Материал обновлен'];
        }

    }

    public function deleteArticle($alias)
    {
        $article = Article::where('alias', $alias)->first();

        $article->comment()->delete();

        if ($article->delete()) {
            return ['status' => 'Материал удален'];
        }

    }
}
