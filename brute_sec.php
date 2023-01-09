<?php
    // DB 설정 정보를 불러옴
    include './config.php';
    
    // LOW
    // 사용자 입력 필터링하지 않음
    $user = $_POST[ 'username' ]; 
    $pass = $_POST[ 'password' ];

    // MEDIUM
    // 사용자 입력 필터링함 : mysqli_real_escape_string()
    $user = ((isset($conn) && is_object($conn)) ? mysqli_real_escape_string($conn,  $user ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    $pass = ((isset($conn) && is_object($conn)) ? mysqli_real_escape_string($conn,  $pass ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));    


    $pass = md5( $pass );

    if( isset($user) && isset($pass) ) {
        $query  = "SELECT * FROM `users` WHERE user = '$user' AND password = '$pass';";
        $result = mysqli_query($conn,  $query ) or die("연결안됨");

        if( $result && mysqli_num_rows( $result ) == 1 ) {
              // 로그인 성공
            $row    = mysqli_fetch_assoc( $result );
            echo "<h1>Login Successfull</h1><p>Welcome to the password protected area {$user}</p>";
        }
        else {// 로그인 실패
            
            // HIGH
            sleep( 2 );
            
            echo "<h1>Login Falied</h1><p>Username and/or password incorrect.</p>";
        }

        mysqli_close($conn);
    }

?>