 <?php
//  error_reporting(E_ALL);
//  ini_set('display_errors', 1);

 require('../classes/classes.php');

 $errors = [];
 $success_message = '';

 if ($_SERVER['REQUEST_METHOD'] === 'POST') {
     $userData = [
         'name' => $_POST['name'] ?? '',
         'email' => $_POST['email'] ?? '',
         'password' => $_POST['password'] ?? '',
         'passwordRepeat' => $_POST['passwordRepeat'] ?? '',
         'role' => $_POST['role'] ?? ''
     ];

     $result = Visitor::registerUser($userData);
      header('Location : login.php');
     if ($result['success']) {
         $success_message = $result['message'];
     } else {
         $errors = $result['errors'];
     }
 }
 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Sign Up - Youdemy</title>
     <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
     <style>
         body {
             font-family: 'Roboto', sans-serif;
             background-color: #f4f4f4;
             margin: 0;
             padding: 0;
             display: flex;
             justify-content: center;
             align-items: center;
             min-height: 100vh;
         }
         .signup-container {
             background-color: #ffffff;
             padding: 2rem;
             border-radius: 8px;
             box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
             width: 100%;
             max-width: 400px;
         }
         h2 {
             color: #333;
             text-align: center;
             margin-bottom: 1.5rem;
         }
         .form-group {
             margin-bottom: 1rem;
         }
         label {
             display: block;
             margin-bottom: 0.5rem;
             color: #555;
         }
         input, select {
             width: 100%;
             padding: 0.5rem;
             border: 1px solid #ddd;
             border-radius: 4px;
             font-size: 1rem;
         }
         button {
             width: 100%;
             padding: 0.75rem;
             background-color: #007bff;
             color: #fff;
             border: none;
             border-radius: 4px;
             font-size: 1rem;
             cursor: pointer;
             transition: background-color 0.3s;
         }
         button:hover {
             background-color: #0056b3;
         }
         .error-messages {
             background-color: #ffebee;
             border: 1px solid #ffcdd2;
             border-radius: 4px;
             padding: 0.75rem;
             margin-bottom: 1rem;
             color: #b71c1c;
         }
         .success-message {
             background-color: #e8f5e9;
             border: 1px solid #c8e6c9;
             border-radius: 4px;
             padding: 0.75rem;
             margin-bottom: 1rem;
             color: #1b5e20;
         }
         p {
             text-align: center;
             margin-top: 1rem;
         }
         a {
             color: #007bff;
             text-decoration: none;
         }
         a:hover {
             text-decoration: underline;
         }
     </style>
 </head>
 <body>
     <div class="signup-container">
         <h2>Create Account</h2>

         <?php if (!empty($errors)): ?>
             <div class="error-messages">
                 <?php foreach ($errors as $error): ?>
                     <p><?php echo $error; ?></p>
                 <?php endforeach; ?>
             </div>
         <?php endif; ?>

         <?php if (!empty($success_message)): ?>
             <div class="success-message">
                 <p><?php $success_message; ?></p>
             </div>
         <?php endif; ?>

         <form method="POST" action="signup.php">
             <div class="form-group">
                 <label for="name">Full Name:</label>
                 <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
             </div>

             <div class="form-group">
                 <label for="email">Email:</label>
                 <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
             </div>

             <div class="form-group">
                 <label for="password">Password:</label>
                 <input type="password" id="password" name="password" required>
             </div>

             <div class="form-group">
                 <label for="passwordRepeat">Confirm Password:</label>
                 <input type="password" id="passwordRepeat" name="passwordRepeat" required>
             </div>

             <div class="form-group">
                 <label for="role">Role:</label>
                 <select id="role" name="role" required>
                     <option value="">Select Role</option>
                     <option value="Student" >Student</option>
                     <option value="Teacher" >Teacher</option>
                 </select>
             </div>

             <button type="submit">Sign Up</button>
         </form>

         <p>Already have an account? <a href="login.php">Login here</a></p>
     </div>
 </body>
 </html>