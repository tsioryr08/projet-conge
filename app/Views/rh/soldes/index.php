<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$avatar = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
$soldes = $soldes ?? [];
$departements = $departements ?? [];
$year = $year ?? date('Y');
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
      <li><a href="/rh/demandes"><i class="bi bi-inbox"></i> Demandes à traiter</a></li>
      <li><a href="/rh/demandes?statut=approuvee"><i class="bi bi-archive"></i> Historique</a></li>
      <li><a href="/rh/soldes" class="active"><i class="bi bi-people"></i> Soldes employés</a></li>
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
        <div class="topbar-title">Soldes employés</div>
        <div class="topbar-breadcrumb"><a href="/rh/soldes">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Soldes</div>
      </div>
    </div>

    <div class="content">
      <!-- Filtres -->
      <div style="display:flex;gap:8px;margin-bottom:1.25rem;flex-wrap:wrap;align-items:flex-end">
        <form method="get" action="/rh/soldes" style="display:flex;gap:8px;align-items:flex-end">
          <div>
            <label class="f-label">Année</label>
            <select name="year" class="f-select" onchange="this.form.submit()" style="font-size:.8rem;padding:6px 10px;width:auto">
              <?php for ($i = date('Y'); $i >= date('Y') - 3; $i--): ?>
                <option value="<?= esc($i) ?>" <?= $year == $i ? 'selected' : '' ?>><?= esc($i) ?></option>
              <?php endfor; ?>
            </select>
          </div>
          <input type="hidden" name="departement_id" value="<?= $departement_id ?>">
        </form>
        <form method="get" action="/rh/soldes" style="margin-left:auto">
          <select name="departement_id" class="f-select" onchange="this.form.submit()" style="font-size:.8rem;padding:6px 10px;width:auto">
            <option value="">Tous les départements</option>
            <?php foreach ($departements as $dept): ?>
              <option value="<?= esc($dept['id']) ?>" <?= $departement_id == $dept['id'] ? 'selected' : '' ?>>
                <?= esc($dept['nom'] ?? 'Département') ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="year" value="<?= $year ?>">
        </form>
      </div>

      <!-- Liste des soldes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Soldes de congés — <?= esc($year) ?></h3>
        </div>
        <table class="tbl">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Type</th>
              <th>Attribués</th>
              <th>Pris</th>
              <th>Restants</th>
              <th>Taux</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($soldes)): ?>
              <?php foreach ($soldes as $solde): ?>
                <?php
                $attribues = (int)($solde['jours_attribues'] ?? 0);
                $pris = (int)($solde['jours_pris'] ?? 0);
                $restants = max(0, $attribues - $pris);
                $taux = $attribues > 0 ? max(0, min(100, ($pris / $attribues) * 100)) : 0;
                $typeClass = 't-annuel';
                if (stripos($solde['type_libelle'], 'maladie') !== false) {
                    $typeClass = 't-maladie';
                } elseif (stripos($solde['type_libelle'], 'spécial') !== false) {
                    $typeClass = 't-special';
                } elseif (stripos($solde['type_libelle'], 'sans solde') !== false) {
                    $typeClass = 't-sans-solde';
                }
                $avatar = strtoupper(substr($solde['prenom'] ?? '', 0, 1) . substr($solde['nom'] ?? '', 0, 1));
                ?>
                <tr>
                  <td>
                    <div class="profile-row">
                      <div class="avatar av-green" style="width:32px;height:32px;font-size:.7rem"><?= esc($avatar) ?></div>
                      <div class="profile-info">
                        <div class="pname"><?= esc($solde['prenom'] ?? '') ?> <?= esc($solde['nom'] ?? '') ?></div>
                        <div class="pdept"><?= esc($solde['dept_libelle'] ?? '—') ?></div>
                      </div>
                    </div>
                  </td>
                  <td><span class="type-badge <?= esc($typeClass) ?>"><?= esc($solde['type_libelle'] ?? 'Congé') ?></span></td>
                  <td class="td-mono"><strong><?= esc($attribues) ?></strong> j</td>
                  <td class="td-mono" style="color:var(--danger)"><strong><?= esc($pris) ?></strong> j</td>
                  <td class="td-mono" style="color:var(--success)"><strong><?= esc($restants) ?></strong> j</td>
                  <td>
                    <div style="display:flex;align-items:center;gap:.5rem">
                      <div style="flex:1;height:6px;background:var(--mint);border-radius:3px;overflow:hidden;min-width:50px">
                        <div style="height:100%;background:<?= $taux > 75 ? 'var(--danger)' : (($taux > 50 ? 'var(--warn)' : 'var(--forest2)')); ?>;width:<?= esc($taux) ?>%"></div>
                      </div>
                      <span class="td-mono" style="font-size:.75rem;min-width:30px"><?= esc(number_format($taux, 0)) ?>%</span>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="6" style="text-align:center;padding:2rem;color:var(--muted)">
                  <div class="empty">
                    <i class="bi bi-inbox"></i>
                    <p>Aucun solde trouvé</p>
                  </div>
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
