<h1>Testing plugin</h1>


<div class="wrap">
    <h2><?=__('Settings OPTIONS', 'premmerce-testing-plugin')?></h2>
    <form method="post" action="">
        <h3><?=__('Section', 'premmerce-testing-plugin')?></h3>
        <p><?=__('Section about', 'premmerce-testing-plugin')?></p>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="text"><?=__('text', 'premmerce-testing-plugin')?></label></th>
                <td><input type="text" id="text" name="premmerce_options[premmerce_field_input]" value="<?php echo $options['premmerce_field_input']; ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="checkbox"><?=__('Checkbox', 'premmerce-testing-plugin')?></label></th>
                <td><input type="checkbox" id="checkbox" name="premmerce_options[premmerce_field_checkbox]" value="1" <?php checked($options['premmerce_field_checkbox']); ?> /></td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="checkbox"><?=__('Select', 'premmerce-testing-plugin')?></label></th>
                <td>
                    <select name="premmerce_options[premmerce_field_select]">
                        <option value="1" <?php selected($options['premmerce_field_select'], 0 ); ?>><?=__('Not selected', 'premmerce-testing-plugin')?></option>
                        <option value="1" <?php selected($options['premmerce_field_select'], 1 ); ?>><?=__('Variant 1', 'premmerce-testing-plugin')?></option>
                        <option value="2" <?php selected($options['premmerce_field_select'], 2 ); ?>><?=__('Variant 2', 'premmerce-testing-plugin')?></option>
                        <option value="3" <?php selected($options['premmerce_field_select'], 3 ); ?>><?=__('Variant 3', 'premmerce-testing-plugin')?></option>
                    </select>
                </td>
            </tr>
        </table>
        <?php submit_button('Зберегти налаштування' ); ?>
    </form>
</div>