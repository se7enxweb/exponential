    {def $counter = 0}
    {foreach $additional_tabs as $tab}
    {def $tab_title = ezini( concat( 'AdditionalTab_', $tab ), 'Title', 'admininterface.ini' )
         $tab_description = ezini( concat( 'AdditionalTab_', $tab ), 'Description', 'admininterface.ini' )
         $tab_header_template = ezini( concat( 'AdditionalTab_', $tab ), 'HeaderTemplate', 'admininterface.ini' )
         $last = false()}
    {* Title and Description in the language of the page when the tab names the context of its messages (TranslationContext) *}
    {if ezini_hasvariable( concat( 'AdditionalTab_', $tab ), 'TranslationContext', 'admininterface.ini' )}
        {def $tab_context = ezini( concat( 'AdditionalTab_', $tab ), 'TranslationContext', 'admininterface.ini' )}
        {if $tab_context}
            {set $tab_title = $tab_title|i18n( $tab_context )
                 $tab_description = $tab_description|i18n( $tab_context )}
        {/if}
        {undef $tab_context}
    {/if}
    {set $tab_title = $tab_title|wash()
         $tab_description = $tab_description|wash()}
    {set $counter = $counter|inc()}

    {if eq( $counter, $additional_tabs_count )}
        {set $last = true()}
    {/if}

    {if eq( $tab_header_template, '' )}
    <li id="node-tab-{$tab}" class="{if $last}last{else}middle{/if}{if $node_tab_index|eq( $tab )} selected{/if}">
        {if $tabs_disabled}
            <span class="disabled" title="{'Tab is disabled, enable with toggler to the left of these tabs.'|i18n( 'design/admin/node/view/full' )}">{$tab_title}</span>
        {else}
            <a href={concat( $node_url_alias, '/(tab)/', $tab )|ezurl} title="{$tab_description}">{$tab_title}</a>
        {/if}
    </li>
    {else}
        {include uri=concat( 'design:tabs/', $tab_header_template ) last=$last}
    {/if}

    {undef $tab_title $tab_description $tab_header_template $last}
    {/foreach}
    {undef $counter}
