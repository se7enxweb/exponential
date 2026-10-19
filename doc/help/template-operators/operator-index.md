---
title: Operator index
audience: developer, designer, app-creator
category: development
order: 10
summary: All 400 template operators registered on alpha's site and admin siteaccesses, A to Z, each with the family page that documents its parameters, return value and examples, the class that serves it and where that class comes from.
keywords: operator index, template operators, A-Z, all operators, operator list, which class, extension operators
type: help
---
# Operator index

Every template operator that the template engine registers on alpha's `site` and `admin` siteaccesses, read from
the engine itself (`eZTemplate::factory()` with all its autoload files, then every operator object loaded and
asked for its parameters). 400 names; 5 of them only on `admin`. The class is the one that serves the
name after the autoload files are read; for names that two classes register, see
[Registration order and overrides](registration-order-and-overrides.md#the-names-registered-twice-on-alpha).

"Documented in" leads to the operator's section on its family page, with parameters, return value and examples
rendered on alpha; "generated" to the page that `./console exp:help:examples` generates from the code, with the
first use in a shipped template. Operators of an extension exist only where that extension is active.

## A

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `abs` ([generated](../../examples/template-operators/abs.md)) | [Arithmetic operators](arithmetic-operators.md#abs-ceil-floor-round-int-and-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `absolute_url` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `action_icon` ([generated](../../examples/template-operators/action_icon.md)) | [Icon operators](icon-operators.md#action_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `add_view_parameters` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#add_view_parameters) | `SwarkAddViewParametersOperator` (extension swark) | site, admin |
| `addcslashes` ([generated](../../examples/template-operators/addcslashes.md)) | [PHP string functions: transform](php-string-functions-transform.md#slashes-addslashes-stripslashes-addcslashes-stripcslashes-and-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `addslashes` ([generated](../../examples/template-operators/addslashes.md)) | [PHP string functions: transform](php-string-functions-transform.md#slashes-addslashes-stripslashes-addcslashes-stripcslashes-and-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `alphabet` ([generated](../../examples/template-operators/alphabet.md)) | [Text helper operators of the kernel](kernel-text-helper-operators.md#alphabet) | `eZAlphabetOperator` (kernel/common) | site, admin |
| `and` ([generated](../../examples/template-operators/and.md)) | [Logic operators](logic-operators.md#and-and-or) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `app` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `append` ([generated](../../examples/template-operators/append.md)) | [Array operators](array-operators.md#adding-append-prepend-and-merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array` ([generated](../../examples/template-operators/array.md)) | [Array operators](array-operators.md#building-array-and-hash) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array_append` ([generated](../../examples/template-operators/array_append.md)) | [Array operators](array-operators.md#deprecated-names-array_append-array_prepend-and-array_merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array_merge` ([generated](../../examples/template-operators/array_merge.md)) | [Array operators](array-operators.md#deprecated-names-array_append-array_prepend-and-array_merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array_prepend` ([generated](../../examples/template-operators/array_prepend.md)) | [Array operators](array-operators.md#deprecated-names-array_append-array_prepend-and-array_merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array_search` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#array_search) | `SwarkArraySearchOperator` (extension swark) | site, admin |
| `array_sum` ([generated](../../examples/template-operators/array_sum.md)) | [Array operators](array-operators.md#array_sum) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `arsort` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#arsort) | `SwarkARSortOperator` (extension swark) | site, admin |
| `asort` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#asort) | `SwarkASortOperator` (extension swark) | site, admin |
| `asset` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `attribute` ([generated](../../examples/template-operators/attribute.md)) | [Attribute and dump operators](attribute-and-dump-operators.md#attribute) | `eZTemplateAttributeOperator` (lib/eztemplate/classes) | site, admin |
| `audit_label` ([generated](../../examples/template-operators/audit_label.md)) | [Installation, environment, audit and CSP operators](exponential-kernel-operators.md#audit_label) | `expAuditTemplateOperator` (kernel/classes/audit/console) | site, admin |
| `autolink` ([generated](../../examples/template-operators/autolink.md)) | [Text helper operators of the kernel](kernel-text-helper-operators.md#autolink) | `eZAutoLinkOperator` (kernel/common) | site, admin |

## B

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `bc_ga_formatNumericDecimal` | [Operators of smaller extensions](small-extension-operators.md#bc_ga_formatnumericdecimal) | `BCWebsiteStatisticsOperators` (extension bcwebsitestatistics) | site, admin |
| `bc_ga_jsEscapedString` | [Operators of smaller extensions](small-extension-operators.md#bc_ga_jsescapedstring) | `BCWebsiteStatisticsOperators` (extension bcwebsitestatistics) | site, admin |
| `bc_ga_script_url` | [Operators of smaller extensions](small-extension-operators.md#bc_ga_script_url) | `BCWebsiteStatisticsOperators` (extension bcwebsitestatistics) | site, admin |
| `bc_ga_urchin` | [Operators of smaller extensions](small-extension-operators.md#bc_ga_urchin) | `BCWebsiteStatisticsOperators` (extension bcwebsitestatistics) | site, admin |
| `bc_ga_urchinOrder` | [Operators of smaller extensions](small-extension-operators.md#bc_ga_urchinorder) | `BCWebsiteStatisticsOperators` (extension bcwebsitestatistics) | site, admin |
| `bc_ga_xmlAttributeValue` | [Operators of smaller extensions](small-extension-operators.md#bc_ga_xmlattributevalue) | `BCWebsiteStatisticsOperators` (extension bcwebsitestatistics) | site, admin |
| `begins_with` ([generated](../../examples/template-operators/begins_with.md)) | [Array operators](array-operators.md#testing-contains-compare-begins_with-and-ends_with) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `bin2hex` ([generated](../../examples/template-operators/bin2hex.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#bin2hex-and-hex2bin) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `break` ([generated](../../examples/template-operators/break.md)) | [String operators](string-operators.md#line-breaks-wrap-and-break) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |

## C

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `ceil` ([generated](../../examples/template-operators/ceil.md)) | [Arithmetic operators](arithmetic-operators.md#abs-ceil-floor-round-int-and-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `charset` | [Swark operators: request and system](swark-operators-request-and-system.md#charset) | `SwarkCharsetOperator` (extension swark) | site, admin |
| `choose` ([generated](../../examples/template-operators/choose.md)) | [Logic operators](logic-operators.md#choose) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `chr` ([generated](../../examples/template-operators/chr.md)) | [String operators](string-operators.md#code-points-ord-and-chr) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `chunk_split` ([generated](../../examples/template-operators/chunk_split.md)) | [PHP string functions: transform](php-string-functions-transform.md#trimming-and-case-ltrim-rtrim-lcfirst-and-chunk_split) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `cjw_newsletter_preg_replace` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjw_newsletter_preg_replace) | `CjwNewsletterOperators` (extension cjw_newsletter) | site, admin |
| `cjw_newsletter_str_replace` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjw_newsletter_str_replace) | `CjwNewsletterOperators` (extension cjw_newsletter) | site, admin |
| `cjw_newsletter_variable` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjw_newsletter_variable) | `CjwNewsletterOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_abs_url` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_abs_url) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_condition_close` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_condition_close) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_condition_else` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_condition_else) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_condition_open` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_condition_open) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_interests_block` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_interests_block) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_plaintext` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_plaintext) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_rendering_data` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_rendering_data) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_richtext` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_richtext) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_text_underline` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_text_underline) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `cjwnl_text_wrap` | [Newsletter operators of cjw_newsletter](newsletter-operators.md#cjwnl_text_wrap) | `CjwNewsletterRenderingOperators` (extension cjw_newsletter) | site, admin |
| `class_icon` ([generated](../../examples/template-operators/class_icon.md)) | [Icon operators](icon-operators.md#class_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `classgroup_icon` ([generated](../../examples/template-operators/classgroup_icon.md)) | [Icon operators](icon-operators.md#classgroup_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `clear_object_cache` | [Swark operators: request and system](swark-operators-request-and-system.md#clear_object_cache) | `SwarkClearObjectCacheOperator` (extension swark) | site, admin |
| `compare` ([generated](../../examples/template-operators/compare.md)) | [Array operators](array-operators.md#testing-contains-compare-begins_with-and-ends_with) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `component_content` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `concat` ([generated](../../examples/template-operators/concat.md)) | [Text operators](text-operators.md#concat) | `eZTemplateTextOperator` (lib/eztemplate/classes) | site, admin |
| `cond` ([generated](../../examples/template-operators/cond.md)) | [Control operators](control-operators.md#cond) | `eZTemplateControlOperator` (lib/eztemplate/classes) | site, admin |
| `contains` ([generated](../../examples/template-operators/contains.md)) | [Array operators](array-operators.md#testing-contains-compare-begins_with-and-ends_with) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `content_link` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `content_structure_tree` ([generated](../../examples/template-operators/content_structure_tree.md)) | [Menu and tree operators](menu-and-tree-operators.md#content_structure_tree) | `eZContentStructureTreeOperator` (kernel/common) | site, admin |
| `content_tags` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `controller` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `convert_uudecode` ([generated](../../examples/template-operators/convert_uudecode.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#convert_uuencode-and-convert_uudecode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `convert_uuencode` ([generated](../../examples/template-operators/convert_uuencode.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#convert_uuencode-and-convert_uudecode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `cookie` | [Swark operators: request and system](swark-operators-request-and-system.md#cookie) | `SwarkCookieOperator` (extension swark) | site, admin |
| `count` ([generated](../../examples/template-operators/count.md)) | [Arithmetic operators](arithmetic-operators.md#count) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `count_chars` ([generated](../../examples/template-operators/count_chars.md)) | [String operators](string-operators.md#counting-count_words-and-count_chars) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `count_words` ([generated](../../examples/template-operators/count_words.md)) | [String operators](string-operators.md#counting-count_words-and-count_chars) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `crc32` ([generated](../../examples/template-operators/crc32.md)) | [Digest operators](digest-operators.md#crc32) | `eZTemplateDigestOperator` (lib/eztemplate/classes) | site, admin |
| `csp_nonce` ([generated](../../examples/template-operators/csp_nonce.md)) | [Installation, environment, audit and CSP operators](exponential-kernel-operators.md#csp_nonce) | `expContentSecurityPolicyOperator` (kernel/common) | site, admin |
| `current_layout` | [Swark operators: request and system](swark-operators-request-and-system.md#current_layout) | `SwarkCurrentLayoutOperator` (extension swark) | site, admin |
| `current_siteaccess` | [Swark operators: request and system](swark-operators-request-and-system.md#current_siteaccess) | `SwarkCurrentSiteaccessOperator` (extension swark) | site, admin |
| `currentdate` ([generated](../../examples/template-operators/currentdate.md)) | [Locale operators](locale-operators.md#currentdate) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |

## D

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `d18n` ([generated](../../examples/template-operators/d18n.md)) | [Translation operators i18n, d18n and x18n](i18n-operators.md#d18n) | `eZi18nOperator` (kernel/common) | site, admin |
| `datetime` ([generated](../../examples/template-operators/datetime.md)) | [Locale operators](locale-operators.md#datetime) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `debug` | [Swark operators: request and system](swark-operators-request-and-system.md#debug) | `SwarkDebugOperator` (extension swark) | site, admin |
| `debug_attributes` | [Swark operators: request and system](swark-operators-request-and-system.md#debug_attributes) | `SwarkDebugAttributesOperator` (extension swark) | site, admin |
| `dec` ([generated](../../examples/template-operators/dec.md)) | [Arithmetic operators](arithmetic-operators.md#inc-and-dec) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `div` ([generated](../../examples/template-operators/div.md)) | [Arithmetic operators](arithmetic-operators.md#mul-div-and-mod) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `downcase` ([generated](../../examples/template-operators/downcase.md)) | [String operators](string-operators.md#case-upcase-downcase-upfirst-and-upword) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `dump` ([generated](../../examples/template-operators/dump.md)) | [Attribute and dump operators](attribute-and-dump-operators.md#dump) | `eZTemplateAttributeOperator` (lib/eztemplate/classes) | site, admin |

## E

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `embed_image` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ends_with` ([generated](../../examples/template-operators/ends_with.md)) | [Array operators](array-operators.md#testing-contains-compare-begins_with-and-ends_with) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `enhanced_link` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `eq` ([generated](../../examples/template-operators/eq.md)) | [Logic operators](logic-operators.md#eq-and-ne) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `exp_can_login_to_siteaccess` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `exp_config` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#exp_config) | `expUITemplateOperators` (extension expui) | site, admin |
| `exp_environment` ([generated](../../examples/template-operators/exp_environment.md)) | [Installation, environment, audit and CSP operators](exponential-kernel-operators.md#exp_environment) | `expEnvironmentOperator` (kernel/common) | site, admin |
| `exp_environment_badge` ([generated](../../examples/template-operators/exp_environment_badge.md)) | [Installation, environment, audit and CSP operators](exponential-kernel-operators.md#exp_environment_badge) | `expEnvironmentOperator` (kernel/common) | site, admin |
| `exp_path` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `exp_url` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `expai_json` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expai_json) | `expAiTemplateOperators` (extension expai) | admin |
| `expai_money` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expai_money) | `expAiTemplateOperators` (extension expai) | admin |
| `expai_number` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expai_number) | `expAiTemplateOperators` (extension expai) | admin |
| `expai_retention` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expai_retention) | `expAiTemplateOperators` (extension expai) | admin |
| `expai_tokens` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expai_tokens) | `expAiTemplateOperators` (extension expai) | admin |
| `expinfo` ([generated](../../examples/template-operators/expinfo.md)) | [The expinfo operator](expinfo-operator.md#expinfo) | `eZTemplateExpInfoOperator` (lib/eztemplate/classes) | site, admin |
| `expl_first` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expl_first) | `ExplBlockOperator` (extension explayouts) | site, admin |
| `expl_has` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expl_has) | `ExplBlockOperator` (extension explayouts) | site, admin |
| `expl_parse` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expl_parse) | `ExplBlockOperator` (extension explayouts) | site, admin |
| `expl_strip` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#expl_strip) | `ExplBlockOperator` (extension explayouts) | site, admin |
| `explayouts_render_result` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `explayouts_render_zone` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `explayouts_ui_has_template` | [Layouts, expui and expai operators](layouts-and-expui-operators.md#explayouts_ui_has_template) | `expLayoutsUiTemplateOperators` (extension explayouts_ui) | site, admin |
| `explode` ([generated](../../examples/template-operators/explode.md)) | [Array operators](array-operators.md#joining-and-splitting-implode-and-explode) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `exponential` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `extract` ([generated](../../examples/template-operators/extract.md)) | [Array operators](array-operators.md#slicing-extract-extract_left-and-extract_right) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `extract_left` ([generated](../../examples/template-operators/extract_left.md)) | [Array operators](array-operators.md#slicing-extract-extract_left-and-extract_right) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `extract_right` ([generated](../../examples/template-operators/extract_right.md)) | [Array operators](array-operators.md#slicing-extract-extract_left-and-extract_right) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `exturl` ([generated](../../examples/template-operators/exturl.md)) | [URL, design, INI and HTTP operators](url-operators.md#exturl) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezarchive` | [ezwebin operators](ezwebin-operators.md#ezarchive) | `eZArchive` (extension ezwebin) | site, admin |
| `ezcreateclasslistgroups` | [Operators of smaller extensions](small-extension-operators.md#ezcreateclasslistgroups) | `eZCreateClassListGroups` (extension ezwt) | site, admin |
| `ezcss` ([generated](../../examples/template-operators/ezcss.md)) | [ezjscore operators in depth](ezjscore-operators.md#ezcss) | `ezjscPackerTemplateFunctions` (extension ezjscore) | site, admin |
| `ezcss_load` ([generated](../../examples/template-operators/ezcss_load.md)) | [ezjscore operators in depth](ezjscore-operators.md#ezcss_load) | `ezjscPackerTemplateFunctions` (extension ezjscore) | site, admin |
| `ezcss_require` ([generated](../../examples/template-operators/ezcss_require.md)) | [ezjscore operators in depth](ezjscore-operators.md#ezcss_require) | `ezjscPackerTemplateFunctions` (extension ezjscore) | site, admin |
| `ezcssfiles` ([generated](../../examples/template-operators/ezcssfiles.md)) | [ezjscore operators in depth](ezjscore-operators.md#ezcssfiles) | `ezjscPackerTemplateFunctions` (extension ezjscore) | site, admin |
| `ezdesign` ([generated](../../examples/template-operators/ezdesign.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezdesign) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezhttp` ([generated](../../examples/template-operators/ezhttp.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezhttp) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezhttp_hasvariable` ([generated](../../examples/template-operators/ezhttp_hasvariable.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezhttp_hasvariable) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezimage` ([generated](../../examples/template-operators/ezimage.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezimage) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezini` ([generated](../../examples/template-operators/ezini.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezini) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezini_hasvariable` ([generated](../../examples/template-operators/ezini_hasvariable.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezini_hasvariable) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezkeywordlist` | [ezwebin operators](ezwebin-operators.md#ezkeywordlist) | `eZKeywordList` (extension ezwebin) | site, admin |
| `ezmodule` ([generated](../../examples/template-operators/ezmodule.md)) | [Preference, module, package and PDF operators](kernel-misc-operators.md#ezmodule) | `eZModuleOperator` (kernel/common) | site, admin |
| `ezoe_ini_section` ([generated](../../examples/template-operators/ezoe_ini_section.md)) | [Operators of smaller extensions](small-extension-operators.md#ezoe_ini_section) | `eZOETemplateUtils` (extension ezoe) | site, admin |
| `ezpackage` ([generated](../../examples/template-operators/ezpackage.md)) | [Preference, module, package and PDF operators](kernel-misc-operators.md#ezpackage) | `eZPackageOperator` (kernel/common) | site, admin |
| `ezpagedata` | [ezwebin operators](ezwebin-operators.md#ezpagedata) | `eZPageData` (extension ezwebin) | site, admin |
| `ezpagedata_append` | [ezwebin operators](ezwebin-operators.md#ezpagedata_append) | `eZPageData` (extension ezwebin) | site, admin |
| `ezpagedata_set` | [ezwebin operators](ezwebin-operators.md#ezpagedata_set) | `eZPageData` (extension ezwebin) | site, admin |
| `ezpreference` ([generated](../../examples/template-operators/ezpreference.md)) | [Preference, module, package and PDF operators](kernel-misc-operators.md#ezpreference) | `eZKernelOperator` (kernel/common) | site, admin |
| `ezroot` ([generated](../../examples/template-operators/ezroot.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezroot) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezscript` ([generated](../../examples/template-operators/ezscript.md)) | [ezjscore operators in depth](ezjscore-operators.md#ezscript) | `ezjscPackerTemplateFunctions` (extension ezjscore) | site, admin |
| `ezscript_load` ([generated](../../examples/template-operators/ezscript_load.md)) | [ezjscore operators in depth](ezjscore-operators.md#ezscript_load) | `ezjscPackerTemplateFunctions` (extension ezjscore) | site, admin |
| `ezscript_require` ([generated](../../examples/template-operators/ezscript_require.md)) | [ezjscore operators in depth](ezjscore-operators.md#ezscript_require) | `ezjscPackerTemplateFunctions` (extension ezjscore) | site, admin |
| `ezscriptfiles` ([generated](../../examples/template-operators/ezscriptfiles.md)) | [ezjscore operators in depth](ezjscore-operators.md#ezscriptfiles) | `ezjscPackerTemplateFunctions` (extension ezjscore) | site, admin |
| `ezsys` ([generated](../../examples/template-operators/ezsys.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezsys) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `eztagcloud` | [ezwebin operators](ezwebin-operators.md#eztagcloud) | `eZTagCloud` (extension ezwebin) | site, admin |
| `eztags_parent_string` | [eztags and star rating operators](eztags-and-starrating-operators.md#eztags_parent_string) | `eZTagsTemplateFunctions` (extension eztags) | site, admin |
| `eztagscloud` | [eztags and star rating operators](eztags-and-starrating-operators.md#eztagscloud) | `eZTagsCloud` (extension eztags) | site, admin |
| `eztoc` ([generated](../../examples/template-operators/eztoc.md)) | [Text helper operators of the kernel](kernel-text-helper-operators.md#eztoc) | `eZTOCOperator` (kernel/common) | site, admin |
| `ezurl` ([generated](../../examples/template-operators/ezurl.md)) | [URL, design, INI and HTTP operators](url-operators.md#ezurl) | `eZURLMultisiteURLOperator` (extension ezurlmultisite) | site, admin |
| `ezurl_path` | [URL, design, INI and HTTP operators](url-operators.md#ezurl_path) | `eZURLMultisiteOperator` (extension ezurlmultisite) | site, admin |
| `ezurl_roots` | [URL, design, INI and HTTP operators](url-operators.md#ezurl_roots) | `eZURLMultisiteOperator` (extension ezurlmultisite) | site, admin |
| `ezurl_siteaccess` | [URL, design, INI and HTTP operators](url-operators.md#ezurl_siteaccess) | `eZURLMultisiteOperator` (extension ezurlmultisite) | site, admin |

## F

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `false` ([generated](../../examples/template-operators/false.md)) | [Logic operators](logic-operators.md#not-null-true-and-false) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `fetch` ([generated](../../examples/template-operators/fetch.md)) | [The fetch and fetch_alias operators](execute-operators.md#fetch) | `eZTemplateExecuteOperator` (lib/eztemplate/classes) | site, admin |
| `fetch_alias` ([generated](../../examples/template-operators/fetch_alias.md)) | [The fetch and fetch_alias operators](execute-operators.md#fetch_alias) | `eZTemplateExecuteOperator` (lib/eztemplate/classes) | site, admin |
| `fetch_by_starrating` | [eztags and star rating operators](eztags-and-starrating-operators.md#fetch_by_starrating) | `ezsrTemplateOperators` (extension ezstarrating) | site, admin |
| `fetch_starrating_data` | [eztags and star rating operators](eztags-and-starrating-operators.md#fetch_starrating_data) | `ezsrTemplateOperators` (extension ezstarrating) | site, admin |
| `fetch_starrating_stats` | [eztags and star rating operators](eztags-and-starrating-operators.md#fetch_starrating_stats) | `ezsrTemplateOperators` (extension ezstarrating) | site, admin |
| `fieldRelation` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `fieldRelations` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `fieldValue` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `filterChildren` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `filterFieldRelationLocations` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `filterFieldRelations` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `first_set` ([generated](../../examples/template-operators/first_set.md)) | [Control operators](control-operators.md#first_set) | `eZTemplateControlOperator` (lib/eztemplate/classes) | site, admin |
| `firstNonEmptyField` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `flag_icon` ([generated](../../examples/template-operators/flag_icon.md)) | [Icon operators](icon-operators.md#flag_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `float` ([generated](../../examples/template-operators/float.md)) | [Arithmetic operators](arithmetic-operators.md#abs-ceil-floor-round-int-and-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `floor` ([generated](../../examples/template-operators/floor.md)) | [Arithmetic operators](arithmetic-operators.md#abs-ceil-floor-round-int-and-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |

## G

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `ge` ([generated](../../examples/template-operators/ge.md)) | [Logic operators](logic-operators.md#lt-gt-le-and-ge) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `get_class` ([generated](../../examples/template-operators/get_class.md)) | [Type operators](type-operators.md#get_type-and-get_class) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `get_netgen_open_graph` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `get_type` ([generated](../../examples/template-operators/get_type.md)) | [Type operators](type-operators.md#get_type-and-get_class) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `getdate` | [OW Simple Operator - PHP functions as template operators](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (extension owsimpleoperator) | site, admin |
| `getenv` | [PHP function operators](php-function-operators.md#getenv) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `getParameter` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `gettime` ([generated](../../examples/template-operators/gettime.md)) | [Locale operators](locale-operators.md#maketime-makedate-and-gettime) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `gt` ([generated](../../examples/template-operators/gt.md)) | [Logic operators](logic-operators.md#lt-gt-le-and-ge) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |

## H

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `has_access_to_limitation` ([generated](../../examples/template-operators/has_access_to_limitation.md)) | [ezjscore operators in depth](ezjscore-operators.md#has_access_to_limitation) | `ezjscAccessTemplateFunctions` (extension ezjscore) | site, admin |
| `has_any_role` ([generated](../../examples/template-operators/has_any_role.md)) | [Role operators](role-operators.md#has_role-has_role_id-and-has_any_role) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_policy` ([generated](../../examples/template-operators/has_policy.md)) | [Role operators](role-operators.md#has_policy-and-has_policy_by_user) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_policy_by_user` ([generated](../../examples/template-operators/has_policy_by_user.md)) | [Role operators](role-operators.md#has_policy-and-has_policy_by_user) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_role` ([generated](../../examples/template-operators/has_role.md)) | [Role operators](role-operators.md#has_role-has_role_id-and-has_any_role) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_role_by_user` ([generated](../../examples/template-operators/has_role_by_user.md)) | [Role operators](role-operators.md#has_role_by_user-and-has_role_by_user_id) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_role_by_user_id` ([generated](../../examples/template-operators/has_role_by_user_id.md)) | [Role operators](role-operators.md#has_role_by_user-and-has_role_by_user_id) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_role_id` ([generated](../../examples/template-operators/has_role_id.md)) | [Role operators](role-operators.md#has_role-has_role_id-and-has_any_role) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `hasField` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `hash` ([generated](../../examples/template-operators/hash.md)) | [Array operators](array-operators.md#building-array-and-hash) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `hasParameter` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `haveToPaginate` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `hebrev` ([generated](../../examples/template-operators/hebrev.md)) | [PHP string functions: transform](php-string-functions-transform.md#str_shuffle-and-hebrev) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `hex2bin` ([generated](../../examples/template-operators/hex2bin.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#bin2hex-and-hex2bin) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `html_entity_decode` ([generated](../../examples/template-operators/html_entity_decode.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#html_entity_decode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `htmlentities` ([generated](../../examples/template-operators/htmlentities.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#htmlentities) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `htmlspecialchars_decode` ([generated](../../examples/template-operators/htmlspecialchars_decode.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#htmlspecialchars_decode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## I

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `i18n` ([generated](../../examples/template-operators/i18n.md)) | [Translation operators i18n, d18n and x18n](i18n-operators.md#i18n) | `eZi18nOperator` (kernel/common) | site, admin |
| `ibexa` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ibexa_path` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ibexa_url` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `icon` ([generated](../../examples/template-operators/icon.md)) | [Icon operators](icon-operators.md#icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `icon_info` ([generated](../../examples/template-operators/icon_info.md)) | [Icon operators](icon-operators.md#icon_info) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `image` ([generated](../../examples/template-operators/image.md)) | [Image operators](image-operators.md#image) | `eZTemplateImageOperator` (lib/eztemplate/classes) | site, admin |
| `image_link` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `imagefile` ([generated](../../examples/template-operators/imagefile.md)) | [Image operators](image-operators.md#imagefile) | `eZTemplateImageOperator` (lib/eztemplate/classes) | site, admin |
| `implode` ([generated](../../examples/template-operators/implode.md)) | [Array operators](array-operators.md#joining-and-splitting-implode-and-explode) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `inc` ([generated](../../examples/template-operators/inc.md)) | [Arithmetic operators](arithmetic-operators.md#inc-and-dec) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `indent` ([generated](../../examples/template-operators/indent.md)) | [Text operators](text-operators.md#indent) | `eZTemplateTextOperator` (lib/eztemplate/classes) | site, admin |
| `insert` ([generated](../../examples/template-operators/insert.md)) | [Array operators](array-operators.md#editing-insert-remove-and-replace) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `installation_name` ([generated](../../examples/template-operators/installation_name.md)) | [Installation, environment, audit and CSP operators](exponential-kernel-operators.md#installation_name) | `ExpInstallationOperator` (kernel/classes) | site, admin |
| `int` ([generated](../../examples/template-operators/int.md)) | [Arithmetic operators](arithmetic-operators.md#abs-ceil-floor-round-int-and-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `intro` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `is_array` ([generated](../../examples/template-operators/is_array.md)) | [Type operators](type-operators.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_boolean` ([generated](../../examples/template-operators/is_boolean.md)) | [Type operators](type-operators.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_class` ([generated](../../examples/template-operators/is_class.md)) | [Type operators](type-operators.md#is_class) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_float` ([generated](../../examples/template-operators/is_float.md)) | [Type operators](type-operators.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_integer` ([generated](../../examples/template-operators/is_integer.md)) | [Type operators](type-operators.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_null` ([generated](../../examples/template-operators/is_null.md)) | [Type operators](type-operators.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_numeric` ([generated](../../examples/template-operators/is_numeric.md)) | [Type operators](type-operators.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_object` ([generated](../../examples/template-operators/is_object.md)) | [Type operators](type-operators.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_post_request` | [Swark operators: request and system](swark-operators-request-and-system.md#is_post_request) | `SwarkIsPostRequestOperator` (extension swark) | site, admin |
| `is_production_system` ([generated](../../examples/template-operators/is_production_system.md)) | [Installation, environment, audit and CSP operators](exponential-kernel-operators.md#is_production_system) | `ExpInstallationOperator` (kernel/classes) | site, admin |
| `is_set` ([generated](../../examples/template-operators/is_set.md)) | [Type operators](type-operators.md#is_set-and-is_unset) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_string` ([generated](../../examples/template-operators/is_string.md)) | [Type operators](type-operators.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_unset` ([generated](../../examples/template-operators/is_unset.md)) | [Type operators](type-operators.md#is_set-and-is_unset) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `item_content_link` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `item_image_link` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `item_params` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `item_view_template` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |

## J

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `json_encode` ([generated](../../examples/template-operators/json_encode.md)) | [ezjscore operators in depth](ezjscore-operators.md#json_encode) | `ezjscEncodingTemplateFunctions` (extension ezjscore) | site, admin |
| `json_ld` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |

## K

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `krsort` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#krsort) | `SwarkKRSortOperator` (extension swark) | site, admin |
| `ksort` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#ksort) | `SwarkKSortOperator` (extension swark) | site, admin |

## L

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `l10n` ([generated](../../examples/template-operators/l10n.md)) | [Locale operators](locale-operators.md#l10n) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `language_switcher` ([generated](../../examples/template-operators/language_switcher.md)) | [Siteaccess operators](siteaccess-operators.md#language_switcher) | `ezpLanguageSwitcherOperator` (kernel/private/eztemplate) | site, admin |
| `latest_tags` | [eztags and star rating operators](eztags-and-starrating-operators.md#latest_tags) | `eZTagsTemplateFunctions` (extension eztags) | site, admin |
| `layout_title` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `lcfirst` ([generated](../../examples/template-operators/lcfirst.md)) | [PHP string functions: transform](php-string-functions-transform.md#trimming-and-case-ltrim-rtrim-lcfirst-and-chunk_split) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `le` ([generated](../../examples/template-operators/le.md)) | [Logic operators](logic-operators.md#lt-gt-le-and-ge) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `levenshtein` ([generated](../../examples/template-operators/levenshtein.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#levenshtein) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `locale` ([generated](../../examples/template-operators/locale.md)) | [Locale operators](locale-operators.md#locale) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `lt` ([generated](../../examples/template-operators/lt.md)) | [Logic operators](logic-operators.md#lt-gt-le-and-ge) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `ltrim` ([generated](../../examples/template-operators/ltrim.md)) | [PHP string functions: transform](php-string-functions-transform.md#trimming-and-case-ltrim-rtrim-lcfirst-and-chunk_split) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## M

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `makedate` ([generated](../../examples/template-operators/makedate.md)) | [Locale operators](locale-operators.md#maketime-makedate-and-gettime) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `maketime` ([generated](../../examples/template-operators/maketime.md)) | [Locale operators](locale-operators.md#maketime-makedate-and-gettime) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `max` ([generated](../../examples/template-operators/max.md)) | [Arithmetic operators](arithmetic-operators.md#min-and-max) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `md5` ([generated](../../examples/template-operators/md5.md)) | [Digest operators](digest-operators.md#md5) | `eZTemplateDigestOperator` (lib/eztemplate/classes) | site, admin |
| `merge` ([generated](../../examples/template-operators/merge.md)) | [Array operators](array-operators.md#adding-append-prepend-and-merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `metadata` | [Operators of smaller extensions](small-extension-operators.md#metadata) | `xrowMetaDataOperator` (extension xrowmetadata) | site, admin |
| `metaphone` ([generated](../../examples/template-operators/metaphone.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#soundex-and-metaphone) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `mimetype_icon` ([generated](../../examples/template-operators/mimetype_icon.md)) | [Icon operators](icon-operators.md#mimetype_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `min` ([generated](../../examples/template-operators/min.md)) | [Arithmetic operators](arithmetic-operators.md#min-and-max) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `mktime` | [OW Simple Operator - PHP functions as template operators](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (extension owsimpleoperator) | site, admin |
| `mod` ([generated](../../examples/template-operators/mod.md)) | [Arithmetic operators](arithmetic-operators.md#mul-div-and-mod) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `modify_view_parameter` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#modify_view_parameter) | `SwarkModifyViewParameterOperator` (extension swark) | site, admin |
| `module_params` ([generated](../../examples/template-operators/module_params.md)) | [Preference, module, package and PDF operators](kernel-misc-operators.md#module_params) | `eZModuleParamsOperator` (kernel/common) | site, admin |
| `month_overview` ([generated](../../examples/template-operators/month_overview.md)) | [Text helper operators of the kernel](kernel-text-helper-operators.md#month_overview) | `eZDateOperatorCollection` (kernel/common) | site, admin |
| `mul` ([generated](../../examples/template-operators/mul.md)) | [Arithmetic operators](arithmetic-operators.md#mul-div-and-mod) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |

## N

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `ne` ([generated](../../examples/template-operators/ne.md)) | [Logic operators](logic-operators.md#eq-and-ne) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `ng_image_alias` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ng_query` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ng_render_field` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ng_view_content` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `nglayouts_render_result` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `nglayouts_render_zone` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ngsite` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ngsite_group_fields` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ngsite_language_name` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `ngsite_topic_path` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `nl2br` ([generated](../../examples/template-operators/nl2br.md)) | [PHP function operators](php-function-operators.md#nl2br) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `node_encode` ([generated](../../examples/template-operators/node_encode.md)) | [ezjscore operators in depth](ezjscore-operators.md#node_encode) | `ezjscEncodingTemplateFunctions` (extension ezjscore) | site, admin |
| `not` ([generated](../../examples/template-operators/not.md)) | [Logic operators](logic-operators.md#not-null-true-and-false) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `null` ([generated](../../examples/template-operators/null.md)) | [Logic operators](logic-operators.md#not-null-true-and-false) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `number_format` ([generated](../../examples/template-operators/number_format.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#number_format) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## O

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `or` ([generated](../../examples/template-operators/or.md)) | [Logic operators](logic-operators.md#and-and-or) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `ord` ([generated](../../examples/template-operators/ord.md)) | [String operators](string-operators.md#code-points-ord-and-chr) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |

## P

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `pad` ([generated](../../examples/template-operators/pad.md)) | [String operators](string-operators.md#cleaning-trim-simplify-and-pad) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `pagerfanta` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `parameter` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `parent` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `parsexml` | [Operators of smaller extensions](small-extension-operators.md#parsexml) | `TemplateParseXMLOperator` (extension enhancedezbinaryfile) | site, admin |
| `path` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `pdf` ([generated](../../examples/template-operators/pdf.md)) | [Preference, module, package and PDF operators](kernel-misc-operators.md#pdf) | `eZPDF` (lib/ezpdf/classes) | site, admin |
| `player` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `player_slide` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `poster` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `poster_slide` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `preg_match` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#preg_match) | `SwarkPregMatchOperator` (extension swark) | site, admin |
| `preg_replace` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#preg_replace) | `SwarkPregReplaceOperator` (extension swark) | site, admin |
| `prepend` ([generated](../../examples/template-operators/prepend.md)) | [Array operators](array-operators.md#adding-append-prepend-and-merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |

## Q

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `quoted_printable_decode` ([generated](../../examples/template-operators/quoted_printable_decode.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#quoted_printable_encode-and-quoted_printable_decode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `quoted_printable_encode` ([generated](../../examples/template-operators/quoted_printable_encode.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#quoted_printable_encode-and-quoted_printable_decode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `quotemeta` ([generated](../../examples/template-operators/quotemeta.md)) | [PHP string functions: transform](php-string-functions-transform.md#slashes-addslashes-stripslashes-addcslashes-stripcslashes-and-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## R

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `rand` ([generated](../../examples/template-operators/rand.md)) | [Arithmetic operators](arithmetic-operators.md#roman-and-rand) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `range` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#range) | `SwarkRangeOperator` (extension swark) | site, admin |
| `rawurldecode` ([generated](../../examples/template-operators/rawurldecode.md)) | [PHP function operators](php-function-operators.md#urlencode-urldecode-rawurlencode-rawurldecode) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `rawurlencode` ([generated](../../examples/template-operators/rawurlencode.md)) | [PHP function operators](php-function-operators.md#urlencode-urldecode-rawurlencode-rawurldecode) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `recaptcha_get_html` | [Operators of smaller extensions](small-extension-operators.md#recaptcha_get_html) | `reCAPTCHATemplateOperator` (extension recaptcha) | site, admin |
| `recipe_schema` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `redirect` | [Swark operators: request and system](swark-operators-request-and-system.md#redirect) | `SwarkRedirectOperator` (extension swark) | site, admin |
| `redirect_to_site_root` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `remove` ([generated](../../examples/template-operators/remove.md)) | [Array operators](array-operators.md#editing-insert-remove-and-replace) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `remove_array_element` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#remove_array_element) | `SwarkRemoveArrayElementOperator` (extension swark) | site, admin |
| `render` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `render_esi` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `repeat` ([generated](../../examples/template-operators/repeat.md)) | [Array operators](array-operators.md#reshaping-repeat-reverse-and-unique) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `replace` ([generated](../../examples/template-operators/replace.md)) | [Array operators](array-operators.md#editing-insert-remove-and-replace) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `return` | [Swark operators: request and system](swark-operators-request-and-system.md#return) | `SwarkReturnOperator` (extension swark) | site, admin |
| `reverse` ([generated](../../examples/template-operators/reverse.md)) | [Array operators](array-operators.md#reshaping-repeat-reverse-and-unique) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `ristring` ([generated](../../examples/template-operators/ristring.md)) | [PHP string functions: transform](php-string-functions-transform.md#replacing-ristring-rstring-and-strtr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `roman` ([generated](../../examples/template-operators/roman.md)) | [Arithmetic operators](arithmetic-operators.md#roman-and-rand) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `rot13` ([generated](../../examples/template-operators/rot13.md)) | [Digest operators](digest-operators.md#rot13) | `eZTemplateDigestOperator` (lib/eztemplate/classes) | site, admin |
| `round` ([generated](../../examples/template-operators/round.md)) | [Arithmetic operators](arithmetic-operators.md#abs-ceil-floor-round-int-and-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `rsort` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#rsort) | `SwarkRSortOperator` (extension swark) | site, admin |
| `rstring` ([generated](../../examples/template-operators/rstring.md)) | [PHP string functions: transform](php-string-functions-transform.md#replacing-ristring-rstring-and-strtr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `rtrim` ([generated](../../examples/template-operators/rtrim.md)) | [PHP string functions: transform](php-string-functions-transform.md#trimming-and-case-ltrim-rtrim-lcfirst-and-chunk_split) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## S

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `saveXML` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `serialize` | [PHP function operators](php-function-operators.md#serialize-and-unserialize) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `server` | [Swark operators: request and system](swark-operators-request-and-system.md#server) | `SwarkServerOperator` (extension swark) | site, admin |
| `set_array_element` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#set_array_element) | `SwarkSetArrayElementOperator` (extension swark) | site, admin |
| `shorten` ([generated](../../examples/template-operators/shorten.md)) | [String operators](string-operators.md#shorten) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `shortenw` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#shortenw) | `SwarkShortenWOperator` (extension swark) | site, admin |
| `shuffle` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#shuffle) | `SwarkShuffleOperator` (extension swark) | site, admin |
| `si` ([generated](../../examples/template-operators/si.md)) | [The si unit operator](unit-operator.md#si) | `eZTemplateUnitOperator` (lib/eztemplate/classes) | site, admin |
| `similar_text` ([generated](../../examples/template-operators/similar_text.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#similar_text) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `simpletags` ([generated](../../examples/template-operators/simpletags.md)) | [Text helper operators of the kernel](kernel-text-helper-operators.md#simpletags) | `eZSimpleTagsOperator` (kernel/common) | site, admin |
| `simplify` ([generated](../../examples/template-operators/simplify.md)) | [String operators](string-operators.md#cleaning-trim-simplify-and-pad) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `site_url` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `siteaccess_href` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `siteaccess_menu` ([generated](../../examples/template-operators/siteaccess_menu.md)) | [Siteaccess operators](siteaccess-operators.md#siteaccess_menu) | `ezpSiteAccessMenuOperator` (kernel/common) | site, admin |
| `siteaccess_menu_key` ([generated](../../examples/template-operators/siteaccess_menu_key.md)) | [Siteaccess operators](siteaccess-operators.md#siteaccess_menu_key) | `ezpSiteAccessMenuOperator` (kernel/common) | site, admin |
| `siteaccess_url` ([generated](../../examples/template-operators/siteaccess_url.md)) | [Siteaccess operators](siteaccess-operators.md#siteaccess_url) | `ezpSiteAccessURLOperator` (kernel/common) | site, admin |
| `sort` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#sort) | `SwarkSortOperator` (extension swark) | site, admin |
| `soundex` ([generated](../../examples/template-operators/soundex.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#soundex-and-metaphone) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `split_by_length` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#split_by_length) | `SwarkSplitByLengthOperator` (extension swark) | site, admin |
| `sprintf` ([generated](../../examples/template-operators/sprintf.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#sprintf-and-vsprintf) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_contains` ([generated](../../examples/template-operators/str_contains.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#str_contains-str_starts_with-str_ends_with) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_ends_with` ([generated](../../examples/template-operators/str_ends_with.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#str_contains-str_starts_with-str_ends_with) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_getcsv` ([generated](../../examples/template-operators/str_getcsv.md)) | [PHP string functions: transform](php-string-functions-transform.md#splitting-str_split-str_word_count-str_getcsv-and-strtok) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_replace` | [OW Simple Operator - PHP functions as template operators](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (extension owsimpleoperator) | site, admin |
| `str_rot13` | [OW Simple Operator - PHP functions as template operators](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (extension owsimpleoperator) | site, admin |
| `str_shuffle` ([generated](../../examples/template-operators/str_shuffle.md)) | [PHP string functions: transform](php-string-functions-transform.md#str_shuffle-and-hebrev) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_split` ([generated](../../examples/template-operators/str_split.md)) | [PHP string functions: transform](php-string-functions-transform.md#splitting-str_split-str_word_count-str_getcsv-and-strtok) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_starts_with` ([generated](../../examples/template-operators/str_starts_with.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#str_contains-str_starts_with-str_ends_with) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_word_count` ([generated](../../examples/template-operators/str_word_count.md)) | [PHP string functions: transform](php-string-functions-transform.md#splitting-str_split-str_word_count-str_getcsv-and-strtok) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strcasecmp` ([generated](../../examples/template-operators/strcasecmp.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strcmp-and-strcasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strcmp` ([generated](../../examples/template-operators/strcmp.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strcmp-and-strcasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strcoll` ([generated](../../examples/template-operators/strcoll.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strcoll) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strcspn` ([generated](../../examples/template-operators/strcspn.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strspn-and-strcspn) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strip_tags` ([generated](../../examples/template-operators/strip_tags.md)) | [PHP function operators](php-function-operators.md#strip_tags) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `stripcslashes` ([generated](../../examples/template-operators/stripcslashes.md)) | [PHP string functions: transform](php-string-functions-transform.md#slashes-addslashes-stripslashes-addcslashes-stripcslashes-and-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `stripos` ([generated](../../examples/template-operators/stripos.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strpos-stripos-strrpos-strripos) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `stripslashes` ([generated](../../examples/template-operators/stripslashes.md)) | [PHP string functions: transform](php-string-functions-transform.md#slashes-addslashes-stripslashes-addcslashes-stripcslashes-and-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `stristr` ([generated](../../examples/template-operators/stristr.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strstr-and-stristr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strlen` ([generated](../../examples/template-operators/strlen.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strlen) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strnatcasecmp` ([generated](../../examples/template-operators/strnatcasecmp.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strnatcmp-and-strnatcasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strnatcmp` ([generated](../../examples/template-operators/strnatcmp.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strnatcmp-and-strnatcasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strncasecmp` ([generated](../../examples/template-operators/strncasecmp.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strncmp-and-strncasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strncmp` ([generated](../../examples/template-operators/strncmp.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strncmp-and-strncasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strpbrk` ([generated](../../examples/template-operators/strpbrk.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strpbrk) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strpos` ([generated](../../examples/template-operators/strpos.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strpos-stripos-strrpos-strripos) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strrchr` ([generated](../../examples/template-operators/strrchr.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strrchr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strripos` ([generated](../../examples/template-operators/strripos.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strpos-stripos-strrpos-strripos) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strrpos` ([generated](../../examples/template-operators/strrpos.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strpos-stripos-strrpos-strripos) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strspn` ([generated](../../examples/template-operators/strspn.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strspn-and-strcspn) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strstr` ([generated](../../examples/template-operators/strstr.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#strstr-and-stristr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strtok` ([generated](../../examples/template-operators/strtok.md)) | [PHP string functions: transform](php-string-functions-transform.md#splitting-str_split-str_word_count-str_getcsv-and-strtok) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strtr` ([generated](../../examples/template-operators/strtr.md)) | [PHP string functions: transform](php-string-functions-transform.md#replacing-ristring-rstring-and-strtr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `sub` ([generated](../../examples/template-operators/sub.md)) | [Arithmetic operators](arithmetic-operators.md#sum-and-sub) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `substr` ([generated](../../examples/template-operators/substr.md)) | [PHP string functions: transform](php-string-functions-transform.md#cutting-substr-and-substr_replace) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `substr_compare` ([generated](../../examples/template-operators/substr_compare.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#substr_compare) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `substr_count` ([generated](../../examples/template-operators/substr_count.md)) | [PHP string functions: search and compare](php-string-functions-search-and-compare.md#substr_count) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `substr_replace` ([generated](../../examples/template-operators/substr_replace.md)) | [PHP string functions: transform](php-string-functions-transform.md#cutting-substr-and-substr_replace) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `sum` ([generated](../../examples/template-operators/sum.md)) | [Arithmetic operators](arithmetic-operators.md#sum-and-sub) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |

## T

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `tag_icon` | [eztags and star rating operators](eztags-and-starrating-operators.md#tag_icon) | `eZTagsTemplateFunctions` (extension eztags) | site, admin |
| `tag_url` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `texttoimage` ([generated](../../examples/template-operators/texttoimage.md)) | [Image operators](image-operators.md#texttoimage) | `eZTemplateImageOperator` (lib/eztemplate/classes) | site, admin |
| `time` | [OW Simple Operator - PHP functions as template operators](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (extension owsimpleoperator) | site, admin |
| `title` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `topmenu` ([generated](../../examples/template-operators/topmenu.md)) | [Menu and tree operators](menu-and-tree-operators.md#topmenu) | `eZTopMenuOperator` (kernel/common) | site, admin |
| `tpl_block_template` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `trans` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `treemenu` ([generated](../../examples/template-operators/treemenu.md)) | [Menu and tree operators](menu-and-tree-operators.md#treemenu) | `eZTreeMenuOperator` (kernel/common) | site, admin |
| `trim` ([generated](../../examples/template-operators/trim.md)) | [String operators](string-operators.md#cleaning-trim-simplify-and-pad) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `true` ([generated](../../examples/template-operators/true.md)) | [Logic operators](logic-operators.md#not-null-true-and-false) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |

## U

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `unique` ([generated](../../examples/template-operators/unique.md)) | [Array operators](array-operators.md#reshaping-repeat-reverse-and-unique) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `unserialize` | [PHP function operators](php-function-operators.md#serialize-and-unserialize) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `upcase` ([generated](../../examples/template-operators/upcase.md)) | [String operators](string-operators.md#case-upcase-downcase-upfirst-and-upword) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `upfirst` ([generated](../../examples/template-operators/upfirst.md)) | [String operators](string-operators.md#case-upcase-downcase-upfirst-and-upword) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `upword` ([generated](../../examples/template-operators/upword.md)) | [String operators](string-operators.md#case-upcase-downcase-upfirst-and-upword) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `uri_path_segment` | [Swark operators: arrays, strings and URLs](swark-operators-arrays-and-strings.md#uri_path_segment) | `SwarkURIPathSegmentOperator` (extension swark) | site, admin |
| `urldecode` ([generated](../../examples/template-operators/urldecode.md)) | [PHP function operators](php-function-operators.md#urlencode-urldecode-rawurlencode-rawurldecode) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `urlencode` ([generated](../../examples/template-operators/urlencode.md)) | [PHP function operators](php-function-operators.md#urlencode-urldecode-rawurlencode-rawurldecode) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `user_id_by_login` | [Swark operators: request and system](swark-operators-request-and-system.md#user_id_by_login) | `SwarkUserIDByLoginOperator` (extension swark) | site, admin |
| `user_limitations` | [eztags and star rating operators](eztags-and-starrating-operators.md#user_limitations) | `eZTagsTemplateFunctions` (extension eztags) | site, admin |

## V

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `variable_names` | [Swark operators: request and system](swark-operators-request-and-system.md#variable_names) | `SwarkVariableNamesOperator` (extension swark) | site, admin |
| `video_thumbnail` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |
| `vsprintf` ([generated](../../examples/template-operators/vsprintf.md)) | [PHP string functions: encode and format](php-string-functions-encode-and-format.md#sprintf-and-vsprintf) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## W

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `wash` ([generated](../../examples/template-operators/wash.md)) | [String operators](string-operators.md#wash) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `wordtoimage` ([generated](../../examples/template-operators/wordtoimage.md)) | [Icon operators](icon-operators.md#wordtoimage) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `wrap` ([generated](../../examples/template-operators/wrap.md)) | [String operators](string-operators.md#line-breaks-wrap-and-break) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |

## X

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `x18n` ([generated](../../examples/template-operators/x18n.md)) | [Translation operators i18n, d18n and x18n](i18n-operators.md#x18n) | `eZi18nOperator` (kernel/common) | site, admin |
| `xml_encode` ([generated](../../examples/template-operators/xml_encode.md)) | [ezjscore operators in depth](ezjscore-operators.md#xml_encode) | `ezjscEncodingTemplateFunctions` (extension ezjscore) | site, admin |
| `xrowmetadata_license` | [Operators of smaller extensions](small-extension-operators.md#xrowmetadata_license) | `xrowMetaDataLicenseOperator` (extension xrowmetadata) | site, admin |
| `xrowmetadata_license_choices` | [Operators of smaller extensions](small-extension-operators.md#xrowmetadata_license_choices) | `xrowMetaDataLicensePickerOperator` (extension xrowmetadata) | site, admin |
| `xrowmetadata_license_class_settings` | [Operators of smaller extensions](small-extension-operators.md#xrowmetadata_license_class_settings) | `xrowMetaDataLicensePickerOperator` (extension xrowmetadata) | site, admin |
| `xrowmetadata_license_enabled` | [Operators of smaller extensions](small-extension-operators.md#xrowmetadata_license_enabled) | `xrowMetaDataLicensePickerOperator` (extension xrowmetadata) | site, admin |
| `xrowmetadata_license_info` | [Operators of smaller extensions](small-extension-operators.md#xrowmetadata_license_info) | `xrowMetaDataLicensePickerOperator` (extension xrowmetadata) | site, admin |
| `xrowmetadata_license_inherited` | [Operators of smaller extensions](small-extension-operators.md#xrowmetadata_license_inherited) | `xrowMetaDataLicensePickerOperator` (extension xrowmetadata) | site, admin |

## Y

| Operator | Documented in | Class | Siteaccesses |
|---|---|---|---|
| `youtube_id` |  | `sevenxThemesMediaOperators` (extension sevenx_themes_media) | site, admin |

## Related pages

- [Template operators](README.md)
- [Operator reference for designers](../designers/templates/operator-reference.md)
- [Free documentation](../free-documentation.md): this page is free to copy, translate and improve under the GNU FDL 1.3.
