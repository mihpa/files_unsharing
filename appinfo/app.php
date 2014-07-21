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

if (!OCP\App::isEnabled('files_sharing')) {
	// if 'files_sharing' would be enabled, share backends required for unsharing would be
	// already loaded. Since 'files_sharing' is shipped (built-in) we can rely on these files.
	OC::$CLASSPATH['OC_Share_Backend_File'] = "apps/files_sharing/lib/share/file.php";
	OC::$CLASSPATH['OC_Share_Backend_Folder'] = 'apps/files_sharing/lib/share/folder.php';
	OCP\Share::registerBackend('file', 'OC_Share_Backend_File');
	OCP\Share::registerBackend('folder', 'OC_Share_Backend_Folder', 'file');
}
$l10n = OC_L10N::get('files_unsharing');
OCP\App::addNavigationEntry( array( 
	'id' => 'files_unsharing',
	'order' => 50,
	'href' => OCP\Util::linkTo( 'files_unsharing', 'index.php' ),
	'icon' => OCP\Util::imagePath( 'files_unsharing', 'share.svg' ),
	'name' => $l10n->t('Shared')
));
