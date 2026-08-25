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

                                // Create SuperAdmin User
        User::updateOrCreate([
            'email' => 'admin@glodaxia.com',
        ], [
            'name' => [
                'en' => 'Administrator',
                'es' => 'Administrador',
            ],
            'slug' => 'admin',
            'password' => bcrypt('password'),
            'bio' => [
                'en' => 'System Administrator for Glodaxia Platform.',
                'es' => 'Administrador del sistema de Glodaxia.',
            ],
            'is_active' => true,
        ]);

        // Create Editorial Team & Freelance Writers (12 Human Authors with Unique Creative Voices)
        $authorsList = config('global.editorial.authors', []);
        
        if (empty($authorsList)) {
            $authorsList = [
                [
                    'name' => 'Luis Figuera',
                    'slug' => 'luis-figuera',
                    'email' => 'luis.figuera@glodaxia.com',
                    'bio' => [
                        'es' => '¡Hola! Soy Luis. Llevo desde 2009 creando aplicaciones web, móviles y de escritorio como desarrollador independiente. Me fascina desmenuzar cómo el código moderno y la inteligencia artificial están revolucionando la forma en que construimos software hoy en día.',
                        'en' => 'Hi! I\'m Luis. I\'ve been building web, mobile, and desktop apps as an independent developer since 2009. I\'m fascinated by breaking down how modern code and artificial intelligence are revolutionizing software development today.',
                    ],
                ],
                [
                    'name' => 'Meudys Vásquez',
                    'slug' => 'meudys-vasquez',
                    'email' => 'meudys.vasquez@glodaxia.com',
                    'bio' => [
                        'es' => '¡Qué tal! Soy Meudys. Vengo del mundo de la docencia y siempre he creído que la mejor tecnología es la que se entiende sin complicaciones. Escribo como redactora freelance para explorar cómo las herramientas digitales y la IA pueden hacer el aprendizaje mucho más inspirador y cercano.',
                        'en' => 'Hey! I\'m Meudys. Coming from the world of teaching, I\'ve always believed the best technology is the kind that\'s easy to grasp. I write as a freelancer to explore how digital tools and AI can make learning much more engaging and inspiring.',
                    ],
                ],
                [
                    'name' => 'Leudys Vásquez',
                    'slug' => 'leudys-vasquez',
                    'email' => 'leudys.vasquez@glodaxia.com',
                    'bio' => [
                        'es' => '¡Hola a todos! Mi nombre es Leudys. Como ingeniera industrial, mi mente siempre busca simplificar flujos y eliminar cuellos de botella. Colaboro como redactora independiente compartiendo análisis sobre automatización inteligente, herramientas de productividad y soluciones que ahorran horas de trabajo.',
                        'en' => 'Hello everyone! My name is Leudys. As an industrial engineer, my mind is wired to streamline workflows and remove bottlenecks. I write as a freelance analyst covering smart automation, productivity tools, and solutions that save hours of work.',
                    ],
                ],
                [
                    'name' => 'Wilmer Rivas',
                    'slug' => 'wilmer-rivas',
                    'email' => 'wilmer.rivas@glodaxia.com',
                    'bio' => [
                        'es' => '¡Un saludo! Soy Wilmer. Combino mi formación en ingeniería industrial con el análisis de datos para entender el pulso real de los mercados. Me encanta redactar artículos que conecten la logística, la cadena de suministro y los avances tecnológicos con historias cotidianas y comprensibles.',
                        'en' => 'Greetings! I\'m Wilmer. I blend industrial engineering with data analytics to understand market dynamics. I love writing articles that connect logistics, supply chain tech, and industry breakthroughs with clear, relatable storytelling.',
                    ],
                ],
                [
                    'name' => 'Mariaurys González',
                    'slug' => 'mariaurys-gonzalez',
                    'email' => 'mariaurys.gonzalez@glodaxia.com',
                    'bio' => [
                        'es' => '¡Hola! Soy Mariaurys. Mi pasión es la administración y el crecimiento de negocios en la era digital. Como redactora freelance, me concentro en analizar cómo el comercio electrónico, las nuevas plataformas financieras y la innovación tecnológica transforman las empresas del mañana.',
                        'en' => 'Hi! I\'m Mariaurys. My passion lies in business administration and digital-age growth. As a freelance writer, I focus on analyzing how e-commerce, new fintech platforms, and tech innovation are shaping tomorrow\'s businesses.',
                    ],
                ],
                [
                    'name' => 'Jesús Millán',
                    'slug' => 'jesus-millan',
                    'email' => 'jesus.millan@glodaxia.com',
                    'bio' => [
                        'es' => '¡Hola! Por aquí Jesús. Paso gran parte de mi tiempo rastreando novedades en internet, comunidades tech y cultura digital. Escribo de forma fresca y sin rodeos sobre esos gadgets curiosos, tendencias virales y noticias tecnológicas que realmente vale la pena conocer.',
                        'en' => 'Hi there! Jesús here. I spend most of my days exploring internet trends, tech communities, and digital culture. I write in a fresh, candid style about interesting gadgets, viral trends, and tech news that are genuinely worth your time.',
                    ],
                ],
                [
                    'name' => 'Andrea García',
                    'slug' => 'andrea-garcia',
                    'email' => 'andrea.garcia@glodaxia.com',
                    'bio' => [
                        'es' => '¡Hola a todos! Soy Andrea. Desde la ingeniería química me apasiona descubrir lo que no se ve a simple vista: nanotecnología, nuevos materiales y biotecnología. Como redactora freelance, me entusiasma divulgar cómo la ciencia y la tecnología se unen para crear un futuro más sostenible.',
                        'en' => 'Hey everyone! I\'m Andrea. As a chemical engineer, I love exploring what lies beyond the visible: nanotechnology, novel materials, and biotech. As a freelance writer, I\'m passionate about sharing how science and tech unite to build a greener future.',
                    ],
                ],
                [
                    'name' => 'Ediliana Figuera',
                    'slug' => 'ediliana-figuera',
                    'email' => 'ediliana.figuera@glodaxia.com',
                    'bio' => [
                        'es' => '¡Hola! Mi nombre es Ediliana. Ejerzo como médico cirujano y sigo con enorme entusiasmo la evolución de la salud digital. Escribo artículos independientes para explicar con rigor humano cómo los algoritmos diagnósticos, los avances biomédicos y la IA están mejorando la atención hospitalaria.',
                        'en' => 'Hello! My name is Ediliana. I practice as a surgeon and closely follow the evolution of digital health. I write independent articles to explain, with human warmth and clinical rigor, how diagnostic algorithms, biomedical breakthroughs, and AI are enhancing patient care.',
                    ],
                ],
                [
                    'name' => 'Braniam Figuera',
                    'slug' => 'braniam-figuera',
                    'email' => 'braniam.figuera@glodaxia.com',
                    'bio' => [
                        'es' => '¡Saludos! Soy Braniam. Vivir la medicina como cirujano me ha demostrado el impacto vital que tienen los instrumentos de precisión. Como redactor freelance, me gusta contar de primera mano hacia dónde van la telemedicina, los implantes biomédicos y las tecnologías que están salvando vidas a diario.',
                        'en' => 'Greetings! I\'m Braniam. Practicing medicine as a surgeon has shown me the life-saving impact of precision tech. As a freelance writer, I love sharing first-hand insights on where telemedicine, biomedical devices, and cutting-edge healthcare tech are headed.',
                    ],
                ],
                [
                    'name' => 'César Márquez',
                    'slug' => 'cesar-marquez',
                    'email' => 'cesar.marquez@glodaxia.com',
                    'bio' => [
                        'es' => '¡Hola! Soy César. Me apasiona el universo de las startups y la ola del desarrollo no-code. Como redactor freelancer, disfruto analizando cómo creadores de todo el mundo están lanzando productos increíbles y automatizaciones complejas sin necesidad de escribir una sola línea de código.',
                        'en' => 'Hi! I\'m César. I\'m passionate about the startup ecosystem and the rise of no-code platforms. As a freelance writer, I enjoy analyzing how creators worldwide launch incredible digital products and smart automations without writing a single line of code.',
                    ],
                ],
                [
                    'name' => 'Roger Figuera',
                    'slug' => 'roger-figuera',
                    'email' => 'roger.figuera@glodaxia.com',
                    'bio' => [
                        'es' => '¡Qué tal! Soy Roger. Veo la tecnología a través del prisma del diseño visual y la experiencia de usuario. Como redactor freelance, me encanta hablar sobre cómo la tipografía, las interfaces limpias y el buen diseño UI/UX convierten productos complejos en herramientas intuitivas y placenteras.',
                        'en' => 'Hey there! I\'m Roger. I look at technology through the lens of visual design and user experience. As a freelance writer, I love discussing how typography, clean interfaces, and thoughtful UI/UX turn complex tools into intuitive, delightful products.',
                    ],
                ],
                [
                    'name' => 'Robert Figuera',
                    'slug' => 'robert-figuera',
                    'email' => 'robert.figuera@glodaxia.com',
                    'bio' => [
                        'es' => '¡Hola a todos! Soy Robert. Como saxofonista y apasionado del sonido, vivo explorando la frontera donde la música se encuentra con la tecnología. Escribo como freelancer sobre sintetizadores, software de producción musical, acústica digital y cómo los algoritmos modernos están reinventando el arte sonoro.',
                        'en' => 'Hi everyone! I\'m Robert. As a saxophonist and sound enthusiast, I live at the intersection of music and technology. I write as a freelancer about synths, music production software, digital acoustics, and how modern algorithms are reinventing the sonic arts.',
                    ],
                ],
            ];
        }

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
