{* The choice of package creation wizard (package/create), reached from Create new package on the package list.
   Each wizard asks its own steps (header.tpl, navigator.tpl and the creators' templates). The POST names are
   those of before: CreatorItemID, CreatePackageButton. Guide: doc/guides/packages.md *}
<div id="package">
<form method="post" action={'package/create'|ezurl}>

<div class="context-block a4-page a4-standalone">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Create package'|i18n('design/admin/package')}</h1>
</div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

    <p class="a4-meta"><a href={'package/list'|ezurl}>&laquo; {'Package list'|i18n('design/admin/package')}</a></p>
    <p class="a4-intro">{'A new package is made in the "local" repository from what this site has. Choose what it should carry; the wizard then asks for its parts, its name, version, license and maintainer, and writes it. Nothing on the site changes.'|i18n('design/admin/package')}</p>

    <fieldset class="a4-field">
    <legend><h2 class="a4-h2">{'Available wizards'|i18n('design/admin/package')}</h2></legend>
    <p class="a4-help">{'Choose one of the following wizards for creating a package'|i18n('design/admin/package')}</p>
    {if $creator_list|count|eq( 0 )}
    <p class="a4-empty">{'You are not allowed to use any of the package wizards (package/create).'|i18n('design/admin/package')}</p>
    {else}
    <ul class="a4-radios">
    {section var=creator loop=$creator_list}
        <li><label><input class="radiobutton" id="{$creator.item.id|wash}" type="radio" name="CreatorItemID" value="{$creator.item.id|wash}" {if $creator.index|eq( 0 )}checked="checked"{/if} /><span>{$creator.item.name|wash}</span></label></li>
    {/section}
    </ul>
    {/if}
    </fieldset>

</div></div></div>

<div class="a4-toolbar is-bottom">
    <div class="a4-actions">
        {* Enter submits a form with its first button, which must stay Create package: this copy comes first and is never seen. *}
        <input type="submit" name="CreatePackageButton" value="" tabindex="-1" aria-hidden="true" class="a4-sr-only" />
        <button class="a4-btn" type="submit" formaction={'package/list'|ezurl} formmethod="get">{'%arrowleft Back'|i18n( 'design/admin/package',, hash( '%arrowleft', '&laquo;' ) )}</button>
        <button class="a4-btn a4-btn-primary" type="submit" name="CreatePackageButton" value="1"{if $creator_list|count|eq( 0 )} disabled="disabled"{/if}>{'Create package'|i18n('design/admin/package')}</button>
    </div>
</div>

</div>

</form>
</div>
