{* The setup wizard on a site that is already installed: nothing is changed, the site stays online. *}
<div class="message-warning">
  <h2>{'This site is already installed'|i18n( 'design/standard/setup/init' )}</h2>
  <p>{'The setup wizard installs Exponential on a new site. It does not run on an installed site, and opening it changes nothing: the site stays online.'|i18n( 'design/standard/setup/init' )}</p>
  <p>{'Reinstalling is done with the kickstarter (%file, then php bin/php/kickstarter.php run --apply) or with a fresh installation.'|i18n( 'design/standard/setup/init',, hash( '%file', $kickstart_file|wash ) )}</p>
</div>
