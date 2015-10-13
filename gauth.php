<?php
/**
 * 使用 Google 帳號登入
 * 
 * @author ninthday <jeffy@ninthday.info>
 * @version 1.0
 * @copyright (c) 2015, Jeffy Shih
 */
session_start();
require_once './inc/setup.inc.php';

// Include Google API init file
require_once _APP_PATH . 'inc/gAuth.inc.php';

//Logout
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['access_token']);
    $gClient->revokeToken();
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL)); //redirect user back to page
}

//Authenticate code from Google OAuth Flow
//Add Access Token to Session
if (isset($_GET['code'])) {
    $gClient->authenticate($_GET['code']);
    $_SESSION['access_token'] = $gClient->getAccessToken();
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

//Set Access Token to make Request
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $gClient->setAccessToken($_SESSION['access_token']);
}

//Get User Data from Google Plus
//If New, Insert to Database
if ($gClient->getAccessToken()) {
    $userData = $objOAuthService->userinfo->get();
    try {
        if (!empty($userData)) {
            require_once _APP_PATH . 'classes/myPDOConn.Class.php';
            require_once _APP_PATH . 'classes/Authentication.Class.php';
            $pdoConn = \ninthday\niceToolbar\myPDOConn::getInstance('myPDOConnConfig.inc.php');
            $objUserAuth = new \ninthday\niceToolbar\Authentication($pdoConn);

            if ($objUserAuth->isExistandActived($userData)) {
                $_SESSION['access_token'] = $gClient->getAccessToken();
                header('Location: index.php');
            } else {
                $strMesg = "Your Account is not Active, Please contact adminstrator, thx!";
            }
        }
    } catch (Exception $exc) {
        echo $exc->getMessage();
    }
} else {
    $authUrl = $gClient->createAuthUrl();
}
?>

<html lang="zh-Hant">
    <head>
        <meta charset="UTF-8">
        <title><?php echo _WEB_NAME ?></title>
        <meta name="description" content="This Tool is for dmi-tcat">
        <!-- CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">

        <!-- niceToolbar CSS -->
        <link href="css/nicetoolbar.css" rel="stylesheet">
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style type="text/css">

            .box {font-family: Arial, sans-serif;background-color: #F1F1F1;border:0;width:340px;webkit-box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.3);box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.3);margin: 0 auto 25px;text-align:center;padding:10px 0px;}
            .box img{padding: 10px 0px;}
            .box a{color: #427fed;cursor: pointer;text-decoration: none;}
            .heading {text-align:center;padding:10px;font-family: 'Open Sans', arial;color: #555;font-size: 18px;font-weight: 400;}
            
            .welcome{font-size: 16px;font-weight: bold;text-align: center;margin: 10px 0 0;min-height: 1em;}
        </style>
    </head>
    <body>
        <div id="login-wrapper">
            <div class="row">
                <div class="col-xs-2 col-md-4"></div>
                <div class="col-xs-8 col-md-4">
                    <div class="card">
                        <div class="shop-item-image"></div>
                        <div class="card-title"><h2>Welcome to niceToolBar!</h2></div>
                        <div class="cart-content">
                            <center>
                                <!-- Show Login if the OAuth Request URL is set -->
                                <?php if (isset($authUrl)){ ?>
                                    <img class="circle-image" src="img/user_circle.png"><br>
                                    <a class='btn btn-google' href='<?php echo $authUrl; ?>'><i class="fa fa-google fa-lg"></i>&nbsp;|&nbsp;Sign in with Google</a>
                                    <!-- Show User Profile otherwise-->
                                    <?php
                                }else{
                                    ?>
                                    <img class="circle-image" src="<?php echo $userData->picture; ?>" width="100px" size="100px" /><br/>
                                    <p class="welcome">Welcome <a href="<?php echo $userData->link; ?>" /><?php echo $userData->name; ?></a>.</p>
                                    <?php
                                    if (isset($strMesg)) {
                                        echo '<p class="text-danger"><strong>' . $strMesg . '</strong></p>';
                                    }
                                    ?>
                                    <p><?php echo $userData->email; ?></p>
                                    <a class="btn btn-primary" href='?logout'><i class="fa fa-sign-out fa-lg"></i>&nbsp;|&nbsp;Logout</a>
                                <?php } ?>
                            </center>
                        </div>
                        <div class="description">
                            <p>Please Sign-in niceToolbar with your Google Account.</p>
                            <p>If you can't direct to main page. Please Contact the Website's manager.</p>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2 col-md-4"></div>
            </div>
        </div>
    </body>
</html>
