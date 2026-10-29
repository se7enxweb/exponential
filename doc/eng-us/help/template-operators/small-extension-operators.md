---
title: Operators of smaller extensions
audience: developer, designer, app-creator
category: development
order: 440
summary: The template operators of ezoe, ezwt, enhancedezbinaryfile, recaptcha, xrowmetadata and bcwebsitestatistics - ezoe_ini_section, ezcreateclasslistgroups, parsexml, recaptcha_get_html, metadata, the six xrowmetadata_license operators and the six bc_ga operators - with parameters, return values, pitfalls found on a development installation (operator names with capital letters, a die() on a missing key) and examples rendered on a development installation.
keywords: ezoe_ini_section, ezcreateclasslistgroups, parsexml, recaptcha_get_html, metadata, xrowmetadata_license, xrowmetadata_license_enabled, xrowmetadata_license_choices, xrowmetadata_license_info, xrowmetadata_license_inherited, xrowmetadata_license_class_settings, bc_ga_urchin, bc_ga_urchinOrder, bc_ga_jsEscapedString, bc_ga_formatNumericDecimal, bc_ga_xmlAttributeValue, bc_ga_script_url
type: help
---
# Operators of smaller extensions

Each extension below registers its operators in its own `autoloads/eztemplateautoload.php`; the operators exist only
while the extension is active. Five of them are found through `[TemplateSettings] ExtensionAutoloadPath[]`;
bcwebsitestatistics adds its folder to `AutoloadPathList[]` instead (its `settings/site.ini.append.php`).

| Extension | Class (in `extension/<name>/autoloads/`) | Operators |
|---|---|---|
| ezoe | `eZOETemplateUtils` (`ezoetemplateutils.php`) | `ezoe_ini_section` |
| ezwt | `eZCreateClassListGroups` (`ezcreateclasslistgroups.php`) | `ezcreateclasslistgroups` |
| enhancedezbinaryfile | `TemplateParseXMLOperator` (`templateparsexmloperator.php`) | `parsexml` |
| recaptcha | `reCAPTCHATemplateOperator` (`recaptchatemplateoperator.php`) | `recaptcha_get_html` |
| xrowmetadata | `xrowMetaDataOperator`, `xrowMetaDataLicenseOperator`, `xrowMetaDataLicensePickerOperator` | `metadata`, `xrowmetadata_license`, five `xrowmetadata_license_*` |
| bcwebsitestatistics | `BCWebsiteStatisticsOperators` (`bcwebsitestatisticsoperators.php`) | six `bc_ga_*` |

## Contents

