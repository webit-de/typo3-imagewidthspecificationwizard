<?php
/***************************************************************
*  Copyright notice
*
*  (c) Dan Untenzu <untenzu@webit.de>
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
 * Class which adds a wizard to imagewidthfield of the tt_content table
 *
 * @author		Dan Untenzu <untenzu@webit.de>
 * @package		TYPO3
 * @subpackage	tx_imagewidthspecificationwizard
 */
class tx_imagewidthspecificationwizard_wizard {
	var $extKey = 'imagewidthspecificationwizard'; // The extension key.

	/**
	 * Remember whether the current imagewidth eqals one of the preconfigured sizes
	 * @var bool
	 */
	protected $fieldsMatch = FALSE;

	/**
	 * Generate the HTML-code for the wizard attached next to every imagewidth-field within
	 * the TYPO3 backend
	 *
	 * @param	array		Parameter array for 'userFunc' wizard type
	 * @param	object		Parent object
	 * @return	string		Returns HTML for the wizard
	 */
	function main(&$params, &$pObj) {
		// fetch TSconfig/UserTSConfig for current page
		$modTSconfig = \TYPO3\CMS\Backend\Utility\BackendUtility::getModTSconfig($params['row']['pid'], 'tx_' . $this->extKey);
		ksort($modTSconfig['properties']['sizes.'], SORT_NUMERIC);

		// generate a unique id for the wizardfield
		$collide = substr( md5($params['md5ID'] . 'wizard_' . $this->extKey), 0, 10);

		$this->fieldsMatch = false;
		$options = $this->getOptions($modTSconfig, $params['row']['imagewidth']);
		// force script to hide the imagewidthfield if TSconfig says so
		if ($modTSconfig['properties']['ownValueDisabled'] == true) {
			$modTSconfig['properties']['hideFieldOnMatch'] = true;
			$this->fieldsMatch = true;
		}
		$JSonchange = $this->getJSonchange($params['row']['uid'], $params['itemName'], $modTSconfig['properties']['hideFieldOnMatch'], $modTSconfig['properties']['ownValueDisabled']);

		$content = $this->getSelectfield($params['itemName'], $collide, $JSonchange, $options);

		$JSpost = $this->getJSpost($params['itemName'], $modTSconfig['properties']['hideFieldOnMatch'], $this->fieldsMatch);
		$pObj->additionalJS_post[] = $JSpost;

		return $content;
	}

	/* Generate OPTION-tags for selectfield with values defined in TSconfig
	 *
	 * @param	array		the TSconfig of the current page
	 * @param	string		the current imagewidth of the contentelement
	 * @return	string		HTML-String with all needed OPTION-tags of the selectfield
	 */
	function getOptions($modTSconfig, $imagewidth){
		$options = '';

		if(TRUE === (empty($modTSconfig['properties']))) {
			$options = '<option value="--div--">'
				. \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('tt_content.tx_imagewidthspecificationwizard.configurationneeded', $this->extKey)
				. '</option>';
			return $options;
		}

		// generate list of predefined values
		foreach($modTSconfig['properties']['sizes.'] as $size => $description) {
			$selected = '';
			// option is »selected« if field imagewidth equals current size in loop
			if($size == $imagewidth) {
				$this->fieldsMatch = true;
				$selected = ' selected="selected"';
			}

			$options .= '<option value="' . $size . '" ' . $selected . '>' .
				$this->getLabel($description) . '</option>';
		}

		// prepend option to use an individual value (»--div--« is working as a flag for JavaScript)
		if ($modTSconfig['properties']['ownValueDisabled'] == false ||
			$modTSconfig['properties']['ownValueDisabled'] == true && ($this->fieldsMatch == false && !empty($imagewidth))) {
			$selected = ($this->fieldsMatch == false && !empty($imagewidth))? ' selected="selected"': '';
			$options = '<option value="--div--"' . $selected . '>'
				. $this->getLabel($modTSconfig['properties']['ownValueLabel'])
				. '</option>' . $options;
		}
		// prepend option to use no value / clear the field imagewidth (»0« triggers the JavaScript the clear the field automatically)
		if ($modTSconfig['properties']['noValueDisabled'] == false) {
			$options = '<option value="0">'
				. $this->getLabel($modTSconfig['properties']['noValueLabel'])
				. '</option>' . $options;
		}

		$this->fieldsMatch = ($this->fieldsMatch == false && empty($imagewidth))? true:$this->fieldsMatch;

		return $options;
	}

