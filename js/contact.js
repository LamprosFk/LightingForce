document.addEventListener('DOMContentLoaded', function() {
    // Contact form handling
    const form = document.getElementById('contact-form');
    const resultDiv = document.getElementById('contact-result');

    if (!form || !resultDiv) {
        return;
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Show loading state
        resultDiv.innerHTML = '<p class="loading">Αποστολή μηνύματος...</p>';
        resultDiv.className = 'form-result loading';

        try {
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: form.method,
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                resultDiv.innerHTML = `<p class="success">${data.message}</p>`;
                resultDiv.className = 'form-result success';
                form.reset();
    } else {
                resultDiv.innerHTML = `<p class="error">${data.error}</p>`;
                resultDiv.className = 'form-result error';
            }
        } catch (error) {
            resultDiv.innerHTML = '<p class="error">Σφάλμα κατά την αποστολή του μηνύματος. Παρακαλώ δοκιμάστε ξανά αργότερα.</p>';
            resultDiv.className = 'form-result error';
        }
    });

    // Newsletter form handling
    const newsletterForm = document.getElementById('newsletter-form');
    const newsletterResult = document.getElementById('newsletter-result');

    if (!newsletterForm || !newsletterResult) {
        return;
    }

    newsletterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Show loading state
        newsletterResult.innerHTML = '<p class="loading">Εγγραφή σε εξέλιξη...</p>';
        newsletterResult.className = 'form-result loading';

        try {
            const formData = new FormData(newsletterForm);
            const response = await fetch(newsletterForm.action, {
                method: newsletterForm.method,
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                newsletterResult.innerHTML = `<p class="success">${data.message}</p>`;
                newsletterResult.className = 'form-result success';
                newsletterForm.reset();
            } else {
                newsletterResult.innerHTML = `<p class="error">${data.error}</p>`;
                newsletterResult.className = 'form-result error';
            }
        } catch (error) {
            newsletterResult.innerHTML = '<p class="error">Σφάλμα κατά την εγγραφή. Παρακαλώ δοκιμάστε ξανά αργότερα.</p>';
            newsletterResult.className = 'form-result error';
    }
    });
  });
  