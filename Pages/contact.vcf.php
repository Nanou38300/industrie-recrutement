<?php
header('Content-Type: text/vcard');
header('Content-Disposition: attachment; filename="contact.vcf"');

$prenom = "Magalie";
$nom = "Dupont";
$tel = "+0613752773";
$email = "exemple@mail.com";
$adresse = "1 Rue de l’Exemple, 43000 Le Puy-en-Velay, France";
$entreprise = "Nom de l’entreprise";

echo "BEGIN:VCARD\r\n";
echo "VERSION:3.0\r\n";
echo "FN:$prenom $nom\r\n";
echo "N:$nom;$prenom;;;\r\n";
echo "ORG:$entreprise\r\n";
echo "TEL;TYPE=CELL:$tel\r\n";
echo "EMAIL:$email\r\n";
echo "ADR;TYPE=WORK:;;$adresse\r\n";
echo "END:VCARD\r\n";
