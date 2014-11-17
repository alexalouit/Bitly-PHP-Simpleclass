<?php
/**
 * Simple Bit.ly PHP-API
 * @author: Alexandre Alouit <alexandre.alouit@gmail.com>
 */

class bitly {

	/**
	 * Timeout for complete connection in seconds
	 * @type: int
	 */
	private $timeout = 6;

	/**
	 * Timeout for first byte connection in seconds
	 * @type: int
	 */
	private $connectionTimeout = 3;

	/**
	 * Delay when we have many links in micro secondes
	 * @type: int
	 */
	private $delay = 100000;

	private $server = "http://api.bitly.com/";
	private $login = NULL;
	private $api_key = NULL;
	public $content = "";
	public $link = "";

	/**
	 * @params: login (string), api_key (string)
	 */
	public function __construct($login, $api_key) {
		if(!function_exists('json_decode')) {
			die("PECL json required.");
		}
		if(!function_exists('curl_init')) {
			die("PHP-Curl required.");
		}

		$this->login = $login;
		$this->api_key = $api_key;
	}

	/**
	 * Shorten for content (with multiple links)
	 * Replace all links in current content
	 * @params: url (string)
	 * @return: url shortening (string)
	 */
	public function content($data) {
		foreach($this->search($data) as $key => $toReplace) {
			$byReplace = $this->link($toReplace);
			$data = $this->replace($data, $toReplace, $byReplace);

			if(!is_null($this->delay)) {
				usleep($this->delay);
			}
		}

		$this->content = $data;
		return $this->content;
	}

	/**
	 * Shortener by link
	 * @params: url (string)
	 * @return: url shortening (string)
	 */
	public function link($inputUrl) {
		$buffer = curl_init();
		curl_setopt($buffer, CURLOPT_URL, $this->server . 'v3/shorten?login=' . $this->login . '&apiKey=' . $this->api_key . '&uri=' . urlencode($inputUrl) . '&format=json');
		curl_setopt($buffer, CURLOPT_CONNECTTIMEOUT, $this->connectionTimeout);
		curl_setopt($buffer, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($buffer, CURLOPT_HEADER, 0);
		curl_setopt($buffer, CURLOPT_RETURNTRANSFER, 1);

		$data = curl_exec($buffer);
		curl_close($buffer);

		$data = json_decode($data);

		$this->verify($data);
		$this->link = $data->data->url;
		return $this->link;
	}

	/**
	 * Check return request is valid and job is done
	 */
	private function verify($data) {
		if(($data->status_txt != "OK" && $data->status_code != 200) OR ($data->status_txt != "LINK_ALREADY_EXISTS" && $data->status_code != 304)) {
			return FALSE;
		}
	}

	/**
	 * Search link in content
	 * @params: content (string)
	 * @return: founds (array)
	 */
	private function search($data) {
		preg_match_all("_(^|[\s.:;?\-\]<\(])(https?://[-\w;/?:@&=+$\|\_.!~*\|'()\[\]%#,☺]+[\w/#](\(\))?)(?=$|[\s',\|\(\).:;?\-\[\]>\)])_i", $data, $return);
		return array_map('trim', $return[0]);
	}

	/**
	 * Replace link in content
	 * @params: content (string), toReplace (array), byReplace (array)
	 * @return: content (sring)
	 */
	private function replace($content, $toReplace, $byReplace) {
		return str_replace($toReplace, $byReplace, $content);
	}

}
?>
