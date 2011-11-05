<?php
// autogenerated file 04.06.2009 09:55
// $Id: $
// $Log: $
//
require_once 'EbatNs_FacetType.php';

/**
 * This specifies the party (Seller/Buyer) who is going to pay the return shipping 
 * cost. 
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/ShippingCostPaidByOptionsCodeType.html
 *
 * @property string Buyer
 * @property string Seller
 * @property string CustomCode
 */
class ShippingCostPaidByOptionsCodeType extends EbatNs_FacetType
{
	const CodeType_Buyer = 'Buyer';
	const CodeType_Seller = 'Seller';
	const CodeType_CustomCode = 'CustomCode';

	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('ShippingCostPaidByOptionsCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_ShippingCostPaidByOptionsCodeType = new ShippingCostPaidByOptionsCodeType();

?>