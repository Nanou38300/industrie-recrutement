<!DOCTYPE html>
<html lang="fr">
<head>
    <!-- Encodage et affichage responsive -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php
    $seoTitle     = htmlspecialchars($SEO['title'] ?? 'TCS - Tuyauterie Chaudronnerie Soudure', ENT_QUOTES, 'UTF-8');
    $seoDesc      = htmlspecialchars($SEO['description'] ?? 'Experts en chaudronnerie, tuyauterie et soudure industrielle. Fabrication, installation et maintenance d’équipements pour le nucléaire, la chimie et la maintenance industrielle.', ENT_QUOTES, 'UTF-8');
    $seoRobots    = htmlspecialchars($SEO['robots'] ?? 'noindex, nofollow', ENT_QUOTES, 'UTF-8');
    $seoCanonical = htmlspecialchars($SEO['canonical'] ?? '', ENT_QUOTES, 'UTF-8');
    ?>

    <title><?= $seoTitle ?></title>
    <meta name="description" content="<?= $seoDesc ?>">
    <meta name="robots" content="<?= $seoRobots ?>">
    <?php if (!empty($seoCanonical)): ?>
        <link rel="canonical" href="<?= $seoCanonical ?>">
    <?php endif; ?>


    <meta property="og:title" content="<?= $seoTitle ?>">
    <meta property="og:description" content="<?= $seoDesc ?>">
    <meta property="og:type" content="website">
    <?php if (!empty($seoCanonical)): ?>
        <meta property="og:url" content="<?= $seoCanonical ?>">
    <?php endif; ?>

    <link rel="stylesheet" href="/assets/css/style.css">

    <base href="http://industrie.localhost:8080">
</head>

<body>