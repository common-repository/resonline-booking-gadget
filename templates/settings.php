<script>
    var phx_validator = "<?php echo $this->namespace ?>";
    var phx_pro = <?php echo ($this->isPro ? "true" : "false") ?>;
    var validator_timer;
    var validator_text = "<?php _e("Validate", PHX_DOMAIN) ?>";
</script>
<div class="wrap phx-options">
    <h1>
        <?php echo $this->settings_page_title ?>
        <div class="version">Version <?php echo $this->version ?></div>
    </h1>
    <form method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
        <?php wp_nonce_field("update", $this->namespace."-options", wp_referer_field(false), true) ?>

        <hr />
        <?php if ($this->isExpired) { ?>
            <h2>Licence Expired</h2>
            <?php if (strlen($this->slug)) { ?>
                <p><a href="<?php echo $this->purchase ?>" target="_blank">Click here to renew your licence.</a></p>
            <?php } ?>
            <p>The licence for this plugin has expired. <strong>The front-end functionality will continue to work as normal</strong>, but you will not be able to update any of the <i>Pro Options</i> until you renew support for this plugin.</p>
            <p>Once renewed, there's need to re-upload the plugin, just enter your <i>Code Canyon / Envato Username</i> and <i>Purchase Code</i>, click <i>Verify</i>, and your <i>Pro Options</i> will be restored.</p>
        <?php } else if (!$this->isPro) { ?>
            <h2>Unlock Pro Version</h2>
            <?php if (strlen($this->slug)) { ?>
                <p><a href="<?php echo $this->purchase ?>" target="_blank">Click here to upgrade, and unlock the Pro features</a></p>
            <?php } ?>
            <p>No need to re-upload the plugin, just enter your <i>Code Canyon / Envato Username</i> and <i>Purchase Code</i>...</p>
        <?php } else { ?>
            <h2>Licence Information</h2>
        <?php } ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->namespace ?>-envato-username">Code Canyon Username</label>
                    </th>
                    <td>
                        <input type="text" name="<?php echo $this->namespace ?>-envato-username" id="envato-username" class="regular-text" value="<?php echo esc_attr(get_option($this->namespace."-envato-username","")) ?>" />
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->namespace ?>-purchase-key">Item Purchase Code</label>
                    </th>
                    <td>
                        <input type="text" name="<?php echo $this->namespace ?>-purchase-key" id="purchase-key" class="regular-text" value="<?php echo esc_attr(get_option($this->namespace."-purchase-key","")) ?>" />
                    </td>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                    <td>
                        <button class="button button-secondary check-licence" readonly="readonly"><?php _e("Validate", PHX_DOMAIN) ?></button>
                        <?php if (strlen($this->status)) { ?>
                            <span class="status-notice bump-right color-<?php echo ($this->isPro ? 'success' : 'danger') ?>"><?php echo $this->status ?></span>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr />
        <h2>Standard Options</h2>
        <table class="form-table">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->namespace ?>-currency">Currency</label>
                    </th>
                    <td>
                        <select name="<?php echo $this->namespace ?>-currency" id="currency">
                            <?php foreach ($this->currencies as $code => $label) { ?>
                                <option value="<?php echo $code ?>" <?php echo ($code == $this->currency ? 'selected="selected"' : "") ?>><?php echo  $label ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->namespace ?>-layout">Layout</label>
                    </th>
                    <td>
                        <select name="<?php echo $this->namespace ?>-layout" id="layout">
                            <?php foreach ($this->layouts as $code => $label) { ?>
                                <option value="<?php echo $code ?>" <?php echo ($code == $this->layout ? 'selected="selected"' : "") ?>><?php echo  $label ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->namespace ?>-show-images">Images</label>
                    </th>
                    <td>
                        <select name="<?php echo $this->namespace ?>-show-images" id="show-images">
                            <?php foreach ($this->previews as $code => $label) { ?>
                                <option value="<?php echo $code ?>" <?php echo ($code == $this->preview ? 'selected="selected"' : "") ?>><?php echo  $label ?></option>
                            <?php } ?>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>

        <hr />
        <h2>Pro Options</h2>
        <?php if (!$this->isPro) { ?>
            <p>The <i>Pro</i> version of this plugin is needed to access these functions.</p>
        <?php } ?>
        <table class="form-table <?php echo (!$this->isPro ? 'no-pro' : '') ?>">
            <tbody>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->namespace ?>-columns">Columns</label>
                    </th>
                    <td>
                        <input type="number" name="<?php echo $this->namespace ?>-columns" id="columns" class="small-text" value="<?php echo esc_attr(stripslashes_deep(get_option($this->namespace."-columns","14"))) ?>" <?php echo (!$this->isPro ? 'readonly="readonly"' : '') ?> />
                        <br /><small>Applies to <i>Wide / Horizontal Layout</i> only</small>
                    </td>
                </tr>
                <tr>
                    <th scope="row">
                        <label for="<?php echo $this->namespace ?>-label">Room Label</label>
                    </th>
                    <td>
                        <input type="text" name="<?php echo $this->namespace ?>-label" id="label" class="regular-text" value="<?php echo esc_attr(stripslashes_deep(get_option($this->namespace."-label","Rooms"))) ?>" <?php echo (!$this->isPro ? 'readonly="readonly"' : '') ?> />
                    </td>
                </tr>
            </tbody>
        </table>
        <p><?php submit_button("Save Options") ?></p>

        <hr />
        <h2>Shortcodes</h2>
        <p><a class="button button-large" target="_blank" href="https://plugins.phoenixonline.io/plugins/resonline/resonline-shortcodes">View Shortcode Documentation</a></p>
    </form>
</div>
