<h2>{'Software update and Maintenance'|i18n( 'design/admin/dashboard/maintenance' )}</h2>

<p>{'Your installation: <span id="ez-version">%1</span>'|i18n( 'design/admin/dashboard/maintenance', , array( fetch( 'setup', 'version' ) ) )}</p>
<p>{'You are using %edition, the <span id="Exponential-community-project-is-innovative-and-cutting-edge">innovative and cutting-edge</span> version of Exponential, built by <a href="%ez_link">7x</a> and the <a href="%ez_community_link">Exponential Community</a>.</p>'|i18n( 'design/admin/dashboard/maintenance', , hash( '%edition', $edition, '%ez_link', "https://se7enx.com", '%ez_community_link', concat( 'https://share.se7enx.com?utm_content=eZ+Publish+Community+Project+', fetch( 'setup', 'version' ) , '&utm_source=eZ+Publish+Community+Project+Dashboard&utm_medium=eZ+Publish+Community+Project+Dashboard&utm_campaign=eZ+Publish+Community+Project+Dashboard' ) ) )}
</p>
