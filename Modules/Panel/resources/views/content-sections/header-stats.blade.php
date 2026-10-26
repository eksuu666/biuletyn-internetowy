@can(config('permissions_list.STATS_HEADER', false))
    <div class="col-md-12">
        <div class="row">
            <div class="col-lg-3 col-12">
                <div class="stat-box use-cyan shadow">
                    <a>
                        <div class="stat">
                            <div class="counter-down" data-value="{{ $dataCounts['users_count'] }}"></div>
                            <div class="h3">Użytkownicy</div>
                        </div><!-- /.stat -->
                        <div class="visual">
                            <i class="icon-people"></i>
                        </div><!-- /.visual -->
                    </a>
                </div><!-- /.stat-box -->
            </div><!-- /.col-lg-3 -->
            <div class="col-lg-3 col-12">
                <div class="stat-box use-blue shadow">
                    <a>
                        <div class="stat">
                            <div class="counter-down" data-value="{{ $dataCounts['articles_count'] }}"></div>
                            <div class="h3">Artykuły</div>
                        </div><!-- /.stat -->
                        <div class="visual">
                            <i class="icon-globe"></i>
                        </div><!-- /.visual -->
                    </a>
                </div><!-- /.stat-box -->
            </div><!-- /.col-lg-3 -->
            <div class="col-lg-3 col-12">
                <div class="stat-box use-green shadow">
                    <a>
                        <div class="stat">
                            <div class="counter-down" data-value="{{ $dataCounts['categories_count'] }}"></div>
                            <div class="h3">Kategorie</div>
                        </div><!-- /.stat -->
                        <div class="visual">
                            <i class="icon-grid"></i>
                        </div><!-- /.visual -->
                    </a>
                </div><!-- /.stat-box -->
            </div><!-- /.col-lg-3 -->

            <div class="col-lg-3 col-12">
                <div class="stat-box use-rose shadow">
                    <a>
                        <div class="stat">
                            <div class="counter-down" data-value="{{ $visitsCount['all'] }}"></div>
                            <div class="h3">Wszystkie odsłony</div>
                        </div><!-- /.stat -->
                        <div class="visual">
                            <i class="icon-eye"></i>
                        </div><!-- /.visual -->
                    </a>
                </div><!-- /.stat-box -->
            </div><!-- /.col-lg-3 -->

            <div class="col-lg-3 col-12">
                <div class="stat-box use-purple shadow">
                    <a>
                        <div class="stat">
                            <div class="counter-down" data-value="{{ $visitorsCount['all'] }}"></div>
                            <div class="h3">Odwiedzający</div>
                        </div><!-- /.stat -->
                        <div class="visual">
                            <i class="fas fa-users-viewfinder"></i>
                        </div><!-- /.visual -->
                    </a>
                </div><!-- /.stat-box -->
            </div><!-- /.col-lg-3 -->

            <div class="col-lg-3 col-12">
                <div class="stat-box use-purple shadow">
                    <a>
                        <div class="stat">
                            <div class="counter-down" data-value="{{ $visitorsCount['member'] }}"></div>
                            <div class="h3">Zalogowani</div>
                        </div><!-- /.stat -->
                        <div class="visual">
                            <i class="far fa-face-smile"></i>
                        </div><!-- /.visual -->
                    </a>
                </div><!-- /.stat-box -->
            </div><!-- /.col-lg-3 -->

            <div class="col-lg-3 col-12">
                <div class="stat-box use-purple shadow">
                    <a>
                        <div class="stat">
                            <div class="counter-down" data-value="{{ $visitorsCount['guest'] }}"></div>
                            <div class="h3">Goście</div>
                        </div><!-- /.stat -->
                        <div class="visual">
                            <i class="fas fa-face-smile"></i>
                        </div><!-- /.visual -->
                    </a>
                </div><!-- /.stat-box -->
            </div><!-- /.col-lg-3 -->

            <div class="col-lg-3 col-12">
                <div class="stat-box use-red shadow">
                    <a>
                        <div class="stat">
                            <div class="counter-down" data-value="{{ $articlesVisitsCount['all'] }}"></div>
                            <div class="h3">Odsłony artykułów</div>
                        </div><!-- /.stat -->
                        <div class="visual">
                            <i class="icon-eyeglass"></i>
                        </div><!-- /.visual -->
                    </a>
                </div><!-- /.stat-box -->
            </div><!-- /.col-lg-3 -->
        </div><!-- /.row -->
    </div><!-- /.col-md-12 -->
@endcan
