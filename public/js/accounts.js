// accounts.js - Client-side validatie voor accountbeheer formulieren

document.addEventListener('DOMContentLoaded', function() {
  // Validatie voor toevoegen en updaten van accounts
  const addForm = document.getElementById('addForm');
  const updateForm = document.getElementById('updateForm');

  function validateAccountForm(form) {
    const errorDiv = form.querySelector('#clientError');
    if (!errorDiv) return true;
    errorDiv.style.display = 'none';
    errorDiv.innerText = '';
    let errors = [];
    const email = form.querySelector('input[name="email"]').value.trim();
    const rol = form.querySelector('select[name="rol"]')?.value;
    const geboortedatum = form.querySelector('input[name="geboortedatum"]')?.value;
    // E-mail validatie
    if (!email) {
      errors.push('E-mailadres is verplicht.');
    } else {
      const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailPattern.test(email)) {
        errors.push('Vul een geldig e-mailadres in.');
      }
    }
    // Rol verplicht
    if (!rol) {
      errors.push('Kies een rol.');
    }
    // Geboortedatum mag niet in de toekomst liggen
    if (geboortedatum) {
      const geboortedatumDate = new Date(geboortedatum);
      const vandaag = new Date();
      vandaag.setHours(0,0,0,0);
      if (geboortedatumDate > vandaag) {
        errors.push('Geboortedatum mag niet in de toekomst liggen.');
      }
    }
    if (errors.length > 0) {
      errorDiv.innerText = errors.join('\n');
      errorDiv.style.display = 'block';
      window.scrollTo({ top: 0, behavior: 'smooth' });
      return false;
    }
    return true;
  }

  if (addForm) {
    addForm.addEventListener('submit', function(e) {
      if (!validateAccountForm(addForm)) e.preventDefault();
    });
  }
  if (updateForm) {
    updateForm.addEventListener('submit', function(e) {
      const errorDiv = document.getElementById('clientError');
      errorDiv.style.display = 'none';
      errorDiv.innerText = '';
      let errors = [];
      const email = document.getElementById('email').value.trim();
      const rol = document.getElementById('rol').value;
      const geboortedatum = document.getElementById('geboortedatum').value;
      // E-mail validatie
      if (!email) {
        errors.push('E-mailadres is verplicht.');
      } else {
        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
          errors.push('Vul een geldig e-mailadres in.');
        }
      }
      // Rol verplicht
      if (!rol) {
        errors.push('Kies een rol.');
      }
      // Geboortedatum mag niet in de toekomst liggen
      if (geboortedatum) {
        const geboortedatumDate = new Date(geboortedatum);
        const vandaag = new Date();
        vandaag.setHours(0,0,0,0);
        if (geboortedatumDate > vandaag) {
          errors.push('Geboortedatum mag niet in de toekomst liggen.');
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
});
