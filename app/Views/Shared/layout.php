<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'PoC PHP MVC' ?></title>
    <link rel="stylesheet" href="/css/styles.css">
</head>

<body>
    <header>
        <h1>Welcome to PoC PHP MVC</h1>
        <nav>
            <a href="/">Home</a> |
            <a href="/about">About</a> |
            <a href="/contact">Contact</a>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer>
        <p>&copy; 2024 - PoC PHP MVC</p>
    </footer>
</body>

</html>