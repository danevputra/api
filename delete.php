<?php
    // Import script autoload agar bisa menggunakan library
    require_once('./vendor/autoload.php');
    // Import library
    use Firebase\JWT\JWT;
    use Dotenv\Dotenv;
    use Carbon\Carbon;
    //require 'vendor/autoload.php';

    $dotenv = Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    // PHP has no base64UrlEncode function, so let's define one that
    // does some magic by replacing + with -, / with _ and = with ''.
    // This way we can pass the string within URLs without
    // any URL encoding.
    function base64UrlEncode($text)
    {
        return str_replace(
            ['+', '/', '='],
            ['-', '_', ''],
            base64_encode($text)
        );
    }


    header('Content-Type: application/json');
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        exit();
    }
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        exit();
    }
    list(, $token) = explode(' ', $headers['Authorization']);

    $input = $token;
    // get the local secret key
    $secret = getenv('SECRET');

    if (!$input) {
        echo json_encode('Please provide a key to verify');
        exit();
    }

    $jwt = $input;

    // split the token
    $tokenParts = explode('.', $jwt);
    $header = base64_decode($tokenParts[0]);
    $payload = base64_decode($tokenParts[1]);
    $signatureProvided = $tokenParts[2];

    // check the expiration time - note this will cause an error if there is no 'exp' claim in the token
    $expiration = Carbon::createFromTimestamp(json_decode($payload)->exp);
    $tokenExpired = (Carbon::now()->diffInSeconds($expiration, false) < 0);

    // build a signature based on the header and payload using the secret
    $base64UrlHeader = base64UrlEncode($header);
    $base64UrlPayload = base64UrlEncode($payload);
    $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, $secret, true);
    $base64UrlSignature = base64UrlEncode($signature);

    // verify it matches the signature provided in the token
    $signatureValid = ($base64UrlSignature === $signatureProvided);

    // echo "Header:\n" . $header . "\n";
    // echo "Payload:\n" . $payload . "\n";
    $mystring = '"username":"admin"';


    // Test if string contains the word 
    if(strpos($payload,$mystring) !== false){
        try{
            JWT::decode($token, $_ENV['ACCESS_TOKEN_SECRET'], ['HS256']);
            //$con = mysqli_connect("localhost", "root","","api");
            $con = mysqli_connect("localhost", "root","Dan020402!@#","api");
            if ($con){
                $arr = json_decode(file_get_contents("php://input"));
                if (empty($arr)){ 
                    exit("Data empty.");
                } else {
                    $sql2 = "SELECT * FROM data WHERE nama='$arr->nama'"; 
                    $query2 = mysqli_query($con,$sql2);
                    $row = mysqli_num_rows($query2);
                    if ($row){
                        $sql = "DELETE FROM data WHERE nama='$arr->nama'"; 
                        $query = mysqli_query($con,$sql);
                        echo json_encode("data dihapus");
                    }
                    else {
                        echo json_encode("data tidak ada");
                    }
                    //$sql = "INSERT INTO data (nama, kota, alamat, telp) VALUES ('$arr->nama','$arr->kota','$arr->alamat','$arr->telp')";
                    
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
    } else{
        echo json_encode("anda tidak berhak");
        http_response_code(401);
        exit();
    }
?>