<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>

<?php
$oldType = old('type_conge_id');
$oldDebut = old('date_debut');
$oldFin = old('date_fin');
$oldMotif = old('motif');
$errors = session()->getFlashdata('errors') ?? [];
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
      <li><a href="/employe/demandes/create" class="active"><i class="bi bi-plus-circle"></i> Nouvelle demande</a></li>
      <li><a href="/employe/demandes"><i class="bi bi-calendar3"></i> Mes demandes <span class="nav-badge alert"><?= esc($pendingCount ?? 0) ?></span></a></li>
      <li><a href="/employe/profile"><i class="bi bi-person"></i> Mon profil</a></li>
      <li><a href="/employe/calendrier" class="active"><i class="bi bi-calendar-week"></i> Calendrier</a></li>
      <li><a href="/employe/statistiques"><i class="bi bi-bar-chart"></i> Statistiques</a></li>
      <li><a href="/logout"><i class="bi bi-box-arrow-right"></i> Déconnexion</a></li>
      
    </ul>
    <div class="sidebar-user">
      <div class="s-user-row">
        <div class="avatar av-green"><?= esc(strtoupper(substr($prenom ?? '', 0, 1) . substr($nom ?? '', 0, 1)) ?: 'E') ?></div>
        <div>
          <div class="user-name"><?= esc(($prenom ?? '') . ' ' . ($nom ?? '')) ?></div>
          <div class="user-role">Employé</div>
        </div>
      </div>
    </div>
  </aside>

  <div class="main">
    <div class="topbar">
      <div>
        <div class="topbar-title">Nouvelle demande de congé</div>
        <div class="topbar-breadcrumb"><a href="/employe/">Accueil</a> <i class="bi bi-chevron-right" style="font-size:.6rem"></i> Nouvelle demande</div>
      </div>
    </div>

    <div class="content">
      <?php if (! empty($errors)): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(implode(' · ', $errors)) ?>
        </div>
      <?php endif; ?>

      <div class="form-section">
        <h3>Détails de la demande</h3>

        <form method="post" action="/employe/demandes">
          <?= csrf_field() ?>

          <div class="f-group" style="margin-bottom:1rem">
            <label class="f-label" for="type_conge_id">Type de congé <span style="color:var(--danger)">*</span></label>
            <select class="f-select" id="type_conge_id" name="type_conge_id" required>
              <option value="">-- Choisir un type --</option>
              <?php foreach ($typesConge ?? [] as $typeConge): ?>
                <?php
                $solde = null;
                foreach ($soldes ?? [] as $item) {
                    if ((int) $item['type_conge_id'] === (int) $typeConge['id']) {
                        $solde = $item;
                        break;
                    }
                }
                $restants = $solde ? max(0, (int) $solde['jours_attribues'] - (int) $solde['jours_pris']) : (int) ($typeConge['jours_annuels'] ?? 0);
                ?>
                <option value="<?= esc($typeConge['id']) ?>" <?= (string) $oldType === (string) $typeConge['id'] ? 'selected' : '' ?>>
                  <?= esc($typeConge['libelle']) ?> (<?= esc($restants) ?> j restants)
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-grid-2" style="margin-bottom:1rem">
            <div class="f-group">
              <label class="f-label" for="date_debut">Date de début <span style="color:var(--danger)">*</span></label>
              <input type="date" id="date_debut" name="date_debut" class="f-input" value="<?= esc($oldDebut ?? '') ?>" required />
            </div>
            <div class="f-group">
              <label class="f-label" for="date_fin">Date de fin <span style="color:var(--danger)">*</span></label>
              <input type="date" id="date_fin" name="date_fin" class="f-input" value="<?= esc($oldFin ?? '') ?>" required />
            </div>
          </div>

          <div class="f-group" style="margin-bottom:1rem">
            <label class="f-label" for="motif">Motif</label>
            <textarea id="motif" name="motif" class="f-textarea" placeholder="Précisez le motif de votre demande si nécessaire..."><?= esc($oldMotif ?? '') ?></textarea>
            <div class="f-hint">Le motif sera visible par le responsable RH.</div>
          </div>

          <div class="form-actions">
            <button class="btn-forest" type="submit"><i class="bi bi-send"></i> Soumettre la demande</button>
            <a href="/employe/" class="btn-secondary"><i class="bi bi-x"></i> Annuler</a>
          </div>
        </form>
      </div>
    </div>

    <div class="footer-app"><i class="bi bi-c-circle"></i> <?= date('Y') ?> <span>TechMada RH</span></div>
  </div>
</div>

<?= $this->endSection() ?>
