<?php namespace Omashu\Front;

use Illuminate\Support\Facades\HTML;

/**
 * Front
 */
class Front {

	protected $title = [];
	protected $description = [];
	protected $keywords = [];
	protected $custom = [];

	public function custom($tag, array $args = [])
	{
		$this->custom[] = [$tag, $args];
		return $this;
	}

	public function getTitle()
	{
		$separator = " / ";
		return HTML::entities(implode($separator, $this->title));
	}

	public function getDescription()
	{
		$description = implode('. ', $this->description).". ";
		$description = mb_substr($description, 0, 250);
		$description = preg_replace("/\s+/s", " ", $description);
		$description = trim($description);

		return HTML::entities($description);
	}

	public function getKeywords()
	{
		$temp_data = implode(', ', $keywords);

		$clean_data = mb_convert_encoding($temp_data, "UTF-8");
		$clean_data = mb_strtolower($clean_data);
		$clean_data = preg_replace("/[^а-яёa-z0-9,]/u", " ", $clean_data);
		$clean_data = preg_replace("/(\s+)/", " ", $clean_data);

		$array_keywords = explode(",", $clean_data);
		array_walk($array_keywords, function(&$value) {
			$value = trim($value);
		});

		$frequent_keywords = array();
		foreach ($array_keywords as $keyword) {
			if (isset($frequent_keywords[$keyword])) {
				$frequent_keywords[$keyword]++;
				continue;
			}

			$frequent_keywords[$keyword] = 1;
		}

		arsort($frequent_keywords);

		return implode(", ", $frequent_keywords);
	}

	public function getHtml()
	{
		$this->custom("meta", ["name" => "description", "content" => $this->getDescription()]);
		$this->custom("meta", ["name" => "keywords", "content" => $this->getKeywords()]);
		$html = "<title>".$this->getTitle()."</title>";

		foreach ($this->custom as $custom)
		{
			$html .= "<$custom[0]".HTML::attributes($custom[1])."/>";
		}

		return $html;
	}

	public function __call($method, $values)
	{
		if (!in_array($method, ["title", "description", "keywords"]))
		{
			return false;
		}

		foreach ($values as $value)
		{
			if (is_array($value))
			{
				$this->{$method}($value);
				continue;
			}

			$this->{$method}[] = $value;
		}

		return $this;
	}
}