<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
class countryRestrictionTab
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    protected $countries;
    protected $products;
    protected $products_cetagory;
    static $action = 'product_autoFill';
    static $actionForCat = 'cetagory_autoFill';
    protected $allProductsInStore;
    protected $allPreviousSavedValue;
    protected $saveProductsDataSet = array();
    protected $saveCetagoriesDataSet = array();
    /**
     * Constructor load tha initail setting of this page
     * @param NULL
     * @return NULL
     */

    public function __construct()
    {
        add_action('admin_menu', array($this, 'initailize_page_configuration'));
        add_action('admin_init', array($this, 'initialize_country_restriction'));
        add_action('admin_init', array($this, 'createFields'));
        add_action('init', array($this, 'get_gernal_information'));
        add_action('wp_ajax_' . self::$action, array($this, 'autocomplete_suggestions'));
        add_action('wp_ajax_' . self::$actionForCat, array($this, 'autocomplete_suggestions_cetagory'));
        add_action('admin_enqueue_scripts', array($this, "enabling_auto_fill"));
        add_action("admin_init", [$this, "getSavedOptionValueFromDb"]);
        add_action("admin_init",  [$this, "makedataSetThatWillSend"]);
    }

    public function getSavedOptionValueFromDb()
    {
        $this->allPreviousSavedValue = get_option("ia-country-restriction", []);
    }

    /**
     * Make data Object that will be send to ajax
     * @param NULL
     * @return NULL
     */
    public function makedataSetThatWillSend()
    {
        if ($this->allPreviousSavedValue["products"]) {
            foreach ($this->allPreviousSavedValue["products"] as $selectedProductsId) :
                $saveProducts = array();
                $saveProducts['id'] = $selectedProductsId;
                $saveProducts['text'] = get_the_title($selectedProductsId);
                $this->saveProductsDataSet[] = $saveProducts;
            endforeach;
        }

        if ($this->allPreviousSavedValue["cetagory"]) {
            foreach ($this->allPreviousSavedValue["cetagory"] as $selectedCetagoryID) :
                $saveCetagory = array();
                $saveCetagory['id'] = $selectedCetagoryID;
                $CAT_NAME = get_term_by("id", (int)$selectedCetagoryID, 'product_cat');
                $saveCetagory['text'] =  $CAT_NAME->name;
                $this->saveCetagoriesDataSet[] = $saveCetagory;
            endforeach;
        }
    }

    /**
     * Function That enable scripts in our page 
     * @param NULL
     * @return NULL
     */

    function enabling_auto_fill()
    {
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery'));
        wp_enqueue_script("ia_autoFill", CW_PLUGIN_DIR . "assets/js/autofill.js", array("jquery", 'select2'), rand(), false);
        wp_localize_script(
            'ia_autoFill',
            'BACKEND',
            array(
                'site_url' => admin_url('admin-ajax.php'),
            )
        );
        wp_localize_script(
            'ia_autoFill',
            'products',
            array(
                'selectedProducts' => $this->saveProductsDataSet,
            )
        );
        wp_localize_script(
            'ia_autoFill',
            'cetagories',
            array(
                'selectedCetagories' => $this->saveCetagoriesDataSet,
            )
        );
    }

    /**
     * Function That filter and select single product from products
     * @param NULL
     * @return NULL
     */
    public function autocomplete_suggestions()
    {
        $productQuery = array(
            'post_type' => 'product',
            's' => $_GET['q'],
        );
        $filterProducts = new WP_Query($productQuery);
        global $product;
        foreach ($filterProducts->posts as $product) :
            setup_postdata($product);
            $suggestion = array();
            $suggestion['id'] = $product->ID;
            $suggestion['text'] = esc_html($product->post_title);
            $suggestions[] = $suggestion;
        endforeach;
        echo json_encode($suggestions);
        die;
    }

    /**
     * Function That filter and select single cetagory from cetagories
     * @param NULL
     * @return NULL
     */

    public function autocomplete_suggestions_cetagory()
    {
        $cetagoryQuery = array(
            'taxonomy'     => 'product_cat',
            'orderby'      => 'name',
            'show_count'   => 0,
            'pad_counts'   => 0,
            'hierarchical' => 1,
            'title_like'     => $_GET['q'],
            'hide_empty'   => 0,
        );
        $filterCetagory = get_categories($cetagoryQuery);
        global $cetagory;
        foreach ($filterCetagory as $cetagory) :
            setup_postdata($cetagory);
            $suggestion = array();
            $suggestion['id'] = $cetagory->term_id;
            $suggestion['text'] = esc_html($cetagory->name);
            $suggestions[] = $suggestion;
        endforeach;
        echo json_encode($suggestions);
        die;
    }

    /**
     * This function get the resgistered country of store and states of registered country 
     * @param NULL
     * @return NULL 
     */

    public function get_gernal_information()
    {
        $argument = array('post_type' => 'product');
        $this->allProductsInStore = new WP_Query($argument);
        $country_obj = new WC_Countries();
        $this->countries = $country_obj->get_countries();
    }


    /**
     * Add options page
     * @param NULL
     * @return NULL
     */

    public function initailize_page_configuration()
    {
        // This page will be under "Settings"
        add_options_page(
            'Country Based Restriction',
            'Country Based Restriction',
            'manage_options',
            'ia-country-configration',
            array($this, 'create_admin_page'),
        );
    }

    /**
     * Options page callback
     * @param NULL
     * @return NULL
     */

    public function create_admin_page()
    {
        $this->options = get_option('ia-country-restriction');
?>
        <div class="wrap">
            <h1>Welcome Dude, Add country Based Restriction</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ia-option-group-restriction');
                do_settings_sections('ia-country-configration');
                submit_button("Register new Restriction", "primary");
                ?>
            </form>
        </div>
    <?php
    }

    /**
     * Register and add settings
     * @param NULL
     * @return NULL
     */

    public function initialize_country_restriction()
    {
        register_setting(
            'ia-option-group-restriction', // Option group
            'ia-country-restriction', // Option name
            array(
                'sanitize_callback' => array($this, 'sanitize'), // Sanitize
            )
        );

        add_settings_section(
            'setting_section_id', // ID
            'Add Country based restriction in Products as well as in cetagory', // Title
            array($this, 'print_section_info'), // Callback
            'ia-country-configration' // Page
        );
    }

    /**
     * Add Fields in our page
     * @param NULL
     * @return NULL
     */

    public function createFields()
    {
        add_settings_field(
            "country",
            "Countries",
            array($this, 'country_dropDown'),
            'ia-country-configration',
            'setting_section_id',
            ['countries' => $this->countries, "selectedCountry" => $this->allPreviousSavedValue["country"]]
        );

        add_settings_field(
            "products",
            "Select Products",
            array($this, 'select_products'),
            'ia-country-configration',
            'setting_section_id',
        );

        add_settings_field(
            "cetagory",
            "Select Cetagory",
            array($this, 'select_cetagory'),
            'ia-country-configration',
            'setting_section_id',
        );
    }

    /**
     * Create Dropdown crossponding to the Countries
     * @param array $val | Contain Id of the field
     * @return NULL
     */

    public function country_dropDown($argu)
    {
    ?>
        <select id="country" name="ia-country-restriction[country]">
            <?php
            if ($argu["selectedCountry"]) {
                foreach ($argu["countries"] as $key => $val) {
                    if ($key == $argu["selectedCountry"]) {
            ?>
                        <option value="<?php echo $argu["selectedCountry"]; ?>" selected><?php echo $val; ?></option>
                    <?php
                    }
                }
            }
            if ($argu["countries"]) {
                foreach ($argu["countries"] as $key => $val) {
                    ?>
                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
            <?php
                }
            }
            ?>
        </select>
    <?php
    }

    /**
     * Create Select crossponding to the Products
     * @param array $val | Contain Id of the field
     * @return NULL
     */

    public function select_products()
    {

    ?>
        <select name="ia-country-restriction[products][]" id="products" multiple="multiple" style="width:99%;max-width:25em;"></select>
    <?php
    }


    /**
     * Create Select crossponding to the Cetagory
     * @param array $val | Contain Id of the field
     * @return NULL
     */

    function select_cetagory()
    {
    ?>
        <select name="ia-country-restriction[cetagory][]" id="cetagory" multiple="multiple" style="width:100%;max-width:65em;"></select></select>
<?php
    }


    /**
     * Sanitize each setting field as needed
     * @param array $input Contains all settings fields as array keys
     * @return array $new_input | contain all field value
     */

    public function sanitize($input)
    {
        $restriction_in_products = array("country_based_restriction" => array());
        $restriction_in_products["country_based_restriction"] = $input;
        return $restriction_in_products["country_based_restriction"];
    }

    /** 
     * Print the Section text
     * @param NULL
     * @return NULL
     */

    public function print_section_info()
    {
        print 'Select Country then add products then chose cetagory';
    }
    function logForDebugging($state_array)
    {
        $log = new WC_Logger();
        $log_entry = print_r($state_array, true);
        $log->add('sanitizeVal', $log_entry);
    }
}

if (is_admin())
    new countryRestrictionTab();
