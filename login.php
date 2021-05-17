<?php
    //$con = mysqli_connect("localhost", "root","","api");
    $con = mysqli_connect("localhost", "root","Dan020402!@#","api");
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
    $json = file_get_contents('php://input');
    $input_user = json_decode($json);
    if (!isset($input_user->username) || !isset($input_user->password)) {
        http_response_code(400);
        exit();
    }
    // $user = [
    //     'email' => 'johndoe@example.com',
    //     'password' => 'qwerty123'
    // ];
    //   if ($input_user->email !== $user['email'] || $input_user->password !== $user['password']) {
    //     echo json_encode([
    //       'message' => 'Email atau password tidak sesuai'
    //     ]);
    //     exit();
    //   }

        $query = "SELECT * FROM user WHERE username = '$input_user->username' AND password = MD5('$input_user->password')";  
           $result = mysqli_query($con, $query);  
           if(mysqli_num_rows($result)==NULL)  
           {  
                echo json_encode([
                'message' => 'Email atau password tidak sesuai'
                ]);
                exit();
           }
           else {
            $waktu_kadaluarsa = time() + (15 * 60);
      
            $payload = [
              'username' => $input_user->username,
              'exp' => $waktu_kadaluarsa
            ];
            
            $access_token = JWT::encode($payload, $_ENV['ACCESS_TOKEN_SECRET']);
            echo json_encode([
              'accessToken' => $access_token,
              'expiry' => date(DATE_ISO8601, $waktu_kadaluarsa)
            ]);
            
            $payload['exp'] = time() + (60 * 60);
            $refresh_token = JWT::encode($payload, $_ENV['REFRESH_TOKEN_SECRET']);
            // Simpan refresh token di http-only cookie
            setcookie('refreshToken', $refresh_token, $payload['exp'], '', '', false, true);   
           }       
?>