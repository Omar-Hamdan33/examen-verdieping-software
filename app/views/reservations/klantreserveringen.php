<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">
<div class="reservations-container">
  <h1>Reserveringen van mijn klanten</h1>
  <?php
  $userRole = $_SESSION['user_role'] ?? ($data['user_role'] ?? null);
  ?>
  <?php if (!empty($userRole) && $userRole === 'beheerder' && !empty($data['instructors'])): ?>
    <form method="get" action="<?= URLROOT ?>/reservations/klantreserveringen"
      style="margin-bottom: 15px; display:inline-block;">
      <input type="hidden" name="periode" value="<?= htmlspecialchars($data['periode'] ?? 'dag') ?>">
      <?php if (!empty($data['maand'])): ?>
        <input type="hidden" name="maand" value="<?= htmlspecialchars($data['maand']) ?>">
      <?php endif; ?>
      <label for="instructeur_id"><b>Instructeur:</b></label>
      <select name="instructeur_id" id="instructeur_id" onchange="this.form.submit()" class="form-control"
        style="width:auto;display:inline-block;">
        <option value="">-- Kies instructeur --</option>
        <?php foreach ($data['instructors'] as $inst): ?>
          <option value="<?= $inst->id ?>" <?= (isset($data['selected_instructor']) && $data['selected_instructor'] == $inst->id) ? 'selected' : '' ?>>
            <?= htmlspecialchars($inst->voornaam . ' ' . $inst->tussenvoegsel . ' ' . $inst->achternaam) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </form>
  <?php endif; ?>

  <!-- Periode knoppen + navigatie -->
  <div style="margin-bottom: 20px;">
    <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=dag<?= isset($data['selected_instructor']) && $data['selected_instructor'] ? '&instructeur_id=' . $data['selected_instructor'] : '' ?>"
      class="btn btn-primary">Dag</a>
    <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=week<?= isset($data['selected_instructor']) && $data['selected_instructor'] ? '&instructeur_id=' . $data['selected_instructor'] : '' ?>"
      class="btn btn-primary">Week</a>
    <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=maand<?= isset($data['selected_instructor']) && $data['selected_instructor'] ? '&instructeur_id=' . $data['selected_instructor'] : '' ?>"
      class="btn btn-primary">Deze maand</a>
    <?php
    $periode = $data['periode'] ?? null;
    $today = date('Y-m-d');
    $extraInst = isset($data['selected_instructor']) && $data['selected_instructor'] ? '&instructeur_id=' . $data['selected_instructor'] : '';
    if ($periode === 'dag') {
      $currentDay = $data['maand'] ?? $today;
      $prevDay = date('Y-m-d', strtotime($currentDay . ' -1 day'));
      $nextDay = date('Y-m-d', strtotime($currentDay . ' +1 day'));
      ?>
      <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=dag&maand=<?= $prevDay . $extraInst ?>"
        class="btn btn-secondary">&lt; <?= date('d-m-Y', strtotime($prevDay)) ?></a>
      <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=dag&maand=<?= $nextDay . $extraInst ?>"
        class="btn btn-secondary"><?= date('d-m-Y', strtotime($nextDay)) ?> &gt;</a>
      <span style="margin-left:10px;font-weight:bold;"> <?= date('d-m-Y', strtotime($currentDay)) ?> </span>
    <?php } elseif ($periode === 'week') {
      $currentWeek = $data['maand'] ?? date('Y-m-d', strtotime('monday this week'));
      $prevWeek = date('Y-m-d', strtotime($currentWeek . ' -1 week'));
      $nextWeek = date('Y-m-d', strtotime($currentWeek . ' +1 week'));
      $weekNr = date('W', strtotime($currentWeek));
      $year = date('Y', strtotime($currentWeek));
      ?>
      <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=week&maand=<?= $prevWeek . $extraInst ?>"
        class="btn btn-secondary">&lt; Week <?= date('W', strtotime($prevWeek)) ?> <?= date('Y', strtotime($prevWeek)) ?></a>
      <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=week&maand=<?= $nextWeek . $extraInst ?>"
        class="btn btn-secondary">Week <?= date('W', strtotime($nextWeek)) ?> <?= date('Y', strtotime($nextWeek)) ?> &gt;</a>
      <span style="margin-left:10px;font-weight:bold;"> Week <?= $weekNr ?> <?= $year ?> </span>
    <?php } elseif ($periode === 'maand') {
      $currentMonth = $data['maand'] ?? date('Y-m');
      $prevMonth = date('Y-m', strtotime($currentMonth . ' -1 month'));
      $nextMonth = date('Y-m', strtotime($currentMonth . ' +1 month'));
      ?>
      <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=maand&maand=<?= $prevMonth . $extraInst ?>"
        class="btn btn-secondary">&lt; <?= date('F Y', strtotime($prevMonth)) ?></a>
      <a href="<?= URLROOT ?>/reservations/klantreserveringen?periode=maand&maand=<?= $nextMonth . $extraInst ?>"
        class="btn btn-secondary"><?= date('F Y', strtotime($nextMonth)) ?> &gt;</a>
      <span style="margin-left:10px;font-weight:bold;"> <?= date('F Y', strtotime($currentMonth . '-01')) ?> </span>
    <?php } ?>
  </div>

  <!-- Flash messages -->
  <?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($_SESSION['flash_success']);
      unset($_SESSION['flash_success']); ?>
    </div>
  <?php endif; ?>
  <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($_SESSION['flash_error']);
      unset($_SESSION['flash_error']); ?>
    </div>
  <?php endif; ?>

  <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Klant</th>
        <th>Duo deelnemer</th>
        <th>Pakket</th>
        <th>Locatie</th>
        <th>Starttijd</th>
        <th>Datum</th>
        <th>Status</th>
        <th>instructeur</th>
        <th>Betaald</th>
        <th>Definitief</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $rows = [];
      foreach ($data['reservations'] as $res) {
        $rows[] = [
          'id' => $res->id,
          'klant' => htmlspecialchars($res->voornaam
            . (isset($res->tussenvoegsel) && $res->tussenvoegsel ? ' ' . $res->tussenvoegsel : '')
            . ' ' . $res->achternaam),
          'pakket' => htmlspecialchars($res->pakket),
          'locatie' => htmlspecialchars($res->locatie),
          'start_time' => htmlspecialchars($res->start_time ?? '-'),
          'available_date' => htmlspecialchars($res->available_date ?? '-'),
          'status' => htmlspecialchars($res->status),
          'duo' => !empty($res->duo_voornaam) ? htmlspecialchars($res->duo_voornaam . ' ' . ($res->duo_tussenvoegsel ?? '') . ' ' . $res->duo_achternaam . ' (' . $res->duo_geboortedatum . ')') : '-',
          'betaald' => isset($res->betaald) ? ($res->betaald ? 'Ja' : 'Nee') : '-',
          'definitief' => isset($res->definitief) ? ($res->definitief ? 'Ja' : 'Nee') : '-',
          'instructeur' => isset($res->instructeur_voornaam)
            ? htmlspecialchars($res->instructeur_voornaam
                . (isset($res->instructeur_tussenvoegsel) && $res->instructeur_tussenvoegsel ? ' ' . $res->instructeur_tussenvoegsel : '')
                . ' ' . $res->instructeur_achternaam)
            : '-',
          'is_availability' => false ,
            'instructor_availability_id' => $res->instructor_availability_id ?? null
        ];
      }

      // Filter unplanned_availability voor instructeurs: alleen eigen slots tonen
      if ($userRole === 'instructeur' && !empty($data['unplanned_availability'])) {
        $userId = $_SESSION['user_id'] ?? null;
        $data['unplanned_availability'] = array_filter($data['unplanned_availability'], function($slot) use ($userId) {
          return isset($slot->instructor_id) && $slot->instructor_id == $userId;
        });
      }
      // Filter unplanned_availability op gekozen instructeur als beheerder
      if ($userRole === 'beheerder' && !empty($data['selected_instructor']) && !empty($data['unplanned_availability'])) {
        $selectedId = $data['selected_instructor'];
        $data['unplanned_availability'] = array_filter($data['unplanned_availability'], function($slot) use ($selectedId) {
          return isset($slot->instructor_id) && $slot->instructor_id == $selectedId;
        });
      }

      if (!empty($data['unplanned_availability'])) {
        foreach ($data['unplanned_availability'] as $slot) {
          $rows[] = [
            'id' => '-',
            'klant' => htmlspecialchars($slot->voornaam . ' ' . (isset($slot->tussenvoegsel) && $slot->tussenvoegsel ? ' ' . $slot->tussenvoegsel : '') . ' ' . $slot->achternaam),
            'pakket' => '-',
            'locatie' => '-',
            'start_time' => htmlspecialchars($slot->start_time),
            'available_date' => htmlspecialchars($slot->available_date),
            'status' => '<span class="badge bg-warning text-dark">Beschikbaar</span>',
            'duo' => '-',
            'betaald' => '-',
            'definitief' => '-',
            'instructeur' => htmlspecialchars($slot->voornaam . (isset($slot->tussenvoegsel) && $slot->tussenvoegsel ? ' ' . $slot->tussenvoegsel : '') . ' ' . $slot->achternaam),
            'is_availability' => true
          ];
        }
      }

      // ✅ Sorteer alle rijen op datum + tijd
      usort($rows, function ($a, $b) {
        $dateTimeA = $a['available_date'] . ' ' . $a['start_time'];
        $dateTimeB = $b['available_date'] . ' ' . $b['start_time'];
        return strtotime($dateTimeA) <=> strtotime($dateTimeB);
      });

      foreach ($rows as $row): ?>
        <tr<?= !empty($row['is_availability']) ? ' class="table-warning"' : '' ?>>
          <td data-label="ID"><?= $row['id'] ?></td>
          <td data-label="Klant"><?= $row['klant'] ?></td>
          <td data-label="Duo deelnemer"><?= $row['duo'] ?></td>
          <td data-label="Pakket"><?= $row['pakket'] ?></td>
          <td data-label="Locatie"><?= $row['locatie'] ?></td>
          <td data-label="Starttijd"><?= $row['start_time'] ?></td>
          <td data-label="Datum"><?= $row['available_date'] ?></td>
          <td data-label="Status"><?= $row['status'] ?></td>
          <td data-label="instructeur"><?= $row['instructeur'] ?></td>
          <td data-label="Betaald"><?= $row['betaald'] ?></td>
          <td data-label="Definitief"><?= $row['definitief'] ?></td>
          <?php if (!$row['is_availability'] && ($userRole === 'beheerder' || $userRole === 'instructeur')): ?>
            <td data-label="Actie">

              <?php if ($row['status'] !== 'geannuleerd'): ?>
                <form method="post" action="<?= URLROOT ?>/Reservations/sendCancelMail" style="display:inline-block;">
                  <input type="hidden" name="reservation_id" value="<?= $row['instructor_availability_id'] ?>">
                  <input type="hidden" name="instructor" value="algemeen">
                  <input type="hidden" name="reason" value="ziekte">
                  <button type="submit" class="btn btn-sm btn-danger"
                    onclick="return confirm('Weet je zeker dat je deze les wilt annuleren wegens ziekte van de instructeur?')">Annuleer (Ziekte)</button>
                </form>
                <form method="post" action="<?= URLROOT ?>/Reservations/sendCancelMail" style="display:inline-block;">
                  <input type="hidden" name="reservation_id" value="<?= $row['instructor_availability_id'] ?>">
                  <input type="hidden" name="reason" value="weer">
                  <button type="submit" class="btn btn-sm btn-warning"
                    onclick="return confirm('Weet je zeker dat je deze les wilt annuleren wegens slechte weersomstandigheden?')">Annuleer (Weer > 10)</button>
                </form>
              <?php else: ?>
                <p>Geannuleerd</p>
              <?php endif; ?>
            </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<script src="<?= URLROOT; ?>/public/js/reserving.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
