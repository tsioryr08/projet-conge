<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$avatar = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
$demandes = $demandes ?? [];
$departements = $departements ?? [];
$stats = $stats ?? ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0, 'annulee' => 0];
$statut = $statut ?? 'en_attente';
$departement_id = $departement_id ?? null;
?>

<div class="app-wrap">

  <!-- SIDEBAR RH -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-shield-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace RH</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="/rh/demandes" class="active"><i class="bi bi-file-earmark-text"></i> Demandes
        <span class="nav-badge alert"><?= esc($stats['en_attente'] ?? 0) ?></span>
      </a></li>
      <li><a href="/rh/soldes"><i class="bi bi-bar-chart"></i> Soldes de congés</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc($avatar ?: 'RH') ?></div>
        <div>
          <div class="user-name"><?= esc($user['prenom'] ?? '') ?> <?= esc($user['nom'] ?? '') ?></div>
          <div class="user-role">RH</div>
        </div>
        <a href="/logout" style="margin-left:auto;color:rgba(255,255,255,.25);font-size:1.1rem" title="Déconnexion">
          <i class="bi bi-box-arrow-right"></i>
        </a>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Gestion des demandes</div>
        <div class="topbar-breadcrumb"><a href="/rh/demandes">Demandes</a></div>
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

      <!-- Métriques -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc($stats['en_attente'] ?? 0) ?></div>
          <div class="metric-label">En attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
          <div class="metric-val"><?= esc($stats['approuvee'] ?? 0) ?></div>
          <div class="metric-label">Approuvées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val"><?= esc($stats['refusee'] ?? 0) ?></div>
          <div class="metric-label">Refusées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-circle"></i></div></div>
          <div class="metric-val"><?= esc($stats['annulee'] ?? 0) ?></div>
          <div class="metric-label">Annulées</div>
        </div>
      </div>

      <!-- Filtres -->
      <div class="data-card" style="margin-bottom:1.5rem">
        <form method="get" action="/rh/demandes" style="padding:1.25rem;display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap">
          <div style="display:flex;gap:1rem;flex:1;min-width:300px">
            <div style="flex:1">
              <label class="f-label">Statut</label>
              <select name="statut" class="f-select">
                <option value="en_attente" <?= $statut === 'en_attente' ? 'selected' : '' ?>>En attente</option>
                <option value="approuvee" <?= $statut === 'approuvee' ? 'selected' : '' ?>>Approuvées</option>
                <option value="refusee" <?= $statut === 'refusee' ? 'selected' : '' ?>>Refusées</option>
                <option value="annulee" <?= $statut === 'annulee' ? 'selected' : '' ?>>Annulées</option>
              </select>
            </div>
            <div style="flex:1">
              <label class="f-label">Département</label>
              <select name="departement_id" class="f-select">
                <option value="">Tous les départements</option>
                <?php foreach ($departements as $dept): ?>
                  <option value="<?= esc($dept['id']) ?>" <?= $departement_id == $dept['id'] ? 'selected' : '' ?>>
                    <?= esc($dept['libelle'] ?? 'Département') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <button type="submit" class="btn-forest">Filtrer</button>
        </form>
      </div>

      <!-- Liste des demandes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Demandes (<?= esc(count($demandes)) ?>)</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Département</th>
              <th>Type</th>
              <th>Du</th>
              <th>Au</th>
              <th>Durée</th>
              <th>Statut</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($demandes)): ?>
              <?php foreach ($demandes as $demande): ?>
                <?php
                $displayStatus = match($demande['statut'] ?? 'en_attente') {
                    'approuvee' => 'approuvée',
                    'refusee'   => 'refusée',
                    'annulee'   => 'annulée',
                    default     => 'en attente',
                };
                ?>
                <tr>
                  <td class="td-name">
                    <div style="font-weight:500"><?= esc($demande['prenom'] ?? '') ?> <?= esc($demande['nom'] ?? '') ?></div>
                    <div class="td-muted" style="font-size:.75rem">#<?= esc($demande['employe_id'] ?? '') ?></div>
                  </td>
                  <td class="td-muted"><?= esc($demande['dept_libelle'] ?? '—') ?></td>
                  <td><span class="type-badge t-annuel"><?= esc($demande['type_libelle'] ?? 'Congé') ?></span></td>
                  <td class="td-muted td-mono"><?= esc(date('d/m/Y', strtotime($demande['date_debut']))) ?></td>
                  <td class="td-muted td-mono"><?= esc(date('d/m/Y', strtotime($demande['date_fin']))) ?></td>
                  <td class="td-mono"><strong><?= esc((int)($demande['nb_jours'] ?? 0)) ?></strong> j</td>
                  <td><span class="statut s-<?= esc($demande['statut'] ?? 'en_attente') ?>"><?= esc($displayStatus) ?></span></td>
                  <td>
                    <div class="action-btns">
                      <?php if (($demande['statut'] ?? '') === 'en_attente'): ?>
                        <form method="post" action="/rh/demandes/<?= esc((int)$demande['id']) ?>/approuver" style="display:inline">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn-sm btn-approve" onclick="return confirm('Approuver cette demande ?')">
                            <i class="bi bi-check-lg"></i> Approuver
                          </button>
                        </form>
                        <form method="post" action="/rh/demandes/<?= esc((int)$demande['id']) ?>/refuser" style="display:inline">
                          <?= csrf_field() ?>
                          <button type="submit" class="btn-sm btn-refuse" onclick="return confirm('Refuser cette demande ?')">
                            <i class="bi bi-x-lg"></i> Refuser
                          </button>
                        </form>
                      <?php else: ?>
                        <a href="/rh/demandes/<?= esc((int)$demande['id']) ?>" class="btn-sm btn-view">
                          <i class="bi bi-eye"></i> Voir
                        </a>
                      <?php endif; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="8" style="text-align:center;padding:2rem;color:var(--muted)">
                  <i class="bi bi-inbox"></i> Aucune demande trouvée
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>

</div>

<?= $this->endSection() ?>
