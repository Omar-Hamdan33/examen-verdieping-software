<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/signup.css">
<div class="signup-container">
  <h1>Wachtwoord resetten</h1>
  <?php if (!empty($data['error'])): ?>
    <div class="alert alert-danger"> <?= htmlspecialchars($data['error']); ?> </div>
  <?php endif; ?>
  <?php if (!empty($data['success'])): ?>
    <div class="alert alert-success"> <?= htmlspecialchars($data['success']); ?> </div>
  <?php endif; ?>
  <form method="POST" action="<?= URLROOT; ?>/Signup/restPassword">
    <div class="form-group">
      <label for="email">E-mail</label>
      <input type="email" id="email" name="email" required value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : '' ?>">
    </div>
    <input type="submit" value="Opslaan" class="signup-button">
  </form>
</div>
<script src="<?= URLROOT; ?>/public/js/signup.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
