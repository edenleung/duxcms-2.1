<?php

namespace app\api\controller;

/**
 * Json Response.
 */
class Response
{
    protected $contentType = 'application/json';

    protected $result = [];

    protected $message = '';

    protected $code = '';

    public function error($message = 'error', $result = [])
    {
        $this->message = $message;
        $this->result = $result;
        $this->code = 500;

        return $this->send();
    }

    public function success($result = [], $message = 'ok')
    {
        $this->message = $message;
        $this->result = $result;
        $this->code = 200;

        return $this->send();
    }

    protected function send()
    {
        header('Content-Type: '.$this->contentType);

        echo json_encode(
            [
                'code'     => $this->code,
                'message'  => $this->message,
                'result'   => !empty($this->result) ? $this->result : null,
            ],
            JSON_UNESCAPED_UNICODE
        );

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }
}
