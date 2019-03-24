<?php
include("sqlAuth.php");

try {
    $postdata = file_get_contents("php://input");
    $request = json_decode($postdata);
    $connexion = connectPsql();
    if($request->categorie=="Promotion"){
        $resultset = $connexion->prepare("SELECT * FROM ProduitPromotionList");
    }else{
        $resultset = $connexion->prepare("SELECT * FROM Produit WHERE idCategorie=:categorie ");
        $resultset->bindParam(':categorie', $request->categorie, PDO::PARAM_STR);
    }
    $resultset->execute();
    $Products = array();
    $i = 0;
    while ($row = $resultset->fetch(PDO::FETCH_ASSOC)) {
        $products[$i] = array(
            "id"=>$row["idproduit"],
            "nom" => $row["nom"],
            "prix" => $row["prix"],
            "description" => $row["description"],
            "prixpromotion"=>$row["prixpromotion"],
            "idvendeur" => $row["idvendeur"],
            "idcategorie" => $row["idcategorie"]
        );
        $i++;
    }
    $myJSON = json_encode($products);
    echo $myJSON;
}catch (Exception $e) {
    throwJsonException($e->getMessage());
}
?>