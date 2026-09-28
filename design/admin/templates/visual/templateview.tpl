{ezcss_require( 'visual-templateview.css' )}

{section show=or( $not_removed, $ini_not_saved )}
<div class="message-error">
<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The overrides could not be removed.'|i18n( 'design/admin/visual/templateview' )}</h2>

{section show=$not_removed}
<p>{'The following files and override rules could not be removed because of insufficient file permissions'|i18n( 'design/admin/visual/templateview' )}:</p>
<ul>

{section var=item loop=$not_removed}
    <li>{$item.filename|wash}</li>
{/section}

</ul>
{/section}

{if $ini_not_saved}
<p>{if $save_error}{$save_error|wash}{else}{'The override.ini file could not be modified because of insufficient permission.'|i18n( 'design/admin/visual/templateview' )}{/if}</p>
{/if}

</div>
{/section}

{if $not_owned}
<div class="message-warning">
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Some overrides were not removed'|i18n( 'design/admin/visual/templateview' )}</h2>
    <p>{'These are defined by an extension, not in the siteaccess settings, and have to be removed there:'|i18n( 'design/admin/visual/templateview' )} {foreach $not_owned as $name}<code>{$name|wash}</code>{delimiter}, {/delimiter}{/foreach}</p>
</div>
{/if}

{if and( $save_error, $ini_not_saved|not )}
<div class="message-error">
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The overrides were not changed'|i18n( 'design/admin/visual/templateview' )}</h2>
    <p>{$save_error|wash}</p>
</div>
{elseif $save_message}
<div class="message-feedback">
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {$save_message|wash}</h2>
</div>
{/if}

<form method="post" name="templateview" action={concat( '/visual/templateview', $template_settings.template )|ezurl}>

<div class="context-block visual-templateview">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Overrides for <%template_name> template in <%current_siteaccess> siteaccess (%override_count)'|i18n( 'design/admin/visual/templateview',, hash( '%template_name', $template_settings.template, '%current_siteaccess', $current_siteaccess, '%override_count', $template_settings.custom_match|count ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="templateview-facts">
    <div class="templateview-fact">
        <span class="templateview-fact-label">{'Default template resource'|i18n( 'design/admin/visual/templateview' )}</span>
        {if $template_settings.base_dir}<code>{$template_settings.base_dir|wash}{$template_settings.template|wash}</code>{else}<em>{'No source template resource found.'|i18n( 'design/admin/visual/templateview' )}</em>{/if}
    </div>
    <div class="templateview-fact">
        <label class="templateview-fact-label" for="templateview-siteaccess">{'Siteaccess'|i18n( 'design/admin/visual/templateview' )}</label>
        <select name="CurrentSiteAccess" id="templateview-siteaccess">
        {section name=SiteAccess loop=ezini('SiteAccessSettings','RelatedSiteAccessList')}
            <option value="{$SiteAccess:item|wash}"{if eq( $current_siteaccess, $:item )} selected="selected"{/if}>{$:item|wash}</option>
        {/section}
        </select>
        <input class="button" type="submit" name="SelectCurrentSiteAccessButton" value="{'Set'|i18n( 'design/admin/visual/templateview' )}" />
    </div>
</div>

{section show=$template_settings.custom_match}

<p class="templateview-hint">{'The overrides are tried from the top, and the first whose conditions all match is used. Drag an override by its handle to a new place, or use its arrows; every change is saved at once as the Priority of these overrides in settings/siteaccess/%siteaccess/override.ini.append.php.'|i18n( 'design/admin/visual/templateview',, hash( '%siteaccess', $current_siteaccess|wash ) )}</p>

<div class="override-filter">
    <label for="override-filter-input">{'Filter'|i18n( 'design/admin/visual/templateview' )}</label>
    <input type="search" id="override-filter-input" class="halfbox" placeholder="{'Name, file or condition'|i18n( 'design/admin/visual/templateview' )|wash}" />
    <span class="override-filter-note" id="override-filter-note" hidden="hidden">{'Clear the filter to move overrides.'|i18n( 'design/admin/visual/templateview' )}</span>
</div>

<ol class="override-order" id="override-order"
    data-url={concat( '/visual/templateview', $template_settings.template )|ezurl}
    data-saving="{'Saving...'|i18n( 'design/admin/visual/templateview' )|wash}"
    data-failed="{'The order could not be saved.'|i18n( 'design/admin/visual/templateview' )|wash}">
{section var=CustomMatch loop=$template_settings.custom_match}
{def $name = $CustomMatch.item.override_name
     $own = $own_overrides|contains( $CustomMatch.item.override_name )}
