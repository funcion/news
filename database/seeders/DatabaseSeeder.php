<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Source;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user (Luis Figuera)
        $bioEs = "¡Hola! Soy Luis Figuera. Me especializo en escribir textos digitales y tradicionales, asegurando que cada palabra cumpla un objetivo comercial.\nConecto marcas con audiencias a través de mensajes claros, atractivos y adaptados a cualquier formato o canal de comunicación.\n\nMis especialidades:\n- Redacción de todo tipo de contenidos: Artículos de blog, textos web (copywriting), publicaciones para redes sociales, newsletters y guiones.\n- Optimización SEO: Estructuro textos basados en la intención de búsqueda para posicionar tu web orgánicamente en Google.\n- Redacción SEM: Creo copys persuasivos de alto impacto para campañas de anuncios pagados en Google Ads.\n- Estrategias de comunicación: Adapto el tono de voz de tu marca según el canal y el público objetivo.\n\n## Mi filosofía\n\nNo existen temas difíciles, sino textos mal enfocados. Mi meta es transformar ideas complejas en mensajes sencillos que informen, eduquen y generen confianza en el usuario.";

        $bioEn = "Hello! I'm Luis Figuera. I specialize in writing digital and traditional copy, ensuring that every word serves a commercial goal.\nI connect brands with audiences through clear, engaging messages tailored to any format or communication channel.\n\nMy specialties:\n- Copywriting & Content Creation: Blog posts, web copy, social media updates, newsletters, and scripts.\n- SEO Optimization: Structuring content based on search intent to rank organically on Google.\n- SEM Copywriting: Crafting high-impact, persuasive copy for Google Ads campaigns.\n- Communication Strategy: Adapting brand voice and tone across channels and target audiences.\n\n## My Philosophy\n\nThere are no difficult topics, only poorly focused copy. My goal is to turn complex ideas into simple, clear messages that inform, educate, and build trust with readers.";

        User::create([
            'name' => [
                'en' => 'Luis Figuera',
                'es' => 'Luis Figuera',
            ],
            'email' => 'admin@glodaxia.com',
            'password' => bcrypt('password'),
            'slug' => 'luis-figuera',
            'bio' => [
                'en' => $bioEn,
                'es' => $bioEs,
            ],
            'is_active' => true,
        ]);

        // Create Categories
        $categories = [
            ['en' => 'Artificial Intelligence', 'es' => 'Inteligencia Artificial', 'slug_en' => 'artificial-intelligence', 'slug_es' => 'inteligencia-artificial'],
            ['en' => 'Machine Learning', 'es' => 'Aprendizaje Automático', 'slug_en' => 'machine-learning', 'slug_es' => 'aprendizaje-automatico'],
            ['en' => 'Startups', 'es' => 'Startups', 'slug_en' => 'startups', 'slug_es' => 'startups'],
            ['en' => 'Cybersecurity', 'es' => 'Ciberseguridad', 'slug_en' => 'cybersecurity', 'slug_es' => 'ciberseguridad'],
            ['en' => 'Tech Industry', 'es' => 'Industria Tech', 'slug_en' => 'tech-industry', 'slug_es' => 'industria-tech'],
            ['en' => 'Science', 'es' => 'Ciencia', 'slug_en' => 'science', 'slug_es' => 'ciencia'],
            ['en' => 'Innovation', 'es' => 'Innovación', 'slug_en' => 'innovation', 'slug_es' => 'innovacion'],
            ['en' => 'Automation', 'es' => 'Automatización', 'slug_en' => 'automation', 'slug_es' => 'automatizacion'],
        ];

        foreach ($categories as $catData) {
            Category::create([
                'name' => ['en' => $catData['en'], 'es' => $catData['es']],
                'slug_en' => $catData['slug_en'],
                'slug_es' => $catData['slug_es'],
                'is_active' => true,
            ]);
        }

        // Create Featured/Sample Tags
        $tags = [
            ['en' => 'OpenAI', 'es' => 'OpenAI'],
            ['en' => 'GPT', 'es' => 'GPT'],
            ['en' => 'Neural Networks', 'es' => 'Redes Neuronales'],
            ['en' => 'Deep Learning', 'es' => 'Deep Learning'],
            ['en' => 'Robotics', 'es' => 'Robótica'],
            ['en' => 'Data Science', 'es' => 'Ciencia de Datos'],
            ['en' => 'Cloud Computing', 'es' => 'Cloud Computing'],
            ['en' => 'Blockchain', 'es' => 'Blockchain'],
        ];

        foreach ($tags as $tagData) {
            Tag::create([
                'name' => ['en' => $tagData['en'], 'es' => $tagData['es']],
                'slug' => strtolower(str_replace(' ', '-', $tagData['en'])),
                'is_featured' => true,
                'article_count' => 0,
            ]);
        }

        // Create Sample RSS Sources
        $rssSources = [
            [
                'name' => 'TechCrunch AI',
                'url' => 'https://techcrunch.com/category/artificial-intelligence/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'OpenAI Blog',
                'url' => 'https://openai.com/blog/feed.xml',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'The Verge AI',
                'url' => 'https://www.theverge.com/ai-artificial-intelligence/rss/index.xml',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'MIT Tech Review AI',
                'url' => 'https://www.technologyreview.com/topic/artificial-intelligence/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
                'is_active' => true,
            ],
        ];

        foreach ($rssSources as $sourceData) {
            Source::create($sourceData);
        }
    }
}