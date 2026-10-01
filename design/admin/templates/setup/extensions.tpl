{ezcss_require( 'setup-extensions.css' )}

{if and( is_set( $warning_messages), $warning_messages|count|ge(1) )}
<div class="message-warning">
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Problems detected during autoload generation:'|i18n( 'design/admin/setup/extensions' )}</h2>
    <ul>
    {foreach $warning_messages as $warning}
        <li><p>{$warning|break()}</p></li>
    {/foreach}
    </ul>
</div>
{/if}

{if and( is_set( $save_error ), $save_error )}
<div class="message-error">
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'The extensions were not changed'|i18n( 'design/admin/setup/extensions' )}</h2>
    <p>{$save_error|wash}</p>
</div>
{elseif and( is_set( $save_message ), $save_message )}
<div class="message-feedback">
    <h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {$save_message|wash}</h2>
</div>
{/if}

{def $inactive_count = $extension_count|sub( $active_extension_order|count, $access_extension_array|count )}

<div class="context-block setup-extensions">

{* DESIGN: Header START *}<div class="box-header">
<h1 class="context-title">{'Available extensions (%extension_count)'|i18n( 'design/admin/setup/extensions',, hash( '%extension_count', $extension_count ) )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div>

{* DESIGN: Content START *}<div class="box-content">

<ul class="extensions-summary">
    <li class="extensions-summary-active"><strong>{$active_extension_order|count}</strong> {'active'|i18n( 'design/admin/setup/extensions' )}</li>
    {if $access_extension_array}<li class="extensions-summary-access"><strong>{$access_extension_array|count}</strong> {'active for siteaccesses'|i18n( 'design/admin/setup/extensions' )}</li>{/if}
    <li class="extensions-summary-inactive"><strong>{$inactive_count}</strong> {'inactive'|i18n( 'design/admin/setup/extensions' )}</li>
</ul>

{* The loading order. Every active extension, not a page of them, so it can be
   dragged into any place; each drop is saved straight away. Outside the form
   below, so the Update button never posts it. *}
{if $active_extension_order}
{* Closed unless this user opened it: it is not needed often. Opening or
   closing it is kept as the user preference admin_extensions_loading_order. *}
<details class="extension-order-card" id="extension-order-card"{if eq( ezpreference( 'admin_extensions_loading_order' ), '1' )} open="open"{/if}
         data-preference-url={'/user/preferences/set_and_exit/admin_extensions_loading_order'|ezurl}>
    <summary><span class="extension-order-title">{'Loading order'|i18n( 'design/admin/setup/extensions' )} <span class="extension-order-count">({$active_extension_order|count})</span></span>
        <span class="extension-order-hint">{'The order of ActiveExtensions in settings/override/site.ini.append.php, which is the order the system loads the extensions in. Drag an extension to a new place, or use its arrows; every change is saved at once.'|i18n( 'design/admin/setup/extensions' )}</span></summary>
    <ol class="extension-order" id="extension-order"
        data-url={'/setup/extensions'|ezurl}
        data-saving="{'Saving...'|i18n( 'design/admin/setup/extensions' )|wash}"
        data-failed="{'The loading order could not be saved.'|i18n( 'design/admin/setup/extensions' )|wash}">
    {foreach $active_extension_order as $index => $name}
        <li class="extension-order-item" draggable="true" data-name="{$name|wash}">
            <span class="extension-order-grip" aria-hidden="true"></span>
            <span class="extension-order-position">{$index|inc}</span>
            <span class="extension-order-name">{$name|wash}</span>
            {if and( is_set( $extension_info[$name] ), $extension_info[$name].name, ne( $extension_info[$name].name, $name ) )}<span class="extension-order-label">{$extension_info[$name].name|wash}</span>{/if}
            <span class="extension-order-moves">
                <button type="button" class="extension-order-up" title="{'Load earlier'|i18n( 'design/admin/setup/extensions' )|wash}" aria-label="{'Load %name earlier'|i18n( 'design/admin/setup/extensions',, hash( '%name', $name ) )|wash}">&#8593;</button>
                <button type="button" class="extension-order-down" title="{'Load later'|i18n( 'design/admin/setup/extensions' )|wash}" aria-label="{'Load %name later'|i18n( 'design/admin/setup/extensions',, hash( '%name', $name ) )|wash}">&#8595;</button>
            </span>
        </li>
    {/foreach}
    </ol>
    <p class="extension-order-status" id="extension-order-status" role="status" aria-live="polite"></p>
</details>
{/if}

<form name="extensionform" method="post" action={'/setup/extensions'|ezurl}>

{section show=$available_extension_array}
<div class="extensions-filter">
    <label for="extensions-filter-input">{'Filter this page'|i18n( 'design/admin/setup/extensions' )}</label>
    <input type="search" id="extensions-filter-input" class="halfbox" placeholder="{'Name, extension or license'|i18n( 'design/admin/setup/extensions' )|wash}" />
</div>

<table class="list extensions-list" cellspacing="0">
<tr>
    <th class="tight"><img src={'toggle-button-16x16.gif'|ezimage} width="16" height="16" alt="{'Invert selection.'|i18n( 'design/admin/setup/extensions' )}" title="{'Toggle all.'|i18n( 'design/admin/content/translations' )}" onclick="ezjs_toggleCheckboxes( document.extensionform, 'ActiveExtensionList[]' ); return false;"/></th>
    {* The headings are the shared sortable one, and the column travels as a
       view parameter so the pager keeps it. Sorting is done over the whole
       list before it is cut to a page, not over the page. *}
    {include uri='design:parts/sortheader.tpl' key='order'     label='Order'|i18n( 'design/admin/setup/extensions' )     sort=$extension_sort page_uri='/setup/extensions' cell_class='tight'}
    {include uri='design:parts/sortheader.tpl' key='name'      label='Name'|i18n( 'design/admin/setup/extensions' )      sort=$extension_sort page_uri='/setup/extensions'}
    {include uri='design:parts/sortheader.tpl' key='info_name' label='Extension'|i18n( 'design/admin/setup/extensions' ) sort=$extension_sort page_uri='/setup/extensions'}
    {include uri='design:parts/sortheader.tpl' key='license'   label='License'|i18n( 'design/admin/setup/extensions' )   sort=$extension_sort page_uri='/setup/extensions'}
    {include uri='design:parts/sortheader.tpl' key='version'   label='Version'|i18n( 'design/admin/setup/extensions' )   sort=$extension_sort page_uri='/setup/extensions'}
    {include uri='design:parts/sortheader.tpl' key='mtime'     label='Modified'|i18n( 'design/admin/setup/extensions' )  sort=$extension_sort page_uri='/setup/extensions'}
    <th>{'Info'|i18n( 'design/admin/setup/extensions' )}</th>
</tr>
{section var=Extensions loop=$available_extension_array sequence=array( bglight, bgdark )}
{def $ext = $extension_info[$Extensions.item]}
<tr class="{$Extensions.sequence} extension-row{if is_set( $extension_positions[$Extensions.item] )} is-active{elseif $access_extension_array|contains( $Extensions.item )} is-access{else} is-inactive{/if}" data-name="{$Extensions.item|wash}">
    {* Status. *}
    {* ShownExtensionList: the extensions on this page. Only these can be switched
       off by this form; the ones on other pages keep their state. *}
    <td><input type="hidden" name="ShownExtensionList[]" value="{$Extensions.item|wash}" /><input type="checkbox" name="ActiveExtensionList[]" value="{$Extensions.item|wash}" {if $selected_extension_array|contains($Extensions.item)}checked="checked"{/if} title="{'Activate or deactivate extension. Use the "Update" button to apply the changes.'|i18n( 'design/admin/setup/extensions' )|wash}" /></td>
    {* Loading order. *}
    <td class="extension-position-cell">{if is_set( $extension_positions[$Extensions.item] )}<span class="extension-badge extension-badge-active" data-position-of="{$Extensions.item|wash}">{$extension_positions[$Extensions.item]}</span>{elseif $access_extension_array|contains( $Extensions.item )}<span class="extension-badge extension-badge-access" title="{'Active for siteaccesses (ActiveAccessExtensions)'|i18n( 'design/admin/setup/extensions' )|wash}">{'SA'|i18n( 'design/admin/setup/extensions' )}</span>{else}<span class="extension-badge extension-badge-inactive">&ndash;</span>{/if}</td>
    {* Name (folder). *}
    <td><a href="#" class="extension-name-link" data-name="{$Extensions.item|wash}">{$Extensions.item|wash}</a></td>
    {* Full extension name. *}
    <td>{if $ext.name}{$ext.name|wash}{else}&mdash;{/if}</td>
    {* License. *}
    <td>{if $ext.license}{$ext.license|wash}{else}&mdash;{/if}</td>
    {* Version. *}
    <td>{if $ext.version}{$ext.version|wash}{else}&mdash;{/if}</td>
    {* Modified. *}
    <td>{if $ext.mtime_formatted}{$ext.mtime_formatted|wash}{else}&mdash;{/if}</td>
    {* Info popin trigger. *}
    <td><a href="#" class="extension-info-link" data-name="{$Extensions.item|wash}">{'Details'|i18n( 'design/admin/setup/extensions' )}</a></td>
</tr>
<tr class="{$Extensions.sequence} extension-card-row" id="extension-card-{$Extensions.item|wash}" style="display:none;">
    <td colspan="8" class="extension-card-cell">
        <div class="extension-card">
            <h3>{$ext.name|wash} <span class="extension-folder">({$Extensions.item|wash})</span></h3>
            {if $ext.meta.description}<p>{$ext.meta.description|wash}</p>{/if}
            <dl>
                {if is_set( $extension_positions[$Extensions.item] )}<dt>{'Loading order'|i18n( 'design/admin/setup/extensions' )}</dt><dd data-position-of="{$Extensions.item|wash}">{$extension_positions[$Extensions.item]}</dd>{/if}
                {if $ext.version}<dt>{'Version'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.version|wash}</dd>{/if}
                {if $ext.mtime_formatted}<dt>{'Modified'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.mtime_formatted|wash}</dd>{/if}
                {if $ext.meta.copyright}<dt>{'Copyright'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.meta.copyright|wash}</dd>{/if}
                {if $ext.meta.author}<dt>{'Author'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.meta.author|wash}</dd>{/if}
                {if $ext.meta.license}<dt>{'License'|i18n( 'design/admin/setup/extensions' )}</dt><dd>{$ext.meta.license|wash}</dd>{/if}
                {if $ext.meta.info_url}<dt>{'Info URL'|i18n( 'design/admin/setup/extensions' )}</dt><dd><a href="{$ext.meta.info_url|wash}" target="_blank">{$ext.meta.info_url|wash}</a></dd>{/if}
            </dl>
            <div class="extension-downloads">
                <strong>{'Download'|i18n( 'design/admin/setup/extensions' )}</strong>
                <a href={concat( '/setup/extensions/', $Extensions.item, '/tar.gz' )|ezurl}>tar.gz</a>
                <a href={concat( '/setup/extensions/', $Extensions.item, '/zip' )|ezurl}>.zip</a>
                <a href={concat( '/setup/extensions/', $Extensions.item, '/tar.bz2' )|ezurl}>tar.bz2</a>
                <a href={concat( '/setup/extensions/', $Extensions.item, '/ezpkg' )|ezurl}>.ezpkg</a>
            </div>
            <a href="#" class="extension-card-close">{'Close'|i18n( 'design/admin/setup/extensions' )}</a>
        </div>
    </td>
</tr>
{undef $ext}
{/section}
</table>

{* Paged; the size is admininterface.ini [PaginationSettings]. *}
{if $extension_count|gt( $limit )}
<div class="context-toolbar">
{include name=ExtensionNavigator
         uri='design:navigator/google.tpl'
         page_uri='/setup/extensions'
         item_count=$extension_count
         view_parameters=$view_parameters
         item_limit=$limit}
</div>
{/if}

{section-else}
<div class="block">
    <p>{'There are no available extensions.'|i18n( 'design/admin/setup/extensions' )}</p>
</div>
{/section}

<div class="block">
<div class="controlbar">
{* DESIGN: Control bar START *}
<div class="block">
{if $available_extension_array}
    <input class="button" type="submit" name="ActivateExtensionsButton" value="{'Update'|i18n( 'design/admin/setup/extensions' )}" title="{'Click this button to store changes if you have modified the status of the checkboxes above.'|i18n( 'design/admin/setup/extensions' )}" />
{else}
    <input class="button-disabled" type="submit" name="ActivateExtensionsButton" value="{'Update'|i18n( 'design/admin/setup/extensions' )}" disabled="disabled" />
{/if}
    <input class="button" type="submit" name="GenerateAutoloadArraysButton" value="{'Regenerate autoload arrays for extensions'|i18n( 'design/admin/setup/extensions' )}" title="{'Click this button to regenerate the autoload arrays used by the system for extensions.'|i18n( 'design/admin/setup/extensions' )}" />
</div>
{* DESIGN: Control bar END *}
</div>
</div>

</form>

{* DESIGN: Content END *}</div>

</div>

{literal}
<script type="text/javascript">
$(document).ready(function() {
    var initialExtensionSettings = {};
    var extensionChecks = jQuery('[name=extensionform] :checkbox');

    // Highlight "Update" button on changes
    function styleUpdateButton() {
        var b = jQuery('[name=ActivateExtensionsButton]:first');
        jQuery(extensionChecks).each( function(){
            if (initialExtensionSettings[this.value] !== this.checked) {
                b.removeClass('button').addClass('defaultbutton');
                return false;
            } else {
                b.removeClass('defaultbutton').addClass('button');
            }
        });
    }

    jQuery(extensionChecks).each( function(){
        initialExtensionSettings[this.value] = this.checked;
    }).on('change', function(){styleUpdateButton();});

    // Extension info card popin
    function toggleExtensionCard( name ) {
        var row = jQuery( document.getElementById( 'extension-card-' + name ) );
        var wasVisible = row.is(':visible');
        jQuery( '.extension-card-row' ).hide();
        if ( !wasVisible ) {
            row.show();
        }
    }

    jQuery( '.extension-name-link, .extension-info-link' ).on( 'click', function( e ) {
        e.preventDefault();
        toggleExtensionCard( jQuery(this).data('name') );
    });

    jQuery( '.extension-card-close' ).on( 'click', function( e ) {
        e.preventDefault();
        jQuery(this).closest( '.extension-card-row' ).hide();
    });

    // Filter the rows of this page as you type.
    jQuery( '#extensions-filter-input' ).on( 'input', function() {
        var q = String( ( this.value ) ?? '' ).trim().toLowerCase();
        jQuery( '.extensions-list tr.extension-row' ).each( function() {
            var match = q === '' || jQuery( this ).text().toLowerCase().indexOf( q ) !== -1;
            jQuery( this ).toggle( match );
            if ( !match ) jQuery( document.getElementById( 'extension-card-' + jQuery( this ).data( 'name' ) ) ).hide();
        });
    });

    // The loading order: drag and drop (or the arrows), saved on every change.
    // Shown or hidden: remembered as a user preference, so the card opens
    // the way it was left, on any browser.
    var card = document.getElementById( 'extension-order-card' );
    if ( card && card.getAttribute( 'data-preference-url' ) ) {
        card.addEventListener( 'toggle', function() {
            var meta = document.querySelector( 'meta[name="csrf-token"]' );
            var field = document.querySelector( 'input[name="ezxform_token"]' );
            var request = new XMLHttpRequest();
            request.open( 'POST', card.getAttribute( 'data-preference-url' ) + '/' + ( card.open ? '1' : '0' ), true );
            request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
            request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
            if ( meta ) request.setRequestHeader( 'X-CSRF-Token', meta.getAttribute( 'content' ) );
            request.send( field ? 'ezxform_token=' + encodeURIComponent( field.value ) : '' );
        });
    }

    var list = document.getElementById( 'extension-order' );
    if ( !list ) return;
    var status = document.getElementById( 'extension-order-status' );
    var csrfMeta = document.querySelector( 'meta[name="csrf-token"]' );
    var tokenField = document.querySelector( 'input[name="ezxform_token"]' );
    var dragged = null, before = null, saving = false, pending = false;

    function items() { return Array.prototype.slice.call( list.querySelectorAll( '.extension-order-item' ) ); }
    function order() { return items().map( function( li ) { return li.getAttribute( 'data-name' ); } ); }

    function say( text, kind ) {
        status.textContent = text || '';
        status.className = 'extension-order-status' + ( kind ? ' is-' + kind : '' );
    }

    // Numbers in the list, the table and the info cards follow the list.
    function renumber() {
        items().forEach( function( li, i ) {
            li.querySelector( '.extension-order-position' ).textContent = i + 1;
            var cells = document.querySelectorAll( '[data-position-of]' );
            for ( var c = 0; c < cells.length; c++ )
                if ( cells[c].getAttribute( 'data-position-of' ) === li.getAttribute( 'data-name' ) )
                    cells[c].textContent = i + 1;
        });
    }

    // Puts the list into the order the server holds (after a refused save).
    function restore( names ) {
        var byName = {};
        items().forEach( function( li ) { byName[li.getAttribute( 'data-name' )] = li; } );
        names.forEach( function( name ) { if ( byName[name] ) list.appendChild( byName[name] ); } );
        renumber();
    }

    function save() {
        if ( saving ) { pending = true; return; }
        saving = true; pending = false;
        list.classList.add( 'is-saving' );
        say( list.getAttribute( 'data-saving' ), 'busy' );
        var body = 'ReorderExtensions=1';
        order().forEach( function( name ) { body += '&ExtensionOrder%5B%5D=' + encodeURIComponent( name ); } );
        if ( tokenField ) body += '&ezxform_token=' + encodeURIComponent( tokenField.value );
        var request = new XMLHttpRequest();
        request.open( 'POST', list.getAttribute( 'data-url' ), true );
        request.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8' );
        request.setRequestHeader( 'X-Requested-With', 'XMLHttpRequest' );
        if ( csrfMeta ) request.setRequestHeader( 'X-CSRF-Token', csrfMeta.getAttribute( 'content' ) );
        request.onload = function() {
            var data = null;
            try { data = JSON.parse( request.responseText ); } catch ( e ) {}
            saving = false;
            list.classList.remove( 'is-saving' );
            if ( data && data.ok ) {
                say( data.message, 'ok' );
                if ( pending ) save();
            } else {
                pending = false;
                if ( data && data.order ) restore( data.order );
                say( ( data && data.error ) || list.getAttribute( 'data-failed' ) + ' (HTTP ' + request.status + ')', 'error' );
            }
        };
        request.onerror = function() {
            saving = false; pending = false;
            list.classList.remove( 'is-saving' );
            say( list.getAttribute( 'data-failed' ), 'error' );
        };
        request.send( body );
    }

    // The list moves while dragging, so what you see is where it lands.
    list.addEventListener( 'dragstart', function( e ) {
        var li = e.target.closest ? e.target.closest( '.extension-order-item' ) : null;
        if ( !li ) return;
        dragged = li;
        before = order().join( ',' );
        e.dataTransfer.effectAllowed = 'move';
        try { e.dataTransfer.setData( 'text/plain', li.getAttribute( 'data-name' ) ); } catch ( err ) {}
        setTimeout( function() { li.classList.add( 'is-dragging' ); }, 0 );
    });
    list.addEventListener( 'dragover', function( e ) {
        if ( !dragged ) return;
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        var over = e.target.closest ? e.target.closest( '.extension-order-item' ) : null;
        if ( !over || over === dragged ) return;
        var box = over.getBoundingClientRect();
        var after = ( e.clientY - box.top ) > box.height / 2;
        list.insertBefore( dragged, after ? over.nextSibling : over );
    });
    list.addEventListener( 'drop', function( e ) { if ( dragged ) e.preventDefault(); } );
    list.addEventListener( 'dragend', function() {
        if ( !dragged ) return;
        dragged.classList.remove( 'is-dragging' );
        dragged = null;
        renumber();
        if ( order().join( ',' ) !== before ) save();
    });

    // The arrows, for the keyboard and for touch screens.
    list.addEventListener( 'click', function( e ) {
        var button = e.target.closest ? e.target.closest( 'button' ) : null;
        if ( !button ) return;
        var li = button.closest( '.extension-order-item' );
        if ( button.classList.contains( 'extension-order-up' ) && li.previousElementSibling )
            list.insertBefore( li, li.previousElementSibling );
        else if ( button.classList.contains( 'extension-order-down' ) && li.nextElementSibling )
            list.insertBefore( li.nextElementSibling, li );
        else
            return;
        button.focus();
        renumber();
        save();
    });
});
</script>
{/literal}
