@if($articles)
<div id="content-page" class="content group">
    <div class="hentry group">
        <h2>Добавленные статьи</h2>
        <div class="short-table white">
            <table style="width: 100%" cellspacing="0" cellpadding="0">
                <thead>
                <tr>
                    <th class="align-left">ID</th>
                    <th>Заголовок</th>
                    <th>Текст</th>
                    <th>Изображение</th>
                    <th>Категория</th>
                    <th>Псевдоним</th>
                    <th>Дествие</th>
                </tr>
                </thead>
                <tbody>

                @foreach($articles as $article)
                <tr>
                    <td class="align-left">{{$article->id}}</td>
                    <td class="align-left">{{ link_to_action('Admin\ArticlesController@edit', $title = $article->title, $parameters = [$article->alias], $attributes = []) }}</td>

                    <td class="align-left">{{Str::limit($article->text,200)}}</td>
                    <td>
                        @if(isset($article->img->mini))
                        {!! Html::image(asset(config('settings.theme')).'/images/articles/'.$article->img->mini) !!}
                        @endif
                    </td>
                    <td>{{$article->category->title}}</td>
                    <td>{{$article->alias}}</td>
                    <td>
                        {!! Form::open(['action' => ['Admin\ArticlesController@destroy', $id = $article->alias],'class'=>'form-horizontal','method'=>'POST']) !!}
                        @method('DELETE')
                        {!! Form::button('Удалить', ['class' => 'btn btn-french-5','type'=>'submit']) !!}
                        {!! Form::close() !!}
                    </td>
                </tr>
                @endforeach

                </tbody>
            </table>
        </div>

        {!! link_to_action('Admin\ArticlesController@create','Добавить  материал', [], ['class' => 'btn btn-the-salmon-dance-3']) !!}


    </div>
    <!-- START COMMENTS -->
    <div id="comments">
    </div>
    <!-- END COMMENTS -->
</div>
@endif
