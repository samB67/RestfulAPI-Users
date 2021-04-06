<?php

require_once('Database.php');
require_once('../model/User.php');
require_once('../model/Response.php');

// Check database connection...
$db = new Database();
if (!$db->isConnected()){
    error_log("Connection error - ".$db->getError());
    $response = new Response();
    $response->setHttpStatusCode(500);
    $response->setSuccess(false);
    $response->addMessage("Database connection error");
    $response->send();
    exit();
}
// Get headers...
$headers = apache_request_headers();

// Check if no authorization token...
if(!isset($headers['Authorization']) || strlen($headers['Authorization']) < 1){
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    (!isset($headers['Authorization']) ? $response->addMessage("Access token is missing from header") : false);
    (isset($headers['Authorization']) && strlen($headers['Authorization']) < 1 ? $response->addMessage("Access token cannot be blank") : false);
    $response->send();
    exit;
}
// Check auth token exits in database...
$accessToken = $headers['Authorization'];
$db->query("SELECT id FROM access_tokens WHERE access_token = :token");
$db->bind(':token', $accessToken);
$db->single();

// if no auth token return error...
if ($db->rowCount() === 0){
    $response = new Response();
    $response->setHttpStatusCode(401);
    $response->setSuccess(false);
    $response->addMessage("Invalid access token");
    $response->send();
    exit;
}
// Check if user id is in header and darkMode toggle is not.
// if so all database calls with an id are in this statement...
if(array_key_exists("userid", $_GET) && !array_key_exists("darkModeToggle", $_GET)) {

    // Get user id from header...
    $userid = $_GET['userid'];

    // validate id is a number or return error...
    if ($userid == '' || !is_numeric($userid)) {
        $response = new Response();
        $response->setHttpStatusCode(400);
        $response->setSuccess(false);
        $response->addMessage("User ID cannot be blank or must be numeric");
        $response->send();
        exit;
    }
    // Check request method...
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {

        // Get user with id...
        $db->query("SELECT id, first_name, last_name, username, DATE_FORMAT(date_created, '%d/%m/%Y %H:%i') as date_created, dark_mode FROM users WHERE id = :userid;");
        $db->bind(":userid", $userid);
        $db->single();

        // Confirm user exists in database...
        if ($db->rowCount() == 0) {
            $response = new Response();
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage("User Not Found");
            $response->send();
            exit;
        }

        // Create user in User module...
        $user = new User($db->single()->id, $db->single()->first_name, $db->single()->last_name, $db->single()->username, (int)$db->single()->dark_mode);
        // Build user array...
        $userArray[] = $user->returnUserAsArray();
        // Build return data...
        $returnData = array();
        $returnData['rows_returned'] = $db->rowCount();
        $returnData['users'] = $userArray;

        // Return success and return data...
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
        $response->setData($returnData);
        $response->send();
        exit;

    // Check request method...
    } else if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

        // Delete user with id query...
        $db->query("DELETE FROM users WHERE id = :userid");
        $db->bind(":userid", $userid);
        $db->execute();

        // if not deleted return error...
        if ($db->rowCount() === 0) {
            $response = new Response();
            $response->setHttpStatusCode(404);
            $response->setSuccess(false);
            $response->addMessage("User Not Found");
            $response->send();
            exit;
        }

        // if deleted return success response message...
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->addMessage("User deleted");
        $response->send();
        exit;

    // Check request method...
    } else if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

        try {
            // check if json content exists or return error...
            if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Content type header is not set to JSON");
                $response->send();
                exit;
            }

            // get json content...
            $rawPatchData = file_get_contents('php://input');
            // validate json...
            if (!$jsonData = json_decode($rawPatchData)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Request body is not valid JSON");
                $response->send();
                exit;
            }

            // create two variables and set default values to false...
            $firstName = false;
            $lastName = false;

            $queryFields = "";

            // check json data for updated firstname and add to query...
            if (isset($jsonData->firstName)) {
                $firstName = true;
                $queryFields .= "first_name = :firstName, ";
            }

            // check json data for updated lastname and add to query...
            if (isset($jsonData->lastName)) {
                $lastName = true;
                $queryFields .= "last_name = :lastName, ";
            }

            // remove last comma from queryfields string...
            $queryFields = rtrim($queryFields, ", ");

            // return error if no updated first and last name is in json data...
            if ($firstName === false && $lastName === false) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("No user fields provided");
                $response->send();
                exit;
            }

            // select the current user data before update...
            $db->query("SELECT id, first_name, last_name, username, DATE_FORMAT(date_created, '%d/%m/%Y %H:%i') as date_created, dark_mode FROM users WHERE id = :userid;");
            $db->bind(":userid", $userid);
            $db->single();

            // return error if no user with that id
            if ($db->rowCount() === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("No user found to update");
                $response->send();
                exit;
            }

            // create user module with select query data...
            $user = new User($db->single()->id, $db->single()->first_name, $db->single()->last_name, $db->single()->username, (int)$db->single()->dark_mode);

            // create query...
            $db->query("UPDATE users SET " . $queryFields . " WHERE id = :userid");

            // bind firstname param if it exits...
            if ($firstName === true) {
                $user->setFirstName($jsonData->firstName);
                $updatedFirstName = $user->getFirstName();
                $db->bind(':firstName', $updatedFirstName);
            }
            // bind lastname param if it exits...
            if ($lastName === true) {
                $user->setFirstName($jsonData->lastName);
                $updatedLastName = $user->getFirstName();
                $db->bind(':lastName', $updatedLastName);
            }

            // don't forget to bind the user id for the record we want to update...
            $db->bind(':userid', $userid);
            $db->execute();

            // return error if could not update...
            if ($db->rowCount() === 0) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("User not updated");
                $response->send();
                exit;
            }

            // select the user we just updated...
            $db->query("SELECT id, first_name, last_name, username, DATE_FORMAT(date_created, '%d/%m/%Y %H:%i') as date_created, dark_mode FROM users WHERE id = :userid;");
            $db->bind(":userid", $userid);
            $db->single();

            // return error if id not found...
            if ($db->rowCount() === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("User not updated after update");
                $response->send();
                exit;
            }

            $userArray = array();

            // create user module from select query
            $user = new User($db->single()->id, $db->single()->first_name, $db->single()->last_name, $db->single()->username, (int)$db->single()->dark_mode);
            $userArray[] = $user->returnUserAsArray();

            // build return data...
            $returnData = array();
            $returnData['rows_returned'] = $db->rowCount();
            $returnData['users'] = $userArray;

            // create response to return updated user data...
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("User updated");
            $response->setData($returnData);
            $response->send();
            exit;

        // catch any input user errors and return errors from user model...
        } catch (UserException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }

    // if request method is something else return error...
    } else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }
