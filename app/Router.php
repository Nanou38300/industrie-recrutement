<?php
/**
 * Router - Gestion centralisée du routing
 * 
 * Simplifie énormément index.php en encapsulant toute la logique
 * de parsing d'URL et de détection de contexte
 */

namespace App;

class Router
{
    private string $action;
    private string $step;
    private string $id;
    private array $segments;
    private array $queryParams;

    public function __construct()
    {
        $this->parseRequest();
    }

    /**
     * Parse la requête HTTP pour extraire action, step, id
     */
    private function parseRequest(): void
    {
        // Parse l'URL
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $this->segments = array_values(array_filter(explode('/', $uri)));

        // Récupère les paramètres avec priorité: GET > segments URL
        $this->action = $_GET['action'] ?? ($this->segments[0] ?? 'accueil');
        $this->step = $_GET['step'] ?? ($this->segments[1] ?? '');
        $this->id = $_GET['id'] ?? ($this->segments[2] ?? '');
        
        $this->queryParams = $_GET;
    }

    /**
     * Retourne l'action courante
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * Retourne l'étape (step) courante
     */
    public function getStep(): string
    {
        return $this->step;
    }

    /**
     * Retourne l'ID comme chaîne
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Retourne l'ID converti en entier (0 si vide)
     */
    public function getIdAsInt(): int
    {
        return (int) $this->id;
    }

    /**
     * Vérifie si l'ID est valide (non vide et numérique)
     */
    public function hasValidId(): bool
    {
        return !empty($this->id) && ctype_digit($this->id);
    }

    /**
     * Retourne tous les paramètres GET
     */
    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    /**
     * Récupère un paramètre GET spécifique
     */
    public function getParam(string $key, mixed $default = null): mixed
    {
        return $this->queryParams[$key] ?? $default;
    }

    /**
     * Vérifie si c'est une page publique (accessible sans connexion)
     */
    public function isPublicPage(): bool
    {
        $publicPages = [
            'accueil',
            'bureauEtude',
            'domaineExpertise',
            'recrutement',
            'contact'
        ];
        
        return in_array($this->action, $publicPages, true);
    }

    /**
     * Vérifie si c'est une page de connexion/inscription
     */
    public function isAuthPage(): bool
    {
        return $this->action === 'utilisateur' 
            && in_array($this->step, ['login', 'create'], true);
    }

    /**
     * Vérifie si c'est un appel API (pas de layout HTML)
     */
    public function isApiCall(): bool
    {
        return $this->action === 'administrateur' && $this->step === 'api-rdv';
    }

    /**
     * Vérifie si le menu public doit être affiché
     */
    public function shouldShowPublicMenu(): bool
    {
        return $this->isPublicPage() || $this->isAuthPage();
    }

    /**
     * Vérifie si le menu connecté doit être affiché
     */
    public function shouldShowAuthMenu(): bool
    {
        return in_array($this->action, ['administrateur', 'candidat'], true);
    }

    /**
     * Vérifie si le footer doit être affiché
     */
    public function shouldShowFooter(): bool
    {
        return $this->shouldShowPublicMenu();
    }

    /**
     * Génère une URL complète
     */
    public function generateUrl(string $action, string $step = '', string $id = ''): string
    {
        $url = "/?action=$action";
        
        if (!empty($step)) {
            $url .= "&step=$step";
        }
        
        if (!empty($id)) {
            $url .= "&id=$id";
        }
        
        return $url;
    }

    /**
     * Redirige vers une action/step
     */
    public function redirect(string $action, string $step = '', string $id = ''): void
    {
        $url = $this->generateUrl($action, $step, $id);
        header("Location: $url");
        exit;
    }

    /**
     * Redirige vers la page d'accueil
     */
    public function redirectToHome(): void
    {
        $this->redirect('accueil');
    }

    /**
     * Redirige vers le login
     */
    public function redirectToLogin(): void
    {
        $this->redirect('utilisateur', 'login');
    }
}
