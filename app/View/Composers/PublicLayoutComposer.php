<?php

namespace App\View\Composers;

use App\Models\Profile;
use App\Models\Setting;
use App\Models\User;
use App\Services\ThemeManager;
use Illuminate\View\View;

class PublicLayoutComposer
{
    public function __construct(private ThemeManager $themeManager) {}

    public function compose(View $view): void
    {
        $profile = Profile::first();
        $user = User::first();
        $themeSlug = $this->themeManager->activeSlug();
        $themeMode = $this->themeManager->activeMode();

        $features = [
            'blog'      => (bool) Setting::get('features.blog_enabled', true),
            'reservas'  => (bool) Setting::get('features.reservas_enabled', true),
            'faq'       => (bool) Setting::get('features.faq_enabled', true),
            'servicios' => (bool) Setting::get('features.servicios_enabled', true),
            'sobre_mi'  => (bool) Setting::get('features.sobre_mi_enabled', true),
        ];

        $social = [
            'facebook'  => (string) Setting::get('social.facebook', ''),
            'instagram' => (string) Setting::get('social.instagram', ''),
            'linkedin'  => (string) Setting::get('social.linkedin', ''),
            'twitter'   => (string) Setting::get('social.twitter', ''),
            'youtube'   => (string) Setting::get('social.youtube', ''),
            'tiktok'    => (string) Setting::get('social.tiktok', ''),
            'whatsapp'  => (string) Setting::get('social.whatsapp', ''),
        ];

        $view->with([
            'profile'   => $profile,
            'user'      => $user,
            'themeSlug' => $themeSlug,
            'themeMode' => $themeMode,
            'features'  => $features,
            'social'    => $social,
        ]);
    }
}
