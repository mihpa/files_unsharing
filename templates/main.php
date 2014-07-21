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
    
function prnt($x) { print($x); } 
$l10n = OC_L10N::get('files_unsharing');
$shares = null;
$title = null;

if (OCP\Share::isEnabled()) {
  $sql='SELECT * FROM `*PREFIX*share` WHERE `uid_owner`= ?  ORDER BY `file_target` ASC ';
  $sql_delete='DELETE FROM `*PREFIX*share` WHERE `item_source`= ?';
  $sql2='SELECT * FROM `*PREFIX*filecache` WHERE `fileid` = ?';
  $query = \OCP\DB::prepare($sql);
  $query_delete = \OCP\DB::prepare($sql_delete);
  $query2 = \OCP\DB::prepare($sql2);
  $UserID=\OCP\User::getUser();
  if(OC_User::isAdminUser(OC_User::getUser()) )
    $users=array_unique(array_merge(array($UserID),\OCP\User::getUsers()));
  else
    $users=array($UserID);
  foreach($users as $username){
    $result = $query->execute(array($username));
    while($row = $result->fetchRow()) {
      $result2 = $query2->execute(array($row['item_source']));
      if(trim($row['token'])!=''){
        if(trim($row['share_with'])=='')
          $row['share_with']=\OCP\Util::getL10N('shareslist')->t('['.$l10n->t('link public').']');
        else
          $row['share_with']=\OCP\Util::getL10N('shareslist')->t('['.$l10n->t('link password').']');
      }
      $row2 = $result2->fetchRow();
      if(trim($row2['path'])!=''){
        $link='';
        if ($username==$UserID){
          $path_parts = pathinfo($row2['path']);
          $link=\OC\Files\Filesystem::normalizePath(substr($path_parts['dirname'],5));
          $link=OCP\Util::linkTo('files', 'index.php') . '#?dir=' . \OCP\Util::encodePath($link);
        }
        $shares[] = array(
          'item_type' => $row['item_type'],
          'share_type' => $row['share_type'],
          'path' => str_replace('files/', '', $row2['path']),
          'token' => $row['token'],
          'displayname_owner_short' => $username,
          'displayname_owner' => OCP\User::getDisplayName($username),
          'share_with_displayname_short' => $row['share_with'],
          'share_with_displayname' => OCP\User::getDisplayName($row['share_with']),
          'expiration' => $row['expiration'],
          'file_source' => $row['item_source']
          );
      }else{
        $query_delete->execute(array($row['item_source']));
      }
    }
  }

	//$shares = OCP\Share::getItemsShared('file');
	if (isset($shares)) {
    $title = $l10n->t('Open shared');
  } else {
    $title = $l10n->t('Do not share any files');
  } 
} else {
	$title = $l10n->t('Sharing is disabled by administrator');
}
?>
<div id='controls'><div id='breadcrumbs'><div class='crumb last'><a href='#'><?php prnt($title) ?></a></div></div></div>
<table id='filestable'>
<thead>
  <tr>
    <th id='headerName'>
      <div id='headerName-container'><span class='name'><?php prnt($l10n->t('File name'))?></span></div>
    </th>
    <th id='headerPublic'>
      <?php prnt($l10n->t('Public link'))?>
    </th>
    <th id='headerOwner'>
      <?php prnt($l10n->t('Owner'))?>
    </th>
    <th id='headerSharedWith'>
      <?php prnt($l10n->t('Shared with'))?>
    </th>
    <th id='headerDate expires'>
      <span id='modified'><?php prnt($l10n->t('Expires'))?></span>
    </th>
  </tr>
</thead>
<tbody id='fileList'>
	<?php
	$tipUnshare = $l10n->t('Unshare');
	$tipGoto = $l10n->t('Open parent directory');
	foreach($shares as $sh) {
		$path = $sh['path'];
		if ($sh['item_type']=='folder') {
			$icon = OC_Helper::mimetypeIcon('dir');
		} else {
			$mime = OC_Filesystem::getMimeType($path);
			$icon = OC_Helper::mimetypeIcon($mime);
		}
    if ($sh['displayname_owner_short'] == OC_User::getUser()) {
      $path = '<a href="'.OCP\Util::linkTo('files', 'index.php').'?dir='.dirname($path).'" class="name" original-title="'.$tipGoto.'">';
      $path .= '<span class="nametext">'.$sh['path'].'</span>';
      $path .= '</a>';
    } else
      $path = '<a class="name nolink"><span class="nametext nolink">'.$sh['path'].'</span></a>';    
    if ($sh['token'] != '')
      $public = '<a href="'.\OCP\Util::linkToPublic('files').'&t='.$sh['token'].'" original-title="'.$l10n->t('Open link').'" class="action permanent" target="_blank"><img class="svg" src="'.OC::$WEBROOT.'/core/img/actions/public.svg"/></a>';
    else
      $public = '';
		$exp = str_replace('00:00:00', '', $sh['expiration']); // remove zero-time from date
		$exp = strlen($exp)>0 ? trim($l10n->t('Expires on').' '.$exp) : $l10n->t('Never expires');
		$itemType = $sh['item_type'];
		$itemSource = $sh['file_source'];
    $shareType = $sh['share_type'];
    if ($sh['token'] == '')
      $shareWith = $sh['share_with_displayname_short'];
    else
      $shareWith = '';
	?>
	<tr data-type='file'>
    <td class='filename' style='background-image:url(<?php prnt($icon) ?>)'>
      <?php prnt($path)?>
    </td>
    <td class='link'>
      <?php prnt($public) ?>
    </td>
    <td class='displayname'>
      <?php prnt($sh['displayname_owner']) ?>
    </td>
    <td class='displayname'>
      <?php prnt($sh['share_with_displayname']) ?>
    </td>
    <td class='delete' style='min-width:15em'>
      <span class='modified'><?php prnt($exp) ?></span>
			<a href='files_unsharing/unshare.php?itemType=<?php prnt($itemType) ?>&itemSource=<?php prnt($itemSource) ?>&shareType=<?php prnt($shareType) ?>&shareWith=<?php prnt($shareWith) ?>' class='action delete delete-icon' original-title='<?php prnt($tipUnshare) ?>'/>
    </td>
	</tr>
	<?php
	} //foreach
	?>
</tbody>
</table>
