<pre>{$output}</pre>

<div class="controlbar">
	<form method="post" action="{'git_manager/dashboard'|ezurl( 'no' )}">
		<input type="hidden" name="hash" value="{$hash}">
		<div class="button-left">
			<div class="block">
				<input class="button" type="submit" name="CheckoutCommit" value="{'Checkout'|i18n( 'extension/git_manager' )}">
			</div>
		</div>
	</form>
</div>