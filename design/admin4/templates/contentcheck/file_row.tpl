{* One "file problem" row of an a4-table (doc/features/6.0/content-checks.md, "Missing files"): select, object and
   node, field, version, expected path, last modified, state, actions. Shared by the file checks.

   Variables: select_all (tick the box), row (key, object_id, object_name, node_id, field_name, language, version, version_status, path,
   file_name, aliases, modified, file_modified, problem, review, can_fix, has_aliases), selectable (show the tick
   box column), can_fix (the user has contentcheck/fixfiles). The row's buttons post ItemAction=<action>:<key> in
   the list's form. The same file is in design/admin and design/admin4. *}
{def $cc_problem_names = hash( 'missing', 'File missing'|i18n( 'kernel/contentcheck' ),
                               'alias_missing', 'Alias missing'|i18n( 'kernel/contentcheck' ),
                               'alias_stale', 'Alias out of date'|i18n( 'kernel/contentcheck' ),
                               'alias_absent', 'Alias never made'|i18n( 'kernel/contentcheck' ) )
     $cc_problem_classes = hash( 'missing', 'is-bad', 'alias_missing', 'is-warn', 'alias_stale', 'is-warn', 'alias_absent', 'is-info' )
     $cc_alias_states = hash( 'missing', 'missing'|i18n( 'kernel/contentcheck' ), 'stale', 'out of date'|i18n( 'kernel/contentcheck' ), 'absent', 'never made'|i18n( 'kernel/contentcheck' ) )
     $cc_row_actions = cond( eq( $row.problem, 'missing' ), array( 'restore', 'replace', 'remove', 'review' ), array( 'regenerate', 'review' ) )
     $cc_action_names = hash( 'regenerate', 'Regenerate'|i18n( 'kernel/contentcheck' ),
                              'restore', 'Restore'|i18n( 'kernel/contentcheck' ),
                              'replace', 'Re-upload'|i18n( 'kernel/contentcheck' ),
                              'remove', 'Remove reference'|i18n( 'kernel/contentcheck' ),
                              'review', 'Mark for review'|i18n( 'kernel/contentcheck' ) )
     $cc_label = concat( $row.object_name, ' – ', $row.field_name, ' – ', 'Version %version'|i18n( 'kernel/contentcheck',, hash( '%version', $row.version ) ) )}
<tr>
    {if $selectable}<td>{if and( $can_fix, $row.can_fix )}<input type="checkbox" name="Keys[]" value="{$row.key|wash}" aria-label="{$cc_label|wash}" data-a4-select-item="1"{if $select_all} checked="checked"{/if} />{/if}</td>{/if}
    <td>{if $row.node_id|gt( 0 )}<a href={concat( '/content/view/full/', $row.node_id )|ezurl}>{$row.object_name|wash}</a>{else}{$row.object_name|wash}{/if}
        <div class="a4-meta">{'Object %id'|i18n( 'kernel/contentcheck',, hash( '%id', $row.object_id ) )}{if $row.node_id|gt( 0 )} &middot; {'Node %id'|i18n( 'kernel/contentcheck',, hash( '%id', $row.node_id ) )}{/if}</div></td>
    <td>{if $row.field_name|ne( '' )}{$row.field_name|wash}{else}&ndash;{/if}{if $row.language|ne( '' )} <span class="a4-tag is-mono">{$row.language|wash}</span>{/if}
        {if $row.file_name|ne( '' )}<div class="a4-meta">{$row.file_name|wash}</div>{/if}</td>
    <td>{'Version %version'|i18n( 'kernel/contentcheck',, hash( '%version', $row.version ) )}
        <div class="a4-meta">{if eq( $row.version_status, 'published' )}{'Published'|i18n( 'kernel/contentcheck' )}{else}{'Draft'|i18n( 'kernel/contentcheck' )}{/if}</div></td>
    <td><code class="a4-code">{$row.path|wash}</code>
        {if $row.aliases|count|gt( 0 )}<div class="a4-meta">{foreach $row.aliases as $cc_alias}{delimiter}, {/delimiter}{$cc_alias.alias|wash} ({$cc_alias_states[$cc_alias.state]|wash}){/foreach}</div>{/if}</td>
    <td>{if $row.modified|gt( 0 )}{$row.modified|l10n( 'shortdatetime' )}{else}&ndash;{/if}
        {if $row.file_modified|gt( 0 )}<div class="a4-meta">{'File: %time'|i18n( 'kernel/contentcheck',, hash( '%time', $row.file_modified|l10n( 'shortdatetime' ) ) )}</div>{/if}</td>
    <td><span class="a4-tag {$cc_problem_classes[$row.problem]}">{$cc_problem_names[$row.problem]|wash}</span>
        {if $row.review}<div><span class="a4-tag is-accent">{'Marked for review'|i18n( 'kernel/contentcheck' )}</span></div>{/if}</td>
    {if $can_fix}<td>{if $row.can_fix}<div class="a4-actions">
        {foreach $cc_row_actions as $cc_action}<button type="submit" class="a4-btn a4-btn-small" name="ItemAction" value="{concat( $cc_action, ':', $row.key )|wash}" aria-label="{concat( $cc_action_names[$cc_action], ': ', $cc_label )|wash}">{$cc_action_names[$cc_action]|wash}</button>{/foreach}
        </div>{/if}</td>{/if}
</tr>
{undef $cc_problem_names $cc_problem_classes $cc_alias_states $cc_row_actions $cc_action_names $cc_label}
