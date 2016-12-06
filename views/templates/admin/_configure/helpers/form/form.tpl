{*	Block Customer Group
*	Add a block in the registration form where the customer can select his group.
*
*	@author			Daniele Gambaletta
*	@version		1.0.0
*	@PS version		1.6 recommended
*	@license   		MIT
*}

{extends file="helpers/form/form.tpl"}


{block name="input_row"}

    {if $input.type == 'table_group_list'}

        {assign var=groups value=$input.values}
        {if isset($groups) && count($groups) > 0}
            <div class form-group>
                <label class="control-label col-lg-3">
                    <span class="label-tooltip"
                          data-toggle="tooltip"
                          data-html="true" title=""
                          data-original-title="{$input.hint}">
                        {$input.label}
                    </span>
                </label>
                <div class="col-lg-9">
                    <div class="panel">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>
                                    {assign var=name value=$input.name|cat:'[]'}
                                    <input type="checkbox" name="checkme" id="checkme"
                                           onclick="checkDelBoxes(this.form, '{$name}', this.checked)"/>
                                </th>
                                <th>{l s='ID' mod='blockcustomergroup'}</th>
                                <th>{l s='Name' mod='blockcustomergroup'}</th>
                                <th>{l s='Reduction' mod='blockcustomergroup'}</th>
                                <th>{l s='Show Prices' mod='blockcustomergroup'}</th>

                            </tr>
                            </thead>
                            <tbody>
                            {foreach $groups as $key => $group}
                                <tr>
                                    <td>
                                        {assign var=id_checkbox value=$group['id_group']}
                                        <input type="checkbox" name="{$name}"
                                               id="{$id_checkbox}"
                                               value="{$id_checkbox}"
                                               {if ($fields_value[$id_checkbox] == 1)}checked="checked" {/if} />
                                    </td>
                                    <td>
                                        {$group['id_group']}
                                    </td>
                                    <td>
                                        {$group['name']}
                                    </td>
                                    <td>
                                        {$group['reduction']}
                                    </td>
                                    <td>
                                        {if ($group['show_prices'])}
                                            <a class="list-action-enable action-enabled">
                                                <i class="icon-check"></i></a>
                                        {else}
                                            <a class="list-action-enable action-disabled">
                                                <i class="icon-remove"></i></a>
                                        {/if}
                                    </td>

                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                        <div class="help-block" style="padding-top: 5px;">
                            {$input.desc}
                        </div>
                    </div>
                </div>
            </div>
        {else}
            <p>{l s='No groups have been created.' mod='blockcustomergroup'}</p>
        {/if}
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="field"}
    {block name="input"}
        {if $input.type == 'switch_input_type'}
            <div class="col-lg-{if isset($input.col)}{$input.col|intval}{else}9{/if}{if !isset($input.label)} col-lg-offset-3{/if}">
            <span class="switch prestashop-switch fixed-width-lg">
                {foreach $input.values as $value}
                    <input type="radio"
                           name="{$input.name}"{if $value.value == 1} id="{$input.name}_on"{else} id="{$input.name}_off"{/if}
                           value="{$value.value}"{if $fields_value[$input.name] == $value.value} checked="checked"{/if}
                            {if isset($input.disabled) && $input.disabled} disabled="disabled"{/if}/>

                                            {strip}
                    <label {if $value.value == 1} for="{$input.name}_on"{else} for="{$input.name}_off"{/if}>
                        {$value.label}
                    </label>
                {/strip}
                {/foreach}
                <a class="slide-button btn"></a>
            </span>
                {if isset($input.desc)}
                    <p class="help-block">
                        {$input.desc}
                    </p>
                {/if}
            </div>
        {else}
            {$smarty.block.parent}
        {/if}
    {/block}
{/block}