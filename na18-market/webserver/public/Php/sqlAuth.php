<?php
function connectPsql()
{
    $conn = new PDO('pgsql:host=tuxa.sme.utc;port=5432;dbname=dbna18a016', 'na18a016', 'cVydUH0r');
    return $conn;
}
function throwJsonException($msg) {
    echo json_encode(array('error'=> true, 'msg' => $msg));
}

?>