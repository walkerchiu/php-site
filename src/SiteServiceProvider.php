<?php

namespace WalkerChiu\Site;

use Illuminate\Support\ServiceProvider;
use WalkerChiu\Site\Providers\EventServiceProvider;

class SiteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
        $this->app['router']->aliasMiddleware('wkSiteEnable' , config('wk-core.class.site.verifyEnable'));

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
           __DIR__ .'/config/site.php' => config_path('wk-site.php'),
        ], 'config');

        // Publish migration files
        $from = __DIR__ .'/database/migrations/';
        $to   = database_path('migrations') .'/';
        $this->publishes([
            $from .'create_wk_site_table.php'
                => $to .date('Y_m_d_His', time()) .'_create_wk_site_table.php'
        ], 'migrations');

        $this->loadViewsFrom(__DIR__.'/views', 'php-site');
        $this->publishes([
           __DIR__.'/views' => resource_path('views/vendor/php-site'),
        ]);

        $this->loadTranslationsFrom(__DIR__.'/translations', 'php-site');
        $this->publishes([
            __DIR__.'/translations' => resource_path('lang/vendor/php-site'),
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                config('wk-site.command.cleaner'),
                config('wk-site.command.initializer')
            ]);
        }

        config('wk-core.class.site.site')::observe(config('wk-core.class.site.siteObserver'));
        config('wk-core.class.site.siteLang')::observe(config('wk-core.class.site.siteLangObserver'));
        config('wk-core.class.site.email')::observe(config('wk-core.class.site.emailObserver'));
        config('wk-core.class.site.emailLang')::observe(config('wk-core.class.site.emailLangObserver'));
        config('wk-core.class.site.layout')::observe(config('wk-core.class.site.layoutObserver'));
        config('wk-core.class.site.layoutLang')::observe(config('wk-core.class.site.layoutLangObserver'));
    }

    /**
     * Register the blade directives
     *
     * @return void
     */
    private function bladeDirectives()
    {
    }

    /**
     * Merges user's and package's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        if (!config()->has('wk-site')) {
            $this->mergeConfigFrom(
                __DIR__ .'/config/site.php', 'wk-site'
            );
        }

        $this->mergeConfigFrom(
            __DIR__ .'/config/site.php', 'site'
        );
    }

    /**
     * Merge the given configuration with the existing configuration.
     *
     * @param String  $path
     * @param String  $key
     * @return void
     */
    protected function mergeConfigFrom($path, $key)
    {
        if (
            !(
                $this->app instanceof CachesConfiguration
                && $this->app->configurationIsCached()
            )
        ) {
            $config = $this->app->make('config');
            $content = $config->get($key, []);

            $config->set($key, array_merge(
                require $path, $content
            ));
        }
    }
}
