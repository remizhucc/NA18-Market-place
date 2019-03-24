<?php
include("sqlAuth.php");

try {
    $connexion = connectPsql();

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);

    $resultset = $connexion->prepare("INSERT INTO  Livraison(idacheteur,adresse,codepostal,nom,prenom) VALUES (:acheteur,:adresse,:codePostal,:nom,:prenom) RETURNING idlivraison");
    $resultset->bindParam(':acheteur', $request->acheteur, PDO::PARAM_INT);
    $resultset->bindParam(':adresse', $request->adresse, PDO::PARAM_STR);
    $resultset->bindParam(':codePostal', $request->postal, PDO::PARAM_INT);
    $resultset->bindParam(':nom', $request->nom, PDO::PARAM_STR);
    $resultset->bindParam(':prenom', $request->prenom, PDO::PARAM_STR);
    $resultset->execute();
    $id = $resultset->fetch(PDO::FETCH_ASSOC)["idlivraison"];
    echo $id;

} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>