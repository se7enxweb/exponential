{* The package information step of the package creation wizard (package/create, every creator).

   Name, summary, description and version; the format it is kept in; the license, a strict choice from package.ini
   [LicenseSettings] (the server refuses any other value; the link and description follow the chosen option); host
   and packager. An error of the last Continue is shown at its field and linked from the summary above. The POST
   names are those of before: PackageName, PackageSummary, PackageDescription, PackageVersion, PackageFormat,
   PackageLicence, PackageHost, PackagePackager. The same file is in design/admin and design/admin4.
   Guide: doc/features/6.0/package-wizard.md *}
{def $k_name = 'Package name'|i18n( 'kernel/package' )
     $k_summary = 'Summary'|i18n( 'kernel/package' )
     $k_version = 'Version'|i18n( 'kernel/package' )
     $k_licence = 'License'|i18n( 'kernel/package' )
     $errs = hash()}
{foreach $error_list as $e}{set $errs = $errs|merge( hash( $e.field, $e.description ) )}{/foreach}
<div id="package" class="create">
<div id="sid-{$current_step.id|wash}" class="pc-{$creator.id|wash}">
<form method="post" action={'package/create'|ezurl} class="a4-page a4-standalone a4-package-wizard">
<div class="context-block">
{include uri="design:package/create/wizard_head.tpl"}
<div class="box-bc"><div class="box-ml"><div class="box-content">
{include uri="design:package/create/wizard_progress.tpl"
         intro='Name the package and say what it holds. The name becomes its folder in the local repository and cannot be changed later.'|i18n( 'design/admin/package' )
         field_ids=hash( $k_name, 'PackageName', $k_summary, 'PackageSummary', $k_version, 'PackageVersion', $k_licence, 'PackageLicence' )}

<p class="a4-required-hint"><span class="a4-required-mark" aria-hidden="true">*</span>{'Required'|i18n( 'design/admin/package' )}</p>

