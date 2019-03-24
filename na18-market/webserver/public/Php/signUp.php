<?php
include("sqlAuth.php");

try {
    $connexion = connectPsql();

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $resultset = $connexion->prepare("SELECT * FROM Utilisateur WHERE email=:email");
    $resultset->bindParam(':email', $request->email, PDO::PARAM_STR);
    $resultset->execute();
    $row = $resultset->fetch(PDO::FETCH_ASSOC);
    if ($row==null) {
        $resultset = $connexion->prepare("INSERT INTO  Utilisateur(email,motdepass,nom,prenom) VALUES (:email,:motdepass,:nom,:prenom) RETURNING id");
        $resultset->bindParam(':email', $request->email, PDO::PARAM_STR);
        $resultset->bindParam(':motdepass', $request->motdepass, PDO::PARAM_STR);
        $resultset->bindParam(':nom', $request->nom, PDO::PARAM_STR);
        $resultset->bindParam(':prenom', $request->prenom, PDO::PARAM_STR);
        $resultset->execute();
        if ($resultset) {
            $id =$resultset->fetch(PDO::FETCH_ASSOC)["id"];
            $resultset = $connexion->prepare("INSERT INTO Acheteur(id) VALUES (:id)");
            $resultset->bindParam(':id', $id);
            $resultset->execute();
            if ($resultset) {
                echo true;
            }else{
                throwJsonException("Inserer perdu");
            }
        } else {
            throwJsonException("Inserer perdu");
        }
    } else {
        throwJsonException("Compte exist");
    }
} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>