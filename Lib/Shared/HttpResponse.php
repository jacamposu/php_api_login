<?php

class HttpResponse
{
    private ResponseModel $response;

    public function __construct()
    {
        $this->response = new ResponseModel();
    }

    #region public methods

    public function Response(int $httpCode=400, mixed $data=null):void
    {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, token");
        header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
        header("Allow: GET, POST, PUT, DELETE, OPTIONS");
        header('Content-Type: application/json');

        // Manejo especial para preflight
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit; // No enviar JSON, solo OK
        }

        http_response_code($httpCode);
        $this->SetResponse($data, $httpCode);
        die(json_encode($this->response));
    }


    public function VerifyMethod(string $method):void
    {
        if($_SERVER['REQUEST_METHOD'] != $method)
        {
            throw new Exception("Method not allowed", 405);
        }
    }

    #endregion

    #region private methods
    
    private function SetResponse(mixed $data=null, int $code=400):void
    {
        $this->response->description = $this->DescriptionErrorCode($code);
        $this->response->statusCode = $code;
        $successCodes = [100,101,102,103,200,201,202,203,204,205,206,207,208,226,300,301,302,303,304,307,308];
        if(in_array($code, $successCodes))
            $this->response->response = $data;
        else
            $this->response->error = $data;
    }   


    private function DescriptionErrorCode(int $code):string
    {
        $arrayCodes = [
            100 => 'CONTINUE',
            101 => 'SWITCHING PROTOCOLS',
            102 => 'PROCESSING',
            103 => 'EARLY HINTS',
            200 => 'OK',
            201 => 'CREATED',
            202 => 'ACCEPTED',
            203 => 'NON-AUTHORITATIVE INFORMATION',
            204 => 'NO CONTENT',
            205 => 'RESET CONTENT',
            206 => 'PARTIAL CONTENT',
            207 => 'MULTI-STATUS',
            208 => 'ALREADY REPORTED',
            226 => 'IM USED',
            300 => 'MULTIPLE CHOICES',
            301 => 'MOVED PERMANENTLY',
            302 => 'FOUND',
            303 => 'SEE OTHER',
            304 => 'NOT MODIFIED',
            307 => 'TEMPORARY REDIRECT',
            308 => 'PERMANENT REDIRECT',
            400 => 'BAD REQUEST',
            401 => 'UNAUTHORIZED',
            402 => 'PAYMENT REQUIRED',
            403 => 'FORBIDDEN',
            404 => 'NOT FOUND',
            405 => 'METHOD NOT ALLOWED',
            406 => 'NOT ACCEPTABLE',
            407 => 'PROXY AUTHENTICATION REQUIRED',
            408 => 'REQUEST TIMEOUT',
            409 => 'CONFLICT',
            410 => 'GONE',
            411 => 'LENGTH REQUIRED',
            412 => 'PRECONDITION FAILED',
            413 => 'PAYLOAD TOO LARGE',
            414 => 'URI TOO LONG',
            415 => 'UNSUPPORTED MEDIA TYPE',
            416 => 'RANGE NOT SATISFIABLE',
            417 => 'EXPECTATION FAILED',
            418 => 'IM A TEAPOT',
            421 => 'MISDIRECTET REQUEST',
            422 => 'UNPROCESSABLE CONTENT',
            423 => 'LOCKED',
            424 => 'FAILED DEPENDENCY',
            425 => 'TOO EARLY',
            426 => 'UPGRADE REQUIRED',
            427 => 'UPGRADE REQUIRED',
            428 => 'PRECONDITION REQUIRED',
            429 => 'TOO MAY REQUEST',
            431 => 'REQUEST HEADER FIELDS TOO LARGE',
            451 => 'UNAVAILABLE FOR LEGAL REASONS',
            500 => 'INTERNAL SERVER ERROR',
            501 => 'NOT IMPLEMENTED',
            502 => 'BAD GATEWAY',
            503 => 'SERVICE UNAVAILABLE',
            504 => 'GATEWAY TIMEOUT',
            505 => 'HTTP VERSION NOT SUPPORTED',
            506 => 'VARIANT ALSO NEGOTIATES',
            507 => 'INSUFFICIENT STORAGE',
            508 => 'LOOP DETECTED',
            510 => 'NOT EXTENDED',
            511 => 'NETWORK AUTHENTICATION REQUIRED'
        ];
        
        return isset($arrayCodes[$code]) ? $arrayCodes[$code] : '';
    }

    #endregion
}