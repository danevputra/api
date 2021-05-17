<?php
//$con = mysqli_connect("localhost", "root","","api");
$con = mysqli_connect("localhost", "root","Dan020402!@#","api");
$response = array();
if ($con){
    header('Content-type: JSON');
    $input = (string) $_GET['kota'];
    if ($input){
        //$city = "jakarta";
        $sql= "SELECT * FROM data WHERE kota='$input'";
        $result = mysqli_query($con, $sql);
        $i = 0;
        while($row = mysqli_fetch_assoc($result)){
            $response[$i]['nama'] = $row ['nama'];
            $response[$i]['kota'] = $row ['kota'];
            $response[$i]['alamat'] = $row ['alamat'];
            $response[$i]['img'] = $row ['img'];
            $response[$i]['telp'] = $row ['telp'];
            $i++;
        }
        echo json_encode($response,JSON_PRETTY_PRINT);
    }
    else{
        echo "empty";
    }
}
else {
    echo "not connect";
}
?>