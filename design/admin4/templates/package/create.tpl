{* The choice of package creation wizard (package/create), reached from Create new package on the package list.
   Each wizard asks its own steps (package/create/wizard_*.tpl and the creators' templates); this page says what each
   one exports. The POST names are those of before: CreatorItemID, CreatePackageButton. The same file is in
   design/admin and design/admin4. Guides: doc/features/6.0/package-wizard.md, doc/guides/packages.md *}
{def $creator_help = hash( 'ezcontentclass', 'The definitions of content classes, to create the same classes on another site. No content is included.'|i18n( 'design/admin/package' ),
                           'ezcontentobject', 'Nodes or whole subtrees with their content, optionally with their class definitions and templates.'|i18n( 'design/admin/package' ),
                           'ezextension', 'The files of extensions, as they are on this site. Exporting extensions has to be switched on first.'|i18n( 'design/admin/package' ),
                           'ezstyle', 'A site style: two stylesheets, their images and a thumbnail.'|i18n( 'design/admin/package' ) )}
<div id="package">
<form method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">

<div class="box-header"><div class="box-ml">
<div class="a4-pagehead">
    <div class="a4-pagehead-main">
        <p class="a4-pagehead-pre"><a href={'package/list'|ezurl}>{'Packages'|i18n( 'design/admin/package' )}</a></p>
        <h1 class="context-title a4-pagehead-title">{'Create package'|i18n( 'design/admin/package' )}</h1>
    </div>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

    {* Enter submits a form with its first button, which must stay Create package: this copy comes first and is never seen. *}
    <input type="submit" name="CreatePackageButton" value="" tabindex="-1" aria-hidden="true" class="a4-sr-only" />

    <p class="a4-intro">{'A new package is made in the "local" repository from what this site has. Choose what it should carry; the wizard then asks for its parts, its name, version, license and maintainer, and writes it. Nothing on the site changes.'|i18n( 'design/admin/package' )}</p>

    <fieldset class="a4-card a4-package-creators">
    <legend class="a4-h2">{'Available wizards'|i18n( 'design/admin/package' )}</legend>
    <p class="a4-help" id="a4-package-creators-help">{'Choose one of the following wizards for creating a package'|i18n( 'design/admin/package' )}</p>
    {if $creator_list|count|eq( 0 )}
    <p class="a4-empty">{'You are not allowed to use any of the package wizards (package/create).'|i18n( 'design/admin/package' )}</p>
    {else}
    <ul class="a4-radios a4-package-creator-list">
    {foreach $creator_list as $index => $creator}
        <li><label><input id="{$creator.id|wash}" type="radio" name="CreatorItemID" value="{$creator.id|wash}"{if $index|eq( 0 )} checked="checked"{/if} aria-describedby="{$creator.id|wash}-help" /><span><strong>{$creator.name|wash}</strong><span class="a4-help" id="{$creator.id|wash}-help">{first_set( $creator_help[$creator.id], '' )|wash}</span></span></label></li>
    {/foreach}
    </ul>
    {/if}
    </fieldset>

    <div class="a4-toolbar is-bottom a4-package-wizard-actions">
        <div class="a4-actions">
            <button class="a4-btn a4-btn-primary" type="submit" name="CreatePackageButton" value="1"{if $creator_list|count|eq( 0 )} disabled="disabled"{/if}>{'Start the wizard'|i18n( 'design/admin/package' )}</button>
        </div>
        <a class="a4-btn a4-btn-ghost a4-package-wizard-cancel" href={'package/list'|ezurl}>{'Cancel'|i18n( 'design/admin/package' )}</a>
    </div>

</div></div></div>
</div>
</form>
</div>
{undef $creator_help}
