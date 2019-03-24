<?php
include("sqlAuth.php");

try {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $connexion = connectPsql();


    $resultset = $connexion->prepare("SELECT * FROM ProduitDansPanier LEFT JOIN Produit ON Produit.idproduit=ProduitDansPanier.idProduit WHERE idAcheteur=:acheteur");
    $resultset->bindParam(':acheteur', $request->acheteur, PDO::PARAM_STR);
    $resultset->execute();
    $i = 0;
    $products=array();
    while ($row = $resultset->fetch(PDO::FETCH_ASSOC)) {
        $products[$i] = array(
            "id" => $row["idproduit"],
            "nom" => $row["nom"],
            "prix" => $row["prix"],
            "description" => $row["description"],
            "prixpromotion" => $row["prixpromotion"],
            "idvendeur" => $row["idvendeur"],
            "idcategorie" => $row["idcategorie"],
            "quantite" => $row["quantité"]
        );
        $i++;
    }
    $myJSON = json_encode($products);
    echo $myJSON;
} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>