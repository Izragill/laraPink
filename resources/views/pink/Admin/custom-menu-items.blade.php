@foreach($items as $item)
    <tr>
        <td style="text-align: left;">{{ $paddingLeft }} {!! link_to_action('Admin\MenusController@edit', $item->title, [$item->id]) !!}</td>

        <td>{{ $item->url() }}</td>

        <td>
            {!! Form::open(['action' => ['Admin\MenusController@destroy', $item->id],'class'=>'form-horizontal','method'=>'POST']) !!}
            {{ method_field('DELETE') }}
            {!! Form::button('Удалить', ['class' => 'btn btn-french-5','type'=>'submit']) !!}
            {!! Form::close() !!}

        </td>
    </tr>
    @if($item->hasChildren())
        @include(config('settings.theme').'.admin.custom-menu-items', array('items' => $item->children(),'paddingLeft' => $paddingLeft.'--'))

    @endif

@endforeach
