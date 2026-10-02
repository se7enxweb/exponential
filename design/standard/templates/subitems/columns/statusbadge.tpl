{* Subitems list column "Status" (Column_statusbadge): one badge saying what visitors see of
   the item. Variables: $node (the row), $column (the column's settings), $key (its key).
   The output is the cell; its text ("Hidden", "Locked" ...) is the value and the CSV text.
   Guide: doc/bc/6.0/subitems-table-options.md *}
{def $exp_badge = 'visible'}
{if $node.is_hidden}
    {set $exp_badge = 'hidden'}
{elseif $node.is_invisible}
    {set $exp_badge = 'invisible'}
{elseif $node.object.state_identifier_array|contains( 'ez_lock/locked' )}
    {set $exp_badge = 'locked'}
{/if}
{switch match=$exp_badge}
{case match='hidden'}<span class="exp-subitems-badge exp-subitems-badge-hidden" style="padding:0 .4em;border-radius:3px;background:#fde2e1;color:#8a1f11">{'Hidden'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{case match='invisible'}<span class="exp-subitems-badge exp-subitems-badge-invisible" style="padding:0 .4em;border-radius:3px;background:#fff1cc;color:#6b4e00">{'Hidden by a parent'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{case match='locked'}<span class="exp-subitems-badge exp-subitems-badge-locked" style="padding:0 .4em;border-radius:3px;background:#e4e7eb;color:#333">{'Locked'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{case}<span class="exp-subitems-badge exp-subitems-badge-visible" style="padding:0 .4em;border-radius:3px;background:#dff3e3;color:#1d5c2b">{'Visible'|i18n( 'design/admin/node/view/full' )}</span>{/case}
{/switch}
{undef $exp_badge}
