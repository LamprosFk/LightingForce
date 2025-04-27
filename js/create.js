document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('create-form');
    const result = document.getElementById('submission-result');
  
    form.addEventListener('submit', function (e) {
      e.preventDefault();
  
      result.innerHTML = "✅ Η αποστολή σας καταχωρήθηκε επιτυχώς!";
      form.reset();
    });
  });
  