// if userid and darkModeToggle are both in the header run the following...
} else if(array_key_exists("userid", $_GET) && array_key_exists("darkModeToggle", $_GET)){

    // get user id in header...
    $userid = $_GET['userid'];

    // check request method...
    if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {

        try {
            // check if user with id exists...
            $db->query("SELECT id, first_name, last_name, username, DATE_FORMAT(date_created, '%d/%m/%Y %H:%i') as date_created, dark_mode FROM users WHERE id = :userid;");
            $db->bind(":userid", $userid);
            $db->single();

            // if no user with that id return error...
            if ($db->rowCount() === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("No user found to update");
                $response->send();
                exit;
            }
            // create user module...
            $user = new User($db->single()->id, $db->single()->first_name, $db->single()->last_name, $db->single()->username, (int)$db->single()->dark_mode);

            // create query to update user dark mode...
            $db->query("UPDATE users SET dark_mode = :darkMode WHERE id = :userid");

            // call the toggleDarkMode method on user module.
            $updatedDarkMode = $user->toggleDarkMode($user->getDarkMode());
            // bind updatedDarkMode value to query...
            $db->bind(':darkMode', $updatedDarkMode);
            $db->bind(':userid', $userid);
            $db->execute();

            // confirm query returned a record or return error...
            if ($db->rowCount() === 0) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Dark mode not updated");
                $response->send();
                exit;
            }

            // get user query with updated values...
            $db->query("SELECT id, first_name, last_name, username, DATE_FORMAT(date_created, '%d/%m/%Y %H:%i') as date_created, dark_mode FROM users WHERE id = :userid;");
            $db->bind(":userid", $userid);
            $db->single();

            // if user id does not exist return error...
            if ($db->rowCount() === 0) {
                $response = new Response();
                $response->setHttpStatusCode(404);
                $response->setSuccess(false);
                $response->addMessage("Dark mode not updated after update");
                $response->send();
                exit;
            }

            // create user array...
            $userArray = array();

            // create user module with query data...
            $user = new User($db->single()->id, $db->single()->first_name, $db->single()->last_name, $db->single()->username, (int)$db->single()->dark_mode);
            $userArray[] = $user->returnUserAsArray();

            // build return data...
            $returnData = array();
            $returnData['rows_returned'] = $db->rowCount();
            $returnData['users'] = $userArray;

            // return the response data...
            $response = new Response();
            $response->setHttpStatusCode(200);
            $response->setSuccess(true);
            $response->addMessage("Dark mode updated");
            $response->setData($returnData);
            $response->send();
            exit;

        // catch any errors and return them...
        } catch (UserException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }

    // if request method is something else return error...
    } else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }

