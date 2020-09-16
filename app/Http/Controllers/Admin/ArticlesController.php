<?php

namespace App\Http\Controllers\Admin;

use App\Article;
use App\Category;
use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleRequest;
use foo\bar;
use Illuminate\Http\Request;
use App\Repositories\ArticlesRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Lang;

class ArticlesController extends AdminController
{
    /**
     * ArticlesController constructor.
     */
    public function __construct(ArticlesRepository $a_rep)
    {
        parent::__construct();

//        if (Gate::denies('VIEW_ADMIN_ARTICLES')) {
//            abort(403);
//        }

        $this->a_rep = $a_rep;

        $this->template = config('settings.theme') . '.admin.articles';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->title = Lang::get('ru.manager_articles');

        $articles = $this->getArticles();
        $this->content = view(config('settings.theme') . '.admin.articles_content')
            ->with('articles', $articles)
            ->render();

        return $this->renderOutput();
    }

    public function getArticles()
    {
        return $this->a_rep->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
//        if (Gate::denies('save', new \App\Article)) {
//            abort(403);
//        }

        $this->title = Lang::get('ru.create_new_article');

        $categories = Category::select('title','alias','parent_id','id')->get();

        foreach ($categories as $category) {
            if ($category->parent_id == 0) {
                $lists[$category->title] = array();
            } else {
                $lists[$categories->where('id', $category->parent_id)->first()->title][$category->id] = $category->title;
            }
        }

        $this->content = view(config('settings.theme').'.admin.articles_create_content')->with('categories', $lists)->render();

        return $this->renderOutput();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ArticleRequest $request)
    {
        $result = $this->a_rep->addArticle($request);

        if (is_array($result) && !empty(['error'])) {
            return back()->with($result);
        }

        return redirect('/admin')->with($result);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($alias)
    {
//        if (Gate::denies('edit', new \App\Article)) {
//            abort(403);
//        }

        $article = Article::where('alias', $alias)->first();
        $article->img = json_decode($article->img);

        $categories = Category::select('title','alias','parent_id','id')->get();

        foreach ($categories as $category) {
            if ($category->parent_id == 0) {
                $lists[$category->title] = array();
            } else {
                $lists[$categories->where('id', $category->parent_id)->first()->title][$category->id] = $category->title;
            }
        }

        $this->title = 'Редактирование материала - ' . $article->title;
        $this->content = view(config('settings.theme').'.admin.articles_create_content')->with(['categories' => $lists, 'article' => $article])->render();

        return $this->renderOutput();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // articles->Article RouteServiceProvider
    public function update(ArticleRequest $request, $article)
    {
        $result = $this->a_rep->updateArticle($request, $article);

        if(is_array($result) && !empty($result['error'])) {
            return back()->with($result);
        }

        return redirect('/admin')->with($result);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->a_rep->deleteArticle($id);

        if(is_array($result) && !empty($result['error'])) {
            return back()->with($result);
        }

        return redirect('/admin')->with($result);
    }
}
