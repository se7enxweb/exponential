{* The start location of a content check (doc/features/6.0/content-checks.md, "Where a check starts"), shared by
   every check that takes one. Include it inside the check's GET filter form (an a4-filterbar):

     {include uri='design:contentcheck/scope.tpl' scope=$scope}

   Variable: scope, from expContentCheckScope::forTemplate( $node_id, '<view>' ): node_id, node_name, path, view,
   presets (key, node_id, name, path, current), browse_url. With only node_id and node_name it shows the field alone.
   The field is NodeID: the views read GET NodeID before the node in the address, and remember it for the user.
   The presets and "Choose with the node picker" are links; the picker comes back to the same check. The same file
   is in design/admin and design/admin4. *}
{def $cc_scope_id = concat( 'cc-scope-', cond( is_set( $scope.view ), $scope.view, 'check' ) )}
    <div class="a4-field a4-field-wide">
        <label for="{$cc_scope_id}">{'Start location (node id)'|i18n( 'kernel/contentcheck' )}</label>
        <input type="text" id="{$cc_scope_id}" name="NodeID" value="{$scope.node_id|wash}" inputmode="numeric" pattern="[0-9]*" maxlength="10" aria-describedby="{$cc_scope_id}-help" />
        <span class="a4-help" id="{$cc_scope_id}-help">{if and( is_set( $scope.path ), $scope.path|ne( '' ) )}{'Now: %path'|i18n( 'kernel/contentcheck',, hash( '%path', $scope.path ) )|wash}{elseif $scope.node_name|ne( '' )}{'Now: %path'|i18n( 'kernel/contentcheck',, hash( '%path', $scope.node_name ) )|wash}{/if} {'1 is the whole tree. Your choice is remembered.'|i18n( 'kernel/contentcheck' )}</span>
{if and( is_set( $scope.presets ), is_set( $scope.view ), $scope.view|ne( '' ) )}
        <nav class="a4-row" aria-label="{'Start locations'|i18n( 'kernel/contentcheck' )|wash}">
        {foreach $scope.presets as $preset}
            <a class="a4-btn a4-btn-small{if $preset.current} a4-btn-primary{/if}" href={concat( '/contentcheck/', $scope.view, '/', $preset.node_id )|ezurl}{if $preset.current} aria-current="true"{/if}{if $preset.path|ne( '' )} title="{$preset.path|wash}"{/if}>{$preset.name|wash}</a>
        {/foreach}
        {if $scope.browse_url|ne( '' )}
            <a class="a4-btn a4-btn-small a4-btn-ghost" href={$scope.browse_url|ezurl}><span class="a4-icon a4-bi-folder" aria-hidden="true"></span> {'Choose with the node picker'|i18n( 'kernel/contentcheck' )}</a>
        {/if}
        </nav>
{/if}
    </div>
{undef $cc_scope_id}
