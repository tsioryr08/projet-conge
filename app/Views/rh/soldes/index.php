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
      <div class="sidebar-logo-icon"><i class="bi bi-shield-check"></i></div>
      <div class="sidebar-brand-name">TechMada RH<span>Espace RH</span></div>
    </div>
    <div class="sidebar-section">Menu</div>
    <ul class="sidebar-nav">
      <li><a href="/rh/demandes"><i class="bi bi-file-earmark-text"></i> Demandes</a></li>
      <li><a href="/rh/soldes" class="active"><i class="bi bi-bar-chart"></i> Soldes de congés</a></li>
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
        <div class="topbar-title">Soldes de congés</div>
        <div class="topbar-breadcrumb"><a href="/rh/soldes">Soldes</a></div>
      </div>
    </div>

    <div class="content">
      <!-- Filtres -->
      <div class="data-card" style="margin-bottom:1.5rem">
        <form method="get" action="/rh/soldes" style="padding:1.25rem;display:flex;gap:1rem;align-items:flex-end;flex-wrap:wrap">
          <div style="display:flex;gap:1rem;flex:1;min-width:300px">
            <div style="flex:1">
              <label class="f-label">Année</label>
              <select name="year" class="f-select">
                <?php for ($i = date('Y'); $i >= date('Y') - 3; $i--): ?>
                  <option value="<?= esc($i) ?>" <?= $year == $i ? 'selected' : '' ?>><?= esc($i) ?></option>
                <?php endfor; ?>
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

      <!-- Liste des soldes -->
      <div class="data-card">
        <div class="data-card-head">
          <h3>Soldes de congés — <?= esc($year) ?> (<?= esc(count($soldes)) ?> entrées)</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr>
              <th>Employé</th>
              <th>Département</th>
              <th>Type de congé</th>
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
                ?>
                <tr>
                  <td class="td-name">
                    <div style="font-weight:500"><?= esc($solde['prenom'] ?? '') ?> <?= esc($solde['nom'] ?? '') ?></div>
                  </td>
                  <td class="td-muted"><?= esc($solde['dept_libelle'] ?? '—') ?></td>
                  <td><span class="type-badge t-annuel"><?= esc($solde['type_libelle'] ?? 'Congé') ?></span></td>
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
                <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)">
                  <i class="bi bi-inbox"></i> Aucun solde trouvé
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
