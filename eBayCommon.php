<?php
error_reporting(E_ALL ^ E_NOTICE);

require_once EBAY_PLUGIN_ROOT.'/includes/ebayframework/EbatNs_ServiceProxy.php';
require_once EBAY_PLUGIN_ROOT.'/includes/ebayframework/EbatNs_Logger.php';

require_once EBAY_PLUGIN_ROOT.'/includes/ebayframework/GetMyeBaySellingRequestType.php';
require_once EBAY_PLUGIN_ROOT.'/includes/ebayframework/GetItemRequestType.php';

// for listing posting
require_once EBAY_PLUGIN_ROOT.'/includes/ebayframework/AddItemRequestType.php';
require_once EBAY_PLUGIN_ROOT.'/includes/ebayframework/AddItemResponseType.php';
require_once EBAY_PLUGIN_ROOT.'/includes/ebayframework/ItemType.php';
require_once EBAY_PLUGIN_ROOT.'/includes/ebayframework/ItemConditionCodeType.php';

define("EBAY_TITLE_LENGTH", 80);

class eBayController {
	public $debug;
	private $configPath;
	
	function __construct() {
		$this->debug = FALSE;
		$this->configPath = EBAY_DATA_FOLDER.'ebay.ini.php';
	}
	
	private function setLogger($serviceProxy) {
		if($this->debug) {
			$logger = new EbatNs_Logger(true);
			$logger->_debugXmlBeautify = true;
			$logger->_debugSecureLogging = false;
			
			$serviceProxy->attachLogger($logger);
		}
	}
	
	private function handleErrors($errors) {
		$errorOutput = "Call Failure\n";

		foreach ($errors as $error) {
			$errorOutput .= "#" . $error->getErrorCode() . " " . htmlentities($error->getShortMessage()) . "/" .htmlentities($error->getLongMessage()) . "\n";
		}
		
		if(IS_LOCAL_SERVER) echo nl2br($errorOutput);
		else trigger_error($errorOutput);
	}
	
	public function getActiveListingItems($page = 1) {		
		$session = new EbatNs_Session($this->configPath);
		$cs = new EbatNs_ServiceProxy($session);
		$req = new GetMyeBaySellingRequestType();
		
		$this->setLogger($cs);
		
		$pagination = new PaginationType();
		$pagination->setEntriesPerPage(200);
		$pagination->setPageNumber($page);
		
		// Set the Active List inputs and filters  
		$activeList = new ItemListCustomizationType();
		$activeList->setPagination($pagination);
		$req->setActiveList($activeList);
		
		$response = $cs->GetMyeBaySelling($req);

		if ($response->getAck() == AckCodeType::CodeType_Success) {
			$activeList = $response->getActiveList();
			
			if(!$activeList) {
				// when the list is completely empty
				return array();
			}
			
			$pagination = $activeList->getPaginationResult();
			$itemNumbers = array();
			
			if($activeList) {
				$itemArray = $activeList->GetItemArray();			

				foreach($itemArray as $item) {
					$itemNumbers[] = $item->ItemID;
				}
			}

			if($pagination->getTotalNumberOfPages() == $page) {
				return $itemNumbers;
			} else {
				// array_unique?
				return array_merge($this->getActiveListingItems($page + 1), $itemNumbers);
			}
			
		} else {
			$this->handleErrors($response->getErrors());
		}
	}
	
	public function getItemInfo($itemNumber) {		
		$session = new EbatNs_Session($this->configPath);
		$cs = new EbatNs_ServiceProxy($session);
		
		$this->setLogger($cs);
		
		$request = new GetItemRequestType();
		$request->setItemID($itemNumber);
		$request->setDetailLevel(DetailLevelCodeType::CodeType_ReturnAll);
		$response = $cs->GetItem($request);
		
		if($response->getAck() == AckCodeType::CodeType_Success) {
			return $response->getItem();
		} else {
			$this->handleErrors($response->getErrors());
		}
	}
	
