<h2>{"Related content"|i18n("design/ezdemo/full/article")}</h2>
<article>
    <ul>
    {foreach $related_content|reverse() as $related_object max 7}
        <li><a href="{$related_object.url_alias|ezurl( 'no' )}" title="{$related_object.name|wash()}">{$related_object.name|wash()}</a></li>
    {/foreach}
    </ul>
</article>