<li class="override-card" data-name="{$name|wash}">
    <input type="hidden" name="ShownOverrideList[]" value="{$name|wash}" />
    <div class="override-head">
        <span class="override-grip" title="{'Drag to move'|i18n( 'design/admin/visual/templateview' )|wash}" aria-hidden="true"></span>
        <span class="override-position">{$CustomMatch.number}</span>
        <span class="override-name">{$name|wash}</span>
        {if $own}
            <span class="override-badge override-badge-own" title="{'Defined in settings/siteaccess/%siteaccess/override.ini.append.php'|i18n( 'design/admin/visual/templateview',, hash( '%siteaccess', $current_siteaccess|wash ) )|wash}">{'siteaccess'|i18n( 'design/admin/visual/templateview' )}</span>
        {else}
            <span class="override-badge override-badge-extension" title="{'Defined by an extension; its order and conditions can be changed here, removing it is done in the extension'|i18n( 'design/admin/visual/templateview' )|wash}">{'extension'|i18n( 'design/admin/visual/templateview' )}</span>
        {/if}
        <span class="override-actions">
            {if $CustomMatch.item.match_file}
            <a class="override-edit" href={concat( '/visual/templateedit/', $CustomMatch.item.match_file )|ezurl} title="{'Edit override template.'|i18n( 'design/admin/visual/templateview' )|wash}">{'Edit'|i18n( 'design/admin/visual/templateview' )}</a>
            {/if}
            {if $own}
            <label class="override-remove" title="{'Remove selected template overrides.'|i18n( 'design/admin/visual/templateview' )|wash}"><input type="checkbox" name="RemoveOverrideArray[]" value="{$name|wash}" /> {'Remove'|i18n( 'design/admin/visual/templateview' )}</label>
            {/if}
            <button type="button" class="override-up" title="{'Try earlier'|i18n( 'design/admin/visual/templateview' )|wash}" aria-label="{'Try %name earlier'|i18n( 'design/admin/visual/templateview',, hash( '%name', $name ) )|wash}">&#8593;</button>
            <button type="button" class="override-down" title="{'Try later'|i18n( 'design/admin/visual/templateview' )|wash}" aria-label="{'Try %name later'|i18n( 'design/admin/visual/templateview',, hash( '%name', $name ) )|wash}">&#8595;</button>
        </span>
    </div>
    <div class="override-file">
        {if $CustomMatch.item.match_file}<code>{$CustomMatch.item.match_file|wash}</code>{else}<em>{'No file matched'|i18n( 'design/admin/visual/templateview' )}</em> <code>{$CustomMatch.item.match_file_raw|wash}</code>{/if}
    </div>
    <div class="override-conditions">
        {if and( is_set( $CustomMatch.item.conditions ), $CustomMatch.item.conditions )}
            {foreach $CustomMatch.item.conditions as $key => $value}
            <div class="override-condition">
                <label class="override-condition-key" for="match-{$name|wash}-{$key|wash}">{$key|wash}</label>
                <input type="text" id="match-{$name|wash}-{$key|wash}" name="MatchArray[{$name|wash}][{$key|wash}]" value="{$value|wash}" size="24" />
                <label class="override-condition-remove"><input type="checkbox" name="RemoveMatchArray[{$name|wash}][{$key|wash}]" value="1" /> {'Remove this condition'|i18n( 'design/admin/visual/templateview' )}</label>
            </div>
            {/foreach}
        {else}
            <p class="override-no-conditions">{'No conditions: used for every request of this template.'|i18n( 'design/admin/visual/templateview' )}</p>
        {/if}
        <details class="override-add">
        <summary>{'Add condition'|i18n( 'design/admin/visual/templateview' )}</summary>
        <div class="override-condition override-condition-new">
            <select name="NewMatch[{$name|wash}][key]" aria-label="{'Add condition'|i18n( 'design/admin/visual/templateview' )|wash}">
                <option value="">{'Add condition'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="class_identifier">class_identifier</option>
                <option value="class">{'class'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="node">{'node'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="object">{'object'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="section">{'section'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="section_identifier">section_identifier</option>
                <option value="remote_id">remote_id</option>
                <option value="node_remote_id">node_remote_id</option>
                <option value="parent_node">parent_node</option>
                <option value="class_group">class_group</option>
                <option value="depth">{'depth'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="url_alias">url_alias</option>
                <option value="viewmode">{'viewmode'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="navigation_part_identifier">navigation_part_identifier</option>
                <option value="persistent_variable">persistent_variable</option>
                <option value="state">{'state'|i18n( 'design/admin/visual/templateview' )}</option>
                <option value="state_identifier">state_identifier</option>
            </select>
            <input type="text" name="NewMatch[{$name|wash}][value]" value="" size="24" aria-label="{'Value'|i18n( 'design/admin/visual/templateview' )|wash}" />
        </div>
        </details>
    </div>
</li>
{undef $name $own}
{/section}
</ol>
<p class="override-order-status" id="override-order-status" role="status" aria-live="polite"></p>

{section-else}
<div class="block">
<p>{'There are no overrides for the <%template_name> template.'|i18n( 'design/admin/visual/templateview',, hash( '%template_name', $template_settings.template ) )|wash}</p>
</div>
{/section}

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
    <div class="block">
        <div class="button-left">
        {if $own_overrides}
        <input class="button" type="submit" name="RemoveOverrideButton" value="{'Remove selected'|i18n( 'design/admin/visual/templateview' )}" title="{'Remove selected template overrides.'|i18n( 'design/admin/visual/templateview' )}" />
        {else}
        <input class="button-disabled" type="submit" name="RemoveOverrideButton" value="{'Remove selected'|i18n( 'design/admin/visual/templateview' )}" disabled="disabled"/>
        {/if}

        {if $new_override_allowed}
        <input class="button" type="submit" name="NewOverrideButton" value="{'New override'|i18n( 'design/admin/visual/templateview' )}" title="{'Create a new template override.'|i18n( 'design/admin/visual/templateview' )}" />
        {/if}
        </div>
        <div class="button-right">
            {if $template_settings.custom_match}
            <input class="button" type="submit" name="UpdateOverrideButton" value="{'Save conditions'|i18n( 'design/admin/visual/templateview' )}" title="{'Save the conditions edited above. The order is saved on its own, as you move the overrides.'|i18n( 'design/admin/visual/templateview' )}" />
            {else}
            <input class="button-disabled" type="submit" name="UpdateOverrideButton" value="{'Save conditions'|i18n( 'design/admin/visual/templateview' )}" disabled="disabled"/>
            {/if}
        </div>
        <div class="break"></div>
    </div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>

<script type="text/javascript">
{literal}
(function()
{
    var form = document.forms['templateview'];
    if ( !form )
        return;

    // Enter in a condition field saves the conditions, instead of the first
    // submit button of the form (Remove selected).
    form.addEventListener( 'keydown', function( e )
    {
        if ( e.key !== 'Enter' )
            return;
        var target = e.target;
        if ( target && ( target.tagName === 'SELECT' || ( target.tagName === 'INPUT' && ( target.type === 'text' || target.type === 'number' ) ) ) )
        {
            e.preventDefault();
            if ( form.UpdateOverrideButton )
                form.UpdateOverrideButton.click();
        }
    } );

    // The order: drag by the handle (or the arrows), saved on every change.
    var list = document.getElementById( 'override-order' );
    if ( !list )
        return;

    // Filter the cards as you type. Moving is off while filtered: a move
    // among the cards shown would say nothing of where it lands among the
    // hidden ones.
    var filter = document.getElementById( 'override-filter-input' );
    var filterNote = document.getElementById( 'override-filter-note' );
    if ( filter )
    {
        filter.addEventListener( 'input', function()
        {
            var q = this.value.replace( /^\s+|\s+$/g, '' ).toLowerCase();
            Array.prototype.forEach.call( list.querySelectorAll( '.override-card' ), function( card )
            {
                var text = card.getAttribute( 'data-name' ) + ' ' + card.querySelector( '.override-file' ).textContent;
                Array.prototype.forEach.call( card.querySelectorAll( '.override-conditions input[type=text]' ), function( i ) { text += ' ' + i.value; } );
                card.hidden = q !== '' && text.toLowerCase().indexOf( q ) === -1;
            } );
            list.classList.toggle( 'is-filtered', q !== '' );
            if ( filterNote ) filterNote.hidden = q === '';
        } );
    }
    var status = document.getElementById( 'override-order-status' );
    var csrfMeta = document.querySelector( 'meta[name="csrf-token"]' );
    var tokenField = form.querySelector( 'input[name="ezxform_token"]' );
    var dragged = null, before = null, saving = false, pending = false;

    function items() { return Array.prototype.slice.call( list.querySelectorAll( '.override-card' ) ); }
    function order() { return items().map( function( li ) { return li.getAttribute( 'data-name' ); } ); }
    function say( text, kind )
    {
        status.textContent = text || '';
        status.className = 'override-order-status' + ( kind ? ' is-' + kind : '' );
    }
    function renumber()
    {
        items().forEach( function( li, i ) { li.querySelector( '.override-position' ).textContent = i + 1; } );
    }
    function restore( names )
    {
        var byName = {};
        items().forEach( function( li ) { byName[li.getAttribute( 'data-name' )] = li; } );
        names.forEach( function( name ) { if ( byName[name] ) list.appendChild( byName[name] ); } );
        renumber();
    }

    function save()
    {
        if ( saving ) { pending = true; return; }
        saving = true; pending = false;
        list.classList.add( 'is-saving' );
        say( list.getAttribute( 'data-saving' ), 'busy' );
        var body = 'ReorderOverrides=1';
        order().forEach( function( name ) { body += '&OverrideOrder%5B%5D=' + encodeURIComponent( name ); } );
        if ( tokenField ) body += '&ezxform_token=' + encodeURIComponent( tokenField.value );
        var request = new XMLHttpRequest();
        request.open( 'POST', list.getAttribute( 'data-url' ), true );
        request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( csrfMeta ) request.setRequestHeader( 'X-CSRF-Token', csrfMeta.getAttribute( 'content' ) );
        request.onload = function()
        {
            var data = null;
            try { data = JSON.parse( request.responseText ); } catch ( e ) {}
            saving = false;
            list.classList.remove( 'is-saving' );
            if ( data && data.ok )
            {
                say( data.message, 'ok' );
                if ( pending ) save();
            }
            else
            {
                pending = false;
                if ( data && data.order ) restore( data.order );
                say( ( data && data.error ) || list.getAttribute( 'data-failed' ) + ' (HTTP ' + request.status + ')', 'error' );
            }
        };
        request.onerror = function()
        {
            saving = false; pending = false;
            list.classList.remove( 'is-saving' );
            say( list.getAttribute( 'data-failed' ), 'error' );
        };
        request.send( body );
    }

    // Only the handle starts a drag, so the fields in a card can still be
    // selected and typed in.
    list.addEventListener( 'mousedown', function( e )
    {
        if ( list.classList.contains( 'is-filtered' ) ) return;
        var card = e.target.closest( '.override-card' );
        if ( card ) card.setAttribute( 'draggable', e.target.closest( '.override-grip' ) ? 'true' : 'false' );
    } );
    list.addEventListener( 'dragstart', function( e )
    {
        var card = e.target.closest ? e.target.closest( '.override-card' ) : null;
        if ( !card || card.getAttribute( 'draggable' ) !== 'true' ) { e.preventDefault(); return; }
        dragged = card;
        before = order().join( '\n' );
        e.dataTransfer.effectAllowed = 'move';
        try { e.dataTransfer.setData( 'text/plain', card.getAttribute( 'data-name' ) ); } catch ( err ) {}
        setTimeout( function() { card.classList.add( 'is-dragging' ); }, 0 );
    } );
    list.addEventListener( 'dragover', function( e )
    {
        if ( !dragged ) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var over = e.target.closest ? e.target.closest( '.override-card' ) : null;
        if ( !over || over === dragged ) return;
        var box = over.getBoundingClientRect();
        list.insertBefore( dragged, ( e.clientY - box.top ) > box.height / 2 ? over.nextSibling : over );
    } );
    list.addEventListener( 'drop', function( e ) { if ( dragged ) e.preventDefault(); } );
    list.addEventListener( 'dragend', function()
    {
        if ( !dragged ) return;
        dragged.classList.remove( 'is-dragging' );
        dragged.setAttribute( 'draggable', 'false' );
        dragged = null;
        renumber();
        if ( order().join( '\n' ) !== before ) save();
    } );

    // The arrows, for the keyboard and touch screens.
    list.addEventListener( 'click', function( e )
    {
        var button = e.target.closest ? e.target.closest( 'button' ) : null;
        if ( !button || list.classList.contains( 'is-filtered' ) ) return;
        var card = button.closest( '.override-card' );
        if ( button.classList.contains( 'override-up' ) && card.previousElementSibling )
            list.insertBefore( card, card.previousElementSibling );
        else if ( button.classList.contains( 'override-down' ) && card.nextElementSibling )
            list.insertBefore( card.nextElementSibling, card );
        else
            return;
        button.focus();
        renumber();
        save();
    } );
})();
{/literal}
</script>
