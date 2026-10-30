<?php

namespace App\Providers;

use App\Models\User;
use Database\Seeders\PresupuestosMasterDataSeeder;
use Database\Seeders\ReparacionesMasterDataSeeder;
use Database\Seeders\RepuestosMasterDataSeeder;
use Database\Seeders\TurnosMasterDataSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Native\Desktop\Contracts\ProvidesPhpIni;
use Native\Desktop\Facades\Window;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        $this->seedDesktopDemoIfNecessary();

        Window::open()
            ->title('AutoTech S.A.')
            ->width(1360)
            ->height(850)
            ->minWidth(1024)
            ->minHeight(700)
            ->rememberState()
            ->hideMenu()
            ->backgroundColor('#f4f7f7');
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }

    private function seedDesktopDemoIfNecessary(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        if (! User::query()->exists()) {
            Artisan::call('db:seed', ['--force' => true]);

            return;
        }

        foreach ([
            TurnosMasterDataSeeder::class,
            PresupuestosMasterDataSeeder::class,
            RepuestosMasterDataSeeder::class,
            ReparacionesMasterDataSeeder::class,
        ] as $seeder) {
            Artisan::call('db:seed', [
                '--class' => $seeder,
                '--force' => true,
            ]);
        }
    }
}
