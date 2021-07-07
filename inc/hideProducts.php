<?php
class hideProducts
{

    protected $restrictedCountry;
    protected $restrictedProducts = array();
    protected $restrictedCetagories = array();
    protected $base_country;

    function __construct()
    {
        add_action("init", [$this, "getRestrictedValues"]);
        add_action('woocommerce_product_query', [$this, "update_restriction"]);
    }

    /**
     * Get the current country Ip
     */
    public function getIPAddress()
    {
        //whether ip is from the share internet  
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        //whether ip is from the proxy  
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        //whether ip is from the remote address  
        else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    public function getRestrictedValues()
    {
        // For single country 

        // $restrictedArray = get_option('ia-country-restriction', []);
        // $this->restrictedCountry = $restrictedArray["country"];
        // $this->restrictedProducts = $restrictedArray["products"];
        // $this->restrictedCetagories = $restrictedArray["cetagory"];

        // For multiple countries
        $userIP = $this->getIPAddress();
        $result = file_get_contents('http://www.geoplugin.net/json.gp?ip='.$userIP);
        $resultArr = json_decode($result);
        $this->base_country = $resultArr->geoplugin_countryCode;
        $multiRestriction = get_option("country_based_restriction", []);
        for ($i = 0; $i < sizeof($multiRestriction); $i++) {
            if ($multiRestriction[$i]["restrictedCountry"] == $this->base_country) {
                if ($multiRestriction[$i]["restrictedProducts"]) {
                    foreach ($multiRestriction[$i]["restrictedProducts"] as $productid) {
                        array_push($this->restrictedProducts, $productid);
                    }
                }
                if ($multiRestriction[$i]["restrictedCetagories"]) {
                    foreach ($multiRestriction[$i]["restrictedCetagories"] as $cetagoryId) {
                        array_push($this->restrictedCetagories, $cetagoryId);
                    }
                }
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
