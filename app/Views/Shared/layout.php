<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $title ?? 'PoC PHP MVC' ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom Styles (if needed) -->
    <?= $this->renderBundles('styles'); ?>
    <?= $this->renderBundles('fonts'); ?>

</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">PoC PHP MVC</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about/">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact/">Contact</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLinkApi" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            API
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkApi">
                            <li><a class="dropdown-item" href="/docs/">Docs</a></li>
                            <li><a class="dropdown-item" href="/sandbox/">Sandbox</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLinkFeatures" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Features Demos
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLinkFeatures">
                            <li><a class="dropdown-item" href="/sections/">Sections</a></li>
                            <li><a class="dropdown-item" href="/users/">User management</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://github.com/guilhermestracini/poc-php-mvc" target="_blank">GitHub</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="container my-5">
        <?= $content ?? '<p>Content goes here.</p>' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-light text-center py-4">
        <div class="container">
            <p>&copy; <?= date('Y') ?> PoC PHP MVC. All Rights Reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- FontAwesome JS (for dynamic icons, if needed) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>

    <!-- Custom Scripts (if needed) -->
    <?= $this->renderBundles('scripts'); ?>
    <?= $this->renderSection('scripts') ?>

</body>

</html>