<?php
/**
 * Displays the actual booking widgets
 */
class PhxResOnlinePublic
{
    /**
     * The version number of this plugin
     *
     * @var string $version Version number
     */
    private $version = "1.0.0";

    /**
     * Name of the file calling this class
     *
     * @var string $caller File name
     */
    private $caller = "";

    /**
     * Faux namespace, used for HTML calls and tags
     *
     * @var string $namespace Faux name space
     */
    private $namespace = "";

    /**
     * Flags if the default values have been loaded
     *
     * @var bool $haveDefaults Defaults loaded?
     */
    private $haveDefaults = false;

    /**
     * List of available option fields
     *
     * @var array $fields Option fields
     */
    private $fields = array(
        "currency",
        "layout",
        "show-images",
        "columns",
        "label"
    );

    /**
     * Flags if the necessary scripts and styles have been
     * enqueued yet
     *
     * @var bool $enqueued Scripts enequed?
     */
    private $enqueued = false;


    /**
     * Called on instantiation
     */
    public function __construct($caller = "", $version = "")
    {
        /** Set up the plugin's basics */
        $this->caller = substr($caller, strrpos($caller, DIRECTORY_SEPARATOR) + 1);
        $this->namespace = str_replace(".php", "", $this->caller);
        $this->version = $version;

        /** Register the shortcode */
        add_shortcode("resonline", array($this, "display_gadget"));
    }

    /**
     * Called on destruction
     */
    public function __destruct()
    {

    }

    /**
     * Attempts to retrieve the plugin settings / defaults
     */
    private function get_defaults()
    {
        /** Check if we've loaded the defaults already */
        if (!$this->haveDefaults) {

            /**
             * Nope, we don't have the default values. Grab
             * a list, and load them into their relevant
             * properties
             */
            foreach ($this->fields AS $key) {
                $value = get_option("{$this->namespace}-{$key}", false);
                $key = str_replace("show-images", "showImages",$key);
                $this->$key = $value;
            }

            /** Flag the defaults as loaded */
            $this->haveDefaults = true;
        }
    }

    /**
     * Creates the code for a ResOnline Booking Gadget
     *
     * @param array $atts List of options / attributes
     * @return string Widget if available, error if there's an issue
     */
    public function display_gadget($atts = array())
    {
        /** Make sure we have the bare minimum */
        if (!isset($atts["id"]) || !strlen($id = preg_replace("/[^0-9]/", "", $atts["id"]))) {
            return "<p><strong>ResOnline Error:</strong> The Hotel ID appears to be missing or invalid.</p>";
        }

        /** Add the Gadget scripts, if needed */
        if (!$this->enqueued) {

            /** Add the ResOnline scripts */
            wp_enqueue_script("{$this->namespace}-resonline-base", "//gadgets.securetravelpayments.com/_shared/base.jsz", array("jquery"), $this->version, true);
            wp_enqueue_script("{$this->namespace}-resonline-room-types", "//gadgets.securetravelpayments.com/room-types/room-types.jsz", array("jquery", "{$this->namespace}-resonline-base"), $this->version, true);

            /** Add the base CSS */
            wp_enqueue_style('phx-resonline-booking-gadget-css', '//gadgets.securetravelpayments.com/_shared/css/all.cssz');

            /** ...and flag it, so we don't double-up */
            $this->enqueued = true;
        }

        /**
         * Get the settings for this gadget
         *
         * Note: We only need to escape passed attributes, not
         * options pulled from the DB (as options in the DB have
         * already been escaped)
         */
        $this->get_defaults();
        $currency = (isset($atts["currency"]) ? esc_js($atts["currency"]) : $this->currency);
        $layout = (isset($atts["layout"]) ? esc_js($atts["layout"]) : $this->layout);
        $showImages = (isset($atts["show-images"]) ? esc_js($atts["show-images"]) : $this->showImages);
        $columns = (isset($atts["columns"]) ? esc_js($atts["columns"]) : $this->columns);
        $label = (isset($atts["label"]) ? esc_js($atts["label"]) : $this->label);

        /** Build the javascript hook... */
        $inline =
            "\$w('#phx-resonline-booking-grid-{$id}').roomTypesGadget({
                hotelID: '{$id}',
                autoTickedNights: false,
                showPromotionCode: false,
                defaultCurrency: '{$currency}',
                defaultGridColumns: '{$columns}',
                gridLabel: '{$label}',
                layout: '{$layout}',
                defaultNights: 1,
                defaultDaysOut: 0,
                showRoomImages: {$showImages}";
        $inline .= "
           });";
        wp_add_inline_script("{$this->namespace}-resonline-room-types", $inline);

        /** Return the HTML for the base grid holder */
        return '<div id="phx-resonline-booking-grid-'.$id.'">&nbsp;</div>';
    }
}
