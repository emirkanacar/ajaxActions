# ajaxActions // i need time fix for issues(thank you)
Easily manage your ajax connections and secure your ajax connections.

Installation guide:

  * require package
    ```
    $ composer require emirkanacar/ajax-actions
    ```

  * You need to start session(if not start session time functions not work)
    ```php
    session_start();
    ```
  * require the composer vendor
    ```php
    reqire 'vendor/autoload.php';
    ```
  * call by namespace
    ```php
    use emirkanacar\AjaxActions;
    ```
  * create config variables
    ```php
    $action_name = ""; // need the action name
    $request_number = 0; // if you want to set the request limit, if you do not want request limitation type 0
    $time_limit = 0; //  if you want to set specify time limit for the request set limit, if you want not limit set 0
    $http_protocol = ''; if you are using https set https, if you are using http set http
    $debug = false; // if you want debug details set true
    ```
  * create library connection
    ```php
    $ajax = new AjaxActions($action_name, $request_number, $time_limit, $http_protocol, $debug);
    ```
  * this function check the connection type(ajax, etc) and filtering for security
    ```php
    $ajax->checkConnectionType();
    ```
  * get ajax data from javascript function
    ```php
    $ajax->getAjaxData();
    ```
    * get request debug data
    ```php
    $ajax->getDebugData();
    ```
  * (optional) set callback javascript function
    ```php
    $ajax->returnCallback(['name' => 'value']);
    ```
    
    If you found issue, please create issue form.
    
