<aside class="sidebar">
    <nav class="sidebar__nav">
        <ul class="sidebar__list">
            <li class="sidebar__item">
                <a href="{{ route('admin.dashboard') }}" class="sidebar__link {{ request()->routeIs('admin.dashboard') ? 'sidebar__link_active' : '' }}">
                    <i class="fa-solid fa-house sidebar__link-icon"></i>Главная
                </a>
            </li>
            <li class="sidebar__item">
                <a href="{{ route('admin.users.index') }}" class="sidebar__link {{ request()->routeIs('admin.users.index') ? 'sidebar__link_active' : '' }}">
                    <i class="fa-solid fa-user sidebar__link-icon"></i>Пользователи
                </a>
            </li>
            <li class="sidebar__item">
                <a href="{{ route('admin.roles.index') }}" class="sidebar__link {{ request()->routeIs('admin.roles.index') ? 'sidebar__link_active' : '' }}">
                    <i class="fa-solid fa-layer-group sidebar__link-icon"></i>Группы
                </a>
            </li>
            <li class="sidebar__item">
                <a href="{{ route('admin.categories.index') }}" class="sidebar__link {{ request()->routeIs('admin.categories.index') ? 'sidebar__link_active' : '' }}">
                    <i class="fa-solid fa-list sidebar__link-icon"></i>Категории
                </a>
            </li>
            <li class="sidebar__item">
                <a href="{{ route('admin.tags.index') }}" class="sidebar__link {{ request()->routeIs('admin.tags.index') ? 'sidebar__link_active' : '' }}">
                    <i class="fa-solid fa-tags sidebar__link-icon"></i>Теги
                </a>
            </li>
            <li class="sidebar__item">
                <a href="{{ route('admin.posts.index') }}" class="sidebar__link {{ request()->routeIs('admin.posts.index') ? 'sidebar__link_active' : '' }}">
                    <i class="fa-regular fa-newspaper sidebar__link-icon"></i>Посты
                </a>
            </li>
            <li class="sidebar__item">
                <a href="{{ route('admin.comments.index') }}" class="sidebar__link {{ request()->routeIs('admin.comments.index') ? 'sidebar__link_active' : '' }}">
                    <i class="fa-solid fa-comment sidebar__link-icon"></i>Комментарии
                </a>
            </li>
            <li class="sidebar__item">
                <a href="{{ route('admin.settings.index') }}" class="sidebar__link {{ request()->routeIs('admin.settings.index') ? 'sidebar__link_active' : '' }}">
                    <i class="fa-solid fa-gear sidebar__link-icon"></i>Настройки
                </a>
            </li>
        </ul>
    </nav>
</aside>
