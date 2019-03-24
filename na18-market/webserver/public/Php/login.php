<?php
include("sqlAuth.php");

try {
    $connexion = connectPsql();

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $resultset = $connexion->prepare("SELECT * FROM Utilisateur WHERE email=:email AND motdepass=:motdepasse");
    $resultset->bindParam(':email', $request->email, PDO::PARAM_STR);
    $resultset->bindParam(':motdepasse', $request->motdepass, PDO::PARAM_STR);
    $resultset->execute();
    $row = $resultset->fetch(PDO::FETCH_ASSOC);
    if (!$row == null) {
        $identification = array(
            "id" => $row["id"],
            "nom" => $row["nom"],
            "prenom" => $row["prenom"],
            "email" => $row["email"]
        );
        $myJSON = json_encode($identification);
        echo $myJSON;
    } else {
        echo $row;
        throwJsonException("Wrong identification");
    }
} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>