// signup.js - Client-side validatie voor registratieformulier

document.addEventListener('DOMContentLoaded', function() {
  const form = document.getElementById('registratieForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      const email = document.getElementById('email').value.trim();
      let errors = [];
      if (!email.match(/^\S+@\S+\.\S+$/)) {
        errors.push('Vul een geldig e-mailadres in.');
      }
      if (errors.length > 0) {
        let alertDiv = document.querySelector('.alert.alert-danger');
        if (!alertDiv) {
          alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-danger';
          document.querySelector('.signup-container').insertBefore(alertDiv, document.querySelector('form'));
        }
        alertDiv.innerHTML = errors.map(e => `<div>${e}</div>`).join('');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        e.preventDefault();
      }
    });
  }

  // Validatie voor wachtwoord reset e-mail formulier
  const resetForm = document.querySelector('form[action$="/Signup/restPassword"]');
  if (resetForm) {
    resetForm.addEventListener('submit', function(e) {
      const email = document.getElementById('email').value.trim();
      let errors = [];
      if (!email.match(/^\S+@\S+\.\S+$/)) {
        errors.push('Vul een geldig e-mailadres in.');
      }
      if (errors.length > 0) {
        let alertDiv = document.querySelector('.alert.alert-danger');
        if (!alertDiv) {
          alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-danger';
          document.querySelector('.signup-container').insertBefore(alertDiv, document.querySelector('form'));
        }
        alertDiv.innerHTML = errors.map(e => `<div>${e}</div>`).join('');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        e.preventDefault();
      }
    });
  }

  // Validatie voor wachtwoord aanmaken formulier
  const setPwForm = document.querySelector('form[action$="/Signup/setPassword"]');
  if (setPwForm) {
    setPwForm.addEventListener('submit', function(e) {
      const pw1 = document.getElementById('wachtwoord').value;
      const pw2 = document.getElementById('wachtwoord2').value;
      let errors = [];
      if (pw1.length < 12) {
        errors.push('Wachtwoord moet minimaal 12 tekens zijn.');
      }
      if (!/[A-Z]/.test(pw1)) {
        errors.push('Wachtwoord moet een hoofdletter bevatten.');
      }
      if (!/[0-9]/.test(pw1)) {
        errors.push('Wachtwoord moet een cijfer bevatten.');
      }
      if (!/[@#\$!%*?&.,;:\-_=+()\[\]{}<>]/.test(pw1)) {
        errors.push('Wachtwoord moet een leesteken bevatten, zoals @ of #.');
      }
      if (pw1 !== pw2) {
        errors.push('De wachtwoorden komen niet overeen.');
      }
      if (errors.length > 0) {
        let alertDiv = document.querySelector('.alert.alert-danger');
        if (!alertDiv) {
          alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-danger';
          document.querySelector('.signup-container').insertBefore(alertDiv, document.querySelector('form'));
        }
        alertDiv.innerHTML = errors.map(e => `<div>${e}</div>`).join('');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        e.preventDefault();
      }
    });
  }
});
