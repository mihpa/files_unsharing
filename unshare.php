<?php

/**
* ownCloud - Unsharing shared files
*
* @author Andrzej Michalec & Pavel Mihajluk
* @copyright 2013 Andrzej Michalec (andrzej.michalec@gmail.com, http://amichalec.net)
* @copyright 2014 Pavel Mihajluk (mihpa4@gmail.com, http://liftsoft.ru)
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either 
* version 3 of the License, or any later version.
* 
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*  
* You should have received a copy of the GNU Affero General Public 
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
* 
*/
if (OCP\Share::isEnabled()) {
  $itemType = $_GET['itemType'];
  $itemSource = $_GET['itemSource'];
  $shareType = $_GET['shareType'];
  $shareWith = ($_GET['shareWith'] != '' ? $_GET['shareWith'] : null);
  if (($itemType != null) && ($itemSource != null) && ($shareType != null))
    try {
      $ret = OCP\Share::unshare($itemType, $itemSource, (int) $shareType, $shareWith);
      //$ret = OCP\Share::unshareAll($itemType, $itemSource);
      OC_Log::write('files_unsharing', 'unshare.php: unshare('.$itemSource.') returned: '.$ret, OC_Log::INFO);
    } catch (Exception $e) {
      OC_Log::write('files_unsharing', 'Exception: '.$e->getMessage(), OC_Log::ERROR);
    }
}
$redirect = 'Location: /'.OCP\Config::getSystemValue('overwritewebroot', '').'/'.dirname($_SERVER['REQUEST_URI']);
while (strpos($redirect, '//') !== false)
  $redirect = str_replace('//', '/', $redirect);
header($redirect);
?>