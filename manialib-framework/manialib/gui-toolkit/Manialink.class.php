<?php
/**
 * @package Manialib
 * @author Maxime Raoust
 */

require_once( APP_FRAMEWORK_GUI_TOOLKIT_PATH.'standard.php' );

/**
 * Manialink GUI toolkit main class
 * 
 * @package Manialib
 */
abstract class Manialink
{
	public static $domDocument;
	public static $parentNodes;
	public static $parentLayouts;
	
	/**
	 * Loads the Manialink GUI toolkit. This should be called before doing
	 * anything with the toolkit.
	 * 
	 * @param bool Whether you want to create the root "<manialink>" element in
	 * the XML
	 * @param int The timeout value in seconds. Use 0 if you have dynamic pages
	 * to avoid caching
	 */
	final public static function load($createManialinkElement = true, $timeoutValue=0)
	{
		self::$domDocument = new DOMDocument;
		self::$parentNodes = array();
		self::$parentLayouts = array();
		
		if($createManialinkElement)
		{
			$manialink = self::$domDocument->createElement('manialink');
			self::$domDocument->appendChild($manialink);
			self::$parentNodes[] = $manialink;
			
			$timeout = self::$domDocument->createElement('timeout');
			$manialink->appendChild($timeout); 
			$timeout->nodeValue = $timeoutValue;
		}
	}
	
	/**
	 * Renders the Manialink
	 * 
	 * @param boolean Wehther you want to return the XML instead of printing it
	 */	
	final public static function render($return = false)
	{
		if($return)
		{
			return self::$domDocument->saveXML();
		}
		else
		{
			header('Content-Type: text/xml; charset=utf-8');
			echo self::$domDocument->saveXML();
		}
	}
	
	/**
	 * Creates a new Manialink frame, with an optionnal associated layout
	 * 
	 * @param float X position
	 * @param float Y position
	 * @param float Z position
	 * @param AbstractLayout The optionnal layout associated with the frame. If
	 * you pass a layout object, all the items inside the frame will be
	 * positionned using constraints defined by the layout
	 */
	final public static function beginFrame($x=0, $y=0, $z=0, 
		AbstractLayout $layout=null)
	{
		// Update parent layout
		$parentLayout = end(self::$parentLayouts);
		if($parentLayout instanceof AbstractLayout)
		{
			// If we have a current layout, we have a container size to deal with
			if($layout instanceof AbstractLayout)
			{
				$ui = new Spacer($layout->getSizeX(), $layout->getSizeY());
				$ui->setPosition($x, $y, $z);
				
				$parentLayout->preFilter($ui);
				$x += $parentLayout->xIndex;
				$y += $parentLayout->yIndex;
				$z += $parentLayout->zIndex;
				$parentLayout->postFilter($ui);
			}
		}
		
		// Create DOM element
		$frame = self::$domDocument->createElement('frame');
		if($x || $y || $z)
		{ 
			$frame->setAttribute('posn', $x.' '.$y.' '.$z);
		}
		end(self::$parentNodes)->appendChild($frame);
		
		// Update stacks
		self::$parentNodes[] = $frame;
		self::$parentLayouts[] = $layout;
	}
	
	/**
	 * Closes the current Manialink frame
	 */
	final public static function endFrame()
	{
		if(!end(self::$parentNodes)->hasChildNodes())
		{
			end(self::$parentNodes)->nodeValue = ' ';
		}
		array_pop(self::$parentNodes);
		array_pop(self::$parentLayouts);
	}
}

?>