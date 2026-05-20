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
      <div class="sidebar-logo-icon"><i class="bi bi-person-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace responsable</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="/rh/demandes" class="active"><i class="bi bi-inbox"></i> Demandes à traiter
        <span class="nav-badge alert"><?= esc($stats['en_attente'] ?? 0) ?></span>
      </a></li>
      <li><a href="/rh/demandes?statut=approuvee"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="/rh/soldes"><i class="bi bi-people"></i> Soldes employés</a></li>
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-blue"><?= esc($avatar ?: 'RH') ?></div>
        <div>
          <div class="user-name"><?= esc($user['prenom'] ?? '') ?> <?= esc($user['nom'] ?? '') ?></div>
          <div class="user-role">Responsable RH</div>
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
        <div class="topbar-title">Demandes à traiter</div>
        <div class="topbar-breadcrumb"><a href="/rh/demandes">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Demandes</div>
      </div>
      <div class="topbar-actions">
        <span style="font-size:.8rem;color:var(--muted);background:var(--warn-bg);border:1px solid var(--warn-br);border-radius:6px;padding:5px 10px;display:flex;align-items:center;gap:5px;color:var(--warn)">
          <i class="bi bi-hourglass-split"></i> <?= esc($stats['en_attente'] ?? 0) ?> en attente
        </span>
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
      <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap">
        <a href="/rh/demandes" class="btn-sm" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--forest);background:var(--forest);color:var(--white);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">
          Tous (<?= $stats['en_attente'] + $stats['approuvee'] + $stats['refusee'] + $stats['annulee'] ?>)
        </a>
        <a href="/rh/demandes?statut=en_attente" class="btn-sm" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">
          En attente (<?= $stats['en_attente'] ?>)
        </a>
        <a href="/rh/demandes?statut=approuvee" class="btn-sm" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">
          Approuvées (<?= $stats['approuvee'] ?>)
        </a>
        <a href="/rh/demandes?statut=refusee" class="btn-sm" style="padding:6px 14px;border-radius:20px;font-size:.8rem;font-weight:500;border:1.5px solid var(--border);background:var(--white);color:var(--muted);cursor:pointer;text-decoration:none;display:inline-flex;align-items:center">
          Refusées (<?= $stats['refusee'] ?>)
        </a>
        <form method="get" action="/rh/demandes" style="margin-left:auto">
          <select name="departement_id" class="f-select" onchange="this.form.submit()" style="font-size:.8rem;padding:6px 10px;width:auto">
            <option value="">Tous les départements</option>
            <?php foreach ($departements as $dept): ?>
              <option value="<?= esc($dept['id']) ?>" <?= $departement_id == $dept['id'] ? 'selected' : '' ?>>
                <?= esc($dept['nom'] ?? 'Département') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </form>
      </div>

      <!-- Liste des demandes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes les demandes</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Type</th>
              <th>Période</th>
              <th>Durée</th>
              <th>Solde dispo</th>
              <th>Statut</th>
              <th>Actions</th>
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
                $typeClass = 't-annuel';
                if (stripos($demande['type_libelle'], 'maladie') !== false) {
                    $typeClass = 't-maladie';
                } elseif (stripos($demande['type_libelle'], 'spécial') !== false) {
                    $typeClass = 't-special';
                } elseif (stripos($demande['type_libelle'], 'sans solde') !== false) {
                    $typeClass = 't-sans-solde';
                }
                $avatar = strtoupper(substr($demande['prenom'] ?? '', 0, 1) . substr($demande['nom'] ?? '', 0, 1));
                ?>
                <tr>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= esc($avatar) ?></div>
                      <div class="profile-info">
                        <div class="pname"><?= esc($demande['prenom'] ?? '') ?> <?= esc($demande['nom'] ?? '') ?></div>
                        <div class="pdept"><?= esc($demande['dept_libelle'] ?? '—') ?></div>
                      </div>
                    </div>
                  </td>
                  <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($demande['type_libelle'] ?? 'Congé') ?></span></td>
                  <td class="td-muted" style="font-size:.8rem"><?= esc(date('d/m', strtotime($demande['date_debut']))) ?> – <?= esc(date('d/m/Y', strtotime($demande['date_fin']))) ?></td>
                  <td class="td-mono"><strong><?= esc((int)($demande['nb_jours'] ?? 0)) ?></strong> j</td>
                  <td>
                    <span style="font-family:'DM Mono',monospace;font-size:.82rem;color:var(--success);font-weight:500"><?= esc($demande['solde_restant'] ?? 0) ?></span>
                    <span style="font-size:.72rem;color:var(--muted)"> dispo</span>
                  </td>
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
                        <button type="button" class="btn-sm btn-refuse" onclick="showRefusalModal(<?= (int)$demande['id'] ?>, '<?= esc(addslashes($demande['prenom'])) ?> <?= esc(addslashes($demande['nom'])) ?>', <?= (int)($demande['nb_jours'] ?? 0) ?>, '<?= esc(date('d/m', strtotime($demande['date_debut']))) ?>', '<?= esc(date('d/m/Y', strtotime($demande['date_fin']))) ?>', '<?= esc($demande['type_libelle']) ?>')">
                          <i class="bi bi-x-lg"></i> Refuser
                        </button>
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
                <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">
                  <div class="empty">
                    <i class="bi bi-inbox"></i>
                    <p>Aucune demande pour ces critères</p>
                  </div>
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Modal refus -->
      <div id="refusalModal" style="margin-top:1.5rem;display:none">
        <div class="form-section" style="border-color:var(--danger-br);background:var(--danger-bg)">
          <h3 style="color:var(--danger)"><i class="bi bi-x-circle"></i> Confirmer le refus — <span id="modalEmployeeName"></span></h3>
          <div style="font-size:.875rem;color:var(--ink);margin-bottom:1rem">
            Demande de <strong id="modalDays"></strong> jours du <span id="modalDateStart"></span> au <span id="modalDateEnd"></span> · Type : <span id="modalType"></span>
          </div>
          <form method="post" id="refusAlForm">
            <?= csrf_field() ?>
            <div class="f-group">
              <label class="f-label">Commentaire pour l'employé (optionnel)</label>
              <textarea name="commentaire_rh" class="f-textarea" placeholder="Ex : Solde insuffisant, veuillez contacter les RH pour un congé sans solde."></textarea>
            </div>
            <div class="form-actions">
              <button type="submit" class="btn-sm btn-refuse" style="padding:9px 16px;font-size:.875rem">
                <i class="bi bi-x-lg"></i> Confirmer le refus
              </button>
              <button type="button" class="btn-secondary" onclick="hideRefusalModal()">
                <i class="bi bi-arrow-left"></i> Annuler
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>

</div>

<script>
function showRefusalModal(demandeId, employeeName, days, dateStart, dateEnd, typeLibelle) {
  document.getElementById('modalEmployeeName').textContent = employeeName;
  document.getElementById('modalDays').textContent = days;
  document.getElementById('modalDateStart').textContent = dateStart;
  document.getElementById('modalDateEnd').textContent = dateEnd;
  document.getElementById('modalType').textContent = typeLibelle;
  
  const form = document.getElementById('refusAlForm');
  form.action = '/rh/demandes/' + demandeId + '/refuser';
  
  document.getElementById('refusalModal').style.display = 'block';
  
  // Scroll vers le modal
  document.getElementById('refusalModal').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function hideRefusalModal() {
  document.getElementById('refusalModal').style.display = 'none';
}
</script>

<?= $this->endSection() ?>
