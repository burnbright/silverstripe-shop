<?php

namespace SilverShop\Core\Cms;


use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Security\Group;
use SilverStripe\Assets\Image;
use SilverStripe\SiteConfig\SiteConfig;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\Forms\TabSet;
use SilverStripe\Core\Config\Config_ForClass;
use SilverStripe\ORM\DataExtension;



/**
 * @package    shop
 * @subpackage cms
 */
class ShopConfig extends DataExtension
{
    private static $db      = array(
        'AllowedCountries' => 'Text',
    );

    private static $has_one = array(
        'TermsPage'           => SiteTree::class,
        "CustomerGroup"       => Group::class,
        'DefaultProductImage' => Image::class,
    );

    private static $email_from;

    public static function current()
    {
        return SiteConfig::current_site_config();
    }

    public function updateCMSFields(FieldList $fields)
    {
        $fields->insertBefore($shoptab = Tab::create('Shop', 'Shop'), 'Access');
        $fields->addFieldsToTab(
            "Root.Shop",
            TabSet::create(
                "ShopTabs",
                $maintab = Tab::create(
                    "Main",
                    TreeDropdownField::create(
                        'TermsPageID',
                        _t("ShopConfig.TermsPage", 'Terms and Conditions Page'),
                        SiteTree::class
                    ),
                    TreeDropdownField::create(
                        "CustomerGroupID",
                        _t("ShopConfig.CustomerGroup", "Group to add new customers to"),
                        Group::class
                    ),
                    UploadField::create('DefaultProductImage', _t('ShopConfig.DefaultImage', 'Default Product Image'))
                ),
                $countriestab = Tab::create(
                    "Countries",
                    CheckboxSetField::create(
                        'AllowedCountries',
                        _t('ShopConfig.AllowedCountries', 'Allowed Ordering and Shipping Countries'),
                        self::config()->iso_3166_country_codes
                    )
                )
            )
        );
        $fields->removeByName("CreateTopLevelGroups");
        $countriestab->setTitle(_t('ShopConfig.AllowedCountriesTabTitle', "Allowed Countries"));
    }

    public static function get_base_currency()
    {
        return self::config()->base_currency;
    }

    public static function get_site_currency()
    {
        return self::get_base_currency();
    }

    /**
     * Get list of allowed countries
     *
     * @param boolean $prefixisocode - prefix the country code
     *
     * @return array
     */
    public function getCountriesList($prefixisocode = false)
    {
        $countries = self::config()->iso_3166_country_codes;
        asort($countries);
        if ($allowed = $this->owner->AllowedCountries) {
            $allowed = explode(",", $allowed);
            if (count($allowed > 0)) {
                $countries = array_intersect_key($countries, array_flip($allowed));
            }
        }
        if ($prefixisocode) {
            foreach ($countries as $key => $value) {
                $countries[$key] = "$key - $value";
            }
        }
        return $countries;
    }

    /**
     * For shops that only sell to a single country,
     * this will return the country code, otherwise null.
     *
     * @param fullname get long form name of country
     *
     * @return string country code
     */
    public function getSingleCountry($fullname = false)
    {
        $countries = $this->getCountriesList();
        if (count($countries) == 1) {
            if ($fullname) {
                return array_pop($countries);
            } else {
                reset($countries);
                return key($countries);
            }
        }
        return null;
    }

    /*
     * Convert iso country code to English country name
     * @return string - name of country
     */
    public static function countryCode2name($code)
    {
        $codes = self::config()->iso_3166_country_codes;
        if (isset($codes[$code])) {
            return $codes[$code];
        }
        return $code;
    }

    /**
     * Helper for getting static shop config.
     * The 'config' static function isn't available on Extensions.
     *
     * @return Config_ForClass configuration object
     */
    public static function config()
    {
        return new Config_ForClass("ShopConfig");
    }
}
