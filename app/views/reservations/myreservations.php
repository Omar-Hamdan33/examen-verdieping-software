<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">
<div class="reservations-container">
  <h1>Mijn Reserveringen</h1>

  <?php if (isset($_SESSION['flash_success'])): ?>
    <div class="alert alert-success">
      <?= htmlspecialchars($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
    </div>
  <?php endif; ?>
  <?php if (isset($_SESSION['flash_error'])): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
    </div>
  <?php endif; ?>

  <table class="table">
    <thead>
      <tr>
        <th>Pakket</th>
        <th>Locatie</th>
        <th>Starttijd</th>
        <th>Datum</th>
        <th>Status</th>
        <th>Duo deelnemer</th>
        <th>Betaald</th>
        <th>Definitief</th>
        <th>Actie</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($data['reservations'] as $res): ?>
      <tr>
        <td data-label="Pakket"><?= htmlspecialchars($res->pakket); ?></td>
        <td data-label="Locatie"><?= htmlspecialchars($res->locatie); ?></td>
        <td data-label="Starttijd"><?= htmlspecialchars($res->start_time ?? '-'); ?></td>
        <td data-label="Datum"><?= htmlspecialchars($res->available_date ?? '-'); ?></td>
        <td data-label="Status"><?= htmlspecialchars($res->status); ?></td>
        <td data-label="Duo deelnemer">
          <?php
            if (!empty($res->duo_voornaam)) {
              echo htmlspecialchars($res->duo_voornaam . ' ' . ($res->duo_tussenvoegsel ?? '') . ' ' . $res->duo_achternaam . ' (' . $res->duo_geboortedatum . ')');
            } else {
              echo '-';
            }
          ?>
        </td>
        <td data-label="Betaald">
          <?= isset($res->betaald) ? ($res->betaald ? 'Ja' : 'Nee') : '-'; ?>
        </td>
        <td data-label="Definitief">
          <?= isset($res->definitief) ? ($res->definitief ? 'Ja' : 'Nee') : '-'; ?>
        </td>
        <td data-label="Actie">
          <?php if ($res->status !== 'geannuleerd' && $res->status !== 'annulering_in_afwachting'): ?>
            <!-- Annuleerknop -->
            <form method="post" action="<?= URLROOT; ?>/Reservations/cancelReservation" style="display:inline-block;">
              <input type="hidden" name="reservation_id" value="<?= $res->instructor_availability_id; ?>">
              <button type="button" class="btn btn-sm btn-danger" onclick="showCancelReasonForm(<?= $res->instructor_availability_id; ?>)">Annuleer</button>
            </form>
            <form id="cancel-reason-form-<?= $res->instructor_availability_id; ?>" method="post" action="<?= URLROOT; ?>/Reservations/doCancelReservation" style="display:none; margin-top:5px;">
              <input type="hidden" name="reservation_id" value="<?= $res->instructor_availability_id; ?>">
              <textarea name="cancel_reason" class="form-control mb-2" placeholder="Reden van annulering" required></textarea>
              <button type="submit" class="btn btn-sm btn-warning">Verstuur reden</button>
              <button type="button" class="btn btn-sm btn-secondary" onclick="hideCancelReasonForm(<?= $res->instructor_availability_id; ?>)">Annuleren</button>
            </form>
          <?php elseif ($res->status === 'annulering_in_afwachting'): ?>
            In afwachting goedkeuring
          <?php elseif ($res->status === 'geannuleerd'): ?>
            <form method="post" action="<?= URLROOT; ?>/Reservations/herplan">
              <input type="hidden" name="reservation_id" value="<?= $res->id; ?>">
              <input type="hidden" name="instructor_availability_id" value="<?= $res->instructor_availability_id; ?>">
              <button type="submit" class="btn btn-sm btn-success">Nieuwe datum kiezen</button>
            </form>
          <?php else: ?>
            Geannuleerd
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
</div>

<script src="<?= URLROOT; ?>/public/js/reserving.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
