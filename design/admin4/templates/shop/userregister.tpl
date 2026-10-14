{* Warning. *}
{if $input_error}
<div class="message-warning" role="alert">
<h2><span class="time">[{currentdate()|l10n( shortdatetime )}]</span> {'Required information is missing...'|i18n( 'design/admin/shop/userregister' )}</h2>
<ul>
<li>
{'Fill in the fields that are marked with a star.'|i18n( 'design/admin/shop/userregister' )}
</li>
</ul>
</div>
{/if}

<form method="post" action={'/shop/userregister/'|ezurl}>

<div class="context-block">

{* DESIGN: Header START *}<div class="box-header"><div class="box-ml">

<h1 class="context-title">{'Account information'|i18n( 'design/admin/shop/userregister' )}</h1>

{* DESIGN: Mainline *}<div class="header-mainline"></div>

{* DESIGN: Header END *}</div></div>

{* DESIGN: Content START *}<div class="box-ml"><div class="box-mr"><div class="box-content">

<div class="context-attributes">

<p>{'Fill in the necessary information. Required fields are marked with a star.'|i18n( 'design/admin/shop/userregister' )}</p>

{* First name. *}
<div class="block">
<label for="userregister-first-name">{'First name'|i18n( 'design/admin/shop/userregister' )}:*</label>
<input id="userregister-first-name" class="halfbox" type="text" name="FirstName" size="20" value="{$first_name|wash}" />
</div>

{* Last name. *}
<div class="block">
<label for="userregister-last-name">{'Last name'|i18n( 'design/admin/shop/userregister' )}:*</label>
<input id="userregister-last-name" class="halfbox" type="text" name="LastName" size="20" value="{$last_name|wash}" />
</div>

{* Email. *}
<div class="block">
<label for="userregister-email">{'Email'|i18n( 'design/admin/shop/userregister' )}:*</label>
<input id="userregister-email" class="halfbox" type="text" name="EMail" size="20" value="{$email|wash}" />
</div>

{* Company. *}
<div class="block">
<label for="userregister-street1">{'Company'|i18n( 'design/admin/shop/userregister' )}:</label>
<input id="userregister-street1" class="halfbox" type="text" name="Street1" size="20" value="{$street1|wash}" />
</div>

{* Street. *}
<div class="block">
<label for="userregister-street2">{'Street'|i18n( 'design/admin/shop/userregister' )}:*</label>
<input id="userregister-street2" class="halfbox" type="text" name="Street2" size="20" value="{$street2|wash}" />
</div>

{* ZIP code. *}
<div class="block">
<label for="userregister-zip">{'ZIP code'|i18n( 'design/admin/shop/userregister' )}:*</label>
<input id="userregister-zip" class="halfbox" type="text" name="Zip" size="20" value="{$zip|wash}" />
</div>

{* City. *}
<div class="block">
<label for="userregister-place">{'City'|i18n( 'design/admin/shop/userregister' )}:*</label>
<input id="userregister-place" class="halfbox" type="text" name="Place" size="20" value="{$place|wash}" />
</div>

{* State. *}
<div class="block">
<label for="userregister-state">{'State'|i18n( 'design/admin/shop/userregister' )}:</label>
<input id="userregister-state" class="halfbox" type="text" name="State" size="20" value="{$state|wash}" />
</div>

{* Country/region. *}
<div class="block">
<label for="userregister-country">{'Country/region'|i18n( 'design/admin/shop/userregister' )}:*</label>
{include uri='design:shop/country/edit.tpl' select_name='Country' select_id='userregister-country' select_size=1 current_val=$country}
</div>

{* Comments. *}
<div class="block">
<label for="userregister-comment">{'Comments'|i18n( 'design/admin/shop/userregister' )}:</label>
<textarea id="userregister-comment" name="Comment" cols="80" rows="5">{$comment|wash}</textarea>
</div>

</div>

{* DESIGN: Content END *}</div></div></div>

<div class="controlbar">
{* DESIGN: Control bar START *}<div class="box-bc"><div class="box-ml">
<div class="block">
    <input class="button" type="submit" name="StoreButton" value="{'OK'|i18n( 'design/admin/shop/userregister' )}" />
    <input class="button" type="submit" name="CancelButton" value="{'Cancel'|i18n('design/admin/shop/userregister' )}" />
</div>
{* DESIGN: Control bar END *}</div></div>
</div>

</div>

</form>
