# README_GUIDE — Module Employé (Projet Congés)

But: ce guide contient les prérequis, l'ordre de travail et les règles métier à respecter pour implémenter la partie « Employé » du projet Conge (CodeIgniter 4 + SQLite).

**Prérequis**
- PHP >= 7.4 (ou version compatible CI4), extension `pdo_sqlite` activée
- Composer
- CodeIgniter 4 (le projet existe déjà dans ce repo)
- Utiliser le driver `Query Builder` (pas de SQL brut)

**Fichier SQLite**
- Créer la base SQLite :

```bash
mkdir -p writable/database
touch writable/database/database.sqlite
chmod 0666 writable/database/database.sqlite
```
- Configurer la connexion DB dans le fichier `.env` (ou `app/Config/Database.php`) pour pointer vers `writable/database/database.sqlite`.

**Migrations (ordre recommandé)**
1. `departements`
2. `types_conge`
3. `employes`
4. `soldes`
5. `conges`

Chaque migration doit définir les colonnes décrites dans le cahier (voir `1.txt`) et ajouter les contraintes FK.

**Seeders**
- Ajouter un seeder initial : 1 admin, 2 employés, 3 types de congé, soldes initialisés.
- Exemple d'exécution : `php spark db:seed InitialSeeder` (adapter selon nom du seeder).

**Authentification & sécurité**
- Utiliser la session CI4 native pour gérer la connexion.
- `password_hash()` obligatoire pour stocker les mots de passe ; `password_verify()` à la connexion.
- Mettre en place un `AuthFilter` pour protéger les routes.
- Groupes de routes : `/employe`, `/rh`, `/admin`.
- Vérifier le rôle (`role` champ `employes`) **dans chaque controller** en plus du filtre.
- CSRF activé pour tous les formulaires POST (config CI4 par défaut).

**Structure des fichiers (suggestion)**
- Controllers: `app/Controllers/Employe/Auth.php`, `app/Controllers/Employe/CongeController.php`, `app/Controllers/Employe/ProfileController.php`
- Models: `app/Models/EmployeModel.php`, `app/Models/SoldeModel.php`, `app/Models/CongeModel.php`, `app/Models/TypeCongeModel.php`
- Views: `app/Views/employe/` (login, dashboard, demandes/list, demandes/create, profile)
- Layout global: `app/Views/layouts/app.php` (sidebar variant selon rôle)

**Règles métier à respecter (essentielles)**
- Solde stocké en deux colonnes : `jours_attribues` et `jours_pris`. `restant = jours_attribues - jours_pris` (calculé à la volée).
- Le solde ne doit être déduit qu'à l'`approbation` (opération RH). Pour le module Employé, vérifier et afficher le solde restant avant la soumission.
- Toujours valider côté serveur : `jours_pris + nb_jours_demande <= jours_attribues` (condition utilisée au moment d'approuver).
- Si une demande est annulée ou refusée après approbation -> rembourser le solde (RH côté back).

**Fonctionnalités Employé (implémenter dans cet ordre)**
1. Inscription / connexion / déconnexion (rôle par défaut `employe`).
2. Afficher le tableau de bord employé (solde par type, liens vers demandes).
3. Formulaire `Soumettre une demande` : champs `type_conge`, `date_debut`, `date_fin`, `motif`.
   - PRG pattern : POST → redirect (flashdata pour messages).
   - CSRF token présent.
4. Validations lors de la soumission :
   - `date_debut < date_fin` (bloquer sinon)
   - Pas de chevauchement avec d'autres demandes actives pour le même employé (vérifier dates)
   - Calcul du `nb_jour` : idéalement exclure week-ends. Pour simplifier (TD), on peut compter tous les jours calendaires mais prévoir le code pour basculer vers jours ouvrables.
   - Vérifier localement (avant envoi) que le solde courant *apparaît* suffisant (mais l'acceptation finale vérifie côté RH).
5. Lister ses propres demandes et statuts (`en_attente`, `approuvee`, `refusee`, `annulee`).
6. Optionnel : Annuler une demande encore en attente (autoriser uniquement si statut `en_attente`).
7. Optionnel : Modifier profil (nom, mot de passe) — mot de passe doit être rehashé.

**Calcul des jours**
- Implémentation recommandée : utiliser `DateTime` / `DateInterval` pour compter jours.
- Pour exclusivité week-ends : itérer jours et ignorer samedi/dimanche.
- Simplification acceptée pour TD : compter tous les jours calendaires (documenter choix).

**Bonnes pratiques CI4**
- 1 Model par table, utiliser les règles de validation intégrées dans les Models.
- Utiliser Query Builder (Model ou `db->table()`), éviter SQL brut.
- Pattern PRG pour toutes les écritures.
- Flashdata pour les messages de succès/erreur.
- Pas de JS côté client requis ; tout géré serveur.

**Tests**
- Ajouter tests unitaires basiques pour :
  - Calcul `nb_jour` entre deux dates
  - Vérification de chevauchement
  - Contraintes de validation lors de soumission

**Commandes utiles**
```
composer install
php spark migrate   # exécuter les migrations créées
php spark db:seed InitialSeeder
php spark serve
```

---
Si tu veux, j'implémente maintenant l'étape suivante : création des migrations (ordre ci‑dessus) ou mise en place de l'authentification employé. Dis-moi celle que tu veux commencer en premier.