<?php
// autogenerated file 04.06.2009 09:55
// $Id: $
// $Log: $
//
//
require_once 'AbstractRequestType.php';

/**
 * Returns contact information for a specified user if abidding relationship 
 * exists.The bidder must be bidding on the seller's activeitem, or an eBay user 
 * must have made an offer onthe item using Best Offer.The item must be in the 
 * Motors or Business & Industrial categories.Bidders can use this call to 
 * contactsellers of an item they are bidding on or have made anoffer on (through 
 * Best Offer).Note that this call does NOT return an email address.Sellers who 
 * wish to send anemail to bidders should use AddMemberMessagesAAQToBidder. 
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/GetUserContactDetailsRequestType.html
 *
 */
class GetUserContactDetailsRequestType extends AbstractRequestType
{
	/**
	 * @var string
	 */
	protected $ItemID;
	/**
	 * @var string
	 */
	protected $ContactID;
	/**
	 * @var string
	 */
	protected $RequesterID;

	/**
	 * @return string
	 */
	function getItemID()
	{
		return $this->ItemID;
	}
	/**
	 * @return void
	 * @param string $value 
	 */
	function setItemID($value)
	{
		$this->ItemID = $value;
	}
	/**
	 * @return string
	 */
	function getContactID()
	{
		return $this->ContactID;
	}
	/**
	 * @return void
	 * @param string $value 
	 */
	function setContactID($value)
	{
		$this->ContactID = $value;
	}
	/**
	 * @return string
	 */
	function getRequesterID()
	{
		return $this->RequesterID;
	}
	/**
	 * @return void
	 * @param string $value 
	 */
	function setRequesterID($value)
	{
		$this->RequesterID = $value;
	}
	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('GetUserContactDetailsRequestType', 'urn:ebay:apis:eBLBaseComponents');
		if (!isset(self::$_elements[__CLASS__]))
				self::$_elements[__CLASS__] = array_merge(self::$_elements[get_parent_class()],
				array(
					'ItemID' =>
					array(
						'required' => false,
						'type' => 'string',
						'nsURI' => 'http://www.w3.org/2001/XMLSchema',
						'array' => false,
						'cardinality' => '0..1'
					),
					'ContactID' =>
					array(
						'required' => false,
						'type' => 'string',
						'nsURI' => 'http://www.w3.org/2001/XMLSchema',
						'array' => false,
						'cardinality' => '0..1'
					),
					'RequesterID' =>
					array(
						'required' => false,
						'type' => 'string',
						'nsURI' => 'http://www.w3.org/2001/XMLSchema',
						'array' => false,
						'cardinality' => '0..1'
					)
				));
	}
}
?>
