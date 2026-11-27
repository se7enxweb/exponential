{* The setup wizard on a site that is already installed: nothing is changed, the site stays online.
   $run_anyway: [SetupSettings] AllowWizardOnInstalledSite allows the wizard here (auto: CheckValidity=true, or enabled);
   the button only leads to the confirm page. The form posts to $script, never to a /setup/... address. *}
<div class="message-warning">
  <h2>{'This site is already installed'|i18n( 'design/standard/setup/init' )}</h2>
{if $run_anyway|not}
  <p>{'The setup wizard installs Exponential on a new site. It does not run on an installed site, and opening it changes nothing: the site stays online.'|i18n( 'design/standard/setup/init' )}</p>
{/if}
  <p>{'Reinstalling is done with the kickstarter (%file, then php bin/php/kickstarter.php run --apply) or with a fresh installation.'|i18n( 'design/standard/setup/init',, hash( '%file', $kickstart_file|wash ) )}</p>
{if $run_anyway}
  <p>{'Or with the setup wizard, which asks for a confirmation first.'|i18n( 'design/standard/setup/init' )}</p>
{/if}
</div>
{if $run_anyway}

<form method="post" action="{$script|wash}">
  <div class="buttonblock">
    <input class="button" type="submit" name="{$run_anyway_button|wash}" value="{'Run the setup wizard anyway'|i18n( 'design/standard/setup/init' )}" />
  </div>
</form>
{/if}
