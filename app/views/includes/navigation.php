<!-- Navigation bar extracted from header.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="#">Windkracht-12</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center gap-2">
        <li class="nav-item"><a class="nav-link " href="<?= URLROOT; ?>/homepages/index">Home</a></li>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'beheerder'): ?>
      
          <li class="nav-item"><a class="nav-link" href="<?= URLROOT; ?>/Reservations/inplannen">inplannen</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'beheerder' || isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'instructeur'): ?>
          <li class="nav-item"><a class="nav-link" href="<?= URLROOT; ?>/Accountoverzicht/index">Accountoverzicht</a></li>
   <li class="nav-item"><a class="nav-link" href="<?= URLROOT; ?>/Reservations/index">reserverings overzicht</a>
          </li>
          <li class="nav-item"><a class="nav-link" href="<?= URLROOT; ?>/Reservations/klantreserveringen">Mijn lessen</a>
          </li>
        <?php endif; ?>
        <?php if (!isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="btn btn-primary ms-2" href="<?= URLROOT; ?>/Signup/index">signup</a></li>
          <li class="nav-item"><a class="btn btn-primary ms-2" href="<?= URLROOT; ?>/Users/login">login</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="<?= URLROOT; ?>/Reservations/book">reserveren</a></li>

          <li class="nav-item"><a class="nav-link" href="<?= URLROOT; ?>/Reservations/myreservations">mijn
              reserveringen</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= URLROOT; ?>/Users/userbeheer">gebruikersbeheer</a></li>
          <li class="nav-item"><a class="btn btn-danger ms-2" href="<?= URLROOT; ?>/Users/logout">logout</a></li>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item">
            <span class="nav-link disabled" style="color:#fff;">
              <?php
                $naam = $_SESSION['user_name'] ?? '';

                $rol = $_SESSION['user_role'] ?? '';
                echo htmlspecialchars(trim($naam)) . ' (' . htmlspecialchars($rol) . ')';
              ?>
            </span>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>