---
title: Operator reference
audience: designer, developer
category: templates
order: 320
summary: Every template operator of the template library and the kernel, grouped by the file that registers it, with a one-line purpose, plus the operators the main design-related extensions add, and how to list the operators of your own installation.
keywords: operator reference, all operators, template operators list, eztemplateautoload, kernel operators, library operators, extension operators, operator names
type: help
---
# Operator reference

This page lists the template operators of Exponential by where they are registered. Purpose lines are short; the
family pages linked at the end explain the important ones with examples.

## Contents

1. [The template library](#the-template-library)
2. [The kernel](#the-kernel)
3. [PHP functions by setting](#php-functions-by-setting)
4. [Design-related extensions](#design-related-extensions)
5. [Listing the operators of your installation](#listing-the-operators-of-your-installation)

## The template library

Registered in `lib/eztemplate/classes/eztemplateautoload.php`:

| Operators | Class | Purpose |
|---|---|---|
| `array`, `hash` | eZTemplateArrayOperator | build an array or a hash |
| `append`, `prepend`, `merge` (older: `array_append`, `array_prepend`, `array_merge`) | eZTemplateArrayOperator | add items, join arrays |
| `contains`, `compare`, `begins_with`, `ends_with` | eZTemplateArrayOperator | tests on arrays and strings |
| `extract`, `extract_left`, `extract_right`, `reverse`, `unique`, `insert`, `remove`, `replace`, `repeat` | eZTemplateArrayOperator | take parts, change |
| `implode`, `explode`, `array_sum` | eZTemplateArrayOperator | join, split, sum |
| `fetch`, `fetch_alias` | eZTemplateExecuteOperator | call a fetch function ([Fetch basics](fetch-basics.md)) |
| `l10n`, `locale`, `datetime`, `currentdate`, `maketime`, `makedate`, `gettime` | eZTemplateLocaleOperator | locale formatting and dates |
| `attribute`, `dump` | eZTemplateAttributeOperator | show a value's attributes (debugging) |
| `nl2br` | eZTemplateNl2BrOperator | line breaks to `<br>` |
| `concat`, `indent` | eZTemplateTextOperator | join texts, indent |
| `si` | eZTemplateUnitOperator | numbers with unit prefixes (sizes) |
| `lt`, `gt`, `le`, `ge`, `eq`, `ne`, `null`, `not`, `true`, `false`, `or`, `and`, `choose` | eZTemplateLogicOperator | comparisons and logic |
| `is_array`, `is_boolean`, `is_integer`, `is_float`, `is_numeric`, `is_string`, `is_object`, `is_class`, `is_null`, `is_set`, `is_unset`, `get_type`, `get_class` | eZTemplateTypeOperator | type tests |
| `cond`, `first_set` | eZTemplateControlOperator | choose a value |
| `sum`, `sub`, `inc`, `dec`, `div`, `mod`, `mul`, `max`, `min`, `abs`, `ceil`, `floor`, `round`, `int`, `float`, `count`, `roman`, `rand` | eZTemplateArithmeticOperator | arithmetic |
| `texttoimage`, `image`, `imagefile` | eZTemplateImageOperator | text rendered as images (legacy) |
| `upcase`, `downcase`, `count_words`, `count_chars`, `trim`, `break`, `wrap`, `upfirst`, `upword`, `simplify`, `wash`, `chr`, `ord`, `shorten`, `pad` | eZTemplateStringOperator | text shaping and escaping |
| `crc32`, `md5`, `rot13` | eZTemplateDigestOperator | checksums |
| `expinfo` | eZTemplateExpInfoOperator | metadata of extensions: `expinfo()` the active ones, `expinfo( 'all' )`, `expinfo( '<name>' )` |
| `strlen`, `substr`, `sprintf`, `str_contains`, `number_format`, `strip_tags` and about fifty more | eZTemplateStringsOperator | PHP string functions ([PHP string functions as operators](string-template-operators.md)) |

## The kernel

Registered in `kernel/common/eztemplateautoload.php`:

| Operators | Purpose |
|---|---|
| `ezurl`, `ezroot`, `ezdesign`, `ezimage`, `exturl`, `ezsys` | URLs and design files ([URL operators](url-operators.md)) |
| `ezhttp`, `ezhttp_hasvariable` | request values ([The ezhttp operator](ezhttp-operator.md)) |
| `ezini`, `ezini_hasvariable` | settings ([The ezini operator](ezini-operator.md)) |
| `i18n`, `x18n`, `d18n` | translation ([The i18n operator](i18n-operator.md)) |
| `alphabet` | the letters for A to Z filters, from `content.ini [AlphabeticalFilterSettings]` |
| `month_overview` | data for a month calendar |
| `autolink` | turn URLs in text into links (`template.ini [AutoLinkOperator]`) |
| `simpletags` | simple markup to HTML (`template.ini [SimpleTagsOperator]`) |
| `treemenu`, `content_structure_tree`, `topmenu` | menu and tree data |
| `wordtoimage`, `mimetype_icon`, `class_icon`, `classgroup_icon`, `action_icon`, `icon`, `flag_icon`, `icon_info` | icons of the icon themes |
| `ezpreference` | a preference of the current user |
| `siteaccess_url`, `siteaccess_menu`, `siteaccess_menu_key` | links to other siteaccesses (language switchers) |
| PHP functions of `template.ini [PHP] PHPOperatorList[]` | see below |
| `module_params` | module and view of the current request |
| `ezpackage` | package information |
| `eztoc` | a table of contents of rich text |
| `ezmodule` | module information (see `doc/en/us/features/3.10/ezmodule_template_operator.txt`) |
| `installation_name`, `is_production_system` | the installation's name and whether it is production |
| `audit_label` | labels of the audit log |
| `exp_environment`, `exp_environment_badge` | the environment (development, staging, production) and its badge |
| `csp_nonce` | the Content Security Policy nonce of the request ([CSP-safe design](../theme-design/csp-safe-design.md)) |

`kernel/private/eztemplate/eztemplateautoload.php` adds `language_switcher`.

## PHP functions by setting

`settings/template.ini [PHP] PHPOperatorList[]` exposes PHP functions under operator names; shipped: `urlencode`,
`urldecode`, `rawurlencode`, `rawurldecode`.

## Design-related extensions

Available only where the extension is active:

| Extension | Operators |
|---|---|
| ezjscore | `ezscript`, `ezscript_require`, `ezscript_load`, `ezscriptfiles`, `ezcss`, `ezcss_require`, `ezcss_load`, `ezcssfiles`, `json_encode`, `xml_encode`, `node_encode`, `has_access_to_limitation` ([ezjscore template operators](ezjscore-template-operators.md)) |
| ezwebin | `ezpagedata`, `ezpagedata_set`, `ezpagedata_append`, `ezkeywordlist`, `ezarchive`, `eztagcloud` |
| explayouts | `expl_first`, `expl_has`, `expl_parse`, `expl_strip`; function `explblock` |
| eztags | `eztags_parent_string`, `eztagscloud`, `latest_tags`, `tag_icon`, `user_limitations` |
| ezstarrating | `fetch_by_starrating`, `fetch_starrating_data`, `fetch_starrating_stats` |
| ezoe | `ezoe_ini_section` |
| expui | `exp_config` |
| sevenx_themes_media | 54 operators of the media theme (`intro`, `title`, `path`, `content_link`, `image`, `ng_image_alias`, `site_url`, `json_ld` ...), see its `doc/USAGE.md` |

## Listing the operators of your installation

On a development installation, the operators registered for a siteaccess are the keys of `eZTemplate::factory()->Operators`;
a developer can print them with a small script run through `php bin/php/ezexec.php`. Extension operators appear
only when the extension is active for that siteaccess and listed in `[TemplateSettings] ExtensionAutoloadPath[]`.

## Related pages

- [Operators overview](operators-overview.md)
- [Function reference](function-reference.md)
- [Free documentation](../../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
