<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$avatar = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
$absencesThisMonth = $absencesThisMonth ?? [];
$conges = $conges ?? [];
$stats = $stats ?? ['employes' => 0, 'pending' => 0, 'approved' => 0, 'refused' => 0];
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
      <li><a href="/admin" class="active"><i class="bi bi-speedometer2"></i> Vue d'ensemble</a></li>
      <li>
        <a href="/rh/demandes">
          <i class="bi bi-inbox"></i> Toutes les demandes
          <span class="nav-badge alert"><?= esc($stats['pending'] ?? 0) ?></span>
        </a>
      </li>
      <li><a href="/admin/employes"><i class="bi bi-people"></i> Employés</a></li>
      <li><a href="/admin/departements"><i class="bi bi-building"></i> Départements</a></li>
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
        <div class="topbar-title">Vue d'ensemble</div>
        <div class="topbar-breadcrumb">Administration</div>
      </div>
      <div class="topbar-actions">
        <a href="/admin/employes/create" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-person-plus"></i> Ajouter un employé</a>
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

      <!-- Métriques admin -->
      <div class="metrics">
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-forest"><i class="bi bi-people"></i></div></div>
          <div class="metric-val"><?= esc($stats['employes'] ?? 0) ?></div>
          <div class="metric-label">Employés actifs</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-amber"><i class="bi bi-hourglass-split"></i></div></div>
          <div class="metric-val"><?= esc($stats['pending'] ?? 0) ?></div>
          <div class="metric-label">Demandes en attente</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-green"><i class="bi bi-calendar-check"></i></div></div>
          <div class="metric-val"><?= esc($stats['approved'] ?? 0) ?></div>
          <div class="metric-label">Approuvées ce mois</div>
        </div>
        <div class="metric">
          <div class="metric-top"><div class="metric-icon mi-blue"><i class="bi bi-building"></i></div></div>
          <div class="metric-val">4</div>
          <div class="metric-label">Départements</div>
        </div>
      </div>

      <!-- Graphiques statistiques -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-bottom:1.5rem">
        <!-- Graphique 1: Congés par mois -->
        <div class="data-card" style="margin:0;padding:1rem">
          <div class="data-card-head" style="margin-bottom:1rem"><h3 style="margin:0">Congés par mois</h3></div>
          <canvas id="monthlyChart" style="max-height:220px"></canvas>
        </div>

        <!-- Graphique 2: Congés par jour de semaine -->
        <div class="data-card" style="margin:0;padding:1rem">
          <div class="data-card-head" style="margin-bottom:1rem"><h3 style="margin:0">Congés par jour</h3></div>
          <canvas id="weeklyChart" style="max-height:220px"></canvas>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 320px;gap:1.5rem;align-items:start">

        <!-- Demandes récentes -->
        <div class="data-card" style="margin:0">
          <div class="data-card-head">
            <h3>Demandes récentes</h3>
            <a href="/rh/demandes" style="font-size:.8rem;color:var(--forest);text-decoration:none">Tout voir →</a>
          </div>
          <?php if (!empty($conges)): ?>
            <table class="tbl">
              <thead>
                <tr><th>Employé</th><th>Type</th><th>Durée</th><th>Statut</th></tr>
              </thead>
              <tbody>
                <?php foreach (array_slice($conges, 0, 3) as $cong): ?>
                  <?php
                  $displayStatus = match($cong['statut'] ?? 'en_attente') {
                      'approuvee' => 'approuvée',
                      'refusee'   => 'refusée',
                      'annulea'   => 'annulée',
                      default     => 'en attente',
                  };
                  $statusClass = match($cong['statut'] ?? 'en_attente') {
                      'approuvee' => 's-approuvee',
                      'refusee'   => 's-refusee',
                      'annulea'   => 's-annulee',
                      default     => 's-attente',
                  };
                  ?>
                  <tr>
                    <td><div style="display:flex;align-items:center;gap:7px"><div class="avatar av-green" style="width:28px;height:28px;font-size:.62rem"><?= strtoupper(substr($cong['prenom'] ?? '', 0, 1)) . strtoupper(substr($cong['nom'] ?? '', 0, 1)) ?></div><span class="td-name" style="font-size:.84rem"><?= esc($cong['prenom'] ?? '') ?> <?= esc($cong['nom'] ?? '') ?></span></div></td>
                    <td><span class="type-badge t-annuel"><?= esc($cong['type_libelle'] ?? '') ?></span></td>
                    <td class="td-mono"><?= esc($cong['nb_jour'] ?? 0) ?> j</td>
                    <td><span class="statut <?= esc($statusClass) ?>"><?= esc($displayStatus) ?></span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <div style="padding:1rem;text-align:center;color:#666;font-size:.85rem">Aucune demande</div>
          <?php endif; ?>
        </div>

        <!-- Absents du jour + soldes critiques -->
        <div style="display:flex;flex-direction:column;gap:1rem">
          <div class="data-card" style="margin:0">
            <div class="data-card-head"><h3><i class="bi bi-person-slash" style="color:var(--muted);margin-right:5px"></i>Absents (ce mois)</h3></div>
            <div style="padding:.75rem 1.1rem;display:flex;flex-direction:column;gap:.6rem">
              <?php if (!empty($absencesThisMonth)): ?>
                <?php foreach (array_slice($absencesThisMonth, 0, 3) as $abs): ?>
                  <div style="display:flex;align-items:center;gap:8px">
                    <div class="avatar av-green" style="width:30px;height:30px;font-size:.65rem"><?= strtoupper(substr($abs['prenom'] ?? '', 0, 1)) . strtoupper(substr($abs['nom'] ?? '', 0, 1)) ?></div>
                    <div><div style="font-size:.83rem;font-weight:500;color:var(--ink)"><?= esc($abs['prenom'] ?? '') ?> <?= esc($abs['nom'] ?? '') ?></div><div style="font-size:.72rem;color:var(--muted)"><?= esc($abs['libelle'] ?? '') ?> · <?= esc(date('d/m', strtotime($abs['date_fin']))) ?></div></div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div style="text-align:center;color:#999;font-size:.8rem;padding:.5rem">Aucune absence ce mois</div>
              <?php endif; ?>
            </div>
          </div>
          <div class="flash flash-warn" style="margin:0">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span style="font-size:.8rem">Gérez les soldes et les demandes via le menu</span>
          </div>
        </div>

      </div>

    </div>
  </div>

