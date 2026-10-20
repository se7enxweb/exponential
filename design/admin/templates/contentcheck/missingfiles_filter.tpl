{* The filters of the missing files in the GET form of contentcheck/brokenrelations, shown when the target "Missing
   files" is ticked (doc/eng-us/features/6.0/content-checks.md, "Missing files"). Included twice: the groups before the
   form's Show button, their buttons after it (Enter in a field still submits Show). "Select all" and "Select none" are the shared submit buttons
   (filter_choice.tpl: ProblemChoice, VersionChoice), which work without JavaScript. Variable: files (from section():
   params, problems, versions), part (chips: the two groups, before the Show button; choice: their Select all and
   Select none, after it). The same file is in design/admin and design/admin4. *}
{def $cc_problem_names = hash( 'missing', 'File missing'|i18n( 'kernel/contentcheck' ),
                               'alias_missing', 'Alias missing'|i18n( 'kernel/contentcheck' ),
                               'alias_stale', 'Alias out of date'|i18n( 'kernel/contentcheck' ),
                               'alias_absent', 'Alias never made'|i18n( 'kernel/contentcheck' ) )
     $cc_version_names = hash( 'published', 'Published version'|i18n( 'kernel/contentcheck' ),
                               'draft', 'Drafts'|i18n( 'kernel/contentcheck' ) )}
    {if eq( $part, 'chips' )}<fieldset class="a4-field">
        <legend>{'Missing files: problems'|i18n( 'kernel/contentcheck' )}</legend>
        <div class="a4-chips">
        {foreach $files.problems as $cc_problem}
            <label class="a4-chip"><input type="checkbox" name="Problem[]" value="{$cc_problem|wash}"{if $files.params.problems|contains( $cc_problem )} checked="checked"{/if} /><span>{$cc_problem_names[$cc_problem]|wash}</span></label>
        {/foreach}
        </div>
    </fieldset>{/if}
    {if eq( $part, 'chips' )}<fieldset class="a4-field">
        <legend>{'Missing files: versions'|i18n( 'kernel/contentcheck' )}</legend>
        <div class="a4-chips">
        {foreach $files.versions as $cc_version}
            <label class="a4-chip"><input type="checkbox" name="Version[]" value="{$cc_version|wash}"{if $files.params.versions|contains( $cc_version )} checked="checked"{/if} /><span>{$cc_version_names[$cc_version]|wash}</span></label>
        {/foreach}
        </div>
    </fieldset>{/if}
    {if eq( $part, 'choice' )}{include uri='design:contentcheck/filter_choice.tpl' group='Problem' label='Missing files: problems'|i18n( 'kernel/contentcheck' )}{/if}
    {if eq( $part, 'choice' )}{include uri='design:contentcheck/filter_choice.tpl' group='Version' label='Missing files: versions'|i18n( 'kernel/contentcheck' )}{/if}
{undef $cc_problem_names $cc_version_names}
