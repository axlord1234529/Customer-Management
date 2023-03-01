<?php

namespace App\entity;

class Customer {
    private $id = 0;
    private $name;
    private $address;
    private $password;
    private $email;
    private $billingAddresses = array();
    private $shippingAddresses = array();

    public function __construct($id,$name, $address, $password, $email) {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->password = $password;
        $this->email = $email;
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }
    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getAddress() {
        return $this->address;
    }

    public function setAddress($address) {
        $this->address = $address;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getBillingAddresses() {
        return $this->billingAddresses;
    }

    public function setBillingAddresses($billingAddresses)
    {
        $this->billingAddresses = $billingAddresses;
    }

    public function addBillingAddress($id,$address, $taxNumber = null, $default = false) {
        $this->billingAddresses[] = array(
            'id' => $id,
            'customer_id' => $this->id,
            'address' => $address,
            'tax_number' => $taxNumber,
            'is_default' => $default
        );
    }
    public function deleteBillingAddress($addressId) {
        foreach ($this->billingAddresses as $key => $billingAddress) {
            if ($billingAddress['id'] == $addressId) {
                unset($this->billingAddresses[$key]);
            }
        }
    }
    public function getDefaultBillingAddress() {
        foreach ($this->billingAddresses as $billingAddress) {
            if ($billingAddress['is_default']) {
                return $billingAddress['address'];
            }
        }
        return null;
    }

    public function getShippingAddresses() {
        return $this->shippingAddresses;
    }

    public function setShippingAddresses($shippingAddresses){
         $this->shippingAddresses = $shippingAddresses;
    }

    public function addShippingAddress($id,$address, $default = false) {
        $this->shippingAddresses[] = array(
            'id' => $id,
            'customer_id' => $this->id,
            'address' => $address,
            'is_default' => $default
        );
    }

    public function deleteShippingAddress($addressId) {
        foreach ($this->shippingAddresses as $key => $shippingAddress) {
            if ($shippingAddress['id'] == $addressId) {
                unset($this->shippingAddresses[$key]);
            }
        }
    }

    public function getDefaultShippingAddress() {
        foreach ($this->shippingAddresses as $shippingAddress) {
            if ($shippingAddress['is_default']) {
                return $shippingAddress['address'];
            }
        }
        return null;
    }
}