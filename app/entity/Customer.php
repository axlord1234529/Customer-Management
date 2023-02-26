<?php

namespace App\entity;

class Customer {
    private $name;
    private $address;
    private $password;
    private $email;
    private $billingAddresses = array();
    private $shippingAddresses = array();

    public function __construct($name, $address, $password, $email) {
        $this->name = $name;
        $this->address = $address;
        $this->password = $password;
        $this->email = $email;
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

    public function addBillingAddress($address, $taxNumber = null) {
        $this->billingAddresses[] = array(
            'address' => $address,
            'taxNumber' => $taxNumber
        );
    }

    public function getShippingAddresses() {
        return $this->shippingAddresses;
    }

    public function addShippingAddress($address, $default = false) {
        $this->shippingAddresses[] = array(
            'address' => $address,
            'default' => $default
        );
    }

    public function getDefaultShippingAddress() {
        foreach ($this->shippingAddresses as $shippingAddress) {
            if ($shippingAddress['default']) {
                return $shippingAddress['address'];
            }
        }
        return null;
    }
}