<?php
// autogenerated file 04.06.2009 09:55
// $Id: $
// $Log: $
//
//
require_once 'AbstractRequestType.php';

/**
 * Retrieves Selling Manager alerts.For Selling Manager applications only.This call 
 * is subject to change without notice; the deprecation process isinapplicable to 
 * this call. For moreinformation about writing Selling Manager applications, 
 * please see <a 
 * href="http://developer.ebay.com/products/selling-manager-applications/"target="_blank">http://developer.ebay.com/products/selling-manager-applications</a>. 
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GetSellingManagerAlertsRequestType.html
 *
 */
class GetSellingManagerAlertsRequestType extends AbstractRequestType
{

	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('GetSellingManagerAlertsRequestType', 'urn:ebay:apis:eBLBaseComponents');
		if (!isset(self::$_elements[__CLASS__])) {
			self::$_elements[__CLASS__] = array();
		}
	}
}
?>