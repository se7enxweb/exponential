{* The administration's Info > About page: the version, what Exponential is, the license, the
   copyright notice, contributors and third-party software, and the extensions loaded at run time.

   Set by kernel/ezinfo/about.php:
     ezinfo            the version string
     license           the text of the LICENSE file (false when it cannot be read)
     contributors      array of hash( 'name', 'files' )
     third_party_software  array of strings (var/storage/third_party_software.php)
     third_party_rows  the software the extensions include: name, version, license, copyright,
                       url, extension, extension_identifier
     extension_rows    one per extension: position, identifier, name, description, version,
                       license, url, copyright, author, includes; all plain text
     extension_sort    hash( 'field', 'direction', 'opposite' ) for parts/sortheader.tpl

   The styles are in stylesheets/ezinfo.css. The license text is a legal notice and is shown
   as it is. *}
{ezcss_require( 'ezinfo.css' )}
{def $label_order = 'Order'|i18n( 'design/admin/ezinfo/about' )
     $label_name = 'Name'|i18n( 'design/admin/ezinfo/about' )
     $label_identifier = 'Identifier'|i18n( 'design/admin/ezinfo/about' )
     $label_version = 'Version'|i18n( 'design/admin/ezinfo/about' )
     $label_license = 'License'|i18n( 'design/admin/ezinfo/about' )
     $label_website = 'Website'|i18n( 'design/admin/ezinfo/about' )
     $label_includes = 'Includes'|i18n( 'design/admin/ezinfo/about' )
     $label_not_stated = 'Not stated'|i18n( 'design/admin/ezinfo/about' )
     $has_contributors = and( is_set( $contributors ), is_array( $contributors ), $contributors|count|gt( 0 ) )
     $has_third_party_list = and( is_set( $third_party_software ), is_array( $third_party_software ), $third_party_software|count|gt( 0 ) )
     $third_party_count = sum( $third_party_rows|count, cond( $has_third_party_list, $third_party_software|count, 0 ) )}

<div class="context-block ezinfo-page ezinfo-about">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'Exponential information: %version'|i18n( 'design/admin/ezinfo/about',, hash( '%version', $ezinfo ) )|wash}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="ezinfo-intro">
    <ul class="ezinfo-summary">
        <li class="ezinfo-summary-version"><span>{$label_version|wash}</span> <strong>{$ezinfo|wash}</strong></li>
        <li><strong>{$extension_rows|count}</strong> {'extensions loaded'|i18n( 'design/admin/ezinfo/about' )}</li>
        <li><strong>{$third_party_count}</strong> {'third-party components'|i18n( 'design/admin/ezinfo/about' )}</li>
        <li class="ezinfo-summary-license">GNU GPL v2</li>
    </ul>
    <nav class="ezinfo-toc" aria-label="{'On this page'|i18n( 'design/admin/ezinfo/about' )|wash}">
        <span class="ezinfo-toc-label">{'On this page'|i18n( 'design/admin/ezinfo/about' )}:</span>
        <a href="#ezinfo-what">{'What is Exponential?'|i18n( 'design/admin/ezinfo/about' )}</a>
        <a href="#ezinfo-license">{'License'|i18n( 'design/admin/ezinfo/about' )}</a>
        <a href="#ezinfo-copyright">{'Copyright Notice'|i18n( 'design/admin/ezinfo/about' )}</a>
        <a href="#ezinfo-third-party">{if $has_contributors}{'Contributors and third-party software'|i18n( 'design/admin/ezinfo/about' )}{else}{'Third-Party Software'|i18n( 'design/admin/ezinfo/about' )}{/if}</a>
        <a href="#ezinfo-extensions">{'Extensions'|i18n( 'design/admin/ezinfo/about' )}</a>
    </nav>
</div>

<section class="ezinfo-section" id="ezinfo-what">
    <h2>{'What is Exponential?'|i18n( 'design/admin/ezinfo/about' )}</h2>
    <div class="ezinfo-prose">
        <p>{'Exponential is a professional PHP application framework with advanced CMS (content management system) functionality. As a CMS, its most notable feature is its fully customizable and extendable content model. This is also what makes Exponential suitable as a platform for general PHP development, allowing you to rapidly create professional web-based applications.'|i18n( 'design/admin/ezinfo/about' )}</p>
        <p>{'Standard CMS functionality, such as news publishing, e-commerce and forums, is already implemented and ready to use. Standalone libraries can be used for cross-platform, database-independent and browser-neutral PHP projects. Because Exponential is a web-based application, it can be used from anywhere with an internet connection.'|i18n( 'design/admin/ezinfo/about' )}</p>
        <p class="ezinfo-links"><a href="https://exponential.earth" rel="noopener">exponential.earth</a> <a href="https://se7enx.com" rel="noopener">se7enx.com</a></p>
    </div>
</section>

