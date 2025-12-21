<?php require APPROOT . '/views/includes/header.php'; ?>
<?php require APPROOT . '/views/includes/navigation.php'; ?>
<link rel="stylesheet" href="<?= URLROOT; ?>/public/css/signup.css">
<div class="signup-container">
    <h1>Registreren</h1>
    <?php if (!empty($data['error'])): ?>
        <div class="alert alert-danger"> <?= htmlspecialchars($data['error']); ?> </div>
    <?php endif; ?>
    <form id="registratieForm" method="POST" action="<?= URLROOT; ?>/Signup/create">
        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="text" id="email" name="email" value="<?= isset($data['email']) ? htmlspecialchars($data['email']) : '' ?>" required>
        </div>
        <input type="submit" name="submit" value="Registreren" class="signup-button">
    </form>
    <p>Heb je al een account? <a href="<?= URLROOT; ?>/users/login">Login hier</a></p>
</div>
<script src="<?= URLROOT; ?>/public/js/signup.js"></script>
<?php require APPROOT . '/views/includes/footer.php'; ?>
