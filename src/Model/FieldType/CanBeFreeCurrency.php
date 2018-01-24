<?php

namespace SilverShop\Core\Model\FieldType;


use SilverStripe\ORM\FieldType\DBCurrency;



/**
 * Allows casting some template values to show "FREE" instead of $0.00.
 */
class CanBeFreeCurrency extends DBCurrency
{
    public function Nice()
    {
        if ($this->value == 0) {
            return _t("ShopCurrency.Free", "<span class=\"free\">FREE</span>");
        }
        return parent::Nice();
    }
}
