<?php
class Request {
	/** @const cookie file*/
	const COOKIE_FILE = '/cache/cookie';
	/** @const user agent*/
	const USER_AGENT = 'Mozilla/5.0 (X11; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0';
	/** @var array Массив возможных HTTP статус кодов */
	private $_codes = array(0=>'Domain Not Found',
	               100=>'Continue',
	               101=>'Switching Protocols',
	               200=>'OK',
	               201=>'Created',
	               202=>'Accepted',
	               203=>'Non-Authoritative Information',
	               204=>'No Content',
	               205=>'Reset Content',
	               206=>'Partial Content',
	               300=>'Multiple Choices',
	               301=>'Moved Permanently',
	               302=>'Found',
	               303=>'See Other',
	               304=>'Not Modified',
	               305=>'Use Proxy',
	               307=>'Temporary Redirect',
	               400=>'Bad Request',
	               401=>'Unauthorized',
	               402=>'Payment Required',
	               403=>'Forbidden',
	               404=>'Not Found',
	               405=>'Method Not Allowed',
	               406=>'Not Acceptable',
	               407=>'Proxy Authentication Required',
	               408=>'Request Timeout',
	               409=>'Conflict',
	               410=>'Gone',
	               411=>'Length Required',
	               412=>'Precondition Failed',
	               413=>'Request Entity Too Large',
	               414=>'Request-URI Too Long',
	               415=>'Unsupported Media Type',
	               416=>'Requested Range Not Satisfiable',
	               417=>'Expectation Failed',
	               500=>'Internal Server Error',
	               501=>'Not Implemented',
	               502=>'Bad Gateway',
	               503=>'Service Unavailable',
	               504=>'Gateway Timeout',
	               505=>'HTTP Version Not Supported');
	/**
	 * @desc  Запрос на сервер
	 * @param string $url
	 * @param array  $args
	 * @param string $referer = ''
	 * @param curl &$process = null
	 * @param is_xhr = false
	 * @param string $userAgent = '' (default see const USER_AGENT)
	 * @return stdClass {responseText, responseStatus. responseStatusText}
	 **/
	public function execute($url, $args = array(), $referer = '', &$process = null, $close_connection = true, $is_xhr = false, $userAgent = '') {
		if (!$process) {
			$process = curl_init($url);
		} else {
			curl_setopt($process, CURLOPT_URL, $url); 
		}
		curl_setopt($process, CURLOPT_HEADER, 0);
		if(count($args) > 0) {
			curl_setopt($process, CURLOPT_POST, 1);
			curl_setopt($process, CURLOPT_POSTFIELDS, $args);
		}
		
		$headers = array (
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: ru-RU,ru;q=0.8,en-US;q=0.5,en;q=0.3',
			//'Accept-Encoding: gzip, deflate',
			'Content-Type: application/x-www-form-urlencoded'
		);
		if ($is_xhr) {
			$headers[] = 'X-Requested-With: XMLHttpRequest';
		}
		curl_setopt($process, CURLOPT_HTTPHEADER, $headers); 
		
		if ($referer) {
	    	curl_setopt($process, CURLOPT_REFERER, $referer);
		}
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false); 
		curl_setopt($process, CURLOPT_COOKIEFILE, dirname(__FILE__) . self::COOKIE_FILE);
		curl_setopt($process, CURLOPT_COOKIEJAR, dirname(__FILE__) . self::COOKIE_FILE);
		if (!$userAgent) {
			$userAgent = self::USER_AGENT;
		}
		curl_setopt($process, CURLOPT_USERAGENT, $userAgent);
		//@curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		$response = curl_exec($process);
		$httpCode = curl_getinfo($process, CURLINFO_HTTP_CODE);
		if ($close_connection) {
			curl_close($process);
		}
		$obj = new StdClass();
		$obj->responseText = $response;
		$obj->responseStatus = $httpCode;
		$obj->responseStatusText = $this->_codes[$httpCode];
		return $obj;
	}
}
