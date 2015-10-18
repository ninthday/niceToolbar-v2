<?php
session_start();
require './inc/setup.inc.php';

// Include Google API init file
require_once _APP_PATH . 'inc/gAuth.inc.php';

//Set Access Token to make Request
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
    $gClient->setAccessToken($_SESSION['access_token']);
    $userData = $objOAuthService->userinfo->get();
} else {
    header('Location: ' . _WEB_ADDR . 'gauth.php');
}

require_once _APP_PATH . 'classes/myPDOConn.Class.php';
require_once _APP_PATH . 'classes/BinManager.Class.php';

use ninthday\niceToolbar\myPDOConn;
use ninthday\niceTcatBar\BinManager;

$binID = intval(filter_input(INPUT_GET, 'bid', FILTER_SANITIZE_NUMBER_INT));

try {
    $tcatPDOConn = myPDOConn::getInstance('tcatPDOConnConfig.inc.php');
    $objBinMgr = new BinManager($tcatPDOConn);
    $objBin = $objBinMgr->getBinByBinID($binID);
} catch (Exception $exc) {
    echo $exc->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-Hant">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="">
        <meta name="author" content="ninithday">
        <title>niceToolBar-v2</title>
        <!-- CSS -->
        <link href="css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
        <!-- MetisMenu CSS -->
        <link href="resources/metisMenu/dist/metisMenu.min.css" rel="stylesheet">
        <!-- niceToolbar CSS -->
        <link href="css/nicetoolbar.css" rel="stylesheet">
        <!-- bootstrap datePicker CSS -->
        <link href="resources/bootstrap-datepicker/css/bootstrap-datepicker3.min.css" rel="stylesheet">
        <!-- NProgress CSS -->
        <link rel='stylesheet' href="resources/nprogress/nprogress.css">
        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
          <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
          <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>
        <div id="wrapper">
            <!-- Navigation -->
            <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="index.html">niceToolBar@v2</a>
                </div>
                <!-- /.navbar-header -->

                <ul class="nav navbar-top-links navbar-right">
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-envelope fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-messages">
                            <li>
                                <a href="#">
                                    <div>
                                        <strong>John Smith</strong>
                                        <span class="pull-right text-muted">
                                            <em>Yesterday</em>
                                        </span>
                                    </div>
                                    <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <strong>John Smith</strong>
                                        <span class="pull-right text-muted">
                                            <em>Yesterday</em>
                                        </span>
                                    </div>
                                    <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <strong>John Smith</strong>
                                        <span class="pull-right text-muted">
                                            <em>Yesterday</em>
                                        </span>
                                    </div>
                                    <div>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Pellentesque eleifend...</div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a class="text-center" href="#">
                                    <strong>Read All Messages</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                        <!-- /.dropdown-messages -->
                    </li>
                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-tasks fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-tasks">
                            <li>
                                <a href="#">
                                    <div>
                                        <p>
                                            <strong>Task 1</strong>
                                            <span class="pull-right text-muted">40% Complete</span>
                                        </p>
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-success" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%">
                                                <span class="sr-only">40% Complete (success)</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <p>
                                            <strong>Task 2</strong>
                                            <span class="pull-right text-muted">20% Complete</span>
                                        </p>
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%">
                                                <span class="sr-only">20% Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <p>
                                            <strong>Task 3</strong>
                                            <span class="pull-right text-muted">60% Complete</span>
                                        </p>
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
                                                <span class="sr-only">60% Complete (warning)</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <p>
                                            <strong>Task 4</strong>
                                            <span class="pull-right text-muted">80% Complete</span>
                                        </p>
                                        <div class="progress progress-striped active">
                                            <div class="progress-bar progress-bar-danger" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%">
                                                <span class="sr-only">80% Complete (danger)</span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a class="text-center" href="#">
                                    <strong>See All Tasks</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                        <!-- /.dropdown-tasks -->
                    </li>
                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-bell fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-alerts">
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-comment fa-fw"></i> New Comment
                                        <span class="pull-right text-muted small">4 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-twitter fa-fw"></i> 3 New Followers
                                        <span class="pull-right text-muted small">12 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-envelope fa-fw"></i> Message Sent
                                        <span class="pull-right text-muted small">4 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-tasks fa-fw"></i> New Task
                                        <span class="pull-right text-muted small">4 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <div>
                                        <i class="fa fa-upload fa-fw"></i> Server Rebooted
                                        <span class="pull-right text-muted small">4 minutes ago</span>
                                    </div>
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a class="text-center" href="#">
                                    <strong>See All Alerts</strong>
                                    <i class="fa fa-angle-right"></i>
                                </a>
                            </li>
                        </ul>
                        <!-- /.dropdown-alerts -->
                    </li>
                    <!-- /.dropdown -->
                    <li class="dropdown">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-user fa-fw"></i>  <i class="fa fa-caret-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-user">
                            <li><a href="#"><i class="fa fa-user fa-fw"></i> User Profile</a>
                            </li>
                            <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                            </li>
                            <li class="divider"></li>
                            <li><a href="login.html"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                            </li>
                        </ul>
                        <!-- /.dropdown-user -->
                    </li>
                    <!-- /.dropdown -->
                </ul>
                <!-- /.navbar-top-links -->

                <div class="navbar-default sidebar" role="navigation">
                    <div class="sidebar-nav navbar-collapse">
                        <ul class="nav" id="side-menu">
                            <li class="sidebar-header text-center">
                                <img class="circle-image-small" src="<?php echo $userData->picture; ?>"><br>
                                <p>Hi, <?php echo $userData->givenName; ?></p>
                                <a href="<?php echo _WEB_ADDR . 'gauth.php' ?>?logout"><i class="fa fa-sign-out fa-lg"></i>&nbsp;|&nbsp;Logout</a>
                            </li>

                            <li>
                                <a href="index.html"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Tcat View<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="#">Bin Overview</a>
                                    </li>
                                    <li>
                                        <a href="#">URL Analysis</a>
                                    </li>
                                    <li>
                                        <a href="#">Mention Analysis</a>
                                    </li>
                                    <li>
                                        <a href="#">Language Analysis</a>
                                    </li>
                                </ul>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-bar-chart-o fa-fw"></i> Charts<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="flot.html">Flot Charts</a>
                                    </li>
                                    <li>
                                        <a href="morris.html">Morris.js Charts</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li>
                                <a href="tables.html"><i class="fa fa-table fa-fw"></i> Tables</a>
                            </li>
                            <li>
                                <a href="forms.html"><i class="fa fa-edit fa-fw"></i> Forms</a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-wrench fa-fw"></i> UI Elements<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="panels-wells.html">Panels and Wells</a>
                                    </li>
                                    <li>
                                        <a href="buttons.html">Buttons</a>
                                    </li>
                                    <li>
                                        <a href="notifications.html">Notifications</a>
                                    </li>
                                    <li>
                                        <a href="typography.html">Typography</a>
                                    </li>
                                    <li>
                                        <a href="icons.html"> Icons</a>
                                    </li>
                                    <li>
                                        <a href="grid.html">Grid</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-sitemap fa-fw"></i> Multi-Level Dropdown<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="#">Second Level Item</a>
                                    </li>
                                    <li>
                                        <a href="#">Second Level Item</a>
                                    </li>
                                    <li>
                                        <a href="#">Third Level <span class="fa arrow"></span></a>
                                        <ul class="nav nav-third-level">
                                            <li>
                                                <a href="#">Third Level Item</a>
                                            </li>
                                            <li>
                                                <a href="#">Third Level Item</a>
                                            </li>
                                            <li>
                                                <a href="#">Third Level Item</a>
                                            </li>
                                            <li>
                                                <a href="#">Third Level Item</a>
                                            </li>
                                        </ul>
                                        <!-- /.nav-third-level -->
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li> 
                            <li>
                                <a href="#"><i class="fa fa-files-o fa-fw"></i> Sample Pages<span class="fa arrow"></span></a>
                                <ul class="nav nav-second-level">
                                    <li>
                                        <a href="blank.html">Blank Page</a>
                                    </li>
                                    <li>
                                        <a href="login.html">Login Page</a>
                                    </li>
                                </ul>
                                <!-- /.nav-second-level -->
                            </li>
                            <li class="sidebar-search">
                                <div class="input-group custom-search-form">
                                    <input type="text" class="form-control" placeholder="Search...">
                                    <span class="input-group-btn">
                                        <button class="btn btn-default" type="button">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </span>
                                </div>
                                <!-- /input-group -->
                            </li>
                            <li>
                                <?php include './footer.php'; ?>
                            </li>
                        </ul>
                    </div>
                    <!-- /.sidebar-collapse -->
                </div>
                <!-- /.navbar-static-side -->
            </nav>
            <div id="page-wrapper">
                <div class="row">
                    <div class="col-md-3 col-sm-6">
                        <?php
                        $cardType = ($objBin->activeState) ? 'card-danger' : 'card-default';
                        $cardActive = ($objBin->activeState) ? 'card-active' : '';
                        ?>
                        <div class="card <?php echo $cardType; ?>">
                            <div class="shop-item-image"></div>
                            <div class="card-title <?php echo $cardActive; ?>"><h3>Bin Information</h3></div>
                            <div class="card-info">
                                <h3><?php echo $objBin->binName; ?></h3><small>Bin Name</small>
                                <div class="description">
                                    <input type="hidden" id="bin-id" value="<?php echo $binID; ?>">
                                    <p><?php echo $objBin->binComment; ?></p>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <i class="fa fa-twitter"></i>
                                        <h3><?php echo number_format($objBin->nrOfTweets); ?></h3><small>Number of Tweets</small>
                                    </div>
                                    <div class="col-xs-6">
                                        <i class="fa fa-user"></i>
                                        <h3><?php echo number_format($objBin->nrOfUsers); ?></h3><small>Number of Users</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <i class="fa fa-calendar"></i>
                                        <h4><?php echo $objBin->dataStart; ?></h4><small>Data Start</small>
                                    </div>
                                    <div class="col-xs-6">
                                        <i class="fa fa-calendar"></i>
                                        <h4><?php echo $objBin->dataEnd; ?></h4><small>Data End</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <div class="row">
                                    <div class="col-xs-6">
                                        <i class="fa fa-clock-o"></i>
                                        <h4><?php echo $objBin->periodStart; ?></h4><small>Collect Start</small>
                                    </div>
                                    <div class="col-xs-6">
                                        <i class="fa fa-clock-o"></i>
                                        <h4><?php echo $objBin->periodEnd; ?></h4><small>Collect End</small>
                                    </div>
                                </div>
                            </div>
                            <div class="card-content">
                                <?php
                                foreach ($objBin->binPhrases as $binPhrase) {
                                    echo '<span class="phrase">' . $binPhrase . '</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="col-md-4 col-sm-6">
                                <div class="card card-info">
                                    <div class="card-title"><h3>Sub-Bin Infomation</h3></div>
                                    <div class="card-info">
                                        <small>Sub-Bin Condition</small>
                                        <div class="info-item" title="Data Duration"><i class="fa fa-calendar"></i>&nbsp;&nbsp;<span id="sbin-date"></span></div>
                                        <div class="info-item" title="Search"><i class="fa fa-search"></i>&nbsp;&nbsp;<span id="sbin-search"></span></div>
                                        <div class="info-item" title="User"><i class="fa fa-user"></i>&nbsp;&nbsp;<span id="sbin-user"></span></div>
                                        <div class="info-item" title="Language"><i class="fa fa-microphone"></i>&nbsp;&nbsp;<span id="sbin-lang"></span></div>
                                    </div>
                                    <div class="card-content">
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <i class="fa fa-twitter"></i>
                                                <h3>201,598</h3><small>Number of Tweets</small>
                                            </div>
                                            <div class="col-xs-6">
                                                <i class="fa fa-user"></i>
                                                <h3>151,367</h3><small>Number of Users</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="actions">
                                            <button type="button" class="btn btn-info btn-small" id="sbin-bookmark">
                                                <i class="fa fa-bookmark"></i>&nbsp;&nbsp;Bookmark this
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8 col-sm-6">
                                <div class="card card-warning">
                                    <div class="card-title"><h3>Sub-Bin Creator</h3></div>
                                    <div class="card-content">
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="duration">Data Duration</label>
                                                    <div class="input-daterange input-group" id="datepicker">
                                                        <input type="text" class="input-sm form-control" name="startday" value="<?php echo date("Y-m-d",
                                        strtotime($objBin->dataStart)); ?>">
                                                        <span class="input-group-addon">to</span>
                                                        <input type="text" class="input-sm form-control" name="endday" value="<?php echo date("Y-m-d",
                                        strtotime($objBin->dataEnd)); ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="search-text">Search</label>
                                                    <input type="text" class="form-control" id="search-text" name="search-keyword" placeholder="Search Tweets Content">
                                                    <p class="help-block">empty: from any text.</p>
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label for="from-user">From User</label>
                                                    <input type="text" class="form-control" id="from-user" name="from-user" placeholder="From User">
                                                    <p class="help-block">empty: from any user.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-12">
                                                <div class="form-group">
                                                    <label for="language">Language</label><br>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" name="language" id="lan-en" value="en" checked="checked"> en
                                                    </label>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" name="language" id="lan-zh" value="zh" checked="checked"> zh
                                                    </label>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" name="language" id="lan-zhtw" value="zh-tw" checked="checked"> zh-tw
                                                    </label>
                                                    <label class="checkbox-inline">
                                                        <input type="checkbox" name="language" id="lan-other" value="other" checked="checked"> other
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-content">
                                        <div class="actions">
                                            <button type="button" class="btn btn-warning btn-small" id="select-subbin"><i class="fa fa-refresh"></i>&nbsp;&nbsp;Update Sub-Bin</button>&nbsp;
                                            <button type="button" class="btn btn-small"><i class="fa fa-power-off"></i>&nbsp;&nbsp;Reset</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="card card-default">
                                    <div class="card-content">
                                        <div class="row">
                                            <div class="col-md-10">
                                                <div id="timeseries-chart" style="min-width: 200px; height: 300px; margin: 0 auto"></div>
                                            </div>
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label for="resolution">Resolution</label><br>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="resolution" id="perdays" value="day" checked="checked"> days
                                                    </label>
                                                    <label class="radio-inline">
                                                        <input type="radio" name="resolution" id="perhours" value="hour"> hours
                                                    </label>
                                                </div>
                                                <button type="button" class="btn btn-small">Redraw</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8 col-sm-12">
                                <div class="card card-default">
                                    <div class="card-content">
                                        <div class="row">
                                            <div class="col-md-4 col-xs-6">
                                                <h4>151,367</h4><small>Contain Mentions</small>
                                                <div style="height: 200px; margin: 0 auto"></div>
                                            </div>
                                            <div class="col-md-4  col-xs-6">
                                                <h4>151,367</h4><small>Contain Hashtags</small>
                                                <div style="height: 200px; margin: 0 auto"></div>
                                            </div>
                                            <div class="col-md-4 col-xs-6">
                                                <h4>151,367</h4><small>Contain Media</small>
                                                <div style="height: 200px; margin: 0 auto"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-12">
                                <div class="card card-default">
                                    <div class="card-content">
                                        <h4>Language</h4><small>Percentage (%)</small>
                                        <div style="height: 200px; margin: 0 auto"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <!-- /#page-wrapper -->
        </div>
        <!-- /#wrapper -->
        <div id="alert-message" class="alert alert-warning alert-dismissible" role="alert" style="display: none;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <span id="alert-content"></span>
        </div>
        <!-- /#alert-message -->
        <!-- jquery CDN -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <!-- Bootstrap Core JavaScript -->
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <!-- Metis Menu Plugin JavaScript -->
        <script src="resources/metisMenu/dist/metisMenu.min.js"></script>
        <!-- NProcess JavaScript -->
        <script src='resources/nprogress/nprogress.js'></script>
        <!-- Highcharts JavaScript -->
        <script src="http://code.highcharts.com/highcharts.js"></script>
        <!-- Custom Theme JavaScript -->
        <script src="js/sb-admin-2.js"></script>
        <script src="resources/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
        <script type="text/javascript" src="js/binview.js"></script>
        <script type="text/javascript">
            $('.input-daterange').datepicker({
                format: "yyyy-mm-dd",
                startView: 1,
                autoclose: true,
                todayHighlight: true
            });

            $(document).ajaxStart(function () {
                NProgress.start();
            })
                    .ajaxStop(function () {
                        NProgress.done();
                    });
        </script>

    </body>

</html>