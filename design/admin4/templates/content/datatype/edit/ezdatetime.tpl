{* The calendar of the icon below is Exponential UI's exp::datepicker (loaded on every admin page by
   design.ini [JavaScriptSettings] BackendJavaScriptList[]=exp::datepicker), which defines showDatePicker(). *}
{default attribute_base=ContentObjectAttribute}
{if ne( $attribute_base, 'ContentObjectAttribute' )}
    {def $id_base = concat( 'ezcoa-', $attribute_base, '-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{else}
    {def $id_base = concat( 'ezcoa-', $attribute.contentclassattribute_id, '_', $attribute.contentclass_attribute_identifier )}
{/if}
{* admin4: the date and the time are two rows of parts (.a4-content-parts) side by side, each label above its field;
   no colon after a label *}
<div class="block a4-content-datetime">

<div class="a4-content-parts">
<div>
<label for="{$id_base}_year">{'Year'|i18n( 'design/admin/content/datatype' )}</label>
<input id="{$id_base}_year" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_datetime_year_{$attribute.id}" size="5" value="{section show=$attribute.content.is_valid}{$attribute.content.year}{/section}" />
</div>

<div>
<label for="{$id_base}_month">{'Month'|i18n( 'design/admin/content/datatype' )}</label>
<input id="{$id_base}_month"  class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_datetime_month_{$attribute.id}" size="3" value="{section show=$attribute.content.is_valid}{$attribute.content.month}{/section}" />
</div>

<div>
<label for="{$id_base}_day">{'Day'|i18n( 'design/admin/content/datatype' )}</label>
<input id="{$id_base}_day" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_datetime_day_{$attribute.id}" size="3" value="{section show=$attribute.content.is_valid}{$attribute.content.day}{/section}" />
</div>
<div>
{* A button, so the keyboard reaches the calendar (exp::datepicker gives the focus back to it on Escape). *}
<button type="button" class="a4-btn a4-btn-icon a4-content-datepicker-icon" id="{$attribute_base}_datetime_cal_{$attribute.id}" aria-haspopup="dialog" title="{'Choose a date'|i18n( 'design/admin/content/datatype' )|wash}" aria-label="{'Choose a date for %name'|i18n( 'design/admin/content/datatype',, hash( '%name', $attribute.contentclass_attribute.name ) )|wash}" onclick="showDatePicker( '{$attribute_base}', '{$attribute.id}', 'datetime' );"><span class="a4-icon a4-bi-calendar3" aria-hidden="true"></span></button>
<div id="{$attribute_base}_datetime_cal_container_{$attribute.id}" class="a4-content-datepicker"></div>
</div>
</div>

<div class="a4-content-parts">
<div>
<label for="{$id_base}_hour">{'Hour'|i18n( 'design/admin/content/datatype' )}</label>
<input id="{$id_base}_hour" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_datetime_hour_{$attribute.id}" size="3" value="{section show=$attribute.content.is_valid}{$attribute.content.hour}{/section}" />
</div>

<div>
<label for="{$id_base}_minute">{'Minute'|i18n( 'design/admin/content/datatype' )}</label>
<input id="{$id_base}_minute" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_datetime_minute_{$attribute.id}" size="3" value="{section show=$attribute.content.is_valid}{$attribute.content.minute}{/section}" />
</div>

{if $attribute.contentclass_attribute.data_int2|eq(1)}
<div>
<label for="{$id_base}_second">{'Second'|i18n( 'design/standard/content/datatype' )}</label>
<input id="{$id_base}_second" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_datetime_second_{$attribute.id}" size="3" value="{if $attribute.content.is_valid}{$attribute.content.second}{/if}" />
</div>
{/if}

</div>

</div>
{/default}
