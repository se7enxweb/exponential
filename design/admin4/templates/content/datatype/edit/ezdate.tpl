{* The calendar of the icon below is Exponential UI's exp::datepicker (loaded on every admin page by
   design.ini [JavaScriptSettings] BackendJavaScriptList[]=exp::datepicker), which defines showDatePicker(). *}
{default attribute_base=ContentObjectAttribute}
<div class="block">
<div class="date">

<div class="element">
<label for="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_year">{'Year'|i18n( 'design/admin/content/datatype' )}:</label>
<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_year" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_date_year_{$attribute.id}" size="5" value="{section show=$attribute.content.is_valid}{$attribute.content.year}{/section}" />
</div>

<div class="element">
<label for="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_month">{'Month'|i18n( 'design/admin/content/datatype' )}:</label>
<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_month" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_date_month_{$attribute.id}" size="3" value="{section show=$attribute.content.is_valid}{$attribute.content.month}{/section}" />
</div>

<div class="element">
<label for="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_day">{'Day'|i18n( 'design/admin/content/datatype' )}:</label>
<input id="ezcoa-{if ne( $attribute_base, 'ContentObjectAttribute' )}{$attribute_base}-{/if}{$attribute.contentclassattribute_id}_{$attribute.contentclass_attribute_identifier}_day" class="ezcc-{$attribute.object.content_class.identifier} ezcca-{$attribute.object.content_class.identifier}_{$attribute.contentclass_attribute_identifier}" type="text" name="{$attribute_base}_date_day_{$attribute.id}" size="3" value="{section show=$attribute.content.is_valid}{$attribute.content.day}{/section}" />
</div>
<div class="element">
{* A button, so the keyboard reaches the calendar (exp::datepicker gives the focus back to it on Escape). *}
<button type="button" class="datepicker-icon a4-content-datepicker-icon" id="{$attribute_base}_date_cal_{$attribute.id}" aria-haspopup="dialog" title="{'Choose a date'|i18n( 'design/admin/content/datatype' )|wash}" aria-label="{'Choose a date'|i18n( 'design/admin/content/datatype' )|wash}" onclick="showDatePicker( '{$attribute_base}', '{$attribute.id}', 'date' );"><img src={"calendar_icon.png"|ezimage} width="24" height="28" alt="" /></button>
<div id="{$attribute_base}_date_cal_container_{$attribute.id}" class="a4-content-datepicker"></div>
&nbsp;
&nbsp;
&nbsp;
&nbsp;
</div>

<div class="break"></div>
</div>
</div>
{/default}
