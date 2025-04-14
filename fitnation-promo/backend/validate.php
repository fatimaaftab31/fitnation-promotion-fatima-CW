<?php
// Database connection (update credentials as needed)
$host = 'fit-promo-db_friend-1';
$port = 5432;
$db   = 'postgres';
$user = 'friend_user';
$pass = 'friend_pass';
$dsn  = "pgsql:host=$host;port=$port;dbname=$db";


try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(" Database connection failed: " . $e->getMessage());
}

// Handle POST request
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $promo_code = $_POST['promo_code'] ?? null;
    $email = $_POST['email'] ?? null;

    if (!$promo_code || !$email) {
        die(" Error: Missing promo code or email.");
    }

    // Load promo codes from CSV file
    $csv_file = 'codes.csv';
    $promo_codes = [];

    if (($handle = fopen($csv_file, "r")) !== FALSE) {
        $header = fgetcsv($handle); // Read header row

        // Remove BOM (Byte Order Mark) if present
        $header[0] = preg_replace('/\x{FEFF}/u', '', $header[0]);

        while (($data = fgetcsv($handle)) !== FALSE) {
            $promo_codes[] = array_combine($header, $data);
        }
        fclose($handle);
    } else {
        die(" Error: Unable to open CSV file.");
    }

    // Check if promo code exists in CSV
    $found = false;
    $voucher_code = null;

    foreach ($promo_codes as $row) {
        if (trim($row['hexCode']) === trim($promo_code)) {
            $found = true;
            $voucher_code = trim($row['vCode']); // Get corresponding voucher code
            break;
        }
    }

    if (!$found) {
        die(" Error: Invalid promo code.");
    }

    // Check if the promo code has already been used in the database
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM promotion WHERE promo_code = ?");
    $stmt->execute([$promo_code]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        die(" Error: Promo code already used.");
    }

    // Insert into the database
    try {
        $stmt = $pdo->prepare("INSERT INTO promotion (promo_code, email, voucher_code) VALUES (?, ?, ?)");
        $stmt->execute([$promo_code, $email, $voucher_code]);

        echo "✅ Success! Your voucher code is: $voucher_code : Enjoy your 10% discount";
    } catch (PDOException $e) {
        die(" Database Error: " . $e->getMessage());
    }
} else {
    die(" Error: Invalid request method.");
}
?>
