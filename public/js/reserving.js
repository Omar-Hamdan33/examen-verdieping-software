// reserving.js - Client-side validatie en duo-velden logica voor les boeken

document.addEventListener('DOMContentLoaded', function() {
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
  if (packageSelect) {
    packageSelect.addEventListener('change', checkDuo);
    checkDuo();
  }

  // Client-side validatie
  const bookForm = document.getElementById('bookForm');
  if (bookForm) {
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
  }

  // Herplan-formulier validatie
  const herplanForm = document.getElementById('herplanForm');
  if (herplanForm) {
    herplanForm.addEventListener('submit', function(e) {
      const errorDiv = document.getElementById('clientError');
      errorDiv.style.display = 'none';
      errorDiv.innerText = '';
      const slot = document.getElementById('time_slot').value;
      if (!slot) {
        e.preventDefault();
        errorDiv.innerText = 'Kies een tijdslot.';
        errorDiv.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  }

  // Client-side validatie voor status-wijziging in reserveringenoverzicht
  const statusForms = document.querySelectorAll('form[action$="/Reservations/changeStatus"]');
  const mainContainer = document.querySelector('.reservations-container');

  statusForms.forEach(form => {
    form.addEventListener('submit', function(e) {
      const select = form.querySelector('select[name="status"]');
      if (!select.value) {
        e.preventDefault();
        let errorDiv = document.getElementById('statusClientError');
        if (!errorDiv) {
          errorDiv = document.createElement('div');
          errorDiv.id = 'statusClientError';
          errorDiv.className = 'alert alert-danger';
          errorDiv.style.marginBottom = '1em';
          mainContainer.insertBefore(errorDiv, mainContainer.firstChild.nextSibling);
        }
        errorDiv.innerText = 'Selecteer een status.';
        errorDiv.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  });

  // Validatie voor inplannen-formulier
  const inplannenForm = document.getElementById('inplannenForm');
  if (inplannenForm) {
    inplannenForm.addEventListener('submit', function(e) {
      const errorDiv = document.getElementById('clientError');
      errorDiv.style.display = 'none';
      errorDiv.innerText = '';

      const instructor = document.getElementById('instructor_id').value;
      const date = document.getElementById('available_date').value;
      const start = document.getElementById('start_time').value;
      const end = document.getElementById('end_time').value;
      let errors = [];

      if (!instructor) errors.push('Kies een instructeur.');
      if (!date) errors.push('Vul een datum in.');
      if (!start) errors.push('Vul een starttijd in.');
      if (!end) errors.push('Vul een eindtijd in.');

      // Datum mag niet in het verleden liggen
      if (date) {
        const today = new Date();
        const inputDate = new Date(date);
        today.setHours(0,0,0,0);
        if (inputDate < today) {
          errors.push('Datum mag niet in het verleden liggen.');
        }
      }

      // Eindtijd moet na starttijd liggen
      if (start && end && start >= end) {
        errors.push('Eindtijd moet na starttijd liggen.');
      }

      if (errors.length > 0) {
        e.preventDefault();
        errorDiv.innerText = errors.join('\n');
        errorDiv.style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    });
  }

  // Functies voor tonen/verbergen van annuleerreden-formulier
  window.showCancelReasonForm = function(id) {
    document.getElementById('cancel-reason-form-' + id).style.display = '';
  }
  window.hideCancelReasonForm = function(id) {
    document.getElementById('cancel-reason-form-' + id).style.display = 'none';
  }

  document.querySelectorAll('form[action$="/Reservations/doCancelReservation"]').forEach(function(form) {
    form.onsubmit = function() { return validateCancelReasonForm(form); };
  });
});
