{* The choice of package creation wizard (package/create), reached from Create new package on the package list.
   Each wizard asks its own steps (header.tpl, navigator.tpl and the creators' templates). The POST names are
   those of before: CreatorItemID, CreatePackageButton. Guide: doc/guides/packages.md *}
{include uri='design:package/exp_style.tpl'}
<div id="package">
<form method="post" action={'package/create'|ezurl}>

<div class="context-block exp-packages exp-standalone">

<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Create package'|i18n('design/admin/package')}</h1>
</div></div>

<div class="box-ml"><div class="box-mr"><div class="box-content">

    <p class="exp-meta"><a href={'package/list'|ezurl}>&laquo; {'Package list'|i18n('design/admin/package')}</a></p>
    <p class="exp-intro">{'A new package is made in the "local" repository from what this site has. Choose what it should carry; the wizard then asks for its parts, its name, version, license and maintainer, and writes it. Nothing on the site changes.'|i18n('design/admin/package')}</p>

    <fieldset class="exp-field" style="margin: 0">
    <legend><h2 class="exp-h2">{'Available wizards'|i18n('design/admin/package')}</h2></legend>
    <p class="exp-help">{'Choose one of the following wizards for creating a package'|i18n('design/admin/package')}</p>
    {if $creator_list|count|eq( 0 )}
    <p class="exp-empty">{'You are not allowed to use any of the package wizards (package/create).'|i18n('design/admin/package')}</p>
    {else}
    <ul class="exp-choices">
    {section var=creator loop=$creator_list}
        <li class="exp-choice"><label><input class="radiobutton" id="{$creator.item.id|wash}" type="radio" name="CreatorItemID" value="{$creator.item.id|wash}" {if $creator.index|eq( 0 )}checked="checked"{/if} /><span>{$creator.item.name|wash}</span></label></li>
    {/section}
    </ul>
    {/if}
    </fieldset>

</div></div></div>

<div class="exp-bottombar">
    <div class="exp-actions">
        {* Enter submits a form with its first button, which must stay Create package: this copy comes first and is never seen. *}
        <input type="submit" name="CreatePackageButton" value="" tabindex="-1" aria-hidden="true" style="position: absolute; left: -10000px; width: 1px; height: 1px; overflow: hidden;" />
        <button class="exp-btn" type="submit" formaction={'package/list'|ezurl} formmethod="get">{'%arrowleft Back'|i18n( 'design/admin/package',, hash( '%arrowleft', '&laquo;' ) )}</button>
        <button class="exp-btn exp-btn-primary" type="submit" name="CreatePackageButton" value="1"{if $creator_list|count|eq( 0 )} disabled="disabled"{/if}>{'Create package'|i18n('design/admin/package')}</button>
    </div>
</div>

</div>

</form>
</div>
