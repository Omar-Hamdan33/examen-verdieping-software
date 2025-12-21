<?php require   APPROOT . '/views/includes/header.php'; 
$results = $data['Account'] ?? null;
$error = $data['error'] ?? null;
?>
  <?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/update.css">

<body>

<div class="signup-container">
    <h1>Account bewerken</h1>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if (isset($results[0])):
            $user = $results[0];
             ?>
    <form method="POST" action="<?= URLROOT; ?>/Accountoverzicht/update/<?php echo $user->id; ?>" id="updateForm" novalidate>
    <div id="clientError" class="alert alert-danger" style="display:none;"></div>
    <input type="hidden" name="Id" value="<?php echo $user->id; ?>">

    <div class="form-group">
        <label for="voornaam">Voornaam (optioneel)</label>
        <input type="text" id="voornaam" name="voornaam" value="<?php echo $user->voornaam !== null ? htmlspecialchars($user->voornaam) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="tussenvoegsel">Tussenvoegsel (optioneel)</label>
        <input type="text" id="tussenvoegsel" name="tussenvoegsel" value="<?php echo $user->tussenvoegsel !== null ? htmlspecialchars($user->tussenvoegsel) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="achternaam">Achternaam (optioneel)</label>
        <input type="text" id="achternaam" name="achternaam" value="<?php echo $user->achternaam !== null ? htmlspecialchars($user->achternaam) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="email">Email *</label>
        <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($user->email); ?>">
    </div>
    <div class="form-group">
        <label for="telefoon">Telefoon (optioneel)</label>
        <input type="text" id="telefoon" name="telefoon" value="<?php echo $user->telefoon !== null ? htmlspecialchars($user->telefoon) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="adres">Adres (optioneel)</label>
        <input type="text" id="adres" name="adres" value="<?php echo $user->adres !== null ? htmlspecialchars($user->adres) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="woonplaats">Woonplaats (optioneel)</label>
        <input type="text" id="woonplaats" name="woonplaats" value="<?php echo $user->woonplaats !== null ? htmlspecialchars($user->woonplaats) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="geboortedatum">Geboortedatum (optioneel)</label>
        <input type="date" id="geboortedatum" name="geboortedatum" value="<?php echo $user->geboortedatum !== null ? htmlspecialchars($user->geboortedatum) : ''; ?>">
    </div>
    <div class="form-group">
        <label for="bsn">BSN (optioneel)</label>
        <input type="text" id="bsn" name="bsn" value="<?php echo $user->bsn !== null ? htmlspecialchars($user->bsn) : ''; ?>">
    </div>
    <div class="form-group select-responsive">
        <label for="rol" class="form-label">Rol *</label>
        <div class="select-wrapper">
          <select class="form-select" id="rol" name="rol" required>
            <option value="klant" <?= $user->rol === 'klant' ? 'selected' : ''; ?>>Klant</option>
            <option value="instructeur" <?= $user->rol === 'instructeur' ? 'selected' : ''; ?>>Instructeur</option>
            <option value="beheerder" <?= $user->rol === 'beheerder' ? 'selected' : ''; ?>>Beheerder</option>
          </select>
        </div>
      </div>
      <div class="form-actions-responsive">
        <input type="submit" name="submit" value="Bijwerken" class="signup-button">
      </div>
</form>

    <?php else: ?>
            <p>Geen gegevens gevonden voor deze gebruiker.</p>
        <?php            header('Refresh:3; ' . URLROOT . '/Accountoverzicht/index');
        endif; ?>
</div>

</body>
<script src="<?= URLROOT; ?>/public/js/accounts.js"></script>
<?php require   APPROOT . '/views/includes/footer.php'; ?>