<?php

namespace L5SwaggerExtATC;

use Illuminate\Support\ServiceProvider;
use L5SwaggerExtATC\Console\GenerateDocsCommand;

class L5SwaggerExtATCServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Register commands
        $this->commands([GenerateDocsCommand::class]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('command.l5-swagger-extatc.generate', function ($app) {
            return $app->make(GenerateDocsCommand::class);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @codeCoverageIgnore
     *
     * @return array
     */
    public function provides()
    {
        return [
          'command.l5-swagger-extatc.generate',
        ];
    }
}
