
<?php

session_start();

require_once "../backend/config.php";

$error = "";

if (isset($_SESSION["admin_id"])) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {

        $error = "Please enter username and password.";

    } else {

        $sql = "SELECT * FROM admins WHERE username = :username LIMIT 1";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            ":username" => $username
        ]);

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin["password"])) {

            session_regenerate_id(true);

            $_SESSION["admin_id"] = $admin["id"];
            $_SESSION["admin_username"] = $admin["username"];

            header("Location: dashboard.php");
            exit;

        } else {

            $error = "Invalid username or password.";

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin Login</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f6ff;
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
        }

        .login-box {
            background: #ffffff;

            padding: 42px;

            border-radius: 14px;

            border: 1px solid #e6eaf2;

            box-shadow: 0 12px 35px rgba(55, 113, 200, 0.08);
        }

        .eyebrow {
            color: #3771c8;

            font-size: 13px;
            font-weight: 700;

            letter-spacing: 1.5px;

            margin-bottom: 12px;
        }

        h1 {
            color: #3771c8;

            font-size: 32px;
            font-weight: 700;

            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;

            color: #333333;

            font-size: 14px;
            font-weight: 600;

            margin-bottom: 8px;
        }

        input {
            width: 100%;

            padding: 13px 14px;

            border: 1px solid #dfe3eb;

            border-radius: 7px;

            background: #ffffff;

            color: #333333;

            font-size: 15px;

            transition: border-color 0.2s ease;
        }

        input:focus {
            outline: none;

            border-color: #3771c8;
        }

        .login-btn {
            width: 100%;

            border: none;

            border-radius: 7px;

            padding: 13px;

            background: #3771c8;

            color: #ffffff;

            font-size: 15px;
            font-weight: 600;

            cursor: pointer;

            transition:
                background 0.2s ease,
                transform 0.2s ease;
        }

        .login-btn:hover {
            background: #2f63ae;

            transform: translateY(-1px);
        }

        .error {
            background: #fff1f1;

            color: #c62828;

            border: 1px solid #f3d0d0;

            border-radius: 7px;

            padding: 11px 13px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        @media (max-width: 480px) {

            .login-box {
                padding: 30px 24px;
            }

            h1 {
                font-size: 28px;
            }

        }

    </style>

</head>

<body>

    <div class="login-container">

        <div class="login-box">

            <p class="eyebrow">
                ADMIN LOGIN
            </p>

            <h1>
                Welcome back.
            </h1>

            <?php if ($error !== ""): ?>

                <div class="error">
                    <?php echo htmlspecialchars($error); ?>
                </div>

            <?php endif; ?>

            <form method="POST">

                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Enter your username"
                        required
                    >

                </div>

                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>

                <button
                    type="submit"
                    class="login-btn"
                >
                    Sign In
                </button>

            </form>

        </div>

    </div>

</body>

</html>
