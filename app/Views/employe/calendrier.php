<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<?php $avatar = strtoupper(substr($prenom ?? '', 0, 1) . substr($nom ?? '', 0, 1)); ?>

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
      <li><a href="/employe/calendrier" class="active"><i class="bi bi-calendar-week"></i> Calendrier</a></li>
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
        <div class="topbar-title">Calendrier des congés</div>
        <div class="topbar-breadcrumb">
          <a href="/employe/">Accueil</a>
          <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Calendrier
        </div>
      </div>
      <div class="topbar-actions">
        <a href="/employe/demandes/create" class="btn-forest" style="padding:7px 14px;font-size:.82rem">
          <i class="bi bi-plus-lg"></i> Nouvelle demande
        </a>
      </div>
    </div>

    <div class="content">

      <!-- Légende -->
      <div style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1.25rem">
        <span style="display:flex;align-items:center;gap:6px;font-size:.8rem">
          <span style="width:12px;height:12px;border-radius:3px;background:#b8750a;display:inline-block"></span> En attente
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:.8rem">
          <span style="width:12px;height:12px;border-radius:3px;background:#1e6b3f;display:inline-block"></span> Approuvée
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:.8rem">
          <span style="width:12px;height:12px;border-radius:3px;background:#c0392b;display:inline-block"></span> Refusée
        </span>
        <span style="display:flex;align-items:center;gap:6px;font-size:.8rem">
          <span style="width:12px;height:12px;border-radius:3px;background:#7a8f80;display:inline-block"></span> Annulée
        </span>
      </div>

      <!-- Calendrier -->
      <div class="data-card" style="padding:1.25rem">
        <div id="calendar"></div>
      </div>

      <!-- Popup détail -->
      <div id="event-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:999;align-items:center;justify-content:center">
        <div style="background:#fff;border-radius:12px;padding:1.5rem;max-width:380px;width:90%;box-shadow:0 8px 32px rgba(0,0,0,.18)">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem">
            <h3 id="modal-title" style="margin:0;font-size:1rem;font-family:'Playfair Display',serif"></h3>
            <button onclick="document.getElementById('event-modal').style.display='none'"
              style="background:none;border:none;font-size:1.3rem;cursor:pointer;color:#7a8f80">&times;</button>
          </div>
          <div id="modal-body" style="font-size:.875rem;color:#1c2b1e;line-height:1.8"></div>
        </div>
      </div>

    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= date('Y') ?> <span>TechMada RH</span></div>
  </div>
</div>

<!-- Remplace le script et ajoute le CSS -->
<link rel="stylesheet" href="/assets/fullcalendar/index.global.min.css">
<script src="/assets/fullcalendar/index.global.min.js"></script>
<style>
  .fc { font-family: 'DM Sans', sans-serif; font-size: .875rem; }
  .fc-toolbar-title { font-family: 'Playfair Display', serif; font-size: 1.1rem !important; }
  .fc-button { background: var(--forest) !important; border-color: var(--forest) !important; font-size: .8rem !important; }
  .fc-button:hover { background: var(--forest2) !important; }
  .fc-button-active { background: var(--ink) !important; border-color: var(--ink) !important; }
  .fc-event { cursor: pointer; font-size: .75rem; border: none !important; padding: 2px 5px; border-radius: 4px !important; }
  .fc-daygrid-day-number { color: var(--ink); text-decoration: none; }
  .fc-day-today { background: var(--mint) !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const events = <?= $events ?>;

  const calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
    initialView: 'dayGridMonth',
    locale: 'fr',
    firstDay: 1,
    headerToolbar: {
      left:   'prev,next today',
      center: 'title',
      right:  'dayGridMonth,timeGridWeek,listWeek'
    },
    buttonText: {
      today:       'Aujourd\'hui',
      month:       'Mois',
      week:        'Semaine',
      list:        'Liste',
    },
    events: events,
    eventClick: function(info) {
      const p = info.event.extendedProps;
      document.getElementById('modal-title').textContent = info.event.title;
      document.getElementById('modal-body').innerHTML =
        '<b>Statut :</b> '    + (p.statut   || '—') + '<br>' +
        '<b>Durée :</b> '     + (p.nb_jours || '—') + ' jour(s)<br>' +
        '<b>Motif :</b> '     + (p.motif    || 'Aucun motif');
      document.getElementById('event-modal').style.display = 'flex';
    },
  });

  calendar.render();
});
</script>

<?= $this->endSection() ?>