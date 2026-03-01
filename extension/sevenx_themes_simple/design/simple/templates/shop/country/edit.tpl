{if is_set( $countries ) | not }
    {def $countries = fetch( 'content', 'country_list' )|prepend( fetch( 'content', 'country_list', hash( 'filter', 'Name', 'value', 'United States of America' ) ) )|prepend( fetch( 'content', 'country_list', hash( 'filter', 'Name', 'value', 'Canada' ) ) )}
{/if}
{default $max_len = 20
    $select_size = 1}

<select name="{$select_name}" size="{$select_size}">
{if and( is_set( $default_val ), is_set( $default_desc ) )}
    <option {if eq( $current_val, false )}selected="selected"{/if} value="{$default_val}">{$default_desc|wash}</option>
{/if}
{foreach $countries as $country}
    <option {if eq( $country['Name'], $current_val )} selected="selected" {/if} value="{if $use_country_code}{$country['Alpha2']}{else}{$country['Name']}{/if}">
    {$country['Name']}
    </option>
{/foreach}
</select>
{/default}
