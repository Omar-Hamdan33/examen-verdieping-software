<?php 
require APPROOT . '/views/includes/header.php'; 

$user = $data['HASHEDId'];
?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/signup.css">
<div class="signup-container signup-center">
  <h1>Wachtwoord aanmaken</h1>
  <?php if (!empty($data['error'])): ?>
    <div class="alert alert-danger"> <?= htmlspecialchars($data['error']); ?> </div>
  <?php endif; ?>
  <form method="POST" action="<?= URLROOT; ?>/Signup/setPassword">
    <input type="hidden" name="id" value="<?php echo ( $user ); ?>">
    <div class="form-group">
      <label for="wachtwoord">Wachtwoord</label>
      <input type="password" id="wachtwoord" name="wachtwoord" required>
    </div>
    <div class="form-group">
      <label for="wachtwoord2">Herhaal wachtwoord</label>
      <input type="password" id="wachtwoord2" name="wachtwoord2" required>
      <small class="form-text text-muted">Minimaal 12 tekens, een hoofdletter, een cijfer en een leesteken (@, #, ...).</small>
    </div>
    <input type="submit" value="Opslaan" class="signup-button">
  </form>
</div>
<script src="<?= URLROOT; ?>/public/js/signup.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
