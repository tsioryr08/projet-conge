<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$avatar         = strtoupper(substr($prenom ?? '', 0, 1) . substr($nom ?? '', 0, 1));
$soldes         = $soldes ?? [];
$latestDemandes = $latestDemandes ?? [];
$stats          = $stats ?? ['en_attente' => 0, 'approuvee' => 0, 'refusee' => 0];
?>

<div class="app-wrap">

  <!-- SIDEBAR EMPLOYÉ -->
  <aside class="sidebar">
    <div class="sidebar-brand">
      <div class="sidebar-logo-icon"><i class="bi bi-briefcase"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace employé</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="/employe/" class="active"><i class="bi bi-grid-1x2"></i> Tableau de bord</a></li>
      <li><a href="/employe/demandes/create"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li>
        <a href="/employe/demandes">
          <i class="bi bi-calendar3"></i> Mes demandes
          <span class="nav-badge alert"><?= esc($pendingCount ?? 0) ?></span>
        </a>
      </li>
      <li><a href="/employe/profile"><i class="bi bi-person"></i> Mon profil</a></li>
   
<li><a href="/employe/calendrier"><i class="bi bi-calendar-week"></i> Calendrier</a></li>
<li><a href="/employe/statistiques"><i class="bi bi-bar-chart"></i> Statistiques</a></li>
<li><a href="/logout"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc($avatar ?: 'E') ?></div>
        <div>
          <div class="user-name"><?= esc($prenom ?? '') ?> <?= esc($nom ?? '') ?></div>
          <div class="user-role">Employé</div>
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
        <div class="topbar-title">Tableau de bord</div>
        <div class="topbar-breadcrumb">Accueil</div>
      </div>
      <div class="topbar-actions">
        <a href="/employe/demandes/create" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
          <i class="bi bi-plus-lg"></i> Nouvelle demande
        </a>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success">
          <i class="bi bi-check-circle-fill"></i>
          <?= esc(session()->getFlashdata('success')) ?>
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
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc(array_sum(array_map(static fn($s) => (int)($s['jours_attribues'] ?? 0) - (int)($s['jours_pris'] ?? 0), $soldes))) ?></div>
          <div class="metric-label">Jours restants</div>
          <div class="metric-sub">sur <?= esc(array_sum(array_map(static fn($s) => (int)($s['jours_attribues'] ?? 0), $soldes)) ?: 0) ?> cette année</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val"><?= esc($stats['refusee'] ?? 0) ?></div>
          <div class="metric-label">Refusée</div>
        </div>
      </div>

      <!-- Soldes de congés -->
      <div class="data-card">
        <div class="data-card-head"><h3>Mes soldes de congés — <?= esc($year ?? date('Y')) ?></h3></div>
        <div style="padding:1rem 1.25rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem">
          <?php if (!empty($soldes)): ?>
            <?php foreach ($soldes as $solde): ?>
              <?php
              $attribues = (int)($solde['jours_attribues'] ?? 0);
              $pris      = (int)($solde['jours_pris'] ?? 0);
              $restants  = max(0, $attribues - $pris);
              $progress  = $attribues > 0 ? max(0, min(100, ($restants / $attribues) * 100)) : 0;
              ?>
              <div class="solde-card" style="margin:0">
                <div class="solde-header">
                  <span class="solde-type"><?= esc($solde['libelle'] ?? 'Congé') ?></span>
                  <span class="solde-nums"><strong><?= esc($restants) ?></strong> / <?= esc($attribues) ?> j</span>
                </div>
                <div class="solde-bar">
                  <div class="solde-fill<?= $progress < 30 ? ' warn' : '' ?>" style="width:<?= esc($progress) ?>%"></div>
                </div>
                <div class="solde-label"><?= esc($restants) ?> jours restants · <?= esc($pris) ?> pris</div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty" style="grid-column:1/-1">
              <i class="bi bi-piggy-bank"></i>
              <p>Aucun solde trouvé pour cette année.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Dernières demandes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Mes dernières demandes</h3>
          <a href="/employe/demandes" style="font-size:.8rem;color:var(--forest);text-decoration:none">Voir tout →</a>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Du</th><th>Au</th><th>Durée</th><th>Statut</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (!empty($latestDemandes)): ?>
              <?php foreach ($latestDemandes as $demande): ?>
                <?php
                // Calcul du displayStatus ici, par demande
                $displayStatus = match($demande['statut'] ?? 'en_attente') {
                    'approuvee' => 'approuvée',
                    'refusee'   => 'refusée',
                    'annulee'   => 'annulée',
                    default     => 'en attente',
                };
                ?>
                <tr>
                  <td><span class="type-badge t-annuel"><?= esc($demande['type_libelle'] ?? 'Congé') ?></span></td>
                  <td class="td-muted"><?= esc(date('d/m/Y', strtotime($demande['date_debut']))) ?></td>
                  <td class="td-muted"><?= esc(date('d/m/Y', strtotime($demande['date_fin']))) ?></td>
                  <td class="td-mono"><?= esc((int)($demande['nb_jours'] ?? 0)) ?> j</td>
                  <td><span class="statut s-<?= esc($demande['statut'] ?? 'en_attente') ?>"><?= esc($displayStatus) ?></span></td>
                  <td>
                    <?php if (($demande['statut'] ?? '') === 'en_attente'): ?>
                      <form method="post" action="/employe/demandes/<?= esc((int)$demande['id']) ?>/annuler" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn-sm btn-cancel"><i class="bi bi-x"></i> Annuler</button>
                      </form>
                    <?php else: ?>
                      <span class="td-muted" style="font-size:.75rem">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted)">
                  <i class="bi bi-inbox"></i> Aucune demande pour le moment
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= date('Y') ?> <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>
</div>

<?= $this->endSection() ?>
