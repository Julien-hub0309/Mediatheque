---
niveau: "BTS CIEL IR — 2e année"
cadre: "Mini-projet en binôme"
duree: "6 heures"
environnement: "XAMPP / Apache / MariaDB, HTML5, CSS3, JavaScript, PHP, PDO"
tags:
  - bts-ciel
  - php
  - sql
  - web
  - cahier-des-charges
---

# Cahier des charges : Application Web de Gestion de Médiathèque

## 1. Contexte et Objectifs du Projet
La médiathèque dispose déjà d'une base de données relationnelle (nommée `mediatheque`) permettant de gérer les adhérents, les livres, les auteurs, les catégories et les emprunts. L'objectif de ce projet est de s'affranchir de l'utilisation directe de phpMyAdmin pour les opérations courantes en développant une application Web dédiée au personnel.

L'application doit permettre de :
* Consulter le catalogue des livres et leurs informations principales.
* Rechercher un ouvrage par son titre (et potentiellement par auteur ou catégorie en bonus).
* Consulter la liste des adhérents.
* Enregistrer de nouveaux emprunts et gérer les retours de livres.
* Suivre en temps réel les emprunts en cours et identifier visuellement les retards.

---

## 2. Modèle de Données et Règles de Gestion

### Modèle Physique de Données (MPD)
La base de données repose sur les tables suivantes :
* **ADHERENT** (`id_adherent`, `nom`, `prenom`, `email`, `date_inscription`)
* **LIVRE** (`id_livre`, `titre`, `isbn`, `annee_publication`, `disponible`, `id_categorie`)
* **AUTEUR** (`id_auteur`, `nom`, `prenom`)
* **CATEGORIE** (`id_categorie`, `libelle`)
* **EMPRUNT** (`id_emprunt`, `id_adherent`, `id_livre`, `date_emprunt`, `date_retour_prevue`, `date_retour`)
* **LIVRE_AUTEUR** (Table de jointure N:N entre `LIVRE` et `AUTEUR`)

### Règles de Gestion Métier
* Un adhérent peut effectuer plusieurs emprunts, mais un emprunt n'est rattaché qu'à un seul adhérent.
* Un livre peut être emprunté successivement à différentes dates, mais un emprunt actif ne concerne qu'un seul livre.
* Un livre appartient à une seule catégorie, mais une catégorie peut regrouper plusieurs livres.
* Un livre peut être écrit par un ou plusieurs auteurs (relation N:N gérée par `LIVRE_AUTEUR`)[cite: 1].
* **Disponibilité :** Un livre ne peut être proposé à l'emprunt que si son statut `disponible` est à vrai (TRUE)[cite: 1].

---

## 3. Spécifications Fonctionnelles

| Fonction | Description détaillée |
| :--- | :--- |
| **F01 — Accueil** | Page d'accueil (`index.php`) présentant le nom de l'application, un menu de navigation commun et un tableau de bord statistique[cite: 1, 2]. |
| **F02 — Liste des livres** | Affichage tabulaire des ouvrages : titre, auteur(s), catégorie, année de publication, ISBN et statut de disponibilité[cite: 1]. |
| **F03 — Recherche** | Formulaire de recherche textuelle de livres par titre (possibilité d'extension par auteur/catégorie)[cite: 1]. |
| **F04 — Liste des adhérents** | Affichage des informations des inscrits : nom, prénom, e-mail et date d'inscription[cite: 1]. |
| **F05 — Nouvel emprunt** | Interface de création d'emprunt associant un adhérent et un livre strictement disponible (date d'emprunt au jour J, retour prévu à J+14)[cite: 1]. |
| **F06 — Emprunts en cours** | Suivi des emprunts non clôturés (`date_retour IS NULL`) avec l'adhérent, le livre, la date d'emprunt et la date de retour prévue[cite: 1]. |
| **F07 — Gestion des retards** | Détection automatique : si `date_retour IS NULL` et que la date du jour dépasse `date_retour_prevue`, affichage explicite de la mention **« EN RETARD »** avec une mise en forme CSS spécifique[cite: 1]. |
| **F08 — Retour de livre** | Action de clôture : mise à jour de la `date_retour` à la date du jour et passage du livre concerné à l'état disponible (`disponible = TRUE`)[cite: 1]. |

---

