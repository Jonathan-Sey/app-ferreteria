<?php

namespace App\Providers\Filament;

use App\Filament\Resources\SucursalResource\Pages\CambiarSucursal;
use App\Filament\Resources\UserResource\Widgets\StatsOverview;
use App\Filament\Widgets\VentasChart;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Pages\Settings;
use App\Filament\Resources\ClienteResource\Widgets\BlogPostsChart;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->brandName('FerreteriaLaPaz')
            // ->topNavigation()
            ->sidebarCollapsibleOnDesktop()
            ->id('admin')
            ->path('admin')
            ->login()
            ->plugins([
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),
                \Hasnayeen\Themes\ThemesPlugin::make(),
            ])
            // ->registration()
            ->passwordReset()
            ->emailVerification()
            ->profile()
            ->colors([
                'primary' => Color::Green,
                'secondary' => Color::Gray,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                CambiarSucursal::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                StatsOverview::class,
                VentasChart::class, // Nuevo widget de gráfico de ventas
                BlogPostsChart::class,
                // Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                \Hasnayeen\Themes\Http\Middleware\SetTheme::class,
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->navigationItems([
                // NavigationItem::make('Analytics')
                //     ->url('https://filament.pirsch.io', shouldOpenInNewTab: true)
                //     ->icon('heroicon-o-presentation-chart-line')
                //     ->group('Reports')
                //     ->sort(3),
                NavigationItem::make('dashboard')
                    ->label(fn(): string => __('filament-panels::pages/dashboard.title'))
                    ->url(fn(): string => Dashboard::getUrl())
                    ->isActiveWhen(fn() => request()->routeIs('filament.admin.pages.dashboard')),
                // ...
            ])
            ->userMenuItems([
                // MenuItem::make()
                //     ->label('Settings')
                //     ->url('https://demo.microsystemplus2.com/')
                //     ->icon('heroicon-o-cog-6-tooth'),
                // ...
            ])
            ->font('Poppins')
            
        ;
    }
}