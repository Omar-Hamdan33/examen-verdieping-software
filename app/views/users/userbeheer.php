<?php require APPROOT . '/views/includes/header.php';
$user = $data['user'];?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/users.css">

<div class="userbeheer-container">
<?php if (!empty($data['errors'])): ?>
  <div class="alert alert-danger">
    <?php foreach ($data['errors'] as $error): ?>
      <div><?= htmlspecialchars($error) ?></div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<?php if (!empty($data['success'])): ?>
  <div class="alert alert-success" style="margin-bottom:1rem;">
    <?= htmlspecialchars($data['success']);?>
  </div>
<?php endif; ?>

<h2>Gegevens wijzigen</h2>
<form action="/users/userbeheer" method="POST" id="userbeheerForm">
    <input type="hidden" name="id" value="<?= htmlspecialchars($user->id) ?>">
    <label>Voornaam:</label>
    <input type="text" name="voornaam" id="voornaam" value="<?= htmlspecialchars($user->voornaam ?? '') ?>">
    <label>Tussenvoegsel:</label>
    <input type="text" name="tussenvoegsel" id="tussenvoegsel" value="<?= htmlspecialchars($user->tussenvoegsel ?? '') ?>">
    <label>Achternaam:</label>
    <input type="text" name="achternaam" id="achternaam" value="<?= htmlspecialchars($user->achternaam ?? '') ?>">
    <label>Email:</label>
    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user->email) ?>" required>
    <label>Telefoon:</label>
    <input type="text" name="telefoon" id="telefoon" value="<?= htmlspecialchars($user->telefoon ?? '') ?>">
    <label>Adres:</label>
    <input type="text" name="adres" id="adres" value="<?= htmlspecialchars($user->adres ?? '') ?>">
    <label>Woonplaats:</label>
    <input type="text" name="woonplaats" id="woonplaats" value="<?= htmlspecialchars($user->woonplaats ?? '') ?>">
    <label>Geboortedatum:</label>
    <input type="date" name="geboortedatum" id="geboortedatum" value="<?= htmlspecialchars($user->geboortedatum ?? '') ?>">
    <?php if (isset($user->rol) && in_array($user->rol, ['instructeur', 'beheerder'])): ?>
        <label>BSN:</label>
        <input type="text" name="bsn" id="bsn" value="<?= htmlspecialchars($user->bsn ?? '') ?>">
    <?php endif; ?>
    <div class="mb-3">
        <label for="new_password">Nieuw wachtwoord</label>
        <input type="password" name="new_password" id="new_password" class="form-control">
    </div>
    <div class="mb-3">
        <label for="new_password2">Herhaal nieuw wachtwoord</label>
        <input type="password" name="new_password2" id="new_password2" class="form-control">
        <small class="form-text text-muted">Laat leeg als je het wachtwoord niet wilt wijzigen. Minimaal 12 tekens, een hoofdletter, een cijfer en een leesteken.</small>
    </div>
    <input type="submit" value="Opslaan">
</form>
<script src="<?= URLROOT; ?>/public/js/login.js"></script>
</div>
<?php require APPROOT . '/views/includes/footer.php'; ?>
