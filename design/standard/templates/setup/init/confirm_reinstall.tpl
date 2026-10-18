{* The setup wizard on an installed site with [SetupSettings] AllowWizardOnInstalledSite=enabled: nothing starts before this form is posted. *}
<div class="message-warning">
  <h2>{'Reinstall this site?'|i18n( 'design/standard/setup/init' )}</h2>
  <p>{'This site is already installed. The setup wizard is enabled for installed sites (AllowWizardOnInstalledSite), which is meant for development only.'|i18n( 'design/standard/setup/init' )}</p>
  <p>{'Starting the wizard puts the whole site into maintenance mode: every other visitor gets a maintenance page until the wizard finishes, or until it has been left for 15 minutes.'|i18n( 'design/standard/setup/init' )}</p>
</div>

<form method="post" action={'setup/init'|ezurl}>
  <div class="buttonblock">
    <input type="hidden" name="{$confirm_token_name|wash}" value="{$confirm_token|wash}" />
    <input class="button" type="submit" name="{$confirm_button|wash}" value="{'Start the setup wizard'|i18n( 'design/standard/setup/init' )}" />
    <a href={'/'|ezurl}>{'Cancel'|i18n( 'design/standard/setup/init' )}</a>
  </div>
</form>
