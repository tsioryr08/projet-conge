<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$avatar = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
$employes = $employes ?? [];
?>

<div class="app-wrap">

  <!-- SIDEBAR ADMIN -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon" style="background:var(--ink);border:1px solid rgba(255,255,255,.15)"><i class="bi bi-shield-check" style="color:var(--leaf)"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Administration</span></div>
    </div>
    <div class="sidebar-section">Gestion</div>
    <ul class="sidebar-nav">
      <li><a href="/admin"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li><a href="/rh/demandes"><i class="bi bi-inbox"></i> Toutes les demandes</a></li>
      <li><a href="/admin/employes" class="active"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="/admin/departements"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="/admin/types_conge"><i class="bi bi-calendar"></i> Types de congé</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar" style="background:#5a2d82;width:32px;height:32px;font-size:.7rem">AD</div>
        <div><div class="user-name"><?= esc($user['prenom'] ?? '') ?> <?= esc($user['nom'] ?? '') ?></div><div class="user-role">Admin système</div></div>
      </div>
      <a href="/logout" class="sidebar-logout" title="Déconnexion">
        <i class="bi bi-box-arrow-right"></i> Déconnexion
      </a>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Gestion des employés</div>
        <div class="topbar-breadcrumb"><a href="/admin">Admin</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Employés</div>
      </div>
      <div class="topbar-actions">
        <a href="/admin/employes/create" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter</a>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc(session()->getFlashdata('success')) ?>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <!-- Formulaire ajout -->
      <div class="form-section">
        <h3><i class="bi bi-person-plus" style="color:var(--forest);margin-right:6px"></i>Ajouter un employé</h3>
        <form method="post" action="/admin/employes" style="margin-bottom:0">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Prénom *</label>
              <input type="text" name="prenom" class="f-input" placeholder="Jean" value="<?= old('prenom') ?>" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Nom *</label>
              <input type="text" name="nom" class="f-input" placeholder="Rakoto" value="<?= old('nom') ?>" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Email *</label>
              <input type="email" name="email" class="f-input" placeholder="jean.rakoto@techmada.mg" value="<?= old('email') ?>" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Mot de passe initial *</label>
              <input type="password" name="password" class="f-input" placeholder="À communiquer à l'employé" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Département *</label>
              <select name="departement_id" class="f-select" required>
                <option value="">Sélectionner...</option>
                <?php foreach ($departements as $dept): ?>
                  <option value="<?= esc($dept['id']) ?>" <?= old('departement_id') == $dept['id'] ? 'selected' : '' ?>>
                    <?= esc($dept['nom']) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Rôle *</label>
              <select name="role" class="f-select" required>
                <option value="employe" <?= old('role') === 'employe' ? 'selected' : '' ?>>Employé</option>
                <option value="rh" <?= old('role') === 'rh' ? 'selected' : '' ?>>Responsable RH</option>
                <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrateur</option>
              </select>
            </div>
            <div class="f-group">
              <label class="f-label">Date d'embauche *</label>
              <input type="date" name="date_embauche" class="f-input" value="<?= old('date_embauche', date('Y-m-d')) ?>" required/>
            </div>
          </div>
          <?php if (session()->getFlashdata('errors')): ?>
            <div class="flash flash-error" style="margin-bottom:1rem">
              <i class="bi bi-exclamation-circle-fill"></i>
              <ul style="margin:0;padding-left:1.5rem">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                  <li><?= esc($error) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
          <div class="flash flash-info" style="margin-bottom:1rem">
            <i class="bi bi-info-circle-fill"></i>
            <span style="font-size:.82rem">Les soldes de congés seront initialisés automatiquement selon les types de congé configurés.</span>
          </div>
          <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi bi-plus"></i> Créer l'employé</button>
            <button type="reset" class="btn-secondary">Réinitialiser</button>
          </div>
        </form>
      </div>

      <!-- Liste des employés -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Tous les employés</h3>
          <div style="display:flex;gap:6px">
            <input type="text" class="f-input" placeholder="Rechercher..." style="width:200px;padding:6px 10px;font-size:.8rem"/>
          </div>
        </div>
        <?php if (!empty($employes)): ?>
          <table class="tbl">
            <thead>
              <tr>
                <th>Employé</th>
                <th>Département</th>
                <th>Rôle</th>
                <th>Embauche</th>
                <th>Statut</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($employes as $emp): ?>
                <tr>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.68rem"><?= strtoupper(substr($emp['prenom'] ?? '', 0, 1)) . strtoupper(substr($emp['nom'] ?? '', 0, 1)) ?></div>
                      <div class="profile-info"><div class="pname"><?= esc($emp['prenom'] ?? '') ?> <?= esc($emp['nom'] ?? '') ?></div><div class="pdept"><?= esc($emp['email'] ?? '') ?></div></div>
                    </div>
                  </td>
                  <td class="td-muted"><?= esc($emp['departement_nom'] ?? 'N/A') ?></td>
                  <td><span class="type-badge" style="background:<?= $emp['role'] === 'admin' ? '#f8dbd8' : ($emp['role'] === 'rh' ? '#d5e8f7' : '#f1efe8') ?>;color:<?= $emp['role'] === 'admin' ? '#c00' : ($emp['role'] === 'rh' ? '#1a4f7a' : '#7a7a7a') ?>"><?= esc($emp['role'] ?? '') ?></span></td>
                  <td class="td-muted td-mono" style="font-size:.78rem"><?= esc($emp['date_embauche'] ?? '') ?></td>
                  <td><span class="statut <?= esc($emp['actif'] ? 's-approuvee' : 's-annulee') ?>" style="font-size:.68rem"><?= esc($emp['actif'] ? 'actif' : 'inactif') ?></span></td>
                  <td>
                    <div class="action-btns">
                      <a href="/admin/employes/<?= esc($emp['id']) ?>/edit" class="btn-sm btn-edit"><i class="bi bi-pencil"></i> Éditer</a>
                      <?php if ($emp['actif']): ?>
                        <form method="post" action="/admin/employes/<?= esc($emp['id']) ?>/deactivate" style="display:inline" onsubmit="return confirm('Désactiver cet employé ?')">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn-sm btn-del"><i class="bi bi-slash-circle"></i></button>
                        </form>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div style="padding:2rem;text-align:center;color:#666;">Aucun employé</div>
        <?php endif; ?>
      </div>

    </div>
    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span></div>
  </div>

</div>

<?= $this->endSection() ?>
