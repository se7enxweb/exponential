{* The setup wizard on an installed site, allowed by [SetupSettings] AllowWizardOnInstalledSite (auto: CheckValidity=true;
   enabled): nothing starts before this form is posted. It posts to $script, never to a /setup/... address. *}
<div class="message-warning">
  <h2>{'Reinstall this site?'|i18n( 'design/standard/setup/init' )}</h2>
  <p>{'This site is already installed. Running the setup wizard installs it again and replaces its content and settings.'|i18n( 'design/standard/setup/init' )}</p>
  <p>{'Starting the wizard puts the whole site into maintenance mode: every other visitor gets a maintenance page until the wizard finishes, or until it has been left for 15 minutes.'|i18n( 'design/standard/setup/init' )}</p>
</div>

<form method="post" action="{$script|wash}">
  <div class="buttonblock">
    <input type="hidden" name="{$confirm_token_name|wash}" value="{$confirm_token|wash}" />
    <input class="button" type="submit" name="{$confirm_button|wash}" value="{'Start the setup wizard'|i18n( 'design/standard/setup/init' )}" />
    <a href={'/'|ezurl}>{'Cancel'|i18n( 'design/standard/setup/init' )}</a>
  </div>
</form>
