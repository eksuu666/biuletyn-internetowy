<?php

return [
    'sidebar_menus' => [
        'panel' => [
            'title' => 'Kokpit',
            'icon' => 'icon-home',
            'url' => route(config('app.panel_prefix', 'panel').'.index'),
        ],

        'article' => [
            'title' => 'Artykuły',
            'icon' => 'icon-globe',
            'url' => route(config('app.panel_prefix', 'panel').'.articles.index'),
            'permissions' => config('permissions_list.ARTICLE_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.articles.create',
                config('app.panel_prefix', 'panel').'.articles.edit',
                config('app.panel_prefix', 'panel').'.articles.seo-settings',
            ],
        ],

        'category' => [
            'title' => 'Kategorie',
            'icon' => 'icon-grid',
            'url' => route(config('app.panel_prefix', 'panel').'.categories.index'),
            'permissions' => config('permissions_list.CATEGORY_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.categories.create',
                config('app.panel_prefix', 'panel').'.categories.edit',
                config('app.panel_prefix', 'panel').'.categories.seo-settings',
            ],
        ],

        'tag' => [
            'title' => 'Tagi',
            'icon' => 'icon-tag',
            'url' => route(config('app.panel_prefix', 'panel').'.tags.index'),
            'permissions' => config('permissions_list.TAG_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.tags.create',
                config('app.panel_prefix', 'panel').'.tags.edit',
                config('app.panel_prefix', 'panel').'.tags.seo-settings',
            ],
        ],

        'comment' => [
            'title' => 'Komentarze',
            'icon' => 'icon-bubbles',
            'url' => route(config('app.panel_prefix', 'panel').'.comments.index'),
            'permissions' => config('permissions_list.COMMENT_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.comments.show',
            ],
        ],

        'menu_builder' => [
            'title' => 'Kreator menu',
            'icon' => 'icon-menu',
            'url' => route(config('app.panel_prefix', 'panel').'.menus.index'),
            'permissions' => config('permissions_list.MENU_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.menus.create',
                config('app.panel_prefix', 'panel').'.menus.edit',
                config('app.panel_prefix', 'panel').'.menus.category-menu.create',
                config('app.panel_prefix', 'panel').'.menus.category-menu.edit',
            ],
        ],

        'page_builder' => [
            'title' => 'Kreator stron',
            'icon' => 'fas fa-laptop-file',
            'url' => route(config('app.panel_prefix', 'panel').'.pages.index'),
            'permissions' => config('permissions_list.PAGE_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.pages.create',
                config('app.panel_prefix', 'panel').'.pages.edit',
            ],
        ],

        'file_manager' => [
            'title' => 'Menedżer plików',
            'icon' => 'icon-folder-alt',
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.images.index',
                config('app.panel_prefix', 'panel').'.images.create',
                config('app.panel_prefix', 'panel').'.images.edit',
                config('app.panel_prefix', 'panel').'.videos.index',
                config('app.panel_prefix', 'panel').'.videos.create',
                config('app.panel_prefix', 'panel').'.videos.edit',
            ],
            'permissions' => [
                config('permissions_list.IMAGE_INDEX_ALL', false),
                config('permissions_list.IMAGE_INDEX_OWN', false),
                config('permissions_list.VIDEO_INDEX_ALL', false),
                config('permissions_list.VIDEO_INDEX_OWN', false),
            ],
            'children' => [
                [
                    'title' => 'Obrazy',
                    'icon' => 'icon-picture',
                    'url' => route(config('app.panel_prefix', 'panel').'.images.index'),
                    'permissions' => [
                        config('permissions_list.IMAGE_INDEX_ALL', false),
                        config('permissions_list.IMAGE_INDEX_OWN', false),
                    ],
                    'active_routes' => [
                        config('app.panel_prefix', 'panel').'.images.create',
                        config('app.panel_prefix', 'panel').'.images.edit',
                    ],
                ],
                [
                    'title' => 'Wideo',
                    'icon' => 'icon-film',
                    'url' => route(config('app.panel_prefix', 'panel').'.videos.index'),
                    'permissions' => [
                        config('permissions_list.VIDEO_INDEX_ALL', false),
                        config('permissions_list.VIDEO_INDEX_OWN', false),
                    ],
                    'active_routes' => [
                        config('app.panel_prefix', 'panel').'.videos.create',
                        config('app.panel_prefix', 'panel').'.videos.edit',
                    ],
                ],
            ],
        ],

        'user' => [
            'title' => 'Użytkownicy',
            'icon' => ' icon-people',
            'url' => route(config('app.panel_prefix', 'panel').'.users.index'),
            'permissions' => config('permissions_list.USER_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.users.create',
                config('app.panel_prefix', 'panel').'.users.edit',
                config('app.panel_prefix', 'panel').'.users.role-assignment',
                config('app.panel_prefix', 'panel').'.users.seo-settings',
            ],
        ],

        'role' => [
            'title' => 'Role i uprawnienia',
            'icon' => 'fas fa-arrow-down-up-lock',
            'url' => route(config('app.panel_prefix', 'panel').'.roles.index'),
            'permissions' => config('permissions_list.ROLE_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.roles.create',
                config('app.panel_prefix', 'panel').'.roles.edit',
            ],
        ],

        'profile' => [
            'title' => 'Profil',
            'icon' => 'icon-user',
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.profile.edit',
                config('app.panel_prefix', 'panel').'.profile.email.change',
                config('app.panel_prefix', 'panel').'.profile.password.change',
                config('app.panel_prefix', 'panel').'.profile.social-networks',
            ],
            'permissions' => [
                config('permissions_list.PROFILE_EDIT', false),
                config('permissions_list.PROFILE_CHANGE_PASSWORD', false),
                config('permissions_list.PROFILE_CHANGE_EMAIL', false),
            ],
            'children' => [
                [
                    'title' => 'Edycja profilu',
                    'icon' => 'icon-note',
                    'url' => route(config('app.panel_prefix', 'panel').'.profile.edit'),
                    'permissions' => config('permissions_list.PROFILE_EDIT', false),
                ],
                [
                    'title' => 'Zmiana hasła',
                    'icon' => 'icon-key',
                    'url' => route(config('app.panel_prefix', 'panel').'.profile.password.change'),
                    'permissions' => config('permissions_list.PROFILE_CHANGE_PASSWORD', false),
                ],
                [
                    'title' => 'Zmiana e-mail',
                    'icon' => 'icon-envelope-letter',
                    'url' => route(config('app.panel_prefix', 'panel').'.profile.email.change'),
                    'permissions' => config('permissions_list.PROFILE_CHANGE_EMAIL', false),
                ],
                [
                    'title' => 'Media społecznościowe',
                    'icon' => 'icon-link',
                    'url' => route(config('app.panel_prefix', 'panel').'.profile.social-networks.edit'),
                    'permissions' => config('permissions_list.PROFILE_SOCIAL_NETWORKS', false),
                ],
            ],
        ],

        'user_activity' => [
            'title' => 'Analityka i ruch',
            'icon' => 'icon-chart',
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.users-track.index',
                config('app.panel_prefix', 'panel').'.requests-track.index',
                config('app.panel_prefix', 'panel').'.requests-track.visits-stats',
            ],
            'permissions' => [
                config('permissions_list.USER_TRACK_INDEX', false),
                config('permissions_list.REQUEST_TRACK_INDEX', false),
            ],
            'children' => [
                [
                    'title' => 'Śledzenie użytkowników',
                    'icon' => 'fas fa-users-viewfinder',
                    'url' => route(config('app.panel_prefix', 'panel').'.users-track.index'),
                    'permissions' => config('permissions_list.USER_TRACK_INDEX', false),
                ],
                [
                    'title' => 'Historia odwiedzin',
                    'icon' => 'fas fa-arrows-to-eye',
                    'url' => route(config('app.panel_prefix', 'panel').'.requests-track.index'),
                    'permissions' => config('permissions_list.REQUEST_TRACK_INDEX', false),
                ],
                [
                    'title' => 'Statystyki odsłon',
                    'icon' => 'icon-eye',
                    'url' => route(config('app.panel_prefix', 'panel').'.requests-track.visits-stats'),
                    'permissions' => config('permissions_list.REQUEST_TRACK_INDEX', false),
                ],
            ],
        ],

        'contact_us' => [
            'title' => 'Kontakt',
            'icon' => 'icon-earphones-alt',
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.contact-us.info.edit',
                config('app.panel_prefix', 'panel').'.contact-us.messages.index',
                config('app.panel_prefix', 'panel').'.contact-us.messages.show',
            ],
            'permissions' => [
                config('permissions_list.CONTACT_INFO', false),
                config('permissions_list.CONTACT_MESSAGES', false),
            ],
            'children' => [
                [
                    'title' => 'Dane kontaktowe',
                    'icon' => 'icon-call-in',
                    'url' => route(config('app.panel_prefix', 'panel').'.contact-us.info.edit'),
                    'permissions' => config('permissions_list.CONTACT_INFO', false),
                ],
                [
                    'title' => 'Wiadomości czytelników',
                    'icon' => 'icon-envelope',
                    'url' => route(config('app.panel_prefix', 'panel').'.contact-us.messages.index'),
                    'permissions' => config('permissions_list.CONTACT_MESSAGES', false),
                    'active_routes' => [
                        config('app.panel_prefix', 'panel').'.contact-us.messages.show',
                    ],
                ],
            ],
        ],

        'ads' => [
            'title' => 'Reklamy i banery',
            'icon' => 'fas fa-bullhorn',
            'url' => route(config('app.panel_prefix', 'panel').'.ads.index'),
            'permissions' => config('permissions_list.AD_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.ads.create',
                config('app.panel_prefix', 'panel').'.ads.edit',
            ],
        ],

        'newsletter' => [
            'title' => 'Newsletter',
            'icon' => 'icon-paper-plane',
            'url' => route(config('app.panel_prefix', 'panel').'.newsletters.index'),
            'permissions' => config('permissions_list.NEWSLETTER_INDEX', false),
        ],

        'redirect' => [
            'title' => 'Przekierowania',
            'icon' => 'fas fa-link',
            'url' => route(config('app.panel_prefix', 'panel').'.redirects.index'),
            'permissions' => config('permissions_list.REDIRECT_INDEX', false),
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.redirects.create',
                config('app.panel_prefix', 'panel').'.redirects.edit',
            ],
        ],

        'setting' => [
            'title' => 'Ustawienia serwisu',
            'icon' => 'icon-settings',
            'active_routes' => [
                config('app.panel_prefix', 'panel').'.settings.site-details.edit',
                config('app.panel_prefix', 'panel').'.settings.social-networks.edit',
                config('app.panel_prefix', 'panel').'.settings.about-us.edit',
                config('app.panel_prefix', 'panel').'.settings.cache-management.index',
            ],
            'permissions' => [
                config('permissions_list.SETTING_SITE_DETAILS', false),
                config('permissions_list.SETTING_SOCIAL_NETWORKS', false),
                config('permissions_list.SETTING_ABOUT_US', false),
                config('permissions_list.CACHE_INDEX', false),
            ],
            'children' => [
                [
                    'title' => 'Informacje o serwisie',
                    'icon' => 'icon-wrench',
                    'url' => route(config('app.panel_prefix', 'panel').'.settings.site-details.edit'),
                    'permissions' => config('permissions_list.SETTING_SITE_DETAILS', false),
                ],
                [
                    'title' => 'Social media',
                    'icon' => 'icon-link',
                    'url' => route(config('app.panel_prefix', 'panel').'.settings.social-networks.edit'),
                    'permissions' => config('permissions_list.SETTING_SOCIAL_NETWORKS', false),
                ],
                [
                    'title' => 'O sŁodziaku',
                    'icon' => 'icon-question',
                    'url' => route(config('app.panel_prefix', 'panel').'.settings.about-us.edit'),
                    'permissions' => config('permissions_list.SETTING_ABOUT_US', false),
                ],
                [
                    'title' => 'Pamięć podręczna (Cache)',
                    'icon' => 'icon-layers',
                    'url' => route(config('app.panel_prefix', 'panel').'.settings.cache-management.index'),
                    'permissions' => config('permissions_list.CACHE_INDEX', false),
                ],
            ],
        ],
    ],
];
