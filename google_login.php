<?php
require_once __DIR__ . '/_bootstrap.php';
require_once __DIR__ . '/../config/google.php';

function base64url_decode_custom(string $input): string {
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $input .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($input, '-_', '+/')) ?: '';
}

function base64url_decode_binary(string $data): string {
    $remainder = strlen($data) % 4;
    if ($remainder) {
        $data .= str_repeat('=', 4 - $remainder);
    }
    return base64_decode(strtr($data, '-_', '+/')) ?: '';
}

function encode_der_length(int $length): string {
    if ($length <= 0x7F) {
        return chr($length);
    }
    $temp = ltrim(pack('N', $length), "\x00");
    return chr(0x80 | strlen($temp)) . $temp;
}

function encode_der_integer(string $value): string {
    if ($value === '') {
        throw new Exception('Invalid RSA integer value.');
    }
    if (ord($value[0]) > 0x7F) {
        $value = "\x00" . $value;
    }
    return "\x02" . encode_der_length(strlen($value)) . $value;
}

function jwk_to_pem(string $n, string $e): string {
    $modulus  = base64url_decode_binary($n);
    $exponent = base64url_decode_binary($e);

    if ($modulus === '' || $exponent === '') {
        throw new Exception('Unable to decode Google signing key.');
    }

    $components   = encode_der_integer($modulus) . encode_der_integer($exponent);
    $rsaPublicKey = "\x30" . encode_der_length(strlen($components)) . $components;
    $bitString    = "\x03" . encode_der_length(strlen($rsaPublicKey) + 1) . "\x00" . $rsaPublicKey;

    $algorithmIdentifier = hex2bin('300d06092a864886f70d0101010500');
    if ($algorithmIdentifier === false) {
        throw new Exception('Unable to build RSA algorithm identifier.');
    }

    $sequence = "\x30" . encode_der_length(strlen($algorithmIdentifier . $bitString)) . $algorithmIdentifier . $bitString;

    return "-----BEGIN PUBLIC KEY-----\n"
        . chunk_split(base64_encode($sequence), 64, "\n")
        . "-----END PUBLIC KEY-----\n";
}

/**
 * Fetch a URL using cURL first (works on most shared hosts),
 * falling back to file_get_contents if cURL is unavailable.
 */
function fetch_url(string $url): string {
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);
        if ($response !== false && $response !== '') {
            return $response;
        }
        // Fall through to file_get_contents if curl failed
    }

    $response = @file_get_contents($url);
    if ($response === false || $response === '') {
        throw new Exception('Unable to reach Google certs endpoint. Check server network settings.');
    }
    return $response;
}

function verify_google_id_token(string $jwt, string $expectedAud): array {
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) {
        throw new Exception('Invalid token format.');
    }

    [$encodedHeader, $encodedPayload, $encodedSig] = $parts;

    $header    = json_decode(base64url_decode_custom($encodedHeader), true);
    $payload   = json_decode(base64url_decode_custom($encodedPayload), true);
    $signature = base64url_decode_custom($encodedSig);

    if (!is_array($header) || !is_array($payload)) {
        throw new Exception('Unable to decode token.');
    }

    if (($header['alg'] ?? '') !== 'RS256') {
        throw new Exception('Unexpected signing algorithm.');
    }

    $certsJson = fetch_url('https://www.googleapis.com/oauth2/v3/certs');
    $certs     = json_decode($certsJson, true);

    if (!is_array($certs)) {
        throw new Exception('Unable to decode Google cert response.');
    }

    $kid     = $header['kid'] ?? '';
    $keyData = null;

    foreach (($certs['keys'] ?? []) as $key) {
        if (($key['kid'] ?? '') === $kid) {
            $keyData = $key;
            break;
        }
    }

    if (!$keyData) {
        throw new Exception('Signing key not found.');
    }

    $n = $keyData['n'] ?? '';
    $e = $keyData['e'] ?? '';

    if ($n === '' || $e === '') {
        throw new Exception('Google signing key is incomplete.');
    }

    $pem       = jwk_to_pem($n, $e);
    $signedData = $encodedHeader . '.' . $encodedPayload;
    $publicKey  = openssl_pkey_get_public($pem);

    if ($publicKey === false) {
        throw new Exception('Unable to load Google public key.');
    }

    $verified = openssl_verify($signedData, $signature, $publicKey, OPENSSL_ALGO_SHA256);

    if ($verified !== 1) {
        throw new Exception('Invalid token signature.');
    }

    $iss           = $payload['iss'] ?? '';
    $aud           = $payload['aud'] ?? '';
    $exp           = (int)($payload['exp'] ?? 0);
    $emailVerified = $payload['email_verified'] ?? false;

    if (!in_array($iss, ['accounts.google.com', 'https://accounts.google.com'], true)) {
        throw new Exception('Invalid token issuer.');
    }

    if ($aud !== $expectedAud) {
        throw new Exception('Token audience mismatch. Expected: ' . $expectedAud);
    }

    if ($exp < time()) {
        throw new Exception('Token has expired.');
    }

    if (!$emailVerified) {
        throw new Exception('Google email is not verified.');
    }

    return $payload;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['success' => false, 'message' => 'Invalid request method.'], 405);
}

