<div id="sidebar-content">
    <h1>{{ $menuBuilder->title }}</h1>
    <nav>
        <ul>
            @foreach($menuBuilder->items as $item)
                <li>
                    <a href="{{ $item->url }}">{{ $item->label }}</a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
