<?php
class hideProducts
{

    protected $restrictedCountry;
    protected $restrictedProducts;
    protected $restrictedCetagories;
    protected $base_country;

    function __construct()
    {
        add_action("init", [$this, "getRestrictedValues"]);
        add_action('woocommerce_product_query', [$this, "update_restriction"]);
    }

    public function getRestrictedValues()
    {
        $location = wc_get_base_location();
        $this->base_country = $location["country"];
        $restrictedArray = get_option('ia-country-restriction', []);
        $this->restrictedCountry = $restrictedArray["country"];
        $this->restrictedProducts = $restrictedArray["products"];
        $this->restrictedCetagories = $restrictedArray["cetagory"];
        $multiRestriction = get_option( "country_based_restriction", [] );
        $this->logForDebugging($multiRestriction);
    }

    public function update_restriction($q)
    {
        if ($this->base_country == $this->restrictedCountry) {
            $q->set('tax_query', $this->restrictedCetagories);
            $q->set('post__not_in', $this->restrictedProducts);
        }
    }
    function logForDebugging($state_array)
    {
        $log = new WC_Logger();
        $log_entry = print_r($state_array, true);
        $log->add('multi_country_restriction', $log_entry);
    }
}
new hideProducts();
