<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$avatar = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
$departement = $departement ?? [];
$errors = session()->getFlashdata('errors') ?? [];
?>

<div class="app-wrap">

  <!-- SIDEBAR ADMIN -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-shield-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace Admin</span></div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="/admin"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="/rh/demandes"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="/admin/employes"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="/admin/departements" class="active"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="/admin/types_conge"><i class="bi bi-calendar"></i> Types de congé</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc($avatar ?: 'ADM') ?></div>
        <div>
          <div class="user-name"><?= esc($user['prenom'] ?? '') ?> <?= esc($user['nom'] ?? '') ?></div>
          <div class="user-role">Admin</div>
        </div>
      </div>
      <a href="/logout" class="sidebar-logout" title="Déconnexion">
        <i class="bi bi-box-arrow-right"></i> Déconnexion
      </a>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Éditer un département</div>
        <div class="topbar-breadcrumb"><a href="/admin">Admin</a> <i class="bi bi-chevron-right"></i> <a href="/admin/departements">Départements</a> <i class="bi bi-chevron-right"></i> Éditer</div>
      </div>
    </div>

    <div class="content">
      <?php if (!empty($errors)): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <ul style="margin:0;padding-left:1.5rem">
            <?php foreach ($errors as $error): ?>
              <li><?= esc($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <!-- Formulaire -->
      <div class="data-card" style="max-width:600px">
        <form method="post" action="/admin/departements/<?= esc($departement['id']) ?>">
          <?= csrf_field() ?>

          <div class="form-group">
            <label class="f-label">Nom *</label>
            <input type="text" name="nom" class="f-input" required value="<?= esc($departement['nom'] ?? '') ?>">
          </div>

          <div class="form-group">
            <label class="f-label">Description</label>
            <textarea name="description" class="f-input" style="resize:vertical;height:100px"><?= esc($departement['description'] ?? '') ?></textarea>
          </div>

          <div style="display:flex;gap:1rem;margin-top:2rem">
            <button type="submit" class="btn-forest">Mettre à jour</button>
            <a href="/admin/departements" class="btn-secondary">Annuler</a>
          </div>
        </form>
      </div>

    </div>

    <footer class="footer-app">
      <div class="footer-content">
        <span>&copy; 2025 TechMada RH. Tous droits réservés.</span>
      </div>
    </footer>
  </div>

</div>

<?= $this->endSection() ?>
