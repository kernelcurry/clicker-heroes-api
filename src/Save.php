<?php namespace KernelCurry\ClickerHeroes;

/**
 * Clicker Heroes API - A game save api for php.
 * This class has methods to create, encrypt, decrypt
 * and manipulate game play data.
 *
 * @package  Clicker Heroes API
 * @author   Michael Curry <kernelcurry@gmail.com>
 */

class Save
{
	/**
	 * @var Helper
	 */
	private $helper;

	/**
	 * @var Crypt
	 */
	private $crypt;

	/**
	 * @var mixed|\stdClass
	 */
	protected $save = null;

	/**
	 * Construct for the class.
	 */
	public function __construct()
	{
		$this->helper = new Helper;
		$this->crypt  = new Crypt;
	}

	/**
	 * This function uses a game save to populate required variables.
	 *
	 * @param string $value
	 * @return $this
	 */
	public function import($value)
	{
		$this->save = $this->crypt->decrypt($value);

		return $this;
	}

	/**
	 * Take the game save you have manipulated and export it
	 * into an encrypted save that can be used in the game.
	 *
	 * @return string
	 */
	public function export()
	{
		return $this->crypt->encrypt($this->save);
	}

	/**
	 * Getter for the current state of the save.  This could
	 * return  null or a stdClass.
	 *
	 * @return mixed|\stdClass
	 */
	public function getSave()
	{
		return $this->save;
	}

	/**
	 * Setter for the current save.  This must be a stdClass in
	 * the correct format for the game.  This function does not
	 * measure the integrity of the set value, so be careful.
	 *
	 * @param mixed|\stdClass $value
	 */
	public function setSave($value)
	{
		$this->save = $value;
	}

}