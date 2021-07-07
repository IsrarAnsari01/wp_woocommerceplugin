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
        // For single country 

        // $restrictedArray = get_option('ia-country-restriction', []);
        // $this->restrictedCountry = $restrictedArray["country"];
        // $this->restrictedProducts = $restrictedArray["products"];
        // $this->restrictedCetagories = $restrictedArray["cetagory"];

        // For multiple countries
        $location = wc_get_base_location();
        $this->base_country = $location["country"];
        $multiRestriction = get_option("country_based_restriction", []);
        for ($i = 0; $i < sizeof($multiRestriction); $i++) {
            if ($multiRestriction[$i]["restrictedCountry"] == $this->base_country) {
                $this->restrictedProducts = $multiRestriction[$i]["restrictedProducts"];
                $this->restrictedCetagories = $multiRestriction[$i]["restrictedCetagories"];
            }
        }
    }

    public function update_restriction($q)
    {
        if (sizeof($this->restrictedProducts)) {
            $q->set('post__not_in', $this->restrictedProducts);
        }
        if (sizeof($this->restrictedCetagories)) {
            $q->set('tax_query', $this->restrictedCetagories);
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
