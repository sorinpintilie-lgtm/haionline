<?php
// SendGrid Configuration
$apiKey = getenv('SENDGRID_API_KEY');

// Get form data
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$projectType = $_POST['project_type'] ?? '';
$budget = $_POST['budget'] ?? '';

// Validate required fields
if (empty($name) || empty($email) || empty($phone)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Te rugăm să completezi toate câmpurile obligatorii.']);
    exit;
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Adresa de email nu este validă.']);
    exit;
}

// Prepare email content
$to = 'bogdan.epure@sky.ro,sorin.pintilie@sky.ro'; // Change to your email
$subject = 'New Project Inquiry - Sky.ro';
$message = "
<html>
<head>
    <title>Nouă Cerere de Proiect - Sky.ro</title>
</head>
<body>
    <h2>Nouă cerere de proiect de pe Sky.ro</h2>
    <table style='border-collapse: collapse; width: 100%; max-width: 600px;'>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Nume</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($name) . "</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Email</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($email) . "</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Telefon</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($phone) . "</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Tip Proiect</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($projectType) . "</td>
        </tr>
        <tr>
            <td style='padding: 10px; border: 1px solid #ddd; font-weight: bold;'>Buget</td>
            <td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($budget) . "</td>
        </tr>
    </table>
</body>
</html>
";

$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-Type:text/html;charset=UTF-8" . "\r\n";
$headers .= "From: Sky.ro <noreply@sky.ro>" . "\r\n";

// Send email using SendGrid API
$url = 'https://api.sendgrid.com/v3/mail/send';

$data = [
    'personalizations' => [
        [
            'to' => [
                ['email' => $to]
            ]
        ]
    ],
    'from' => [
        'email' => 'noreply@sky.ro'
    ],
    'subject' => $subject,
    'content' => [
        [
            'type' => 'text/html',
            'value' => $message
        ]
    ]
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $apiKey,
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    echo json_encode(['success' => true, 'message' => 'Mesajul a fost trimis cu succes!']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'A apărut o eroare. Te rugăm să încerci din nou.']);
}
?>
