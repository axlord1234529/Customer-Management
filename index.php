<?php
require_once realpath("vendor/autoload.php");
require_once realpath('app/include/init.php');

use App\database\CustomerRepository;
use App\entity\Customer;

$customerRepository = new CustomerRepository();

//Adding a customer to the database
$customer = new Customer(0,"Teszt Elek","Petőfi Sándor utca 4","12345","valami@gmail.com");
$customer->addBillingAddress(0,"Isten tudja utca 12");
$customer->addBillingAddress(0,"Kovács utca 14","123",true);
$customer->addShippingAddress(0,"Ide küld utca 15",true);

if($customerRepository->addCustomer($customer))
{
    echo "<h2>Customer added:</h2>";
}

// Getting a customer from the database
$customerFromDb = $customerRepository->getCustomerById($customer->getId());
echo "<pre>";
var_dump($customerFromDb);
echo "</pre><br><br>";



//Adding addresses to customer and database
$customerRepository->addBillingAddress($customer,"Új számlázási cím");
$customerRepository->addShippingAddress($customer,'Új szállítási cím');

$customerFromDb = $customerRepository->getCustomerById($customer->getId());
echo "<h2>Same customer with the new addresses:</h2><pre>";
var_dump($customerFromDb);
echo "</pre>";




//Deleting address
$billingAddresses = $customer->getBillingAddresses();
$billingAddress = end($billingAddresses);
echo "<h2>This billing address will be deleted:</h2><pre>";
var_dump($billingAddress);
echo "</pre>";
$customerRepository->deleteBillingAddress($customer,$billingAddress['id']);
$billingAddressesFormDb = $customerRepository->getBillingAddresses($customer->getId());

echo "<h2>Customer's billing addresses from the database after deleting address above:</h2><pre>";
var_dump($billingAddressesFormDb);
echo "</pre>";

//Updating address
$shippingAddresses = $customer->getShippingAddresses();
$shippingAddress = end($shippingAddresses);

echo "<h2>This shipping address will be updated:</h2><pre>";
var_dump($shippingAddress);
echo "</pre>";

$shippingAddress['address'] = 'Új szállítási cím UPDATED';
$updated = $customerRepository->updateShippingAddress($customer,$shippingAddress['id'],$shippingAddress);
var_dump($updated);
$shippingAddressesFromDb = $customerRepository->getShippingAddresses($customer->getId());

echo "<h2>Customer's shipping addresses from database after update:</h2><pre>";
var_dump($shippingAddressesFromDb);
echo "</pre>";


// Updating customer
$customer->setName("Vicc Elek");
$customerId = $customer->getId();
$customerRepository->updateCustomer($customer);

$customerFromDb = $customerRepository->getCustomerById($customerId);

echo "<h2>Same customer from db with updated name:</h2><pre>";
var_dump($customerFromDb);
echo "</pre>";

// Deleting customer

if ($customerRepository->deleteCustomerById($customer->getId()))
{
    echo "<h2>Customer is deleted</h2>";
}

















