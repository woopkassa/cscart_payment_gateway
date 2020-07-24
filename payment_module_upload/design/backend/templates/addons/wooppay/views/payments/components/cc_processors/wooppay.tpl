<div class="control-group">
    <label class="control-label" for="wooppay_url">{__("addons.wooppay.url")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][url]" id="wooppay_url" value="{$processor_params.url}" size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="wooppay_login">{__("addons.wooppay.login")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][login]" id="wooppay_login" value="{$processor_params.login}" size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="wooppay_password">{__("addons.wooppay.password")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][password]" id="wooppay_password" value="{$processor_params.password}" size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="wooppay_prefx">{__("addons.wooppay.prefix")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][prefix]" id="wooppay_prefix" value="{$processor_params.prefix}" size="60">
    </div>
</div>

<div class="control-group">
    <label class="control-label" for="wooppay_service">{__("addons.wooppay.service")}:</label>
    <div class="controls">
        <input type="text" name="payment_data[processor_params][service]" id="wooppay_service" value="{$processor_params.service}" size="60">
    </div>
</div>

