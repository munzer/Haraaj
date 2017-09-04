<? 

include("functions/master.php");

?>




<html>

<head>
	<style>
	.Pagination{
	width:40%;
	height:50px;
	float:right;
	background:#ccc;
	margin:100px 30%;
	}	
	
	.Pagination button{
	width:40px;
	height:40px;
	font-size:14px;
	font-weight:bold;
	float:right;
	margin:5px;
	border-radius:100px;
	border:1px solid #777;
	background:#DE5B25;
	color:#fff;
	text-align:center;
	}	
	</style>
	<script type="text/javascript" src="jq.js"></script>
</head>

<body>

<script>


	
	$(document).on("click",".Pagination button",function(){
		MyCurrentPage = $(this).text();
		Class.Build({
			Total         : Total,
			PerPage       : PerPage,
			CurrentPage   : MyCurrentPage,
			ButtonsNumber : ButtonsNumber
		});
	});

}
</script>

<div class="Pagination">

</div>

<script>
$(document).ready(function(){
	MyCurrentPage = 1;
	
	Pagination = new Pagination({ Contener:".Pagination" });
	
	Pagination.Build({
		Total         : 100,
		PerPage       : 10,
		CurrentPage   : MyCurrentPage,
		ButtonsNumber : 10
	});
});



</script>

</body>

</html>


<?


/*
require 'functions/PayPalSdk/autoload.php';

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;


$apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            'AT5lJXzrubdRJ559CKyt9fmSsOQql3Ilvo58EwOoHf84oNZ0Brlxr37PP0NPwR52Yk4lbFjPrekRy7ud',     // ClientID
            'EH2ObpAa187ssSAN_DI8e-UzAuuRFyzPAaAPN9bIuQvmC9s2Of93qjtVJudNrGx725Aq541tPEXqKFo3'      // ClientSecret
        )
);



$apiContext->setConfig([
	'mod'                   => 'sandbox',
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
$details->setTax('0.0')
->setShipping('0.00')
->setSubtotal('20.00');

// Amount
$amount->setCurrency('USD')
->setTotal('21.00')
->setDetails($details);

// Transaction
$transaction->setAmount($amount)
->setDescription('menmbership');

// Payment
$payment->setIntent('sale')
->setPayer($payer)
->setTransactions([$transaction]);

// Redirect Urls
$redirectUrl->setReturnUrl('http://127.0.0.1/LiveStreem/example.php?state=true')
->setCancelUrl('http://127.0.0.1/LiveStreem/example.php?state=false');

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


echo"<pre>";
var_dump($payment->getLinks());
*/
?>