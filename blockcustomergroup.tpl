{*	Block Customer Group
*	Add a block in the registration form where the customer can select his group.
*
*	@author			Daniele Gambaletta
*	@version		1.0.0
*	@PS version		1.6 recommended
*	@license   		MIT
*}

<fieldset class="account_creation customergroup">
    <h3 class="page-subheading">{l s='Customer group' mod='blockcustomergroup'}</h3>
    <p style="margin: 9px 0 5px 0">

        {if $type == '1'}
            <select size="1" id="customer_group" name="customer_group" style="width: 20%">
                {foreach $groups as $row}
                    {if $show_reduction == '1'}
                        <option value="{$row['name']}">{$row['name']} ({$row['reduction']}&#37;)</option>
                    {else}
                        <option value="{$row['name']}">{$row['name']}</option>
                    {/if}
                {/foreach}
            </select>
        {else}

            {assign var = loopingFirstTime  value = true}
            {foreach $groups as $row}
                {if $loopingFirstTime == true}
                    {assign var = checked  value = 'checked="checked"'}
                    {assign var = loopingFirstTime  value = false}
                {else}
                    {assign var = checked  value = ''}
                {/if}
                <input type="radio" name="customer_group" value="{$row['name']}" {$checked}>
                {$row['name']}
                <br>
            {/foreach}

        {/if}
    </p>
    <label for="customer_group" style="color:#777; font-weight: lighter; margin-bottom: 10px">{$module_message}</label>
</fieldset>