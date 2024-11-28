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

	public static function deamon_info()
	{
		$return = array();
		$return['log'] = '';
		$return['state'] = 'nok';
		$cron = cron::byClassAndFunction(__CLASS__, 'update');
		if (is_object($cron) && $cron->running()) {
			$return['state'] = 'ok';
		}
		$return['launchable'] = 'ok';
		return $return;
	}

	public static function deamon_start()
	{
		self::deamon_stop();
		$deamon_info = self::deamon_info();
		if ($deamon_info['launchable'] != 'ok') {
			throw new Exception(__('Veuillez vérifier la configuration', __FILE__));
		}
		$cron = cron::byClassAndFunction(__CLASS__, 'update');
		if (!is_object($cron)) {
			throw new Exception('Tache cron introuvable');
		}
		$cron->run();
	}

	public static function deamon_stop()
	{
		$cron = cron::byClassAndFunction(__CLASS__, 'update');
		if (!is_object($cron)) {
			throw new Exception('Tache cron introuvable');
		}
		$cron->halt();
	}

	public static function deamon_changeAutoMode($_mode)
	{
		$cron = cron::byClassAndFunction(__CLASS__, 'update');
		if (!is_object($cron)) {
			throw new Exception('Tache cron introuvable');
		}
		$cron->setEnable($_mode);
		$cron->save();
	}

	public static function update()
	{
		foreach (eqLogic::byType(__CLASS__, true) as $eqLogic) {
			try {
				$eqLogic->refreshData();
			} catch (Exception $exc) {
				log::add(__CLASS__, 'error', $eqLogic->getHumanName() . ' : Erreur : ' . $exc->getMessage());
			}
		}
	}

	/*     * *********************Méthodes d'instance************************* */

	// Fonction exécutée automatiquement avant la création de l'équipement
	public function preInsert()
	{
		$this->setIsVisible(1);
		$this->setIsEnable(1);
		$this->setCategory('multimedia', 1);
	}

	// Fonction exécutée automatiquement après la création de l'équipement
	public function postInsert() {}

	// Fonction exécutée automatiquement avant la mise à jour de l'équipement
	public function preUpdate()
	{
		if (empty($this->getConfiguration('ip'))) {
			throw new Exception('L\'adresse IP du décodeur ne peut être vide');
		}
	}

	// Fonction exécutée automatiquement après la mise à jour de l'équipement
	public function postUpdate() {}

	// Fonction exécutée automatiquement avant la sauvegarde (création ou mise à jour) de l'équipement
	public function preSave() {}

	// Fonction exécutée automatiquement après la sauvegarde (création ou mise à jour) de l'équipement
	public function postSave()
	{
		if (!is_file(dirname(__FILE__) . '/../config/cmd.json')) {
			throw new Exception(__('Fichier cmd.json non trouvé', __FILE__));
		}
		if (!is_file(dirname(__FILE__) . '/../config/channel.json')) {
			throw new Exception(__('Fichier channel.json non trouvé', __FILE__));
		}
		$cmdsArray = json_decode(file_get_contents(dirname(__FILE__) . '/../config/cmd.json'), true);
		$channelsArray = json_decode(file_get_contents(dirname(__FILE__) . '/../config/channel.json'), true);
		$cmdsArray = array_merge($cmdsArray, $channelsArray);
		$order = 0;
		log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $cmdsArray : ' . json_encode($cmdsArray));
		foreach ($cmdsArray as $cmdArray) {
			log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $cmdArray : ' . json_encode($cmdArray));
			$cmd = $this->getCmd(null, $cmdArray['logicalId']);
			if (!is_object($cmd)) {
				log::add(__CLASS__, 'info', $this->getHumanName() . ' : Commande [' . $cmdArray['name'] . '] créée');
				$cmd = (new tvByOrangeCmd);
				if (isset($cmdArray['logicalId'])) {
					$cmd->setLogicalId($cmdArray['logicalId']);
				}
				if (isset($cmdArray['generic_type'])) {
					$cmd->setGeneric_type($cmdArray['generic_type']);
				}
				if (isset($cmdArray['name'])) {
					$cmd->setName($cmdArray['name']);
				}
				$cmd->setOrder($order++);
				if (isset($cmdArray['type'])) {
					$cmd->setType($cmdArray['type']);
				}
				if (isset($cmdArray['subType'])) {
					$cmd->setSubType($cmdArray['subType']);
				}
				$cmd->setEqLogic_id($this->getId());
				if (isset($cmdArray['isHistorized'])) {
					$cmd->setIsHistorized($cmdArray['isHistorized']);
				}
				if (isset($cmdArray['unite'])) {
					$cmd->setUnite($cmdArray['unite']);
				}
				if (isset($cmdArray['configuration'])) {
					foreach ($cmdArray['configuration'] as $key => $value) {
						$cmd->setConfiguration($key, $value);
					}
				}
				if (isset($cmdArray['template'])) {
					foreach ($cmdArray['template'] as $key => $value) {
						$cmd->setTemplate($key, $value);
					}
				}
				if (isset($cmdArray['display'])) {
					foreach ($cmdArray['display'] as $key => $value) {
						$cmd->setDisplay($key, $value);
					}
				}
				if (isset($cmdArray['value'])) {
					$cmd->setValue($this->getCmd(null, $cmdArray['value'])->getId());
				}
				if (isset($cmdArray['isVisible'])) {
					$cmd->setIsVisible($cmdArray['isVisible']);
				}
				$cmd->save();
			}
		}
		self::deamon_start();
	}

	public function postAjax()
	{
		$channelSelect = '';
		$customListValue = '';
		if (!empty($this->getConfiguration('ip'))) {
			$cmds = $this->getCmd('action');
			foreach ($cmds as $cmd) {
				if ($cmd->getConfiguration('table') == 'channel') {
					$channelSelect .= $cmd->getConfiguration('epg_id') . '|' . $cmd->getName() . ';';
					if ($cmd->getConfiguration('customChannelSelect') == 1) {
						$customChannelSelect .= $cmd->getConfiguration('epg_id') . '|' . $cmd->getName() . ';';
					}
				}
			}
			$channelSelect = rtrim($channelSelect, ';');
			log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $channelSelect : ' . $channelSelect);
			$cmdChannelSelect = $this->getCmd('action', 'channelSelect');
			$cmdChannelSelect->setConfiguration('listValue', $channelSelect);
			$cmdChannelSelect->save();

			$customChannelSelect = rtrim($customChannelSelect, ';');
			log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $customChannelSelect : ' . $customChannelSelect);
			$cmdCustomChannelSelect = $this->getCmd('action', 'customChannelSelect');
			$cmdCustomChannelSelect->setConfiguration('listValue', $customChannelSelect);
			$cmdCustomChannelSelect->save();
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

	public function refreshData()
	{
		if (!empty($this->getConfiguration('ip'))) {
			$url = 'http://' . $this->getConfiguration('ip') . ':8080/remoteControl/cmd?operation=10';

			log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $url : ' . $url);

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);

			$result = curl_exec($ch);

			if (curl_errno($ch)) {
				log::add(__CLASS__, 'warning', $this->getHumanName() . ' : Erreur cURL : ' . curl_error($ch));
				$this->checkAndUpdateCmd('online', 0);
			} else {
				log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $result : ' . $result);
				$result = json_decode($result, true);
				$this->checkAndUpdateCmd('osdContext', $result['result']['data']['osdContext']);
				$this->checkAndUpdateCmd('playedMediaType', $result['result']['data']['playedMediaType']);
				$this->checkAndUpdateCmd('playedMediaState', $result['result']['data']['playedMediaState']);
				$this->checkAndUpdateCmd('playedMediaId', $result['result']['data']['playedMediaId']);
				$this->checkAndUpdateCmd('playedMediaContextId', $result['result']['data']['playedMediaContextId']);
				$this->checkAndUpdateCmd('playedMediaPosition', $result['result']['data']['playedMediaPosition']);
				$this->checkAndUpdateCmd('timeShiftingState', $result['result']['data']['timeShiftingState']);
				$this->checkAndUpdateCmd('macAddress', $result['result']['data']['macAddress']);
				$this->checkAndUpdateCmd('wolSupport', $result['result']['data']['wolSupport']);
				$this->checkAndUpdateCmd('friendlyName', $result['result']['data']['friendlyName']);
				$this->checkAndUpdateCmd('activeStandbyState', $result['result']['data']['activeStandbyState']);
				if (isset($result['result']['data']['npvrSupport'])) {
					$this->checkAndUpdateCmd('activeStandbyState', $result['result']['data']['npvrSupport']);
				}
				$cmds = $this->getCmd('action');
				$epg_id = false;
				if (preg_match('/[1-9]/', $result['result']['data']['playedMediaId']) === 1) {
					foreach ($cmds as $cmd) {
						if ($cmd->getConfiguration('table') == 'channel') {
							if ($result['result']['data']['playedMediaId'] == $cmd->getConfiguration('epg_id')) {
								$this->checkAndUpdateCmd('channelNumber', $cmd->getConfiguration('number'));
								$this->checkAndUpdateCmd('channelText', $cmd->getName());
								$epg_id = true;
								break;
							}
						}
					}
					if (!$epg_id) {
						log::add(__CLASS__, 'error', $this->getHumanName() . ' : L\'ID EPG ' . $result['result']['data']['playedMediaId'] . ' n\'existe pas dans la liste des chaînes');
					}
				} else {
					$this->checkAndUpdateCmd('channelNumber', 'NA');
					$this->checkAndUpdateCmd('channelText', 'NA');
				}
				$this->checkAndUpdateCmd('online', 1);
			}

			curl_close($ch);
		}
	}

	public function sendCmd($key)
	{
		if (!empty($this->getConfiguration('ip'))) {
			$url = 'http://' . $this->getConfiguration('ip') . ':8080/remoteControl/cmd?operation=01&key=' . $key . '&mode=0';

			log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $url : ' . $url);

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);

			$result = curl_exec($ch);

			if (curl_errno($ch)) {
				log::add(__CLASS__, 'error', $this->getHumanName() . ' : Erreur cURL : ' . curl_error($ch));
			} else {
				log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $result : ' . $result);
			}

			curl_close($ch);
		}
	}

	public function sendChannel($epg_id)
	{
		if (!empty($this->getConfiguration('ip'))) {
			$epg_id = str_pad($epg_id, 10, '*', STR_PAD_LEFT);

			$url = 'http://' . $this->getConfiguration('ip') . ':8080/remoteControl/cmd?operation=09&epg_id=' . $epg_id . '&uui=1';

			log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $url : ' . $url);

			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);

			$result = curl_exec($ch);

			if (curl_errno($ch)) {
				log::add(__CLASS__, 'error', $this->getHumanName() . ' : Erreur cURL : ' . curl_error($ch));
			} else {
				log::add(__CLASS__, 'debug', $this->getHumanName() . ' : $result : ' . $result);
			}

			curl_close($ch);
		}
	}

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
	public function execute($_options = array())
	{
		log::add('tvByOrange', 'debug', $this->getHumanName() . ' : $_options : ' . json_encode($_options));
		if ($this->getConfiguration('table') == 'cmd') {
			if ($this->getLogicalId() == 'refresh') {
				$this->getEqLogic()->refreshData();
			} else if ($this->getLogicalId() == 'channelSelect' || $this->getLogicalId() == 'customChannelSelect') {
				$this->getEqLogic()->sendChannel($_options['select']);
			} else if ($this->getLogicalId() == 'channelSlider') {
				$cmds = $this->getEqLogic()->getCmd('action');
				$number = false;
				foreach ($cmds as $cmd) {
					if ($cmd->getConfiguration('table') == 'channel') {
						if ($_options['slider'] == $cmd->getConfiguration('number')) {
							$this->getEqLogic()->sendChannel($cmd->getConfiguration('epg_id'));
							$number = true;
							break;
						}
					}
				}
				if (!$number) {
					log::add('tvByOrange', 'error', $this->getHumanName() . ' : Le numéro ' . $_options['slider'] . ' n\'existe pas dans la liste des chaînes');
				}
			} else if ($this->getLogicalId() == 'volumeSlider') {
				$volumeState = $this->getEqLogic()->getCmd('info', 'volumeState')->execCmd();
				if ($_options['slider'] > $volumeState) {
					$this->getEqLogic()->sendCmd(115);
				} else {
					$this->getEqLogic()->sendCmd(114);
				}
				$this->getEqLogic()->checkAndUpdateCmd('volumeState', $_options['slider']);
			} else if ($this->getLogicalId() == 'netflix') {
				$this->getEqLogic()->sendCmd(518);
				$this->getEqLogic()->sendCmd(518);
			} else if ($this->getLogicalId() == 'primeVideo') {
				$this->getEqLogic()->sendCmd(518);
				$this->getEqLogic()->sendCmd(519);
			} else if ($this->getLogicalId() == 'disneyPlus') {
				$this->getEqLogic()->sendCmd(518);
				$this->getEqLogic()->sendCmd(520);
			} else if ($this->getLogicalId() == 'paramountPlus') {
				$this->getEqLogic()->sendCmd(518);
				$this->getEqLogic()->sendCmd(521);
			} else if ($this->getLogicalId() == 'max') {
				$this->getEqLogic()->sendCmd(519);
				$this->getEqLogic()->sendCmd(512);
			} else {
				$this->getEqLogic()->sendCmd($this->getConfiguration('key'));
			}
		} else if ($this->getConfiguration('table') == 'channel') {
			$this->getEqLogic()->sendChannel($this->getConfiguration('epg_id'));
		}
	}

	/*     * **********************Getteur Setteur*************************** */
}
