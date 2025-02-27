<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bootstrap Basics Example</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <!-- Header -->
        <h1 class="text-primary text-center">Bootstrap Basics Lesson</h1>

        <!-- Text Inputs -->
        <h3 class="mt-4">Text Inputs</h3>
        <form>
            <div class="mb-3">
                <label for="exampleInputText" class="form-label">Name</label>
                <input type="text" class="form-control" id="exampleInputText" placeholder="Enter your name">
            </div>
            <div class="mb-3">
                <label for="exampleInputEmail" class="form-label">Email</label>
                <input type="email" class="form-control" id="exampleInputEmail" placeholder="Enter your email">
            </div>
            <div class="mb-3">
                <label for="exampleInputPassword" class="form-label">Password</label>
                <input type="password" class="form-control" id="exampleInputPassword" placeholder="Enter your password">
            </div>
        </form>

        <!-- Colored Text -->
        <h3 class="mt-4">Text Colors</h3>
        <p class="text-success">This is a success message!</p>
        <p class="text-danger">This is an error message!</p>
        <p class="text-warning">This is a warning message!</p>
        <p class="text-info">This is an informational message!</p>
        <p class="text-primary">This is an informational message!</p>

        <!-- Background Colors -->
        <h3 class="mt-4">Background Colors</h3>
        <div class="p-3 mb-2 bg-primary text-white">Primary Background</div>
        <div class="p-3 mb-2 bg-secondary text-white">Secondary Background</div>
        <div class="p-3 mb-2 bg-success text-white">Success Background</div>
        <div class="p-3 mb-2 bg-danger text-white">Danger Background</div>
        <div class="p-3 mb-2 bg-warning text-dark">Warning Background</div>
        <div class="p-3 mb-2 bg-info text-dark">Info Background</div>

        <!-- Basic List -->
        <h3 class="mt-4">Basic List</h3>
        <ul class="list-group">
            <li class="list-group-item">Item 1</li>
            <li class="list-group-item">Item 2</li>
            <li class="list-group-item">Item 3</li>
        </ul>

        <!-- Buttons -->
        <h3 class="mt-4">Buttons</h3>
        <button class="btn btn-primary">Primary Button</button>
        <button class="btn btn-secondary">Secondary Button</button>
        <button class="btn btn-success">Success Button</button>
        <button class="btn btn-danger">Danger Button</button>
        <button class="btn btn-warning">Warning Button</button>
        <button class="btn btn-info">Info Button</button>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
