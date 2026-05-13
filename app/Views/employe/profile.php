<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php $errors = session()->getFlashdata('errors') ?? []; ?>

<div class="app-wrap">
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="/employe/"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employe/demandes/create"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/employe/demandes"><i class="bi bi-calendar3"></i> Mes demandes <span class="nav-badge alert"><?= esc($pendingCount ?? 0) ?></span></a></li>
      <li><a href="/employe/profile" class="active"><i class="bi bi-person"></i> Mon profil</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc(strtoupper(substr($prenom ?? '', 0, 1) . substr($nom ?? '', 0, 1)) ?: 'E') ?></div>
        <div>
          <div class="user-name"><?= esc(($prenom ?? '') . ' ' . ($nom ?? '')) ?></div>
          <div class="user-role">Employé</div>
        </div>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mon profil</div>
        <div class="topbar-breadcrumb"><a href="/employe/">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Profil</div>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>

      <?php if (! empty($errors)): ?>
        <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc(implode(' · ', $errors)) ?></div>
      <?php endif; ?>

      <div class="form-section">
        <h3>Modifier mes informations</h3>

        <form method="post" action="/employe/profile">
          <?= csrf_field() ?>

          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label" for="prenom">Prénom</label>
              <input id="prenom" type="text" class="f-input" name="prenom" value="<?= esc(old('prenom') ?? $prenom ?? '') ?>" required>
            </div>
            <div class="f-group">
              <label class="f-label" for="nom">Nom</label>
              <input id="nom" type="text" class="f-input" name="nom" value="<?= esc(old('nom') ?? $nom ?? '') ?>" required>
            </div>
          </div>

          <div class="f-group" style="margin-bottom:1rem">
            <label class="f-label" for="email">Email</label>
            <input id="email" type="email" class="f-input" value="<?= esc($email ?? '') ?>" readonly>
            <div class="f-hint">L’email n’est pas modifiable ici.</div>
          </div>

          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label" for="current_password">Mot de passe actuel</label>
              <input id="current_password" type="password" class="f-input" name="current_password" required>
            </div>
            <div class="f-group">
              <label class="f-label" for="new_password">Nouveau mot de passe</label>
              <input id="new_password" type="password" class="f-input" name="new_password" placeholder="Laisser vide pour ne pas changer">
            </div>
          </div>

          <div class="f-group" style="margin-bottom:1rem">
            <label class="f-label" for="confirm_password">Confirmer le nouveau mot de passe</label>
            <input id="confirm_password" type="password" class="f-input" name="confirm_password" placeholder="Répéter le nouveau mot de passe">
          </div>

          <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi bi-check2-circle"></i> Enregistrer les modifications</button>
            <a href="/employe/" class="btn-secondary"><i class="bi bi-arrow-left"></i> Retour</a>
          </div>
        </form>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= date('Y') ?> <span>TechMada RH</span></div>
  </div>
</div>

<?= $this->endSection() ?>
