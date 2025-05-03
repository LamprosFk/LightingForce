document.getElementById("contact-form").addEventListener("submit", function(e) {
    e.preventDefault(); // Αποφυγή ανανέωσης της σελίδας
    
    const name = document.getElementById("name").value;
    const email = document.getElementById("email").value;
    const message = document.getElementById("message").value;
  
    // Ελέγχουμε αν όλα τα πεδία είναι συμπληρωμένα
    if (name && email && message) {
      // Εμφάνιση του Modal επιτυχίας
      document.getElementById("submission-modal").style.display = "block";
      document.querySelector(".modal-content h2").innerText = "Ευχαριστούμε για την επικοινωνία!";
      document.querySelector(".modal-content p").innerText = "Το μήνυμά σας έχει υποβληθεί επιτυχώς. Θα επικοινωνήσουμε μαζί σας το συντομότερο δυνατό.";
  
      // Κλείσιμο του Modal όταν πατήσουμε το "x"
      document.querySelector(".close-btn").addEventListener("click", function() {
        document.getElementById("submission-modal").style.display = "none";
      });
  
      // Προαιρετικά, μπορείς να κάνεις κάτι με τα δεδομένα (π.χ. να τα στείλεις σε server)
      console.log("Μήνυμα:", message);
      console.log("Αποστολέας:", name);
      console.log("Email:", email);
    } else {
      // Αν κάποιο πεδίο δεν έχει συμπληρωθεί, εμφανίζουμε το modal με το μήνυμα αποτυχίας
      document.getElementById("submission-modal").style.display = "block";
      document.querySelector(".modal-content h2").innerText = "Σφάλμα";
      document.querySelector(".modal-content p").innerText = "Παρακαλώ συμπληρώστε όλα τα πεδία!";
    }
  });
  