<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found!</title>
</head>
<body style="background: #050307">
    <img src="images/404.jpg" width="100%" alt="Your are lost" srcset="">
 <?php if (isset($errorMessage)): ?>
            <pre><?php echo htmlspecialchars($errorMessage); ?></pre>
        <?php endif; ?>
</body>
</html>
