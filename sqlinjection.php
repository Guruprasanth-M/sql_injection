<?php
$servername = "nanba ohh nanba";
$username = "therla bro";
$password = "SREGSRET$#WT%#$ERSB ";
$dbname = "dbname";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Global flag to simulate side-effect from unsafe()
$GLOBALS['unsafe_triggered'] = false;

function unsafe($conn, $product_name) {
    // $GLOBALS['unsafe_triggered'] = true;

    $sql = "SELECT * FROM products WHERE product = '$product_name'";
    $result = $conn->query($sql);
    echo "<br>UNSAFE<br>";

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "product : " . $row["product"] . " - price : " . $row["price"] . "<br>";
        }
    } else {
        echo "0 results<br>";
    }
}

function safe($conn, $product_name) {
    echo "SAFE<br>";
    $stmt = $conn->prepare("SELECT * FROM products WHERE product = ?");
    if (!$stmt) {
        echo "Prepare failed: " . $conn->error;
        return;
    }

    $stmt->bind_param("s", $product_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "product : " . $row["product"] . " - price : " . $row["price"] . "<br>";
        }
    } else {
        echo "0 results<br>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Advanced SQL Injection Demo</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            height: 100vh;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding-top: 50px;
        }

        .container {
            text-align: center;
        }

        .output-box {
            margin-top: 20px;
            padding: 20px;
            background-color: #eaeaea;
            border: 1px solid #ccc;
            border-radius: 8px;
            max-width: 600px;
            text-align: left;
            white-space: pre-wrap;
        }

        form {
            margin-bottom: 20px;
        }

        input[type="text"] {
            padding: 8px;
            width: 200px;
        }

        input[type="submit"] {
            padding: 8px 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Search Product</h1>
        <form method="post" action="">
            <label for="product_name">Product Name:</label>
            <input 
                type="text" 
                id="product_name" 
                name="product_name" 
                required>
            <input type="submit" value="Search">
        </form>

        <div class="output-box">
            <?php
                if ($_SERVER["REQUEST_METHOD"] == "POST") {
                    // ✅ Only sanitize input here
                    $product_name = htmlspecialchars($_POST['product_name'], ENT_QUOTES, 'UTF-8');
                    // DONT USE THIS IT IS VULNERABLE TO SQL INJECTION
                    unsafe($conn, $product_name);
                    // ✅ Use it safely in your prepared SQL
                    safe($conn, $product_name);
                }

                $conn->close();
            ?>
        </div>
    </div>
</body>
</html>
