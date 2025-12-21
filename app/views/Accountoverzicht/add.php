<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/update.css">
<div class="container mt-4">
  <h2>Nieuwe klant toevoegen</h2>
  <?php if (!empty($data['error'])): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($data['error']); ?></div>
    <?php if (!empty($data['show_force'])): ?>
      <form method="post">
        <?php foreach ($data as $key => $value):
          if (in_array($key, ['error', 'show_force'])) continue; ?>
          <input type="hidden" name="<?= htmlspecialchars($key); ?>" value="<?= $value !== null ? htmlspecialchars((string)$value) : ''; ?>">
        <?php endforeach; ?>
        <input type="hidden" name="force_email" value="1">
        <button type="submit" class="btn btn-warning mb-3">Verder en e-mail aanpassen</button>
        <a href="<?= URLROOT; ?>/Accountoverzicht/add" class="btn btn-secondary mb-3">Annuleren</a>
      </form>
    <?php endif; ?>
  <?php endif; ?>
  <form method="post" id="addForm" novalidate>
    <div id="clientError" class="alert alert-danger" style="display:none;"></div>
    <div class="row g-3">
      <div class="col-12 col-md-4 mb-3">
        <label>Voornaam</label>
        <input type="text" name="voornaam" class="form-control" value="<?= $data['voornaam'] !== null ? htmlspecialchars((string)$data['voornaam']) : ''; ?>">
      </div>
      <div class="col-12 col-md-2 mb-3">
        <label>Tussenvoegsel</label>
        <input type="text" name="tussenvoegsel" class="form-control" value="<?= $data['tussenvoegsel'] !== null ? htmlspecialchars((string)$data['tussenvoegsel']) : ''; ?>">
      </div>
      <div class="col-12 col-md-4 mb-3">
        <label>Achternaam</label>
        <input type="text" name="achternaam" class="form-control" value="<?= $data['achternaam'] !== null ? htmlspecialchars((string)$data['achternaam']) : ''; ?>">
      </div>
      <div class="col-12 col-md-4 mb-3">
        <label>Email *</label>
        <input type="email" name="email" class="form-control" value="<?= $data['email'] !== null ? htmlspecialchars((string)$data['email']) : ''; ?>" required>
      </div>
      <div class="col-12 col-md-4 mb-3">
        <label>Telefoon</label>
        <input type="text" name="telefoon" class="form-control" value="<?= $data['telefoon'] !== null ? htmlspecialchars((string)$data['telefoon']) : ''; ?>">
      </div>
      <div class="col-12 col-md-4 mb-3 select-responsive">
        <label>Rol *</label>
        <div class="select-wrapper">
          <?php if ($_SESSION['user_role'] === 'instructeur'): ?>
            <input type="hidden" name="rol" value="klant">
            <input type="text" class="form-control" value="Klant" disabled>
          <?php else: ?>
            <select name="rol" class="form-control" required>
              <option value="klant" <?= $data['rol'] === 'klant' ? 'selected' : '' ?>>Klant</option>
              <option value="instructeur" <?= $data['rol'] === 'instructeur' ? 'selected' : '' ?>>Instructeur</option>
              <option value="beheerder" <?= $data['rol'] === 'beheerder' ? 'selected' : '' ?>>Beheerder</option>
            </select>
          <?php endif; ?>
        </div>
      </div>
      <div class="col-12 col-md-4 mb-3">
        <label>Adres</label>
        <input type="text" name="adres" class="form-control" value="<?= $data['adres'] !== null ? htmlspecialchars((string)$data['adres']) : ''; ?>">
      </div>
      <div class="col-12 col-md-4 mb-3">
        <label>Woonplaats</label>
        <input type="text" name="woonplaats" class="form-control" value="<?= $data['woonplaats'] !== null ? htmlspecialchars((string)$data['woonplaats']) : ''; ?>">
      </div>
      <div class="col-12 col-md-4 mb-3">
        <label>Geboortedatum</label>
        <input type="date" name="geboortedatum" class="form-control" value="<?= $data['geboortedatum'] !== null ? htmlspecialchars((string)$data['geboortedatum']) : ''; ?>">
      </div>
    </div>
    <div class="form-actions-responsive d-flex flex-wrap gap-2 mt-3">
      <button type="submit" class="btn btn-primary">Toevoegen</button>
      <a href="<?= URLROOT; ?>/Accountoverzicht/index" class="btn btn-secondary">Annuleren</a>
    </div>
  </form>
</div>
<script src="<?= URLROOT; ?>/public/js/accounts.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
