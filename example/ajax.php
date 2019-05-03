<?php
    session_start();
    
    include 'ajax_actions/AjaxActions.php';

    use emirkanacar\AjaxActions;

    $ajax = new AjaxActions('user_action', 12, 30, 'http', true);

    $ajax->checkConnectionType();
    $ajax->getAjaxData();
    $ajax->returnCallback(['return' => 'ok', 'data' => 'rlx']);