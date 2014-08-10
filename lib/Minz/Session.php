<?php

/**
 * La classe Session gère la session utilisateur
 * C'est un singleton
 */
class Minz_Session {
	/**
	 * $session stocke les variables de session
	 */
	private static $session = array ();	//TODO: Try to avoid having another local copy

	/**
	 * Initialise la session, avec un nom
	 * Le nom de session est utilisé comme nom pour les cookies et les URLs (i.e. PHPSESSID).
	 * Il ne doit contenir que des caractères alphanumériques ; il doit être court et descriptif
	 */
	public static function init($name) {
		$cookie = session_get_cookie_params();
		self::keepCookie($cookie['lifetime']);

		// démarre la session
		session_name($name);
		session_start();

		if (isset($_SESSION)) {
			self::$session = $_SESSION;
		}
	}


	/**
	 * Permet de récupérer une variable de session
	 * @param $p le paramètre à récupérer
	 * @return la valeur de la variable de session, false si n'existe pas
	 */
	public static function param ($p, $default = false) {
		return isset(self::$session[$p]) ? self::$session[$p] : $default;
	}


	/**
	 * Permet de créer ou mettre à jour une variable de session
	 * @param $p le paramètre à créer ou modifier
	 * @param $v la valeur à attribuer, false pour supprimer
	 */
	public static function _param ($p, $v = false) {
		if ($v === false) {
			unset ($_SESSION[$p]);
			unset (self::$session[$p]);
		} else {
			$_SESSION[$p] = $v;
			self::$session[$p] = $v;
		}
	}


	/**
	 * Permet d'effacer une session
	 * @param $force si à false, n'efface pas le paramètre de langue
	 */
	public static function unset_session ($force = false) {
		$language = self::param ('language');

		session_destroy();
		self::$session = array ();

		if (!$force) {
			self::_param ('language', $language);
			Minz_Translate::reset ();
		}
	}


	/**
	 * Spécifie la durée de vie des cookies
	 * @param $l la durée de vie
	 */
	public static function keepCookie($l) {
		$cookie_dir = dirname(
			empty($_SERVER['SCRIPT_NAME']) ? '' : $_SERVER['SCRIPT_NAME']
		) . '/';
		session_set_cookie_params($l, $cookie_dir, $_SERVER['HTTP_HOST'],
		                          false, true);

		$l_session = max(1440, $l);
		ini_set('session.gc_maxlifetime', $l_session);
	}


	/**
	 * Régénère un id de session.
	 * Utile pour appeler session_set_cookie_params après session_start()
	 */
	public static function regenerateID() {
		session_regenerate_id(true);
	}

}
