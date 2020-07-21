<?php
/**
 * Helper class for Hello World! module
 * 
 * @package    Joomla.Tutorials
 * @subpackage Modules
 * @link http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * @license        GNU/GPL, see LICENSE.php
 * mod_helloworld is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
 
 defined('_JEXEC') or die();


class GetCats //extends PhocacartCategory
{
    
  private static $categoryA = array();  
    
public static function categoryTree($d, $r = 0, $pk = 'parent_id', $k = 'id', $c = 'children') {
		$m = array();
		foreach ($d as $e) {
			isset($m[$e[$pk]]) ?: $m[$e[$pk]] = array();
			isset($m[$e[$k]]) ?: $m[$e[$k]] = array();
			$m[$e[$pk]][] = array_merge($e, array($c => &$m[$e[$k]]));
		}
		//return $m[$r][0]; // remove [0] if there could be more than one root nodes
		if (isset($m[$r])) {
			return $m[$r];
		}
		return 0;
}

public static function getCategoryTreeArray($ordering = 1, $display = '', $hide = '', $type = array(0,1), $lang = '', $limitCount = -1) {

		$cis = str_replace(',', '', 'o'.$ordering .'d'. $display .'h'. $hide . 'l'. $lang . 'c'.$limitCount);
		if( empty(self::$categoryA[$cis])) {
			/*phocacart import('phocacart.ordering.ordering');*/
			$itemOrdering 	= PhocacartOrdering::getOrderingText($ordering,1);
			$db 			= JFactory::getDBO();
			$wheres			= array();
			$user 			= PhocacartUser::getUser();
			$userLevels		= implode (',', $user->getAuthorisedViewLevels());
			$userGroups 	= implode (',', PhocacartGroup::getGroupsById($user->id, 1, 1));
			$wheres[] 		= " c.access IN (".$userLevels.")";
			$wheres[] 		= " (gc.group_id IN (".$userGroups.") OR gc.group_id IS NULL)";
			$wheres[] 		= " c.published = 1";

			if ($lang != '' && $lang != '*') {
				$wheres[] 	= PhocacartUtilsSettings::getLangQuery('c.language', $lang);
			}

			if (!empty($type) && is_array($type)) {
				$wheres[] = " c.type IN (".implode(',', $type).")";
			}

			if ($display != '') {
				$wheres[] = " c.id IN (".$display.")";
			}
			if ($hide != '') {
				$wheres[] = " c.id NOT IN (".$hide.")";
			}

			if ((int)$limitCount > -1) {
				$wheres[] = " c.count_products > ".(int)$limitCount;
			}

			$query = 'SELECT c.id, c.title, c.alias, c.parent_id, c.icon_class, c.image, c.description, c.count_products'
			. ' FROM #__phocacart_categories AS c'
			. ' LEFT JOIN #__phocacart_item_groups AS gc ON c.id = gc.item_id AND gc.type = 2'// type 2 is category
			. ' WHERE ' . implode( ' AND ', $wheres )
			. ' ORDER BY '.$itemOrdering;
			$db->setQuery( $query );
			$items 						= $db->loadAssocList();
			print_r ($items);
		//	self::$categoryA[$cis]	= self::categoryTree($items);
			self::$categoryA[$cis]	= ($items);
		}
		return self::$categoryA[$cis];
	}
}	