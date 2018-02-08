<?php
require_once 'vendor/autoload.php';
require_once 'contacts.php';

session_start();

$client = new Google_Client();
$contacts = new contacts();

$client->setDeveloperKey(CONTACTS::DEVELOPER_KEY);
$client->setClientId(CONTACTS::CLIENT_KEY);
$client->setClientSecret(CONTACTS::CLIENT_SECRET);
$client->setRedirectUri(CONTACTS::REDIRCT_URL);
$client->addScope('profile');
$client->addScope('https://www.googleapis.com/auth/contacts.readonly');

if (isset($_GET['oauth'])) {
    // Start auth flow by redirecting to Google's auth server
    header('Location: ' . filter_var($client->createAuthUrl(), FILTER_SANITIZE_URL));
} elseif (isset($_GET['code'])) {
    // Receive auth code from Google, exchange it for an access token, and redirect to your base URL
    $client->authenticate($_GET['code']);
    // Set session access token
    $_SESSION['access_token'] = $client->getAccessToken();
    // Re-direct back
    header('Location: ' . filter_var(CONTACTS::REDIRCT_URL, FILTER_SANITIZE_URL));
} elseif (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    // You have an access token; use it to call the People API
    $client->setAccessToken($_SESSION['access_token']);
    // Display contacts
    echo $contacts->displayContacts(new Google_Service_PeopleService($client));
} else {
    // Re-direct to auth url
    header('Location: ' . filter_var(CONTACTS::REDIRCT_URL . '?auth', FILTER_SANITIZE_URL));
}
