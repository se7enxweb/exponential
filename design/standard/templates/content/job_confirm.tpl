{* The confirmation of an operation that had none before (hide, reveal, section ...), shown only when it is large
   or the user chose the background last time: what it touches and the now-or-background choice. Variables:
   $title, $text, $action_url, $back_url, $hidden (hash name => value, or name[] => list of values), $job_summary,
   $job_mode, $operation. *}
<form method="post" action={$action_url|ezurl}>
<div class="context-block" id="exp-contentjob-confirm">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">
<h1 class="context-title">{$title|wash}</h1>
{* DESIGN: Mainline *}<div class="header-mainline"></div>
{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">
<div class="block"><p>{$text|wash}</p></div>
{if $job_summary}{include uri='design:content/job_summary.tpl' job_summary=$job_summary operation=$operation}{/if}
{foreach $hidden as $name => $value}{if is_array( $value )}{foreach $value as $item}<input type="hidden" name="{$name|wash}" value="{$item|wash}" />{/foreach}{else}<input type="hidden" name="{$name|wash}" value="{$value|wash}" />{/if}{/foreach}
{if $job_mode}{include uri='design:content/job_mode_choice.tpl' job_mode=$job_mode}{/if}
{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    <input class="defaultbutton" type="submit" name="ConfirmJobButton" value="{'OK'|i18n( 'design/admin/content/job' )}" />
    <a class="button" href={$back_url|ezurl}>{'Cancel'|i18n( 'design/admin/content/job' )}</a>
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>
</form>
