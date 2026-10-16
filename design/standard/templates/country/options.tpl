{* The options of every country select: the ranked groups of countrygroups.ini,
   then "All countries" A-Z, each in an <optgroup>. The caller draws the <select>
   itself (name, id, label, autocomplete) and includes this inside it.

   Parameters, all optional:
   - country_value_field   'Alpha2' (default) or 'Name': what an option posts
   - country_selected      array of stored values, matched against the code and the name
   - country_selected_map  hash keyed by code (the country attribute's content)
   - country_multiple      true for a multiple select: no country is listed twice
   - country_max_len       shorten the names to this many characters (0: never)
   - country_groups        groups as fetch( 'content', 'country_groups' ) returns them

   A country of a ranked group is repeated in "All countries" for a single
   select, but only its first option can be selected, so a stored value comes
   back once. Browser autofill picks the first option matching the value or
   the text, which is the ranked one: "United States" finds the United States
   of America, not the United States Minor Outlying Islands.
   Guide: doc/features/6.0/country-groups.md *}
{default $country_value_field = 'Alpha2'
         $country_selected = array()
         $country_selected_map = hash()
         $country_multiple = false()
         $country_max_len = 0}
{if is_set( $country_groups )}
    {def $country_option_groups = $country_groups}
{else}
    {def $country_option_groups = fetch( 'content', 'country_groups', hash( 'repeat', $country_multiple|not ) )}
{/if}
{def $country_option_code = ''}
{foreach $country_option_groups as $country_option_group}
<optgroup label="{$country_option_group.label|wash}">
{foreach $country_option_group.countries as $country_option}
{set $country_option_code = $country_option.Alpha2}
<option value="{$country_option.$country_value_field|wash}"{if and( is_set( $country_option.Repeated )|not, or( $country_selected|contains( $country_option_code ), $country_selected|contains( $country_option.Name ), is_set( $country_selected_map.$country_option_code ) ) )} selected="selected"{/if}>{if gt( $country_max_len, 0 )}{$country_option.Name|shorten( $country_max_len )|wash}{else}{$country_option.Name|wash}{/if}</option>
{/foreach}
</optgroup>
{/foreach}
{undef $country_option_code $country_option_groups}
{/default}
