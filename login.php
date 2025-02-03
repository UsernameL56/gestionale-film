<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login/Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background: linear-gradient(135deg, #000000, #434343);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background: #ffffff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            position: relative;
        }

        .alert {
            margin-bottom: 1rem;
            text-align: center;
            transition: opacity 1s ease-in-out;
        }

        .btn-primary {
            background: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background: #0056b3;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        a {
            text-decoration: none;
            color: #007bff;
        }

        a:hover {
            text-decoration: underline;
            color: #0056b3;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const alertBox = document.querySelector(".alert");
            if (alertBox) {
                setTimeout(() => {
                    alertBox.style.opacity = "0";
                    setTimeout(() => alertBox.remove(), 1000);
                }, 3000);
            }
        });
    </script>
</head>

<body>
    <div class="form-container">
        <?php
        if (isset($_GET['message']) && $_GET['message'] === 'logged_out') {
            echo "<div class='alert alert-success'>You have successfully logged out.</div>";
        } else if (isset($_GET['message']) && $_GET['message'] === 'registered') {
            echo "<div class='alert alert-success'>You have successfully registered.</div>";
        } else if (isset($_GET['message']) && $_GET['message'] === 'login_error') {
            echo "<div class='alert alert-danger'>Error while logging in.</div>";
        }
        ?>
        <h2 class="text-center mb-4">Login / Register</h2>
        <form action="controllo.php" method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email"
                    required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password"
                    required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-warning" name="login">Login</button>
            </div>
            <?php if (isset($login_error)) echo "<p class='text-danger text-center mt-2'>$login_error</p>"; ?>
        </form>
        <p class="text-center mt-3">Not registered yet? <a href="register.php">Sign up here</a></p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>
