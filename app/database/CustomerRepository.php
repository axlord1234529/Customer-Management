<?php

namespace App\database;

use App\entity\Customer;
class CustomerRepository
{
    private $db;

    public function __construct()
    {
        $this->db = Db::getInstance();
    }

    public function getALlCustomers()
    {
        $customers = array();
        $results = $this->db->get(CUSTOMER_TABLE);
        foreach ($results as $result) {
            $customer = new Customer($result['id'], $result['name'], $result['address'], $result['password'], $result['email']);
            $this->setAddresses($customer);
            $customers[] = $customer;
        }
        return $customers;
    }

    public function getCustomerById($customerId)
    {
        if (!is_int($customerId)) {
            throw new \InvalidArgumentException("Argument must be integer");
        }
        $customer = null;

        $result = $this->db->get(CUSTOMER_TABLE, array('id', '=', strval($customerId)));
        if ($result) {
            $result = array_shift($result);
            $customer = new Customer($result['id'], $result['name'], $result['address'], $result['password'], $result['email']);

        }
        $this->setAddresses($customer);
        return $customer;
    }

    public function updateCustomer(Customer $customer)
    {
        $data = array(
            'name' => $customer->getName(),
            'address' => $customer->getAddress(),
            'password' => $customer->getPassword(),
            'email' => $customer->getEmail()
        );
        $result = $this->db->update(CUSTOMER_TABLE, $data, array('id', '=', $customer->getId()));

        if (!$result) return false;
        // Update billing addresses
        $billingAddresses = $customer->getBillingAddresses();
        foreach ($billingAddresses as $billingAddress) {
            $billingData = array(
                'address' => $billingAddress['address'],
                'tax_number' => $billingAddress['tax_number'],
                'is_default' => $billingAddress['is_default']
            );
            $result = $this->db->update(BILLING_ADDRESSES_TABLE, $billingData, array('id', '=', $billingAddress['id']));
            if (!$result) return false;
        }

        // Update shipping addresses
        $shippingAddresses = $customer->getShippingAddresses();
        foreach ($shippingAddresses as $shippingAddress) {
            $shippingData = array(
                'address' => $shippingAddress['address'],
                'is_default' => $shippingAddress['is_default']
            );
            $result = $this->db->update(SHIPPING_ADDRESSES_TABLE, $shippingData, array('id', '=', $shippingAddress['id']));
            if (!$result) return false;
        }
        return true;
    }

    public function addCustomer(Customer $customer)
    {

        $customerIsInserted = $this->db->insert(CUSTOMER_TABLE,array('id'=>'', 'name'=>$customer->getName(),
            'address'=>$customer->getAddress(),'password'=>$customer->getPassword(),'email'=>$customer->getEmail()));
        if (!$customerIsInserted) return false;

        $customer->setId(intval($this->db->lastInsertId()));

        $billingAddresses = $customer->getBillingAddresses();
        $shippingAddresses = $customer->getShippingAddresses();

        if(!empty($billingAddresses))
        {

            foreach ($billingAddresses as $billingAddress)
            {
                $isInserted = $this->db->insert(BILLING_ADDRESSES_TABLE,array('id'=>0,'customer_id' =>$customer->getId(),
                    'address'=>$billingAddress['address'],'tax_number'=>$billingAddress['tax_number'],'is_default'=>$billingAddress['is_default']));

                if(!$isInserted) return false;
                $billingAddress['id'] = $this->db->lastInsertId();

            }
        }

        if(!empty($shippingAddresses))
        {
            foreach ($shippingAddresses as $shippingAddress)
            {
                $isInserted = $this->db->insert(SHIPPING_ADDRESSES_TABLE,array('id'=>0,'customer_id' =>$customer->getId(),
                    'address'=>$shippingAddress['address'],'is_default'=>$shippingAddress['is_default']));
                if(!$isInserted) return false;
                $shippingAddress['id'] = $this->db->lastInsertId();
            }
        }

        return true;
    }