1. [ezoe, ezwt, enhancedezbinaryfile](#ezoe-ezwt-enhancedezbinaryfile): [ezoe_ini_section](#ezoe_ini_section),
   [ezcreateclasslistgroups](#ezcreateclasslistgroups), [parsexml](#parsexml)
2. [recaptcha](#recaptcha): [recaptcha_get_html](#recaptcha_get_html)
3. [xrowmetadata](#xrowmetadata): [metadata](#metadata), [xrowmetadata_license](#xrowmetadata_license),
   [xrowmetadata_license_enabled](#xrowmetadata_license_enabled),
   [xrowmetadata_license_choices](#xrowmetadata_license_choices),
   [xrowmetadata_license_info](#xrowmetadata_license_info),
   [xrowmetadata_license_inherited](#xrowmetadata_license_inherited),
   [xrowmetadata_license_class_settings](#xrowmetadata_license_class_settings)
4. [bcwebsitestatistics](#bcwebsitestatistics): [bc_ga_urchin](#bc_ga_urchin), [bc_ga_urchinOrder](#bc_ga_urchinorder),
   [bc_ga_jsEscapedString](#bc_ga_jsescapedstring), [bc_ga_formatNumericDecimal](#bc_ga_formatnumericdecimal),
   [bc_ga_xmlAttributeValue](#bc_ga_xmlattributevalue), [bc_ga_script_url](#bc_ga_script_url)

## ezoe, ezwt, enhancedezbinaryfile

### ezoe_ini_section

Generated page: [ezoe_ini_section](../../examples/template-operators/ezoe_ini_section.md).

| Parameter | Type | Required | Default |
|---|---|---|---|
| `section` | string | yes | empty |
| `file` | string | no | `site.ini` |

No input. Returns `true` when the INI file has the group (`eZINI::hasSection()`). The online editor uses it in
`ezoe/generalattributes.tpl` and `ezoe/customattributes.tpl` before reading a group with `ezini`.

### ezcreateclasslistgroups

| Parameter | Type | Required | Default |
|---|---|---|---|
| `can_create_class_list` | array | yes | empty |

No input. Takes a list of hashes with `id` (the form of a node's `can_create_class_list`) and returns them grouped by
class group: group id => `group_name` and `items` (class objects). Classes in `websitetoolbar.ini`
`[WebsiteToolbarSettings] HiddenContentClasses[]` are left out. A class id that does not exist makes the operator
return `false` for the whole list. The website toolbar uses it for its "create here" menu.

### parsexml

| Parameter | Type | Required | Default |
|---|---|---|---|
| `first_param` | string | no | `default text` |

Input: an XML string (the stored data of an enhanced binary file). Returns the text of the first element named
`first_param`, or its `value` attribute when the element has no text. An empty input is returned unchanged; XML that
does not parse leaves no value (the input is replaced by nothing). The class also has a `filecheck` case, but the
autoload file registers only `parsexml`, so `filecheck` cannot be called from a template.

```tpl
1 ezoe_ini_section: {ezoe_ini_section( 'EditorSettings', 'ezoe.ini' )|choose( 'no', 'yes' )} / {ezoe_ini_section( 'NoSuchGroup' )|choose( 'no', 'yes' )}

{def $groups = ezcreateclasslistgroups( array( hash( 'id', 1 ), hash( 'id', 2 ) ) )}
2 ezcreateclasslistgroups: {foreach $groups as $id => $g}group {$id} "{$g.group_name}": {foreach $g.items as $c}{$c.identifier}{delimiter}, {/delimiter}{/foreach}{delimiter}; {/delimiter}{/foreach}

3 parsexml: {'<binaryfile><Filename>report.pdf</Filename><Size value="1024"/></binaryfile>'|parsexml( 'Filename' )} / {'<binaryfile><Size value="1024"/></binaryfile>'|parsexml( 'Size' )}
```

Output:

```text
1 ezoe_ini_section: yes / no
2 ezcreateclasslistgroups: group 1 "Content": folder, article
3 parsexml: report.pdf / 1024
```

## recaptcha

### recaptcha_get_html

No parameters, no input. Returns the reCAPTCHA widget markup for the public key of `recaptcha.ini` `[Keys] PublicKey`
(a list keyed by host name picks the current host, else the first entry), or the text "User bypasses CAPTCHA" when
the user has the policy `recaptcha/bypass_captcha`. It chooses the secure address when `$_SERVER['SERVER_PORT']` is
443.

With an empty public key the bundled `recaptcha_get_html()` (`extension/recaptcha/classes/recaptchalib.php`) calls
`die()` and **ends the request** with a message: configure the key before the operator reaches a template. The widget
loads a script from a third-party host, which a strict Content Security Policy must allow. In the example only the
fact that markup came back is printed, not the key:

```tpl
1 recaptcha_get_html: {recaptcha_get_html()|count_chars|gt( 0 )|choose( 'empty', 'markup returned' )}
2 xrowmetadata_license_enabled: {xrowmetadata_license_enabled()|choose( 'no', 'yes' )}

{def $choices = xrowmetadata_license_choices()}
3 xrowmetadata_license_choices: {$choices|count} groups{if $choices|count}, first: {$choices[0].label|wash} ({$choices[0].options|count} options){/if}


{def $mit = xrowmetadata_license_info( 'MIT' )}
4 xrowmetadata_license_info MIT: {if $mit}{$mit.name|wash}, url {$mit.url|wash}{else}false{/if}


{def $inh = xrowmetadata_license_inherited( '' )}
5 xrowmetadata_license_inherited: {if $inh}{$inh.id|wash} from {$inh.source}{else}false{/if}


{def $lic = xrowmetadata_license( 90 )}
6 xrowmetadata_license node 90: {if $lic}{$lic.id|wash} ({$lic.kind}), meta {$lic.meta_name}{else}false{/if}


7 metadata node 90: {metadata( 90 )|get_type}
```

Output:

```text
1 recaptcha_get_html: markup returned
2 xrowmetadata_license_enabled: yes
3 xrowmetadata_license_choices: 9 groups, first: Public domain (23 options)
4 xrowmetadata_license_info MIT: MIT License, url https://opensource.org/license/MIT
5 xrowmetadata_license_inherited: GPL-2.0-or-later from installation
6 xrowmetadata_license node 90: GPL-2.0-or-later (license), meta dcterms.license

7 metadata node 90: object[xrowMetaData]
```

## xrowmetadata

### metadata

| Parameter | Type | Required | Default |
|---|---|---|---|
| `node_id` | int | yes | `null` |

No input. Returns the `xrowMetaData` object stored for the node's content object (`xrowMetaDataFunctions::fetchByObject()`),
or `false`. Placeholders in its `title` and `description` are replaced: `[name]`, `[count]` (subtree size),
`[count:localbusiness]`, `[path]` (the path names in reverse, joined by ` | `), `[parentname]` and `[pagecount]`
(page n of m, from `[EditorInputSettings] MaxPageLimit` and the `offset` view parameter). Line 7 above.

### xrowmetadata_license

| Parameter | Type | Required | Default |
|---|---|---|---|
| `node` | mixed (node, object or node id) | no | `null` (or the input) |

Returns the license that applies to the node (`xrowMetaDataLicense::resolve()`; without a node the current one), as a
hash with `id`, `name`, `url` (only `http`/`https`, validated), `type`, `source`, `holder`, `year`, `attribution`,
`groups`, `kind` (`license`, `dedication`, `mark` or `reserved`), `page_url`, `show` (`link_rel`, `meta`, `json_ld`
from `[LicenseSettings]`), `meta_name`, `meta_content` and ready JSON-LD (`json_ld_value`, `json_ld_property`,
`json_ld_node`, `json_ld_script`). `false` when licensing is disabled or nothing applies. Line 6 above.

### xrowmetadata_license_enabled

No parameters. `true` unless `xrowmetadata.ini` `[LicenseSettings] Enabled` says otherwise. Line 2.

### xrowmetadata_license_choices

Parameter `selected` (string, default empty). The license list for an edit form, grouped (each group: `key`,
`label`, `options`), cached per locale. Line 3.

### xrowmetadata_license_info

Parameter `id` (string). The catalogue entry of one license id, or `false`. Line 4.

### xrowmetadata_license_inherited

Parameter `class_default` (string). The license a new object would inherit: the class default, else the
installation default, with `source` set to `class` or `installation`. Line 5.

### xrowmetadata_license_class_settings

Parameter `class_attribute` (object, required). For a class attribute of the metadata datatype, its
`default_license` and `allow_object_license`; for anything else the defaults `''` and `true`. It needs a class
attribute object and is used in the class edit templates; it is not in the example.

## bcwebsitestatistics

Four of these operator names contain capital letters. The template parser recognizes a tag that **starts** with an
operator call only when the name matches `[a-z0-9_-]+(` (`eZTemplateMultiPassParser`), so
`{bc_ga_jsEscapedString( ... )}` as a tag of its own is read as an unknown **function** and fails with "Function
"bc_ga_jsEscapedString" is not registered". Inside an expression (a `def`, a parameter, after `|`) the names work:

```tpl
{def $escaped = bc_ga_jsEscapedString( concat( "It's ", '"quoted"' ) )
     $price = bc_ga_formatNumericDecimal( 12.5, 2 )}
1 bc_ga_jsEscapedString: {$escaped}
2 bc_ga_formatNumericDecimal: {$price} / {''|bc_ga_formatNumericDecimal( 0, 2 )}
3 bc_ga_urchin: {bc_ga_urchin()|count_chars} characters
```

Output:

```text
1 bc_ga_jsEscapedString: It\'s '+String.fromCharCode(34)+'quoted'+String.fromCharCode(34)+'
2 bc_ga_formatNumericDecimal: 12.50 / 0.00
3 bc_ga_urchin: 225 characters
```

### bc_ga_urchin

No parameters. With `bcwebsitestatistics.ini` `[BCWebsiteStatisticsSettings] PageSubmit=enabled` and an account in
`Urchin`, it returns the analytics script tags (the tag manager script with the account id, or `Script`/`SecureScript`,
plus the extension's `bcwebsitestatistics_gtag.js` with `data-bc-ga-id`); with `OrderSubmit=enabled` also the order
tracking script. Line 3 above prints only its length, not the account id.

### bc_ga_urchinOrder

Parameter `order` (an order object). Builds the hidden order tracking form with the order number, totals, the
customer's city, state and country and the product lines. Not run on a development installation: it needs a real order and prints customer
data.

### bc_ga_jsEscapedString

Parameter `string`. Escapes single quotes as `\'` and replaces double quotes with `'+String.fromCharCode(34)+'` for a
single-quoted JavaScript string; `false` for an empty string. It is not a general JavaScript or HTML escaper. Line 1.

### bc_ga_formatNumericDecimal

Parameters `number` and `places`. Returns the number with two decimals (`number_format( $n, 2, '.', '' )`; `places`
is ignored); zero gives `0.00`. Line 2.

### bc_ga_xmlAttributeValue

Parameters `name` and `data`. Returns the text of the first element `name` in the XML `data` (through `eZXML`), or
`false`. Used internally for the order address; not in the example.

### bc_ga_script_url

Input: JavaScript code. Returns an address for it: through `ezjscPacker::generatedContentURL()` when ezjscore offers
it, otherwise it **writes** `bcwebsitestatistics_<md5>.js` into the public cache folder. Not run on a development installation because it
writes a file.

## Related pages

- [URL, design, INI and HTTP operators](url-operators.md) (`ezini` for single settings)
- [Content security policy for applications](../creators/app-creators/content-security-policy.md)
- [All template operators (generated)](../../examples/template-operators/README.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../partners/partners-start-here.md) and [How to apply](../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
