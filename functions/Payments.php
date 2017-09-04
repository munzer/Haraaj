<?
require 'functions/PayPalSdk/autoload.php';

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Api\PaymentExecution;

	Class Payments{
		
		// This Function Check Payment Details Using Paypal
		function StartPayPalTransaction($Aragments){	
		
			$apiContext = new \PayPal\Rest\ApiContext(
			 new \PayPal\Auth\OAuthTokenCredential(
			settings("PayPalClientId"),     // ClientID
			settings("PayPalClientSecret")      // ClientSecret
			)
			);
	
			$apiContext->setConfig([
				'mod'                   => $Aragments["Mod"],
				'http.ConnetionTimeOut' => '30',
				'log.logEnabled'        => false,
				'log.FileName'          => '',
				'log.logLevel'          => 'FINE',
				'validation.level'      => 'log'
			]);
			
			
			$payment = Payment::get($Aragments["PaymentId"], $apiContext);
			$execution = new PaymentExecution();
			$execution->setPayerId($Aragments['PayerID']);
			
			try {
				$payment->execute($execution,$apiContext);
			} catch (Exception $ex) {
			}
			
			return array(
				"Id"     =>$payment->getId(),
				"State"  =>$payment->getState(),
				"Amount" =>$payment->transactions[0]->amount->total
			);
		}
		
		// This Function Create Paypal Pay Link
		function CreatePaypalPayLink($Aragments){
			
				$apiContext = new \PayPal\Rest\ApiContext(
					 new \PayPal\Auth\OAuthTokenCredential(
						settings("PayPalClientId"),     // ClientID
						settings("PayPalClientSecret")      // ClientSecret
					 )
				);
				
					$apiContext->setConfig([
						'mod'                   => $Aragments["Mod"],
						'http.ConnetionTimeOut' => '30',
						'log.logEnabled'        => false,
						'log.FileName'          => '',
						'log.logLevel'          => 'FINE',
						'validation.level'      => 'log'
					]);
					
							$payer       = new Payer();
							$details     = new Details();
							$amount      = new Amount();
							$transaction = new Transaction();
							$payment     = new Payment();
							$redirectUrl = new RedirectUrls();
							$payer->setPaymentMethod('paypal');

								// Details
								$details->setTax($Aragments["Tax"])
								->setShipping($Aragments["Shipping"])
								->setSubtotal($Aragments["Subtotal"]);

								// Amount
								$amount->setCurrency('USD')
								->setTotal($Aragments["Total"])
								->setDetails($details);

								// Transaction
								$transaction->setAmount($amount)
								->setDescription($Aragments["Description"]);

								// Payment
								$payment->setIntent('sale')
								->setPayer($payer)
								->setTransactions([$transaction]);

								// Redirect Urls
								$redirectUrl->setReturnUrl($Aragments["TrueUrl"])
								->setCancelUrl($Aragments["FalseUrl"]);

								$payment->setRedirectUrls($redirectUrl);

								try {
								    $payment->create($apiContext);
								} catch (PayPal\Exception\PayPalConnectionException $ex) {
								    echo $ex->getCode(); // Prints the Error Code
								    echo $ex->getData(); // Prints the detailed error message 
								    die($ex);
								} catch (Exception $ex) {
								    die($ex);
								}
								
								$OutPutLink = "";
								foreach($payment->getLinks() as $Link){
									if($Link->getRel() == 'approval_url'){
										$OutPutLink  = $Link->getHref();
									}
								}
				return $OutPutLink;

		}
		
		
		// Function To Complete Payment (Last Step)
		function Create($Aragments){
		global $condb;
		global $sessions;
		
			$TransactionId = $Aragments["TransactionId"];
			$Token         = $Aragments["Token"];
			$PayerID       = $Aragments["PayerID"];
			$User          = $Aragments["User"];
			$State         = $Aragments["State"];
			$Amount        = $Aragments["Amount"];
			$Item          = $Aragments["Item"];
			$Method        = $Aragments["Method"];
			$EndDate       = $Aragments["EndDate"];
			$TrueUrl       = $Aragments["TrueUrl"];
			$FalseUrl      = $Aragments["FalseUrl"];
			
			// Validation Of Parameters
			if(!isset($Aragments["Method"]))    { return "NoMethodPassed"; }
			if(!isset($Aragments["User"]))      { return "NoUserPassed"; }
			if(!isset($Aragments["User"]))      { return "NoUserPassed"; }
			if(!isset($Aragments["Item"]))      { return "NoItemPassed"; }
			if(!isset($Aragments["Amount"]))    { return "NoAmountPassed"; }
			
	
			// If Buy Complete By PayPal
			if($Method == "PayPal"){
				
				if(sql_count("payments"," TransactionId='$TransactionId' ") > 0){
					
					return false;
						
				}else{
				$Payment     =insert("payments",
						array(
							"payment_user"             => $User,
							"payment_method"           => $Method,
							"payment_amount"           => $Amount,
							"payment_item"             => $Item,
							"payment_end_date"         => $EndDate,
							"payment_date"             => time(),
							"payment_state"            => $State,
							"payment_payer_id"         => $PayerID,
							"payment_transaction_id"   => $TransactionId,
							"payment_token"            => $Token
							)
					);
				
				// If User Pass Redirect Url ... The System Well Redirect You Directly to it
				if(isset($TrueUrl) && isset($FalseUrl)){
					if($Payment){
						header("location:$TrueUrl");
					}else{
						header("location:$FalseUrl");
					}
				}else{
					return true;
				}
					
				}
				
				
			}
		}
		
		
		
		// Subscriptions And Prices
		function Planes($Plane){
			// Prices Array (This is a Demo Values , Doesnt Matter About !!)
			$DayPackage        = array("Item"=>"DayPackage","Amount"=>settings("DayPackagePrice"),"EndDate"=>time()+86400);
			$MonthPackage      = array("Item"=>"MonthPackage","Amount"=>settings("MonthPackagePrice"),"EndDate"=>time()+2592000);
			$ThreeMonthPackage = array("Item"=>"ThreeMonthPackage","Amount"=>settings("ThreeMonthPackagePrice"),"EndDate"=>time()+7776000);
			$SixMonthPackage   = array("Item"=>"SixMonthPackage","Amount"=>settings("SixMonthPackagePrice"),"EndDate"=>time()+15552000);
			$YearPackage       = array("Item"=>"YearPackage","Amount"=>settings("YearPackagePrice"),"EndDate"=>time()+31536000);
			$AllPackages       = array(
							0=>$DayPackage,
							1=>$MonthPackage,
							2=>$ThreeMonthPackage,
							3=>$SixMonthPackage,
							4=>$YearPackage
						);
							
			     if($Plane == "DayPackage"){        return $DayPackage; }
			else if($Plane == "MonthPackage"){      return $MonthPackage; }
			else if($Plane == "ThreeMonthPackage"){ return $ThreeMonthPackage; }
			else if($Plane == "SixMonthPackage"){   return $SixMonthPackage; }
			else if($Plane == "YearPackage"){       return $YearPackage; }
			else if($Plane == "*"){                 return $AllPackages;}
			else {                                  return "NotFound";  }
		}
		
		
		
		
		
	}


?>