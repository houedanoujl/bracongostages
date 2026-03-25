<?php

namespace App\Providers;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class FilamentServiceProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => [
                    50 => '#fff7ed',
                    100 => '#ffedd5',
                    200 => '#fed7aa',
                    300 => '#fdba74',
                    400 => '#fb923c',
                    500 => '#f97316', // Orange BRACONGO
                    600 => '#ea580c',
                    700 => '#c2410c',
                    800 => '#9a3412',
                    900 => '#7c2d12',
                    950 => '#431407',
                ],
            ])
            ->resources([
                \App\Filament\Resources\CandidatureResource::class,
                \App\Filament\Resources\EtablissementResource::class,
                \App\Filament\Resources\NiveauEtudeResource::class,
                \App\Filament\Resources\DirectionResource::class,
                \App\Filament\Resources\PosteResource::class,

                // \App\Filament\Resources\UserResource::class, // Temporairement désactivé
                // \App\Filament\Resources\DocumentResource::class, // Temporairement désactivé
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
                \App\Filament\Pages\Messagerie::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\ConfigurationOverviewWidget::class,
                Widgets\AccountWidget::class,
            ])
            ->middleware([
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
            ->brandName('BRACONGO Admin')
            ->favicon(asset('favicon.ico'))
            ->sidebarCollapsibleOnDesktop()
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): HtmlString => new HtmlString(
                    '<style>' . file_get_contents(resource_path('css/filament-workflow.css')) . '</style>'
                ),
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): HtmlString => new HtmlString("
                    <script>
                        document.addEventListener('livewire:navigated', () => {
                            if (window.location.pathname.match(/\\/admin\\/candidatures\\/[0-9]+\\/edit/)) {
                                const sidebar = document.querySelector('[x-data]');
                                if (sidebar && sidebar.__x) {
                                    sidebar.__x.\$data.isOpen = false;
                                }
                                document.querySelectorAll('aside.fi-sidebar').forEach(el => {
                                    el.closest('[x-data]')?.__x?.\$dispatch('collapse-sidebar');
                                });
                                // Forcer via le store Filament
                                if (window.Alpine && window.Alpine.store) {
                                    try { Alpine.store('sidebar').close(); } catch(e) {}
                                }
                            }
                        });
                    </script>
                "),
            );
    }
} 