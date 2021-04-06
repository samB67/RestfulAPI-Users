<?php

class Response{

    private $success;
    private $httpStatusCode;
    private $messages = array();
    private $data;
    private $toCache = false;
    private $responseData = array();

    // set success
    public function setSuccess(bool $success){
        $this->success = $success;
    }

    // set status code...
    public function setHttpStatusCode(int $httpStatusCode){
        $this->httpStatusCode = $httpStatusCode;
    }

    // set user message...
    public function addMessage(string $message){
        $this->messages[] = $message;
    }

    // set data...
    public function setData(array $data){
        $this->data = $data;
    }

    // set a cache...
    public function toCache($toCache){
        $this->toCache = $toCache;
    }

    // return response to user...
    public function send(){
        header('Content-type: application/json;charset=utf-8');

        if($this->toCache == true){
            header('Cache-control: max-age=60');
        } else {
            header('Cache-control: no-cache, no-store');
        }

        if (($this->success !== false && $this->success !== true) || !is_numeric($this->httpStatusCode)){
            http_response_code(500);

            $this->responseData['statusCode'] = 500;
            $this->responseData['success'] = false;
            $this->addMessage("Response creation error");
            $this->responseData['messages'] = $this->messages;
        } else {
            http_response_code($this->httpStatusCode);
            $this->responseData['statusCode'] = $this->httpStatusCode;
            $this->responseData['success'] = $this->success;
            $this->responseData['messages'] = $this->messages;
            $this->responseData['data'] = $this->data;
        }

        echo json_encode($this->responseData) . PHP_EOL;
    }
}