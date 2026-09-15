{let use_url_translation=ezini( 'URLTranslator', 'Translation' )|eq( 'enabled' )}

{section show=$search_result}
{* Built with ezurl so it carries this installation's siteaccess and index file. *}
<script type="text/javascript">var ezSearchMultiEditURL = "{'content/multiedit'|ezurl('no')}";</script>
{* Results are selectable so that a search can feed the multi item editor: pick
   what you want out of the whole site, then edit it in one form.

   No <form> here on purpose. This template is included inside the search form,
   and a form inside a form is dropped by every browser - the inner one simply
   does not exist, and its button posts to the outer action instead. So the
   button builds a form of its own and submits that, carrying the ticked node
   ids and the request token. *}
<table class="list" cellspacing="0">
<tr>
    <th class="tight"><input type="checkbox" id="ezsearch-select-all" title="{'Select every result on this page.'|i18n( 'design/admin/content/search' )|wash}" /></th>
    <th>{'Name'|i18n( 'design/admin/content/search' )}</th>
    <th>{'Type'|i18n( 'design/admin/content/search' )}</th>
</tr>

{section var=SearchResult loop=$search_result sequence=array( bglight, bgdark )}
<tr class="{$SearchResult.sequence}">
<td class="tight">
    <input type="checkbox" class="ezsearch-select" value="{$SearchResult.item.node_id}" title="{'Select this item for editing.'|i18n( 'design/admin/content/search' )|wash}" />
</td>
<td>
{node_view_gui view=line content_node=$SearchResult.item}
</td>
<td>
{$SearchResult.item.class_name|wash}
</td>
</tr>
{/section}

</table>

<div class="block">
    <input class="button" type="button" id="ezsearch-multiedit" value="{'Edit selected'|i18n( 'design/admin/content/search' )}" title="{'Edit every ticked result in one form.'|i18n( 'design/admin/content/search' )|wash}" />
</div>

{literal}<script type="text/javascript">
(function () {
    var all    = document.getElementById( 'ezsearch-select-all' ),
        button = document.getElementById( 'ezsearch-multiedit' );

    function boxes()
    {
        return Array.prototype.slice.call( document.querySelectorAll( 'input.ezsearch-select' ) );
    }

    if ( all )
        all.onclick = function () {
            var checked = this.checked;
            boxes().forEach( function ( box ) { box.checked = checked; } );
        };

    if ( !button )
        return;

    button.onclick = function () {
        var chosen = boxes().filter( function ( box ) { return box.checked; } );

        if ( chosen.length === 0 )
            return;

        var form = document.createElement( 'form' );
        form.method = 'post';
        form.action = ezSearchMultiEditURL;

        chosen.forEach( function ( box ) {
            var field = document.createElement( 'input' );
            field.type  = 'hidden';
            field.name  = 'MultiEditNodeIDArray[]';
            field.value = box.value;
            form.appendChild( field );
        } );

        var back = document.createElement( 'input' );
        back.type  = 'hidden';
        back.name  = 'MultiEditReturnURI';
        back.value = window.location.pathname + window.location.search;
        form.appendChild( back );

        // The request token, read from the meta tag the form token extension
        // puts on every page. Without it the post is refused before the view
        // is ever reached.
        var token = document.querySelector( 'meta[name="csrf-token"]' ),
            param = document.querySelector( 'meta[name="csrf-param"]' );

        if ( token )
        {
            var field = document.createElement( 'input' );
            field.type  = 'hidden';
            field.name  = param ? param.content : 'ezxform_token';
            field.value = token.content;
            form.appendChild( field );
        }

        document.body.appendChild( form );
        form.submit();
    };
})();
</script>{/literal}
{/section}

{/let}
