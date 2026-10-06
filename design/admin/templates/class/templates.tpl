{* The class-level override templates of a class, on the class page. The same file is in design/admin and
   design/admin4. *}
{def $override_templates = fetch( class, override_template_list, hash( class_id, $class.id ) )}
<section class="exp-section" aria-labelledby="class-view-templates">
<div class="exp-section-head">
    <h2 class="exp-h2" id="class-view-templates">{'Override templates (%1)'|i18n( 'design/admin/class/view',, array( $override_templates|count ) )}</h2>
    <p>{'Templates that draw the objects of this class instead of the standard ones, by siteaccess.'|i18n( 'design/admin/class/view' )}</p>
</div>
{if $override_templates|count}
<div class="exp-table-wrap">
<table class="exp-table">
<thead><tr>
    <th scope="col">{'Siteaccess'|i18n( 'design/admin/class/view' )}</th>
    <th scope="col">{'Override'|i18n( 'design/admin/class/view' )}</th>
    <th scope="col">{'Source template'|i18n( 'design/admin/class/view' )}</th>
    <th scope="col">{'Override template'|i18n( 'design/admin/class/view' )}</th>
    <th scope="col"><span class="exp-sr">{'Edit'|i18n( 'design/admin/class/view' )}</span></th>
</tr></thead>
<tbody>
{foreach $override_templates as $override}
<tr>
    <td>{$override.siteaccess|wash}</td>
    <td><code>{$override.block|wash}</code></td>
    <td><a href={concat( '/visual/templateview', $override.source )|ezurl} title="{'View template overrides for the <%source_template_name> template.'|i18n( 'design/admin/class/view',, hash( '%source_template_name', $override.source ) )|wash}">{$override.source|wash}</a></td>
    <td><code>{$override.target|wash}</code></td>
    <td><a class="exp-btn exp-btn-small" href={concat( '/visual/templateedit/', $override.target )|ezurl} title="{'Edit the override template for the <%override_name> override.'|i18n( 'design/admin/class/view',, hash( '%override_name', $override.block ) )|wash}">{'Edit'|i18n( 'design/admin/class/view' )}</a></td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{else}
<p class="exp-empty">{'This class does not have any class-level override templates.'|i18n( 'design/admin/class/view' )}</p>
{/if}
</section>
{undef $override_templates}
