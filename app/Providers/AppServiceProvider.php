<?php

namespace App\Providers;

use App\Services\Contracts\FileStorageServiceProvider;
use App\Services\Contracts\ImportServiceProvider;
use App\Services\FileStorageService;
use App\Services\RowImportService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ImportServiceProvider::class, RowImportService::class);
        $this->app->bind(FileStorageServiceProvider::class, FileStorageService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        DB::listen(function(\Illuminate\Database\Events\QueryExecuted $query) {
//            if ($this->app->environment('local')) {
//                File::append(
//                    storage_path('/logs/query.log'),
//                    sprintf('[%s][%f] %s [%s]%s%s', Carbon::now()->format('Y-m-d H:i:s'), $query->time, $query->sql, implode(', ', $query->bindings), PHP_EOL, PHP_EOL)
//                );
//            }
//        });
    }
}
