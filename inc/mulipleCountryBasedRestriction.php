<?php

class mulipleCountryBasedRestriction
{

    /**
     * Declare Important Variables 
     */
    protected $countries;
    static $action = 'multi_product_autoFill';
    static $actionForCat = 'multi_cetagory_autoFill';
    protected $selectedCountry;
    protected $selectedProducts;
    protected $selectedCetagory;
    protected $privousSavedValue;
    protected $savedProducts;
    protected $savedCetagories;


    /**
     * Invoke all important functions whenever page load
     * @param NULL  
     * @return NULL
     */

    function __construct()
    {
        add_action('admin_menu', array($this, 'initailize_page_configuration'));
        add_action('admin_init', array($this, 'initizeBasicSetting'));
        add_action('admin_enqueue_scripts', array($this, "enable_scripts"));
        add_action('wp_ajax_' . self::$action, array($this, 'multi_autocomplete_suggestions_product'));
        add_action('wp_ajax_' . self::$actionForCat, array($this, 'multi_autocomplete_suggestions_cetagory'));
    }




    /**
     * Connect Js to our file also pass Rule Array to Js file
     * @param NULL  
     * @return NULL
     */

    public function enable_scripts()
    {
        wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/css/select2.min.css');
        wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.3/js/select2.min.js', array('jquery'));
        wp_enqueue_script("ia_multiFields", CW_PLUGIN_DIR . "assets/js/multiSelect.js", array("jquery", 'select2'), rand(), false);
        wp_localize_script(
            'ia_multiFields',
            'hostedUrl',
            array(
                'site_url' => admin_url('admin-ajax.php'),
            )
        );
        wp_localize_script(
            'ia_multiFields',
            'rules',
            array(
                'savedRules' => $this->privousSavedValue,
            )
        );
    }



    /**
     * Get Important data from DB also I make changes in product and cetagory subarray
     * @param NULL  
     * @return NULL
     */


