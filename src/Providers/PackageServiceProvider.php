<?php

namespace Telefunction\Support\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Telefunction\Support\Enums\PackageSeparator;
use Telefunction\Support\Traits\Concerns\ResolvesPackageNames;

abstract class PackageServiceProvider extends ServiceProvider
{
    use ResolvesPackageNames;

    protected static string $path = __DIR__;

    /**
     * @var array<int, class-string>
     */
    protected array $commands = [];

    final public function register(): void
    {
        $this->mergePackageConfig();

        $this->extraRegister();
    }

    final public function boot(): void
    {
        $this->publishPackageConfig();

        $this->loadPackageMigrations();
        $this->loadPackageRoutes();
        $this->loadPackageViews();
        $this->loadPackageTranslations();

        $this->registerPackageCommands();

        $this->extraBoot();
    }

    final public static function packageBasePath(string ...$paths): string
    {
        return implode('/', [static::$path, '..', ...$paths]);
    }

    final public static function configPath(): string
    {
        $root = static::packageBasePath('config');

        return static::packageIdentifier(PackageSeparator::Path, root: $root).'.php';
    }

    final public static function publishedConfigPath(): string
    {
        return config_path(static::packageIdentifier(PackageSeparator::Path).'.php');
    }

    final public static function migrationsPath(): string
    {
        return static::packageBasePath('database', 'migrations');
    }

    final public static function routesPath(string $file): string
    {
        return static::packageBasePath('routes', "$file.php");
    }

    final public static function viewsPath(): string
    {
        return static::packageBasePath('resources', 'views');
    }

    final public static function translationsPath(): string
    {
        return static::packageBasePath('resources', 'lang');
    }

    private function mergePackageConfig(): void
    {
        if (is_file($this->configPath())) {
            $this->mergeConfigFrom(
                static::configPath(),
                static::packageIdentifier(PackageSeparator::Key)
            );
        }
    }

    private function publishPackageConfig(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        if (! is_file(static::configPath())) {
            return;
        }

        $this->publishes([
            static::configPath() => static::publishedConfigPath(),
        ], static::packageIdentifier(PackageSeparator::Slug, segments: ['config']));
    }

    private function loadPackageMigrations(): void
    {
        if (is_dir(static::migrationsPath())) {
            $this->loadMigrationsFrom(static::migrationsPath());
        }
    }

    private function loadPackageRoutes(): void
    {
        if (is_file(static::routesPath('api'))) {
            Route::middleware('api')
                ->prefix('api')
                ->group(static::routesPath('api'));
        }

        if (is_file(static::routesPath('web'))) {
            Route::middleware('web')
                ->group(static::routesPath('web'));
        }
    }

    private function loadPackageViews(): void
    {
        if (is_dir(static::viewsPath())) {
            $this->loadViewsFrom(
                static::viewsPath(),
                static::packageIdentifier(PackageSeparator::Slug)
            );
        }
    }

    private function loadPackageTranslations(): void
    {
        if (is_dir(static::translationsPath())) {
            $this->loadTranslationsFrom(
                static::translationsPath(),
                static::packageIdentifier(PackageSeparator::Slug)
            );
        }
    }

    private function registerPackageCommands(): void
    {
        if ($this->app->runningInConsole() && $this->commands !== []) {
            $this->commands($this->commands);
        }
    }

    protected function extraRegister(): void
    {
        //
    }

    protected function extraBoot(): void
    {
        //
    }
}
