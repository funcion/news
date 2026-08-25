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

                // Create Editorial Team & Freelance Writers (12 Human Authors in First Person)
        $authorsList = config('global.editorial.authors', []);
        foreach ($authorsList as $authorData) {
            User::updateOrCreate([
                'slug' => $authorData['slug'],
            ], [
                'name' => [
                    'en' => $authorData['name'],
                    'es' => $authorData['name'],
                ],
                'email' => $authorData['email'] ?? ($authorData['slug'] . '@glodaxia.com'),
                'password' => bcrypt('password'),
                'bio' => [
                    'en' => $authorData['bio']['en'] ?? '',
                    'es' => $authorData['bio']['es'] ?? '',
                ],
                'is_active' => true,
            ]);
        }

        // Create Categories (Deduplicated with updateOrCreate)
        $categories = [
            [
                'en' => 'Artificial Intelligence',
                'es' => 'Inteligencia Artificial',
                'slug_en' => 'artificial-intelligence',
                'slug_es' => 'inteligencia-artificial',
            ],
            [
                'en' => 'Web & Backend Development',
                'es' => 'Desarrollo Web & Backend',
                'slug_en' => 'web-backend-development',
                'slug_es' => 'desarrollo-web-backend',
            ],
            [
                'en' => 'Mobile Development',
                'es' => 'Desarrollo Móvil',
                'slug_en' => 'mobile-development',
                'slug_es' => 'desarrollo-movil',
            ],
            [
                'en' => 'Cybersecurity',
                'es' => 'Ciberseguridad',
                'slug_en' => 'cybersecurity',
                'slug_es' => 'ciberseguridad',
            ],
            [
                'en' => 'Cloud & DevOps',
                'es' => 'Cloud & DevOps',
                'slug_en' => 'cloud-devops',
                'slug_es' => 'cloud-devops',
            ],
            [
                'en' => 'Databases & Data Engineering',
                'es' => 'Bases de Datos & Data',
                'slug_en' => 'databases-data-engineering',
                'slug_es' => 'bases-de-datos-data',
            ],
            [
                'en' => 'Hardware & Gadgets',
                'es' => 'Hardware & Gadgets',
                'slug_en' => 'hardware-gadgets',
                'slug_es' => 'hardware-gadgets',
            ],
            [
                'en' => 'Web Design & UX/UI',
                'es' => 'Diseño Web & UX/UI',
                'slug_en' => 'web-design-ux-ui',
                'slug_es' => 'diseno-web-ux-ui',
            ],
            [
                'en' => 'SEO & Digital Marketing',
                'es' => 'SEO & Marketing Digital',
                'slug_en' => 'seo-digital-marketing',
                'slug_es' => 'seo-marketing-digital',
            ],
            [
                'en' => 'Startups & Business',
                'es' => 'Startups & Negocios',
                'slug_en' => 'startups-business',
                'slug_es' => 'startups-negocios',
            ],
            [
                'en' => 'Open Source & Linux',
                'es' => 'Open Source & Linux',
                'slug_en' => 'open-source-linux',
                'slug_es' => 'open-source-linux',
            ],
            [
                'en' => 'Cryptocurrency & Web3',
                'es' => 'Criptomonedas & Web3',
                'slug_en' => 'crypto-web3',
                'slug_es' => 'criptomonedas-web3',
            ],
            [
                'en' => 'FinTech & Digital Economy',
                'es' => 'FinTech & Economía Digital',
                'slug_en' => 'fintech-digital-economy',
                'slug_es' => 'fintech-economia-digital',
            ],
            [
                'en' => 'Gaming & 3D Tech',
                'es' => 'Videojuegos & Tecnología 3D',
                'slug_en' => 'gaming-3d-tech',
                'slug_es' => 'videojuegos-tecnologia-3d',
            ],
            [
                'en' => 'Science & Innovation',
                'es' => 'Ciencia & Innovación',
                'slug_en' => 'science-innovation',
                'slug_es' => 'ciencia-innovacion',
            ],
            [
                'en' => 'IoT & Smart Home',
                'es' => 'IoT & Domótica',
                'slug_en' => 'iot-smart-home',
                'slug_es' => 'iot-domotica',
            ],
            [
                'en' => 'No-Code & Automation',
                'es' => 'No-Code & Automatización',
                'slug_en' => 'nocode-automation',
                'slug_es' => 'nocode-automatizacion',
            ],
            [
                'en' => 'Virtual Reality & Spatial Computing',
                'es' => 'Realidad Virtual & Computación Espacial',
                'slug_en' => 'vr-spatial-computing',
                'slug_es' => 'vr-computacion-espacial',
            ],
            [
                'en' => 'ClimateTech & Clean Energy',
                'es' => 'ClimaTech & Energía Limpia',
                'slug_en' => 'climatech-clean-energy',
                'slug_es' => 'climatech-energia-limpia',
            ],
            [
                'en' => 'Tech Career & Productivity',
                'es' => 'Carrera Tech & Productividad',
                'slug_en' => 'tech-career-productivity',
                'slug_es' => 'carrera-tech-productividad',
            ],
            [
                'en' => 'Servers, Hosting & Infrastructure',
                'es' => 'Servidores & Hosting',
                'slug_en' => 'servers-hosting-infrastructure',
                'slug_es' => 'servidores-hosting-infraestructura',
            ],
            [
                'en' => 'E-Commerce & Digital Retail',
                'es' => 'E-Commerce & Tiendas Digitales',
                'slug_en' => 'ecommerce-digital-retail',
                'slug_es' => 'comercio-electronico-tiendas',
            ],
            [
                'en' => 'SaaS & Developer Tools',
                'es' => 'SaaS & Herramientas DevTools',
                'slug_en' => 'saas-developer-tools',
                'slug_es' => 'saas-herramientas-desarrollo',
            ],
            [
                'en' => 'Generative AI & Media',
                'es' => 'IA Generativa & Medios',
                'slug_en' => 'generative-ai-media',
                'slug_es' => 'ia-generativa-medios',
            ],
            [
                'en' => 'Telecom & Networking',
                'es' => 'Telecomunicaciones & Redes',
                'slug_en' => 'telecom-networking',
                'slug_es' => 'telecomunicaciones-redes',
            ],
            [
                'en' => 'Ethical Hacking & Pentesting',
                'es' => 'Hacking Ético & Pentesting',
                'slug_en' => 'ethical-hacking-pentesting',
                'slug_es' => 'hacking-etico-pentesting',
            ],
            [
                'en' => 'LegalTech & AI Regulation',
                'es' => 'LegalTech & Regulación Digital',
                'slug_en' => 'legaltech-regulation',
                'slug_es' => 'derecho-tecnologico-regulacion',
            ],
            [
                'en' => 'Tech Education & Tutorials',
                'es' => 'Educación Tech & Tutoriales',
                'slug_en' => 'tech-education-tutorials',
                'slug_es' => 'educacion-tech-tutoriales',
            ],
            [
                'en' => 'Robotics & Embedded Systems',
                'es' => 'Robótica & Sistemas Embebidos',
                'slug_en' => 'robotics-embedded-systems',
                'slug_es' => 'robotica-sistemas-embebidos',
            ],
            [
                'en' => '3D Printing & Maker Culture',
                'es' => 'Impresión 3D & Cultura Maker',
                'slug_en' => '3d-printing-maker-culture',
                'slug_es' => 'impresion-3d-cultura-maker',
            ],
            [
                'en' => 'Electronics & DIY Hardware',
                'es' => 'Electrónica & Hardware DIY',
                'slug_en' => 'electronics-diy-hardware',
                'slug_es' => 'electronica-hardware-diy',
            ],
        ];

        foreach ($categories as $catData) {
            Category::updateOrCreate(
                ['slug_en' => $catData['slug_en']],
                [
                    'name' => ['en' => $catData['en'], 'es' => $catData['es']],
                    'slug_es' => $catData['slug_es'],
                    'is_active' => true,
                ]
            );
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
            $slug = strtolower(str_replace(' ', '-', $tagData['en']));
            Tag::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => ['en' => $tagData['en'], 'es' => $tagData['es']],
                    'is_featured' => true,
                ]
            );
        }

                // Create Premium Curated RSS & Atom Sources (Tier 1 High-Signal & Filtered)
        $rssSources = [
            // --- AI & Machine Learning (Tier 1) ---
            [
                'name' => 'OpenAI Tech Releases',
                'url' => 'https://openai.com/news/rss.xml',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
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
            [
                'name' => 'TechCrunch AI',
                'url' => 'https://techcrunch.com/category/artificial-intelligence/feed/',
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
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Google Research Blog',
                'url' => 'https://research.google/blog/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Meta AI Blog',
                'url' => 'https://ai.meta.com/blog/rss/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Hugging Face Papers',
                'url' => 'https://huggingface.co/papers.rss',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => true,
            ],

            // --- Cybersecurity (Tier 1) ---
            [
                'name' => 'Bleeping Computer',
                'url' => 'https://www.bleepingcomputer.com/feed/',
                'type' => 'rss',
                'category' => 'Cybersecurity',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Krebs on Security',
                'url' => 'https://krebsonsecurity.com/feed/',
                'type' => 'rss',
                'category' => 'Cybersecurity',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'TechCrunch Security',
                'url' => 'https://techcrunch.com/category/security/feed/',
                'type' => 'rss',
                'category' => 'Cybersecurity',
                'frequency' => 60,
                'is_active' => true,
            ],

            // --- Tech Industry, Hardware & Startups (Tier 1) ---
            [
                'name' => 'The Verge Tech',
                'url' => 'https://www.theverge.com/rss/tech/index.xml',
                'type' => 'rss',
                'category' => 'Hardware & Gadgets',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Ars Technica',
                'url' => 'https://feeds.arstechnica.com/arstechnica/index',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Wired Tech',
                'url' => 'https://www.wired.com/feed/rss',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'TechCrunch Startups',
                'url' => 'https://techcrunch.com/category/startups/feed/',
                'type' => 'rss',
                'category' => 'Startups & Business',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'VentureBeat',
                'url' => 'https://venturebeat.com/feed/',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'The Register',
                'url' => 'https://www.theregister.com/headlines.atom',
                'type' => 'atom',
                'category' => 'Tech Industry',
                'frequency' => 60,
                'is_active' => true,
            ],

            // --- SEO, Search & Digital Marketing (Tier 1) ---
            [
                'name' => 'Search Engine Land',
                'url' => 'https://searchengineland.com/feed',
                'type' => 'rss',
                'category' => 'SEO & Digital Marketing',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Search Engine Journal',
                'url' => 'https://www.searchenginejournal.com/feed/',
                'type' => 'rss',
                'category' => 'SEO & Digital Marketing',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Ahrefs Blog',
                'url' => 'https://ahrefs.com/blog/feed/',
                'type' => 'rss',
                'category' => 'SEO & Digital Marketing',
                'frequency' => 120,
                'is_active' => true,
            ],

            // --- Software Development & Official Framework Releases (Tier 1) ---
            [
                'name' => 'Next.js Releases',
                'url' => 'https://github.com/vercel/next.js/releases.atom',
                'type' => 'atom',
                'category' => 'Web & Backend Development',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Laravel News',
                'url' => 'https://laravel-news.com/feed',
                'type' => 'rss',
                'category' => 'Web & Backend Development',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Laravel Core Releases',
                'url' => 'https://github.com/laravel/framework/releases.atom',
                'type' => 'atom',
                'category' => 'Web & Backend Development',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Tailwind CSS Releases',
                'url' => 'https://github.com/tailwindlabs/tailwindcss/releases.atom',
                'type' => 'atom',
                'category' => 'Web Design & UX/UI',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Node.js Releases',
                'url' => 'https://github.com/nodejs/node/releases.atom',
                'type' => 'atom',
                'category' => 'Web & Backend Development',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Smashing Magazine',
                'url' => 'https://www.smashingmagazine.com/feed/',
                'type' => 'rss',
                'category' => 'Web Design & UX/UI',
                'frequency' => 120,
                'is_active' => true,
            ],

            // --- Curated Community Sources (High-Signal Feeds Only) ---
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

            // --- Inactive / Noisy / Low-Signal Feeds (Disabled by default) ---
            ['name' => 'HackerNoon Machine Learning', 'url' => 'https://hackernoon.com/tagged/machine-learning/feed', 'type' => 'rss', 'category' => 'Artificial Intelligence', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Programming', 'url' => 'https://hackernoon.com/tagged/programming/feed', 'type' => 'rss', 'category' => 'Web & Backend Development', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Software Development', 'url' => 'https://hackernoon.com/tagged/software-development/feed', 'type' => 'rss', 'category' => 'Web & Backend Development', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Cybersecurity', 'url' => 'https://hackernoon.com/tagged/cybersecurity/feed', 'type' => 'rss', 'category' => 'Cybersecurity', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Technology', 'url' => 'https://hackernoon.com/tagged/technology/feed', 'type' => 'rss', 'category' => 'Tech Industry', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Blockchain', 'url' => 'https://hackernoon.com/tagged/blockchain/feed', 'type' => 'rss', 'category' => 'Cryptocurrency & Web3', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Web Monetization', 'url' => 'https://hackernoon.com/tagged/web-monetization/feed', 'type' => 'rss', 'category' => 'FinTech & Digital Economy', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon LLM', 'url' => 'https://hackernoon.com/tagged/llm/feed', 'type' => 'rss', 'category' => 'Artificial Intelligence', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Google Cloud Platform', 'url' => 'https://hackernoon.com/tagged/google-cloud-platform/feed', 'type' => 'rss', 'category' => 'Cloud & DevOps', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Google Chrome', 'url' => 'https://hackernoon.com/tagged/google-chrome/feed', 'type' => 'rss', 'category' => 'Tech Industry', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Google Analytics', 'url' => 'https://hackernoon.com/tagged/google-analytics/feed', 'type' => 'rss', 'category' => 'SEO & Digital Marketing', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Google Ads', 'url' => 'https://hackernoon.com/tagged/google-ads/feed', 'type' => 'rss', 'category' => 'SEO & Digital Marketing', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Google Cloud', 'url' => 'https://hackernoon.com/tagged/google-cloud/feed', 'type' => 'rss', 'category' => 'Cloud & DevOps', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Google Maps', 'url' => 'https://hackernoon.com/tagged/google-maps/feed', 'type' => 'rss', 'category' => 'Tech Industry', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon AWS', 'url' => 'https://hackernoon.com/tagged/aws/feed', 'type' => 'rss', 'category' => 'Cloud & DevOps', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon AWS S3', 'url' => 'https://hackernoon.com/tagged/aws-s3/feed', 'type' => 'rss', 'category' => 'Cloud & DevOps', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon VPS', 'url' => 'https://hackernoon.com/tagged/vps/feed', 'type' => 'rss', 'category' => 'Servers, Hosting & Infrastructure', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon SEO', 'url' => 'https://hackernoon.com/tagged/seo/feed', 'type' => 'rss', 'category' => 'SEO & Digital Marketing', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon SEO Tips', 'url' => 'https://hackernoon.com/tagged/seo-tips/feed', 'type' => 'rss', 'category' => 'SEO & Digital Marketing', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon SEO Optimization', 'url' => 'https://hackernoon.com/tagged/seo-optimization/feed', 'type' => 'rss', 'category' => 'SEO & Digital Marketing', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Local SEO', 'url' => 'https://hackernoon.com/tagged/local-seo/feed', 'type' => 'rss', 'category' => 'SEO & Digital Marketing', 'frequency' => 120, 'is_active' => false],
            ['name' => 'HackerNoon Artificial Intelligence', 'url' => 'https://hackernoon.com/tagged/artificial-intelligence/feed', 'type' => 'rss', 'category' => 'Artificial Intelligence', 'frequency' => 120, 'is_active' => false],
            ['name' => 'Papers with Code', 'url' => 'https://paperswithcode.com/latest.rss', 'type' => 'rss', 'category' => 'Artificial Intelligence', 'frequency' => 120, 'is_active' => false],
            ['name' => 'CVE Details', 'url' => 'https://www.cvedetails.com/rss/last.xml', 'type' => 'rss', 'category' => 'Cybersecurity', 'frequency' => 30, 'is_active' => false],
            ['name' => 'Docker Engine Releases', 'url' => 'https://github.com/docker/engine/releases.atom', 'type' => 'atom', 'category' => 'Cloud & DevOps', 'frequency' => 120, 'is_active' => false],
            ['name' => 'PHP Core Releases', 'url' => 'https://github.com/php/php-src/releases.atom', 'type' => 'atom', 'category' => 'Web & Backend Development', 'frequency' => 120, 'is_active' => false],
            ['name' => 'Python Core Releases', 'url' => 'https://github.com/python/cpython/releases.atom', 'type' => 'atom', 'category' => 'Web & Backend Development', 'frequency' => 120, 'is_active' => false],
            ['name' => 'Rust Core Releases', 'url' => 'https://github.com/rust-lang/rust/releases.atom', 'type' => 'atom', 'category' => 'Web & Backend Development', 'frequency' => 120, 'is_active' => false],
            ['name' => 'CSS Tricks', 'url' => 'https://css-tricks.com/feed/', 'type' => 'rss', 'category' => 'Web Design & UX/UI', 'frequency' => 120, 'is_active' => false],
            ['name' => 'A List Apart', 'url' => 'https://alistapart.com/main/feed/', 'type' => 'rss', 'category' => 'Web Design & UX/UI', 'frequency' => 120, 'is_active' => false],
            ['name' => 'TechCrunch General', 'url' => 'https://techcrunch.com/feed/', 'type' => 'rss', 'category' => 'Tech Industry', 'frequency' => 60, 'is_active' => false],
            ['name' => 'The Verge General', 'url' => 'https://www.theverge.com/rss/index.xml', 'type' => 'rss', 'category' => 'Tech Industry', 'frequency' => 60, 'is_active' => false],
            ['name' => 'ZDNet Dev', 'url' => 'https://www.zdnet.com/topic/developer/rss.xml', 'type' => 'rss', 'category' => 'Web & Backend Development', 'frequency' => 60, 'is_active' => false],
            ['name' => 'InfoWorld', 'url' => 'https://www.infoworld.com/feed', 'type' => 'rss', 'category' => 'Tech Industry', 'frequency' => 60, 'is_active' => false],
            ['name' => 'Hacker Noon', 'url' => 'https://hackernoon.com/feed', 'type' => 'rss', 'category' => 'Tech Industry', 'frequency' => 60, 'is_active' => false],
        ];

        foreach ($rssSources as $sourceData) {
            $sourceData['max_age_days'] = 1;
            $sourceData['trusted'] = true;
            Source::updateOrCreate(
                ['url' => $sourceData['url']],
                $sourceData
            );
        }
        }
}