// if no user id or darkModeToggle is present run the following...
} else if(empty($_GET)){

    // check request method...
    if($_SERVER['REQUEST_METHOD'] === 'GET'){

        // select all users query...
        $db->query("SELECT id, first_name, last_name, username, DATE_FORMAT(date_created, '%d/%m/%Y %H:%i') as date_created, dark_mode FROM users");
        $db->resultset();

        // create user array...
        $userArray = array();

        // loop over all users returned and start user module...
        foreach($db->resultset() as $row){
            $user = new User($row->id, $row->first_name, $row->last_name, $row->username, (int)$row->dark_mode);
            $userArray[] = $user->returnUserAsArray();
        }

        // build return data...
        $returnData = array();
        $returnData['rows_returned'] = $db->rowCount();
        $returnData['users'] = $userArray;

        // return the data in a response...
        $response = new Response();
        $response->setHttpStatusCode(200);
        $response->setSuccess(true);
        $response->toCache(true);
        $response->setData($returnData);
        $response->send();
        exit;

    // check request method...
    } else if($_SERVER['REQUEST_METHOD'] === 'POST') {


        try {
            // check for json data or return error...
            if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Content type header is not set to JSON");
                $response->send();
                exit;
            }

            // get json content...
            $rawPOSTData = file_get_contents('php://input');

            // confirm if json or return error...
            if (!$jsonData = json_decode($rawPOSTData)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                $response->addMessage("Request body is not valid JSON");
                $response->send();
                exit;
            }

            // confirm all fields in supplied json data or return error...
            if (!isset($jsonData->username) || !isset($jsonData->firstName) || !isset($jsonData->lastName) || !isset($jsonData->darkMode)) {
                $response = new Response();
                $response->setHttpStatusCode(400);
                $response->setSuccess(false);
                (!isset($jsonData->username) ? $response->addMessage("Username field is required") : false);
                (!isset($jsonData->firstName) ? $response->addMessage("firstName field is required") : false);
                (!isset($jsonData->lastName) ? $response->addMessage("lastName field is required") : false);
                (!isset($jsonData->darkMode) ? $response->addMessage("darkMode field is required") : false);
                $response->send();
                exit;
            }

            // create user module with json data...
            $newUser = new User(null, $jsonData->firstName, $jsonData->lastName, $jsonData->username, (int)$jsonData->darkMode);
            $firstName = $newUser->getFirstName();
            $lastName = $newUser->getLastName();
            $username = $newUser->getUsername();
            $darkMode = $newUser->getDarkMode();

            // insert a new user...
            $db->query("INSERT INTO users (first_name, last_name, username, date_created, dark_mode) VALUES (:firstName, :lastName, :Username, NOW(), :darkMode)");
            $db->bind(":firstName", $firstName);
            $db->bind(":lastName", $lastName);
            $db->bind(":Username", $username);
            $db->bind(":darkMode", $darkMode);
            $db->execute();

            // if not inserted return error...
            if ($db->rowCount() === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Failed to create user");
                $response->send();
                exit;
            }

            // get the last insert id...
            $lastUserId = $db->lastInsertId();

            // get new user with the id last created...
            $db->query("SELECT id, first_name, last_name, username, DATE_FORMAT(date_created, '%d/%m/%Y %H:%i') as date_created, dark_mode FROM users WHERE id = :userid;");
            $db->bind(":userid", $lastUserId);
            $db->single();

            // confirm a record returned or return error...
            if ($db->rowCount() === 0) {
                $response = new Response();
                $response->setHttpStatusCode(500);
                $response->setSuccess(false);
                $response->addMessage("Failed to retrieve user");
                $response->send();
                exit;
            }

            // create user array...
            $userArray = array();

            // loop over all users and create user module...
            foreach ($db->resultset() as $row) {
                $user = new User($row->id, $row->first_name, $row->last_name, $row->username, (int)$row->dark_mode);
                $userArray[] = $user->returnUserAsArray();
            }

            // Build return data...
            $returnData = array();
            $returnData['rows_returned'] = $db->rowCount();
            $returnData['users'] = $userArray;

            // return respopnse data...
            $response = new Response();
            $response->setHttpStatusCode(201);
            $response->setSuccess(true);
            $response->addMessage("User created");
            $response->setData($returnData);
            $response->send();
            exit;

        // catch any errors with user data...
        } catch (UserException $ex) {
            $response = new Response();
            $response->setHttpStatusCode(400);
            $response->setSuccess(false);
            $response->addMessage($ex->getMessage());
            $response->send();
            exit;
        }
    // if request method is something else return error...
    } else {
        $response = new Response();
        $response->setHttpStatusCode(405);
        $response->setSuccess(false);
        $response->addMessage("Request method not allowed");
        $response->send();
        exit;
    }

// if endpoint is something else return error...
} else {
    $response = new Response();
    $response->setHttpStatusCode(404);
    $response->setSuccess(false);
    $response->addMessage("Endpoint not found");
    $response->send();
    exit;
}

