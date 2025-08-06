<?php
// Database Connection
$host = 'localhost';
$dbname = 'twinkleadmin';
$username = 'root'; // Update as needed
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if the table `users` exists; if not, create it
    $createTableQuery = "
        CREATE TABLE IF NOT EXISTS adminusers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
    $conn->exec($createTableQuery);

    $defaultUsername = 'admin';
    $defaultPassword = password_hash('admin0118', PASSWORD_BCRYPT);

    $checkUserQuery = "SELECT * FROM adminusers WHERE username = :username";
    $stmt = $conn->prepare($checkUserQuery);
    $stmt->execute([':username' => $defaultUsername]);

    if ($stmt->rowCount() === 0) {
        $insertUserQuery = "INSERT INTO adminusers (username, password) VALUES (:username, :password)";
        $stmt = $conn->prepare($insertUserQuery);
        $stmt->execute([
            ':username' => $defaultUsername,
            ':password' => $defaultPassword
        ]);
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $inputUsername = $_POST['username'];
        $inputPassword = $_POST['password'];

        // Query the database for the user
        $query = "SELECT * FROM adminusers WHERE username = :username";
        $stmt = $conn->prepare($query);
        $stmt->execute([':username' => $inputUsername]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($inputPassword, $user['password'])) {
            // Start a session
            session_start();
            $_SESSION['username'] = $user['username'];

            // Redirect to the admin dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid username or password!";
        }
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Axiom Admin - Login</title>
  <style>
    /* Same CSS as before */
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(90deg, rgba(26,19,140,1) 0%, rgba(9,9,121,1) 29%, rgba(0,212,255,1) 100%);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      color: #fff;
    }

    h1 {
      position: absolute;
      top: 20px;
      font-size: 48px;
      color: orange;
      text-align: center;
      width: 100%;
    }

    .login-container {
      background: linear-gradient(145deg, #3a3a3a, #292929);
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
      padding: 30px 25px;
      width: 90%;
      max-width: 400px;
      text-align: center;
    }

    .login-container h2 {
      font-size: 38px;
      margin-bottom: 25px;
      color: radial-gradient(circle, rgba(238,202,174,1) 0%, rgba(218,115,0,1) 100%);
    }

    .login-form {
      display: flex;
      flex-direction: column;
    }

    .login-form label {
      margin-bottom: 8px;
      font-weight: bold;
      color: #ccc;
      text-align: left;
    }

    .login-form input {
      padding: 12px;
      border: none;
      border-radius: 8px;
      margin-bottom: 20px;
      background-color: #444;
      color: #fff;
      font-size: 16px;
      transition: all 0.3s ease;
    }

    .login-form input:focus {
      outline: none;
      background-color: #555;
      box-shadow: 0 0 5px #1a73e8;
    }

    .login-form button {
      padding: 12px;
      border: none;
      border-radius: 8px;
      background-color: #1a73e8;
      color: #fff;
      font-size: 18px;
      font-weight: bold;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .login-form button:hover {
      background-color: #4285f4;
    }

    .login-form a {
      margin-top: 15px;
      color: #bbb;
      text-decoration: none;
      font-size: 14px;
    }

    .login-form a:hover {
      color: #fff;
    }

    .error {
      color: red;
      margin-bottom: 15px;
      font-size: 14px;
    }

    @media (max-width: 768px) {
      .login-container {
        padding: 20px;
      }

      .login-form input,
      .login-form button {
        font-size: 14px;
      }

      .login-form a {
        font-size: 12px;
      }
    }
  </style>
</head>
<body>
  <h1>Twinkle Admin</h1>
  <div class="login-container">
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form class="login-form" method="POST">
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Enter your username" required>
      
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required>
      
      <button type="submit">Login</button>
    </form>
  </div> 
</body>
</html>
