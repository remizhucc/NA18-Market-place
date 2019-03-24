<?php
include("sqlAuth.php");

try {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $connexion = connectPsql();
    $resultset = $connexion->prepare("SELECT commande.idCommande AS idcommande, commande.date AS date, commande.statut AS statut, PrixCommande.prix AS prix FROM commande LEFT JOIN PrixCommande ON commande.idCommande= PrixCommande.Commande WHERE idacheteur=:id ORDER BY commande.date DESC");
    $resultset->bindParam(':id', $request->acheteur, PDO::PARAM_INT);
    $resultset->execute();

    $i = 0;
    $commande=array();
    while ($row = $resultset->fetch(PDO::FETCH_ASSOC)) {
        $commande[$i] = array(
            "id" => $row["idcommande"],
            "date" => $row["date"],
            "statut" => $row["statut"],
            "prix" => $row["prix"]
        );
        $i++;
    }
    $myJSON = json_encode($commande);
    echo $myJSON;
} catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>