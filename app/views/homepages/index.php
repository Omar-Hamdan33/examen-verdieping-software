<?php require_once APPROOT . '/views/includes/header.php'; 
?>
  <?php require APPROOT . '/views/includes/navigation.php'; ?>

<!-- Voor het centreren van de container gebruiken we het boorstrap grid -->
<div class="container">
    <div class="row mt-3">

        <div class="col-2"></div>

        <div class="col-8">


        </div>
        
        <div class="col-2"></div>
        
    </div>

</div>



  <!-- Hero Section -->
  <section id="home" class="hero py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-10 col-lg-8 text-center">
          <h1 class="display-4">Ervaar de sensatie van kitesurfen</h1>
          <p class="lead">Leer kitesurfen bij Windkracht-12 op de mooiste locaties langs de Nederlandse kust.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Packages Section -->
  <section id="packages" class="py-5">
    <div class="container">
      <h2 class="mb-4 text-center">Onze Pakketten</h2>
      <div class="row g-4">
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100">
            <img src="/public/img/duo.png" class="card-img-top" alt="Privéles">
            <div class="card-body">
              <h5 class="card-title">Privéles</h5>
              <p class="card-text">2,5 uur &ndash; €175 inclusief materialen &ndash; Eén persoon per les &ndash; 1 dagdeel </p>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100">
            <img src="/public/img/duo.png" class="card-img-top" alt="Duo Kiteles">
            <div class="card-body">
              <h5 class="card-title">Losse Duo Kiteles</h5>
              <p class="card-text">3,5 uur &ndash; €135 p.p. inclusief materialen &ndash; Maximaal 2 personen per les &ndash; 1 dagdeel</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100">
            <img src="/public/img/duo.png" class="card-img-top" alt="Pakket 3 lessen">
            <div class="card-body">
              <h5 class="card-title">Duo Pakket (3 lessen)</h5>
              <p class="card-text">10,5 uur &ndash; €375 p.p. inclusief materialen  &ndash;  Maximaal 2 personen per les &ndash;  3 dagdelen</p>
            </div>
          </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
          <div class="card h-100">
            <img src="/public/img/duo.png" class="card-img-top" alt="Pakket 5 lessen">
            <div class="card-body">
              <h5 class="card-title">Duo Pakket (5 lessen)</h5>
              <p class="card-text">17,5 uur &ndash; €675 p.p. inclusief materialen &ndash;  Maximaal 2 personen per les &ndash;  3 dagdelen</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- About Section -->
  <section id="about" class="py-5 bg-light">
    <div class="container">
      <h2 class="mb-4 text-center">Over Windkracht-12</h2>
      <p>Kitesurfschool Windkracht-12 is al 8 jaar actief langs de Nederlandse kust. Onze ervaren instructeurs nemen je mee naar de mooiste spots, van Zandvoort tot Hoek van Holland. Met maximaal 2 leerlingen per instructeur garanderen we persoonlijke aandacht en veiligheid.</p>
      <ul>
        <li>Zandvoort</li>
        <li>Muiderberg</li>
        <li>Wijk aan Zee</li>
        <li>IJmuiden</li>
        <li>Scheveningen</li>
        <li>Hoek van Holland</li>
      </ul>
    </div>
  </section>

  <!-- Contact Section -->
  <section id="contact" class="py-5">
    <div class="container">
      <h2 class="mb-4 text-center">Contact</h2>
      <p class="text-center">Vragen? Bel ons op <a href="tel:+31201234567">020-1234567</a> of mail naar <a href="mailto:info@windkracht12.nl">info@windkracht12.nl</a>.</p>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-dark text-white py-3">
    <div class="container text-center">
      <small>&copy; 2025 KiteSurfschool Windkracht-12. Alle rechten voorbehouden.</small>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php require_once APPROOT . '/views/includes/footer.php'; ?>