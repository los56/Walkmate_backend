<?php 
// Written by NamHyeok Kim

session_start();
require_once("dbConfig.php");

$_POST = json_decode(file_get_contents("php://input"), true);

$reqWalkKey = $_POST['walkKey'];
$resArray = array('isSuccess' => false);

try { 
    $sql = "SELECT * FROM walk WHERE walkKey = :reqKey";
    $query = $database -> prepare($sql);
    $query -> bindValue(':reqKey', $reqWalkKey, PDO::PARAM_INT);

    execQuery($query);

    if($query -> rowCount() < 1) {
        throw new Exception("없는 글", 3);
    }   

    $walkPost = $query -> fetch(PDO::FETCH_ASSOC);
    $resArray['isSuccess'] = true;
    $resArray['body'] = $walkPost;
    
    if(isset($_SESSION['userKey']) && ($_SESSION['userKey'] === $walkPost['hostKey'])) {
        $resArray['isHost'] = true;

        $memberSql = "SELECT * FROM memberList WHERE walkKey = :reqKey";
        $memberQuery = $database -> prepare($memberSql);
        $memberQuery -> bindValue(':reqKey', $reqWalkKey, PDO::PARAM_INT);
        execQuery($memberQuery);
        $resArray['memberList'] = $memberQuery -> fetchAll(PDO::FETCH_ASSOC);
        
        $applySql = "SELECT * FROM applyList WHERE walkKey = :reqKey";
        $applyQuery = $database -> prepare($applySql);
        $applyQuery -> bindValue(':reqKey', $reqWalkKey, PDO::PARAM_INT);
        execQuery($applyQuery);
        $resArray['applyList'] = $applyQuery -> fetchAll(PDO::FETCH_ASSOC);

    } else {
        $resArray['isHost'] = false;
    }
} catch (Exception $e) {
    $resArray['code'] = $e -> getCode();
    $resArray['errorDetail'] = $e -> getMessage();
}

echo json_encode($resArray, $__JSON_FLAGS|JSON_FORCE_OBJECT);
unset($database);

?>
