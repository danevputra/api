<?php
    // Import script autoload agar bisa menggunakan library
    require_once('./vendor/autoload.php');
    // Import library
    use Firebase\JWT\JWT;
    use Dotenv\Dotenv;
    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit();
    }
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        exit();
    }
    list(, $token) = explode(' ', $headers['Authorization']);
    //var_dump($token);

    try{
        JWT::decode($token, $_ENV['ACCESS_TOKEN_SECRET'], ['HS256']);
        //$con = mysqli_connect("localhost", "root","","api");
        $con = mysqli_connect("localhost", "root","Dan020402!@#","api");
        if ($con){
            $arr = json_decode(file_get_contents("php://input"));
            if (empty($arr)){ 
                exit("Data empty.");
            } else {
                $sql = "INSERT INTO data (nama, kota, alamat, img, telp) VALUES ('$arr->nama','$arr->kota','$arr->alamat','$arr->img','$arr->telp')";
                $query = mysqli_query($con,$sql);
                echo json_encode("data ditambahkan");
            }
        }
        else {
            echo "not connect";
        }
    }
    catch (Exception $e) {
        // Bagian ini akan jalan jika terdapat error saat JWT diverifikasi atau di-decode
        http_response_code(401);
        exit();
      }
?>