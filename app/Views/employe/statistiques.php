<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php
$avatar = strtoupper(substr($prenom ?? '', 0, 1) . substr($nom ?? '', 0, 1));

// donnees pour nos graphes
$labelsType  = json_encode(array_keys($parType ?? []));
$dataType    = json_encode(array_values($parType ?? []));
$labelsJours = json_encode(array_keys($joursByType ?? []));
$dataJours   = json_encode(array_values($joursByType ?? []));
?>

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
        <div class="topbar-title">Historique & Statistiques</div>
        <div class="topbar-breadcrumb">
          <a href="/employe/">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Statistiques
        </div>
      </div>
    </div>

    <div class="content">

      <div class="metrics" style="margin-bottom:1.75rem">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-collection"></i></div></div>
          <div class="metric-val"><?= esc($total ?? 0) ?></div>
          <div class="metric-label">Total demandes</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc($parStatut['en_attente'] ?? 0) ?></div>
          <div class="metric-label">En attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-check-circle"></i></div></div>
          <div class="metric-val"><?= esc($parStatut['approuvee'] ?? 0) ?></div>
          <div class="metric-label">Approuvées</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-red"><i class="bi bi-x-circle"></i></div></div>
          <div class="metric-val"><?= esc($parStatut['refusee'] ?? 0) ?></div>
          <div class="metric-label">Refusées</div>
        </div>
      </div>

      <!-- par type de conge -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;margin-bottom:1.75rem">

        <div class="data-card" style="padding:1.25rem">
          <div class="data-card-head" style="padding:0 0 1rem">
            <h3>Demandes par type de congé</h3>
          </div>
          <canvas id="chartType"></canvas>
        </div>

        <div class="data-card" style="padding:1.25rem">
          <div class="data-card-head" style="padding:0 0 1rem">
            <h3>Jours approuvés par type</h3>
          </div>
          <canvas id="chartJours"></canvas>
        </div>

      </div>

    <!-- historique complet -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Historique complet</h3>
          <span style="font-size:.8rem;color:var(--muted)"><?= esc($total ?? 0) ?> demande(s)</span>
        </div>
        <table class="tbl">
          <thead>
            <tr>
              <th>Type</th>
              <th>Du</th>
              <th>Au</th>
              <th>Durée</th>
              <th>Motif</th>
              <th>Statut</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($demandes)): ?>
              <?php foreach ($demandes as $d): ?>
                <?php
                $displayStatus = match($d['statut'] ?? 'en_attente') {
                    'approuvee' => 'Approuvée',
                    'refusee'   => 'Refusée',
                    'annulee'   => 'Annulée',
                    default     => 'En attente',
                };
                ?>
                <tr>
                  <td><span class="type-badge t-annuel"><?= esc($d['type_libelle'] ?? 'Congé') ?></span></td>
                  <td class="td-muted td-mono"><?= esc(date('d/m/Y', strtotime($d['date_debut']))) ?></td>
                  <td class="td-muted td-mono"><?= esc(date('d/m/Y', strtotime($d['date_fin']))) ?></td>
                  <td class="td-mono"><?= esc($d['nb_jours'] ?? 0) ?> j</td>
                  <td class="td-muted" style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                    <?= esc($d['motif'] ?? '—') ?>
                  </td>
                  <td><span class="statut s-<?= esc($d['statut'] ?? 'en_attente') ?>"><?= esc($displayStatus) ?></span></td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted)">
                  <i class="bi bi-inbox"></i> Aucune demande
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= date('Y') ?> <span>TechMada RH</span></div>
  </div>
</div>

<script src="/assets/chart.min.js"></script>
<script>
const colorsSet = ['#2d5a3d','#5fa876','#b8750a','#1a4f7a','#c0392b','#7a8f80'];

// Graphique par type
new Chart(document.getElementById('chartType'), {
  type: 'bar',
  data: {
    labels: <?= $labelsType ?>,
    datasets: [{
      label: 'Nombre de demandes',
      data: <?= $dataType ?>,
      backgroundColor: colorsSet,
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  }
});

// Graphique jours approuves
new Chart(document.getElementById('chartJours'), {
  type: 'bar',
  data: {
    labels: <?= $labelsJours ?>,
    datasets: [{
      label: 'Jours approuvés',
      data: <?= $dataJours ?>,
      backgroundColor: colorsSet,
      borderWidth: 1
    }]
  },
  options: {
    responsive: true,
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
  }
});
</script>

<?= $this->endSection() ?>