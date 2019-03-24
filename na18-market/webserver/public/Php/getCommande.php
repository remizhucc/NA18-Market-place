<?php
include("sqlAuth.php");

try {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $connexion = connectPsql();
    $resultset1 = $connexion->prepare("select utilisateur.email AS email,commande.idcommande AS idcommande, commande.date AS date, commande.modelivraison AS modelivraison, commande.statut AS statut,livraison.adresse AS adresse,livraison.nom AS nom,livraison.prenom AS prenom,livraison.codepostal AS codepostal,prixcommande.prix AS prix from ((Commande JOIN Utilisateur ON Commande.idAcheteur=Utilisateur.id) JOIN Livraison ON Commande.idAdresse=Livraison.idLivraison) JOIN PrixCommande ON PrixCommande.Commande=Commande.idCommande WHERE  Commande.idCommande=:idcommande");
    $resultset1->bindParam(':idcommande', $request->idcommande, PDO::PARAM_INT);
    $resultset1->execute();
    $rowCommande = $resultset1->fetch(PDO::FETCH_ASSOC);

    $resultset2 = $connexion->prepare("SELECT Produit.nom AS nom,ProduitDansCommande.prix AS prix, ProduitDansCommande.quantite AS quantite FROM ProduitDansCommande LEFT JOIN Produit ON Produit.idProduit=ProduitDansCommande.idProduit WHERE idCommande=:idcommande");
    $resultset2->bindParam(':idcommande', $request->idcommande, PDO::PARAM_INT);
    $resultset2->execute();

    $i = 0;
    $products=array();
    while ($row = $resultset2->fetch(PDO::FETCH_ASSOC)) {
        $products[$i] = array(
            "nom" => $row["nom"],
            "quantite" => $row["quantite"],
            "prix" => $row["prix"]
        );
        $i++;
    }

    $commande = array(
        "email" => $rowCommande["email"],
        "idcommande" => $rowCommande["idcommande"],
        "date" => $rowCommande["date"],
        "modelivraison" => $rowCommande["modelivraison"],
        "statut" => $rowCommande["statut"],
        "adresse" => $rowCommande["adresse"],
        "nom" => $rowCommande["nom"],
        "prenom" => $rowCommande["prenom"],
        "codepostal" => $rowCommande["codepostal"],
        "prix" => $rowCommande["prix"],
        "products" => $products
    );
    $myJSON = json_encode($commande);
    echo $myJSON;
} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>