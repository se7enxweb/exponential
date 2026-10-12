{def $row = fetch( 'content', 'site_data', hash( 'name', $name ) )}{if $row}{$row.value|wash}{else}false{/if}{undef $row}
