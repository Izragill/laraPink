<?php

namespace App\Http\Controllers;

use App\Category;
use App\Repositories\ArticlesRepository;
use App\Repositories\MenusRepository;
use App\Repositories\PortfoliosRepository;
use App\Repositories\CommentsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ArticlesController extends SiteController
{
    public function __construct(PortfoliosRepository $p_rep, ArticlesRepository $a_rep, CommentsRepository $c_rep)
    {
        parent::__construct(new MenusRepository(new \App\Menu));

        $this->p_rep = $p_rep;
        $this->a_rep = $a_rep;
        $this->c_rep = $c_rep;
        $this->bar = 'right';
        $this->template = config('settings.theme') . '.articles';
    }

    public function index($cat_alias = FALSE)
    {
        $this->title = 'Блог';
        $this->keywords = 'String keywords';
        $this->meta_desc = 'String meta_desc';

        $articles = $this->getArticles($cat_alias);

        $content = view(config('settings.theme') . '.articles_content')->with('articles', $articles)->render();
        $this->vars = Arr::add($this->vars, 'content', $content);

        $comments = $this->getComments(config('settings.resent_comments'));
        $portfolios = $this->getPortfolios(config('settings.resent_portfolios'));
        $this->contentRightBar = view(config('settings.theme') . '.articlesBar')->with(['comments' => $comments, 'portfolios' => $portfolios])->render();

        return $this->renderOutput();
    }

    public function getComments($take)
    {
        $comments = $this->c_rep->get(['text','name','email','site','article_id','user_id'], $take);

        if ($comments) {
            $comments->load('article', 'user');
        }

        return $comments;
    }

    public function getPortfolios($take)
    {
        $portfolios = $this->p_rep->get(['title','text','alias','img','filter_alias'], $take);
        return $portfolios;
    }

    public function getArticles($alias = FALSE)
    {
        $where = FALSE;
        if ($alias) {
            $id = Category::select('id')->where('alias', $alias)->first()->id;
            $where = ['category_id', $id];
        }

        $articles = $this->a_rep->get(
            ['id','title','alias','created_at','img','desc','user_id','category_id','keywords','meta_desc'],
            FALSE,
            TRUE,
            $where
        );

        if ($articles) {
            $articles->load('user', 'category', 'comment');
        }

        return $articles;
    }

    public function show($alias = FALSE)
    {
        $article = $this->a_rep->one($alias,['comment' => TRUE]);

        if ($article) {
            $article->img = json_decode($article->img);
        }
//        dd($article->comment->groupBy('parent_id'));

        if (isset($article->id)) {
            $this->title = $article->title;
            $this->keywords = $article->keywords;
            $this->meta_desc = $article->meta_desc;


        }

        $content = view(config('settings.theme') . '.article_content')->with('article', $article)->render();
        $this->vars = Arr::add($this->vars, 'content', $content);

        $comments = $this->getComments(config('settings.resent_comments'));
        $portfolios = $this->getPortfolios(config('settings.resent_portfolios'));
        $this->contentRightBar = view(config('settings.theme') . '.articlesBar')->with(['comments' => $comments, 'portfolios' => $portfolios])->render();

        return $this->renderOutput();
    }
}