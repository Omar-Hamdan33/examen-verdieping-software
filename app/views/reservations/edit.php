<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<?php 
// Fix: $data['reservation'] kan een array met één object zijn
$reservation = $data['reservation'] ?? null;
if (is_array($reservation) && count($reservation) > 0) {
    $reservation = $reservation[0];
}
$packages = $data['packages'] ?? [];
$locations = $data['locations'] ?? [];
?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">
<div class="reservations-container">
  <h1>Reservering aanpassen</h1>
  <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
  <?php endif; ?>
  <?php if ($reservation): ?>
  <form method="post" action="">
    <div class="form-group">
      <label for="user_id">Gebruiker ID</label>
      <input type="number" name="user_id" id="user_id" class="form-control" value="<?= htmlspecialchars($reservation->user_id ?? '') ?>" required>
    </div>
    <div class="form-group">
      <label for="package_id">Pakket</label>
      <select name="package_id" id="package_id" class="form-control" required>
        <?php foreach ($packages as $p): ?>
          <option value="<?= $p->id ?>" <?= $p->id == ($reservation->package_id ?? null) ? 'selected' : '' ?>><?= htmlspecialchars($p->name) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="location_id">Locatie</label>
      <select name="location_id" id="location_id" class="form-control" required>
        <?php foreach ($locations as $l): ?>
          <option value="<?= $l->id ?>" <?= $l->id == ($reservation->location_id ?? null) ? 'selected' : '' ?>><?= htmlspecialchars($l->name) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="aantal">Aantal</label>
      <input type="number" name="aantal" id="aantal" class="form-control" value="<?= htmlspecialchars($reservation->aantal ?? 1) ?>" min="1">
    </div>
    <div class="form-group">
      <label for="duo_voornaam">Duo voornaam</label>
      <input type="text" name="duo_voornaam" id="duo_voornaam" class="form-control" value="<?= htmlspecialchars($reservation->duo_voornaam ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="duo_tussenvoegsel">Duo tussenvoegsel</label>
      <input type="text" name="duo_tussenvoegsel" id="duo_tussenvoegsel" class="form-control" value="<?= htmlspecialchars($reservation->duo_tussenvoegsel ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="duo_achternaam">Duo achternaam</label>
      <input type="text" name="duo_achternaam" id="duo_achternaam" class="form-control" value="<?= htmlspecialchars($reservation->duo_achternaam ?? '') ?>">
    </div>
    <div class="form-group">
      <label for="duo_geboortedatum">Duo geboortedatum</label>
      <input type="date" name="duo_geboortedatum" id="duo_geboortedatum" class="form-control" value="<?= htmlspecialchars($reservation->duo_geboortedatum ?? '') ?>">
    </div>
    <div class="form-group form-check">
      <input type="checkbox" name="betaald" id="betaald" class="form-check-input" value="1" <?= !empty($reservation->betaald) ? 'checked' : '' ?> >
      <label for="betaald" class="form-check-label">Betaald</label>
    </div>
    <div class="form-group form-check">
      <input type="checkbox" name="definitief" id="definitief" class="form-check-input" value="1" <?= !empty($reservation->definitief) ? 'checked' : '' ?> >
      <label for="definitief" class="form-check-label">Definitief</label>
    </div>
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="<?= URLROOT; ?>/reservations/index" class="btn btn-secondary">Annuleren</a>
  </form>
  <?php else: ?>
    <div class="alert alert-danger">Reservering niet gevonden.</div>
  <?php endif; ?>
</div>
<?php require APPROOT . '/views/includes/footer.php'; ?>