## 4. Contraintes Techniques et Sécurité
* **Langages et Environnement :** HTML5, CSS3, JavaScript, PHP, et MySQL/MariaDB[cite: 1].
* **Connexion :** Utilisation obligatoire de **PDO** pour la connexion à la base de données via le fichier centralisé (`config/db.php`)[cite: 1].
* **Sécurité des données :**
  * Utilisation systématique de **requêtes préparées** pour toutes les interactions de formulaires[cite: 1].
  * Protection contre les failles XSS à l'aide de `htmlspecialchars()` lors de l'affichage[cite: 1].

---

## 5. Direction Artistique et Identité Visuelle
L'interface de l'application bénéficie d'une **direction artistique soignée**, pensée pour s'éloigner des interfaces austères de l'administration de bases de données et offrir une ambiance de **« bibliothèque nocturne »** chaleureuse, élégante et immersive.

### Choix Esthétiques & Typographiques
* **Ambiance chromique :** 
  * Un **fond nocturne profond** (dégradés de bleus nuit et marine profonds : `--navy-950` à `--navy-600`) évoquant le calme d'une bibliothèque feutrée[cite: 3].
  * Des touches d'**or antique** (`--gold-500`, `--gold-300`) pour souligner les éléments d'importance et structurer la navigation avec distinction[cite: 3].
  * Des teintes de **papier/crème** (`--cream-100`, `--cream-50`) pour les zones de lecture de contenu, garantissant un grand confort visuel et un contraste optimal avec l'encre des textes (`--ink-900`)[cite: 3].
* **Typographie éditoriale :**
  * Association de la police à empattement **Playfair Display** pour les titres et en-têtes (apportant le cachet littéraire et classique)[cite: 3].
  * Utilisation de **Work Sans** pour le corps de texte et les interfaces fonctionnelles, garantissant une lisibilité moderne et épurée[cite: 3].
* **États et Indicateurs Visuels :**
  * Des codes couleurs explicites et non agressifs pour le suivi métier : des tons pastels ou sombres nuancés pour le statut disponible (`--ok-bg`), les emprunts en cours (`--gold-100`), et une alerte visuelle nette en cas de **retard** (`--danger-bg` / `--danger-text`) pour attirer immédiatement l'attention du personnel sur les anomalies de restitution[cite: 3].

---

## 6. Architecture Logicielle et Organisation du Projet
Le projet adopte une structure modulaire et rigoureuse :

├── config

│   └── db.php

├── database

│   ├── mediatheque_backup.sql

│   └── schema.sql

├── includes

│   ├── footer.php

│   └── header.php

├── index.php

├── modules

│   ├── css

│   │   └── styles.css

│   └── java

│       └── script.js

└── web

    ├── adherent.php
    
    ├── emprunter.php
    
    ├── emprunt.php
    
    ├── livres.php
    
    └── retour.php
    
---

7. Difficultés Techniques Rencontrées et Résolutions
Au cours de la mise en œuvre de ce mini-projet, plusieurs points de blocage ont été identifiés et résolus par le binôme :

Dépendances fonctionnelles (Livre / Emprunt) : La gestion de l'état du livre en lien avec la table des emprunts a nécessité une rigueur particulière pour éviter les incohérences (notamment s'assurer qu'un livre emprunté bascule bien en indisponible et qu'il ne peut être réemprunté qu'après son retour effectif)[cite: 1].

Routage et chemins d'accès du serveur : L'organisation du code dans un dossier web/ a entraîné des erreurs de pointage du serveur vers les mauvais fichiers ou de mauvais chemins relatifs pour les inclusions, résolues par une adaptation minutieuse des chemins d'accès (../).

Erreur de liaison de la base de données (db.php) : Un problème temporaire de connexion PDO est survenu lors de l'appel du fichier de configuration centralisé, suite à une simple erreur d'orthographe dans les variables de connexion, rapidement repérée et corrigée.

8. Planning Prévisionnel de Réalisation (6 Heures)
- 0h00 – 1h00 : Analyse du cahier des charges, vérification de la base de données, mise en place de l'arborescence et de la connexion PHP/PDO[cite: 1].

- 1h00 – 2h15 : Développement de l'accueil (index.php), de la navigation globale et de la liste des livres[cite: 1].

- 2h15 – 3h00 : Implémentation de la liste des adhérents et du module de recherche[cite: 1].

- 3h00 – 4h30 : Développement du formulaire de création d'emprunt et mise à jour de la disponibilité[cite: 1].

- 4h30 – 5h15 : Gestion des retours, affichage des emprunts en cours et des retards[cite: 1].

- 5h15 – 6h00 : Phase de tests, corrections, captures d'écran, rédaction du README et préparation du rendu[cite: 1].

