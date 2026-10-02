{* The browse page of "Add a location for selected" (content/action AddLocationsButton): the selected items, what
   the operation touches and the now-or-background choice, sent with the browse form. doc/bc/6.0/content-jobs.md *}
{def $content_object_name = $browse.content.name_list|implode( ', ' )}
<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Choose where to add a location for <%object_name>'|i18n( 'design/admin/content/job',, hash( '%object_name', $content_object_name ) )|wash}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-bc"><div class="box-ml"><div class="box-content">

<div class="block">
<p>{'Choose the node to add a location under using the radio buttons then click "Select". Every selected item keeps its locations and gets one more under that node.'|i18n( 'design/admin/content/job' )}</p>
<p>{'Navigate using the available tabs (above), the tree menu (left) and the content list (middle).'|i18n( 'design/admin/content/browse_move_node' )}</p>
</div>

{if and( is_set( $browse.content_job_summary ), is_array( $browse.content_job_summary ) )}
{include uri='design:content/job_summary.tpl' job_summary=$browse.content_job_summary operation='addlocation'}
{/if}
{if and( is_set( $browse.content_job_mode ), is_array( $browse.content_job_mode ) )}
{include uri='design:content/job_mode_choice.tpl' job_mode=$browse.content_job_mode form_name='browse'}
{/if}

{* DESIGN: Content END *}</div></div></div>

</div>

{undef}
