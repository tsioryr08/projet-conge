<?= $this->extend('layouts/app') ?>
<?= $this->section('content') ?>

<div class="auth-page geo-bg">
  <div class="auth-split">
    <!-- Panneau gauche -->
    <div class="auth-left">
      <div>
        <p class="auth-left-brand">TechMada RH<span>Gestion des congés</span></p>
        <p class="auth-left-text" style="margin-top:2rem">
          <strong>Bienvenue sur votre espace RH.</strong>
          Gérez vos demandes de congés, consultez votre solde et suivez l'état de vos demandes en temps réel.
        </p>
      </div>
      <div style="border-top:1px solid rgba(255,255,255,.1);padding-top:1.5rem">
        <div style="font-size:.65rem;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.25);margin-bottom:8px">Comptes de démonstration</div>
        <div style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12);border-radius:8px;padding:8px 12px;display:flex;align-items:center;gap:10px;margin-bottom:8px">
          <i class="bi bi-person" style="color:var(--leaf)"></i>
          <div>
            <div style="font-size:.8rem;font-weight:500;color:var(--white)">Admin</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.4);font-family:monospace">admin@techmada.mg · admin123</div>
          </div>
        
           <div>
            <div style="font-size:.8rem;font-weight:500;color:var(--white)">RH</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.4);font-family:monospace">rh@techmada.mg · rh123</div>
          </div>
          <div>
            <div style="font-size:.8rem;font-weight:500;color:var(--white)">Employé</div>
            <div style="font-size:.72rem;color:rgba(255,255,255,.4);font-family:monospace">marie@techmada.mg · employe123</div>
          </div>
         
        </div>
      </div>
    </div>

    <!-- Panneau droit -->
    <div class="auth-right">
      <p class="auth-title">Connexion</p>
      <p class="auth-sub">Entrez vos identifiants pour accéder à votre espace.</p>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="flash flash-error">
          <i class="bi bi-exclamation-circle-fill"></i>
          <?= esc(session()->getFlashdata('error')) ?>
        </div>
      <?php endif; ?>

      <form method="post" action="<?= site_url('login') ?>">
        <?= csrf_field() ?>
        <div class="f-group">
          <label for="email" class="f-label">Adresse email</label>
          <input id="email" type="email" class="f-input" name="email"
                 placeholder="vous@techmada.mg" value="marie@techmada.mg" required />
        </div>
        <div class="f-group">
          <label for="password" class="f-label">Mot de passe</label>
          <input id="password" type="password" class="f-input" name="password"
                 placeholder="••••••••" value="employe123" required />
        </div>
        <button type="submit" class="btn-primary" style="margin-top:.5rem">
          Se connecter <i class="bi bi-arrow-right-short"></i>
        </button>
      </form>

    </div><!-- /.auth-right -->
  </div><!-- /.auth-split -->
</div><!-- /.auth-page -->

<?= $this->endSection() ?>