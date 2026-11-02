document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form[data-validate]').forEach(form => {
    form.addEventListener('submit', event => {
      let firstInvalid = null;
      form.querySelectorAll('[required]').forEach(field => {
        field.setCustomValidity('');
        if (!field.value.trim()) field.setCustomValidity('Please complete this field.');
        if (field.type === 'email' && field.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(field.value))
          field.setCustomValidity('Enter a valid email address.');
        if (field.name === 'password' && field.value.length < 8)
          field.setCustomValidity('Password must contain at least 8 characters.');
        if (!field.checkValidity() && !firstInvalid) firstInvalid = field;
      });
      if (firstInvalid) { event.preventDefault(); firstInvalid.reportValidity(); }
    });
  });
});
