<?php


namespace emirkanacar;


class AjaxActions
{
    protected $action_name;
    protected $request_number;
    protected $request_timeout;
    protected $connectionType;
    protected $debug;
    protected $firstRequestTime;
    protected $timeExpire;
    protected $requested_count;

    private $address;
    protected $action_id;
    protected $action_hash;
    private $time;
    private $requestStatus;

    public function __construct( $action_name, $request_number = 0, $request_timeout = 0, $connectionType = 'http', $debug = false)
    {
        $this->action_name = $action_name;
        $this->request_number = $request_number;
        $this->request_timeout = $request_timeout;
        $this->connectionType = $connectionType;
        $this->debug = $debug;
    }

    protected  function setTime()
    {
        $this->time = date("Y-m-d H:i:s");
    }

    protected function checkCookie()
    {
        if(!isset($_COOKIE['ajaxActions']))
        {
            setcookie('ajaxActions', null, time() - 600);
        }
    }

    protected function getRequestDetails()
    {
        return json_encode(array('name' => $this->action_name, 'hash' => $this->action_hash, 'time' => $this->time));
    }

    protected function createRequestHash()
    {
        $this->action_id = rand();
        $this->action_hash = hash_hmac('ripemd160', $this->action_name, $this->action_id);
    }

    protected function is_ajax()
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
    }

    protected function initRequestTimeBlock() {
        if(!isset($_SESSION['FIRST_REQUESTED_TIME']))
        {
            $_SESSION['FIRST_REQUESTED_TIME'] = $this->time;
        }

        $this->firstRequestTime = $_SESSION['FIRST_REQUESTED_TIME'];
        $this->timeExpire = date("Y-m-d H:i:s", strtotime($this->firstRequestTime)+($this->request_timeout));
        if(!isset($_SESSION['REQ_COUNT']))
        {
            $_SESSION['REQ_COUNT'] = 0;
        }

        $this->requested_count = $_SESSION['REQ_COUNT'];
        $this->requested_count++;

        if($this->time > $this->timeExpire)
        {
            $this->requested_count = 1;
            $this->firstRequestTime = $this->time;
        }

        $_SESSION['REQ_COUNT'] = $this->requested_count;
        $_SESSION['FIRST_REQUESTED_TIME'] = $this->firstRequestTime;
    }

    protected function setRequestTimeBlockHeaders()
    {
        header('X-RateLimit-Limit: '.$this->request_number);
        header('X-RateLimit-Remaining: ' . ($this->request_number-$this->requested_count));
    }

    public function checkConnectionType()
    {
        if($this->is_ajax())
        {
            if(isset($_SERVER['HTTP_ORIGIN']))
            {
                if($this->connectionType == 'https')
                {
                    $this->address = 'https://' . $_SERVER['SERVER_NAME'];
                }
                else {
                    $this->address = 'https://' . $_SERVER['SERVER_NAME'];
                }

                if(strpos($this->address, $_SERVER['HTTP_ORIGIN']) ==! 0 )
                {
                    exit(json_encode([
                        'type' => 'error',
                        'code' => 400,
                        'message' => 'Invalid ajax request!'
                    ]));
                }else
                {
                    if($this->request_timeout == 0 && $this->request_number == 0)
                    {
                        $this->createRequestHash();
                        $this->checkCookie();
                        $this->setTime();

                        $cookie = array('name' => $this->action_name, 'hash' => $this->action_hash, 'time' => $this->time);
                        setcookie('ajaxActions', json_encode($cookie));
                        $this->requestStatus = true;

                        if(isset($_COOKIE['ajaxActions']) )
                        {
                            $cookieDecoded = json_decode($_COOKIE['ajaxActions']);

                            if($cookieDecoded->hash == $this->action_hash)
                            {
                                exit(json_encode([
                                    'type' => 'error',
                                    'code' => 400,
                                    'message' => 'Invalid request!'
                                ]));
                        }
                        }
                        else {
                            $cookie = array('name' => $this->action_name, 'hash' => $this->action_hash, 'time' => $this->time);
                            setcookie('ajaxActions', json_encode($cookie));
                            $this->requestStatus = true;
                        }
                    }else
                    {
                        $this->setTime();
                        $this->initRequestTimeBlock();
                        $this->setRequestTimeBlockHeaders();

                        if($this->requested_count > $this->request_number){
                            http_response_code(429);
                            exit();
                        }
                        else {
                            $this->createRequestHash();
                            $this->checkCookie();
                            $this->setTime();

                            $cookie = array('name' => $this->action_name, 'hash' => $this->action_hash, 'time' => $this->time);
                            setcookie('ajaxActions', json_encode($cookie));
                            $this->requestStatus = true;
                        }
                    }
                }
            }
        } else
        {
            http_response_code(400);
            exit();
        }
    }

    public function getAjaxData()
    {

        if($this->requestStatus == true)
        {
            $this->requestStatus = false;
            return json_encode([$_POST]);
        }

        else {
            exit(json_encode([
                'type' => 'error',
                'code' => 400,
                'message' => 'Request data not found'
            ]));
        }
    }

    public function returnCallback($data)
    {
        if($this->debug == true)
        {
            $debugData = [
                'name' => $this->action_name,
                'hash' => $this->action_hash,
                'time' => $this->time
            ];

            $Callback = json_encode(array_merge($data, $debugData));
        }
        else {
            $Callback = json_encode($data);
        }
        print_r($Callback);
    }
}