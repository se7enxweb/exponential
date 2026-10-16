{* The class step of the content class export wizard (package/create, creator ezcontentclass): the content classes
   the package carries, as a list of checkboxes with their identifier and groups. A filter above the list (a script,
   it posts nothing) narrows it by name or identifier; without the script the whole list shows. The POST name is
   that of before: ClassList[] (the ids of the chosen classes), read by eZContentClassPackageCreator.
   The same file is in design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{def $class_list = fetch( class, list )
     $chosen = first_set( $persistent_data.classlist, array() )
     $k_list = 'Class list'|i18n( 'kernel/package' )
     $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='Choose the content classes to export. The package carries their definitions, not their objects.'|i18n( 'design/admin/package' )
         field_ids=hash( $k_list, 'a4-package-classes' )}

<section class="a4-card is-flush a4-package-pick" id="a4-package-classes" tabindex="-1" aria-labelledby="a4-package-classes-title" aria-describedby="{if is_set( $errs[$k_list] )}a4-package-classes-error {/if}a4-package-classes-count">
    <div class="a4-card-head">
        <h3 class="a4-h3" id="a4-package-classes-title">{'Content classes'|i18n( 'design/admin/package' )}</h3>
        <p class="a4-package-pick-count" id="a4-package-classes-count" aria-live="polite" data-template="{'%count of %total selected'|i18n( 'design/admin/package' )|wash}">{'%count of %total selected'|i18n( 'design/admin/package',, hash( '%count', $chosen|count, '%total', $class_list|count ) )|wash}</p>
    </div>
    {if is_set( $errs[$k_list] )}<p class="a4-field-error a4-package-pick-error" id="a4-package-classes-error">{$errs[$k_list]|wash}</p>{/if}
    <div class="a4-filterbar a4-package-pick-filter" hidden>
        <div class="a4-field">
            <label for="a4-package-classes-filter">{'Filter by name or identifier'|i18n( 'design/admin/package' )}</label>
            <input type="search" id="a4-package-classes-filter" autocomplete="off" spellcheck="false" />
        </div>
    </div>
    {if $class_list|count|eq( 0 )}
    <p class="a4-empty">{'There are no content classes.'|i18n( 'design/admin/package' )}</p>
    {else}
    <div class="a4-table-wrap">
    <table class="a4-table a4-package-pick-table">
        <thead>
        <tr>
            <th scope="col" class="a4-package-pick-check"><span class="a4-sr-only">{'Include'|i18n( 'design/admin/package' )}</span></th>
            <th scope="col" id="a4-package-classes-name">{'Name'|i18n( 'design/admin/package' )}</th>
            <th scope="col">{'Identifier'|i18n( 'design/admin/package' )}</th>
            <th scope="col" class="a4-package-pick-groups">{'Groups'|i18n( 'design/admin/package' )}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $class_list as $class}
        <tr data-filter="{concat( $class.name, ' ', $class.identifier )|downcase|wash}">
            <td class="a4-package-pick-check"><input type="checkbox" name="ClassList[]" id="a4-package-class-{$class.id}" value="{$class.id}"{if $chosen|contains( $class.id )} checked="checked"{/if} /></td>
            <td><label class="a4-inline-label" for="a4-package-class-{$class.id}">{$class.name|wash}</label></td>
            <td><code>{$class.identifier|wash}</code></td>
            <td class="a4-package-pick-groups">{foreach $class.ingroup_list as $group}<span class="a4-tag">{$group.group_name|wash}</span> {/foreach}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    <p class="a4-empty a4-package-pick-none" hidden>{'No class matches the filter.'|i18n( 'design/admin/package' )}</p>
    {/if}
</section>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{include uri="design:package/create/wizard_pick_script.tpl"}
{undef $class_list $chosen $k_list $errs}
