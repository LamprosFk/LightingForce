document.addEventListener('DOMContentLoaded', function () {
  const body = document.body;
  const themeToggle = document.getElementById('theme-toggle');

  // Get saved theme from localStorage or default to light
  const savedTheme = localStorage.getItem('theme') || 'light';
  body.setAttribute('data-theme', savedTheme);
  if (themeToggle) {
    themeToggle.textContent = savedTheme === 'dark' ? '☀️' : '🌙';
  }

  // Theme toggle functionality
  if (themeToggle) {
    themeToggle.addEventListener('click', () => {
      const currentTheme = body.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
      body.setAttribute('data-theme', currentTheme);
      themeToggle.textContent = currentTheme === 'dark' ? '☀️' : '🌙';
      localStorage.setItem('theme', currentTheme);
    });
  }

  // Hamburger menu
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

  // Mobile menu toggle
  const navLinks = document.querySelector('.nav-links');

  if (menuToggle && navLinks) {
    menuToggle.addEventListener('click', function() {
      navLinks.classList.toggle('active');
      menuToggle.classList.toggle('active');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
      if (!event.target.closest('.nav-links') && !event.target.closest('.menu-toggle')) {
        navLinks.classList.remove('active');
        menuToggle.classList.remove('active');
      }
    });

    // Close menu when window is resized above mobile breakpoint
    window.addEventListener('resize', function() {
      if (window.innerWidth > 768) {
        navLinks.classList.remove('active');
        menuToggle.classList.remove('active');
      }
    });
  }

  // Add smooth scrolling for anchor links
  document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute('href'));
      if (target) {
        target.scrollIntoView({
          behavior: 'smooth',
          block: 'start'
        });
        // Close mobile menu if open
        if (navLinks && navLinks.classList.contains('active')) {
          navLinks.classList.remove('active');
          menuToggle.classList.remove('active');
        }
      }
    });
  });

  // Add active class to current navigation item
  const currentLocation = window.location.pathname;
  document.querySelectorAll('.nav-links a').forEach(link => {
    if (link.getAttribute('href') === currentLocation) {
      link.classList.add('active');
    }
  });
});
