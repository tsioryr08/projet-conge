<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$avatar = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
$typesConge = $typesConge ?? [];
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
      <li><a href="/admin/departements"><i class="bi bi-building"></i> Départements</a></li>
      <li><a href="/admin/types_conge" class="active"><i class="bi bi-calendar"></i> Types de congé</a></li>
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
        <div class="topbar-title">Types de congé</div>
        <div class="topbar-breadcrumb"><a href="/admin">Admin</a> <i class="bi bi-chevron-right"></i> Types de congé</div>
      </div>
      <a href="/admin/types_conge/create" class="btn-forest">
        <i class="bi bi-plus"></i> Ajouter
      </a>
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
        <h3><i class="bi bi-calendar" style="color:var(--forest);margin-right:6px"></i>Ajouter un type de congé</h3>
        <form method="post" action="/admin/types_conge" style="margin-bottom:0">
          <?= csrf_field() ?>
          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label">Libellé *</label>
              <input type="text" name="libelle" class="f-input" placeholder="Congé annuel" value="<?= old('libelle') ?>" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Jours annuels *</label>
              <input type="number" name="jours_annuels" class="f-input" placeholder="30" min="1" value="<?= old('jours_annuels') ?>" required/>
            </div>
            <div class="f-group">
              <label class="f-label">Déductible *</label>
              <select name="deductible" class="f-select" required>
                <option value="1" <?= old('deductible') === '1' ? 'selected' : '' ?>>Oui</option>
                <option value="0" <?= old('deductible') === '0' ? 'selected' : '' ?>>Non</option>
              </select>
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
          <div class="form-actions">
            <button type="submit" class="btn-forest"><i class="bi bi-plus"></i> Créer le type de congé</button>
            <button type="reset" class="btn-secondary">Réinitialiser</button>
          </div>
        </form>
      </div>

      <!-- Liste des types de congé -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Tous les types de congé</h3>
        </div>
        <?php if (!empty($typesConge)): ?>
          <table class="tbl">
            <thead>
              <tr>
                <th>Libellé</th>
                <th>Jours annuels</th>
                <th>Déductible</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($typesConge as $type): ?>
                <tr>
                  <td class="td-name">
                    <div style="font-weight:500"><?= esc($type['libelle'] ?? '') ?></div>
                  </td>
                  <td><?= esc($type['jours_annuels'] ?? 0) ?></td>
                  <td>
                    <span class="badge <?= esc($type['deductible'] ? 'badge-green' : 'badge-gray') ?>">
                      <?= esc($type['deductible'] ? 'Oui' : 'Non') ?>
                    </span>
                  </td>
                  <td class="td-actions">
                    <a href="/admin/types_conge/<?= esc($type['id']) ?>/edit" class="btn-icon" title="Éditer">
                      <i class="bi bi-pencil"></i>
                    </a>
                    <form method="post" action="/admin/types_conge/<?= esc($type['id']) ?>" style="display:inline" onsubmit="return confirm('Supprimer ce type de congé ?')">
                      <?= csrf_field() ?>
                      <input type="hidden" name="_method" value="DELETE">
                      <button type="submit" class="btn-icon btn-red" title="Supprimer">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div style="padding:2rem;text-align:center;color:#666;">Aucun type de congé</div>
        <?php endif; ?>
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