</div>

<script src="/lib/chart.min.js"></script>
<script>
  // Couleurs pour les mois
  const monthColors = [
    '#3498db', // Bleu - Janvier
    '#2ecc71', // Vert - Février
    '#f39c12', // Orange - Mars
    '#9b59b6', // Violet - Avril
    '#e74c3c', // Rouge - Mai
    '#1abc9c', // Turquoise - Juin
    '#34495e', // Gris bleu - Juillet
    '#e67e22', // Orange foncé - Août
    '#16a085', // Vert foncé - Septembre
    '#8e44ad', // Violet foncé - Octobre
    '#c0392b', // Rouge foncé - Novembre
    '#f1c40f'  // Jaune - Décembre
  ];

  // Couleurs pour les jours
  const dayColors = [
    '#3498db', // Bleu - Lundi
    '#2ecc71', // Vert - Mardi
    '#f39c12', // Orange - Mercredi
    '#9b59b6', // Violet - Jeudi
    '#e74c3c', // Rouge - Vendredi
    '#1abc9c', // Turquoise - Samedi
    '#34495e'  // Gris bleu - Dimanche
  ];

  // Graphique 1: Congés par mois
  const ctxMonthly = document.getElementById('monthlyChart');
  if (ctxMonthly && typeof Chart !== 'undefined') {
    const monthlyData = <?= json_encode($congesByMonth ?? []) ?>;
    const maxMonthly = Math.max(...monthlyData, 0);
    
    new Chart(ctxMonthly, {
      type: 'bar',
      data: {
        labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
        datasets: [{
        //   label: 'Congés approuvés',
          data: monthlyData,
          backgroundColor: monthColors,
          borderColor: monthColors,
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: true }
        },
        scales: {
          y: { 
            beginAtZero: true,
            max: maxMonthly + 2,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  }

  // Graphique 2: Congés par jour de semaine
  const ctxWeekly = document.getElementById('weeklyChart');
  if (ctxWeekly && typeof Chart !== 'undefined') {
    const weeklyData = <?= json_encode($congesByDay ?? []) ?>;
    const maxWeekly = Math.max(...weeklyData, 0);
    
    new Chart(ctxWeekly, {
      type: 'bar',
      data: {
        labels: <?= json_encode($daysOfWeek ?? ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim']) ?>,
        datasets: [{
        //   label: 'Congés par jour',
          data: weeklyData,
          backgroundColor: dayColors,
          borderColor: dayColors,
          borderWidth: 0
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
          legend: { display: true }
        },
        scales: {
          y: { 
            beginAtZero: true,
            max: maxWeekly + 2,
            ticks: { stepSize: 1 }
          }
        }
      }
    });
  }
</script>

<?= $this->endSection() ?>