    public function initizeBasicSetting()
    {
        $this->privousSavedValue = get_option("country_based_restriction");
        if ($this->privousSavedValue) {
            for ($i = 0; $i < sizeof($this->privousSavedValue); $i++) {
                if ($this->privousSavedValue[$i]["restrictedProducts"]) {
                    foreach ($this->privousSavedValue[$i]["restrictedProducts"] as $index => $productId) {
                        $saveProduct = array();
                        $saveProduct["id"] = $productId;
                        $saveProduct["text"] = get_the_title($productId);
                        $this->privousSavedValue[$i]["restrictedProducts"][$index] = $saveProduct;
                    }
                }
                if ($this->privousSavedValue[$i]["restrictedCetagories"]) {
                    foreach ($this->privousSavedValue[$i]["restrictedCetagories"] as $index => $cetagoryId) {
                        $saveCetagory = array();
                        $saveCetagory["id"] = $cetagoryId;
                        $CETAGORY_NAME = get_term_by("id", (int)$cetagoryId, 'product_cat');
                        $saveCetagory["text"] = $CETAGORY_NAME->name;
                        $this->privousSavedValue[$i]["restrictedCetagories"][$index] = $saveCetagory;
                    }
                }
            }
        }
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
            'Multi Country Based Restriction',
            'Multi Country Based Restriction',
            'manage_options',
            'ia-multi-country-configration',
            array($this, 'create_admin_page'),
        );
    }

    /**
     * This will create main layout of the page | all the things in page write here
     * @param NULL
     * @return NULL
     */


    public function create_admin_page()

    {

        if (isset($_POST["restrictBtn"])) {
            echo "<pre>" .
                print_r($_POST, 1)
                . "</pre>";
            $rules = [];
            $this->selectedCountry = $_POST["restrictedCountries"];
            $this->selectedProducts = $_POST["restrictedProducts"];
            $this->selectedCetagory = $_POST["restrictedCetagories"];
            foreach ($this->selectedCountry as $key => $value) {
                $appendArray = array();
                $appendArray["restrictedCountry"] = $this->selectedCountry[$key][0];
                $appendArray["restrictedProducts"] = $this->selectedProducts[$key];
                $appendArray["restrictedCetagories"] = $this->selectedCetagory[$key];
                $rules[] = $appendArray;
            }
            update_option('country_based_restriction', $rules);
        }
?>
        <div class="wrap">
            <h1>Welcome Dude, Add multiple countries Based Restriction</h1>
            <br>
            <form method="post">
                <table id="repeatable-fieldset-one" width="100%">
                    <tbody>
                        <?php
                        if ($this->privousSavedValue) {
                            for ($i = 0; $i < sizeof($this->privousSavedValue); $i++) {
                        ?>
                                <tr class="forDataInsertion">
                                    <td>
                                        <label for="countries">Select Country</label> <br>
                                        <select name="restrictedCountries" id="countries">
                                            <?php
                                            if ($this->privousSavedValue[$i]["restrictedCountry"]) {
                                                foreach ($this->countries as $key => $val) {
                                                    if ($this->privousSavedValue[$i]["restrictedCountry"] == $key) {
                                            ?>
                                                        <option value="<?php echo $key; ?>" selected><?php echo $val; ?></option>

                                            <?php
                                                    }
                                                }
                                            }
                                            ?>
                                            <?php
                                            if ($this->countries) {
                                                foreach ($this->countries as $key => $val) {
                                            ?>
                                                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </td>
                                    <td>
                                        <label for="restrictedProducts">Select Product</label> <br>
                                        <select name="restrictedProducts" class="restrictedProducts" id="restrictedProducts" multiple="multiple" style="width:99%;max-width:50em;" autocomplete="off"></select>
                                    </td>
                                    <td>
                                        <label for="restrictedCetagories">Select Cetagory</label> <br>
                                        <select name="restrictedCetagories" class="restrictedCetagories" id="restrictedCetagories" multiple="multiple" style="width:99%;max-width:50em;" autocomplete="off"></select>
                                    </td>
                                    <td>
                                        <button class="button remove-row" href="#">Remove</button>
                                    </td>
                                    <td>
                                        <button type="submit" name="restrictBtn" class='button'>Save Changes</button>
                                    </td>
                                </tr>


                        <?php
                            }
                        }
                        ?>
                        <tr class="empty-row screen-reader-text">
                            <td>
                                <label for="countries">Select Country</label> <br>
                                <select name="restrictedCountries" id="countries">
                                    <option value="null" selected disabled> Choose One </option>
                                    <?php
                                    if ($this->countries) {
                                        foreach ($this->countries as $key => $val) {
                                    ?>
                                            <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                            <td>
                                <label for="restrictedProducts">Select Product</label> <br>
                                <select name="restrictedProducts" class="restrictedProducts" id="restrictedProducts" multiple="multiple" style="width:99%;max-width:50em;" autocomplete="off"></select>
                            </td>
                            <td>
                                <label for="restrictedCetagories">Select Cetagory</label> <br>
                                <select name="restrictedCetagories" class="restrictedCetagories" id="restrictedCetagories" multiple="multiple" style="width:99%;max-width:50em;"></select>
                            </td>
                            <td>
                                <button class="button remove-row" href="#">Remove</button>
                            </td>
                            <td>
                                <button type="submit" name="restrictBtn" class='button'>Save Changes</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <button class="button" id="addRestrictedRow"> Add new Setting</button>
        </div>
<?php
    }


    /**
     * Autoselect in products | Filter products to send desire product
     * @param NULL
     * @return NULL
     */


    public function multi_autocomplete_suggestions_product()
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
     * Autoselect in Cetagory | Filter Cetagory to send desire product
     * @param NULL
     * @return NULL
     */

    public function multi_autocomplete_suggestions_cetagory()
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
}
new mulipleCountryBasedRestriction();
