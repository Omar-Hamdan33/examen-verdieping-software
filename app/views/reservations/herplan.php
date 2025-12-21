<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">

<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

<div class="reservations-container">
  <h2>Nieuwe lesdatum kiezen</h2>
  <p>Kies een nieuwe datum en tijd voor je les. Alleen beschikbare slots binnen je pakket worden getoond.</p>

  <?php if (!empty($data['errors']) && is_array($data['errors'])): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($data['errors'] as $error): ?>
          <li><?= htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <?php $reservation = $data['reservation'] ?? null; 
  if (is_array($reservation)) {
    $reservation = reset($reservation);
}
   ?>
  
  <form method="post" action="<?= URLROOT; ?>/Reservations/doHerplan" id="herplanForm" novalidate>
    <div id="clientError" class="alert alert-danger" style="display:none;"></div>
    <input type="hidden" name="reservation_id" value="<?= isset($reservation->id) ? htmlspecialchars($reservation->id) : '' ?>">

    <!-- Kalenderweergave -->
    <div class="form-group mb-4">
      <label for="fullcalendar">Kalenderweergave</label>
      <div id="calendar-container">
        <div id="fullcalendar"></div>
      </div>
    </div>

    <!-- Dropdown tijdslots -->
    <div class="form-group mb-3">
      <label for="time_slot">Beschikbare tijdsloten</label>
      <div class="select-wrapper">
        <select name="time_slot_id" id="time_slot" class="form-control" required>
          <option value="">-- Kies een tijdslot --</option>
          <?php foreach ($data['timeSlots'] as $slot): ?>
            <option value="<?= $slot->id ?>">
              <?= htmlspecialchars($slot->available_date . ' ' . $slot->start_time . ' - ' . $slot->end_time) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-actions-responsive">
      <button type="submit" class="btn btn-success">Bevestig nieuwe datum</button>
      <a href="<?= URLROOT; ?>/Reservations/myreservations" class="btn btn-secondary">Annuleren</a>
    </div>
  </form>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const allSlots = <?= json_encode($data['timeSlots']); ?>;

  const seen = new Set();
  const events = allSlots
    .filter(slot => {
      const key = slot.available_date + ' ' + slot.start_time;
      if (seen.has(key)) return false;
      seen.add(key);
      return true;
    })
    .map(slot => ({
      id: slot.id,
      title: slot.start_time + ' - ' + slot.end_time,
      start: slot.available_date + 'T' + slot.start_time
    }));

  const calendarEl = document.getElementById('fullcalendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    events: events,
    eventClick: function(info) {
      document.getElementById('time_slot').value = info.event.id;
    }
  });
  calendar.render();
});
</script>

<script src="<?= URLROOT; ?>/public/js/reserving.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
