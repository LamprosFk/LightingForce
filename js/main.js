document.addEventListener('DOMContentLoaded', function () {
    const body = document.body;
    const themeToggle = document.getElementById('theme-toggle');
  
    // ➤ ΕΦΑΡΜΟΓΗ αποθηκευμένου θέματος
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
      body.setAttribute('data-theme', savedTheme);
      if (themeToggle) {
        themeToggle.textContent = savedTheme === 'dark' ? '☀️' : '🌙';
      }
    }
  
    // ➤ ΕΝΑΛΛΑΓΗ θέματος με αποθήκευση
    if (themeToggle) {
      themeToggle.addEventListener('click', () => {
        const currentTheme = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        body.setAttribute('data-theme', currentTheme);
        themeToggle.textContent = currentTheme === 'dark' ? '☀️' : '🌙';
        localStorage.setItem('theme', currentTheme);
      });
    }
  
    // ➤ Hamburger menu
    const menuToggle = document.querySelector('.menu-toggle');
    const navList = document.querySelector('nav ul');
    if (menuToggle && navList) {
      menuToggle.addEventListener('click', () => {
        navList.classList.toggle('show');
      });
    }
  
    // ➤ Υπολογισμός κόστους (αν υπάρχει φόρμα)
    const form = document.getElementById('cost-form');
    const modal = document.getElementById('cost-modal');
    const modalResult = document.getElementById('modal-cost-result');
    const closeBtn = document.querySelector('.close-btn');
  
    if (form && modal && modalResult) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        const destination = parseFloat(document.getElementById('destination')?.value) || 0;
        const serviceType = parseFloat(document.getElementById('service-type')?.value) || 0;
        let total = destination + serviceType;
  
        if (document.getElementById('cod')?.checked) {
          total += parseFloat(document.getElementById('cod').value) || 0;
        }
        if (document.getElementById('insurance')?.checked) {
          total += parseFloat(document.getElementById('insurance').value) || 0;
        }
        if (document.getElementById('sms')?.checked) {
          total += parseFloat(document.getElementById('sms').value) || 0;
        }
  
        modalResult.innerText = `${total.toFixed(2)} €`;
        modal.style.display = 'block';
      });
  
      if (closeBtn) {
        closeBtn.addEventListener('click', function () {
          modal.style.display = 'none';
        });
  
        window.addEventListener('click', function (e) {
          if (e.target === modal) {
            modal.style.display = 'none';
          }
        });
      }
    }
  });
  