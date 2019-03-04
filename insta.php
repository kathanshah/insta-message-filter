<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');
ini_set('max_execution_time', -1);
ini_set('error_log', "php-error.log");

require_once 'vendor/autoload.php';
require_once 'param.php';
require_once 'db.php';
require_once 'common.php';

require_once 'followers.php';
require_once 'inbox.php';
//require_once 'pending_inbox.php';

use \PDO as PDO;

//\InstagramAPI\Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
$ig = new \InstagramAPI\Instagram(INSTA_DEBUG, INSTA_TRUNCATED_DEBUG, [
    'storage'    => 'mysql',
    'dbhost'     => DB_HOST,
    'dbname'     => DB_NAME,
    'dbusername' => DB_USER,
    'dbpassword' => DB_PASS,
]);

$dbConnectionObj = Connection::getInstance();

try {

    define("INSTA_EMAIL", $argv[1]);
    define("INSTA_PASS", $argv[2]);

    $sql = "TRUNCATE TABLE inbox";
    $stmt = $dbConnectionObj->prepare($sql);
    $stmt->execute();

    $sql = "TRUNCATE TABLE followers";
    $stmt = $dbConnectionObj->prepare($sql);
    $stmt->execute();
    
    $loginResponse = $ig->login(INSTA_EMAIL, INSTA_PASS);

    //pre($loginResponse,1);

    if ($loginResponse !== null && $loginResponse->isTwoFactorRequired()) {
        $twoFactorIdentifier = $loginResponse->getTwoFactorInfo()->getTwoFactorIdentifier();
        // The "STDIN" lets you paste the code via terminal for testing.
        // You should replace this line with the logic you want.
        // The verification code will be sent by Instagram via SMS.
        $verificationCode = trim(fgets(STDIN));
        $ig->finishTwoFactorLogin(INSTA_EMAIL, INSTA_PASS, $twoFactorIdentifier, $verificationCode);
    }

    printDebugMessage('--- Login Successfull ---');

    //pre($ig->account->getCurrentUser()->asStdClass()->user->pk);
    //prea($ig->account);

    //$userId = $ig->people->getUserIdForName(INSTA_USERNAME);
    $userId = $ig->account->getCurrentUser()->asStdClass()->user->pk;
    printDebugMessage('Your userId is : '.$userId);

    /*$response = $ig->people->getInfoById($userId);
    printDebugMessage("--- User Info  Start ---");
    pre($response,1);
    echo "Your username is : ".$response->getUser()->getUsername(); 
    printDebugMessage("--- User Info  End ---");*/

    \Inbox::scrapInbox($ig);

    /*$pendingInbox = $ig->direct->getPendingInbox();
    printDebugMessage("--- User Pending Inbox  Start ---");
    pre($pendingInbox);
    printDebugMessage("--- User Pending Inbox  End ---");*/

    \Followers::scrapFollowers($ig);

    printDebugMessage("----- THE END ----");

} catch (\Exception $e) {
    printDebugMessage('Something went wrong: '.$e->getMessage());
    exit(0);
}

