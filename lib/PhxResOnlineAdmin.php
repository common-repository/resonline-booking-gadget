<?php
/**
 * Creates option pages, manages Admin styles / functions
 * and manages licencing
 */
class PhxResOnlineAdmin
{
    /**
     * The version number of this plugin
     *
     * @var string $version Version number
     */
    private $version = "1.0.0";

    /**
     * Settings menu slug
     *
     * @var string $settings_menu_slug Slug
     */
    private $settings_menu_slug = "phx-resonline-booking-gadget-options";

    /**
     * Settings menu label
     *
     * @var string $settings_menu_title Title
     */
    private $settings_menu_title = "ResOnline";

    /**
     * Full title of the main options page
     *
     * @var string $settings_page_title Page title
     */
    private $settings_page_title = "ResOnline Display Options ";

    /**
     * Is this the pro version of this plugin?
     *
     * @var bool $isPro "Purchased" flag
     */
    private $isPro = false;

    /**
     * Was this a pro version, but the licence has expired?
     *
     * @var bool $isExpired "Purchased" flag
     */
    private $isExpired = false;

    /**
     * Holds the status of the licence, as text
     *
     * @var string $status Status of licence
     */
    private $status = "";

    /**
     * Name of the file calling this class
     *
     * @var string $caller File name
     */
    private $caller = "";

    /**
     * Root path of the plugin
     *
     * @var string $path Plugin path
     */
    private $path = "";

    /**
     * Root URI of the plugin
     *
     * @var string $uri Plugin URI
     */
    private $uri = "";

    /**
     * Faux namespace, used for HTML calls and tags
     *
     * @var string $namespace Faux name space
     */
    private $namespace = "";

    /**
     * Slug used for this product, by Code Canyon
     *
     * @var string $slug Code Canyon slug
     */
    private $slug = "";

    /**
     * Item ID used for this product, by Code Canyon
     *
     * @var string $slug Code Canyon ID
     */
    private $item = "";

    /**
     * Link for the purchasable product on Envato
     *
     * @var string $purchase URL
     */
    private $purchase = "";

    /**
     * List of currencies available to most ResOnline accounts
     *
     * @var array $currencies List of currencies
     */
    private $currencies = array(
        "AUD" => "Australian Dollar (AUD)",
        "AED" => "Arab Emirates Dirham (AED)",
        "BDT" => "Bangladeshi Taka (BDT)",
        "BRL" => "Brazilian Real (BRL)",
        "BGN" => "Bulgarian Lev (BGN)",
        "CAD" => "Canadian Dollar (CAD)",
        "CNY" => "Chinese Yuan (CNY)",
        "EUR" => "Euro (EUR)",
        "FJD" => "Fiji Dollar (FJD)",
        "GHS" => "Ghana Cedis (GHS)",
        "HKD" => "Hong Kong Dollar (HKD)",
        "INR" => "India Rupees (INR)",
        "IDR" => "Indonesian Rupiah (IDR)",
        "JPY" => "Japanese Yen (JPY)",
        "LVL" => "Latvian Lats (LVL)",
        "MYR" => "Malaysian Ringgit (MYR)",
        "MUR" => "Mauritian Rupee (MUR)",
        "NZD" => "New Zealand Dollar (NZD)",
        "PGK" => "Papua New Guinean Kina (PGK)",
        "PEN" => "Peruvian Soles (PEN)",
        "PHP" => "Philippine Peso (PHP)",
        "GBP" => "Pound Sterling (GBP)",
        "RUB" => "Russian Ruble (RUB)",
        "SAR" => "Saudi Arabian Riyal (SAR)",
        "SGD" => "Singapore Dollar (SGD)",
        "LKR" => "Sri Lanka Rupees (LKR)",
        "WST" => "Samoan Tālā (WST)",
        "SBD" => "Solomon Island Dollar (SBD)",
        "ZAR" => "South Africa Rand (ZAR)",
        "KRW" => "South Korean Won (KRW)",
        "THB" => "Thai Baht (THB)",
        "TOP" => "Tongan Pa'anga (TOP)",
        "TVD" => "Tuvaluan Dollar (TVD)",
        "USD" => "US Dollar (USD)",
        "VUV" => "Vanuatu Vatu (VUV)",
        "VND" => "Vietnamese Dong (VND)"
    );

