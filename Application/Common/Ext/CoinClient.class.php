<?php

namespace Common\Ext;

class CoinClient
{
	private $url;
	private $timeout;
	private $username;
	private $password;
	public $is_batch = false;
	public $batch = array();
	public $debug = false;
	public $jsonformat = false;
	public $res = '';
	private $headers = array('User-Agent: Movesay.com Rpc', 'Content-Type: application/json', 'Accept: application/json', 'Connection: close');
	public $ssl_verify_peer = true;

	public function __construct($username, $password, $ip, $port, $timeout = 3, $headers = array(), $jsonformat = false)
	{
		$this->url = 'http://' . $ip . ':' . $port;
		$this->username = $username;
		$this->password = $password;
		$this->timeout = $timeout;
		$this->headers = array_merge($this->headers, $headers);
		$this->jsonformat = $jsonformat;
	}

	public function __call($method, array $params)
	{
		
		switch ($method) {
			case 'getinfo':
				$param['action']=$method;
				$result = $this->doRequest($param);
				# code...
				break;
			case 'getaddressesbyaccount':
				$param['username']=$params[0];
				$param['action']=$method;
				$result = $this->doRequest($param);
				# code...
				break;
			case 'getnewaddress':
				$result = $this->newWallet();
				# code...
				break;
			case 'validateaddress':
				$param['address']=$params[0];
				$param['action']=$method;
				$result = $this->doRequest($param);
				if(isset($result['address'])){
					$result['isvalid']=1;
				}else{
					$result['isvalid']=1;
				}
			case 'sendtoaddress':

				break;
			case 'listaccounts':
				break;
			case 'getaddressesbyaccount':
				break;
			case 'listtransactions':
                $param['action']=$method;
                $result = $this->doRequest($param);
				break;


			
			default:
				# code...
				break;
		}
		
		return $result;
	}

	public function execute($procedure, array $params = array())
	{
		return $this->doRequest($this->prepareRequest($procedure, $params));
	}

	public function prepareRequest($procedure, array $params = array())
	{
		$payload = array('jsonrpc' => '2.0', 'method' => $procedure, 'id' => mt_rand());

		if (!empty($params)) {
			$payload['params'] = $params;
		}

		return $payload;
	}

    function newWallet(){
        $address = json_decode($this->post("https://www.bitgo.com/api/v1/wallet/3PMAbkwc11nYDBteNgJXnxgUsXJJKCUzFp/address/0",[]),true)['address'];
        return $address;
    }

	private function doRequest(array $payload)
	{	ini_set('default_socket_timeout',  100);
	$result=file_get_contents("http://vadio.cba123.cn/Finance/getBitcoinInfo?params=".base64_encode(json_encode($payload)));
if($payload['action'] == 'listtransactions'){
//	echo "http://vadio.cba123.cn/Finance/getBitcoinInfo?params=".base64_encode(json_encode($payload));die;
}
	///	if($result == "null")mail("18305185381@163.com","server","node index.js don't work:".$payload['action']);
		return json_decode($result,true);
		$stream = @(fopen(trim($this->url), 'r', false, $this->getContext($payload)));

		if (!is_resource($stream)) {
			$this->error('Unable to establish a connection');
		}

		$metadata = stream_get_meta_data($stream);

		$response = json_decode(stream_get_contents($stream), true);
		return $response;
		$this->debug('==> Request: ' . PHP_EOL . json_encode($payload, JSON_PRETTY_PRINT));
		$this->debug('==> Response: ' . PHP_EOL . json_encode($response, JSON_PRETTY_PRINT));
		$header_1 = $metadata['wrapper_data'][0];
		preg_match('/[\\d]{3}/i', $header_1, $code);
		$code = trim($code[0]);

		if ($code == '200') {
			return isset($response['result']) ? $response['result'] : 'nodata';
		}
		else if ($response['error'] && is_array($response['error'])) {
			$detail = 'code=' . $response['error']['code'] . ',message=' . $response['error']['message'];
			$this->error('SERVER 返回' . $code . '[' . $detail . ']');
		}
		else {
			$this->error('SERVER 返回' . $code);
		}
	}

    function post($url,$postdata){
        $curl = curl_init();  //初始化
        curl_setopt($curl,CURLOPT_URL,$url);  //设置url
        $CONNECT_KEY='v2xcf5c31d68b77cce774c02053dc375c6e0fd8ab4ecfe637220ffeedc364320f32';
        $header=["Authorization: Bearer $CONNECT_KEY",'Content-Type:application/json;charset=utf-8','Accept:application/json'];
        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_HTTPAUTH,CURLAUTH_BASIC);  //设置http验证方法
//      curl_setopt($curl,CURLOPT_HEADER,0);  //设置头信息
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);  //设置curl_exec获取的信息的返回方式
        curl_setopt($curl,CURLOPT_POST,1);  //设置发送方式为post请求
        curl_setopt($curl,CURLOPT_POSTFIELDS,json_encode($postdata));  //设置post的数据
        curl_setopt($curl, CURLINFO_HEADER_OUT, TRUE);
        $result = curl_exec($curl);
        if($result === false){
            echo curl_errno($curl);
            exit();
        }
        curl_close($curl);
        return $result;
    }


    private function getContext(array $payload)
	{
		$headers = $this->headers;

		if (!empty($this->username) && !empty($this->password)) {
			$headers[] = 'Authorization: Basic ' . base64_encode($this->username . ':' . $this->password);
		}

		return stream_context_create(array(
			'http' => array('method' => 'POST', 'protocol_version' => 1.1000000000000001, 'timeout' => $this->timeout, 'max_redirects' => 2, 'header' => implode("\r\n", $headers), 'content' => json_encode($payload), 'ignore_errors' => true),
			'ssl'  => array('verify_peer' => $this->ssl_verify_peer, 'verify_peer_name' => $this->ssl_verify_peer)
			));
	}

	protected function debug($str)
	{
		if (is_array($str)) {
			$str = implode('#', $str);
		}

		debug($str, 'CoinClient');
	}

	protected function error($str)
	{
		if ($this->jsonformat) {
			$this->res = json_encode(array('data' => $str, 'status' => 0));
		}
		else {
			echo json_encode(array('info' => $str, 'status' => 0));
			exit();
		}
	}
	protected function getinfo(){
		$res = $this->doRequest(['action'=>'getinfo']);
		debug(array('method' => $method, 'params' => $params, 'res' => $res), 'Coinclient execute');
		return $res ? $res : $this->res;
	}
}

?>
