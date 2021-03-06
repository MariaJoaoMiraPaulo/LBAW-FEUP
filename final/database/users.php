<?php

function isLoginCorrect($email, $password){

    global $conn;
    $stmt = $conn -> prepare('SELECT password FROM user_table WHERE email = ?');
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hashed_password = $result['password'];

    return password_verify($password,$hashed_password);
}

function isPasswordCorrect($password){

    global $conn;
    $stmt = $conn -> prepare('SELECT password FROM user_table WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $hashed_password = $result['password'];

    return password_verify($password,$hashed_password);
}


function changePassword($new_password){
    global $conn;
    $stmt = $conn -> prepare('UPDATE user_table SET password = ? WHERE id = ?');
    return $stmt->execute([$new_password, $_SESSION['user_id']]);
}

function createUser($name, $email, $username, $password){

    global $conn;
    $stmt = $conn->prepare('INSERT INTO user_table (name, email, username, password ) VALUES(?,?,?,?)');

    return $stmt->execute([$name, $email,$username,password_hash($password, PASSWORD_DEFAULT)]);
}

function getUserId($email){
    global $conn;
    $stmt = $conn -> prepare('SELECT id FROM user_table WHERE email = ?');
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['id'];
}

function getUsername($email){
    global $conn;
    $stmt = $conn -> prepare('SELECT username FROM user_table WHERE email = ?');
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['username'];
}

function getNickNameById($id){
    global $conn;
    $stmt = $conn -> prepare('SELECT username FROM user_table WHERE id = ?');
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['username'];
}


function getUsernameById($id){

    global $conn;
    $stmt = $conn -> prepare('SELECT user_table.name FROM user_table WHERE id = ?');
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result['name'];
}



function checkForInvitation($email,$project){

    global $conn;

    $stmt = $conn->prepare('SELECT id_project FROM invited_users WHERE email = ?');
    $stmt->execute([$email]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $id_project = $result['id_project'];

    return $project + $id_project;
}

function joinProject($id, $project, $is_coordinator=FALSE){

    global $conn;

    if($is_coordinator == FALSE) {
      $is_coordinator = 'false';
    } elseif($is_coordinator == TRUE) {
      $is_coordinator = 'true';
    } else {
      //TODO show error??
      $is_coordinator = 'false';
    }
    $stmt = $conn->prepare('INSERT INTO user_project VALUES(?,?,?)');
    return $stmt->execute([$id,$project,$is_coordinator]);
}

function joinProjectInvited($id_user, $project) {
    global $conn;

    $user_info = getUserInfo($id_user);

    $sql_op1 =  "DELETE FROM invited_users WHERE email = ?;";
    $sql_op2 = "INSERT INTO user_project(id_user,id_project, is_coordinator)
    VALUES (?, ?, ?);";

    $is_coordinator = 'false';

    $conn->beginTransaction();
    $stmt = $conn->prepare($sql_op1);
    $sucess1 = $stmt->execute(array($user_info['email']));
    $stmt = $conn->prepare($sql_op2);
    $sucess2 = $stmt->execute(array($id_user,$project,$is_coordinator));
    if($sucess1 && $sucess2) {
        return $conn->commit();
    } else {
        $conn->rollBack();
        return $sucess1 && $sucess2;
    }

}

function getUserInfo($id){

    global $conn;
    $stmt = $conn -> prepare('SELECT * FROM user_table WHERE id = ?');
    $stmt->execute([$id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result;
}

function updateUserPhoto($email){
    global $conn;
    $stmt = $conn->prepare('UPDATE user_table SET photo_path = ? WHERE id = ?');
    return $stmt->execute([$email,$_SESSION['user_id']]);
}

function getUserEmail(){

    global $conn;
    $stmt = $conn -> prepare('SELECT email FROM user_table WHERE id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();

    return $result;
}

function getUserCountry($userId){
    global $conn;
    $stmt = $conn -> prepare('SELECT DISTINCT country.name FROM user_table, country WHERE user_table.country_id = country.id AND user_table.id = ?');
    $stmt->execute([$userId]);
    $result = $stmt->fetchAll();

    return $result;
}

function updateUserInfo($userName, $userEmail, $userCountry, $userCity){
    global $conn;
    $stmt = $conn->prepare('UPDATE user_table SET name = ?, email = ?, country_id = ?, city = ? WHERE id = ?');
    return $stmt->execute([$userName, $userEmail, $userCountry, $userCity, $_SESSION['user_id']]);
}


function getCountries(){
    global $conn;
    $stmt = $conn -> prepare('SELECT * FROM country');
    $stmt->execute();
    return $stmt->fetchAll();

}

function createProject($name){

    global $conn;
    $stmt = $conn->prepare('INSERT INTO project(name) VALUES (?)');
    $stmt->execute([$name]);

    $last_id = $conn->lastInsertId();

    global $conn;
    $stmt = $conn->prepare('INSERT INTO user_project(id_user,id_project,is_coordinator) VALUES (?,?,?)');
    $stmt->execute([$_SESSION['user_id'],$last_id,TRUE]);
}


function leaveProject($projectID){
    global $conn;
    $stmt = $conn->prepare('DELETE FROM user_project WHERE id_project = ?');
    $stmt->execute([$projectID]);
}
