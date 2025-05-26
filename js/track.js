document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('tracking-form');
    const result = document.getElementById('tracking-result');
  
    form.addEventListener('submit', async function (e) {
        e.preventDefault();
    
        const trackingId = document.getElementById('tracking-number').value.trim();
  
        if (!trackingId) {
            return;
        }

        result.innerHTML = '<p>🔍 Αναζήτηση αποστολής...</p>';

        try {
            const response = await fetch(`php/deliveries/get_deliveries.php?tracking_id=${encodeURIComponent(trackingId)}`);
            const data = await response.json();

            if (data && data.length > 0) {
                const delivery = data[0];
                const createdDate = new Date(delivery.created_at).toLocaleDateString('el-GR', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                result.innerHTML = `
                    <div class="tracking-details">
                        <h3>📦 Στοιχεία Αποστολής</h3>
                        <div class="tracking-table">
                            <div class="table-row">
                                <div class="table-cell label">Αριθμός Αποστολής</div>
                                <div class="table-cell value">${delivery.tracking_id}</div>
                            </div>
                            <div class="table-row">
                                <div class="table-cell label">Αποστολέας</div>
                                <div class="table-cell value">${delivery.sender}</div>
                            </div>
                            <div class="table-row">
                                <div class="table-cell label">Παραλήπτης</div>
                                <div class="table-cell value">${delivery.recipient}</div>
                            </div>
                            <div class="table-row">
                                <div class="table-cell label">Κατάσταση</div>
                                <div class="table-cell value">${delivery.status}</div>
                            </div>
                            <div class="table-row">
                                <div class="table-cell label">Ημερομηνία Δημιουργίας</div>
                                <div class="table-cell value">${createdDate}</div>
                            </div>
                        </div>
                        
                        <div class="tracking-status">
                            <h4>Κατάσταση Αποστολής</h4>
                            <div class="progress-bar">
                                <div class="progress" style="width: ${calculateStatusProgress(delivery.status)}%;">${delivery.status}</div>
                            </div>
                        </div>
                    </div>
                `;
            } else {
                result.innerHTML = `
                    <div class="error-message">
                        <p>❌ Δεν βρέθηκε αποστολή με αυτόν τον αριθμό.</p>
                        <p class="help-text">Βεβαιωθείτε ότι έχετε εισάγει σωστά τον αριθμό αποστολής.</p>
                    </div>`;
            }
        } catch (error) {
            console.error('Error:', error);
            result.innerHTML = `
                <div class="error-message">
                    <p>❌ Σφάλμα κατά την αναζήτηση της αποστολής.</p>
                    <p class="help-text">Παρακαλώ δοκιμάστε ξανά αργότερα.</p>
                </div>`;
        }
    });
});
  
function calculateStatusProgress(status) {
    const statusProgress = {
        'Σε Επεξεργασία': 20,
        'Σε Μεταφορά': 40,
        'Στο Κέντρο Διανομής': 60,
        'Σε Διανομή': 80,
        'Παραδόθηκε': 100
    };
    return statusProgress[status] || 0;
}
