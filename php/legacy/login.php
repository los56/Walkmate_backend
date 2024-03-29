<?php 
//Written by NamHyeok Kim

session_start();
require_once("dbConfig.php");

$_POST = json_decode(file_get_contents("php://input"), true);

$id = $_POST['id'];
$pw = $_POST['password'];

$sql = "SELECT userKey, nickname, password FROM user WHERE ID = ?";
$query = $database->prepare($sql);

$resArray = array('isSuccess' => false);

if($query->execute(array($id))) {
    if($query->rowCount() === 1) {
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $pwVerify = password_verify($pw, $result['password']);
        if($pwVerify) {
            $_SESSION['user_key'] = $result['userKey'];
            $_SESSION['user_id'] = $id;
            $_SESSION['user_nickname'] = $result['nickname'];
            $resArray['isSuccess'] = true;
        } else {
            $resArray['reason'] = "비밀번호가 일치하지 않습니다.";
        }
    } else {
        $resArray['reason'] = "계정이 존재하지 않습니다.";
    }
} else {
    $resArray['reason'] = "DB 오류";
}

echo json_encode($resArray, $__JSON_FLAGS);
unset($database);

?>