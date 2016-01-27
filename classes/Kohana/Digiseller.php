<?php defined('SYSPATH') or die('No direct script access.'); 

class Kohana_Digiseller
{
	public $config;

	public function __construct()
    {
        $this->config = Kohana::$config->load('digiseller')->as_array();        
    }

    public static function factory(array $config = array())
    {
        return new Digiseller($config); 
    }

    private function r_b_ch($tmp, $text) 
    {
		$text = preg_replace("/".$tmp."/", "", $text);
		return $text; 
	}

	protected function request($act, $req) 
	{
		// Инициализируем сеанс CURL
		$ch = curl_init($act);
		// В выводе CURL http-заголовки не нужны
		curl_setopt($ch, CURLOPT_HEADER, 0);
		// Возвращать результат, а не выводить его в браузер
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		// Метод http-запроса - POST
		curl_setopt($ch, CURLOPT_POST, 1);
		// Что передаем?
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		// Выполняем запрос, ответ помещаем в переменную $result;
		$result = curl_exec($ch);
		if(curl_errno($ch)) echo "Curl Error number = ".curl_errno($ch).", Error desc = ".curl_error($ch)."<br />";
		curl_close($ch);
		return $result; 
	}

	// функция, формирующая запрос
	private function request_chech_code($id_seller, $unique_code, $sign) 
	{
		$request = "<?xml version=\"1.0\" encoding=\"windows-1251\"?>
			<digiseller.request>
			<id_seller>".$id_seller."</id_seller>
			<unique_code>".$unique_code."</unique_code>
			<sign>".$sign."</sign>
			</digiseller.request>";
		return $request; 
	}

	// функция, получающая ответ XML
	public function answer_check_code($id_seller, $unique_code, $sign) 
	{
		$tmp_id_seller = "[^0-9]";
		$id_seller = $this->r_b_ch($tmp_id_seller, $id_seller);
		$tmp_unique_code = "[^A-Z0-9]";
		$unique_code = substr($this->r_b_ch($tmp_unique_code, $unique_code), 0, 16);
		$tmp_sign = "[^A-Za-z0-9]";
		$id_sign = $this->r_b_ch($tmp_sign, $sign);
		$request = $this->request_chech_code($id_seller, $unique_code, $sign);
		$answer = $this->request($this->config['digi_link'], $request);

		$xml_data = new SimpleXMLElement($answer);		
		return $xml_data; 
	}

	public function sign($uniquecode = null)
	{
		return md5( $this->config['id_seller'] . ':' . $uniquecode . ':' . $this->config['password'] );
	}	
}