<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Setting;
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
        // Seed default rate limits configurations (ranges for natural publication cadence)
        Setting::set('rate_limits.max_articles_per_day', '7,20', 'string', 'rate_limits');
        Setting::set('rate_limits.max_articles_per_hour', '2,7', 'string', 'rate_limits');
        Setting::set('rate_limits.max_articles_per_category_per_day', '1,5', 'string', 'rate_limits');

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
            ['en' => 'SEO & Marketing', 'es' => 'SEO y Marketing', 'slug_en' => 'seo-marketing', 'slug_es' => 'seo-marketing'],
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

        // Create Premium RSS & Atom Sources
        $rssSources = [
            [
                'name' => 'OpenAI Research Blog',
                'url' => 'https://openai.com/blog/rss.xml',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'Google Research Blog',
                'url' => 'https://research.google/blog/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'Meta AI Blog',
                'url' => 'https://ai.meta.com/blog/rss/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'Hugging Face Papers',
                'url' => 'https://huggingface.co/papers.rss',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'Papers with Code',
                'url' => 'https://paperswithcode.com/latest.rss',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'Bleeping Computer',
                'url' => 'https://www.bleepingcomputer.com/feed/',
                'type' => 'rss',
                'category' => 'Cybersecurity',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'CVE Details',
                'url' => 'https://www.cvedetails.com/rss/last.xml',
                'type' => 'rss',
                'category' => 'Cybersecurity',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'Krebs on Security',
                'url' => 'https://krebsonsecurity.com/feed/',
                'type' => 'rss',
                'category' => 'Cybersecurity',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'Docker Engine Releases',
                'url' => 'https://github.com/docker/engine/releases.atom',
                'type' => 'atom',
                'category' => 'Automation',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'PHP Core Releases',
                'url' => 'https://github.com/php/php-src/releases.atom',
                'type' => 'atom',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'Python Core Releases',
                'url' => 'https://github.com/python/cpython/releases.atom',
                'type' => 'atom',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'Node.js Releases',
                'url' => 'https://github.com/nodejs/node/releases.atom',
                'type' => 'atom',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'Rust Core Releases',
                'url' => 'https://github.com/rust-lang/rust/releases.atom',
                'type' => 'atom',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'Laravel Core Releases',
                'url' => 'https://github.com/laravel/framework/releases.atom',
                'type' => 'atom',
                'category' => 'Automation',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Laravel News',
                'url' => 'https://laravel-news.com/feed',
                'type' => 'rss',
                'category' => 'Automation',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Next.js Releases',
                'url' => 'https://github.com/vercel/next.js/releases.atom',
                'type' => 'atom',
                'category' => 'Tech Industry',
                'frequency' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'Tailwind CSS Releases',
                'url' => 'https://github.com/tailwindlabs/tailwindcss/releases.atom',
                'type' => 'atom',
                'category' => 'Tech Industry',
                'frequency' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'The Verge Tech',
                'url' => 'https://www.theverge.com/rss/tech/index.xml',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'Wired Tech',
                'url' => 'https://www.wired.com/feed/rss',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'Ars Technica',
                'url' => 'https://feeds.arstechnica.com/arstechnica/index',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'TechCrunch AI',
                'url' => 'https://techcrunch.com/category/artificial-intelligence/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 180,
                'is_active' => false,
            ],
            [
                'name' => 'The Verge AI',
                'url' => 'https://www.theverge.com/ai-artificial-intelligence/rss/index.xml',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 180,
                'is_active' => false,
            ],
            [
                'name' => 'MIT Tech Review AI',
                'url' => 'https://www.technologyreview.com/topic/artificial-intelligence/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'TechCrunch General',
                'url' => 'https://techcrunch.com/feed/',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'TechCrunch Startups',
                'url' => 'https://techcrunch.com/category/startups/feed/',
                'type' => 'rss',
                'category' => 'Startups',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'TechCrunch Security',
                'url' => 'https://techcrunch.com/category/security/feed/',
                'type' => 'rss',
                'category' => 'Cybersecurity',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'The Verge General',
                'url' => 'https://www.theverge.com/rss/index.xml',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 30,
                'is_active' => false,
            ],
            [
                'name' => 'VentureBeat',
                'url' => 'https://venturebeat.com/feed/',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'The Register',
                'url' => 'https://www.theregister.com/headlines.atom',
                'type' => 'atom',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'ZDNet Dev',
                'url' => 'https://www.zdnet.com/topic/developer/rss.xml',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'InfoWorld',
                'url' => 'https://www.infoworld.com/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'Hacker Noon',
                'url' => 'https://hackernoon.com/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'Smashing Magazine',
                'url' => 'https://www.smashingmagazine.com/feed/',
                'type' => 'rss',
                'category' => 'Innovation',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'CSS Tricks',
                'url' => 'https://css-tricks.com/feed/',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'A List Apart',
                'url' => 'https://alistapart.com/main/feed/',
                'type' => 'rss',
                'category' => 'Innovation',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'Search Engine Land',
                'url' => 'https://searchengineland.com/feed',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'Search Engine Journal',
                'url' => 'https://www.searchenginejournal.com/feed/',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 180,
                'is_active' => false,
            ],
            [
                'name' => 'Ahrefs Blog',
                'url' => 'https://ahrefs.com/blog/feed/',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 180,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Top Story',
                'url' => 'https://hackernoon.com/tagged/hackernoon-top-story/feed/',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon AI',
                'url' => 'https://hackernoon.com/tagged/ai/feed',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Artificial Intelligence',
                'url' => 'https://hackernoon.com/tagged/artificial-intelligence/feed',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Machine Learning',
                'url' => 'https://hackernoon.com/tagged/machine-learning/feed',
                'type' => 'rss',
                'category' => 'Machine Learning',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Programming',
                'url' => 'https://hackernoon.com/tagged/programming/feed',
                'type' => 'rss',
                'category' => 'Automation',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Software Development',
                'url' => 'https://hackernoon.com/tagged/software-development/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Cybersecurity',
                'url' => 'https://hackernoon.com/tagged/cybersecurity/feed',
                'type' => 'rss',
                'category' => 'Cybersecurity',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Technology',
                'url' => 'https://hackernoon.com/tagged/technology/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Blockchain',
                'url' => 'https://hackernoon.com/tagged/blockchain/feed',
                'type' => 'rss',
                'category' => 'Innovation',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Web Monetization',
                'url' => 'https://hackernoon.com/tagged/web-monetization/feed',
                'type' => 'rss',
                'category' => 'Innovation',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon LLM',
                'url' => 'https://hackernoon.com/tagged/llm/feed',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Google Cloud Platform',
                'url' => 'https://hackernoon.com/tagged/google-cloud-platform/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Google Chrome',
                'url' => 'https://hackernoon.com/tagged/google-chrome/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Google Analytics',
                'url' => 'https://hackernoon.com/tagged/google-analytics/feed',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Google Ads',
                'url' => 'https://hackernoon.com/tagged/google-ads/feed',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Google Cloud',
                'url' => 'https://hackernoon.com/tagged/google-cloud/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Google Maps',
                'url' => 'https://hackernoon.com/tagged/google-maps/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon AWS',
                'url' => 'https://hackernoon.com/tagged/aws/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon AWS S3',
                'url' => 'https://hackernoon.com/tagged/aws-s3/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon VPS',
                'url' => 'https://hackernoon.com/tagged/vps/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon SEO',
                'url' => 'https://hackernoon.com/tagged/seo/feed',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon SEO Tips',
                'url' => 'https://hackernoon.com/tagged/seo-tips/feed',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon SEO Optimization',
                'url' => 'https://hackernoon.com/tagged/seo-optimization/feed',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'HackerNoon Local SEO',
                'url' => 'https://hackernoon.com/tagged/local-seo/feed',
                'type' => 'rss',
                'category' => 'SEO & Marketing',
                'frequency' => 120,
                'is_active' => true,
            ],
        ];

        foreach ($rssSources as $sourceData) {
            $sourceData['max_age_days'] = 1;
            $sourceData['trusted'] = true;
            Source::create($sourceData);
        }
    }
}