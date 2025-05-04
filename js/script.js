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
// Εφαρμογή του θέματος κατά την φόρτωση
applyTheme();
// Χάρτης και Markers
function initMap() {
  // Τοποθεσία του κεντρικού χάρτη (π.χ. Αθήνα)
  var mapOptions = {
    center: { lat: 37.9838, lng: 23.7275 },
    zoom: 12,
  };

  // Δημιουργία του χάρτη
  var map = new google.maps.Map(document.getElementById("map"), mapOptions);

  // Καταστήματα (με διευθύνσεις ή συντεταγμένες)
  var stores = [
    { name: "Κατάστημα Αθήνα", location: { lat: 37.9838, lng: 23.7275 } },
    { name: "Κατάστημα Θεσσαλονίκη", location: { lat: 40.6401, lng: 22.9444 } },
    { name: "Κατάστημα Πάτρα", location: { lat: 38.2466, lng: 21.7356 } },
  ];

  // Προσθήκη Markers για τα καταστήματα
  stores.forEach(function (store) {
    var marker = new google.maps.Marker({
      position: store.location,
      map: map,
      title: store.name,
    });

    // Προσθήκη InfoWindow (popup) για κάθε marker
    var infoWindow = new google.maps.InfoWindow({
      content: store.name,
    });

    marker.addListener("click", function () {
      infoWindow.open(map, marker);
    });
  });
}