	public function postListing(Array $listingData, $additionalConfig = array()) {		
		$session = new EbatNs_Session($this->configPath);
		$cs = new EbatNs_ServiceProxy($session);
		
		$this->setLogger($cs);
		
		$request = new AddItemRequestType();
		$item = new ItemType();
		
		// this information must be customized per client
		// here a quick list of information that must be changed:
		//		* Category ID
		//		* Store URL
		//		* Shipping
		//		* Payment
		//		* Location
		//		* Store ID & URL
		// the ListingType should be "FixedPriceItem" when the person does use a store
		
		// some useful links:
		// http://art.listings.ebay.com/_W0QQloctZShowCatIdsQQsacatZ550QQsalocationZatsQQsocmdZListingCategoryOverview - category listings
		// http://cgi6.ebay.com/ws/eBayISAPI.dll?StoreCategoryMgmt&rCode=SC.BIZ.MSG.CATS_CREATED store categories
		
		$sellerConfig = parse_ini_file(EBAY_DATA_FOLDER.'seller.ini.php', TRUE);
		
		if(!empty($additionalConfig) && is_array($additionalConfig)) {
			$sellerConfig = array_replace_recursive($sellerConfig, $additionalConfig);
		}

		// setup the store information
		
		$storeInformation = new StorefrontType();
		$storeInformation->StoreCategoryID = $sellerConfig['store']['category'];
		if(!empty($sellerConfig['store']['category2'])) $storeInformation->StoreCategory2ID = $sellerConfig['store']['category2'];
		$storeInformation->StoreURL = $sellerConfig['store']['url'];
		$item->setStorefront($storeInformation);

		// Set the Auction Starting Price and Buy It Now Price
		$item->StartPrice = new AmountType();
		$item->StartPrice->setTypeValue($listingData['price']);
		$item->StartPrice->setTypeAttribute('currencyID', 'USD');

		$item->ShipToLocations = "US";		
		$item->setDispatchTimeMax($sellerConfig['shipping']['time']); // this is the handling time
		$item->ShippingDetails = new ShippingDetailsType();
		// $item->ShippingDetails->PaymentInstructions = "";
		
		$shipping = new ShippingServiceOptionsType();
		$shipping->ShippingServiceCost = new AmountType();
		$shipping->ShippingServiceCost->setTypeValue($sellerConfig['shipping']['cost']);
		$shipping->ShippingService = $sellerConfig['shipping']['method'];

		if((float) $sellerConfig['shipping']['cost'] == 0) {
			$shipping->FreeShipping = true;
		}
		
		// $shipping->setShippingTimeMin(2);
		// $shipping->setShippingTimeMax(3);
		$item->ShippingDetails->setShippingServiceOptions($shipping);
		
		// return policy & handling time is now required
		
		/*
			example return policy:
			<ReturnPolicy>
                <RefundOption>Exchange</RefundOption>
                <Refund>Exchange</Refund>
                <ReturnsWithinOption>Days_7</ReturnsWithinOption>
                <ReturnsWithin>7 Days</ReturnsWithin>
                <ReturnsAcceptedOption>ReturnsAccepted</ReturnsAcceptedOption>
                <ReturnsAccepted>Returns Accepted</ReturnsAccepted>
                <ShippingCostPaidByOption>Buyer</ShippingCostPaidByOption>
                <ShippingCostPaidBy>Buyer</ShippingCostPaidBy>
            </ReturnPolicy>
        */
        
		$returnPolicy = new ReturnPolicyType();
		$returnPolicy->setRefundOption($sellerConfig['refund']['option']);
		$returnPolicy->setRefund($sellerConfig['refund']['option']);
		$returnPolicy->setReturnsWithinOption($sellerConfig['refund']['within']);
		$returnPolicy->setReturnsWithin($sellerConfig['refund']['within']);
		$returnPolicy->setReturnsAcceptedOption($sellerConfig['refund']['returns']);
		$returnPolicy->setReturnsAccepted($sellerConfig['refund']['returns']);
		$returnPolicy->setDescription($sellerConfig['refund']['description']);
		$returnPolicy->setShippingCostPaidByOption($sellerConfig['refund']['paidby']);
		$returnPolicy->setShippingCostPaidBy($sellerConfig['refund']['paidby']);
		$item->ReturnPolicy = $returnPolicy;
		
		// set condition
		// http://developer.ebay.com/devzone/finding/callref/Enums/conditionIdList.html
		$item->setConditionID(1000);

		// $item->BuyItNowPrice = new AmountType();
		// $item->BuyItNowPrice->setTypeValue($listingData['price']);
		// $item->BuyItNowPrice->setTypeAttribute('currencyID', 'USD');
		
		// add prefix to the title if we have space
		$titlePrefix = $sellerConfig['listing']['title_prefix'];
		
		if(strlen($titlePrefix) + strlen($listingData['title']) > EBAY_TITLE_LENGTH) {
			$listingTitle = substr($titlePrefix.$listingData['title'], 0, EBAY_TITLE_LENGTH);
		} else {
			$listingTitle = $titlePrefix.$listingData['title'];
		}
		
		// Set the Item Title and Description
		$item->Description = $listingData['description'];
		$item->Title = $listingTitle;
		
		// picture information
		
		/* Example Data:
		    <PictureDetails>
                <GalleryType>Gallery</GalleryType>
                <GalleryURL>http://www.postersoftheday.com/IMP/ST4564R.jpg</GalleryURL>
                <PhotoDisplay>None</PhotoDisplay>
                <PictureURL>http://www.postersoftheday.com/IMP/ST4564R.jpg</PictureURL>
                <PictureSource>Vendor</PictureSource>
            </PictureDetails>
        */

		$picture = new PictureDetailsType();
		$picture->GalleryType = "Gallery";
		$picture->GalleryURL = $picture->PictureURL = $listingData['image_url'];
		$picture->PictureSource = "Vendor";
		$picture->PhotoDisplay = "None";
		
		$item->PictureDetails = $picture;

		// Set Listing Properties
		// for non-store listings Days_7, for store listing GTC
		$item->ListingDuration = $sellerConfig['listing']['duration'];
		$item->ListingType = $sellerConfig['listing']['type']; // other options: Chinese
		$item->Quantity = $sellerConfig['listing']['quantity'];

		// Set Local Info
		$item->Currency = 'USD';
		$item->Country = 'US';
		$item->Location = $sellerConfig['store']['location'];
		$item->Site = 'US';

		$item->PrimaryCategory = new CategoryType();
		$item->PrimaryCategory->CategoryID = empty($listingData['category_id']) ? $sellerConfig['listing']['category'] : $listingData['category_id'];

		// Set Payment Methods
		// $item->PaymentMethods[] = 'PersonalCheck';
		// $item->PaymentMethods[] = 'MOCC';
		$item->setPaymentMethods('PayPal');
		$item->setPayPalEmailAddress($sellerConfig['payment']['paypal']);

		$request->Item = $item;
		$response = $cs->AddItem($request);

		if($response->getAck() == AckCodeType::CodeType_Success || $response->getAck() == AckCodeType::CodeType_Warning) {
			return $response;
		} else {
			$this->handleErrors($response->getErrors());
			return false;
		}
	}
	
	public function getSiteDomain() {
		$config = parse_ini_file($this->configPath);
		
		if($config['app-mode'] == 0) {
			return "ebay.com";
		} else {
			return "sandbox.ebay.com";
		}
	}
}

$eBayController = new eBayController();
?>