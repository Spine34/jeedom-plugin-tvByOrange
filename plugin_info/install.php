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

require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';

// Fonction exécutée automatiquement après l'installation du plugin
function tvByOrange_install()
{
	$cron = cron::byClassAndFunction('tvByOrange', 'update');
	if (!is_object($cron)) {
		$cron = new cron();
		$cron->setClass('tvByOrange');
		$cron->setFunction('update');
		$cron->setEnable(1);
		$cron->setDeamon(1);
		$cron->setSchedule('* * * * *');
		$cron->setTimeout(1440);
		$cron->setDeamonSleepTime(5);
		$cron->save();
		tvByOrange::deamon_start();
	}
}

// Fonction exécutée automatiquement après la mise à jour du plugin
function tvByOrange_update()
{
	$cron = cron::byClassAndFunction('tvByOrange', 'update');
	if (!is_object($cron)) {
		$cron = new cron();
	}
	$cron->setClass('tvByOrange');
	$cron->setFunction('update');
	$cron->setEnable(1);
	$cron->setDeamon(1);
	$cron->setTimeout(1440);
	$cron->setSchedule('* * * * *');
	$cron->setDeamonSleepTime(5);
	$cron->save();
	tvByOrange::deamon_start();
}

if (!is_file(dirname(__FILE__) . '/../core/config/channel.json')) {
	throw new Exception(__('Fichier channel.json non trouvé', __FILE__));
}
$cmdsArray = json_decode(file_get_contents(dirname(__FILE__) . '/../core/config/channel.json'), true);
foreach (eqLogic::byType('tvByOrange') as $eqLogic) {
	foreach (($eqLogic->getCmd('action')) as $cmd) {
		if ($cmd->getLogicalId() == 'c8' || $cmd->getLogicalId() == 'nrj12' || $cmd->getLogicalId() == 'canalPlusSeries' || $cmd->getLogicalId() == 'paramountChannel' || $cmd->getLogicalId() == 'paramountChannelDecale' || $cmd->getLogicalId() == 'eurochanne') {
			$cmd->remove();
		} else {
			foreach ($cmdsArray as $cmdArray) {
				if ($cmd->getLogicalId() == $cmdArray['logicalId']) {
					if (isset($cmdArray['configuration'])) {
						foreach ($cmdArray['configuration'] as $key => $value) {
							$cmd->setConfiguration($key, $value);
							$cmd->save();
						}
					}
				}
			}
		}
	}
	$eqLogic->save();
	tvByOrange::orderChannel($eqLogic->getId());
	$eqLogic->postAjax();
}

// Fonction exécutée automatiquement après la suppression du plugin
function tvByOrange_remove()
{
	$cron = cron::byClassAndFunction('tvByOrange', 'update');
	if (is_object($cron)) {
		$cron->remove();
	}
}
