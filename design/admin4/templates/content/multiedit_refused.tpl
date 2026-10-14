{* content/multiedit: the selected items that could not be opened, and why. Used beside a form that did open (with a
   title) and alone when nothing could be opened. $refused: object id => hash( name, reason ). *}
<div class="a4-notice is-warn" role="status">
    {if and( is_set( $title ), $title )}<strong>{$title|wash}:</strong>{/if}
    <ul>
    {foreach $refused as $refused_item}
        <li>{$refused_item.name|wash} &mdash;
            {if $refused_item.reason|eq('no-permission')}{'you may not edit this item'|i18n('design/admin/content/multiedit')}
            {elseif $refused_item.reason|eq('gone')}{'no longer exists'|i18n('design/admin/content/multiedit')}
            {elseif $refused_item.reason|eq('no-draft')}{'a draft could not be opened, somebody may be editing it'|i18n('design/admin/content/multiedit')}
            {else}{'cannot be edited'|i18n('design/admin/content/multiedit')}
            {/if}
        </li>
    {/foreach}
    </ul>
</div>
