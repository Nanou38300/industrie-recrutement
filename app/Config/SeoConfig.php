<?php
/**
 * SeoConfig - Configuration SEO centralisée
 * 
 * Évite d'encombrer index.php avec de gros tableaux
 * Facilite la maintenance et l'ajout de nouvelles pages
 */

namespace App\Config;

class SeoConfig
{
    /**
     * Pages publiques indexables par les moteurs de recherche
     */
    private const PUBLIC_PAGES = [
        'accueil',
        'bureauEtude',
        'domaineExpertise',
        'recrutement',
        'contact'
    ];

    /**
     * Métadonnées par page
     */
    private const META_DATA = [
        'accueil' => [
            'title' => "TCS Chaudronnerie - Spécialistes en chaudronnerie industrielle",
            'description' => "Spécialistes en chaudronnerie, tuyauterie et soudure, nous accompagnons les industriels dans la fabrication, l'installation et la maintenance de leurs équipements. Grâce à notre expertise technique, notre réactivité et notre exigence qualité, nous intervenons sur des installations complexes dans les secteurs du nucléaire, de la chimie et de la maintenance industrielle.",
        ],
        
        'bureauEtude' => [
            'title' => "Bureau d'études — TCS Chaudronnerie",
            'description' => "Conception, ingénierie, dossiers techniques (DMOS/QMOS), et accompagnement de la conception à la mise en service de vos projets industriels.",
        ],
        
        'domaineExpertise' => [
            'title' => "Domaines d'expertise — TCS Chaudronnerie",
            'description' => "Nous intervenons dans les secteurs du nucléaire, de la chimie et de la maintenance industrielle, en mettant à disposition notre savoir-faire en chaudronnerie et tuyauterie. Nos équipes qualifiées réalisent des travaux en zones contrôlées, fabriquent des équipements sous pression, installent des réseaux de tuyauterie pour fluides complexes et assurent la maintenance d'installations industrielles.",
        ],
        
        'recrutement' => [
            'title' => "Recrutement — TCS Chaudronnerie",
            'description' => "Nos offres d'emploi en chaudronnerie, tuyauterie et soudage. Rejoignez une équipe experte et participez à des projets industriels d'envergure.",
        ],
        
        'contact' => [
            'title' => "Contact — TCS Chaudronnerie",
            'description' => "Parlez-nous de votre projet : maintenance, fabrication et installation d'équipements industriels. Notre équipe est à votre écoute.",
        ],
    ];

    /**
     * Métadonnées par défaut pour les pages non-publiques
     */
    private const DEFAULT_META = [
        'title' => "TCS Chaudronnerie",
        'description' => "Solutions de chaudronnerie, tuyauterie et soudure pour l'industrie.",
        'robots' => 'noindex, nofollow',
    ];

    /**
     * Récupère les métadonnées SEO pour une action donnée
     */
    public static function getMetaForAction(string $action): array
    {
        // Récupère les meta ou utilise les valeurs par défaut
        $meta = self::META_DATA[$action] ?? self::DEFAULT_META;
        
        // Ajoute l'URL canonique
        $meta['canonical'] = self::generateCanonicalUrl();
        
        // Définit robots selon le type de page
        $meta['robots'] = self::isPublicPage($action) 
            ? 'index, follow' 
            : 'noindex, nofollow';
        
        return $meta;
    }

    /**
     * Récupère les métadonnées SEO pour une annonce spécifique
     */
    public static function getMetaForAnnonce(int $id, ?string $titre = null): array
    {
        $title = $titre 
            ? "$titre — TCS Chaudronnerie" 
            : "Offre #$id — TCS Chaudronnerie";

        return [
            'title' => $title,
            'description' => "Découvrez l'offre d'emploi #$id chez TCS Chaudronnerie. Postulez dès maintenant et rejoignez notre équipe.",
            'canonical' => self::generateCanonicalUrl(),
            'robots' => 'index, follow',
        ];
    }

    /**
     * Vérifie si une action correspond à une page publique
     */
    private static function isPublicPage(string $action): bool
    {
        return in_array($action, self::PUBLIC_PAGES, true);
    }

    /**
     * Génère l'URL canonique de la page courante
     */
    private static function generateCanonicalUrl(): string
    {
        // Protocole (http ou https)
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' 
            ? 'https' 
            : 'http';
        
        // Hôte
        $host = $_SERVER['HTTP_HOST'];
        
        // Chemin sans query string
        $path = strtok($_SERVER['REQUEST_URI'], '?');
        
        // Construction de l'URL canonique
        return rtrim("$protocol://$host", '/') . $path;
    }

    /**
     * Génère les balises meta HTML
     */
    public static function generateMetaTags(array $meta): string
    {
        $html = '';
        
        // Title
        $html .= '<title>' . htmlspecialchars($meta['title'], ENT_QUOTES, 'UTF-8') . '</title>' . PHP_EOL;
        
        // Description
        if (!empty($meta['description'])) {
            $html .= '<meta name="description" content="' . htmlspecialchars($meta['description'], ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
        }
        
        // Robots
        if (!empty($meta['robots'])) {
            $html .= '<meta name="robots" content="' . htmlspecialchars($meta['robots'], ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
        }
        
        // Canonical
        if (!empty($meta['canonical'])) {
            $html .= '<link rel="canonical" href="' . htmlspecialchars($meta['canonical'], ENT_QUOTES, 'UTF-8') . '">' . PHP_EOL;
        }
        
        return $html;
    }

    /**
     * Retourne la liste des pages publiques
     */
    public static function getPublicPages(): array
    {
        return self::PUBLIC_PAGES;
    }

    /**
     * Ajoute une page aux métadonnées (utile pour l'extension)
     */
    public static function addMetaData(string $action, array $meta): void
    {
        self::META_DATA[$action] = $meta;
    }
}
