{if $attribute.content.keywords|count()}
{if is_set( $#persistent_variable.keywords )}
    {set scope='global' persistent_variable=$#persistent_variable|merge( hash( 'keywords', concat( $#persistent_variable.keywords, ', ', $attribute.content.keyword_string ) ) )}
{else}
    {if is_array( $#persistent_variable )|not()}
        {set scope='global' persistent_variable=hash( 'keywords', $attribute.content.keyword_string )}
    {else}
        {set scope='global' persistent_variable=$#persistent_variable|merge( hash( 'keywords', $attribute.content.keyword_string ) )}
    {/if}
{/if}
<ul class="tags-wrapper">
    {foreach $attribute.content.keywords as $keyword}
    <li>
        <a href="{concat( '/content/keyword/', $keyword|wash() )|ezurl( 'no' )}">
            <span class="tag-title">{$keyword|wash()}</span>
            <span class="tag-amount">{fetch( 'content', 'keyword_count', hash( 'alphabet', $keyword ) )}</span>
        </a>
    </li>
    {/foreach}
</ul>
{/if}
