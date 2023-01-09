<?php
    // DB 설정 정보를 불러옴
    include './config.php';
    
    // LOW
    // 사용자 입력 필터링하지 않음
    $user = $_GET[ 'username' ]; 
    $pass = $_GET[ 'password' ];

    // MEDIUM
    // 사용자 입력 필터링함 : mysqli_real_escape_string()
    $user = ((isset($conn) && is_object($conn)) ? mysqli_real_escape_string($conn,  $user ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));
    $pass = ((isset($conn) && is_object($conn)) ? mysqli_real_escape_string($conn,  $pass ) : ((trigger_error("[MySQLConverterToo] Fix the mysql_escape_string() call! This code does not work.", E_USER_ERROR)) ? "" : ""));    
    $pass = md5( $pass );

    // Default values
	$total_failed_login = 3;
	$lockout_time       = 15;
	$account_locked     = false;

    // Check the database (Check user information) : prepare()사용
	$data = $db->prepare( 'SELECT failed_login, last_login FROM users WHERE user = (:user) LIMIT 1;' );
	$data->bindParam( ':user', $user, PDO::PARAM_STR );
	$data->execute();
	$row = $data->fetch();

    // Check to see if the user has been locked out.
	if( ( $data->rowCount() == 1 ) && ( $row[ 'failed_login' ] >= $total_failed_login ) )  {
		$last_login = strtotime( $row[ 'last_login' ] );
		$timeout    = $last_login + ($lockout_time * 60);
		$timenow    = time();

		if( $timenow < $timeout ) {
			$account_locked = true;
			// print "The account is locked<br />";
		}
	}

	// Check the database (if username matches the password)
	$data = $db->prepare( 'SELECT * FROM users WHERE user = (:user) AND password = (:password) LIMIT 1;' );
	$data->bindParam( ':user', $user, PDO::PARAM_STR);
	$data->bindParam( ':password', $pass, PDO::PARAM_STR );
	$data->execute();
	$row = $data->fetch();

    if( ( $data->rowCount() == 1 ) && ( $account_locked == false ) ) {
		// Get users details
		$avatar       = $row[ 'avatar' ];
		$failed_login = $row[ 'failed_login' ];
		$last_login   = $row[ 'last_login' ];

		// Login successful
		$html .= "<p>Welcome to the password protected area <em>{$user}</em></p>";
		$html .= "<img src=\"{$avatar}\" />";

		// Had the account been locked out since last login?
		if( $failed_login >= $total_failed_login ) {
			$html .= "<p><em>Warning</em>: Someone might of been brute forcing your account.</p>";
			$html .= "<p>Number of login attempts: <em>{$failed_login}</em>.<br />Last login attempt was at: <em>${last_login}</em>.</p>";
		}

		// Reset bad login count
		$data = $db->prepare( 'UPDATE users SET failed_login = "0" WHERE user = (:user) LIMIT 1;' );
		$data->bindParam( ':user', $user, PDO::PARAM_STR );
		$data->execute();
	} else {
		// Login failed
		sleep( rand( 2, 4 ) );

		// Give the user some feedback
		$html .= "<pre><br />Username and/or password incorrect.<br /><br/>Alternative, the account has been locked because of too many failed logins.<br />If this is the case, <em>please try again in {$lockout_time} minutes</em>.</pre>";

		// Update bad login count
		$data = $db->prepare( 'UPDATE users SET failed_login = (failed_login + 1) WHERE user = (:user) LIMIT 1;' );
		$data->bindParam( ':user', $user, PDO::PARAM_STR );
		$data->execute();
	}

	// Set the last login time
	$data = $db->prepare( 'UPDATE users SET last_login = now() WHERE user = (:user) LIMIT 1;' );
	$data->bindParam( ':user', $user, PDO::PARAM_STR );
	$data->execute();
?>

/*
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
*/