<?php

use PhpEws\ExchangeWebServices;
use PhpEws\EWSType\MessageType;
use PhpEws\EWSType\EmailAddressType;
use PhpEws\EWSType\SingleRecipientType;
use PhpEws\EWSType\BodyType;
use PhpEws\EWSType\CreateItemType;
use PhpEws\EWSType\NonEmptyArrayOfAllItemsType;

include  __DIR__."/vendor/autoload.php";
$PhpEwsPath = __DIR__."/PhpEws/";



require_once $PhpEwsPath.'NTLMSoapClient.php';
require_once $PhpEwsPath.'NTLMSoapClient/Exchange.php';
require_once $PhpEwsPath.'EWSException.php';
require_once $PhpEwsPath.'\EWSType/EWSType.php';
require_once $PhpEwsPath.'EWSType/MessageType.php';
require_once $PhpEwsPath.'EWSType/EmailAddressType.php';
require_once $PhpEwsPath.'EWSType/BodyType.php';
require_once $PhpEwsPath.'EWSType/SingleRecipientType.php';
require_once $PhpEwsPath.'EWSType/CreateItemType.php';
require_once $PhpEwsPath.'EWSType/NonEmptyArrayOfAllItemsType.php';
require_once $PhpEwsPath.'EWSType/ItemType.php';    

$server = 'smtp.office365.com';
$username = 'sistema@sapucai.com.ar';
$password = 'Leo181979';  
$ews = new ExchangeWebServices($server, $username, $password);

$msg = new MessageType();

$toAddresses = array();
$toAddresses[0] = new EmailAddressType();
$toAddresses[0]->EmailAddress = 'leochabur@gmail.com';
$toAddresses[0]->Name = 'John Doe';   

/*$toAddresses[1] = new \EWSType_EmailAddressType();
$toAddresses[1]->EmailAddress = 'email2@example.com';
$toAddresses[1]->Name = 'Richard Roe';  

$toAddresses[2] = new \EWSType_EmailAddressType();
$toAddresses[2]->EmailAddress = 'email3@example.com';
$toAddresses[2]->Name = 'Hustle and Flow';        

$toAddresses[3] = new \EWSType_EmailAddressType();
$toAddresses[3]->EmailAddress = 'email4@example.com';
$toAddresses[3]->Name = 'Crookedeye Moe';*/     

$msg->ToRecipients = $toAddresses;

$fromAddress = new EmailAddressType();
$fromAddress->EmailAddress = 'sistema@sapucai.com.ar';
$fromAddress->Name = 'Sistema';

$msg->From = new SingleRecipientType();
$msg->From->Mailbox = $fromAddress;

$msg->Subject = 'Test email message from RAS';

$msg->Body = new BodyType();
$msg->Body->BodyType = 'HTML';
$msg->Body->_ = '<p style="font-size: 18px; font-weight: bold;">Test email message from php ews library from RAS.</p>';

$msgRequest = new CreateItemType();
$msgRequest->Items = new NonEmptyArrayOfAllItemsType();
$msgRequest->Items->Message = $msg;
$msgRequest->MessageDisposition = 'SendAndSaveCopy';
$msgRequest->MessageDispositionSpecified = true;

$response = $ews->CreateItem($msgRequest);
var_dump($response);        

?>