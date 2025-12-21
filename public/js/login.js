// login.js - Client-side validatie voor loginformulier

function validateUserbeheerForm() {
  const email = document.getElementById('email').value.trim();
  const newPassword = document.getElementById('new_password').value;
  const newPassword2 = document.getElementById('new_password2').value;
  let errors = [];

  // Email validatie
  if (!email.match(/^\S+@\S+\.\S+$/)) {
    errors.push('Vul een geldig e-mailadres in.');
  }

  // Wachtwoord validatie (alleen als ingevuld)
  if (newPassword.length > 0) {
    if (newPassword.length < 12) {
      errors.push('Wachtwoord moet minimaal 12 tekens zijn.');
    }
    if (!/[A-Z]/.test(newPassword)) {
      errors.push('Wachtwoord moet een hoofdletter bevatten.');
    }
    if (!/[0-9]/.test(newPassword)) {
      errors.push('Wachtwoord moet een cijfer bevatten.');
    }
    if (!/[@#\$!%*?&.,;:\-_=+()\[\]{}<>]/.test(newPassword)) {
      errors.push('Wachtwoord moet een leesteken bevatten, zoals @ of #.');
    }
    if (newPassword !== newPassword2) {
      errors.push('De wachtwoorden komen niet overeen.');
    }
  }

  // Toon foutmeldingen
  if (errors.length > 0) {
    let alertDiv = document.querySelector('.alert.alert-danger');
    if (!alertDiv) {
      alertDiv = document.createElement('div');
      alertDiv.className = 'alert alert-danger';
      document.querySelector('.userbeheer-container').insertBefore(alertDiv, document.querySelector('form'));
    }
    alertDiv.innerHTML = errors.map(e => `<div>${e}</div>`).join('');
    window.scrollTo({ top: 0, behavior: 'smooth' });
    return false;
  }
  return true;
}

document.addEventListener('DOMContentLoaded', function() {
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function(e) {
      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      let errors = [];
      // Email validatie
      if (!email.match(/^\S+@\S+\.\S+$/)) {
        errors.push('Vul een geldig e-mailadres in.');
      }
      // Wachtwoord validatie: minimaal 12 tekens, hoofdletter, cijfer, leesteken
      if (password.length < 12) {
        errors.push('Wachtwoord moet minimaal 12 tekens zijn.');
      }
      if (!/[A-Z]/.test(password)) {
        errors.push('Wachtwoord moet een hoofdletter bevatten.');
      }
      if (!/[0-9]/.test(password)) {
        errors.push('Wachtwoord moet een cijfer bevatten.');
      }
      if (!/[@#\$!%*?&.,;:\-_=+()\[\]{}<>]/.test(password)) {
        errors.push('Wachtwoord moet een leesteken bevatten, zoals @ of #.');
      }
      if (errors.length > 0) {
        let alertDiv = document.querySelector('.alert.alert-danger');
        if (!alertDiv) {
          alertDiv = document.createElement('div');
          alertDiv.className = 'alert alert-danger';
          document.querySelector('.login-container').insertBefore(alertDiv, document.querySelector('form'));
        }
        alertDiv.innerHTML = errors.map(e => `<div>${e}</div>`).join('');
        window.scrollTo({ top: 0, behavior: 'smooth' });
        e.preventDefault();
      }
    });
  }
  const userbeheerForm = document.getElementById('userbeheerForm');
  if (userbeheerForm) {
    userbeheerForm.onsubmit = function() { return validateUserbeheerForm(); };
  }
});