if (strpos($GOOGLE_CLIENT_ID, 'YOUR_GOOGLE_WEB_CLIENT_ID') !== false) {
    json_response([
        'success' => false,
        'message' => 'Google client ID is not configured. Update src/config/google.php first.'
    ], 500);
}

$data    = get_json_input();
$idToken = trim((string)($data['credential'] ?? ''));

if ($idToken === '') {
    json_response(['success' => false, 'message' => 'Google credential is required.'], 422);
}

try {
    $payload = verify_google_id_token($idToken, $GOOGLE_CLIENT_ID);
} catch (Exception $e) {
    json_response(['success' => false, 'message' => $e->getMessage()], 401);
}

$email    = trim((string)($payload['email'] ?? ''));
$fullName = trim((string)($payload['name'] ?? 'Google User'));
$googleSub = trim((string)($payload['sub'] ?? ''));
$avatar   = trim((string)($payload['picture'] ?? ''));

if ($email === '' || $googleSub === '') {
    json_response(['success' => false, 'message' => 'Google account details are incomplete.'], 422);
}

$stmt = $conn->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    $legacyRoleName = 'Student Member';
    $blankPassword  = '';

    $stmt = $conn->prepare('
        INSERT INTO users
        (full_name, role_name, student_id, email, password, course, year_level, contact_number, address, profile_image, google_sub)
        VALUES (?, ?, NULL, ?, ?, NULL, NULL, NULL, NULL, ?, ?)
    ');
    $stmt->bind_param('ssssss', $fullName, $legacyRoleName, $email, $blankPassword, $avatar, $googleSub);
    $stmt->execute();
    $userId = (int)$stmt->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("INSERT IGNORE INTO user_roles (user_id, role_key) VALUES (?, 'user')");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare('SELECT * FROM users WHERE user_id = ? LIMIT 1');
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    $userId = (int)$user['user_id'];

    $stmt = $conn->prepare('UPDATE users SET full_name = ?, profile_image = ?, google_sub = ? WHERE user_id = ?');
    $stmt->bind_param('sssi', $fullName, $avatar, $googleSub, $userId);
    $stmt->execute();
    $stmt->close();
}

$roles = fetch_user_roles($conn, $userId);
if (!$roles) {
    $roles = ['user'];
    $stmt  = $conn->prepare("INSERT IGNORE INTO user_roles (user_id, role_key) VALUES (?, 'user')");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $stmt->close();
}

$blocked = is_user_blocked($conn, $userId);

$_SESSION['pending_google_user'] = [
    'user_id'   => $userId,
    'full_name' => $fullName,
    'email'     => $email,
    'avatar'    => $avatar,
    'roles'     => $roles,
    'blocked'   => $blocked,
];

json_response([
    'success' => true,
    'message' => 'Google account verified.',
    'profile' => [
        'name'    => $fullName,
        'email'   => $email,
        'avatar'  => $avatar,
        'roles'   => $roles,
        'blocked' => $blocked,
    ]
]);
?>
