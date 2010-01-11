<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006-2009 Oliver Wand <wand@itaw.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Plugin 'ITAW Gigdisplayer' for the 'itaw_gigdisplayer' extension.
 *
 * @author	Oliver Wand <wand@itaw.de>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_itawgigdisplayer_pi1 extends tslib_pibase {
	var $prefixId = 'tx_itawgigdisplayer_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_itawgigdisplayer_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'itaw_gigdisplayer';	// The extension key.
	var $pi_checkCHash = TRUE;
	var $content = "";
	var	$marker = array();

	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
		global $GLOBALS;
		$akt_pid = $GLOBALS['TSFE']->id;
    	$gigid = $this->piVars['gigid'];
		$lang = $GLOBALS['TSFE']->sys_language_uid;
		$tmpl = $this->cObj->fileResource($conf["templateFile"]);
		$img_path = "uploads/tx_itawgigdisplayer/";
		$today = time();

		// Getting the pid list via the flexform
		$pid_list = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],
										'Pagebrowser', 'sDEF')
					? implode(t3lib_div::intExplode(',',
							$this->pi_getFFvalue($this->cObj->data['pi_flexform'],
												'Pagebrowser', 'sDEF')), ',')
							: $GLOBALS['TSFE']->id;

		// Checking for recursive level
        $recursive = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],
        									'recursive', 'sDEF');
		if (is_numeric($recursive) && $recursive > 0) {
			$this->config['pid_list'] = $this->pi_getPidList($pid_list,$recursive);
        } else {
            $this->config['pid_list'] = $pid_list;
        }

		// $code = what to render in FrontEnd
		$display = $this->pi_getFFvalue($this->cObj->data['pi_flexform'],
										'displayType', 'sDEF');

		if (!count($display)) {
		   $display = 'noOPTION';
		}

		// Load Lightbox-JS and -CSS
		$content .= $this->initLightbox();

		// Check what to do.
		switch ($display) {
  			case 'noOPTION':
				$content .= 'No OPTION given. Please correct your Plugin settings.';
				break;
			case 'teaser':

				$content .= $this->showTeaserView($conf, $lang, $pid_list);
  				break;

  			case 'list':

		        if(isset($gigid)) { // Show detailed data
					$content .= $this->showDetailView($conf, $lang, $pid_list, $gigid);
					break;
		        } else { // Show list-preview of date records
					$content .= $this->showListView($conf, $lang, $pid_list);
					break;
		        }

		      // 'History'-Display; displays old dates als kind of archive if wanted

   			case 'history':

		      if(isset($gigid)) {     // if Detailed View show no History below it
		         return $content;
		      }

    	      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_itawgigdisplayer_main',
		      				'deleted = 0 AND hidden = 0 AND sys_language_uid =
		      				' . $lang . ' and pid = ' . $pid_list . ' ORDER BY date DESC');
		      $totalRows_search = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		      if ($totalRows_search > 0) {
		         while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
		            $datum = date("d.m.y",$row['date']);
		            if ($today > $row['date']) {
		               $content .= $datum . '<br />' . $row['country'] . '&nbsp;-&nbsp;'
		               				. $row['location_city'] . '&nbsp;-&nbsp;'
		               				. $row['location'] . '<br /><br />';
		            }
		         }
		         return $content;
		      } else {
		         $content .= $this->pi_getLL('main_noHistoryRecord');
		         return $content;
		      }

		      break;
		}

		return $this->pi_wrapInBaseClass($content);


    } // End function main

/**
 * shows Teaser View
 *
 * @param array $conf: The PlugIn configuration
 * @param string $lang: sys_language_uid
 * @param string $pid_list: corresponding pid
 * @return content
 */
    function showTeaserView($conf, $lang, $pid_list) {
    			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_itawgigdisplayer_main',
											 'deleted = 0 AND hidden = 0 AND sys_language_uid =
											 ' . $lang . ' and pid = ' . $pid_list . ' order by date ASC');
				$totalRows_search = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
				$content .= '<br />';

				if ($totalRows_search > 0) {
		             while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
		                $setlink = $this->pi_linkTP_keepPIvars_url(array(
								    'gigid'=>$row['uid']),1,1,intval($this->conf['listView']));
		                $datum = date("d.m.y",$row['date']);
		                if ($today <= $row['date']+24*60*60) {
							// get listView ID from TypoScript-Configuration
							// and link it on teaser date
		                	$content .= '<br /><a href="' . $setlink . '">' . $datum . '&nbsp;'
		                   				. $row['country'] . '<br />' . $row['location_city'] . '<br />';
		                   $content .= $row['location'];
		                   $content .= '</a>';
		                   return $content;
		                } else {
		                   $content = '';
		                }
		        	} // end while

	            	if ($content == '') {
	                	$content .= $this->pi_getLL('main_currentlyNoConfirmedLive');
	             	}

	             	return $content;

	           } else { // if no records are found in database
					$content .= $this->pi_getLL('main_currentlyNoConfirmedLive');
					return $content;
   		       }

    }


