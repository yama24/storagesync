<?php

namespace App\Providers;

use Native\Laravel\Dialog;
use Native\Laravel\Menu\Menu;
use Native\Laravel\Facades\Window;
use Native\Laravel\Facades\MenuBar;
use Native\Laravel\Facades\Notification;
use Native\Laravel\Contracts\ProvidesPhpIni;

class NativeAppServiceProvider implements ProvidesPhpIni
{
    /**
     * Executed once the native application has been booted.
     * Use this method to open windows, register global shortcuts, etc.
     */
    public function boot(): void
    {
        Menu::new()
        // ->appMenu()
        // ->submenu('NativePHP', Menu::new()
        //     ->link('https://nativephp.com', 'Documentation')
        // )
        ->register();

        Window::open()
            ->maximized()
            ->title('Storage Sync');
            // ->hideMenu();
        // MenuBar::create()
        //     ->showDockIcon()
        //     ->label('Storage Sync')
        //     ->route('sync.index')
        //     ->width(500);
        // ->withContextMenu(
        //     Menu::new()
        //         ->label('Storage Sync')
        //         ->separator()
        //         ->link('Settings', 'settings.index')
        //         ->quit()
        // );
        // Dialog::new()
        //     ->hideMenu();

        // Notification::title('Hello from NativePHP')
        //     ->message('This is a detail message coming from your Laravel app.')
        //     ->show();
    }

    /**
     * Return an array of php.ini directives to be set.
     */
    public function phpIni(): array
    {
        return [];
    }
}