    public function deleteCustomerById($customerId)
    {
        if (!is_int($customerId))
        {
            throw new \InvalidArgumentException("Argument must be an integer");
        }

        return $this->db->delete(CUSTOMER_TABLE,array('id','=',$customerId));
    }

    public function getBillingAddresses($customerId)
    {
        if (!is_int($customerId))
        {
            throw new \InvalidArgumentException("Argument must be an integer");
        }
        return $this->db->get(BILLING_ADDRESSES_TABLE,array('customer_id','=',strval($customerId)));
    }

    public function addBillingAddress(Customer $customer,$address, $taxNumber = null, $default = false)
    {
        $inserted = $this->db->insert(BILLING_ADDRESSES_TABLE,array('id'=>0,'customer_id'=>$customer->getId(),
            'address'=>$address,'tax_number'=>$taxNumber,'is_default'=>$default));
        if(!$inserted) return false;
        $customer->addBillingAddress($this->db->lastInsertId(),$address,$taxNumber,$default);
        return $inserted;
    }

    public function deleteBillingAddress(Customer $customer,$addressId) {

        $rowCount = $this->db->delete(BILLING_ADDRESSES_TABLE, array('id', '=', $addressId));
        if ($rowCount > 0)
        {
            $customer->deleteBillingAddress($addressId);
        }
        return $rowCount;
    }

    public function updateBillingAddress(Customer $customer, $billingAddressId, array $updatedBillingAddress)
    {
        $fields = $updatedBillingAddress;
        $fields['id'] = $billingAddressId;
        $where = array('id', '=', $billingAddressId);

        if ($this->db->update(BILLING_ADDRESSES_TABLE, $fields, $where)) {
            $customer->setShippingAddresses($this->getShippingAddresses($customer->getId()));
            return $this->updateCustomer($customer);
        }

        return false;
    }

    public function getShippingAddresses($customerId)
    {
        if (!is_int($customerId))
        {
            throw new \InvalidArgumentException("Argument must be an integer");
        }
        return $this->db->get(SHIPPING_ADDRESSES_TABLE,array('customer_id','=',strval($customerId)));
    }

    public function addShippingAddress(Customer $customer,$address, $default = false)
    {
        $inserted = $this->db->insert(SHIPPING_ADDRESSES_TABLE,array('id'=>0,'customer_id'=>$customer->getId(),
            'address'=>$address,'is_default'=>$default));
        if(!$inserted) return false;
        $customer->addShippingAddress($this->db->lastInsertId(),$address,$default);
        return $inserted;
    }

    public function deleteShippingAddresses(Customer $customer,$addressId){

        $rowCount = $this->db->delete(SHIPPING_ADDRESSES_TABLE,array('id','=',$addressId));
        if($rowCount > 0)
        {
            $customer->deleteShippingAddress($addressId);
        }
        return $rowCount;
    }
    public function updateShippingAddress(Customer $customer, $shippingAddressId, array $updatedShippingAddress)
    {
        $fields = $updatedShippingAddress;
        $fields['id'] = $shippingAddressId;
        $where = array('id', '=', $shippingAddressId);

        if ($this->db->update(SHIPPING_ADDRESSES_TABLE, $fields, $where)) {
            $customer->setShippingAddresses($this->getShippingAddresses($customer->getId()));
            return $this->updateCustomer($customer);
        }

        return false;
    }

    private function setAddresses(Customer $customer)
    {
        $billingAddresses = $this->getBillingAddresses($customer->getId());
        foreach ($billingAddresses as $address) {
            $customer->addBillingAddress($address['id'], $address['address'], $address['tax_number'], $address['is_default']);
        }

        $shippingAddresses = $this->getShippingAddresses($customer->getId());
        foreach ($shippingAddresses as $address) {
            $customer->addShippingAddress($address['id'], $address['address'], $address["is_default"]);
        }
    }


}