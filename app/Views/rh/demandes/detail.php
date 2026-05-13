<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$avatar = strtoupper(substr($user['prenom'] ?? '', 0, 1) . substr($user['nom'] ?? '', 0, 1));
$demande = $demande ?? [];

$displayStatus = match($demande['statut'] ?? 'en_attente') {
    'approuvee' => 'approuvée',
    'refusee'   => 'refusée',
    'annulee'   => 'annulée',
    default     => 'en attente',
};
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
        <div class="topbar-title">Détail de la demande</div>
        <div class="topbar-breadcrumb">
          <a href="/rh/demandes">Demandes</a> /
          Demande #<?= esc((int)($demande['id'] ?? 0)) ?>
        </div>
      </div>
      <div class="topbar-actions">
        <a href="/rh/demandes" class="btn-secondary">
          <i class="bi bi-arrow-left"></i> Retour
        </a>
      </div>
    </div>

    <div class="content">
      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <div style="display:grid;grid-template-columns:1fr 350px;gap:1.5rem">

        <!-- INFO PRINCIPALE -->
        <div>
          <div class="data-card">
            <div class="data-card-head">
              <div>
                <h3>Informations de la demande</h3>
                <div style="font-size:.8rem;color:var(--muted);margin-top:.3rem">
                  Demande #<?= esc((int)($demande['id'] ?? 0)) ?> · Créée le <?= esc(date('d/m/Y à H:i', strtotime($demande['created_at'] ?? ''))) ?>
                </div>
              </div>
            </div>
            <div style="padding:1.5rem">
              <!-- Employé -->
              <div style="margin-bottom:1.75rem;padding-bottom:1.75rem;border-bottom:1px solid var(--border)">
                <div style="font-size:.75rem;font-weight:500;text-transform:uppercase;color:var(--muted);margin-bottom:.5rem">Employé</div>
                <div class="profile-row">
                  <div class="avatar av-green" style="width:44px;height:44px"><?= esc(strtoupper(substr($demande['prenom'] ?? '', 0, 1) . substr($demande['nom'] ?? '', 0, 1))) ?></div>
                  <div class="profile-info">
                    <div class="pname"><?= esc($demande['prenom'] ?? '') ?> <?= esc($demande['nom'] ?? '') ?></div>
                    <div class="pdept"><?= esc($demande['dept_libelle'] ?? 'Département inconnu') ?></div>
                  </div>
                </div>
              </div>

              <!-- Dates et durée -->
              <div style="margin-bottom:1.75rem;padding-bottom:1.75rem;border-bottom:1px solid var(--border)">
                <div style="font-size:.75rem;font-weight:500;text-transform:uppercase;color:var(--muted);margin-bottom:.75rem">Période</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem">
                  <div>
                    <div class="f-label">Date de début</div>
                    <div style="padding:9px 12px;background:var(--cream);border-radius:6px;font-size:.875rem"><?= esc(date('d/m/Y', strtotime($demande['date_debut'] ?? ''))) ?></div>
                  </div>
                  <div>
                    <div class="f-label">Date de fin</div>
                    <div style="padding:9px 12px;background:var(--cream);border-radius:6px;font-size:.875rem"><?= esc(date('d/m/Y', strtotime($demande['date_fin'] ?? ''))) ?></div>
                  </div>
                </div>
              </div>

              <!-- Type et motif -->
              <div style="margin-bottom:1.75rem;padding-bottom:1.75rem;border-bottom:1px solid var(--border)">
                <div style="font-size:.75rem;font-weight:500;text-transform:uppercase;color:var(--muted);margin-bottom:.75rem">Type</div>
                <div style="display:flex;align-items:center;gap:.75rem;margin-bottom:1rem">
                  <span class="type-badge t-annuel"><?= esc($demande['type_libelle'] ?? 'Congé') ?></span>
                  <span style="font-family:'DM Mono',monospace;font-size:.8rem;color:var(--muted)"><?= esc((int)($demande['nb_jours'] ?? 0)) ?> jour(s) ouvrable(s)</span>
                </div>
                <?php if (!empty($demande['motif'])): ?>
                  <div style="margin-top:1rem">
                    <div class="f-label">Motif</div>
                    <div style="padding:.75rem;background:var(--cream);border-radius:6px;font-size:.85rem;line-height:1.6"><?= esc($demande['motif'] ?? '—') ?></div>
                  </div>
                <?php endif; ?>
              </div>

              <!-- Commentaire RH si existe -->
              <?php if (!empty($demande['commentaire_rh'])): ?>
                <div style="margin-bottom:1.75rem;padding-bottom:1.75rem;border-bottom:1px solid var(--border);background:var(--mint);padding:1rem;border-radius:8px">
                  <div style="font-size:.75rem;font-weight:500;text-transform:uppercase;color:var(--forest);margin-bottom:.5rem">Commentaire RH</div>
                  <div style="font-size:.85rem;color:var(--forest);line-height:1.6"><?= esc($demande['commentaire_rh'] ?? '') ?></div>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <!-- SIDEBAR ACTION -->
        <div>
          <div class="data-card">
            <div class="data-card-head">
              <h3>Statut</h3>
            </div>
            <div style="padding:1.5rem;text-align:center">
              <div style="margin-bottom:1.5rem">
                <span class="statut s-<?= esc($demande['statut'] ?? 'en_attente') ?>" style="font-size:.85rem;padding:8px 12px">
                  <?= esc($displayStatus) ?>
                </span>
              </div>

              <?php if (($demande['statut'] ?? '') === 'en_attente'): ?>
                <!-- APPROUVER -->
                <form method="post" action="/rh/demandes/<?= esc((int)$demande['id']) ?>/approuver" style="margin-bottom:1rem">
                  <?= csrf_field() ?>
                  <div style="margin-bottom:1rem">
                    <label class="f-label">Commentaire (optionnel)</label>
                    <textarea name="commentaire_rh" class="f-textarea" style="min-height:60px;resize:vertical;margin-bottom:.75rem" placeholder="Commentaire à ajouter..."></textarea>
                  </div>
                  <button type="submit" class="btn-forest" style="width:100%;background:var(--success);margin-bottom:.5rem">
                    <i class="bi bi-check-lg"></i> Approuver
                  </button>
                </form>

                <!-- REFUSER -->
                <form method="post" action="/rh/demandes/<?= esc((int)$demande['id']) ?>/refuser">
                  <?= csrf_field() ?>
                  <div style="margin-bottom:1rem">
                    <label class="f-label">Raison du refus (optionnel)</label>
                    <textarea name="commentaire_rh" class="f-textarea" style="min-height:60px;resize:vertical" placeholder="Motif du refus..."></textarea>
                  </div>
                  <button type="submit" class="btn-forest" style="width:100%;background:var(--danger)">
                    <i class="bi bi-x-lg"></i> Refuser
                  </button>
                </form>
              <?php else: ?>
                <div style="padding:1rem;background:var(--cream);border-radius:8px;font-size:.85rem;color:var(--muted);line-height:1.6;text-align:left">
                  <p style="margin:0"><strong>Cette demande a déjà été traitée.</strong></p>
                  <p style="margin:.5rem 0 0;font-size:.8rem">Vous ne pouvez plus la modifier.</p>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>

      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> 2025 <span>TechMada RH</span> — Projet CodeIgniter 4</div>
  </div>

</div>

<?= $this->endSection() ?>
