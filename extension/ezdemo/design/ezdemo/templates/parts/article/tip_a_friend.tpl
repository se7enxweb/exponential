{* This is a helper template for eZ Publish DemoBundle for displaying a "Tip a friend" link that respects user permissions *}
{def $tipafriend_access = fetch( 'user', 'has_access_to', hash( 'module', 'content', 'function', 'tipafriend' ) )}
{if and( ezmodule( 'content/tipafriend' ), $tipafriend_access )}
    <div class="attribute-tipafriend">
        <p><a href={concat( "/content/tipafriend/", $node.node_id )|ezurl} title="{'Tip a friend'|i18n( 'design/ezdemo/full/article' )}">{'Tip a friend'|i18n( 'design/ezdemo/full/article' )}</a></p>
    </div>
{/if}
