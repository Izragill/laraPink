@if($menu)
    <div class="menu classic">
        <ul id="nav" class="menu">
            @include(config('settings.theme').'.customMenuItems', ['items'=>$menu->roots()])
{{--            {!! $menu->asUL() !!}--}}
        </ul>
    </div>
@endif
