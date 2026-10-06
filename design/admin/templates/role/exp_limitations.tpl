{* The limitation fields of a policy, for the third step of the policy wizard (role/createpolicystep3.tpl) and the
   policy editor (role/policyedit.tpl): one list per limitation the function supports, and the nodes and subtrees
   picked in the content browser.

   Variables: function_limitations, current_limitation_list, node_list, subtree_list (as the views set them) and
   i18n_context (the context of the page that includes it, so the strings keep their translations). Every value is
   escaped, the stored ones included. The same file is in design/admin and design/admin4. *}
{default i18n_context='design/admin/role/createpolicystep3'}

{def $list_limitations = array()}
{foreach $function_limitations as $limitation}
    {if and( ne( $limitation.name, 'Node' ), ne( $limitation.name, 'Subtree' ) )}{set $list_limitations = $list_limitations|append( $limitation )}{/if}
{/foreach}

{if $list_limitations}
<section class="exp-panel" aria-labelledby="policy-limits-title">
<div class="exp-section-head"><h2 class="exp-h2" id="policy-limits-title">{'Properties'|i18n( $i18n_context )}</h2>
<p>{'Hold Ctrl (Cmd on a Mac) to choose more than one value.'|i18n( 'design/admin/role/createpolicystep3' )}</p></div>
<div class="exp-limits">
{foreach $list_limitations as $limitation}
    {def $current = first_set( $current_limitation_list[$limitation.name], '-1' )
         $field_id = concat( 'ezrole_createpolizy_limitation_', $limitation.name|wash( 'xhtml' ) )}
    <div class="exp-field">
        <label for="{$field_id}">{if is_set( $limitation.label )}{$limitation.label|wash}{else}{$limitation.name|wash}{/if}</label>
        <select id="{$field_id}" name="{$limitation.name|wash}[]" size="8"{if or( not( is_set( $limitation.single_select ) ), not( $limitation.single_select ) )} multiple="multiple"{/if}>
            <option value="-1"{if eq( $current, '-1' )} selected="selected"{/if}>{'Any'|i18n( $i18n_context )}</option>
            {foreach $limitation.values as $value}
            <option value="{$value.value|wash}"{if and( is_array( $current ), $current|contains( $value.value ) )} selected="selected"{/if}>{$value.Name|wash}</option>
            {/foreach}
        </select>
    </div>
    {undef $current $field_id}
{/foreach}
</div>
</section>
{/if}

<div class="exp-pickers">
{foreach $function_limitations as $limitation}
{if eq( $limitation.name, 'Node' )}
<section class="exp-panel" aria-labelledby="policy-nodes-title">
    <h2 class="exp-h2" id="policy-nodes-title">{'Nodes (%node_count)'|i18n( $i18n_context,, hash( '%node_count', $node_list|count ) )}</h2>
    <p class="exp-help">{'Only these nodes themselves, not what lies below them. Choosing nodes drops the other limitations the function names.'|i18n( 'design/admin/role/createpolicystep3' )}</p>
    {if $node_list}
    <ul class="exp-picked">
    {foreach $node_list as $node}{if $node}
        <li><input type="checkbox" name="DeleteNodeIDArray[]" value="{$node.node_id}" id="policy-node-{$node.node_id}" /> <label for="policy-node-{$node.node_id}">{$node.name|wash}</label></li>
    {/if}{/foreach}
    </ul>
    {else}
    <p class="exp-muted">{'The node list is empty.'|i18n( $i18n_context )}</p>
    {/if}
    <span class="exp-actions">
        <button class="exp-btn" type="submit" name="BrowseLimitationNodeButton" value="1">{'Add nodes'|i18n( $i18n_context )}</button>
        <button class="exp-btn exp-btn-outline-danger" type="submit" name="DeleteNodeButton" value="1"{if not( $node_list )} disabled="disabled"{/if}>{'Remove selected'|i18n( $i18n_context )}</button>
    </span>
</section>
{elseif eq( $limitation.name, 'Subtree' )}
<section class="exp-panel" aria-labelledby="policy-subtrees-title">
    <h2 class="exp-h2" id="policy-subtrees-title">{'Subtrees (%subtree_count)'|i18n( $i18n_context,, hash( '%subtree_count', $subtree_list|count ) )}</h2>
    <p class="exp-help">{'These nodes and everything below them.'|i18n( 'design/admin/role/createpolicystep3' )}</p>
    {if $subtree_list}
    <ul class="exp-picked">
    {foreach $subtree_list as $subtree}{if $subtree}
        <li><input type="checkbox" name="DeleteSubtreeIDArray[]" value="{$subtree.node_id}" id="policy-subtree-{$subtree.node_id}" /> <label for="policy-subtree-{$subtree.node_id}">{$subtree.name|wash}</label></li>
    {/if}{/foreach}
    </ul>
    {else}
    <p class="exp-muted">{'The subtree list is empty.'|i18n( $i18n_context )}</p>
    {/if}
    <span class="exp-actions">
        <button class="exp-btn" type="submit" name="BrowseLimitationSubtreeButton" value="1">{'Add subtrees'|i18n( $i18n_context )}</button>
        <button class="exp-btn exp-btn-outline-danger" type="submit" name="DeleteSubtreeButton" value="1"{if not( $subtree_list )} disabled="disabled"{/if}>{'Remove selected'|i18n( $i18n_context )}</button>
    </span>
</section>
{/if}
{/foreach}
</div>
{undef $list_limitations}