<section class="ezinfo-section" id="ezinfo-license">
    <h2>{'License'|i18n( 'design/admin/ezinfo/about' )}</h2>
    <div class="ezinfo-prose">
        <p>{'Exponential is free software, licensed under the GNU General Public License version 2. The license text distributed with this copy follows.'|i18n( 'design/admin/ezinfo/about' )}</p>
    </div>
    {if $license}
    <pre class="ezinfo-license" tabindex="0" aria-label="{'License'|i18n( 'design/admin/ezinfo/about' )|wash}">{$license|wash}</pre>
    {else}
    <div class="message-warning"><p>{'Could not load LICENSE file! You should have a LICENSE file in your Exponential root directory.'|i18n( 'design/admin/ezinfo/about' )}</p></div>
    {/if}
</section>

<section class="ezinfo-section" id="ezinfo-copyright">
    <h2>{'Copyright Notice'|i18n( 'design/admin/ezinfo/about' )}</h2>
    <div class="ezinfo-prose">
        <p>{'Copyright for Exponential is included in the license shown above. Portions are copyright by other parties. A complete list of all contributors and third-party software follows.'|i18n( 'design/admin/ezinfo/about' )}</p>
        <p><a href={'/ezinfo/copyright'|ezurl}>{'Read the full copyright notice'|i18n( 'design/admin/ezinfo/about' )}</a></p>
    </div>
</section>

<section class="ezinfo-section" id="ezinfo-third-party">
    <h2>{if $has_contributors}{'Contributors and third-party software'|i18n( 'design/admin/ezinfo/about' )}{else}{'Third-Party Software'|i18n( 'design/admin/ezinfo/about' )}{/if}</h2>

    {if $has_contributors}
    <h3>{'Contributors'|i18n( 'design/admin/ezinfo/about' )}</h3>
    <div class="ezinfo-prose">
        <p>{'The following is a list of Exponential contributors who have licensed their work for use by 7x under the terms and conditions of the eZ Systems Contributor Licensing Agreement. As permitted by this agreement, 7x redistributes each contribution under the same license as the file that the contribution is included in. The list names each contributor, optional contact info and the files they have contributed or contributed work to.'|i18n( 'design/admin/ezinfo/about' )}</p>
    </div>
    <ul class="ezinfo-list">
    {foreach $contributors as $contributor}
        <li><strong>{$contributor['name']|wash}</strong>: {$contributor['files']|wash}</li>
    {/foreach}
    </ul>
    <h3>{'Third-Party Software'|i18n( 'design/admin/ezinfo/about' )}</h3>
    {/if}

    <div class="ezinfo-prose">
        <p>{'The following is a list of the third-party software that is distributed with this copy of Exponential. The list of third party software includes the license for the software in question and the directory or files that contain the third-party software.'|i18n( 'design/admin/ezinfo/about' )}</p>
    </div>

    {if $third_party_rows}
    <div class="ezinfo-table-wrap">
    <table class="list ezinfo-third-party" cellspacing="0">
        <thead>
            <tr>
                <th scope="col">{'Software'|i18n( 'design/admin/ezinfo/about' )}</th>
                <th scope="col">{$label_version|wash}</th>
                <th scope="col">{$label_license|wash}</th>
                <th scope="col">{'Included by'|i18n( 'design/admin/ezinfo/about' )}</th>
            </tr>
        </thead>
        <tbody>
        {foreach $third_party_rows as $software sequence array( 'bglight', 'bgdark' ) as $style}
            <tr class="{$style}">
                <td class="ezinfo-cell-name">
                    {if $software.url}<a href="{$software.url|wash}" rel="noopener">{$software.name|wash}</a>{else}{$software.name|wash}{/if}
                    {if $software.copyright}<span class="ezinfo-meta">{$software.copyright|wash}</span>{/if}
                </td>
                <td class="ezinfo-cell-version" data-label="{$label_version|wash}">{if $software.version}{$software.version|wash}{else}<span class="ezinfo-none">{$label_not_stated|wash}</span>{/if}</td>
                <td class="ezinfo-cell-license" data-label="{$label_license|wash}">{if $software.license}{$software.license|wash}{else}<span class="ezinfo-none">{$label_not_stated|wash}</span>{/if}</td>
                <td class="ezinfo-cell-extension" data-label="{'Included by'|i18n( 'design/admin/ezinfo/about' )|wash}"><a href="#ezinfo-extension-{$software.extension_identifier|wash}">{$software.extension|wash}</a></td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
    {/if}

    {if $has_third_party_list}
    <ul class="ezinfo-list">
    {foreach $third_party_software as $software}
        <li>{$software|strip_tags|wash}</li>
    {/foreach}
    </ul>
    {/if}

    {if and( $third_party_rows|not, $has_third_party_list|not )}
    <p class="ezinfo-none">{'No third-party software is listed for this installation.'|i18n( 'design/admin/ezinfo/about' )}</p>
    {/if}
</section>