/**
 * shows Detailed View
 *
 * @param array $conf: The PlugIn configuration
 * @param string $lang: sys_language_uid
 * @param string $pid_list: corresponding pid
 * @param string $gigid: id of gig to show
 * @return content
 */
    function showDetailView($conf, $lang, $pid_list, $gigid) {

    	$tmpl = $this->cObj->fileResource($conf["templateFile"]);
		if (!count($tmpl)) {
			$content .= '<font color="red">No Template file was defined via TypoScript!
						<br />Please correct your Settings!</font>';
			return $content;
		}

		$tmpl = $this->cObj->getSubpart($tmpl, 'ITAW_GIGDISPLAYER');
		if (!count($tmpl)) {
		    $content .= '<font color="red">Subparts could not be addressed</font>';
		    return $content;
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
		         				'tx_itawgigdisplayer_main', 'deleted = 0 AND hidden = 0
		         				AND sys_language_uid = ' . $lang . ' and pid =
		          				' . $pid_list . ' and uid=' . $gigid);

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
		           $datum = date("d.m.y",$row['date']);
		           $marker['###DATETIME###'] = $datum;
		           $marker['###LOCATION###'] = $row['location'];
		           $marker['###LOCATION_ADDRESS###'] = $row['location_address'];
		           $marker['###LOCATION_CITY###'] = $row['country'] . "&nbsp;-&nbsp;" . $row['location_city'];
		           if ($row['info'] <> "") {
		              $marker['###INFO###'] = $row['info'];
		           } else {
		              $marker['###INFO###'] = $this->pi_getLL('main_noInfo');
		           }
		           if ($row['flyer'] <> "") {
			           	$imgTSConfig['file'] = $img_path.$row['flyer'];
			            $imgTSConfig['file.']['width'] = '75';
			            if (!defined( 'T3MOOTOOLS' )) {
			          		$imgTSConfig['imageLinkWrap'] = '1';
			             	$imgTSConfig['imageLinkWrap.']['enable'] = '1';
			            } else {
			              	$imgTSConfig['wrap'] = "<a href=\"".$imgTSConfig['file']
			                					. "\"  rel=\"lightbox\">|</a>";
			            }
			            $marker['###FLYER###'] = $this->cObj->IMAGE($imgTSConfig).' ';
			       } else {
			            $marker['###FLYER###'] = $this->pi_getLL('main_noFlyer');
			       }
			       $marker['###DATETEXT###'] = $this->pi_getLL('main_Datetext');
			       $marker['###LOCATIONTEXT###'] = $this->pi_getLL('main_Locationtext');
			       $marker['###INFOTEXT###'] = $this->pi_getLL('main_Infotext');
			       $marker['###FLYERTEXT###'] = $this->pi_getLL('main_Flyertext');

				   // get listView ID from TypoScript-Configuration
				   // and link it as backlink on listView
			       $setinternallink = $this->pi_getPageLink(intval($this->conf['listView']));
			       $marker['###BACKLINK###'] = '<a href="' . $setinternallink .
			           							'">'.$this->pi_getLL('main_backlink');
			       $content .= $this->cObj->substituteMarkerArrayCached($tmpl, $marker);
	    }

	    $content = $this->cObj->substituteSubpart($tmpl, '###EINTRAG###', $content);
	    return $content;
    }


/**
 * shows List View
 *
 * @param array $conf: The PlugIn configuration
 * @param string $lang: sys_language_uid
 * @param string $pid_list: corresponding pid
 * @return content
 */
    function showListView($conf, $lang, $pid_list) {

    	       	$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_itawgigdisplayer_main',
		         					'deleted = 0 AND hidden = 0 AND sys_language_uid =
		          					' . $lang . ' and pid = ' . $pid_list . ' order by date ASC');
		        $totalRows_search = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		        if ($totalRows_search > 0) {
		             while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
		             	// get listView ID from TypoScript-Configuration
						// and link it on page
						$setlink = $this->pi_linkTP_keepPIvars_url(array(
											'gigid'=>$row['uid']),1,1,intval($this->conf['listView']));
		                $datum = date("d.m.y",$row['date']);
		                if ($today < $row['date']+24*60*60) {
		                   $content .= '<a href="' . $setlink . '">' . $datum .
		                   				'&nbsp;' . $row['country'] . '&nbsp;-&nbsp;
		                   				' . $row['location_city'] . '&nbsp;-&nbsp;';
		                	if ($row['url']=="") {
		                      $content .= $row['location'].'</a><br />';
		                   } else {
		                      $url = $row['url'];
		                      $content .= $row['location'].'</a> (<a href="http://'.$row['url'].'" target="_blank">www</a>)<br />';
		                   }
		                   $content .= '<br />';
		                }
			          }

		              return $content;

		         } else { // if no records are found in database
		                $content .= $this->pi_getLL('main_currentlyNoConfirmedLive');
		                return $content;
		         }

    }

/**
 * initialises the Lightbox feature. Requires t3mootools!
 *
 * If t3mootools is not available then Lightbox feature will not be active
 *
 */
    function initLightbox() {

    	if (t3lib_extMgm::isLoaded( 't3mootools' )) {
			require_once(t3lib_extMgm::extPath( 't3mootools' ) . 'class.tx_t3mootools.php');
		}
		if (defined( 'T3MOOTOOLS' )) {
			tx_t3mootools::addMooJS();
			$GLOBALS['TSFE']->additionalHeaderData['itawgigdisplayer'] = $header . '
        		<script src="' . t3lib_extMgm::siteRelPath($this->extKey)
						. 'res/js/slimbox.js" type="text/javascript"></script>
				<link rel="stylesheet" href="' . t3lib_extMgm::siteRelPath($this->extKey)
						. 'res/css/slimbox.css" type="text/css" media="screen" />
			';

		}


    }

} // End class



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/itaw_gigdisplayer/pi1/class.tx_itawgigdisplayer_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/itaw_gigdisplayer/pi1/class.tx_itawgigdisplayer_pi1.php']);
}

?>