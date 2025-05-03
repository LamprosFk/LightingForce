document.addEventListener('DOMContentLoaded', function () {
  const form = document.getElementById('create-form');
  const result = document.getElementById('submission-result');
  const costResult = document.getElementById('cost-result');

  const senderZipInput = document.querySelector('input[name="sender-zip"]');
  const senderCityInput = document.querySelector('input[name="sender-city"]');
  const recipientZipInput = document.querySelector('input[name="zip"]');
  const recipientCityInput = document.querySelector('input[name="city"]');

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const trackingCode = generateTrackingCode();
    result.innerHTML = `✅ Η αποστολή σας καταχωρήθηκε επιτυχώς!<br>📦 Κωδικός Αποστολής: <strong>${trackingCode}</strong>`;
    form.reset();
    costResult.innerText = '';
    clearMap();
  });

  // Αυτόματη συμπλήρωση πόλης
  senderZipInput.addEventListener('blur', async function () {
    const zip = senderZipInput.value.trim();
    if (isValidGreekZip(zip)) {
      const cityData = await getCityFromZip(zip);
      if (cityData && cityData.locality) {
        senderCityInput.value = cityData.locality;
      }
    } else {
      senderCityInput.value = "";
      alert("❌ Μη έγκυρος Τ.Κ. Αποστολέα!");
    }
  });

  recipientZipInput.addEventListener('blur', async function () {
    const zip = recipientZipInput.value.trim();
    if (isValidGreekZip(zip)) {
      const cityData = await getCityFromZip(zip);
      if (cityData && cityData.locality) {
        recipientCityInput.value = cityData.locality;
      }
    } else {
      recipientCityInput.value = "";
      alert("❌ Μη έγκυρος Τ.Κ. Παραλήπτη!");
    }
  });
});

// ➔ Έλεγχος εγκυρότητας Τ.Κ.
function isValidGreekZip(zip) {
  return /^\d{5}$/.test(zip);
}

// ➔ Υπολογισμός Κόστους
async function calculateCost() {
  const senderZip = document.querySelector('input[name="sender-zip"]').value.trim();
  const recipientZip = document.querySelector('input[name="zip"]').value.trim();
  const costResult = document.getElementById('cost-result');

  if (!senderZip || !recipientZip) {
    alert("Παρακαλώ εισάγετε και τους δύο Τ.Κ.");
    return;
  }

  if (!isValidGreekZip(senderZip)) {
    costResult.innerText = "❌ Μη έγκυρος Τ.Κ. Αποστολέα.";
    return;
  }

  if (!isValidGreekZip(recipientZip)) {
    costResult.innerText = "❌ Μη έγκυρος Τ.Κ. Παραλήπτη.";
    return;
  }

  costResult.innerText = "🔄 Υπολογισμός κόστους...";
  costResult.classList.add('loading-text');

  const senderCity = await getCityFromZip(senderZip);
  const recipientCity = await getCityFromZip(recipientZip);

  if (!senderCity || !recipientCity) {
    costResult.innerText = "❌ Δεν βρέθηκαν πληροφορίες για τους Τ.Κ.";
    costResult.classList.remove('loading-text');
    return;
  }

  let baseCost = 0;
  let serviceType = parseFloat(document.getElementById('service-type').value);

  if (senderCity.country !== "GR" || recipientCity.country !== "GR") {
    baseCost = 15; // Εξωτερικό
  } else if (senderCity.locality === recipientCity.locality) {
    baseCost = 3; // Εντός Πόλης
  } else {
    baseCost = 5; // Εντός Ελλάδας
  }

  let total = baseCost + serviceType;

  if (document.getElementById('cod').checked) {
    total += parseFloat(document.getElementById('cod').value);
  }
  if (document.getElementById('insurance').checked) {
    total += parseFloat(document.getElementById('insurance').value);
  }
  if (document.getElementById('sms').checked) {
    total += parseFloat(document.getElementById('sms').value);
  }

  costResult.innerHTML = `
    Αποστολή από: <strong>${senderCity.locality}</strong> ➔ <strong>${recipientCity.locality}</strong><br>
    Εκτιμώμενο Κόστος: <span style="color: green;">${total.toFixed(2)}€</span>
  `;
  costResult.classList.remove('loading-text');

  await updateMap(senderZip, recipientZip);
}

