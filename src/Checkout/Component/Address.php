<?php

namespace SilverShop\Checkout\Component;

use SilverShop\Model\Order;
use SilverShop\Model\Zone;
use SilverShop\ShopUserInfo;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Security;
use SilverStripe\SiteConfig\SiteConfig;


abstract class Address extends CheckoutComponent
{
    protected $formfielddescriptions = true;

    protected $addresstype;

    protected $addtoaddressbook = false;

    public function getFormFields(Order $order)
    {
        return $this->getAddress($order)->getFrontEndFields(
            [
            'addfielddescriptions' => $this->formfielddescriptions,
            ]
        );
    }

    public function getRequiredFields(Order $order)
    {
        return $this->getAddress($order)->getRequiredFields();
    }

    public function validateData(Order $order, array $data)
    {
    }

    public function getData(Order $order)
    {
        $data = $this->getAddress($order)->toMap();

        //merge data from multiple sources
        $data = array_merge(
            ShopUserInfo::singleton()->getLocation(),
            $data,
            [$this->addresstype . 'AddressID' => $order->{$this->addresstype . 'AddressID'}]
        );

        //merge in default address if an address isn't available
        $member = Security::getCurrentUser();
        if (!$order->{$this->addresstype . 'AddressID'}) {
            $data = array_merge(
                ShopUserInfo::singleton()->getLocation(),
                $member ? $member->{'Default' . $this->addresstype . 'Address'}()->toMap() : array(),
                [$this->addresstype . 'AddressID' => $order->{$this->addresstype . 'AddressID'}]
            );
        }

        unset($data['ID']);
        unset($data['ClassName']);
        unset($data['RecordClassName']);

        //ensure country is restricted if there is only one allowed country
        if ($country = SiteConfig::current_site_config()->getSingleCountry()) {
            $data['Country'] = $country;
        }

        return $data;
    }

    /**
     * Create a new address if the existing address has changed, or is not yet
     * created.
     *
     * @param  Order $order order to get addresses from
     * @param  array $data  data to set
     * @throws \SilverStripe\ORM\ValidationException
     */
    public function setData(Order $order, array $data)
    {
        $address = $this->getAddress($order);
        //if the value matches the current address then unset
        //this is to fix issues with blank fields & the readonly Country field
        $addressFields = DataObject::getSchema()->databaseFields(\SilverShop\Model\Address::class);
        foreach ($data as $key => $value) {
            if (!isset($addressFields[$key]) || (!$value && !$address->{$key})) {
                unset($data[$key]);
            }
        }
        $address->update($data);
        //if only one country is available, then set it
        if ($country = SiteConfig::current_site_config()->getSingleCountry()) {
            $address->Country = $country;
        }
        //write new address, or duplicate if changed
        if (!$address->isInDB()) {
            $address->write();
        } elseif ($address->isChanged()) {
            $address = $address->duplicate();
        }
        //set billing address, if not already set
        $order->{$this->addresstype . 'AddressID'} = $address->ID;
        if (!$order->BillingAddressID) {
            $order->BillingAddressID = $address->ID;
        }
        $order->write();
        //update user info based on shipping address
        if ($this->addresstype === 'Shipping') {
            ShopUserInfo::singleton()->setAddress($address);
            Zone::cache_zone_ids($address);
        }
        //associate member to address
        if ($member = Security::getCurrentUser()) {
            $default = $member->{'Default' . $this->addresstype . 'Address'}();
            //set default address
            if (!$default->exists()) {
                $member->{'Default' . $this->addresstype . 'AddressID'} = $address->ID;
                $member->write();
            }
            if ($this->addtoaddressbook) {
                $member->AddressBook()->add($address);
            }
        }
        //extension hooks
        $order->extend('onSet' . $this->addresstype . 'Address', $address);
    }

    /**
     * Enable adding form field descriptions
     */
    public function setShowFormFieldDescriptions($show = true)
    {
        $this->formfielddescriptions = $show;
    }

    /**
     * Add new addresses to the address book.
     */
    public function setAddToAddressBook($add = true)
    {
        $this->addtoaddressbook = $add;
    }

    /**
     * @param Order $order
     * @return \SilverShop\Model\Address
     */
    public function getAddress(Order $order)
    {
        return $order->{$this->addresstype . 'Address'}();
    }
}
