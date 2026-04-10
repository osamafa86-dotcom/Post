<?php

namespace App\Traits;

use Faker\Factory;

trait HasMultilingualSeeder
{
    /**
     * Check if multilingual system is enabled
     */
    protected function isMultilingualEnabled(): bool
    {
        return config('app.active_languages_system', false);
    }

    /**
     * Get available languages from config
     */
    protected function getLanguages(): array
    {
        return array_keys(config('app.languages', ['ar' => 'Arabic']));
    }

    /**
     * Get website locale for non-multilingual mode
     */
    protected function getWebsiteLocale(): string
    {
        return config('app.website_locale', 'ar');
    }

    /**
     * Generate multilingual data using Faker
     */
    protected function generateMultilingualData(array $fields): array
    {
        if ($this->isMultilingualEnabled()) {
            return $this->generateDataForMultipleLanguages($fields);
        }

        return $this->generateDataForSingleLanguage($fields);
    }

    /**
     * Generate data for multiple languages (multilingual mode)
     */
    protected function generateDataForMultipleLanguages(array $fields): array
    {
        $languages = $this->getLanguages();
        $data = [];

        foreach ($languages as $locale) {
            $faker = $this->createFakerForLocale($locale);
            $data[$locale] = $this->generateFieldsData($faker, $fields, $locale);
        }

        return $data;
    }

    /**
     * Generate data for single language (non-multilingual mode)
     */
    protected function generateDataForSingleLanguage(array $fields): array
    {
        $locale = $this->getWebsiteLocale();
        $faker = $this->createFakerForLocale($locale);

        return [
            $locale => $this->generateFieldsData($faker, $fields, $locale)
        ];
    }

    /**
     * Create Faker instance for specific locale
     */
    protected function createFakerForLocale(string $locale)
    {
        $fakerLocale = $this->mapToFakerLocale($locale);
        return Factory::create($fakerLocale);
    }

    /**
     * Map application locale to Faker locale
     */
    protected function mapToFakerLocale(string $locale): string
    {
        return match($locale) {
            'ar' => 'ar_SA',
            'en' => 'en_US',
            'fr' => 'fr_FR',
            'es' => 'es_ES',
            'de' => 'de_DE',
            'it' => 'it_IT',
            'tr' => 'tr_TR',
            'ru' => 'ru_RU',
            'zh' => 'zh_CN',
            'ja' => 'ja_JP',
            'ko' => 'ko_KR',
            default => 'en_US',
        };
    }

    /**
     * Generate data for all fields using Faker
     */
    protected function generateFieldsData($faker, array $fields, string $locale): array
    {
        $data = [];

        foreach ($fields as $field => $type) {
            $data[$field] = $this->generateFieldValue($faker, $type, $field);
        }

        // Add lang to the data
        $data['lang'] = $locale;

        return $data;
    }

    /**
     * Generate value for specific field type
     */
    protected function generateFieldValue($faker, string $type, string $fieldName): mixed
    {
        return match($type) {
            // Text types
            'title' => $faker->sentence(rand(3, 6)),
            'name' => $faker->name(),
            'description' => $faker->paragraph(rand(2, 4)),
            'content' => $faker->paragraphs(rand(3, 6), true),
            'excerpt' => $faker->paragraph(rand(1, 2)),
            'short_text' => $faker->sentence(rand(6, 10)),
            'summary' => $faker->paragraph(rand(1, 3)),

            // Job and professional types
            'job_title' => $faker->jobTitle(),
            'company' => $faker->company(),
            'profession' => $faker->word(),

            // Meta types
            'meta_title' => $faker->sentence(rand(3, 5)),
            'meta_description' => $faker->sentence(rand(8, 12)),
            'meta_keywords' => implode(', ', $faker->words(rand(4, 8))),

            // SEO & URL types
            'slug' => $faker->slug(),
            'url' => $faker->url(),

            // Contact types
            'subject' => $faker->sentence(rand(3, 5)),
            'message' => $faker->paragraphs(rand(2, 4), true),

            // Product types
            'product_name' => $faker->words(rand(2, 3), true),

            // Default fallback
            default => $faker->sentence(4),
        };
    }
    /**
     * Quick method for common title & description fields
     */
    protected function generateTitleAndDescription(): array
    {
        return $this->generateMultilingualData([
            'title' => 'title',
            'description' => 'description'
        ]);
    }

    /**
     * Quick method for post-like content
     */
    protected function generatePostContent(): array
    {
        return $this->generateMultilingualData([
            'title' => 'title',
            'content' => 'content',
            'excerpt' => 'excerpt',
            'meta_title' => 'meta_title',
            'meta_description' => 'meta_description'
        ]);
    }

    protected function generateProductContent(): array
    {
        return $this->generateMultilingualData([
            'name' => 'product_name',
            'description' => 'description',
            'short_description' => 'short_text',
            'meta_title' => 'meta_title',
            'meta_description' => 'meta_description'
        ]);
    }
}
