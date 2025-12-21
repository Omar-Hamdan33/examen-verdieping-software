<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">
<div class="reservations-container">
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
  <h1>Beheer Reserveringen</h1>
  <div class="row mb-3">
    <div class="col-1"></div>
    <div class="col-10">
      <input 
        type="text" 
        id="searchInput" 
        class="form-control" 
        placeholder="Zoeken…" 
        onkeyup="searchTable()"
      >
      <div id="clientError" class="alert alert-danger mt-2" style="display:none;"></div>
    </div>
    <div class="col-1"></div>
  </div>
  <div class="table-responsive">
    <table class="table responsive-table">
      <thead>
        <tr>
          <th onclick="sortTable(0)" style="cursor:pointer;">ID</th>
          <th onclick="sortTable(1)" style="cursor:pointer;">Klant</th>
          <th onclick="sortTable(2)" style="cursor:pointer;">Pakket</th>
          <th onclick="sortTable(3)" style="cursor:pointer;">Locatie</th>
          <th onclick="sortTable(4)" style="cursor:pointer;">Duo deelnemer</th>
          <th onclick="sortTable(5)" style="cursor:pointer;">Datum</th>
          <th onclick="sortTable(6)" style="cursor:pointer;">Starttijd</th>
          <th onclick="sortTable(7)" style="cursor:pointer;">Eindtijd</th>
          <th onclick="sortTable(8)" style="cursor:pointer;">Instructeur</th>
          <th onclick="sortTable(9)" style="cursor:pointer;">Actie</th>
          <th onclick="sortTable(10)" style="cursor:pointer;">Betaald</th>
          <th onclick="sortTable(11)" style="cursor:pointer;">Definitief</th>
        </tr>
      </thead>
      <tbody>
      <?php  foreach ($data['reservations'] as $res): ?>
        <tr>
          <td data-label="ID"><?= $res->id; ?></td>
          <td data-label="Klant">
            <?php
              $klantNaam = $res->voornaam;
              if (!empty($res->tussenvoegsel)) {
                $klantNaam .= ' ' . $res->tussenvoegsel;
              }
              $klantNaam .= ' ' . $res->achternaam;
              echo htmlspecialchars(trim($klantNaam));
            ?>
          </td>
          <td data-label="Pakket"><?= htmlspecialchars($res->pakket); ?></td>
          <td data-label="Locatie"><?= htmlspecialchars($res->locatie); ?></td>
          <td data-label="Duo deelnemer">
            <?php
              if (!empty($res->duo_voornaam)) {
                echo htmlspecialchars($res->duo_voornaam . ' ' . ($res->duo_tussenvoegsel ?? '') . ' ' . $res->duo_achternaam . ' (' . $res->duo_geboortedatum . ')');
              } else {
                echo '-';
              }
            ?>
          </td>
          <td data-label="Datum"><?= htmlspecialchars($res->available_date ?? '-'); ?></td>
          <td data-label="Starttijd"><?= htmlspecialchars($res->start_time ?? '-'); ?></td>
          <td data-label="Eindtijd"><?= htmlspecialchars($res->end_time ?? '-'); ?></td>
          <td data-label="Instructeur">
            <?php
              if (isset($res->instructeur_voornaam)) {
                $instructeurNaam = $res->instructeur_voornaam;
                if (!empty($res->instructeur_tussenvoegsel)) {
                  $instructeurNaam .= ' ' . $res->instructeur_tussenvoegsel;
                }
                $instructeurNaam .= ' ' . $res->instructeur_achternaam;
                echo htmlspecialchars(trim($instructeurNaam));
              } else {
                echo '-';
              }
            ?>
          </td>
          <td data-label="Actie">

            <?php if ((isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'beheerder') && (empty($res->definitief) || $res->definitief == 0)): ?>
            <form method="post" action="<?= URLROOT; ?>/Reservations/makeDefinitive" style="display:inline-block;">
              <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($res->id); ?>">
              <button type="submit" class="btn btn-sm btn-success">Definitief maken</button>
            </form>
            <?php endif; ?>
            <!-- Nieuwe knoppen voor aanpassen -->
            <a href="<?= URLROOT; ?>/Reservations/edit/<?= $res->id; ?>" class="btn btn-sm btn-warning mt-1">Reservering aanpassen</a>
            <?php if (!empty($res->instructor_availability_id)): ?>
              <a href="<?= URLROOT; ?>/Reservations/editInstructorAvailability/<?= $res->instructor_availability_id; ?>" class="btn btn-sm btn-info mt-1">Instructeur beschikbaarheid aanpassen</a>
            <?php endif; ?>
            <?php if (!empty($res->cancel_reason) && $res->status == 'annulering_in_afwachting'): ?>
            <div class="mt-2 alert alert-warning p-2" style="font-size:0.95em;">
              <strong>Reden annulering:</strong><br>
              <?= nl2br(htmlspecialchars($res->cancel_reason)); ?>
              <form method="post" action="<?= URLROOT; ?>/Reservations/handleCancelReason" class="mt-2">
                <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($res->id); ?>">
                <input type="hidden" name="instructor_availability_id" value="<?= htmlspecialchars($res->instructor_availability_id); ?>">
                <button type="submit" name="action" value="accept" class="btn btn-success btn-sm">Accepteren</button>
                <button type="submit" name="action" value="deny" class="btn btn-danger btn-sm">Weigeren</button>
              </form>
            </div>
            <?php endif; ?>
          </td>
          <td data-label="Betaald">
            <?php
              if (isset($res->betaald)) {
                echo $res->betaald ? 'Ja' : 'Nee';
              } else {
                echo '-';
              }
            ?>
          </td>
          <td data-label="Definitief">
            <?php
              if (isset($res->definitief)) {
                echo $res->definitief ? 'Ja' : 'Nee';
              } else {
                echo '-';
              }
            ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<script src="<?= URLROOT; ?>/public/js/reserving.js"></script>
<script src="<?= URLROOT; ?>/public/js/script.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
