<?php require   APPROOT . '/views/includes/header.php'; ?>
  <?php require APPROOT . '/views/includes/navigation.php'; ?>

<div class="container mt-3">
  <div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 text-center text-success">        
      <h3><?= $data['title']; ?></h3>
      <div class="alert alert-danger" role="alert">
        Account is verwijderen je wordt teruggestuurd naar het overzicht.
      </div>
    </div>
  </div>
</div>

<?php require   APPROOT . '/views/includes/footer.php'; ?>