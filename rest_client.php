<?php

class REST
{
	private $url;
	private $user;
	private $password;

	public function __construct($url, $user = null, $password = null) {
		$this->url = $url;
		$this->user = $user;
		$this->password = $password;
	}

	public function get($params = array())
	{
		return $this->push($this->context('GET'));
	}

	public function post($params = array())
	{
		return $this->push($this->context('POST', $params));
	}

	public function put($params = array())
	{
		return $this->push($this->context('PUT', $params));
	}

	public function delete($params = array())
	{
		return $this->push($this->context('DELETE', $params));
	}

	protected function context($method, $params = null)
	{
		$auth = '';
		if(!is_null($this->user) && !is_null($this->password)) {
			$auth = sprintf("\r\nAuthorization: Basic %s", base64_encode($this->user.':'.$this->password));
		}

		$opts = array(
			'http '=> array(
				'method' => $method,
				'header' => 'Content-type: application/x-www-form-urlencoded' . $auth
			)
		);

		if ($params !== null){
			 if (is_array($params)){
					$params = http_build_query($params);
			 }
			 $opts['http']['content'] = $params; 
		}
		return stream_context_create($opts);
	}

	protected function push($context)
	{
		if(($stream = file_get_contents($this->url, false, $context)) !== false) {
			/*$content = stream_get_contents($stream);
			$header = stream_get_meta_data($stream);
			fclose($stream);*/
			//return array('content' => $content, 'header' => $header);
			return array('content' => $stream);
		} else {
			return false;
		}
	}
}

$url = $_POST['url']; unset($_POST['url']);
$method = $_POST['method']; unset($_POST['method']);
$user = null; $password = null;
if(isset($_POST['auth_user'])) $user = $_POST['auth_user']; unset($_POST['auth_user']);
if(isset($_POST['auth_password'])) $password = $_POST['auth_password']; unset($_POST['auth_password']);

$rest = new REST($url, $user, $password);
$data = $rest->$method($_POST);
echo $data['content'];
