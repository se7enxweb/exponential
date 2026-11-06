{* What a URL map row is (expUrlMapRow kinds): a redirect with its status, a URL alias, a wildcard. Parameters: kind,
   status (0 or the HTTP status). The same file is in design/admin and design/admin4. *}
{switch match=$kind}
{case match='redirect'}{'Redirect (%status)'|i18n( 'design/admin/content/urlimport',, hash( '%status', cond( $status|gt( 0 ), $status, 301 ) ) )|wash}{/case}
{case match='redirect-temporary'}{'Redirect (%status)'|i18n( 'design/admin/content/urlimport',, hash( '%status', cond( $status|gt( 0 ), $status, 302 ) ) )|wash}{/case}
{case match='alias'}{'URL alias'|i18n( 'design/admin/content/urlimport' )}{/case}
{case match='wildcard-forward'}{'Wildcard redirect (%status)'|i18n( 'design/admin/content/urlimport',, hash( '%status', 301 ) )|wash}{/case}
{case match='wildcard-forward-temporary'}{'Wildcard redirect (%status)'|i18n( 'design/admin/content/urlimport',, hash( '%status', 302 ) )|wash}{/case}
{case match='wildcard-direct'}{'Wildcard (direct)'|i18n( 'design/admin/content/urlimport' )}{/case}
{case}{$kind|wash}{/case}
{/switch}