    /**
     * The currently selected default currency code
     *
     * @var string $currency Current currency
     */
    private $currency = "AUD";

    /**
     * List of gadget layout types
     *
     * @var array $layouts List of layouts
     */
    private $layouts = array(
        "horiz" => "Wide / Horizontal Layout",
        "vert" => "Tall / Vertical Layout"
    );

    /**
     * The currently selected default layout
     *
     * @var string $layout Current layout
     */
    private $layout = "horiz";

    /**
     * Show or hide the image previews / thumbnails
     *
     * @var array $previews List of options
     */
    private $previews = array(
        "true" => "Display Room Preview Images",
        "false" => "Hide Room Preview Images"
    );

    /**
     * The currently selected default preview status
     *
     * @var string $layout Current status
     */
    private $preview = "true";

    /**
     * Called on instantiation of this class
     */
    public function __construct($caller = "", $version = "")
    {
        /** Set up the plugin's basics */
        $this->caller = substr($caller, strrpos($caller, DIRECTORY_SEPARATOR) + 1);
        $this->path = trailingslashit(substr($caller, 0,strrpos($caller, DIRECTORY_SEPARATOR)));
        $this->uri = plugin_dir_url($this->path.$this->caller);
        $this->namespace = str_replace(".php", "", $this->caller);
        $this->version = $version;

        /** Get the Code Canyon info */
        if (file_exists("{$this->path}lib/cc.php")) {
            require_once "{$this->path}lib/cc.php";
            $this->slug = $code_canyon_slug;
            $this->item = $code_canyon_item;
            $this->purchase = "https://codecanyon.net/item/{$this->slug}/{$this->item}";
        }

        /** Add the options page */
        add_action('admin_menu', array($this,'options_menu'));
        add_action('admin_enqueue_scripts',array($this, 'load_assets'));

        /** Add a link to the options page in the plugin list */
        add_filter('plugin_action_links', array($this,'settings_link'), 10, 2);

        /** Check if we're looking at a pro version */
        $this->check_status();

        $this->status = __("Pro features temporarily enabled while the plugin developer sorts out some stuff with Envato", $this->namespace);
        $this->isExpired = false;
        $this->isPro = true;
    }

    /**
     * Adds a "Settings" link to the plugin entry, on the plugin
     * management page
     *
     * @param array $links List of current links
     * @return array Updates list of links
     */
    public function settings_link($links = array(), $file = "")
    {
        /** Get the actual file name, from the path supplied */
        $file = substr($file, strrpos($file, "/") + 1);

        /** If we're looking at this plugin, add the settings link */
        if ($file == $this->caller) {
            $links[] = '<a href="'.admin_url("options-general.php?page={$this->settings_menu_slug}").'">Settings</a>';
        }

        /** Return to the plugin list caller */
        return $links;
    }

    /**
     * Enqueues the Admin assets
     */
    public function load_assets()
    {
        wp_register_style($this->namespace."-style", $this->uri."assets/css/dist/admin.css", false, $this->version);
        wp_enqueue_style($this->namespace."-style");

        wp_enqueue_script($this->namespace."-script", $this->uri."assets/js/dist/admin-min.js", array("jquery", "wp-color-picker"), $this->version, true);
    }

    /**
     * Build out the options menu
     */
    public function options_menu()
    {
        add_options_page(
            $this->settings_page_title,
            $this->settings_menu_title,
            "manage_options",
            $this->settings_menu_slug,
            array($this, "options_page")
        );
    }

