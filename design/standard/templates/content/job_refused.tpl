{* A remove, move or copy that was not started: it overlaps a subtree a content job is working on, or the job
   could not be created (permissions, a node that no longer exists). *}
<div class="context-block" id="exp-contentjob-refused">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{'The operation was not started'|i18n( 'design/admin/content/job' )}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="message-error">
    <h2>{$message|wash}</h2>
    {if $holder}
    <p>{'%title, %percent% done.'|i18n( 'design/admin/content/job',, hash( '%title', $holder.title, '%percent', $holder.percent ) )|wash}
       <a href={concat( 'content/job/', $holder.id )|ezurl}>{'Show the job'|i18n( 'design/admin/content/job' )}</a></p>
    {/if}
</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    <a class="button" href={$back_url|ezurl}>{'Back'|i18n( 'design/admin/content/job' )}</a>
    <a class="button" href={'content/jobs'|ezurl}>{'All jobs'|i18n( 'design/admin/content/job' )}</a>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
