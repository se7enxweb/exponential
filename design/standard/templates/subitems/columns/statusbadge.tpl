{* Subitems list column "Status" (Column_statusbadge): one badge saying what visitors see of
   the item. Variables: $node (the row), $column (the column's settings), $key (its key).
   The output is the cell; its text ("Hidden", "Locked" ...) is the value and the CSV text. The badges are the
   administration's .a4-badge (components.css): red, yellow, grey and green tints from the tokens.
   Guide: doc/en/us/bc/6.0/subitems-table-options.md *}
{def $exp_badge = 'visible'}
{if $node.is_hidden}
    {set $exp_badge = 'hidden'}
{elseif $node.is_invisible}
    {set $exp_badge = 'invisible'}
{elseif $node.object.state_identifier_array|contains( 'ez_lock/locked' )}
    {set $exp_badge = 'locked'}
{/if}
{switch match=$exp_badge}
{case match='hidden'}<span class="a4-badge is-bad exp-subitems-badge exp-subitems-badge-hidden">{'Hidden'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{case match='invisible'}<span class="a4-badge is-warn exp-subitems-badge exp-subitems-badge-invisible">{'Hidden by a parent'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{case match='locked'}<span class="a4-badge exp-subitems-badge exp-subitems-badge-locked">{'Locked'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{case}<span class="a4-badge is-ok exp-subitems-badge exp-subitems-badge-visible">{'Visible'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{/switch}
{undef $exp_badge}
