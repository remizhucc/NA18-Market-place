<?php
include("sqlAuth.php");

try {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $connexion = connectPsql();

    $resultset = $connexion->prepare("INSERT INTO  Commande(idAcheteur,idAdresse,modeLivraison) VALUES (:acheteur,:adresse,:mode) RETURNING idCommande");
    $resultset->bindParam(':acheteur', $request->acheteur, PDO::PARAM_INT);
    $resultset->bindParam(':adresse', $request->livraison, PDO::PARAM_INT);
    $resultset->bindParam(':mode', $request->modelivraison, PDO::PARAM_STR);
    $resultset->execute();

    $id = $resultset->fetch(PDO::FETCH_ASSOC)["idcommande"];

    $resultset1 = $connexion->prepare("DELETE FROM ProduitDansPanier WHERE idacheteur =:acheteur  AND idproduit=:produit AND EXISTS (SELECT 1 FROM ProduitDansPanier WHERE idacheteur = :acheteur AND idproduit=:produit)");
    $resultset2 = $connexion->prepare("INSERT INTO  ProduitDansCommande(idCommande,idProduit,quantite,prix) VALUES (:commande,:produit,:quantite,:prix)");

    foreach ($request->products as &$product) {
        if ($product->prixpromotion != null) {
            $prix=$product->prixpromotion;
        }else{
            $prix=$product->prix;
        }
        if((int)($product->quantite)>0){
            $resultset1->bindParam(':acheteur', $request->acheteur, PDO::PARAM_INT);
            $resultset1->bindParam(':produit', $product->id, PDO::PARAM_INT);
            $resultset1->execute();
            $resultset2->bindParam(':commande', $id, PDO::PARAM_INT);
            $resultset2->bindParam(':produit', $product->id, PDO::PARAM_INT);
            $resultset2->bindParam(':quantite', $product->quantite, PDO::PARAM_INT);
            $resultset2->bindParam(':prix', $prix, PDO::PARAM_STR);
            $resultset2->execute();
        }
    }


    echo $id;

} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>