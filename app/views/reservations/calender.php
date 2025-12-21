<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">
<!-- FullCalendar CSS -->
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
<div class="reservations-container">
  <h1>Kies lesmoment(en)</h1>
  <form method="post" action="<?php echo URLROOT; ?>/reservations/store" id="calenderForm" novalidate>
    <div id="clientError" class="alert alert-danger" style="display:none;"></div>

    <?php 
    $hiddenFields = [
      'user_id', 'package_id', 'location_id', 'instructor_id',
      'duo_voornaam', 'duo_tussenvoegsel', 'duo_achternaam', 'duo_geboortedatum'
    ];
    foreach ($hiddenFields as $field) {
      if (isset($data[$field])) {
        echo '<input type="hidden" name="' . htmlspecialchars($field) . '" value="' . htmlspecialchars($data[$field]) . '">';
      }
    }
    ?>

    <div class="form-group mb-4">
      <label for="fullcalendar">Kalenderweergave</label>
      <div id="calendar-container">
        <div id="fullcalendar"></div>
      </div>
    </div>

    <?php
    // Dropdown: unieke datum + tijd combi
    $unique_times = [];
    foreach ($data['time_slots'] as $slot) {
      $key = $slot->available_date . ' ' . $slot->start_time;
      if (!isset($unique_times[$key])) {
        $unique_times[$key] = [
          'id' => $slot->id,
          'label' => $slot->available_date . ' ' . $slot->start_time
        ];
      }
    }

    // Kalender: unieke datum + tijd combi
    $uniqueCalendarSlots = [];
    $seenDates = [];
    foreach ($data['time_slots'] as $slot) {
      $key = $slot->available_date . ' ' . $slot->start_time;
      if (!isset($seenDates[$key])) {
        $uniqueCalendarSlots[] = $slot;
        $seenDates[$key] = true;
      }
    }
    ?>

    <?php for ($i = 1; $i <= $data['sessions_count']; $i++): ?>
      <div class="form-group">
        <label for="session_<?php echo $i; ?>">Sessie <?php echo $i; ?>:</label>
        <select name="time_slots[]" id="session_<?php echo $i; ?>" class="form-control" required>
          <option value="">-- Kies een tijdslot --</option>
          <?php foreach ($unique_times as $slot): ?>
            <option value="<?php echo htmlspecialchars($slot['id']); ?>">
              <?php echo htmlspecialchars($slot['label']); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    <?php endfor; ?>

    <button type="submit" class="btn btn-primary mt-3">Verstuur tijden</button>
  </form>
</div>

<!-- FullCalendar JS -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const allSlots = <?php echo json_encode($uniqueCalendarSlots); ?>;
  const events = allSlots.map(slot => ({
    id: slot.id,
    title: slot.start_time + ' - ' + slot.end_time,
    start: slot.available_date + 'T' + slot.start_time,
    end: slot.available_date + 'T' + slot.end_time
  }));

  const calendarEl = document.getElementById('fullcalendar');
  const calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: 'dayGridMonth',
    headerToolbar: {
      left: 'prev,next today',
      center: 'title',
      right: 'dayGridMonth,timeGridWeek'
    },
    events: events,
    eventClick: function(info) {
      const slotId = info.event.id;
      const selects = document.querySelectorAll('select[name="time_slots[]"]');
      for (let s of selects) {
        if (!s.value) {
          s.value = slotId;
          s.dispatchEvent(new Event('change'));
          break;
        }
      }
      alert("Tijdslot geselecteerd: " + info.event.title);
    }
  });

  calendar.render();
});
</script>

<?php require APPROOT . '/views/includes/footer.php'; ?>
