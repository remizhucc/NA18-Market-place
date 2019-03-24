<?php
include("sqlAuth.php");

try {
    $connexion = connectPsql();

    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $resultset = $connexion->prepare("INSERT INTO ProduitDansPanier(idAcheteur, idProduit) SELECT :acheteur, :produit WHERE NOT EXISTS (SELECT 1 FROM ProduitDansPanier WHERE idAcheteur = :acheteur AND idProduit=:produit)");
    $resultset->bindParam(':acheteur', $request->acheteur, PDO::PARAM_STR);
    $resultset->bindParam(':produit', $request->produit, PDO::PARAM_STR);
    $resultset->execute();
    echo"INSERT INTO ProduitDansPanier(idAcheteur, idProduit) SELECT :acheteur, :produit FROM ProduitDansPanier WHERE NOT EXISTS (SELECT 1 FROM ProduitDansPanier WHERE idAcheteur = :acheteur AND idProduit=:produit)";
} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>