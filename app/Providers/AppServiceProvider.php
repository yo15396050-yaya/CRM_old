<?php

namespace App\Providers;

use App\Models\Company;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */

    public function register()
    {
        Cashier::ignoreMigrations();
        Sanctum::ignoreMigrations();

        if (config('app.redirect_https')) {
            $this->app['request']->server->set('HTTPS', true);
        }
    }

    public function boot()
    {
        Cashier::useCustomerModel(Company::class);

        if (config('app.redirect_https')) {
            URL::forceScheme('https');
        }

        Schema::defaultStringLength(191);

        if (app()->environment('development')) {
            $this->app->register(IdeHelperServiceProvider::class);
        }

        // Garde-fou de developpement : si MAIL_REDIRECT_TO est renseigne dans
        // le .env, TOUS les mails sortants sont rediriges vers cette seule
        // adresse, quels que soient leurs destinataires reels. Permet de tester
        // la chaine d'envoi complete sans ecrire aux clients ni aux
        // collaborateurs. La variable est absente en production : la condition
        // est alors fausse et le comportement normal s'applique.
        $redirection = env('MAIL_REDIRECT_TO');

        if (!empty($redirection)) {
            \Illuminate\Support\Facades\Mail::alwaysTo($redirection);
        }

        CarbonInterval::macro('formatHuman', function ($totalMinutes, $seconds = false): string {

            if ($seconds) {
                return static::seconds($totalMinutes)->cascade()->forHumans(['short' => true, 'options' => 0]);
                /** @phpstan-ignore-line */
            }

            return static::minutes($totalMinutes)->cascade()->forHumans(['short' => true, 'options' => 0]);
            /** @phpstan-ignore-line */
        });

        //        Model::preventLazyLoading(app()->environment('development'));

    }

}