<section class="ezinfo-section" id="ezinfo-extensions">
    <h2>{'Extensions'|i18n( 'design/admin/ezinfo/about' )} <span class="ezinfo-count">({$extension_rows|count})</span></h2>
    <div class="ezinfo-prose">
        <p>{'The following is a list of the extensions that have been loaded at run-time by this copy of Exponential.'|i18n( 'design/admin/ezinfo/about' )} {'Order is the loading order: an extension earlier in the list overrides the settings and templates of the ones after it.'|i18n( 'design/admin/ezinfo/about' )}</p>
    </div>

    {* On a narrow screen the table turns into cards without its heading row: the same sorting
       as links above them. *}
    <p class="ezinfo-sortbar">
        <span>{'Sort by'|i18n( 'design/admin/ezinfo/about' )}:</span>
        {foreach hash( 'order', $label_order, 'name', $label_name, 'identifier', $label_identifier, 'version', $label_version, 'license', $label_license ) as $sort_key => $sort_label}
            {if eq( $extension_sort.field, $sort_key )}
            <a class="ezinfo-sortbar-current" aria-current="true" href={concat( '/ezinfo/about/(sort)/', $sort_key, '/(dir)/', $extension_sort.opposite, '#ezinfo-extensions' )|ezurl}>{$sort_label|wash} {if eq( $extension_sort.direction, 'asc' )}&#9650;{else}&#9660;{/if}</a>
            {else}
            <a href={concat( '/ezinfo/about/(sort)/', $sort_key, '/(dir)/asc#ezinfo-extensions' )|ezurl}>{$sort_label|wash}</a>
            {/if}
        {/foreach}
    </p>

    <div class="ezinfo-table-wrap">
    <table class="list ezinfo-extensions" cellspacing="0">
        <thead>
            <tr>
                {include uri='design:parts/sortheader.tpl' key='order' label=$label_order sort=$extension_sort page_uri='/ezinfo/about' suffix='#ezinfo-extensions' cell_class='tight ezinfo-col-order'}
                {include uri='design:parts/sortheader.tpl' key='name' label=$label_name sort=$extension_sort page_uri='/ezinfo/about' suffix='#ezinfo-extensions' cell_class='ezinfo-col-name'}
                {include uri='design:parts/sortheader.tpl' key='identifier' label=$label_identifier sort=$extension_sort page_uri='/ezinfo/about' suffix='#ezinfo-extensions' cell_class='ezinfo-col-identifier'}
                {include uri='design:parts/sortheader.tpl' key='version' label=$label_version sort=$extension_sort page_uri='/ezinfo/about' suffix='#ezinfo-extensions' cell_class='ezinfo-col-version'}
                {include uri='design:parts/sortheader.tpl' key='license' label=$label_license sort=$extension_sort page_uri='/ezinfo/about' suffix='#ezinfo-extensions' cell_class='ezinfo-col-license'}
                <th scope="col" class="ezinfo-col-website">{$label_website|wash}</th>
            </tr>
        </thead>
        <tbody>
        {foreach $extension_rows as $row sequence array( 'bglight', 'bgdark' ) as $style}
            <tr class="{$style}" id="ezinfo-extension-{$row.identifier|wash}" data-extension="{$row.identifier|wash}">
                <td class="ezinfo-cell-order tight" data-label="{$label_order|wash}"><span class="ezinfo-position">{$row.position}</span></td>
                <td class="ezinfo-cell-name">
                    <span class="ezinfo-extension-name">{$row.name|wash}</span>
                    {if $row.description}<span class="ezinfo-meta ezinfo-description">{$row.description|wash}</span>{/if}
                    {if $row.copyright}<span class="ezinfo-meta">{$row.copyright|wash}</span>{/if}
                    {if and( $row.author, $row.copyright|contains( $row.author )|not )}<span class="ezinfo-meta">{'Author'|i18n( 'design/admin/ezinfo/about' )}: {$row.author|wash}</span>{/if}
                    {if $row.includes}
                    <ul class="ezinfo-includes">
                    {foreach $row.includes as $software}
                        <li><span class="ezinfo-includes-label">{$label_includes|wash}:</span> {$software.name|wash}{if $software.version} {$software.version|wash}{/if}{if $software.license} ({$software.license|wash}){/if}</li>
                    {/foreach}
                    </ul>
                    {/if}
                </td>
                <td class="ezinfo-cell-identifier ezinfo-extension-id" data-label="{$label_identifier|wash}">{$row.identifier|wash}</td>
                <td class="ezinfo-cell-version" data-label="{$label_version|wash}">{if $row.version}{$row.version|wash}{else}<span class="ezinfo-none">{$label_not_stated|wash}</span>{/if}</td>
                <td class="ezinfo-cell-license" data-label="{$label_license|wash}">{if $row.license}{$row.license|wash}{else}<span class="ezinfo-none">{$label_not_stated|wash}</span>{/if}</td>
                <td class="ezinfo-cell-website" data-label="{$label_website|wash}">{if $row.url}<a href="{$row.url|wash}" rel="noopener">{$row.url_label|wash|explode( '/' )|implode( '/<wbr>' )}</a>{else}<span class="ezinfo-none">{$label_not_stated|wash}</span>{/if}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</section>

</div>
{* DESIGN: Content END *}</div></div></div>

</div>
{undef $label_order $label_name $label_identifier $label_version $label_license $label_website
       $label_includes $label_not_stated $has_contributors $has_third_party_list $third_party_count}
