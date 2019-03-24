<?php
include("sqlAuth.php");

try {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $connexion = connectPsql();


    $resultset = $connexion->prepare("SELECT * FROM Livraison LEFT JOIN acheteur ON acheteur.id=livraison.idacheteur WHERE idacheteur=:acheteur");
    $resultset->bindParam(':acheteur', $request->acheteur, PDO::PARAM_STR);
    $resultset->execute();
    $products = [];
    $i = 0;
    while ($row = $resultset->fetch(PDO::FETCH_ASSOC)) {
        $products[$i] = array(
            "id" => $row["idlivraison"],
            "nom" => $row["nom"],
            "prenom" => $row["prenom"],
            "adresse" => $row["adresse"],
            "postal" => $row["codepostal"],
        );
        $i++;
    }
    $myJSON = json_encode($products);
    echo $myJSON;
} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>