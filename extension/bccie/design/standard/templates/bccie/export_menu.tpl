{* DESIGN: Header START *}<div class="box-header"><div class="box-tc"><div class="box-ml"><div class="box-mr"><div class="box-tl"><div class="box-tr">

<h4>{'Collected informations'|i18n( 'design/bccie/overview')}</h4>

{* DESIGN: Header END *}</div></div></div></div></div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-mr"><div class="box-bl"><div class="box-br"><div class="box-content">

{section show=$object_array}
{* Items per page selector. *}

{* Object table. *}
{section var=Objects loop=$object_array sequence=array( bglight, bgdark )}
<ul>
    <li><a href={concat( '/bccie/export/', $Objects.item.contentobject_id )|ezurl}>{$Objects.item.class_identifier|icon( 'small', 'section'|i18n( 'design/admin/infocollector/overview' ) )}&nbsp;{$Objects.item.name|wash}</a></li>
</ul>
{/section}
{section-else}
<div class="block">
<p>{'There are no objects that have collected any information.'|i18n( 'design/admin/infocollector/overview' )}</p>
</div>

{/section}



{* DESIGN: Content END *}</div></div></div></div></div></div>
