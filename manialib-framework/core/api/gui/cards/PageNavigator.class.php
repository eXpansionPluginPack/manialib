<?php
/**
 * Manialink GUI API
 * @author Maxime Raoust
 */

/**
 * Page Navigator
 * @package gui_api
 */
class PageNavigator
{
	public $arrowNext;
	public $arrowPrev;
	public $arrowFastNext;
	public $arrowFastPrev;
	public $arrowLast;
	public $arrowFirst;
	public $text;

	public $arrowNoneStyle = GUI_PAGE_NAVIGATOR_ARROW_NONE_SUBSTYLE;
	public $arrowNextStyle = GUI_PAGE_NAVIGATOR_ARROW_NEXT_SUBSTYLE;
	public $arrowPrevStyle = GUI_PAGE_NAVIGATOR_ARROW_PREV_SUBSTYLE;
	public $arrowFastNextStyle = GUI_PAGE_NAVIGATOR_ARROW_FAST_NEXT_SUBSTYLE;
	public $arrowFastPrevStyle = GUI_PAGE_NAVIGATOR_ARROW_FAST_PREV_SUBSTYLE;
	public $arrowFirstStyle = GUI_PAGE_NAVIGATOR_ARROW_FIRST_SUBSTYLE;
	public $arrowLastStyle = GUI_PAGE_NAVIGATOR_ARROW_LAST_SUBSTYLE;

	protected $showLast;
	protected $showFastNext;
	protected $showText;
	protected $pageNumber;
	protected $pageIndex;
	protected $posX;
	protected $posY;
	protected $posZ;

	function __construct($iconSize = 5)
	{
		$this->arrowNext = new Icon64($iconSize);
		$this->arrowPrev = new Icon64($iconSize);
		$this->arrowFastNext = new Icon64($iconSize);
		$this->arrowFastPrev = new Icon64($iconSize);
		$this->arrowLast = new Icon64($iconSize);
		$this->arrowFirst = new Icon64($iconSize);
		$this->text = new Label(5);
		
		$this->showLast = true;
		$this->showFastNext = false;
		$this->showText = true;
		$this->pageIndex = 1;
		$this->pageNumber = 2;
		$this->output = "";
		
		$this->arrowNext->setSubStyle($this->arrowNoneStyle);
		$this->arrowPrev->setSubStyle($this->arrowNoneStyle);
		$this->arrowFastNext->setSubStyle($this->arrowNoneStyle);
		$this->arrowFastPrev->setSubStyle($this->arrowNoneStyle);
		$this->arrowLast->setSubStyle($this->arrowNoneStyle);
		$this->arrowFirst->setSubStyle($this->arrowNoneStyle);
	}

	function setIconSize($iconSize)
	{
		$this->arrowNext->setSize($iconSize, $iconSize);
		$this->arrowPrev->setSize($iconSize, $iconSize);
		$this->arrowFastNext->setSize($iconSize, $iconSize);
		$this->arrowFastPrev->setSize($iconSize, $iconSize);
		$this->arrowLast->setSize($iconSize, $iconSize);
		$this->arrowFirst->setSize($iconSize, $iconSize);
	}

	function setPositionX($plop)
	{
		$this->posX = $plop;
	}

	function setPositionY($plop)
	{
		$this->posY = $plop;
	}

	function setPositionZ($plop)
	{
		$this->posZ = $plop;
	}

	function setPosition($px = 0, $py = 0, $pz = 0)
	{
		$this->setPositionX($px);
		$this->setPositionY($py);
		$this->setPositionZ($pz);
	}

	function hideLast()
	{
		$this->showLast = false;
	}

	function showLast()
	{
		$this->showLast = true;
	}

	function isLastShown()
	{
		return $this->showLast;
	}
	
	function isFastNextShown()
	{
		return $this->showFastNext;
	}

	function showFastNext($show = true)
	{
		$this->showFastNext = $show;
	}

	function hideText()
	{
		$this->showText = false;
	}

	function setPageNumber($plop)
	{
		$this->pageNumber = $plop;
	}

	function setPageIndex($plop)
	{
		$this->pageIndex = $plop;
	}

	function save()
	{
		// Show / hide text
		if(!$this->pageIndex || !$this->pageNumber)
		{
			$this->hideText();
			$this->hideLast();
		}
		
		// Arrow styles
		if($this->arrowNext->hasLink()) $this->arrowNext->setSubStyle($this->arrowNextStyle);
		if($this->arrowPrev->hasLink()) $this->arrowPrev->setSubStyle($this->arrowPrevStyle);
		if($this->arrowFastNext->hasLink()) $this->arrowFastNext->setSubStyle($this->arrowFastNextStyle);
		if($this->arrowFastPrev->hasLink()) $this->arrowFastPrev->setSubStyle($this->arrowFastPrevStyle);
		if($this->arrowLast->hasLink()) $this->arrowLast->setSubStyle($this->arrowLastStyle);
		if($this->arrowFirst->hasLink()) $this->arrowFirst->setSubStyle($this->arrowFirstStyle);

		// Text
		$this->text->setStyle("TextStaticSmall");
		$this->text->setText($this->pageIndex . "/" . $this->pageNumber);

		// Positioning in relation to the center of the containing frame
		$this->text->setAlign("center", "center");
		$this->text->setPositionZ(1);

		$this->arrowNext->setValign("center");
		$this->arrowFastNext->setValign("center");
		$this->arrowLast->setValign("center");

		$this->arrowNext->setPosition(($this->text->getSizeX() / 2) + 1, 0, 1);
		$this->arrowFastNext->setPosition($this->arrowNext->getPosX() + $this->arrowNext->getSizeX(), 0, 1);
		$this->arrowLast->setPosition(
			$this->arrowNext->getPosX() + 
			(int)$this->showFastNext*$this->arrowFastNext->getSizeX() + 
			$this->arrowNext->getSizeX(), 
			0, 1);

		$this->arrowPrev->setAlign("right", "center");
		$this->arrowFastPrev->setAlign("right", "center");
		$this->arrowFirst->setAlign("right", "center");

		$this->arrowPrev->setPosition(-($this->text->getSizeX()/2) - 1, 0, 1);
		$this->arrowFastPrev->setPosition($this->arrowPrev->getPosX() - $this->arrowPrev->getSizeX(), 0, 1);
		$this->arrowFirst->setPosition(
			$this->arrowPrev->getPosX() -
			(int)$this->showFastNext*$this->arrowFastPrev->getSizeX() - 
			$this->arrowPrev->getSizeX(),
			0, 1);

		// Save the gui
		Manialink::beginFrame($this->posX, $this->posY, $this->posZ);

			if ($this->showText)
			{
				$this->text->save();
			}
			
			$this->arrowNext->save();
			$this->arrowPrev->save();
			
			if ($this->showLast)
			{
				$this->arrowFirst->save();
				$this->arrowLast->save();
			}
			
			if ($this->showFastNext)
			{
				$this->arrowFastNext->save();
				$this->arrowFastPrev->save();
			}

		Manialink::endFrame();
	}
}
?>
