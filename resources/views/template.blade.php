<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechFinder</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <style>
        body { display: flex; flex-direction: column; min-height: 100vh; padding: 0 3%; }
        .main-content { flex: 1; }
        footer { background: #f8f9fa; padding: 10px; margin-top: 20px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">TechFinder</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="/web/competences">Compétences</a></li>
                <li class="nav-item"><a class="nav-link" href="/web/users">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Interventions</a></li>
                <li class="nav-item"><a class="nav-link" href="/web/user-competences">User competence</a></li>

            </ul>
        </div>
    </div>
</nav>

<div class="main-content">
    @yield('main')
</div>

<footer>
    <div class="d-flex justify-content-between">
        <span>3IL3</span>
        <span>© 2026 TechFinder. Tous droits reservés.</span>
    </div>
</footer>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="universalToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <span id="toastIcon" class="me-2"></span>
                <span id="toastMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var toastEl = document.getElementById('universalToast');
        var toastBody = document.getElementById('toastMessage');
        var toastIcon = document.getElementById('toastIcon');

        // Configuration : 5 secondes (5000ms)
        var bsToast = new bootstrap.Toast(toastEl, { delay: 5000 });

        @if(session('success'))
            toastEl.classList.add('bg-success');
            toastBody.innerText = "{!! addslashes(session('success')) !!}";
            toastIcon.innerHTML = '<i class="bi bi-check-circle-fill"></i>';
            bsToast.show();
        @endif

        @if(session('error') || $errors->any())
            toastEl.classList.add('bg-danger');
            toastBody.innerText = "{!! session('error') ? addslashes(session('error')) : 'Erreur de saisie dans le formulaire' !!}";
            toastIcon.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>';
            bsToast.show();
        @endif
    });
</script>
</body>
</html>
