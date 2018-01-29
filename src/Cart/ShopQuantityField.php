<?php

namespace SilverShop\Core\Cart;


use SilverShop\Core\Model\Buyable;
use SilverShop\Core\Model\OrderItem;
use SilverShop\Core\ShopTools;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\NumericField;
use SilverStripe\View\ViewableData;


class ShopQuantityField extends ViewableData
{
    /** @var OrderItem  */
    protected $item;

    protected $parameters;

    protected $classes = ['ajaxQuantityField'];

    protected $template = 'ShopQuantityField';

    /** @var Buyable */
    protected $buyable;

    public function __construct($object, $parameters = null)
    {
        if ($object instanceof Buyable) {
            $this->item = ShoppingCart::singleton()->get($object, $parameters);
            //provide a 0-quantity facade item if there is no such item in cart
            if (!$this->item) {
                $this->item = $object->createItem();
            }
            $this->buyable = $object;
            //TODO: perhaps we should just store the product itself,
            //and do away with the facade, as it might be unnecessary complication
        } elseif ($object instanceof OrderItem) {
            $this->item = $object;
            $this->buyable = $object->Buyable();
        }
        if (!$this->item) {
            user_error('ShopQuantityField: no item or product passed to constructor.');
        }
        $this->parameters = $parameters;
        //TODO: include javascript for easy update
    }

    public function setClasses($newclasses, $overwrite = false)
    {
        if ($overwrite) {
            $this->classes = array_merge($this->classes, $newclasses);
        } else {
            $this->classes = $newclasses;
        }
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function Item()
    {
        return $this->item;
    }

    public function Quantity()
    {
        return $this->item->Quantity;
    }

    public function Field()
    {
        $qtyArray = array();
        for ($r = 1; $r <= $this->max; $r++) {
            $qtyArray[$r] = $r;
        }
        return NumericField::create(
            $this->MainID() . '_Quantity',
            // this title currently doesn't show up in the front end, better assign a translation anyway.
            _t('SilverShop\Core\Model\Order.Quantity', 'Quantity'),
            $this->item->Quantity
        )->setHTML5(true);
    }

    public function MainID()
    {
        return ShopTools::sanitiseClassName($this->item->class) . '_DB_' . $this->item->ID;
    }

    public function IncrementLink()
    {
        return $this->item->addLink();
    }

    public function DecrementLink()
    {
        return $this->item->removeLink();
    }

    public function forTemplate()
    {
        return $this->renderWith($this->template);
    }

    /**
     * Used for storing the quantity update link for ajax use.
     */
    public function AJAXLinkHiddenField()
    {
        if ($quantitylink = $this->item->setquantityLink()) {
            return HiddenField::create(
                $this->MainID() . '_Quantity_SetQuantityLink'
            )->setValue($quantitylink)->addExtraClass('ajaxQuantityField_qtylink');
        }
    }
}
