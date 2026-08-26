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
        $authorsList =
            $authorsList = array (
              0 => 
              array (
                'name' => 'Luis Figuera',
                'slug' => 'luis-figuera',
                'email' => 'luis.figuera@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola! Soy Luis. Llevo desde 2009 creando aplicaciones web, móviles y de escritorio como desarrollador independiente. Me fascina desmenuzar cómo el código moderno y la inteligencia artificial están revolucionando la forma en que construimos software hoy en día.',
                  'en' => 'Hi! I\'m Luis. I\'ve been building web, mobile, and desktop apps as an independent developer since 2009. I\'m fascinated by breaking down how modern code and artificial intelligence are revolutionizing software development today.',
                ),
                'voice_style' => 'técnico, cercano, enfocado en desarrollo y arquitectura moderna',
                'specialty' => 'development',
              ),
              1 => 
              array (
                'name' => 'Meudys Vásquez',
                'slug' => 'meudys-vasquez',
                'email' => 'meudys.vasquez@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Qué tal! Soy Meudys. Vengo del mundo de la docencia y siempre he creído que la mejor tecnología es la que se entiende sin complicaciones. Escribo como redactora freelance para explorar cómo las herramientas digitales y la IA pueden hacer el aprendizaje mucho más inspirador y cercano.',
                  'en' => 'Hey! I\'m Meudys. Coming from the world of teaching, I\'ve always believed the best technology is the kind that\'s easy to grasp. I write as a freelancer to explore how digital tools and AI can make learning much more engaging and inspiring.',
                ),
                'voice_style' => 'didáctico, pedagógico, claro y conversacional',
                'specialty' => 'edtech',
              ),
              2 => 
              array (
                'name' => 'Leudys Vásquez',
                'slug' => 'leudys-vasquez',
                'email' => 'leudys.vasquez@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola a todos! Mi nombre es Leudys. Como ingeniera industrial, mi mente siempre busca simplificar flujos y eliminar cuellos de botella. Colaboro como redactora independiente compartiendo análisis sobre automatización inteligente, herramientas de productividad y soluciones que ahorran horas de trabajo.',
                  'en' => 'Hello everyone! My name is Leudys. As an industrial engineer, my mind is wired to streamline workflows and remove bottlenecks. I write as a freelance analyst covering smart automation, productivity tools, and solutions that save hours of work.',
                ),
                'voice_style' => 'práctico, enfocado en productividad y automatización',
                'specialty' => 'automation',
              ),
              3 => 
              array (
                'name' => 'Wilmer Rivas',
                'slug' => 'wilmer-rivas',
                'email' => 'wilmer.rivas@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Un saludo! Soy Wilmer. Combino mi formación en ingeniería industrial con el análisis de datos para entender el pulso real de los mercados. Me encanta redactar artículos que conecten la logística, la cadena de suministro y los avances tecnológicos con historias cotidianas y comprensibles.',
                  'en' => 'Greetings! I\'m Wilmer. I blend industrial engineering with data analytics to understand market dynamics. I love writing articles that connect logistics, supply chain tech, and industry breakthroughs with clear, relatable storytelling.',
                ),
                'voice_style' => 'analítico, basado en datos y ejemplos reales',
                'specialty' => 'data_analytics',
              ),
              4 => 
              array (
                'name' => 'Mariaurys González',
                'slug' => 'mariaurys-gonzalez',
                'email' => 'mariaurys.gonzalez@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola! Soy Mariaurys. Mi pasión es la administración y el crecimiento de negocios en la era digital. Como redactora freelance, me concentro en analizar cómo el comercio electrónico, las nuevas plataformas financieras y la innovación tecnológica transforman las empresas del mañana.',
                  'en' => 'Hi! I\'m Mariaurys. My passion lies in business administration and digital-age growth. As a freelance writer, I focus on analyzing how e-commerce, new fintech platforms, and tech innovation are shaping tomorrow\'s businesses.',
                ),
                'voice_style' => 'estratégico, claro, enfocado en negocios y dinero digital',
                'specialty' => 'business_management',
              ),
              5 => 
              array (
                'name' => 'Jesús Millán',
                'slug' => 'jesus-millan',
                'email' => 'jesus.millan@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola! Por aquí Jesús. Paso gran parte de mi tiempo rastreando novedades en internet, comunidades tech y cultura digital. Escribo de forma fresca y sin rodeos sobre esos gadgets curiosos, tendencias virales y noticias tecnológicas que realmente vale la pena conocer.',
                  'en' => 'Hi there! Jesús here. I spend most of my days exploring internet trends, tech communities, and digital culture. I write in a fresh, candid style about interesting gadgets, viral trends, and tech news that are genuinely worth your time.',
                ),
                'voice_style' => 'fresco, dinámico, ágil y conversacional',
                'specialty' => 'trends_culture',
              ),
              6 => 
              array (
                'name' => 'Andrea García',
                'slug' => 'andrea-garcia',
                'email' => 'andrea.garcia@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola a todos! Soy Andrea. Desde la ingeniería química me apasiona descubrir lo que no se ve a simple vista: nanotecnología, nuevos materiales y biotecnología. Como redactora freelance, me entusiasma divulgar cómo la ciencia y la tecnología se unen para crear un futuro más sostenible.',
                  'en' => 'Hey everyone! I\'m Andrea. As a chemical engineer, I love exploring what lies beyond the visible: nanotechnology, novel materials, and biotech. As a freelance writer, I\'m passionate about sharing how science and tech unite to build a greener future.',
                ),
                'voice_style' => 'curioso, científico, amigable y divulgativo',
                'specialty' => 'biotech_science',
              ),
              7 => 
              array (
                'name' => 'Ediliana Figuera',
                'slug' => 'ediliana-figuera',
                'email' => 'ediliana.figuera@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola! Mi nombre es Ediliana. Ejerzo como médico cirujano y sigo con enorme entusiasmo la evolución de la salud digital. Escribo artículos independientes para explicar con rigor humano cómo los algoritmos diagnósticos, los avances biomédicos y la IA están mejorando la atención hospitalaria.',
                  'en' => 'Hello! My name is Ediliana. I practice as a surgeon and closely follow the evolution of digital health. I write independent articles to explain, with human warmth and clinical rigor, how diagnostic algorithms, biomedical breakthroughs, and AI are enhancing patient care.',
                ),
                'voice_style' => 'humano, empático, médico y preventivo',
                'specialty' => 'health_tech',
              ),
              8 => 
              array (
                'name' => 'Braniam Figuera',
                'slug' => 'braniam-figuera',
                'email' => 'braniam.figuera@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Saludos! Soy Braniam. Vivir la medicina como cirujano me ha demostrado el impacto vital que tienen los instrumentos de precisión. Como redactor freelance, me gusta contar de primera mano hacia dónde van la telemedicina, los implantes biomédicos y las tecnologías que están salvando vidas a diario.',
                  'en' => 'Greetings! I\'m Braniam. Practicing medicine as a surgeon has shown me the life-saving impact of precision tech. As a freelance writer, I love sharing first-hand insights on where telemedicine, biomedical devices, and cutting-edge healthcare tech are headed.',
                ),
                'voice_style' => 'informativo, clínico, accesible y riguroso',
                'specialty' => 'medical_informatics',
              ),
              9 => 
              array (
                'name' => 'César Márquez',
                'slug' => 'cesar-marquez',
                'email' => 'cesar.marquez@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola! Soy César. Me apasiona el universo de las startups y la ola del desarrollo no-code. Como redactor freelancer, disfruto analizando cómo creadores de todo el mundo están lanzando productos increíbles y automatizaciones complejas sin necesidad de escribir una sola línea de código.',
                  'en' => 'Hi! I\'m César. I\'m passionate about the startup ecosystem and the rise of no-code platforms. As a freelance writer, I enjoy analyzing how creators worldwide launch incredible digital products and smart automations without writing a single line of code.',
                ),
                'voice_style' => 'entusiasta, emprendedor, no-code y práctico',
                'specialty' => 'startups',
              ),
              10 => 
              array (
                'name' => 'Roger Figuera',
                'slug' => 'roger-figuera',
                'email' => 'roger.figuera@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Qué tal! Soy Roger. Veo la tecnología a través del prisma del diseño visual y la experiencia de usuario. Como redactor freelance, me encanta hablar sobre cómo la tipografía, las interfaces limpias y el buen diseño UI/UX convierten productos complejos en herramientas intuitivas y placenteras.',
                  'en' => 'Hey there! I\'m Roger. I look at technology through the lens of visual design and user experience. As a freelance writer, I love discussing how typography, clean interfaces, and thoughtful UI/UX turn complex tools into intuitive, delightful products.',
                ),
                'voice_style' => 'creativo, visual, enfocado en experiencia de usuario',
                'specialty' => 'design_ui_ux',
              ),
              11 => 
              array (
                'name' => 'Robert Figuera',
                'slug' => 'robert-figuera',
                'email' => 'robert.figuera@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola a todos! Soy Robert. Como saxofonista y apasionado del sonido, vivo explorando la frontera donde la música se encuentra con la tecnología. Escribo como freelancer sobre sintetizadores, software de producción musical, acústica digital y cómo los algoritmos modernos están reinventando el arte sonoro.',
                  'en' => 'Hi everyone! I\'m Robert. As a saxophonist and sound enthusiast, I live at the intersection of music and technology. I write as a freelancer about synths, music production software, digital acoustics, and how modern algorithms are reinventing the sonic arts.',
                ),
                'voice_style' => 'apasionado, creativo, sonoro y musical',
                'specialty' => 'audio_tech',
              ),
              12 => 
              array (
                'name' => 'Valeria Mendoza',
                'slug' => 'valeria-mendoza',
                'email' => 'valeria.mendoza@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola! Soy Valeria. Trabajo como consultora SEO freelance desde hace más de 6 años. Me apasiona descifrar cómo piensan los motores de búsqueda, optimizar estructuras web y escribir sobre visibilidad orgánica en la era de los buscadores impulsados por IA.',
                  'en' => 'Hi! I\'m Valeria. I\'ve worked as a freelance SEO consultant for over 6 years. I love deciphering search engine algorithms, optimizing website architecture, and writing about organic visibility in the era of AI-powered search.',
                ),
                'voice_style' => 'analítico, estructurado, enfocado en SEO técnico y tráfico orgánico',
                'specialty' => 'seo',
              ),
              13 => 
              array (
                'name' => 'Gabriel Palacios',
                'slug' => 'gabriel-palacios',
                'email' => 'gabriel.palacios@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Qué tal! Soy Gabriel. Me muevo en el mundo de las campañas digitales y el marketing de resultados como freelancer. Me fascina analizar métricas de conversión, estrategias de anuncios en Google y redes, y cómo la automatización publicitaria está cambiando las reglas del juego.',
                  'en' => 'Hey there! I\'m Gabriel. I work in digital ad campaigns and performance marketing as a freelancer. I\'m fascinated by conversion metrics, Google/social ad strategies, and how automated advertising is reshaping the game.',
                ),
                'voice_style' => 'directo, orientado a métricas, SEM y retorno publicitario',
                'specialty' => 'sem_advertising',
              ),
              14 => 
              array (
                'name' => 'Daniela Rangel',
                'slug' => 'daniela-rangel',
                'email' => 'daniela.rangel@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola a todos! Mi nombre es Daniela. Soy redactora creativa y copywriter freelance. Me encanta explorar cómo las palabras correctas conectan a las marcas con las personas, analizando el poder de la narrativa digital y las tendencias de contenido que enganchan al lector.',
                  'en' => 'Hello everyone! My name is Daniela. I\'m a creative copywriter and freelance author. I love exploring how the right words connect brands with people, analyzing digital storytelling and content trends that truly engage readers.',
                ),
                'voice_style' => 'persuasivo, empático, narrativo y creativo',
                'specialty' => 'copywriting',
              ),
              15 => 
              array (
                'name' => 'Mauricio Delgado',
                'slug' => 'mauricio-delgado',
                'email' => 'mauricio.delgado@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Saludos! Soy Mauricio. Me dedico al growth marketing y a diseñar estrategias de adquisición digital como freelancer. Disfruto escribiendo sobre experimentos de crecimiento, analítica web y cómo las startups escalan rápido combinando creatividad y datos.',
                  'en' => 'Greetings! I\'m Mauricio. I specialize in growth marketing and digital acquisition strategies as a freelancer. I enjoy writing about growth experiments, web analytics, and how startups scale fast by combining creativity with data.',
                ),
                'voice_style' => 'ágil, experimental, enfocado en crecimiento y analítica digital',
                'specialty' => 'growth_marketing',
              ),
              16 => 
              array (
                'name' => 'Mariana Paredes',
                'slug' => 'mariana-paredes',
                'email' => 'mariana.paredes@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola! Soy Mariana. Trabajo como diseñadora multimedia e ilustradora freelance. Mi pasión es dar vida a las ideas a través de la animación, el motion graphics y el diseño visual para contar historias tecnológicas que entren por los ojos.',
                  'en' => 'Hi! I\'m Mariana. I work as a freelance multimedia designer and illustrator. My passion is bringing ideas to life through animation, motion graphics, and visual design to tell compelling visual tech stories.',
                ),
                'voice_style' => 'visual, dinámico, enfocado en multimedia y diseño gráfico',
                'specialty' => 'multimedia_design',
              ),
              17 => 
              array (
                'name' => 'Fernando Zambrano',
                'slug' => 'fernando-zambrano',
                'email' => 'fernando.zambrano@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Qué tal! Por aquí Fernando. Ejerzo como analista de seguridad informática y colaborador freelance. Me apasiona investigar vulnerabilidades, privacidad de datos y explicar de forma sencilla cómo protegernos frente a las amenazas del mundo digital actual.',
                  'en' => 'Hey there! Fernando here. I work as an IT security analyst and freelance contributor. I\'m passionate about investigating vulnerabilities, data privacy, and clearly explaining how to stay secure in today\'s digital landscape.',
                ),
                'voice_style' => 'preventivo, riguroso, enfocado en ciberseguridad y privacidad',
                'specialty' => 'cybersecurity',
              ),
              18 => 
              array (
                'name' => 'Patricia Cárdenas',
                'slug' => 'patricia-cardenas',
                'email' => 'patricia.cardenas@glodaxia.com',
                'bio' => 
                array (
                  'es' => '¡Hola! Mi nombre es Patricia. Soy diseñadora de producto UI/UX y redactora freelance. Me encanta estudiar cómo interactúan las personas con la tecnología para diseñar y escribir sobre interfaces intuitivas, accesibles y centradas en el usuario.',
                  'en' => 'Hello! My name is Patricia. I\'m a UI/UX product designer and freelance writer. I love studying how humans interact with technology to design and write about intuitive, accessible, and user-centered digital interfaces.',
                ),
                'voice_style' => 'centrado en el usuario, analítico, enfocado en accesibilidad y UX',
                'specialty' => 'product_ux',
              ),
            );
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
        // Master Taxonomy: 8 Core Pillars
        $categories = array (
  0 => 
  array (
    'en' => 'Artificial Intelligence',
    'es' => 'Inteligencia Artificial',
    'slug_en' => 'artificial-intelligence',
    'slug_es' => 'inteligencia-artificial',
    'desc_es' => 'Investigamos a fondo la revolución de la IA: desde modelos fundacionales, agentes autónomos y visión computacional, hasta herramientas generativas, robótica y su impacto práctico en la sociedad.',
    'desc_en' => 'In-depth reporting on the AI revolution: from frontier models, autonomous agents, and computer vision, to generative workflows, robotics, and transformative industry breakthroughs.',
  ),
  1 => 
  array (
    'en' => 'Development & Software',
    'es' => 'Desarrollo & Software',
    'slug_en' => 'development-software',
    'slug_es' => 'desarrollo-software',
    'desc_es' => 'El epicentro del código y la ingeniería de software: arquitecturas web, ecosistemas móviles, bases de datos, herramientas para desarrolladores, código abierto y todo el universo del desarrollo.',
    'desc_en' => 'The heart of software engineering and code: modern web architectures, mobile ecosystems, databases, developer tooling, open source, and the entire programming universe.',
  ),
  2 => 
  array (
    'en' => 'Cybersecurity',
    'es' => 'Ciberseguridad',
    'slug_en' => 'cybersecurity',
    'slug_es' => 'ciberseguridad',
    'desc_es' => 'Análisis riguroso de la seguridad digital: investigación de vulnerabilidades, respuesta a incidentes, defensa corporativa, privacidad, criptografía y las leyes que protegen el ecosistema digital.',
    'desc_en' => 'Rigorous digital security intelligence: vulnerability research, threat response, enterprise defense, privacy protocols, cryptography, and the policies shaping cyber resilience.',
  ),
  3 => 
  array (
    'en' => 'Cloud & Infrastructure',
    'es' => 'Cloud & Infraestructura',
    'slug_en' => 'cloud-infrastructure',
    'slug_es' => 'cloud-infraestructura',
    'desc_es' => 'La columna vertebral de Internet: arquitectura en la nube, prácticas DevOps, centros de datos, redes de telecomunicaciones, servidores y las tecnologías que hacen posible la computación global.',
    'desc_en' => 'The backbone of the modern internet: cloud platforms, DevOps culture, scalable data centers, high-speed networks, and infrastructure powering global distributed computing.',
  ),
  4 => 
  array (
    'en' => 'Hardware & Silicon',
    'es' => 'Hardware & Semiconductores',
    'slug_en' => 'hardware-silicon',
    'slug_es' => 'hardware-semiconductores',
    'desc_es' => 'Exploramos la tecnología tangible: arquitecturas de silicio, procesadores de vanguardia, dispositivos de consumo, sistemas embebidos y la ingeniería física que impulsa la era digital.',
    'desc_en' => 'Exploring tangible technology: next-gen semiconductor silicon, advanced processors, consumer gear, embedded systems, and the physical engineering powering computing.',
  ),
  5 => 
  array (
    'en' => 'Design & Digital Product',
    'es' => 'Diseño & Producto Digital',
    'slug_en' => 'design-digital-product',
    'slug_es' => 'diseno-producto-digital',
    'desc_es' => 'La intersección entre personas y tecnología: diseño de experiencia UX/UI, plataformas no-code, desarrollo de videojuegos 3D, interfaces espaciales y la creación de productos digitales excepcionales.',
    'desc_en' => 'Where humans and technology connect: UX/UI design systems, no-code platforms, 3D game development, spatial computing, and crafting remarkable digital products.',
  ),
  6 => 
  array (
    'en' => 'Business & Startups',
    'es' => 'Negocios & Startups',
    'slug_en' => 'business-startups',
    'slug_es' => 'negocios-startups',
    'desc_es' => 'El pulso de la economía digital: innovación financiera FinTech, comercio electrónico, capital de riesgo, estrategias de crecimiento, Web3 y las historias de los creadores que transforman industrias.',
    'desc_en' => 'Tracking the digital economy: FinTech innovation, e-commerce, venture capital, growth strategies, Web3, and the builders transforming global commercial ecosystems.',
  ),
  7 => 
  array (
    'en' => 'Science & Frontier Tech',
    'es' => 'Ciencia & Futuro',
    'slug_en' => 'science-frontier-tech',
    'slug_es' => 'ciencia-futuro',
    'desc_es' => 'La vanguardia del descubrimiento humano: computación cuántica, tecnologías climáticas, biotecnología, exploración espacial, domótica inteligente y las innovaciones que definen nuestro futuro.',
    'desc_en' => 'The frontier of human progress: quantum computing, climate tech, biotechnology, space exploration, smart environments, and deep scientific innovations defining tomorrow.',
  ),
);

        foreach ($categories as $catData) {
            Category::updateOrCreate(
                ['slug_en' => $catData['slug_en']],
                [
                    'name' => ['en' => $catData['en'], 'es' => $catData['es']],
                    'slug_es' => $catData['slug_es'],
                    'description' => [
                        'en' => $catData['desc_en'],
                        'es' => $catData['desc_es'],
                    ],
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
                'url' => 'https://www.theverge.com/rss/ai-artificial-intelligence/index.xml',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 60,
                'is_active' => true,
            ],
            [
                'name' => 'Google Research Blog',
                'url' => 'https://research.google/blog/rss/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Meta AI Blog',
                'url' => 'https://about.fb.com/news/category/technology-and-innovation/feed/',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => true,
            ],
            [
                'name' => 'Hugging Face Papers',
                'url' => 'https://huggingface.co/blog/feed.xml',
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
                'is_active' => false,
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
                'is_active' => false,
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
                'is_active' => false,
            ],
            [
                'name' => 'Search Engine Journal',
                'url' => 'https://www.searchenginejournal.com/feed/',
                'type' => 'rss',
                'category' => 'SEO & Digital Marketing',
                'frequency' => 60,
                'is_active' => false,
            ],
            [
                'name' => 'Ahrefs Blog',
                'url' => 'https://ahrefs.com/blog/feed/',
                'type' => 'rss',
                'category' => 'SEO & Digital Marketing',
                'frequency' => 120,
                'is_active' => false,
            ],

            // --- Software Development & Official Framework Releases (Tier 1) ---
            [
                'name' => 'Next.js Releases',
                'url' => 'https://github.com/vercel/next.js/releases.atom',
                'type' => 'atom',
                'category' => 'Web & Backend Development',
                'frequency' => 120,
                'is_active' => false,
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
                'is_active' => false,
            ],
            [
                'name' => 'Tailwind CSS Releases',
                'url' => 'https://github.com/tailwindlabs/tailwindcss/releases.atom',
                'type' => 'atom',
                'category' => 'Web Design & UX/UI',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'Node.js Releases',
                'url' => 'https://github.com/nodejs/node/releases.atom',
                'type' => 'atom',
                'category' => 'Web & Backend Development',
                'frequency' => 120,
                'is_active' => false,
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
                'url' => 'https://hackernoon.com/feed',
                'type' => 'rss',
                'category' => 'Tech Industry',
                'frequency' => 120,
                'is_active' => false,
            ],
            [
                'name' => 'HackerNoon AI',
                'url' => 'https://hackernoon.com/tagged/ai/feed',
                'type' => 'rss',
                'category' => 'Artificial Intelligence',
                'frequency' => 120,
                'is_active' => false,
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
            $sourceData['is_default'] = $sourceData['is_active'] ?? false;
            Source::updateOrCreate(
                ['url' => $sourceData['url']],
                $sourceData
            );
        }
        }
}
