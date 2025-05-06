<ul{!! BaseHelper::clean($options) !!}>
    @foreach ($menu_nodes as $key => $row)
        <li @class(['dropdown2' => $row->has_child, 'current' => $row->active, $row->css_class])>
            <a href="{{ $row->url }}" target="{{ $row->target }}">
                {!! BaseHelper::clean($row->icon_html) !!}
                {{ $row->title }}
            </a>

            @if ($row->has_child)
                {!! Menu::generateMenu([
                    'menu' => $menu,
                    'menu_nodes' => $row->child,
                    'view' => 'main-menu',
                ]) !!}
            @endif
        </li>
    @endforeach
</ul>
