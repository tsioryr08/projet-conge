<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

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
      <li><a href="/employe/demandes" class="active"><i class="bi bi-calendar3"></i> Mes demandes</a></li>
      <li><a href="/logout"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
    </ul>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Mes demandes de congé</div>
        <div class="topbar-breadcrumb"><a href="/employe/">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Mes demandes</div>
      </div>
      <div class="topbar-actions">
        <a href="/employe/demandes/create" class="btn-forest" style="padding:7px 14px;font-size:.82rem"><i class="bi bi-plus-lg"></i> Nouvelle demande</a>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('success')): ?>
        <div class="flash flash-success"><i class="bi bi-check-circle-fill"></i> <?= esc(session()->getFlashdata('success')) ?></div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error"><i class="bi bi-exclamation-circle-fill"></i> <?= esc(session()->getFlashdata('error')) ?></div>
      <?php endif; ?>

      <div class="data-card">
        <div class="data-card-head">
          <h3>Toutes mes demandes</h3>
        </div>
        <table class="tbl">
          <thead>
            <tr><th>Type</th><th>Début</th><th>Fin</th><th>Durée</th><th>Statut</th><th>Commentaire RH</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php if (! empty($demandes)): ?>
              <?php foreach ($demandes as $demande): ?>
                <?php
                $status = $demande['statut'] ?? 'en_attente';
                $badgeClass = match ($status) {
                    'approuvee' => 's-approuvee',
                    'refusee' => 's-refusee',
                    'annulee' => 's-annulee',
                    default => 's-attente',
                };
                ?>
                <tr>
                  <td><span class="type-badge t-annuel"><?= esc($demande['type_libelle'] ?? 'Congé') ?></span></td>
                  <td class="td-muted"><?= esc(date('d/m/Y', strtotime($demande['date_debut']))) ?></td>
                  <td class="td-muted"><?= esc(date('d/m/Y', strtotime($demande['date_fin']))) ?></td>
                  <td class="td-mono"><?= esc((int) ($demande['nb_jours'] ?? 0)) ?> j</td>
                  <td><span class="statut <?= esc($badgeClass) ?>"><?= esc($status) ?></span></td>
                  <td class="td-muted" style="font-size:.78rem"><?= esc($demande['commentaire_rh'] ?? '—') ?></td>
                  <td>
                    <?php if ($status === 'en_attente'): ?>
                      <form method="post" action="/employe/demandes/<?= esc((int) $demande['id']) ?>/annuler" style="display:inline">
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
                <td colspan="7" style="text-align:center;padding:2rem;color:var(--muted)"><i class="bi bi-inbox" style="display:block;font-size:2rem;opacity:.3;margin-bottom:.5rem"></i> Aucune demande pour le moment</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= date('Y') ?> <span>TechMada RH</span></div>
  </div>
</div>

<?= $this->endSection() ?>
