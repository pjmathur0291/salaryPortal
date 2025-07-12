<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LSQ Lead Capture Form</title>
</head>
<body>
  <h2>Lead Capture Form</h2>
  <form action="submit.php" method="POST">
    <label>First Name:</label>
    <input type="text" name="first_name" required><br><br>

    <label>Email Address:</label>
    <input type="email" name="email" required><br><br>

    <label>Phone Number:</label>
    <input type="tel" name="phone" required><br><br>

    <input type="submit" value="Submit">
  </form>
</body>
</html>
