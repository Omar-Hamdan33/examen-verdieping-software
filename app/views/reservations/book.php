<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/reservations.css">
<div class="reservations-container">
  <h1>Les boeken</h1>
  <?php 
  // Debugging: dump the data array to check its contents
  // var_dump($data); 
  if (!empty($data['errors'])): ?>
    <div class="alert alert-danger">
      <ul>
        <?php foreach ($data['errors'] as $err): ?>
          <li><?php echo htmlspecialchars($err); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>
  <form method="post" action="<?php echo URLROOT; ?>/reservations/book" id="bookForm" novalidate>
    <div id="clientError" class="alert alert-danger" style="display:none;"></div>
    <div class="form-group select-responsive">
      <label for="package_id">Pakket</label>
      <div class="select-wrapper">
        <select name="package_id" id="package_id" class="form-control">
          <option value="">-- Kies een pakket --</option>
          <?php foreach ($data['packages'] as $pkg): ?>
            <option value="<?php echo $pkg->id; ?>"><?php echo htmlspecialchars($pkg->name); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    <div class="form-group select-responsive">
      <label for="location_id">Locatie</label>
      <div class="select-wrapper">
        <select name="location_id" id="location_id" class="form-control">
          <option value="">-- Kies een locatie --</option>
          <?php foreach ($data['locations'] as $loc): ?>
            <option value="<?php echo $loc->id; ?>"><?php echo htmlspecialchars($loc->name); ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
    
    <div id="duo-fields" style="display:none; margin-top:20px;">
      <h4>Gegevens tweede deelnemer</h4>
      <div class="form-group">
        <label for="duo_voornaam">Voornaam</label>
        <input type="text" name="duo_voornaam" id="duo_voornaam" class="form-control">
      </div>
      <div class="form-group">
        <label for="duo_tussenvoegsel">Tussenvoegsel</label>
        <input type="text" name="duo_tussenvoegsel" id="duo_tussenvoegsel" class="form-control">
      </div>
      <div class="form-group">
        <label for="duo_achternaam">Achternaam</label>
        <input type="text" name="duo_achternaam" id="duo_achternaam" class="form-control">
      </div>
      <div class="form-group">
        <label for="duo_geboortedatum">Geboortedatum</label>
        <input type="date" name="duo_geboortedatum" id="duo_geboortedatum" class="form-control">
      </div>
    </div>
    <div class="form-actions-responsive">
      <button type="submit" class="btn btn-primary">Reserveer les</button>
    </div>
  </form>
</div>
<script>
// Toon duo-velden als een duo-pakket is gekozen
const duoFields = document.getElementById('duo-fields');
const packageSelect = document.getElementById('package_id');
const duoPackageIds = [2, 3, 4]; // IDs van duo-pakketten (pas aan indien nodig)
function checkDuo() {
  const val = parseInt(packageSelect.value);
  if (duoPackageIds.includes(val)) {
    duoFields.style.display = '';
    document.getElementById('duo_voornaam').required = true;
    document.getElementById('duo_achternaam').required = true;
    document.getElementById('duo_geboortedatum').required = true;
  } else {
    duoFields.style.display = 'none';
    document.getElementById('duo_voornaam').required = false;
    document.getElementById('duo_achternaam').required = false;
    document.getElementById('duo_geboortedatum').required = false;
  }
}
packageSelect.addEventListener('change', checkDuo);
document.addEventListener('DOMContentLoaded', checkDuo);

// Client-side validatie
const bookForm = document.getElementById('bookForm');
bookForm.addEventListener('submit', function(e) {
  const errorDiv = document.getElementById('clientError');
  errorDiv.style.display = 'none';
  errorDiv.innerText = '';
  let errors = [];
  const pakket = packageSelect.value;
  const locatie = document.getElementById('location_id').value;
  const val = parseInt(pakket);
  if (!pakket) errors.push('Kies een pakket.');
  if (!locatie) errors.push('Kies een locatie.');
  if (duoPackageIds.includes(val)) {
    const voornaam = document.getElementById('duo_voornaam').value.trim();
    const achternaam = document.getElementById('duo_achternaam').value.trim();
    const geboortedatum = document.getElementById('duo_geboortedatum').value;
    if (!voornaam) errors.push('Voornaam tweede deelnemer is verplicht.');
    if (!achternaam) errors.push('Achternaam tweede deelnemer is verplicht.');
    if (!geboortedatum) {
      errors.push('Geboortedatum tweede deelnemer is verplicht.');
    } else {
      const geboortedatumDate = new Date(geboortedatum);
      const vandaag = new Date();
      vandaag.setHours(0,0,0,0);
      if (geboortedatumDate > vandaag) {
        errors.push('Geboortedatum mag niet in de toekomst liggen.');
      }
    }
  }
  if (errors.length > 0) {
    e.preventDefault();
    errorDiv.innerText = errors.join('\n');
    errorDiv.style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
});
</script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
