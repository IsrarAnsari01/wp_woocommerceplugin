<?php
class countryConfigration
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $store_country;
    private $store_country_states;

    /**
     * Constructor load tha initail setting of this page
     * @param NULL
     * @return NULL
     */

    public function __construct()
    {
        add_action('admin_menu', array($this, 'intial_page_configuration'));
        add_action('admin_init', array($this, 'initail_page_registration'));
        add_action('admin_init', array($this, 'createFieldName'));
        add_action('init', array($this, 'get_store_location_information'));
    }

    /**
     * This function get the resgistered country of store and states of registered country 
     * @param NULL
     * @return NULL 
     */

    public function get_store_location_information()
    {
        $country_obj = new WC_Countries();
        $this->store_country = $country_obj->get_base_country();
        $this->store_country_states = $country_obj->get_states($this->store_country);
        $this->store_country_states['Default'] = "Default";
    }


    /**
     * Add options page
     * @param NULL
     * @return NULL
     */

    public function intial_page_configuration()
    {
        // This page will be under "Settings"
        add_options_page(
            'Configure Restriction',
            'Configure Restriction',
            'manage_options',
            'ia-configration',
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
        $this->options = get_option('ia_configure_restriction');
?>
        <div class="wrap">
            <h1>Your Store Register country <b><u><i><?php echo $this->store_country; ?></i></u></b></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('ia_option_group_configration');
                do_settings_sections('ia-configration');
                submit_button("Register new Configuration");
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

    public function initail_page_registration()
    {
        register_setting(
            'ia_option_group_configration', // Option group
            'ia_configure_restriction', // Option name
            array(
                'sanitize_callback' => array($this, 'sanitize'), // Sanitize
            )
        );

        add_settings_section(
            'setting_section_id', // ID
            'Configure Restriction for Check out form In term of age', // Title
            array($this, 'print_section_info'), // Callback
            'ia-configration' // Page
        );
    }

    /**
     * Add Fields in our page
     * @param NULL
     * @return NULL
     */

    public function createFieldName()
    {
        $allPreviousSavedValue = get_option("ia_configure_restriction", []);
        foreach ($this->store_country_states as $key => $val) {
            add_settings_field(
                $val,
                strtoupper($val),
                array($this, 'create_fields_callback'),
                'ia-configration',
                'setting_section_id',
                ['state' => $key, "savedValue" => $allPreviousSavedValue[$key]]
            );
        }
    }

    /**
     * Create Field crossponding to the name
     * @param array $val | Contain Id of the field
     * @return NULL
     */

    public function create_fields_callback($argu)
    {

    ?>
        <input type="number" min=1 required id="<?php echo $argu['state']; ?>" name="ia_configure_restriction[<?php echo $argu['state']; ?>]" value=<?php echo $argu["savedValue"]?>>
<?php
    }

    /**
     * Sanitize each setting field as needed
     * @param array $input Contains all settings fields as array keys
     * @return array $new_input | contain all field value
     */

    public function sanitize($input)
    {
        $new_input = array("age_restriction" => array());
        $new_input["age_restriction"] = $input;
        return  $new_input["age_restriction"];
    }

    /** 
     * Print the Section text
     * @param NULL
     * @return NULL
     */

    public function print_section_info()
    {
        print 'Enter Age limits below:';
    }
}

if (is_admin())
    $my_settings_page = new countryConfigration();
