{* The setup wizard on a site that is already installed: nothing is changed, the site stays online.
   $run_anyway: [SetupSettings] AllowWizardOnInstalledSite allows the wizard here (auto: CheckValidity=true, or enabled);
   the button only leads to the confirm page. The form posts to $script, never to a /setup/... address. *}
<div class="message-warning">
  <h2>{'This site is already installed'|i18n( 'design/standard/setup/init' )}</h2>
  <p>{'Opening the setup wizard changes nothing: the site stays online.'|i18n( 'design/standard/setup/init' )}</p>
  <p>{'A site is reinstalled in one of three ways: with the setup wizard, which asks for a confirmation first; with the kickstarter (%file, then php bin/php/kickstarter.php run --apply); or with a fresh installation. A reinstall replaces the content and settings of the site.'|i18n( 'design/standard/setup/init',, hash( '%file', $kickstart_file|wash ) )}</p>
{if $run_anyway}
  <p>{'The setup wizard is allowed on this installed site: [SiteAccessSettings] CheckValidity=true is set, or [SetupSettings] AllowWizardOnInstalledSite=enabled. When you are done, set CheckValidity=false again.'|i18n( 'design/standard/setup/init' )}</p>
{else}
  <p>{'The setup wizard does not run on this site. To run it on an installed site, set [SiteAccessSettings] CheckValidity=true (with [SetupSettings] AllowWizardOnInstalledSite=auto, the default) or AllowWizardOnInstalledSite=enabled.'|i18n( 'design/standard/setup/init' )}</p>
{/if}
</div>
{if $run_anyway}

<form method="post" action="{$script|wash}">
  <div class="buttonblock">
    <input class="button" type="submit" name="{$run_anyway_button|wash}" value="{'Run the setup wizard anyway'|i18n( 'design/standard/setup/init' )}" />
  </div>
</form>
{/if}
