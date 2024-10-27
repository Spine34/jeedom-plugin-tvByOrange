<?php
/* This file is part of Jeedom.
*
* Jeedom is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* Jeedom is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
*/

/* * ***************************Includes********************************* */
require_once __DIR__  . '/../../../../core/php/core.inc.php';

class tvByOrange extends eqLogic
{
	/*     * *************************Attributs****************************** */

	// Permet de définir les possibilités de personnalisation du widget (en cas d'utilisation de la fonction 'toHtml' par exemple)
	// Tableau multidimensionnel - exemple: array('custom' => true, 'custom::layout' => false)
	public static $_widgetPossibility = array('custom' => true);

	/*
	* Permet de crypter/décrypter automatiquement des champs de configuration du plugin
	* Exemple : "param1" & "param2" seront cryptés mais pas "param3"
	public static $_encryptConfigKey = array('param1', 'param2');
	*/

	/*     * ***********************Methode static*************************** */

	/*
	* Fonction exécutée automatiquement toutes les minutes par Jeedom
	public static function cron() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les 5 minutes par Jeedom
	public static function cron5() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les 10 minutes par Jeedom
	public static function cron10() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les 15 minutes par Jeedom
	public static function cron15() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les 30 minutes par Jeedom
	public static function cron30() {}
	*/

	/*
	* Fonction exécutée automatiquement toutes les heures par Jeedom
	public static function cronHourly() {}
	*/

	/*
	* Fonction exécutée automatiquement tous les jours par Jeedom
	public static function cronDaily() {}
	*/

	/*
	* Permet de déclencher une action avant modification d'une variable de configuration du plugin
	* Exemple avec la variable "param3"
	public static function preConfig_param3( $value ) {
		// do some checks or modify on $value
		return $value;
	}
	*/

	/*
	* Permet de déclencher une action après modification d'une variable de configuration du plugin
	* Exemple avec la variable "param3"
	public static function postConfig_param3($value) {
		// no return value
	}
	*/

	/*
	* Permet d'indiquer des éléments supplémentaires à remonter dans les informations de configuration
	* lors de la création semi-automatique d'un post sur le forum community
	public static function getConfigForCommunity() {
		// Cette function doit retourner des infos complémentataires sous la forme d'un
		// string contenant les infos formatées en HTML.
		return "les infos essentiel de mon plugin";
	}
	*/

	/*     * *********************Méthodes d'instance************************* */

	// Fonction exécutée automatiquement avant la création de l'équipement
	public function preInsert()
	{
		$this->setIsEnable(1);
		$this->setIsVisible(1);
	}

	// Fonction exécutée automatiquement après la création de l'équipement
	public function postInsert() {}

	// Fonction exécutée automatiquement avant la mise à jour de l'équipement
	public function preUpdate() {}

	// Fonction exécutée automatiquement après la mise à jour de l'équipement
	public function postUpdate() {}

	// Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
	public function preSave() {}

	// Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
	public function postSave()
	{
		$commands = json_decode(file_get_contents(dirname(__FILE__) . '/../config/cmd.json'), true);
		$order = 0;
		log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $commands : ' . json_encode($commands));
		foreach ($commands as $command) {
			log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $command : ' . json_encode($command));
			$cmd = $this->getCmd(null, $command['logicalId']);
			if (!is_object($cmd)) {
				log::add(__CLASS__, 'info', $this->getHumanName() . ' : Command [' . $command['name'] . '] created');
				$cmd = (new speedtestByOoklaCmd);
				if (isset($command['logicalId'])) {
					$cmd->setLogicalId($command['logicalId']);
				}
				if (isset($command['generic_type'])) {
					$cmd->setGeneric_type($command['generic_type']);
				}
				if (isset($command['name'])) {
					$cmd->setName($command['name']);
				}
				$cmd->setOrder($order++);
				if (isset($command['type'])) {
					$cmd->setType($command['type']);
				}
				if (isset($command['subType'])) {
					$cmd->setSubType($command['subType']);
				}
				$cmd->setEqLogic_id($this->getId());
				if (isset($command['isHistorized'])) {
					$cmd->setIsHistorized($command['isHistorized']);
				}
				if (isset($command['unite'])) {
					$cmd->setUnite($command['unite']);
				}
				if (isset($command['configuration'])) {
					foreach ($command['configuration'] as $key => $value) {
						$cmd->setConfiguration($key, $value);
					}
				}
				if (isset($command['template'])) {
					foreach ($command['template'] as $key => $value) {
						$cmd->setTemplate($key, $value);
					}
				}
				if (isset($command['display'])) {
					foreach ($command['display'] as $key => $value) {
						$cmd->setDisplay($key, $value);
					}
				}
				if (isset($command['value'])) {
					$cmd->setValue($this->getCmd(null, $command['value'])->getId());
				}
				if (isset($command['isVisible'])) {
					$cmd->setIsVisible($command['isVisible']);
				}
				$cmd->save();
			}
		}
	}

	// Fonction exécutée automatiquement avant la suppression de l'équipement
	public function preRemove() {}

	// Fonction exécutée automatiquement après la suppression de l'équipement
	public function postRemove() {}

	/*
	* Permet de crypter/décrypter automatiquement des champs de configuration des équipements
	* Exemple avec le champ "Mot de passe" (password)
	public function decrypt() {
		$this->setConfiguration('password', utils::decrypt($this->getConfiguration('password')));
	}
	public function encrypt() {
		$this->setConfiguration('password', utils::encrypt($this->getConfiguration('password')));
	}
	*/

	/*
	* Permet de modifier l'affichage du widget (également utilisable par les commandes)
	public function toHtml($_version = 'dashboard') {}
	*/

	/*     * **********************Getteur Setteur*************************** */
}

class tvByOrangeCmd extends cmd
{
	/*     * *************************Attributs****************************** */

	/*
	public static $_widgetPossibility = array();
	*/

	/*     * ***********************Methode static*************************** */


	/*     * *********************Methode d'instance************************* */

	/*
	* Permet d'empêcher la suppression des commandes même si elles ne sont pas dans la nouvelle configuration de l'équipement envoyé en JS
	public function dontRemoveCmd() {
		return true;
	}
	*/

	// Exécution d'une commande
	public function execute($_options = array()) {}

	/*     * **********************Getteur Setteur*************************** */
}
