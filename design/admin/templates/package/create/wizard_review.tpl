{* The review on the last step of the package creation wizard: everything the earlier steps collected
   ($persistent_data), shown before Create package writes the package. It only reads; Back changes a step.
   The same file is in design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{def $rv = $persistent_data
     $rv_none = '&ndash;'}
<section class="a4-card a4-package-review" aria-labelledby="a4-package-review-title">
    <div class="a4-card-head">
        <h2 class="a4-h2" id="a4-package-review-title">{'Review'|i18n( 'design/admin/package' )}</h2>
        <p>{'Create package writes this package to the local repository. Use Back to change a step; nothing is written until then.'|i18n( 'design/admin/package' )}</p>
    </div>

    <div class="a4-package-review-groups">
    <div class="a4-package-review-group">
        <h3 class="a4-h3">{'Package'|i18n( 'design/admin/package' )}</h3>
        <dl class="a4-facts is-rows">
            <dt>{'Package name'|i18n( 'design/admin/package' )}</dt>
            <dd>{if first_set( $rv.name, false() )}<code>{$rv.name|wash}</code>{else}{$rv_none}{/if}</dd>
            <dt>{'Summary'|i18n( 'design/admin/package' )}</dt>
            <dd>{if first_set( $rv.summary, false() )}{$rv.summary|wash}{else}{$rv_none}{/if}</dd>
            {if first_set( $rv.description, false() )}
            <dt>{'Description'|i18n( 'design/admin/package' )}</dt>
            <dd class="a4-package-review-text">{$rv.description|wash|nl2br}</dd>
            {/if}
            <dt>{'Version'|i18n( 'design/admin/package' )}</dt>
            <dd>{if first_set( $rv.version, false() )}<span class="a4-kbd">{$rv.version|wash}</span>{else}{$rv_none}{/if}</dd>
            <dt>{'Format'|i18n( 'design/admin/package' )}</dt>
            <dd>{if first_set( $rv.format, 'xml' )|eq( 'json' )}{'JSON (package.json)'|i18n( 'design/admin/package' )}{else}{'Classic XML (package.xml)'|i18n( 'design/admin/package' )}{/if}</dd>
            <dt>{'License'|i18n( 'design/admin/package' )}</dt>
            <dd>{if first_set( $rv.licence, false() )}<code>{$rv.licence|wash}</code>{else}{$rv_none}{/if}</dd>
            {if first_set( $rv.host, false() )}
            <dt>{'Package host'|i18n( 'design/admin/package' )}</dt>
            <dd>{$rv.host|wash}</dd>
            {/if}
            {if first_set( $rv.packager, false() )}
            <dt>{'Packager'|i18n( 'design/admin/package' )}</dt>
            <dd>{$rv.packager|wash}</dd>
            {/if}
            {if first_set( $rv.maintainer_person, false() )}
            <dt>{'Maintainer'|i18n( 'design/admin/package' )}</dt>
            <dd>{$rv.maintainer_person|wash}{if first_set( $rv.maintainer_email, false() )} &lt;{$rv.maintainer_email|wash}&gt;{/if}{if first_set( $rv.maintainer_role, false() )} <span class="a4-tag">{foreach fetch( 'package', 'maintainer_role_list' ) as $rv_role}{if $rv_role.id|eq( $rv.maintainer_role )}{$rv_role.name|wash}{/if}{/foreach}</span>{/if}</dd>
            {/if}
            {if first_set( $rv.thumbnail, false() )}
            <dt>{'Thumbnail'|i18n( 'design/admin/package' )}</dt>
            <dd><code>{$rv.thumbnail.filename|wash}</code></dd>
            {/if}
        </dl>
    </div>

    <div class="a4-package-review-group">
        <h3 class="a4-h3">{'Contents'|i18n( 'design/admin/package' )}</h3>
        {if first_set( $rv.classlist, false() )}
        <p class="a4-meta">{'Content classes (%count)'|i18n( 'design/admin/package',, hash( '%count', $rv.classlist|count ) )}</p>
        <ul class="a4-package-review-list">
        {foreach fetch( 'class', 'list', hash( 'class_filter', $rv.classlist ) ) as $rv_class}
            <li>{$rv_class.name|wash} <code>{$rv_class.identifier|wash}</code></li>
        {/foreach}
        </ul>
        {elseif first_set( $rv.node_list, false() )}
        <p class="a4-meta">{'Content (%count)'|i18n( 'design/admin/package',, hash( '%count', $rv.node_list|count ) )}</p>
        <ul class="a4-package-review-list">
        {foreach $rv.node_list as $rv_item}
            {def $rv_node = fetch( 'content', 'node', hash( 'node_id', $rv_item.id ) )}
            <li>{if $rv_node}{$rv_node.name|wash}{else}{$rv_item.id|wash}{/if} <span class="a4-tag">{if $rv_item.type|eq( 'subtree' )}{'Subtree'|i18n( 'design/admin/package' )}{else}{'Node'|i18n( 'design/admin/package' )}{/if}</span></li>
            {undef $rv_node}
        {/foreach}
        </ul>
        {if is_set( $rv.object_options )}
        <dl class="a4-facts is-rows">
            <dt>{'Class definitions'|i18n( 'design/admin/package' )}</dt>
            <dd>{if $rv.object_options.include_classes}{'Included'|i18n( 'design/admin/package' )}{else}{'Not included'|i18n( 'design/admin/package' )}{/if}</dd>
            <dt>{'Templates'|i18n( 'design/admin/package' )}</dt>
            <dd>{if $rv.object_options.include_templates}{'Included'|i18n( 'design/admin/package' )}{if $rv.object_options.site_access_array|count} ({$rv.object_options.site_access_array|implode( ', ' )|wash}){/if}{else}{'Not included'|i18n( 'design/admin/package' )}{/if}</dd>
            <dt>{'Versions'|i18n( 'design/admin/package' )}</dt>
            <dd>{if $rv.object_options.versions|eq( 'all' )}{'All versions'|i18n( 'design/admin/package' )}{else}{'Published version'|i18n( 'design/admin/package' )}{/if}</dd>
            <dt>{'Languages'|i18n( 'design/admin/package' )}</dt>
            <dd>{if $rv.object_options.language_array|count}{$rv.object_options.language_array|implode( ', ' )|wash}{else}{$rv_none}{/if}</dd>
        </dl>
        {/if}
        {elseif first_set( $rv.extensionlist, false() )}
        <p class="a4-meta">{'Extensions (%count)'|i18n( 'design/admin/package',, hash( '%count', $rv.extensionlist|count ) )}</p>
        <ul class="a4-package-review-list">
        {foreach $rv.extensionlist as $rv_extension}
            <li><code>{$rv_extension|wash}</code></li>
        {/foreach}
        </ul>
        {elseif or( first_set( $rv.sitecssfile, false() ), first_set( $rv.imagefiles, false() ) )}
        <ul class="a4-package-review-list">
            {if first_set( $rv.sitecssfile, false() )}<li>{'Site CSS file'|i18n( 'design/admin/package' )}: <code>{$rv.sitecssfile.filename|wash}</code></li>{/if}
            {if first_set( $rv.classescssfile, false() )}<li>{'Classes CSS file'|i18n( 'design/admin/package' )}: <code>{$rv.classescssfile.filename|wash}</code></li>{/if}
            {foreach first_set( $rv.imagefiles, array() ) as $rv_image}
            <li>{'Image'|i18n( 'design/admin/package' )}: <code>{$rv_image.filename|wash}</code></li>
            {/foreach}
        </ul>
        {else}
        <p class="a4-empty">{'Nothing chosen yet.'|i18n( 'design/admin/package' )}</p>
        {/if}
    </div>
    </div>
</section>
{undef $rv $rv_none}
