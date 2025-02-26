document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.toggle-password').forEach(toggle => {
      toggle.addEventListener('click', function () {
        const container = this.closest('.password-container'); 
        const passwordInput = container.querySelector('input'); 
        const icon = this.querySelector('i');
  
        passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
  
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
      });
    });
  });
  