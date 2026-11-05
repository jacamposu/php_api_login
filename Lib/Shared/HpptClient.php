<?php

class HpptClient
{
    public $handler;
    private array $_headers;
	private object $_config;

    public function __construct(object $config)
	{	
		$this->_config = $config;
		$this->handler = curl_init();
        $this->setOptionsDefault();
	}

	public function SetUrl(string $url)
	{
		curl_setopt($this->handler, CURLOPT_URL, $url);
		return $this;
	}

	public function setOptionsDefault()
	{
		curl_setopt($this->handler, CURLOPT_HEADER, false);
		curl_setopt($this->handler, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($this->handler, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->handler, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($this->handler, CURLOPT_TIMEOUT, $this->_config->Database->timeout);
		return $this;
	}

	public function setOption($option, $value)
	{
		curl_setopt($this->handler, $option , $value);
		return $this;
	}

    public function execute()
	{
		$curlExec = curl_exec($this->handler);

		if($curlExec === false)
		{
			return curl_error($this->handler);
		}
		else
		{
			return $curlExec;
		}
	}

	public function decode($asArray=false)
	{
		return json_decode($this->execute(), $asArray ? true : false);
	}

	public function close()
	{
		curl_close($this->handler);
		return $this;
	}

    public function SetHeaders($headers)
    {
        $headers = is_object($headers) ? (array) $headers : $headers;
		
		$session = $headers['_session'];

		unset($headers['_session']);
		unset($headers['_config']);

		$authorization = new Authorization();
		$authorization->app = $session['app'];
		$authorization->token = Util::Encrypt($session['app'].date("Ymdhi"));
		$authorization->user = isset($session['user']) ? $session['user'] : "NonUser";
		$authorization->ip = isset($session['host']) ? $session['host'] : "NonHost";
		$authorization->user_lang = $session['lang'];
		$authorization->user_type = $session['type'];

		$this->_headers = [
			'Authorization: Bearer '.base64_encode(json_encode($authorization)), 
			'Content-Type: application/json'
		];
		
        foreach ($headers as $key => $value) 
        {
            if(is_array($headers[$key]))
            {
                $value = json_encode($value);
                array_push($this->_headers, "{$key}: {$value}");
            }
            else
            {
                array_push($this->_headers, "{$key}: {$value}");
            }
        }

		return $this;
    }

	public function Get():object
	{
		curl_setopt($this->handler, CURLOPT_HTTPHEADER, $this->_headers);
		curl_setopt($this->handler, CURLOPT_HTTPGET, true);
		curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, "GET");
		return $this;
	}

	public function Post($array, $headers=null)
	{
		if(!is_null($this->_headers))
		{
			curl_setopt($this->handler, CURLOPT_HTTPHEADER, $this->_headers);
		}
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, json_encode($array));
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, "POST");
		return $this;
	}

	public function Put($array, $headers=null)
	{
		if(!is_null($this->_headers))
		{
			curl_setopt($this->handler, CURLOPT_HTTPHEADER, $this->_headers);
		}
		curl_setopt($this->handler, CURLOPT_POSTFIELDS, json_encode($array));
		curl_setopt($this->handler, CURLOPT_POST, true);
		curl_setopt($this->handler, CURLOPT_CUSTOMREQUEST, "PUT");
		return $this;
	}
}