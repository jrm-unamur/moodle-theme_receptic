<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * @package    theme_receptic
 * @author     Jean-Roch Meurisse
 * @copyright  2016 - Cellule TICE - Unversite de Namur
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['advancedsettings'] = 'Réglages avancés';
$string['brandbanner'] = 'Bannière';
$string['brandbanner_desc'] = 'Afficher une bannière avec les logos définis dans les réglages généraux du thème';
$string['brandbannercolor'] = 'Couleur de la bannière';
$string['brandbannercolor_desc'] = 'Couleur de base pour générer le dégradé de la bannière';
$string['brandcolor'] = 'Couleur principale';
$string['brandcolor_desc'] = 'La couleur majeure du thème';
$string['branding'] = 'Branding';
$string['choosereadme'] = 'Receptic est un thème basé sur Boost qui offre un panel d\'options supplémentaires dont la gestion des "boules rouges et oranges" qui permettent d\'avoir un aperçu direct et visuel des nouvelles ressources et activités dans les espaces de cours ainsi qu\'à partir du tableau de bord. Il propose également optionnellement un tableau de bord simplifié, une bannière institutionnelle, un bouton d\'édition permanent dans la barre de navigation ainsi que la possibilité d\'afficher des messages flash tant pour les étudiants que pour les enseignants';
$string['configtitle'] = 'Receptic';
$string['generalsettings'] = 'Réglages généraux';
$string['loginbackgroundimage'] = 'Image de fond pour la page de connexion';
$string['loginbackgroundimage_desc'] = 'Votre image sera ajoutée en fond d\'écran sur la page de connexion';
$string['pluginname'] = 'Receptic';
$string['preset'] = 'Préréglage de thème';
$string['preset_desc'] = 'Veuillez choisir un préréglage pour modifier l\'aspect du thème.';
$string['presetfiles'] = 'Fichiers de réglages additionnels pour le thème';
$string['presetfiles_desc'] = 'Des fichiers de réglages peuvent être utilisés afin de changer totalement la présentation du thème. Voir <a href=https://docs.moodle.org/dev/Boost_Presets>Boost presets</a>  pour des informations sur la façon de créer et partager vos propres fichiers de réglages et consultez le <a href=http://moodle.net/boost>catalogue des fichiers de réglages</a> existants.';
$string['rawscss'] = 'SCSS brut';
$string['rawscss_desc'] = 'Ce champ permet d\'indiquer du code SCSS ou CSS qui sera injecté à la fin de la feuille de styles.';
$string['rawscsspre'] = 'Code SCSS initial';
$string['rawscsspre_desc'] = 'Ce champ permet d\'indiquer du code SCSS d\'initialisation, qui sera injecté dans la feuille de styles avant toute autre définition. La plupart du temps, ce code sera utilisé pour définir des variables.';
$string['region-side-pre'] = 'Droite';
$string['otheractivities'] = 'Autres activités';
$string['otherresources'] = 'Autres ressources';
$string['mainmodules'] = 'Outils principaux';
$string['toolselector'] = 'Sélecteur d\'outils';
$string['activitieslist'] = 'Modules d\'activité principaux';
$string['activitieslistdesc'] = 'Select activity types to display on top of tools selector in a "main section"';
$string['resourceslist'] = 'Ressources principales';
$string['resourceslistdesc'] = 'Select resource types to display on top of tools selector in a "main section"';
$string['hideblocks'] = 'Masquer les blocs';
$string['showblocks'] = 'Afficher les blocs';
$string['editmode'] = 'Mode édition';
$string['toolbarsettings'] = 'Barre de navigation';
$string['emptycourselist'] = 'Aucun cours';
$string['mycourses'] = 'Mes cours';
$string['addsection'] = 'Ajouter une section';
$string['unenrolme'] = 'Me désinscrire de ce cours.';
$string['createcourse'] = 'Créer un cours... (manuel)';
$string['editbutton'] = 'Afficher le bouton d\'édition permanent';
$string['editbuttondesc'] = 'Ajoute un bouton d\'activation du mode édition dans la barre de raccourcis';
$string['hidedefaulteditingbutton'] = 'Masquer le bouton d\'édition par défaut';
$string['hidedefaulteditingbutton_desc'] = 'Masque le bouton d\'édition par défaut (dont la position dépend du contexte. Ce paramètre n\'est pas pris en compte si le précédent est désactivé)';
$string['connectme'] = 'Me connecter';
$string['redballs'] = 'Boules rouges';
$string['enableballs'] = 'Activer les boules rouges et oranges';
$string['enableballs_desc'] = 'Afficher le nombre d\'activités/ressources que l\'utilisateur n\'a pas encore consultées sur son tableau de bord et à côté du nom des sections; afficher une boule rouge en regard des activités/ressources que l\'utilisateur n\'a pas encore consultées sur la page d\'accueil des cours.<br/>Afficher sur le tableau de bord et à côté du nom des sections le nombre d\'activités / ressources dont le contenu a été modifié et non encore consulté par l\'utilisateur; afficher une boule orange en regard des activités/ressources que l\'utilisateur n\'a pas encore consultées depuis que leur contenu a été modifié';
$string['hotitemslookback'] = 'Période pour les boules rouges/oranges';
$string['hotitemslookback_desc'] = 'Nombre de jours ou de semaines à prendre en compte pour le calcul initial des boules rouges/oranges';
$string['logoleft'] = 'Logo de gauche pour la bannière';
$string['logoleft_desc'] = 'Votre image sera placée sur la bannière à gauche (logo de la plateforme)';
$string['logocenter'] = 'Logo au centre de la bannière';
$string['logocenter_desc'] = 'Votre image sera placée au centre la bannière';
$string['logoright'] = 'Logo de droite pour la bannière';
$string['logoright_desc'] = 'Votre image sera placée sur la bannière à gauche (logo institutionnel)';
$string['receptic:editflashbox'] = 'Permission d\'éditer les messages flash';
$string['flashboxes'] = 'Messages flash';
$string['flashboxteachers'] = 'Message flash aux enseignants';
$string['flashboxteachers_desc'] = 'Permet d\'afficher un message flash sur le tableau de bord des enseignants';
$string['flashboxteacherstype'] = 'Type du message flash';
$string['flashboxteacherstype_desc'] = 'Type du message à afficher';
$string['flashboxstudents'] = 'Message flash aux étudiants';
$string['flashboxstudents_desc'] = 'Permet d\'afficher un message flash sur le tableau de bord des étudiants';
$string['flashboxstudentstype'] = 'Type du message flash';
$string['flashboxstudentstype_desc'] = 'Type du message à afficher';
$string['trick'] = 'Astuce';
$string['showblocks'] = 'Afficher les blocks';
$string['hideblocks'] = 'Masquer les blocks';
$string['mixedviewindashboard'] = 'Tableau de bord alternatif';
$string['mixedviewindashboard_desc'] = 'Rassembler les deux vues "Chrolonogie" et "Cours" en une seule vue pour le tableau de bord';
$string['addcoursebutton'] = 'Bouton "Créer un cours"';
$string['addcoursebutton_desc'] = 'Ajouter un bouton pour créer un cours sur le tableau de bord des créateurs de cours et gestionnaires';
$string['localcreatecourseplugin'] = 'Plugin local pour la création de cours';
$string['localcreatecourseplugin_desc'] = 'Nom d\'un éventuel plugin local qui gère une méthode alternative de création de cours (par exemple si la création de cours est conditionnée à une liste officielle). Si ce plugin existe il doit impérativement déclarer la permission local/*nomduplugin*:create';
$string['bulkenrolme'] = 'Bouton "M\'inscrire à mes cours"';
$string['bulkenrolme_desc'] = 'Ajouter un bouton pour une inscription en lot d\'un étudiant à ses cours (Nécessite une méthode d\'inscription spécifique à votre institution).';
$string['bulkenrolmeplugin'] = 'Plugin pour l\'inscription en lot';
$string['bulkenrolmeplugin_desc'] = 'Nom d\'une éventuel plugin implémentant l\'inscription en lot d\'un étudiant à ses cours (par exemple enrol_bulkenrol';
$string['bulkenrolmefile'] = 'Nom du fichier du plugin';
$string['bulkenrolmefile_desc'] = 'Nom du fichier implémentant votre méthode d\'inscription  en lot d\'un étudiant à ses cours';
$string['bulkenrolemailpattern'] = 'Modèle de courriel';
$string['bulkenrolemailpattern_desc'] = 'Pour restreindre la méthode locale d\inscription en lot aux étudiants dont l\'adresse de courriel contient ce modèle';
$string['dashboarddisplaymode'] = 'Mode d\'affichage';
$string['coursecreation'] = 'Ajout de cours';
$string['bulkenrol'] = 'Inscription en lot';
$string['otherdashboardshortcuts'] = 'Autres raccourcis sur le tableau de bord';
$string['togglecoursevisibility'] = 'Changer la visibilité';
$string['togglecoursevisibility_desc'] = 'Permettre aux enseingants de rendre visible/invisible leurs cours depuis le tableau de bord sans passer par l\'administration du cours.';
$string['unenrolme'] = 'Me désincrire';
$string['unenrolme_desc'] = 'Permettre aux utilisateurs de se désinscrire d\'un cours depuis le tableau de bord.';
$string['footer'] = 'Pied de page';
$string['contactheader'] = 'Entête des contacts';
$string['contactheader_desc'] = 'Ajouter une entête au-dessus des informations de contact';
$string['contactemail'] = 'Courriel de contace';
$string['contactemail_desc'] = 'Ajouter un lien courriel dans le pied de page (pas de lien si laissé vide).';
$string['contactphone'] = 'Téléphone de contact';
$string['contactphone_desc'] = 'Ajouter un numéro de téléphone de contact dans le pied de page (rien si laissé vide).';
$string['moodlecredits'] = 'Logo Moodle';
$string['moodlecredits_desc'] = 'Afficher le logo Moodle dans le pied de page';
$string['or'] = 'ou';
$string['poweredby'] = 'Utilise';
$string['logininfo'] = 'Utilisateur connecté';
$string['logininfo_desc'] = 'Afficher un lien vers le profil de l\'utilisateur connecté et un lien de connexion dans le pied de page';

$string['makevisible'] = 'Rendre disponible';
$string['confirmmakevisible'] = 'Confirmez pour permettre aux étudiants de consulter votre cours';
$string['makeinvisible'] = 'Rendre indisponible';
$string['confirmmakeinvisible'] = 'Confirmez pour empêcher l\'accès à votre cours';
$string['confirmunenrolme'] = 'Voules-vous vraiment vous désinscrire de ce cours?';
$string['activitynavigation'] = 'Navigateur d\'activités';
$string['activitynavigation_desc'] = 'Afficher une boîte de sélection en-dessous des activités permettant de passer à une autre activité sans devoir repasser par la page d\'accueil du cours';