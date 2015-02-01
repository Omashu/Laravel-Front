<?php namespace Omashu\Front;

use Illuminate\Support\Facades\HTML;

/**
 * Front
 */
class Front {

	/**
	 * @var array page title
	 */
	protected $title = [];

	/**
	 * @var array page description
	 */
	protected $description = [];

	/**
	 * @var array page keywords
	 */
	protected $keywords = [];

	/**
	 * @var array page custom meta tags
	 */
	protected $custom = [];

	/**
	 * @var array js vars
	 */
	protected $js = [];

	/**
	 * @var string js var name
	 */
	protected $jsVarName = "_VALUES";

	/**
	 * Add js var
	 * @param string $name
	 * @param mixed $value
	 * 
	 * @return this
	 */
	public function js($name, $value = null)
	{
		$this->js[$name] = $value;
		return $this;
	}

	/**
	 * Add custom tag
	 * 
	 * @param string $tag tag name, meta | link
	 * @param array $args arguments ["rel" => "canonical"]
	 * @param array $params ["before"=>string, "after"=>string]
	 * @return this
	 */
	public function custom($tag, array $args = [], array $params = [])
	{
		$this->custom[] = [$tag, $args, $params];
		return $this;
	}

	/**
	 * Get page title
	 * 
	 * @return string
	 */
	public function getTitle()
	{
		return HTML::entities(implode(" / ", array_reverse($this->title)));
	}

	/**
	 * Get page description
	 * 
	 * @return string
	 */
	public function getDescription()
	{
		$description = array_reverse($this->description);
		$description = implode('. ', $description).". ";
		$description = strip_tags($description);
		$description = preg_replace("/\s+/s", " ", $description);
		$description = mb_substr($description, 0, 250);
		$description = preg_replace("/\s+/s", " ", $description);
		$description = trim($description, ". ");
		$description = trim($description);

		return HTML::entities($description);
	}

	/**
	 * Get page keywords
	 * 
	 * @return string
	 */
	public function getKeywords()
	{
		$temp_data = implode(', ', $this->keywords);

		$clean_data = mb_convert_encoding($temp_data, "UTF-8");
		$clean_data = mb_strtolower($clean_data);
		$clean_data = preg_replace("/[^а-яёa-z0-9,]/u", " ", $clean_data);
		$clean_data = preg_replace("/(\s+)/", " ", $clean_data);

		$array_keywords = explode(",", $clean_data);
		foreach ($array_keywords as $key => $value)
		{
			if (empty($value))
			{
				unset($array_keywords[$key]);
				continue;
			}

			$array_keywords[$key] = trim($array_keywords[$key]);
		}

		$frequent_keywords = array();
		foreach ($array_keywords as $keyword) {
			if (isset($frequent_keywords[$keyword])) {
				$frequent_keywords[$keyword]++;
				continue;
			}

			$frequent_keywords[$keyword] = 1;
		}

		arsort($frequent_keywords);

		return HTML::entities(implode(", ", array_keys($frequent_keywords)));
	}

	/**
	 * Get custom meta tags
	 * 
	 * @return array
	 */
	public function getCustom()
	{
		return $this->custom;
	}

	/**
	 * Get js vars
	 * @return array
	 */
	public function getJs()
	{
		return $this->js;
	}

	/**
	 * Generate and return meta tags
	 * 
	 * @return string
	 */
	public function getHtml()
	{
		$html = '<script type="text/javascript">var '.$this->jsVarName.' = '.json_encode($this->js).'</script>';
		$html .= "\n<title>".$this->getTitle()."</title>";
		$html .= "\n<meta".HTML::attributes(["name" => "description", "content" => $this->getDescription()])."/>";
		$html .= "\n<meta".HTML::attributes(["name" => "keywords", "content" => $this->getKeywords()])."/>";

		foreach ($this->custom as $custom)
		{
			$html .= array_get($custom[2], "before")
				. "\n<$custom[0]".HTML::attributes($custom[1])."/>"
				. (array_get($custom[2], "close") ? "</$custom[0]>" : "")
				. array_get($custom[2], "after");
		}

		return $html;
	}

	public function __call($method, $values)
	{
		if (!in_array($method, ["title", "description", "keywords"]))
		{
			return $this;
		}

		foreach ($values as $value)
		{
			if (is_array($value))
			{
				$this->__call($method, $value);
				continue;
			}

			$this->{$method}[] = $value;
		}

		return $this;
	}
}