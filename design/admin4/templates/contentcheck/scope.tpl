{* The start location of a content check (doc/en/us/features/6.0/content-checks.md, "Where a check starts"), shared by
   every check that takes one. Include it inside the check's GET filter form (an a4-filterbar):

     {include uri='design:contentcheck/scope.tpl' scope=$scope}

   Variable: scope, from expContentCheckScope::forTemplate( $node_id, '<view>' ): node_id, node_name, path, view,
   query, presets (key, node_id, name, label, path, current), sites (parent_id, parent_label, items of node_id, name,
   current, within; more, more_url), browse_url. With only node_id and node_name it shows the field alone.
   The field is NodeID: the views read GET NodeID before the node in the address, and remember it for the user.
   Beneath the field: the quick scope buttons (contentcheck.ini [ScopeSettings] PresetList[]: Whole tree, Websites,
   Media) and the node picker, then a second row with the sites and sections under the content root (SiteRow*),
   "More..." opening the picker at that level when there are more. All of them are plain links to
   contentcheck/<view>/<node id> (no JavaScript needed); the current one is marked with aria-current.
   The same file is in design/admin and design/admin4. *}
{def $cc_scope_id = concat( 'cc-scope-', cond( is_set( $scope.view ), $scope.view, 'check' ) )
     $cc_scope_query = cond( is_set( $scope.query ), $scope.query, '' )}
    <div class="a4-field a4-field-wide">
        <label for="{$cc_scope_id}">{'Start location (node id)'|i18n( 'kernel/contentcheck' )}</label>
        <input type="text" id="{$cc_scope_id}" name="NodeID" value="{$scope.node_id|wash}" inputmode="numeric" pattern="[0-9]*" maxlength="10" aria-describedby="{$cc_scope_id}-help" />
        <span class="a4-help" id="{$cc_scope_id}-help">{if and( is_set( $scope.path ), $scope.path|ne( '' ) )}{'Now: %path'|i18n( 'kernel/contentcheck',, hash( '%path', $scope.path ) )|wash}{elseif $scope.node_name|ne( '' )}{'Now: %path'|i18n( 'kernel/contentcheck',, hash( '%path', $scope.node_name ) )|wash}{/if} {'1 is the whole tree. Your choice is remembered.'|i18n( 'kernel/contentcheck' )}</span>
{if and( is_set( $scope.presets ), is_set( $scope.view ), $scope.view|ne( '' ) )}
        <div class="a4-toolbar a4-quickpick">
        <nav class="a4-row" aria-labelledby="{$cc_scope_id}-presets">
            <span class="a4-quickpick-label" id="{$cc_scope_id}-presets">{'Start locations'|i18n( 'kernel/contentcheck' )}</span>
        {foreach $scope.presets as $preset}
            <a class="a4-btn a4-btn-small{if $preset.current} a4-btn-primary{/if}" href={concat( '/contentcheck/', $scope.view, '/', $preset.node_id, $cc_scope_query )|ezurl}{if $preset.current} aria-current="true"{/if}{if $preset.path|ne( '' )} title="{$preset.path|wash}"{/if}>{cond( is_set( $preset.label ), $preset.label, $preset.name )|wash}</a>
        {/foreach}
        {if $scope.browse_url|ne( '' )}
            <a class="a4-btn a4-btn-small a4-btn-ghost" href={$scope.browse_url|ezurl}><span class="a4-icon a4-bi-folder" aria-hidden="true"></span> {'Choose with the node picker'|i18n( 'kernel/contentcheck' )}</a>
        {/if}
        </nav>
{if and( is_set( $scope.sites ), $scope.sites.items|count|gt( 0 ) )}
        <nav class="a4-row" aria-labelledby="{$cc_scope_id}-sites">
            <span class="a4-quickpick-label" id="{$cc_scope_id}-sites">{'Sites and sections'|i18n( 'kernel/contentcheck' )}</span>
        {foreach $scope.sites.items as $site}
            <a class="a4-btn a4-btn-small{if $site.current} a4-btn-primary{elseif $site.within} is-within{/if}" href={concat( '/contentcheck/', $scope.view, '/', $site.node_id, $cc_scope_query )|ezurl}{if $site.current} aria-current="true"{/if}>{$site.name|wash}{if $site.within}<span class="a4-sr-only"> {'(contains the start location)'|i18n( 'kernel/contentcheck' )}</span>{/if}</a>
        {/foreach}
        {if $scope.sites.more}
            <a class="a4-btn a4-btn-small a4-btn-ghost" href={$scope.sites.more_url|ezurl} title="{'Choose another site or section with the node picker'|i18n( 'kernel/contentcheck' )|wash}">{'More…'|i18n( 'kernel/contentcheck' )}</a>
        {/if}
        </nav>
{/if}
        </div>
{/if}
    </div>
{undef $cc_scope_id $cc_scope_query}
