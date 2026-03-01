{* ezscript_require( array( 'ezjsc::jqueryui' ) )}
{ezcss_require( 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/cupertino/jquery-ui.min.css' ) *}

{if $error}
<div class="message-error">
    <h2>{$error}</h2>
</div>
{/if}

{if $message}
<div class="message-feedback">
    <h2>{$message}</h2>
</div>	
{/if}

<div class="context-block">
	<div class="box-header">
		<h1 class="context-title">{'Composer'|i18n( 'extension/ezupdate' )}</h1>
		<div class="header-mainline"></div>
	</div>

	<div class="box-content">
		<div class="context-attributes">

			<div class="block">
				<form action="{'update/dashboard'|ezurl( 'no' )}" method="post">
					<label class="inline">{'Update Installed Composert Packages via Composer Update'|i18n( 'extension/ezupdate' )}:
					</label>
					<br /><br />
					<input class="button" type="submit" name="CheckoutUpdateComposerPackage" value="{'Update'|i18n( 'extension/ezupdate' )}">
				</form>
			</div>

		</div>
	</div>

</div>

{if $output}
<pre>{$output}</pre>
{/if}
