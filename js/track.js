document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('tracking-form');
    const result = document.getElementById('tracking-result');
  
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      const code = document.getElementById('tracking-number').value.trim();
  
      result.innerHTML = ''; // Καθαρίζουμε το αποτέλεσμα πριν
  
      if (code === "123456") {
        result.innerHTML = `
          <p>📦 Η αποστολή σας βρίσκεται σε διανομή!</p>
          <div class="progress-bar">
            <div class="progress" style="width: 70%;">Σε Διανομή</div>
          </div>
        `;
      } else if (code === "000000") {
        result.innerHTML = `
          <p>✅ Η αποστολή σας παραδόθηκε!</p>
          <div class="progress-bar">
            <div class="progress" style="width: 100%; background: #28a745;">Παραδόθηκε</div>
          </div>
        `;
      } else {
        result.innerHTML = `<p>❌ Δεν βρέθηκε αποστολή με αυτόν τον αριθμό.</p>`;
      }
    });
  });
  