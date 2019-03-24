<?php
include("sqlAuth.php");

try {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $connexion = connectPsql();


    $resultset = $connexion->prepare("DELETE FROM ProduitDansPanier WHERE idacheteur =:acheteur  AND idproduit=:produit AND EXISTS (SELECT 1 FROM ProduitDansPanier WHERE idacheteur = :acheteur AND idproduit=:produit)");
    $resultset->bindParam(':acheteur', $request->acheteur, PDO::PARAM_INT);
    $resultset->bindParam(':produit', $request->produit, PDO::PARAM_INT);
    $resultset->execute();
} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>