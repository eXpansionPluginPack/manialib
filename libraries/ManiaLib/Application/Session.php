<?php
/**
 * @author Maxime Raoust
 * @copyright 2009-2010 NADEO 
 */
 
/**
 * PHP Session handling simplified
 */
final class ManiaLib_Application_Session
{
	/**
	 * Session identifier name. Used as parameter name for transporting the
	 * session Id in the URL when the client doesn't support cookies.
	 * @ignore
	 * 
	 */
	const SIDName = 'maniasid';
	/**
	 * @ignore
	 */
	protected static $instance;
	protected static $started = false;
	

	/**
	 * Gets the instance
	 * @return ManiaLib_Application_Session
	 */
	public static function getInstance()
	{
		if (!self::$instance)
		{
			if(!ManiaLib_Config_Loader::$config->session->enabled)
			{
				throw new Exception(
					'Cannot instanciate session: session handling has been disabled in the config');
			}
			$class = __CLASS__;
			self::$instance = new $class;
		}
		return self::$instance;
	}
	
	/**
	 * @ignore
	 */
	protected function __construct()
	{
		if(!self::$started)
		{
			try 
			{
				session_name(self::SIDName);
				session_start();
				self::$started = true;
				//ManiaLib_Log_Logger::info('Session started');
			}
			catch(Exception $exception)
			{
				ManiaLib_Log_Logger::error($exception->getMessage());
			}
		}
	}

	/**
	 * Sets a session var
	 * @param string
	 * @param mixed
	 */
	function set($name, $value = null)
	{
		$_SESSION[$name] = $value;
	}

	/**
	 * Deletes a session var
	 * @param string
	 */
	function delete($name)
	{
		unset($_SESSION[$name]);
	}

	/**
	 * Gets a session var, or the default value if nothing was found
	 * @param string The name of the variable
	 * @param mixed The default value
	 * @return mixed
	 */
	function get($name, $default = null)
	{
		return array_key_exists($name, $_SESSION) ? $_SESSION[$name] : $default;
	}
	
	/**
	 * Gets a session var, throws an exception if not found
	 * @param string The name of the variable
	 * @return mixed
	 */
	function getStrict($name)
	{
		if(array_key_exists($name, $_SESSION))
		{
			return $_SESSION[$name];
		}
		throw new SessionException('Session variable "'.$name.'" not found');
	}

	/**
	 * Checks if the specified session var exists
	 * @return boolean
	 */
	function exists($name)
	{
		return array_key_exists($name, $_SESSION);
	}
}

/**
 * @package ManiaLib
 * @ignore
 */
class SessionException extends Exception {}

?>