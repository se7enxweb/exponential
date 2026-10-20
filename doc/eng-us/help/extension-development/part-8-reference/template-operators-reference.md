---
title: Built-in template operators
audience: developer, designer
category: extension-development
order: 270
summary: The template operators of an installation, grouped by implementing class - strings, arrays, logic, arithmetic, types, dates, URLs, settings, translation, icons, access, encoding, the packer.
keywords: template operators, wash, i18n, ezurl, ezdesign, ezimage, ezini, cond, first_set, count, explode, implode, datetime, l10n, json_encode, has_access_to_limitation, ezcss_load, ezscript_load, operator list
type: help
---
# Built-in template operators

Part 8, reference 23. This page lists the operators of a running Exponential 6 installation, grouped by the PHP class
that implements them. The list was produced by asking the template engine of a demo installation for every
registered operator (October 2026), so it is what templates can call, not what an old manual said. Extensions add
their own operators; those appear only when the extension is active. How each operator is used, with examples, is
in the designer help ([Operators overview](../../designers/templates/operators-overview.md) and
[Operator reference](../../designers/templates/operator-reference.md)).

## Strings (eZTemplateStringOperator)

`break`, `chr`, `count_chars`, `count_words`, `downcase`, `ord`, `pad`, `shorten`, `simplify`, `trim`, `upcase`,
`upfirst`, `upword`, `wash`, `wrap`

`wash` escapes a value for HTML (or, with a parameter, for `javascript`, `email` and other targets). Every value that
did not come from your own template text goes through it.

## More string functions (eZTemplateStringsOperator)

PHP's string functions under their own names, among them `sprintf`, `number_format`, `str_contains`,
`str_starts_with`, `str_ends_with`, `strlen`, `substr_count`, `substr_replace`, `strtr`, `stripos`, `lcfirst`,
`html_entity_decode`, `htmlentities`, `levenshtein`, `similar_text`, `soundex`, `metaphone`, `str_split`,
`str_word_count`, `chunk_split`, `bin2hex`, `hex2bin`, `quoted_printable_encode` and the comparison functions
(`strcmp`, `strcasecmp`, `strnatcmp` ...). Also `nl2br`, `strip_tags`, `urlencode`, `rawurlencode`, `urldecode`,
`serialize`, `unserialize` and `getenv`.

## Arrays (eZTemplateArrayOperator)

`append`, `array`, `array_append`, `array_merge`, `array_prepend`, `array_sum`, `begins_with`, `compare`, `contains`,
`ends_with`, `explode`, `extract`, `extract_left`, `extract_right`, `hash`, `implode`, `insert`, `merge`, `prepend`,
`remove`, `repeat`, `replace`, `reverse`, `unique`

## Logic and control (eZTemplateLogicOperator, eZTemplateControlOperator)

`and`, `or`, `not`, `eq`, `ne`, `lt`, `le`, `gt`, `ge`, `choose`, `true`, `false`, `null`; `cond`, `first_set`

## Arithmetic (eZTemplateArithmeticOperator)

`sum`, `sub`, `mul`, `div`, `mod`, `inc`, `dec`, `abs`, `ceil`, `floor`, `round`, `int`, `float`, `min`, `max`,
`count`, `rand`, `roman`

## Types (eZTemplateTypeOperator)

`is_array`, `is_boolean`, `is_integer`, `is_float`, `is_numeric`, `is_string`, `is_object`, `is_class`, `is_null`,
`is_set`, `is_unset`, `get_type`, `get_class`

## Dates and locale (eZTemplateLocaleOperator)

`l10n`, `locale`, `datetime`, `currentdate`, `maketime`, `makedate`, `gettime`

## URLs, designs, settings, request (the URL operator class)

`ezurl`, `ezroot`, `ezdesign`, `ezimage`, `exturl`, `ezsys`, `ezini`, `ezini_hasvariable`, `ezhttp`,
`ezhttp_hasvariable`; for multisite installations also `siteaccess_url`. `ezurl` and the image and design operators
print their result quoted unless given `'no'`.

## Translation (eZi18nOperator)

`i18n`, `x18n`, `d18n`: `{'Text'|i18n( 'extension/myext' )}` with your extension's context.

## Icons (eZWordToImageOperator)

`icon`, `icon_info`, `class_icon`, `classgroup_icon`, `action_icon`, `mimetype_icon`, `flag_icon`, `wordtoimage`

## Access (eZTemplateRoleOperator, ezjscore)

`has_role`, `has_role_id`, `has_any_role`, `has_role_by_user`, `has_role_by_user_id`, `has_policy`,
`has_policy_by_user`, `has_access_to_limitation`. For "may the current user do X", prefer the fetch function
`fetch( 'user', 'has_access_to', ... )` ([eZUser and access checks](ezuser-and-access.md)).

## Encoding and the packer (ezjscore)

`json_encode`, `node_encode`, `xml_encode`; `ezcss`, `ezcss_load`, `ezcss_require`, `ezcssfiles`, `ezscript`,
`ezscript_load`, `ezscript_require`, `ezscriptfiles` ([Theme assets, the packer and CSP](../part-6-theme-developers/theme-assets-the-packer-and-csp.md)).

## Other kernel operators

`fetch`, `fetch_alias` (fetch functions); `attribute`, `dump` (inspect a value while debugging); `concat`, `indent`;
`si` (units); `crc32`, `md5`, `rot13`; `texttoimage`, `imagefile`; `expinfo` (installation and extension
information); `ezpreference`; `ezmodule`, `module_params`; `topmenu`, `treemenu`, `content_structure_tree`;
`eztoc`, `autolink`, `simpletags`, `alphabet`, `month_overview`, `ezpackage`; `installation_name`,
`is_production_system`, `exp_environment`, `exp_environment_badge`; `csp_nonce` (the Content Security Policy's
nonce, for the rare inline script that must stay).

## Operators of shipped extensions

Some operators that templates use often come from extensions shipped with the kernel, and exist only when those are
active: `ezpagedata`, `ezpagedata_set`, `ezpagedata_append`, `ezkeywordlist` and `ezarchive` (ezwebin and ezdemo),
`ezcreateclasslistgroups` (ezwt), `exp_config` (expui), and the ezjscore operators above.

## Listing them yourself

The list differs per installation, because extensions add operators. To see yours, render a template with the
debug output on and an unknown operator: the error names the operator. Or ask the **Extension point survey**
(`/setup/radsurvey`), which lists the operators each active extension registers.

## Related pages

- Previous: [Cache tags, cache blocks and clearing caches](cache-tags-and-clearing.md)
- Next: [Built-in template functions](template-functions-reference.md)
- [Template operators](../part-3-extension-developer/template-operators.md)
- [Template operators builders use most](../part-2-builder/template-operators-builders-use-most.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.

<!-- exp-partner-block:start -->
**Become an Exponential Partner.** Partners share their information, provide funding, contribute back and
develop the products with us, and that is how Exponential grows. A partner website and dashboard are planned for
mid-2027: please become a partner today to speed up our growth rate and land new features faster. Start with
[Partners: start here](../../partners/partners-start-here.md) and [How to apply](../../partners/how-to-apply.md).
<!-- exp-partner-block:end -->
