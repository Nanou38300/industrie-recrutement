FROM php:8.4-apache


# Installer l'extension pour connecter pdo à mySql et gérer les exceptions
RUN docker-php-ext-install pdo_mysql mysqli

# Activer le module de réécriture d'Apache
RUN a2enmod rewrite

# Définit le dossier de travail à l'intérieur du conteneur
# Tous les chemins relatifs partiront de ce répertoire
WORKDIR /var/www/html

# Le conteneur sera sur le port 8080
EXPOSE 8080

# On lance Apache en premier plan pour que Docker le laisse ouvert
CMD ["apache2-foreground"]