// ➔ Δημιουργία Tracking Code
function generateTrackingCode() {
  const randomPart = Math.floor(100 + Math.random() * 900);
  return `023${randomPart}`;
}

// ➔ Λήψη Πόλης και Συντεταγμένων από Zip μέσω Geocoding API
async function getCityFromZip(zip) {
  const apiKey = 'AIzaSyAlx8IedJupc1HzH4H7NwIYc2VZ27Ztykw';
  const urlGreece = `https://maps.googleapis.com/maps/api/geocode/json?address=${zip},+Greece&key=${apiKey}`;
  const urlGlobal = `https://maps.googleapis.com/maps/api/geocode/json?address=${zip}&key=${apiKey}`;

  try {
    // Πρώτο Query: Ψάχνουμε στην Ελλάδα
    const responseGreece = await fetch(urlGreece);
    const dataGreece = await responseGreece.json();

    if (dataGreece.status === "OK") {
      let locality = "";
      let country = "";
      let lat = dataGreece.results[0].geometry.location.lat;
      let lng = dataGreece.results[0].geometry.location.lng;

      const components = dataGreece.results[0].address_components;
      components.forEach(component => {
        if (component.types.includes("locality")) {
          locality = component.long_name;
        }
        if (component.types.includes("country")) {
          country = component.short_name;
        }
      });

      return { locality, country, lat, lng };
    }

    // Αν δεν βρήκαμε στην Ελλάδα, ψάχνουμε παγκοσμίως
    const responseGlobal = await fetch(urlGlobal);
    const dataGlobal = await responseGlobal.json();

    if (dataGlobal.status === "OK") {
      let locality = "";
      let country = "";
      let lat = dataGlobal.results[0].geometry.location.lat;
      let lng = dataGlobal.results[0].geometry.location.lng;

      const components = dataGlobal.results[0].address_components;
      components.forEach(component => {
        if (component.types.includes("locality")) {
          locality = component.long_name;
        }
        if (component.types.includes("country")) {
          country = component.short_name;
        }
      });

      return { locality, country, lat, lng };
    }

    // Αν δεν βρούμε τίποτα σε καμία περίπτωση
    console.error("Δεν βρέθηκαν πληροφορίες για το Τ.Κ.");
    return null;
  } catch (error) {
    console.error("Σφάλμα στο fetch:", error);
    return null;
  }
}


// ➔ Google Maps
let map;
let senderMarker;
let recipientMarker;
let routeLine;

function initMap() {
  map = new google.maps.Map(document.getElementById('map'), {
    center: { lat: 38.2466, lng: 21.7346 }, // Κεντρική Ελλάδα
    zoom: 6
  });
}

async function updateMap(senderZip, recipientZip) {
  if (!map) {
    initMap();
  }

  const senderData = await getCityFromZip(senderZip);
  const recipientData = await getCityFromZip(recipientZip);

  if (!senderData || !recipientData) {
    return;
  }

  const senderLatLng = { lat: senderData.lat, lng: senderData.lng };
  const recipientLatLng = { lat: recipientData.lat, lng: recipientData.lng };

  if (senderMarker) senderMarker.setMap(null);
  if (recipientMarker) recipientMarker.setMap(null);
  if (routeLine) routeLine.setMap(null);

  senderMarker = new google.maps.Marker({
    position: senderLatLng,
    map: map,
    label: 'Α'
  });

  recipientMarker = new google.maps.Marker({
    position: recipientLatLng,
    map: map,
    label: 'Π'
  });

  routeLine = new google.maps.Polyline({
    path: [senderLatLng, recipientLatLng],
    geodesic: true,
    strokeColor: "#007bff",
    strokeOpacity: 1.0,
    strokeWeight: 2,
    map: map
  });

  const bounds = new google.maps.LatLngBounds();
  bounds.extend(senderLatLng);
  bounds.extend(recipientLatLng);
  map.fitBounds(bounds);
}

function clearMap() {
  if (senderMarker) senderMarker.setMap(null);
  if (recipientMarker) recipientMarker.setMap(null);
  if (routeLine) routeLine.setMap(null);
}
