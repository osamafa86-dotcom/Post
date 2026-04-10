<?php

namespace Database\Factories;

use App\Enums\ImageSizeTypeEnum;
use App\Enums\NewsTypeEnum;
use App\Enums\PublishEnum;
use App\Models\Files;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ar_title = [
            'كيفية تحسين تجربة المستخدم في المواقع الإلكترونية',
            'أهم استراتيجيات التسويق الرقمي لعام 2024',
            'دليل شامل لبناء تطبيقات الويب باستخدام Laravel',
            'أفضل الممارسات لتطوير واجهات المستخدم',
            'كيف تبدأ مشروعك الخاص في التجارة الإلكترونية',
            'أحدث تقنيات الذكاء الاصطناعي وتأثيرها على الأعمال',
            'دورة تدريبية لتعلم تطوير تطبيقات الهاتف المحمول',
            'استراتيجيات النجاح في العمل الحر على الإنترنت',
            'التحديات التي تواجه الشركات الناشئة في العالم العربي',
            'كيف تحافظ على أمان بياناتك الشخصية على الإنترنت'
        ];
        $title = fake()->randomElement($ar_title);
        return [
            'publish_date' => fake()->date(),
            'order_number' => fake()->numberBetween(1, 100),
            'title' => $title,
            'slug' => Str::slug($title),
            'image_alt' => 'صورة توضيحية للموضوع',
            'video_url' => null,
            'description' => 'هذا وصف مختصر للموضوع يتحدث عن المحتوى بإيجاز.',
            'body' => 'هذا هو النص الكامل للموضوع الذي يتناول بالتفصيل جوانب متعددة من العنوان المذكور. هنا يمكنك الحديث عن الموضوع بشكل شامل، مع تضمين معلومات دقيقة وأمثلة عملية لجعل المحتوى أكثر جاذبية وفائدة للقارئ.',
            'user_id' => fake()->numberBetween(1, 10),
            'views' => fake()->numberBetween(1, 1000),
            'updates' => fake()->numberBetween(1, 100),
            'news_type' => NewsTypeEnum::cases()[array_rand(NewsTypeEnum::cases())]->value,
            'image_size' => ImageSizeTypeEnum::cases()[array_rand(ImageSizeTypeEnum::cases())]->value,
            'publish_status' => PublishEnum::PUBLISHED->value,
        ];
    }
}
