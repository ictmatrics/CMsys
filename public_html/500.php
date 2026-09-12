<?php declare(strict_types=1);
// The $errorMessage variable is passed from the bootstrap.php file.
$errorTitle = "500 - Internal Server Error";
$userMessage = "We're sorry, but something went wrong on our end. Please try again later.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($errorTitle); ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 40px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 3rem;
            color: #dc3545;
            margin-bottom: 10px;
        }
        p {
            font-size: 1.2rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        pre {
            background-color: #e9ecef;
            border: 1px solid #ced4da;
            padding: 15px;
            border-radius: 4px;
            text-align: left;
            white-space: pre-wrap;
            word-wrap: break-word;
            font-size: 0.9rem;
            color: #495057;
        }
        img .be{
            position: fixed;
            bottom: 0;
            right:0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($errorTitle); ?></h1>
        <p><?php echo htmlspecialchars($userMessage); ?></p>
        <?php if (isset($errorMessage)): ?>
            <pre><?php echo htmlspecialchars($errorMessage); ?></pre>
        <?php endif; ?>
    </div>
</body>
</html>