	/* Generate JavaScript for the onchange-handler of the selectfield
	 * The field imagewidth is supposed to be change every time a the user is selecting
	 * a different value in the wizard
	 *
	 * @param	string		The uid of the current contentelement
	 * @param	string		The distinct name of the imagewidth field in the current content element
	 * @param	bool		Hide the imagewidthfield if a configured value/width is selected (TSconfig)
	 * @param	bool		Allow the possibility to use an individual value for the imagewidth
	 * @return	string		Returns the valid JavaScript for the onchange-attribute
	 */
	function getJSonchange($uid, $fieldName, $hideField, $ownValueDisabled) {
		// Double escape JavaScript since its located inside of HTML-tag attributes wraped with quotes
		$content =
			// user has selected something else than "own value" (flag --div--) → change
			// the value of the imagewidth field
			'if (this.options[this.selectedIndex].value != \'--div--\') {
				document.editform[\'' . $fieldName . '\'].value = this.options[this.selectedIndex].value;' .
				// if configuration says so then the imagewidth field is shielded
				(($hideField)?
					'document.getElementsByName(\'' . $fieldName . '\')[0].parentNode.style.display = \'none\';'
					: ''
				) .
			'}' .
			// if configuration allows own values, than the original imagewidth field is restored
			((!$ownValueDisabled)?
				'else {
					document.getElementsByName(\'' . $fieldName . '\')[0].parentNode.style.display = \'inline\';
				}' : ''
			) .
			'typo3form.fieldGet(\'data[tt_content][' . $uid . '][imagewidth]\', \'int\', \'\', 1, \'0\');
			TBE_EDITOR.fieldChanged(\'tt_content\',\'' . $uid . '\', \'imagewidth\', \'data[tt_content][' . $uid . '][imagewidth]\');
			';
		return $content;
	}

	/* Generate the HTML for the selectfield
	 *
	 * @param	string		The distinct name of the imagewidth field in the current content element
	 * @param	string		Unique id for the wizardfield
	 * @param	string		JavaScript for the onchange-attribute of the select field
	 * @param	string		HTML string containing all options of the selectfield
	 * @return	string		HTML of the wizards selectfield
	 */
	function getSelectfield($fieldName, $collide, $JSonchange, $options) {
		$content = '<select onchange="' . $JSonchange . '"' .
			' name="_WIZARD' . $fieldName . '"' .
			' class="tceforms-select tceforms-wizardselect"' .
			' id="tceforms-select-' . $collide . '">' .
			$options .
			'</select>';
		return $content;
	}

	/* Hide the field imagewidth if TSconfig-option »hideFieldOnMatch« equals true and
	 * the current imagewidth eqals one of the preconfigured sizes
	 *
	 * @param	string		The distinct name of the imagewidth field in the current content element
	 * @param	bool		Hide the imagewidthfield if a given value/width is selected (TSconfig)
	 * @return	string		Returns the JavaScript for TYPO3 $additionalJS_post (appended to form)
	 */
	function getJSpost($fieldName, $hideField) {
		$content = '';
		if($hideField && $this->fieldsMatch) {
			$content = 'document.getElementsByName(\'' . $fieldName . '\')[0].parentNode.style.display = \'none\';';
		}
		return $content;
	}

	/* Get the label for each option - if string starts with 'LLL' it will be translated with LOCAL_LANG
	 *
	 * @param	string	The string to use for translation (will not be translated if it does not start with 'LLL', so use a full LLL-scheme like 'LLL:EXT:imagewidthspecificationwizard/locallang.xml:tt_content.tx_imagewidthspecificationwizard.ownValueLabel')
	 * @param	string	Alternative string to use, if no translation is found
	 * @return	string	The label string - either the original string or the translated value (translated / alternative string if no translation is found but an alternativ string is given / FALSE if no translation is found & an alternativ string isn't given)
	 */
	function getLabel($label, $labelAlternative = FALSE) {
		$content = $label;
		if(substr($label, 0, 3) == 'LLL') {
			$content = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($label, $this->extKey);
			if ($content == FALSE && !empty($labelAlternative)) {
				$content = $labelAlternative;
			}
		}
		return $content;
	}

}

?>