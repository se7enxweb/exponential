{* The test after an import of a URL map (since 6.0.15): Test requests every old address of the file on the site of
   its siteaccess, follows the redirects and compares where it lands with the new address (expUrlMapTester, as
   ./console exp:url:test does). Included by content/urlimport_preview.tpl once the import is done.

   Parameters: test (the kept result: base, counts, rows, tested, limited, error; or null), base (the site's address),
   token. The same file is in design/admin and design/admin4. *}
<section class="a4-section" aria-labelledby="urlimport-test-title">
<h2 class="a4-h2" id="urlimport-test-title">{'Test the old addresses'|i18n( 'design/admin/content/urlimport' )}</h2>
{if $base|eq( '' )}
<p class="a4-help">{'The siteaccess has no site address (site.ini [SiteSettings] SiteURL), so the old addresses cannot be tested here. ./console exp:url:test tests them against any address.'|i18n( 'design/admin/content/urlimport' )}</p>
{else}
<form method="post" action={concat( 'content/urlimport/(plan)/', $token )|ezurl}>
<p class="a4-help">{'Requests each old address on %base, follows its redirects and checks that it lands on its new address.'|i18n( 'design/admin/content/urlimport',, hash( '%base', $base ) )|wash}</p>
<div class="a4-actions">
    <button type="submit" class="a4-btn{if is_array( $test )|not} a4-btn-primary{/if}" name="TestButton" value="1">{if is_array( $test )}{'Test again'|i18n( 'design/admin/content/urlimport' )}{else}{'Test'|i18n( 'design/admin/content/urlimport' )}{/if}</button>
</div>
</form>
{/if}
{if and( is_array( $test ), is_array( $test.counts ) )}
{def $t = $test.counts}
<div class="a4-notice {if $t.landed|eq( $test.tested|sub( $t.skipped ) )}is-ok{else}is-warn{/if}" role="status">
    <p>{'%landed landed, %status with the wrong status, %target on the wrong page, %none not redirected, %error failed, %skipped skipped.'|i18n( 'design/admin/content/urlimport',, hash( '%landed', $t.landed, '%status', $t['wrong-status'], '%target', $t['wrong-target'], '%none', $t['not-redirected'], '%error', $t.error, '%skipped', $t.skipped ) )|wash}</p>
    {if $test.limited}<p>{'Only the first %count rows were tested.'|i18n( 'design/admin/content/urlimport',, hash( '%count', $test.tested ) )|wash}</p>{/if}
</div>
{if $test.rows|count}
<div class="a4-table-wrap">
<table class="a4-table">
<caption class="a4-sr-only">{'The rows that did not land on their new address'|i18n( 'design/admin/content/urlimport' )}</caption>
<thead>
<tr>
    <th scope="col" class="a4-num">{'Line'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Old address'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'New address'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'State'|i18n( 'design/admin/content/urlimport' )}</th>
    <th scope="col">{'Notes'|i18n( 'design/admin/content/urlimport' )}</th>
</tr>
</thead>
<tbody>
{foreach $test.rows as $row}
<tr>
    <td class="a4-num">{$row.line|wash}</td>
    <td><code>{$row.old_url|wash}</code></td>
    <td><code>{$row.new_url|wash}</code></td>
    <td><span class="a4-tag">{$row.state|wash}</span></td>
    <td>{$row.message|wash}</td>
</tr>
{/foreach}
</tbody>
</table>
</div>
{/if}
{undef $t}
{elseif and( is_array( $test ), $test.error|ne( '' ) )}
<div class="a4-notice is-bad" role="alert"><p>{'The old addresses could not be tested.'|i18n( 'design/admin/content/urlimport' )}</p></div>
{/if}
</section>
