# WIKITENDO

MyDigitalSchool | B3-DevWebProjet | Programmation Orienté Objet

- Projet : Développement d’un site WIKI

* Respect de la structure de données
* Respect de la structure des pages du site
* Persistance des données avec Doctrine
* Séparer les classes Modèles et Controller (respect du MVC)
* Utiliser des routes dynamiques
* Utilisation des boucles et conditionnelles TWIG
* Utiliser la composition et/ou l’héritage des vues TWIG
* Gérer les liaisons relationnelles avec Doctrine (1-\* _-_ etc)
* Formulaire d’inscription
* Formulaire de création d’article
* Gestion de la connexion et des droits d’accès

- Structure de l'application :

* Toutes les pages comportent :
  - menu de navigation
  - une bannière
  - un footer avec le nom des membres de l’équipe de dev
  - un bouton de connexion
* Menu de navigation
  - lien vers Accueil
  - lien vers la liste des catégories
  - lien vers la partie Privé
* PUBLIC
  - Page d’accueil
    - description du thème du wiki
    - liste de 2 catégories aléatoires
    - liste de 2 articles aléatoires
  - Page liste de catégorie
    - liste des catégorie avec le nombre d’articles dans la catégorie
  - Page catégorie avec la liste des articles
    - liste des articles avec le pseudo de l’auteur
  - Page article
    - le titre
    - le contenu de l’article
    - les information résumé dans un cadre auxiliaire
    - possibilité pour l’auteur de modifier son article
    - possibilité pour l’auteur de supprimer son article
  - PRIVÉE
    - Liste des articles créés
    - bouton pour créer un nouvel article

- Spech Tech

* PHP Version 7.4.x or greater
* SQLite
* Apache or Nginx
* Symfony 5.4.x

---

## Installation

- Prérequis à installer
  - PHP (ex: x64 Thread Safe ou Wampp)
  - Docker (ex: Docker desktop)
  - Nodejs + (`yarn` ou `npm`)
  - Symfony (CLI pour les intimes)
  - Un navigateur web
  - Un terminal pour executer des `commandes`
  - `symfony` == `php bin`

#### Verification

- Choisir [la bonne version de PHP]

  - `symfony local:php:list` (local web server)
  - `php -v` (composer version)

- Vérifer les prérequis Symfony
  - `symfony check:requirements`

#### Paramétrage

- Mettre à jour le fichier de config
  - Dupliquer le fichier .env -> .env.local
  - Mettre à jour les valeurs de variable (si besoin)

#### "Build front" du projet

- Récupérer les dépendances via Composer

  - `composer install`

- Récupérer les dépendances via npm/yarn

  - `yarn install` (yarn.lock) Oui
  - `npm install` (package.lock) Non

- Compile assets once
  - `yarn dev`
  - `npm run dev`

---

## Installation BDD

- Créer la base de données
  - `symfony console doctrine:database:create`
  - `php bin/console doctrine:database:create`
- Créer le schéma des tables
  - `symfony console doctrine:schema:create`
- Lancer les fixtures pour peupler la base

  - `symfony console doctrine:fixtures:load`

- Migration base de données (en cours de dev)
  - `symfony console make:migration`
  - `symfony console doctrine:migrations:migrate`

#### Commandes d'aide

- Recompile Front auto (en écoute)
  - `yarn watch`
  - `npm run watch`

---

## Lancement du projet

- Lancer le serveur web
  - `symfony server:start` ou `symfony server:start -d` (en arrière-plan)
    - `symfony open:local` (ouvrir le navigateur)
    - `symfony server:stop` (arrêter le serveur)

---

## Documentation

- Utilisateurs mis en place en base de données
  - `Mario` / `MarioPassword`' (Admin)
  - `Luigi` / `LuigiPassword` (Modérateur)
  - `Toad` / `ToadPassword` (User avec plusieurs articles)
  - `Peach` / `PeachPassword` (User avec articles et commentaires)
  - `Yoshi` / `YoshiPassword` (User avec plusieurs commentaires)