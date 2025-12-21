<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">
<div class="reservations-container">
  <h1>Beschikbaarheid toevoegen</h1>
  <?php $instructors = $data['instructors']; ?>
  <?php if (!empty($success)) echo "<div class='alert alert-success'>$success</div>"; ?>
  <?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
  <form method="post" action="<?= URLROOT; ?>/Reservations/inplannen" id="inplannenForm" novalidate>
    <div id="clientError" class="alert alert-danger" style="display:none;"></div>
    <div class="form-group">
      <label for="instructor_id">Instructeur:</label>
      <select name="instructor_id" id="instructor_id" class="form-control" required>
        <option value="">-- Kies een instructeur --</option>
        <?php foreach ($instructors as $trainer): ?>
          <option value="<?= $trainer->id ?>">
            <?= htmlspecialchars($trainer->voornaam . ' ' . $trainer->tussenvoegsel .  ' ' . $trainer->achternaam) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label for="available_date">Datum:</label>
      <input type="date" name="available_date" id="available_date" class="form-control" required>
    </div>
    <div class="form-group">
      <label for="start_time">Starttijd:</label>
      <input type="time" name="start_time" id="start_time" class="form-control" required>
    </div>
    <div class="form-group">
      <label for="end_time">Eindtijd:</label>
      <input type="time" name="end_time" id="end_time" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Toevoegen</button>
  </form>
</div>
<script src="<?= URLROOT; ?>/public/js/reserving.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>