# 📋 README — Projet Congé · TechMada RH
> Système de gestion des congés — CodeIgniter 4 · SQLite · 3 rôles

---

## 🚀 Installation & Lancement

### 1. Cloner le projet


### 2. Installer les dépendances
```bash
composer install
```

### 3. Configurer l'environnement
```bash
cp env .env
```
Ouvrir `.env` et modifier :
```
CI_ENVIRONMENT = development
```

### 4. Configurer la base de données
Dans `app/Config/Database.php`, vérifier :
```php
'database' =>     'database' => WRITEPATH . 'db' . DIRECTORY_SEPARATOR . 'ma_base.db',  
'DBDriver' => 'SQLite3',
```
Créer le dossier si besoin :
```bash
mkdir -p writable/db
```

### 5. Lancer les migrations et le seeder
```bash
php spark migrate
php spark db:seed InitialSeeder
```

### 6. Démarrer le serveur
```bash
php spark serve
```
Accéder à : **http://localhost:8080**

---

## 👤 Comptes de test

| Rôle | Email | Mot de passe |
|------|-------|-------------|
| 🟡 RH | rh@techmada.mg | rh123 |
| 🟢 Employé | marie@techmada.mg | employe123 |
| 🟢 Employé | jean@techmada.mg | employe123 |

---

## 🗂️ Fonctionnalités par rôle

### 🟢 Employé
> Se connecter avec `marie@techmada.mg` / `employe123`

- **Tableau de bord** : voir ses statistiques (demandes en attente, approuvées, refusées) et son solde de congés restant
- **Soumettre une demande** : choisir un type de congé, les dates et un motif
- **Consulter ses demandes** : voir l'historique et le statut de chaque demande
- **Annuler une demande** : possible uniquement si le statut est encore *en attente*

---

### 🟡 Responsable RH
> Se connecter avec `rh@techmada.mg` / `rh123`

- **Voir toutes les demandes en attente** des employés
- **Approuver une demande** → le solde de l'employé est automatiquement déduit
- **Refuser une demande** → avec un commentaire optionnel, le solde reste intact
- **Logique métier** : le solde n'est jamais déduit à la soumission, uniquement à l'approbation. Si une demande approuvée est annulée, le solde est recrédité automatiquement.

---

## 🔄 Workflow d'une demande de congé

```
Employé soumet
      │
      ▼
  [en_attente]
      │
      ├──── RH approuve ──▶ [approuvée] ──▶ solde déduit automatiquement
      │
      └──── RH refuse   ──▶ [refusée]   ──▶ solde intact

Si annulation après approbation ──▶ solde recrédité
```

> ⚠️ **Règle métier importante** : le solde est toujours vérifié avant approbation.
> Si `jours_pris + nb_jours_demandés > jours_attribués`, la demande ne peut pas être approuvée.

---

## 🗄️ Structure de la base de données

| Table | Description |
|-------|-------------|
| `employes` | Comptes utilisateurs avec rôle |
| `departements` | Départements de l'entreprise |
| `types_conge` | Types de congé et jours alloués |
| `soldes` | Solde par employé, par type et par année |
| `conges` | Demandes de congé avec statut |

---

## 🛠️ Stack technique

| Élément | Technologie |
|---------|-------------|
| Framework | CodeIgniter 4.7.x |
| Base de données | SQLite 3 |
| Auth | Sessions CI4 natives |
| Frontend | HTML/CSS pur, Bootstrap Icons |
| PHP | 8.4+ |

---

## 📁 Structure du projet

```
app/
├── Controllers/
│   ├── Employe/       → Auth, Dashboard, Demandes
│   ├── Rh/            → Dashboard, Demandes
│   └── Admin/         → Dashboard, Employes
├── Models/            → EmployeModel, CongeModel, SoldeModel...
├── Views/
│   ├── layouts/       → app.php (layout principal)
│   ├── employe/       → vues espace employé
│   ├── rh/            → vues espace RH
│   └── admin/         → vues espace admin
└── Database/
    ├── Migrations/    → structure BDD
    └── Seeds/         → données de test
```

---

## ⚠️ Notes importantes

- Le fichier base de données se trouve dans `writable/db/ma_base.db`
- Ne pas commiter `writable/db/ma_base.db` sur git (déjà dans `.gitignore`)
- Le CSRF est actif sur tous les formulaires POST
- Les routes sont protégées par rôle via `AuthFilter`

---
