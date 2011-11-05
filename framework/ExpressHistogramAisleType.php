<?php
// autogenerated file 04.06.2009 09:55
// $Id: $
// $Log: $
//
//
require_once 'ExpressHistogramProductType.php';
require_once 'EbatNs_ComplexType.php';
require_once 'ExpressHistogramDomainDetailsType.php';

/**
 * Details about an Express aisle and matching item and catalog product countsin 
 * that aisle, if any. 
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/ExpressHistogramAisleType.html
 *
 */
class ExpressHistogramAisleType extends EbatNs_ComplexType
{
	/**
	 * @var ExpressHistogramDomainDetailsType
	 */
	protected $DomainDetails;
	/**
	 * @var ExpressHistogramProductType
	 */
	protected $ProductType;

	/**
	 * @return ExpressHistogramDomainDetailsType
	 */
	function getDomainDetails()
	{
		return $this->DomainDetails;
	}
	/**
	 * @return void
	 * @param ExpressHistogramDomainDetailsType $value 
	 */
	function setDomainDetails($value)
	{
		$this->DomainDetails = $value;
	}
	/**
	 * @return ExpressHistogramProductType
	 * @param integer $index 
	 */
	function getProductType($index = null)
	{
		if ($index !== null) {
			return $this->ProductType[$index];
		} else {
			return $this->ProductType;
		}
	}
	/**
	 * @return void
	 * @param ExpressHistogramProductType $value 
	 * @param  $index 
	 */
	function setProductType($value, $index = null)
	{
		if ($index !== null) {
			$this->ProductType[$index] = $value;
		} else {
			$this->ProductType = $value;
		}
	}
	/**
	 * @return void
	 * @param ExpressHistogramProductType $value 
	 */
	function addProductType($value)
	{
		$this->ProductType[] = $value;
	}
	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('ExpressHistogramAisleType', 'urn:ebay:apis:eBLBaseComponents');
		if (!isset(self::$_elements[__CLASS__]))
				self::$_elements[__CLASS__] = array_merge(self::$_elements[get_parent_class()],
				array(
					'DomainDetails' =>
					array(
						'required' => false,
						'type' => 'ExpressHistogramDomainDetailsType',
						'nsURI' => 'urn:ebay:apis:eBLBaseComponents',
						'array' => false,
						'cardinality' => '0..1'
					),
					'ProductType' =>
					array(
						'required' => false,
						'type' => 'ExpressHistogramProductType',
						'nsURI' => 'urn:ebay:apis:eBLBaseComponents',
						'array' => true,
						'cardinality' => '0..*'
					)
				));
	}
}
?>