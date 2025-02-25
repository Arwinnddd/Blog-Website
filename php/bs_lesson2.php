<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Basics with Sizes and Footer</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <header class="bg-primary text-white py-3">
        <div class="container">
            <h1 class="text-center">Bootstrap Basics Lesson</h1>
            <p class="text-center">Learn text sizes, headers, and footer examples</p>
        </div>
    </header>

    <!-- Main Content -->
    <div class="container mt-4">
        <!-- Text Sizes -->
        <h2 class="mt-4">Text Sizes</h2>
        <p class="fs-1">This is text with <strong>font size 1</strong>.</p>
        <p class="fs-2">This is text with <strong>font size 2</strong>.</p>
        <p class="fs-3">This is text with <strong>font size 3</strong>.</p>
        <p class="fs-4">This is text with <strong>font size 4</strong>.</p>
        <p class="fs-5">This is text with <strong>font size 5</strong>.</p>
        <p class="fs-6">This is text with <strong>font size 6</strong>.</p>

        <!-- Headers -->
        <h2 class="mt-4">Headers</h2>
        <h1>Header 1</h1>
        <h2>Header 2</h2>
        <h3>Header 3</h3>
        <h4>Header 4</h4>
        <h5>Header 5</h5>
        <h6>Header 6</h6>

        <!-- Background Colors with Text -->
        <h2 class="mt-4">Background Colors with Text</h2>
        <div class="p-3 mb-2 bg-light text-dark">Light Background with Dark Text</div>
        <div class="p-3 mb-2 bg-dark text-white">Dark Background with White Text</div>
        <div class="p-3 mb-2 bg-primary text-white">Primary Background</div>
        <div class="p-3 mb-2 bg-success text-white">Success Background</div>
        <div class="p-3 mb-2 bg-danger text-white">Danger Background</div>
        <div class="p-3 mb-2 bg-warning text-dark">Warning Background</div>
        <div class="p-3 mb-2 bg-info text-dark">Info Background</div>

        <!-- Footer -->
        <footer class="bg-secondary text-white mt-4 py-3">
            <div class="container text-center">
                <p>&copy; 2024 MySite. All Rights Reserved.</p>
                <p>
                    Follow us on 
                    <a href="#" class="text-white text-decoration-none">Facebook</a>, 
                    <a href="#" class="text-white text-decoration-none">Twitter</a>, and 
                    <a href="#" class="text-white text-decoration-none">Instagram</a>.
                </p>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
