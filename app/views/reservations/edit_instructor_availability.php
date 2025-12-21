<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">
<div class="reservations-container">
  <h1>Instructeur beschikbaarheid aanpassen</h1>
  <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
  <?php endif; ?>
  <?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
    </div>
  <?php endif; ?>
  <?php $a = $data['availability'] ?? null; ?>
  <?php if ($a): ?>
  <form method="post" action="" class="mb-3">
    <div class="form-group">
      <label for="available_date">Datum</label>
      <input type="date" name="available_date" id="available_date" class="form-control" value="<?= htmlspecialchars($a['available_date']) ?>" required>
    </div>
    <div class="form-group">
      <label for="start_time">Starttijd</label>
      <input type="time" name="start_time" id="start_time" class="form-control" value="<?= htmlspecialchars($a['start_time']) ?>" required>
    </div>
    <div class="form-group">
      <label for="end_time">Eindtijd</label>
      <input type="time" name="end_time" id="end_time" class="form-control" value="<?= htmlspecialchars($a['end_time']) ?>" required>
    </div>
    <div class="form-group">
      <label for="status">Status</label>
      <select name="status" id="status" class="form-control" required>
        <?php foreach (["gepland","voltooid","geannuleerd","annulering_in_afwachting","beschikbaar","herpland"] as $status): ?>
          <option value="<?= $status ?>" <?= $a['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="instructor_id">Instructeur</label>
      <select name="instructor_id" id="instructor_id" class="form-control" required>
        <?php
        // Haal alle instructeurs op uit het model (of geef ze door via de controller)
        $instructors = $data['instructors'] ?? [];
        foreach ($instructors as $inst): ?>
          <option value="<?= $inst->id ?>" <?= $a['instructor_id'] == $inst->id ? 'selected' : '' ?>>
            <?= htmlspecialchars($inst->voornaam .  ' ' . $inst->tussenvoegsel . ' ' . $inst->achternaam) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <button type="submit" class="btn btn-success">Opslaan</button>
    <a href="<?= URLROOT; ?>/reservations/index" class="btn btn-secondary">Annuleren</a>
  </form>
  <?php else: ?>
    <div class="alert alert-danger">Beschikbaarheid niet gevonden.</div>
  <?php endif; ?>
</div>
<?php require APPROOT . '/views/includes/footer.php'; ?>