    /**
     * Render the options page
     */
    public function options_page()
    {
        wp_create_nonce($this->namespace."-options");

        /** Save the options, if needed */
        if (isset($_POST["submit"]) && strlen($_POST["submit"])) {
            $this->options_save();
        }

        $this->layout = get_option("{$this->namespace}-layout", "horiz");
        $this->currency = get_option("{$this->namespace}-currency", "AUD");
        $this->preview = get_option("{$this->namespace}-show-images", "true");
        require_once $this->path."templates/settings.php";
    }

    /**
     * Installs the base options for the plugin
     */
    public function install()
    {
        /** Define the defaults */
        $fields = array(
            "currency" => "AUD",
            "layout" => "horiz",
            "show-images" => "true",
            "columns" => "4",
            "label" => "Rooms"
        );

        /** Iterate through the defaults and set ONLY if needed */
        foreach ($fields AS $key => $value) {
            if (get_option("{$this->namespace}-{$key}", false) === false) {
                update_option("{$this->namespace}-{$key}",$value);
            }
        }
    }

    /**
     * Save options
     */
    private function options_save()
    {
        /** Create a list of the regular fields we need to save */
        $regular_fields = array(
            "purchase-key" => array("text", ""),
            "envato-username" => array("text", ""),
            "currency" => array("text", ""),
            "layout" => array("text", ""),
            "show-images" => array("text", ""),
        );

        /** Create a list of the Pro-Version fields */
        $pro_fields = array(
            "columns" => array("text", ""),
            "label" => array("text", ""),
        );

        /** Create the list of fields we need to work through */
        $fields = $regular_fields;

        if ($this->isPro) {
            $fields = array_merge($fields, $pro_fields);
        }

        /** Iterate through each field and update */
        foreach ($fields AS $key => $info) {

            /** Set the default */
            $value = $info[1];

            /** Get the updated value */
            switch($info[0]) {

                case "checkbox":
                    if (isset($_POST["{$this->namespace}-{$key}"])) {
                        $value = 1;
                    }
                    break;

                default:
                    if (isset($_POST["{$this->namespace}-{$key}"])) {
                        $value = sanitize_text_field($_POST["{$this->namespace}-{$key}"]);
                    }
            }

            update_option("{$this->namespace}-{$key}",$value);
        }
    }

    /**
     * Get the registration status of the plugin
     */
    private function check_status()
    {
        $info = get_option("{$this->namespace}-{$this->item}",false);

        if ($info !== false) {
            $info = json_decode(base64_decode($info));
        }

        if (is_array($info) && isset($info[1])) {

            switch($info[1]) {

                case "invalid":
                    $this->status = __("Item purchase code is invalid", $this->namespace);
                    break;

                case "verified":

                    /** Check for a VALID licence */
                    if (isset($info[2]) && (time() < strtotime($info[2]." 23:59:59"))) {

                        /** Licence is valid, and in-date */
                        $this->status = __("Item purchase code is valid", $this->namespace);
                        $this->isPro = true;

                    } else {

                        /** Licence is valid, but expired */
                        $this->status = __("Item purchase code has expired", $this->namespace);
                        $this->isExpired = true;

                        /** Update the licence status */
                        $status = array(
                            $info[0],
                            "unsupported",
                            $info[2]
                        );
                        $value = base64_encode(json_encode($status));
                        update_option("{$this->namespace}-{$this->item}", $value);
                    }

                    break;

                case "unsupported":
                    $this->status = __("Item purchase code has expired", $this->namespace);
                    $this->isExpired = true;
                    break;
            }
        }
    }

    /**
     * Returns a plain english description of the purchase
     * code's status
     *
     * @return string $status The status of the purchase code
     */
    public function get_status()
    {
        /** Locked, while Code Canyon sorts their crap out :-/ */
        return true;

        return $this->status;
    }
}
