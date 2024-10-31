<?php
/**
 * Provides the base for AJAX functions throughout
 * the plugin
 */
class PhxResOnlineAjax
{
    /**
     * The version number of this plugin
     *
     * @var string $version Version number
     */
    private $version = "1.0.0";

    /**
     * Is this the pro version of this plugin?
     *
     * @var bool $isPro "Purchased" flag
     */
    private $isPro = false;

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
        }

        /** Set up AJAX functions */
        add_action("wp_ajax_{$this->namespace}_validate_purchase", array($this, "validate_purchase"));
    }

    /**
     * Attempts to validate a purchase key
     */
    public function validate_purchase()
    {
        /** Check if we have a key to, well, check */
        if (!isset($_POST['purchase_key']) || !strlen(trim($_POST['purchase_key']))) {

            /** Nope, we don't */
            $return = array(
                "success" => 0,
                "message" => __("Licence key is invalid", $this->namespace)
            );

            echo json_encode($return);
            wp_die();
        }

        /** Fetch the info from the verification system */
        $options = array(
            "method" => "POST",
            "timeout" => 15,
            "headers" => array(
                "Content-type: application/x-www-form-urlencoded",
            ),
            "body" => array(
                "username" => sanitize_text_field($_POST["envato_username"]),
                "code" => sanitize_text_field($_POST['purchase_key']),
                "item" => $this->item
            ),
            "cookies" => array()
        );
        $response = wp_remote_post("https://verify.phoenixonline.io/", $options);

        /** Work out what to return, based on the result of the check */
        if (isset($response["body"]) && ($purchase_info = (object)json_decode($response["body"]))) {

            if (isset($purchase_info->status)) {

                switch($purchase_info->status) {

                    case "invalid":
                        $return = array(
                            "success" => 0,
                            "message" => __("Licence key is invalid", $this->namespace)
                        );
                        $status = array(
                            $this->item,
                            $purchase_info->status,
                            ""
                        );
                        break;

                    case "verified":
                        $return = array(
                            "success" => 1,
                            "message" => __("Purchase Verified", $this->namespace)
                        );
                        $status = array(
                            $this->item,
                            $purchase_info->status,
                            $purchase_info->date
                        );
                        break;

                    case "unsupported":
                        $return = array(
                            "success" => 1,
                            "message" => __("Licence has expired", $this->namespace)
                        );
                        $status = array(
                            $this->item,
                            $purchase_info->status,
                            $purchase_info->date
                        );
                        break;

                    default:
                        $return = array(
                            "success" => 0,
                            "message" => __("Could not verify purchase", $this->namespace)
                        );
                        $status = array(
                            $this->item,
                            $purchase_info->status,
                            $purchase_info->date
                        );
                }

            } else {

                /** If we're here, something's gone wrong */
                $return = array(
                    "success" => 0,
                    "message" => __("Could not contact Envato. Please try again.", $this->namespace)
                );
            }
        }



        if (isset($status)) {
            $value = base64_encode(json_encode($status));
            update_option("{$this->namespace}-{$this->item}", $value);
        }

        echo json_encode($return);
        wp_die();
    }
}
