<?php require APPROOT . '/views/includes/header.php'; ?>
  <?php require APPROOT . '/views/includes/navigation.php'; ?>
  <link rel="stylesheet" href="<?= URLROOT; ?>/public/css/update.css">

<div class="container mt-3">
  <!-- Nieuwe klant toevoegen knop -->
  <div class="row mb-3">
    <div class="col-1"></div>
    <div class="col-10">
      <a href="<?= URLROOT; ?>/Accountoverzicht/add" class="btn btn-success mb-2">Nieuwe klant toevoegen</a>
    </div>
    <div class="col-1"></div>
  </div>
  <!-- Zoekveld -->
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

  <!-- Tabel -->
  <div class="row">
    <div class="col-12">
      <div class="table-responsive">
        <table class="table table-hover table-bordered table-striped responsive-table" data-sort-order="">
          <thead>
            <tr class="table-danger">
              <th onclick="sortTable(0)" style="cursor: pointer;">Naam</th>
              <th onclick="sortTable(1)" style="cursor: pointer;">Rol *</th>
              <th onclick="sortTable(2)" style="cursor: pointer;">Email *</th>
              <th onclick="sortTable(3)" style="cursor: pointer;">Telefoon</th>
              <th onclick="sortTable(4)" style="cursor: pointer;">Adres</th>
              <th onclick="sortTable(5)" style="cursor: pointer;">Woonplaats</th>
              <th onclick="sortTable(6)" style="cursor: pointer;">Geboortedatum</th>
              <th onclick="sortTable(7)" style="cursor: pointer;">BSN</th>
              <th onclick="sortTable(8)" style="cursor: pointer;">Actief</th>
              <th class="text-center">Wijzig</th>
              <th class="text-center">Verwijderen</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($data['Accountoverzicht'] as $user): ?>
              <tr>
                <td data-label="Naam"><?= !empty($user->naam) ? htmlspecialchars($user->naam) : '-'; ?></td>
                <td data-label="Rol *"><?= htmlspecialchars($user->rol); ?></td>
                <td data-label="Email *"><?= htmlspecialchars($user->email); ?></td>
                <td data-label="Telefoon"><?= !empty($user->telefoon) ? htmlspecialchars($user->telefoon) : '-'; ?></td>
                <td data-label="Adres"><?= !empty($user->adres) ? htmlspecialchars($user->adres) : '-'; ?></td>
                <td data-label="Woonplaats"><?= !empty($user->woonplaats) ? htmlspecialchars($user->woonplaats) : '-'; ?></td>
                <td data-label="Geboortedatum"><?= !empty($user->geboortedatum) ? htmlspecialchars($user->geboortedatum) : '-'; ?></td>
                <td data-label="BSN"><?= !empty($user->bsn) ? htmlspecialchars($user->bsn) : '-'; ?></td>
                <td data-label="Actief"><?= isset($user->actief) ? ($user->actief ? 'Ja' : 'Nee') : '-'; ?></td>
                <td class="text-center" data-label="Wijzig">
                  <a href="<?= URLROOT; ?>/Accountoverzicht/getid/<?= $user->id; ?>">
                    <i class="bi bi-pencil-fill text-dark"></i>
                  </a>
                </td>
                <td class="text-center" data-label="Verwijderen">
                  <a href="<?= URLROOT; ?>/Accountoverzicht/delete/<?= $user->id; ?>" onclick="return confirm('Weet je zeker dat je dit account wilt verwijderen?');">
                    <i class="bi bi-trash3-fill text-dark"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="<?= URLROOT; ?>/public/js/script.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
