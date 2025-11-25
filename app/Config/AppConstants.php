<?php
/**
 * AppConstants - Constantes centralisées de l'application
 * 
 * Évite les "magic strings" dispersées dans le code
 * Facilite la maintenance et réduit les erreurs de typo
 */

namespace App\Config;

class AppConstants
{
    // ==================== STATUTS D'ANNONCE ====================
    
    public const ANNONCE_ACTIVE = 'activée';
    public const ANNONCE_BROUILLON = 'brouillon';
    public const ANNONCE_ARCHIVEE = 'archivée';

    public const ANNONCE_STATUTS = [
        self::ANNONCE_ACTIVE,
        self::ANNONCE_BROUILLON,
        self::ANNONCE_ARCHIVEE,
    ];

    // ==================== RÔLES UTILISATEUR ====================
    
    public const ROLE_ADMIN = 'administrateur';
    public const ROLE_CANDIDAT = 'candidat';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CANDIDAT,
    ];

    // ==================== TYPES DE CONTRAT ====================
    
    public const CONTRAT_CDI = 'CDI';
    public const CONTRAT_CDD = 'CDD';
    public const CONTRAT_INTERIM = 'Intérim';
    public const CONTRAT_ALTERNANCE = 'Alternance';
    public const CONTRAT_STAGE = 'Stage';

    public const CONTRATS_DISPONIBLES = [
        self::CONTRAT_CDI,
        self::CONTRAT_CDD,
        self::CONTRAT_INTERIM,
        self::CONTRAT_ALTERNANCE,
        self::CONTRAT_STAGE,
    ];

    // ==================== STATUTS CANDIDATURE ====================
    
    public const CANDIDATURE_ENVOYEE = 'envoyée';
    public const CANDIDATURE_CONSULTEE = 'consultée';
    public const CANDIDATURE_ENTRETIEN = 'entretien';
    public const CANDIDATURE_RECRUTE = 'recruté';
    public const CANDIDATURE_REFUSE = 'refusé';

    public const CANDIDATURE_STATUTS = [
        self::CANDIDATURE_ENVOYEE,
        self::CANDIDATURE_CONSULTEE,
        self::CANDIDATURE_ENTRETIEN,
        self::CANDIDATURE_RECRUTE,
        self::CANDIDATURE_REFUSE,
    ];

    // ==================== TYPES D'ENTRETIEN ====================
    
    public const ENTRETIEN_PHYSIQUE = 'physique';
    public const ENTRETIEN_VISIO = 'visio';
    public const ENTRETIEN_TELEPHONIQUE = 'téléphonique';

    public const ENTRETIEN_TYPES = [
        self::ENTRETIEN_PHYSIQUE,
        self::ENTRETIEN_VISIO,
        self::ENTRETIEN_TELEPHONIQUE,
    ];

    // ==================== LIMITES FICHIERS ====================
    
    /**
     * Taille maximale fichier: 5 MB
     */
    public const MAX_FILE_SIZE = 5242880;
    
    /**
     * Extensions autorisées pour les CV
     */
    public const CV_ALLOWED_EXTENSIONS = ['pdf', 'doc', 'docx'];
    
    /**
     * Extensions autorisées pour les photos de profil
     */
    public const PHOTO_ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif'];

    // ==================== SESSION & SÉCURITÉ ====================
    
    /**
     * Timeout de session: 30 minutes (1800 secondes)
     */
    public const SESSION_TIMEOUT = 1800;
    
    /**
     * Durée de vie token CSRF: 1 heure (3600 secondes)
     */
    public const CSRF_TOKEN_LIFETIME = 3600;
    
    /**
     * Nombre maximum de tentatives de connexion
     */
    public const LOGIN_MAX_ATTEMPTS = 5;
    
    /**
     * Fenêtre de temps pour le rate limiting (5 minutes)
     */
    public const RATE_LIMIT_WINDOW = 300;

    // ==================== PAGINATION ====================
    
    /**
     * Nombre d'éléments par page par défaut
     */
    public const ITEMS_PER_PAGE = 20;
    
    /**
     * Nombre d'annonces par page
     */
    public const ANNONCES_PER_PAGE = 10;

    // ==================== VALIDATION ====================
    
    /**
     * Longueur minimale mot de passe
     */
    public const PASSWORD_MIN_LENGTH = 8;
    
    /**
     * Longueur minimale titre annonce
     */
    public const ANNONCE_TITRE_MIN_LENGTH = 5;
    
    /**
     * Longueur maximale titre annonce
     */
    public const ANNONCE_TITRE_MAX_LENGTH = 200;

    // ==================== MÉTHODES UTILITAIRES ====================

    /**
     * Vérifie si un statut d'annonce est valide
     */
    public static function isValidAnnonceStatut(string $statut): bool
    {
        return in_array($statut, self::ANNONCE_STATUTS, true);
    }

    /**
     * Vérifie si un rôle est valide
     */
    public static function isValidRole(string $role): bool
    {
        return in_array($role, self::ROLES, true);
    }

    /**
     * Vérifie si un type de contrat est valide
     */
    public static function isValidContrat(string $contrat): bool
    {
        return in_array($contrat, self::CONTRATS_DISPONIBLES, true);
    }

    /**
     * Vérifie si un statut de candidature est valide
     */
    public static function isValidCandidatureStatut(string $statut): bool
    {
        return in_array($statut, self::CANDIDATURE_STATUTS, true);
    }

    /**
     * Vérifie si un type d'entretien est valide
     */
    public static function isValidEntretienType(string $type): bool
    {
        return in_array($type, self::ENTRETIEN_TYPES, true);
    }

    /**
     * Retourne un libellé lisible pour un statut d'annonce
     */
    public static function getAnnonceStatutLabel(string $statut): string
    {
        return match($statut) {
            self::ANNONCE_ACTIVE => 'Active',
            self::ANNONCE_BROUILLON => 'Brouillon',
            self::ANNONCE_ARCHIVEE => 'Archivée',
            default => 'Inconnu',
        };
    }

    /**
     * Retourne un libellé lisible pour un statut de candidature
     */
    public static function getCandidatureStatutLabel(string $statut): string
    {
        return match($statut) {
            self::CANDIDATURE_ENVOYEE => 'Envoyée',
            self::CANDIDATURE_CONSULTEE => 'Consultée',
            self::CANDIDATURE_ENTRETIEN => 'Entretien prévu',
            self::CANDIDATURE_RECRUTE => 'Recruté(e)',
            self::CANDIDATURE_REFUSE => 'Refusée',
            default => 'Inconnu',
        };
    }

    /**
     * Retourne une classe CSS pour un statut d'annonce
     */
    public static function getAnnonceStatutClass(string $statut): string
    {
        return match($statut) {
            self::ANNONCE_ACTIVE => 'badge-success',
            self::ANNONCE_BROUILLON => 'badge-warning',
            self::ANNONCE_ARCHIVEE => 'badge-secondary',
            default => 'badge-light',
        };
    }

    /**
     * Retourne une classe CSS pour un statut de candidature
     */
    public static function getCandidatureStatutClass(string $statut): string
    {
        return match($statut) {
            self::CANDIDATURE_ENVOYEE => 'badge-primary',
            self::CANDIDATURE_CONSULTEE => 'badge-info',
            self::CANDIDATURE_ENTRETIEN => 'badge-warning',
            self::CANDIDATURE_RECRUTE => 'badge-success',
            self::CANDIDATURE_REFUSE => 'badge-danger',
            default => 'badge-light',
        };
    }
}
