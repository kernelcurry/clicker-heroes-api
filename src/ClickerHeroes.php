<?php namespace KernelCurry\ClickerHeroes;

/**
 * Clicker Heroes API - A game save api for php.
 * This class has methods to create, encrypt, decrypt
 * and manipulate game play data.
 *
 * @package  Clicker Heroes API
 * @author   Michael Curry <kernelcurry@gmail.com>
 */

class Save {

	/**
	 * Known salts
	 * @var array
	 */
	private $salts = [
		'af0ik392jrmt0nsfdghy0'
	];

	/**
	 * Salt that is found to work in with the imported save
	 * @var mixed
	 */
	private $salt_used = null;

	/**
	 * Encrypted (imported) save.
	 * @var mixed
	 */
	protected $save_encrypted = null;

	/**
	 * Decrypted (array) save.
	 * @var mixed
	 */
	protected $save_decrypted = null;

	/**
	 * Anti-cheat delimiter that is placed between the game
	 * data and the hack check.
	 * @var mixed
	 */
	protected $delimiter = null;

	/**
	 * If this variable is not empty, something went wrong.
	 * @var array
	 */
	protected $errors = [];

	/**
	 * Construct for the class.
	 */
	public function __construst()
	{
		// Currently not in use
	}

	/**
	 * This function uses a game save to populate required variables.
	 *
	 * @param string $value
	 * @return ClickerHeroes $this
	 */
	public function importSave($value)
	{
		$this->save_encrypted = $value;
		$this->getDelimiter();
		if ( ! $this->decryptSave())
		{
			$this->errors[] = 'importSave() | decrypting game save encountered a problem.';
		}

		return $this;
	}

	/**
	 * Take the game save you have manipulated and
	 * export it into an encrypted save that can be used in the game.
	 *
	 * @return string
	 */
	public function exportSave()
	{
		$new = base64_encode(json_encode($this->save_decrypted));
		$hash = md5($new . $this->salt_used);
		$new_save = '';

		for ($i = 0; $i < strlen($new); $i++)
		{
			$new_save .= $new[$i].$this->randomCharacter();
		}
		$new_save .= $this->delimiter;
		$new_save .= $hash;

		return $new_save;
	}

	protected function decryptSave()
	{
		$result = explode($this->delimiter,$this->save_encrypted);

		foreach ($this->salts as $salt)
		{
			$check = '';

			for ($i = 0; $i < strlen($result[0]); $i += 2)
			{
				$check .= $result[0][$i];
			}

			$hash = md5($check . $salt);
			if ( $hash == $result[1]) {
				$this->salt_used = $salt;
				$this->save_decrypted = json_decode(base64_decode($check));
				return true;
			}
		}

		// salts do not work
		return false;
	}

	protected function getDelimiter()
	{
		if (is_null($this->save_decrypted)) {
			$this->delimiter = substr($this->save_encrypted, strlen($this->save_encrypted) - 48, 16);
			return true;
		}

		return false;
	}

	public function resetCooldowns()
	{
		foreach ($this->save_decrypted->skillCooldowns as &$cooldown)
		{
			$cooldown = 0;
		}

		return $this;
	}

	protected function randomCharacter()
	{
		$characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		return$characters[mt_rand(0,strlen($characters)-1)];
	}

}