<section class="a4-card" aria-labelledby="pkg-info-about">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-info-about">{'About the package'|i18n( 'design/admin/package' )}</h3></div>
    <div class="a4-form is-wide a4-package-form">
        <div class="a4-field">
            <label for="PackageName">{'Package name'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input id="PackageName" type="text" name="PackageName" value="{$persistent_data.name|wash}" aria-required="true" autocomplete="off" spellcheck="false" aria-describedby="{if is_set( $errs[$k_name] )}PackageName-error {/if}PackageName-help"{if is_set( $errs[$k_name] )} aria-invalid="true"{/if} />
            {if is_set( $errs[$k_name] )}<p class="a4-field-error" id="PackageName-error">{$errs[$k_name]|wash}</p>{/if}
            <span class="a4-help" id="PackageName-help">{'Lowercase letters a-z, digits and underscores, for example my_site_classes.'|i18n( 'design/admin/package' )}</span>
        </div>
        <div class="a4-field">
            <label for="PackageVersion">{'Version'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input id="PackageVersion" type="text" name="PackageVersion" value="{$persistent_data.version|wash}" aria-required="true" autocomplete="off" spellcheck="false" aria-describedby="{if is_set( $errs[$k_version] )}PackageVersion-error {/if}PackageVersion-help"{if is_set( $errs[$k_version] )} aria-invalid="true"{/if} />
            {if is_set( $errs[$k_version] )}<p class="a4-field-error" id="PackageVersion-error">{$errs[$k_version]|wash}</p>{/if}
            <span class="a4-help" id="PackageVersion-help">{'Semantic versioning: MAJOR.MINOR.PATCH, for example 1.0.0 or 1.2.0-beta.1.'|i18n( 'design/admin/package' )}</span>
        </div>
        <div class="a4-field a4-field-wide">
            <label for="PackageSummary">{'Summary'|i18n( 'design/admin/package' )}<span class="a4-required-mark" aria-hidden="true">*</span></label>
            <input id="PackageSummary" type="text" name="PackageSummary" value="{$persistent_data.summary|wash}" aria-required="true" aria-describedby="{if is_set( $errs[$k_summary] )}PackageSummary-error {/if}PackageSummary-help"{if is_set( $errs[$k_summary] )} aria-invalid="true"{/if} />
            {if is_set( $errs[$k_summary] )}<p class="a4-field-error" id="PackageSummary-error">{$errs[$k_summary]|wash}</p>{/if}
            <span class="a4-help" id="PackageSummary-help">{'One line, shown in the package list.'|i18n( 'design/admin/package' )}</span>
        </div>
        <div class="a4-field a4-field-wide">
            <label for="PackageDescription">{'Description'|i18n( 'design/admin/package' )}</label>
            <textarea id="PackageDescription" rows="5" name="PackageDescription" aria-describedby="PackageDescription-help">{$persistent_data.description|wash}</textarea>
            <span class="a4-help" id="PackageDescription-help">{'Optional. What the package is for and what it needs.'|i18n( 'design/admin/package' )}</span>
        </div>
    </div>
</section>

<section class="a4-card" aria-labelledby="pkg-info-format">
    <div class="a4-card-head"><h3 class="a4-h3" id="pkg-info-format">{'Format and license'|i18n( 'design/admin/package' )}</h3></div>
    <div class="a4-form is-wide a4-package-form">
        {* The format the package is kept in (doc/bc/6.0/package-json-format.md); either is downloadable in both *}
        <div class="a4-field">
            <label for="PackageFormat">{'Format'|i18n( 'design/admin/package' )}</label>
            <select id="PackageFormat" name="PackageFormat" aria-describedby="PackageFormat-help">
                <option value="xml"{if first_set( $persistent_data.format, 'xml' )|ne( 'json' )} selected="selected"{/if}>{'Classic XML (package.xml)'|i18n( 'design/admin/package' )}</option>
                <option value="json"{if first_set( $persistent_data.format, 'xml' )|eq( 'json' )} selected="selected"{/if}>{'JSON (package.json)'|i18n( 'design/admin/package' )}</option>
            </select>
            <span class="a4-help" id="PackageFormat-help">{'A package can be downloaded in either format later.'|i18n( 'design/admin/package' )}</span>
        </div>
        <div class="a4-field package-licence">
            <label for="PackageLicence">{'License'|i18n( 'design/admin/package' )}</label>
            {if $licence_groups|count|gt( 0 )}
            <select id="PackageLicence" name="PackageLicence" aria-describedby="{if is_set( $errs[$k_licence] )}PackageLicence-error {/if}PackageLicenceInfo"{if is_set( $errs[$k_licence] )} aria-invalid="true"{/if}>
            {foreach $licence_groups as $group}
                <optgroup label="{$group.name|wash}">
                {foreach $group.licenses as $license}
                    <option value="{$license.identifier|wash}" data-name="{$license.name|wash}" data-url="{$license.url|wash}" data-description="{$license.description|wash}"{if $license.identifier|eq( $licence_selected.identifier )} selected="selected"{/if}>{$license.name|wash} ({$license.identifier|wash})</option>
                {/foreach}
                </optgroup>
            {/foreach}
            </select>
            {if is_set( $errs[$k_licence] )}<p class="a4-field-error" id="PackageLicence-error">{$errs[$k_licence]|wash}</p>{/if}
            <p id="PackageLicenceInfo" class="a4-help package-licence-info a4-package-licence-info">
                <span id="PackageLicenceDescription"{if $licence_selected.description|not} hidden{/if}>{$licence_selected.description|wash}<br /></span>
                <span id="PackageLicenceLinkLine"{if $licence_selected.url|not} hidden{/if}>{'License text:'|i18n( 'design/admin/package' )} <a id="PackageLicenceLink" href="{$licence_selected.url|wash}" target="_blank" rel="noopener noreferrer">{$licence_selected.name|wash}</a></span>
            </p>
            {literal}
            <script type="text/javascript">
            (function()
            {
                var select = document.getElementById( 'PackageLicence' ),
                    link = document.getElementById( 'PackageLicenceLink' ),
                    linkLine = document.getElementById( 'PackageLicenceLinkLine' ),
                    description = document.getElementById( 'PackageLicenceDescription' );
                if ( !select || !link )
                    return;
                function update()
                {
                    var option = select.options[select.selectedIndex];
                    if ( !option )
                        return;
                    var url = option.getAttribute( 'data-url' ) || '',
                        text = option.getAttribute( 'data-description' ) || '';
                    link.href = url || '#';
                    link.textContent = option.getAttribute( 'data-name' ) || option.value;
                    linkLine.hidden = !url;
                    description.firstChild.nodeValue = text;
                    description.hidden = !text;
                }
                if ( !description.firstChild || description.firstChild.nodeType !== 3 )
                    description.insertBefore( document.createTextNode( '' ), description.firstChild );
                select.addEventListener( 'change', update );
            })();
            </script>
            {/literal}
            {else}
            <p class="a4-notice is-warn package-licence-none">{'No licenses are configured for packages. Add them to package.ini [LicenseSettings] LicenseList.'|i18n( 'design/admin/package' )}</p>
            {/if}
        </div>
    </div>
</section>

<details class="a4-fold a4-package-origin">
    <summary>{'Origin: package host and packager'|i18n( 'design/admin/package' )}</summary>
    <div class="a4-fold-body">
    <div class="a4-form is-wide a4-package-form">
        <div class="a4-field">
            <label for="PackageHost">{'Package host'|i18n( 'design/admin/package' )}</label>
            <input id="PackageHost" type="text" name="PackageHost" value="{$persistent_data.host|wash}" aria-describedby="PackageHost-help" />
            <span class="a4-help" id="PackageHost-help">{'The site the package is made on.'|i18n( 'design/admin/package' )}</span>
        </div>
        <div class="a4-field">
            <label for="PackagePackager">{'Packager'|i18n( 'design/admin/package' )}</label>
            <input id="PackagePackager" type="text" name="PackagePackager" value="{$persistent_data.packager|wash}" aria-describedby="PackagePackager-help" />
            <span class="a4-help" id="PackagePackager-help">{'Who makes the package.'|i18n( 'design/admin/package' )}</span>
        </div>
    </div>
    </div>
</details>

{include uri="design:package/create/wizard_actions.tpl"}
</div></div></div>
</div>
</form>
</div>
</div>
{undef $k_name $k_summary $k_version $k_licence $errs}
