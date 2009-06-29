<?php
/**
 * i18n package
 * 
 * @author Maxime Raoust
 */
 
/**
 * TODO Modify the lang engine to behave like gettext : you write your app in
 * english, then your parse your files with a dedicated tool which create the
 * indexes and xml files for translation
 */ 
  
/**
 * i18n core class
 * 
 * You shouldn't have to do anything with it. Use "__()" & "__date()" instead
 */
class LangEngine
{
	protected $currentLang = "en";
	public $dico;
	protected static $instance;
	
	/**
	 * Get the translation of the given text ID
	 */
	public static function getTranslation($textId, $lang=null)
	{
		$instance = self::getInstance();
		return $instance->getTranslationPrivate($textId, $instance->currentLang);
	}
		
	public static function getInstance($toolMode = false)
	{
		if (!self::$instance)
		{
			$class = __CLASS__;
			self::$instance = new $class($toolMode);
		}
		return self::$instance;
	}

	protected function __construct($toolMode = false)
	{
		$session = SessionEngine::getInstance();
		$this->currentLang = $session->get("lang", "en");
		if($dico = $session->get(__CLASS__))
		{
			$this->dico = unserialize(rawurldecode($dico));
		}
		else
		{
			$this->loadDicoRecursive(APP_LANGS_PATH);
			$session->set(__CLASS__, rawurlencode(serialize($this->dico)));
		}
	}
	
	/**
	 * Recursive loading method
	 */
	protected function loadDicoRecursive($directoryPath)
	{
		if ($handle = opendir($directoryPath))
		{
			while (false !== ($file = readdir($handle)))
			{
				if(substr($file, 0, 1)==".")
				{
					continue;
				}
				if(is_dir($directoryPath.$file))
				{
					$this->loadDicoRecursive($directoryPath.$file);
				}
				elseif(substr($file, -4)==".xml")
				{
					$this->parseLangFile($directoryPath."/".$file);
				}
			}
			closedir($handle);
		}
	}
	
	/**
	 * XML parsing file. DOM is used here
	 */
	protected function parseLangFile($file)
	{
		/**
		 * Dico structure :
		 * <dico>
		 *    <language id="en">
		 *       <word>My word</word>
		 *    </language>
		 * </dico>
		 */
	
		$dom = new DomDocument;
		$dom->load($file);
		$languages = $dom->getElementsByTagName("language");
		foreach($languages as $language)
		{
			if($language->hasAttribute("id"))
			{
				$lang = $language->getAttribute("id");
				foreach($language->childNodes as $word)
				{
					if($word->nodeType == XML_ELEMENT_NODE)
					{
						$this->dico[$lang][$word->nodeName] = (string) $word->nodeValue;
					}
				}
			}
		}
	}
	
	/**
	 * Get the transaltion of the given text ID
	 */
	protected function getTranslationPrivate($textId, $lang="en")
	{
		if(isset($this->dico[$lang][$textId]))
		{
			return $this->dico[$lang][$textId];
		}	
		elseif(isset($this->dico["en"][$textId]))
		{
			return $this->dico["en"][$textId];
		}
		else
		{
			return $textId;
		}
	}

}

?>