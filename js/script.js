document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('cost-form');
  const modal = document.getElementById('cost-modal');
  const modalResult = document.getElementById('modal-cost-result');
  const closeBtn = document.querySelector('.close-btn');

  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();

      const destination = parseFloat(document.getElementById('destination').value) || 0;
      const serviceType = parseFloat(document.getElementById('service-type').value) || 0;
      let total = destination + serviceType;

      if (document.getElementById('cod').checked) {
        total += parseFloat(document.getElementById('cod').value) || 0;
      }
      if (document.getElementById('insurance').checked) {
        total += parseFloat(document.getElementById('insurance').value) || 0;
      }
      if (document.getElementById('sms').checked) {
        total += parseFloat(document.getElementById('sms').value) || 0;
      }

      modalResult.innerText = `${total.toFixed(2)} €`; // Βάζουμε την τιμή
      modal.style.display = 'block'; // Εμφανίζουμε το Modal
    });
  }

  // Κλείσιμο του popup
  closeBtn.addEventListener('click', function() {
    modal.style.display = 'none';
  });

  window.addEventListener('click', function(e) {
    if (e.target == modal) {
      modal.style.display = 'none';
    }
  });
});
