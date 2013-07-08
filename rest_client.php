<?php

class REST
{
	private $url;

	public function __construct($url) {
		$this->url = $url;
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
		$opts = array(
			'http '=> array(
				'method' => $method,
				'header' => 'Content-type: application/x-www-form-urlencoded',
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
		if(($stream = fopen($this->url, 'r', false, $context)) !== false) {
			$content = stream_get_contents($stream);
			$header = stream_get_meta_data($stream);
			fclose($stream);
			return array('content' => $content, 'header' => $header);
		} else {
			return false;
		}
	}
}

$url = $_POST['url']; unset($_POST['url']);
$method = $_POST['method']; unset($_POST['method']);

$rest = new REST($url);
$data = $rest->$method($_POST);
echo $data['content'];
