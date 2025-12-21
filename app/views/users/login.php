<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/users.css">

<div class="login-container">
  <?php if (!empty($data['errors']['credentials'])): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($data['errors']['credentials']); ?>
    </div>
  <?php endif; ?>
  <?php if (!empty($data['errors']['form'])): ?>
    <div class="alert alert-danger">
      <?= htmlspecialchars($data['errors']['form']); ?>
    </div>
  <?php endif; ?>
  <form action="<?= URLROOT; ?>/users/login" method="post" id="loginForm">
    <div class="form-group">
      <label for="email">E-mail</label>
      <input
        type="email"
        name="email"
        id="email"
        class="form-control <?= isset($data['errors']['email']) ? 'is-invalid' : ''; ?>"
        value="<?= htmlspecialchars($data['email']); ?>"
        required
      >
      <?php if (isset($data['errors']['email'])): ?>
        <div class="invalid-feedback">
          <?= htmlspecialchars($data['errors']['email']); ?>
        </div>
      <?php endif; ?>
    </div>

    <div class="form-group">
      <label for="password">Wachtwoord</label>
      <input
        type="password"
        name="password"
        id="password"
        class="form-control <?= isset($data['errors']['password']) ? 'is-invalid' : ''; ?>"
        required
      >
      <?php if (isset($data['errors']['password'])): ?>
        <div class="invalid-feedback">
          <?= htmlspecialchars($data['errors']['password']); ?>
        </div>
      <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary">Inloggen</button>
    <a href="<?= URLROOT; ?>/Signup/restPassword" class="btn btn-link">Wachtwoord vergeten?</a>
  </form>
</div>
<script src="<?= URLROOT; ?>/public/js/login.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>

