<?php

namespace Database\Seeders;

use App\Enums\LinkStatusEnum;
use App\Enums\LinkUrlTargetEnum;
use App\Models\Icon;
use App\Models\NavbarLink;
use App\Traits\HasMultilingualSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NavbarLinkSeeder extends Seeder
{
    use HasMultilingualSeeder;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $icons = Icon::pluck('id', 'icon_path')->toArray();
        $defaultIcon = Icon::first()->id ?? null;

        if ($this->isMultilingualEnabled()) {
            $this->createMultilingualNavbar($icons, $defaultIcon);
        } else {
            $this->createSingleLanguageNavbar($icons, $defaultIcon);
        }
    }

    protected function createMultilingualNavbar(array $icons, $defaultIcon): void
    {
        $languages = $this->getLanguages();

        foreach ($languages as $locale) {
            $this->createNavbarForLocale($locale, $icons, $defaultIcon);
        }
    }

    protected function createSingleLanguageNavbar(array $icons, $defaultIcon): void
    {
        $locale = $this->getWebsiteLocale();
        $this->createNavbarForLocale($locale, $icons, $defaultIcon);
    }

    protected function createNavbarForLocale(string $locale, array $icons, $defaultIcon): void
    {
        // الروابط الرئيسية
        $mainLinks = [
            ['type' => 'home', 'url' => '/', 'icon' => $icons['fa-solid fa-house'] ?? $defaultIcon],
            ['type' => 'news', 'url' => '/news', 'icon' => $icons['fa-solid fa-newspaper'] ?? $defaultIcon],
            ['type' => 'podcast', 'url' => '/podcasts', 'icon' => $icons['fa-solid fa-podcast'] ?? $defaultIcon],
            ['type' => 'video', 'url' => '/videos', 'icon' => $icons['fa-solid fa-video'] ?? $defaultIcon],
            ['type' => 'tags', 'url' => '/tags', 'icon' => $icons['fa-solid fa-tags'] ?? $defaultIcon],
            ['type' => 'contact', 'url' => '/contact', 'icon' => $icons['fa-solid fa-link'] ?? $defaultIcon],
        ];

        $createdParents = [];

        foreach ($mainLinks as $link) {
            $linkName = $this->getTranslatedName($link['type'], $locale);

            $parent = NavbarLink::firstOrCreate(
                ['link_name' => $linkName, 'lang' => $locale],
                [
                    'link_url' => $link['url'],
                    'link_open' => LinkUrlTargetEnum::SAME_WINDOW->value,
                    'link_status' => LinkStatusEnum::CUSTOM_LINK->value,
                    'icon_id' => $link['icon'],
                    'parent_id' => null,
                    'team_id' => null,
                    'lang' => $locale, // تخزين اللغة
                ]
            );

            $createdParents[$link['type']] = $parent;

            // إنشاء أقسام الأخبار الفرعية
            if ($link['type'] === 'news') {
                $this->createNewsSections($parent, $locale, $icons, $defaultIcon);
            }
        }
    }

    protected function getTranslatedName(string $type, string $locale): string
    {
        $translations = [
            'home' => ['ar' => 'الرئيسية', 'en' => 'Home'],
            'news' => ['ar' => 'الأخبار', 'en' => 'News'],
            'podcast' => ['ar' => 'البودكاست', 'en' => 'Podcasts'],
            'video' => ['ar' => 'الفيديو', 'en' => 'Videos'],
            'tags' => ['ar' => 'الوسوم', 'en' => 'Tags'],
            'contact' => ['ar' => 'اتصل بنا', 'en' => 'Contact'],
            'politics' => ['ar' => 'السياسة', 'en' => 'Politics'],
            'sports' => ['ar' => 'الرياضة', 'en' => 'Sports'],
            'technology' => ['ar' => 'التكنولوجيا', 'en' => 'Technology'],
            'entertainment' => ['ar' => 'الترفيه', 'en' => 'Entertainment'],
            'health' => ['ar' => 'الصحة', 'en' => 'Health'],
            'business' => ['ar' => 'الأعمال', 'en' => 'Business'],
            'science' => ['ar' => 'العلوم', 'en' => 'Science'],
            'culture' => ['ar' => 'الثقافة', 'en' => 'Culture'],
        ];

        return $translations[$type][$locale] ?? $translations[$type]['en'] ?? $type;
    }

    protected function createNewsSections(NavbarLink $parent, string $locale, array $icons, $defaultIcon): void
    {
        $sections = ['politics', 'sports', 'technology', 'entertainment', 'health', 'business', 'science', 'culture'];

        foreach ($sections as $section) {
            $linkName = $this->getTranslatedName($section, $locale);

            NavbarLink::firstOrCreate(
                ['link_name' => $linkName, 'parent_id' => $parent->id, 'lang' => $locale],
                [
                    'link_url' => "/news/{$section}",
                    'link_open' => LinkUrlTargetEnum::SAME_WINDOW->value,
                    'link_status' => LinkStatusEnum::CUSTOM_LINK->value,
                    'icon_id' => $icons['fa-solid fa-link'] ?? $defaultIcon,
                    'parent_id' => $parent->id,
                    'team_id' => null,
                    'lang' => $locale, // تخزين اللغة
                ]
            );
        }
    }
}
