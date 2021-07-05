<?php

class mulipleCountryBasedRestriction
{
    protected $countries;
    static $action = 'multi_product_autoFill';
    static $actionForCat = 'multi_cetagory_autoFill';
    protected $selectedCountry = array("");
    protected $selectedProducts = array();
    protected $selectedCetagory = array();


    function __construct()
    {
        add_action('admin_menu', array($this, 'initailize_page_configuration'));
        add_action('admin_init', array($this, 'initizeBasicSetting'));
        add_action('admin_enqueue_scripts', array($this, "enable_scripts"));
        add_action('wp_ajax_' . self::$action, array($this, 'multi_autocomplete_suggestions_product'));
        add_action('wp_ajax_' . self::$actionForCat, array($this, 'multi_autocomplete_suggestions_cetagory'));
    }
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
    }
    public function initizeBasicSetting()
    {
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

    public function create_admin_page()

    {
        
        if (isset($_POST["restrictBtn"])) {
            echo "<pre>" .
            print_r($_POST,1)
            . "</pre>";
            $rules = [];
            foreach($_POST['restrictedCountries'] as $index => $rule_country){
                if(!$rule_country)
                    continue;

                $rules[]  = [

                ];
            }
            if ($_POST["restrictedCountries"]) {
                foreach ($_POST["restrictedCountries"] as $countryKey) {
                    if ($countryKey) {
                        array_push($this->selectedCountry, sanitize_text_field($countryKey));
                    }
                }
            }
            if ($_POST["restrictedProducts"]) {
                foreach ($_POST["restrictedProducts"] as $productKey) {
                    if ($productKey) {
                        array_push($this->selectedProducts, sanitize_text_field($productKey));
                    }
                }
            }
            if ($_POST["restrictedCetagories"]) {
                foreach ($_POST["restrictedCetagories"] as $cetagoryKey) {
                    if ($cetagoryKey) {
                        array_push($this->selectedCetagory, sanitize_text_field($cetagoryKey));
                    }
                }
            }
        }

        // print_r($this->selectedCountry);

?>
        <div class="wrap">
            <h1>Welcome Dude, Add multiple countries Based Restriction</h1>
            <br>
            <form method="post">
                <table id="repeatable-fieldset-one" width="100%">
                    <tbody>
                        <tr class="empty-row screen-reader-text">
                            <td>
                                <label for="countries">Select Country</label> <br>
                                <select name="restrictedCountries[]" id="countries">
                                    <option value="null"> Chose one </option>
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
                                <select name="restrictedProducts[][]" class="restrictedProducts" id="restrictedProducts" multiple="multiple" style="width:99%;max-width:50em;" autocomplete="off"></select>
                            </td>
                            <td>
                                <label for="restrictedCetagories">Select Cetagory</label> <br>
                                <select name="restrictedCetagories[][]" class="restrictedCetagories" id="restrictedCetagories" multiple="multiple" style="width:99%;max-width:50em;"></select>
                            </td>
                            <td>
                                <button type="submit" class="button" name='restrictBtn'> Submit your information </button>
                            </td>
                            <td>
                                <a class="button remove-row" href="#">Remove</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </form>
            <button class="button" id="addRestrictedRow"> Add new Setting</button>
        </div>
<?php
    }

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
