---
title: Operatorenverzeichnis
audience: developer, designer, app-creator
category: development
order: 10
summary: Alle 400 Template-Operatoren, die auf den Siteaccesses site und admin von alpha registriert sind, von A bis Z, jeweils mit der Seite ihrer Familie, die Parameter, Rückgabewert und Beispiele dokumentiert, der Klasse, die den Operator bereitstellt, und der Herkunft dieser Klasse.
keywords: Operatorenverzeichnis, Template-Operatoren, A-Z, alle Operatoren, Liste der Operatoren, welche Klasse, Operatoren von Erweiterungen, operator index, template operators, all operators, operator list, extension operators
type: help
---
# Operatorenverzeichnis

Alle Template-Operatoren, die die Template-Engine auf den Siteaccesses `site` und `admin` von alpha registriert, aus
der Engine selbst gelesen (`eZTemplate::factory()` mit allen Autoload-Dateien, danach jedes Operatorobjekt geladen
und nach seinen Parametern gefragt). 400 Namen, davon 5 nur auf `admin`. Angegeben ist die Klasse, die den Namen
bereitstellt, nachdem die Autoload-Dateien gelesen sind; zu Namen, die zwei Klassen registrieren, siehe
[Registrierungsreihenfolge und Überschreibungen](registrierungsreihenfolge-und-ueberschreibungen.md#die-auf-alpha-doppelt-registrierten-namen).

"Dokumentiert in" führt zum Abschnitt des Operators auf der Seite seiner Familie, mit Parametern, Rückgabewert und
auf alpha gerenderten Beispielen; "generiert" zu der Seite, die `./console exp:help:examples` aus dem Code erzeugt,
mit der ersten Verwendung in einem mitgelieferten Template. Operatoren einer Erweiterung gibt es nur dort, wo diese
Erweiterung aktiv ist.

## A

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `abs` ([generiert](../../../eng-us/examples/template-operators/abs.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#abs-ceil-floor-round-int-und-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `absolute_url` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `action_icon` ([generiert](../../../eng-us/examples/template-operators/action_icon.md)) | [Icon-Operatoren](icon-operatoren.md#action_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `add_view_parameters` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#add_view_parameters) | `SwarkAddViewParametersOperator` (Erweiterung swark) | site, admin |
| `addcslashes` ([generiert](../../../eng-us/examples/template-operators/addcslashes.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#backslashes-addslashes-stripslashes-addcslashes-stripcslashes-und-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `addslashes` ([generiert](../../../eng-us/examples/template-operators/addslashes.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#backslashes-addslashes-stripslashes-addcslashes-stripcslashes-und-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `alphabet` ([generiert](../../../eng-us/examples/template-operators/alphabet.md)) | [Text-Hilfsoperatoren des Kernels](text-hilfsoperatoren-des-kernels.md#alphabet) | `eZAlphabetOperator` (kernel/common) | site, admin |
| `and` ([generiert](../../../eng-us/examples/template-operators/and.md)) | [Logische Operatoren](logische-operatoren.md#and-und-or) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `app` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `append` ([generiert](../../../eng-us/examples/template-operators/append.md)) | [Array-Operatoren](array-operatoren.md#hinzufügen-append-prepend-und-merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array` ([generiert](../../../eng-us/examples/template-operators/array.md)) | [Array-Operatoren](array-operatoren.md#aufbauen-array-und-hash) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array_append` ([generiert](../../../eng-us/examples/template-operators/array_append.md)) | [Array-Operatoren](array-operatoren.md#veraltete-namen-array_append-array_prepend-und-array_merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array_merge` ([generiert](../../../eng-us/examples/template-operators/array_merge.md)) | [Array-Operatoren](array-operatoren.md#veraltete-namen-array_append-array_prepend-und-array_merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array_prepend` ([generiert](../../../eng-us/examples/template-operators/array_prepend.md)) | [Array-Operatoren](array-operatoren.md#veraltete-namen-array_append-array_prepend-und-array_merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `array_search` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#array_search) | `SwarkArraySearchOperator` (Erweiterung swark) | site, admin |
| `array_sum` ([generiert](../../../eng-us/examples/template-operators/array_sum.md)) | [Array-Operatoren](array-operatoren.md#array_sum) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `arsort` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#arsort) | `SwarkARSortOperator` (Erweiterung swark) | site, admin |
| `asort` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#asort) | `SwarkASortOperator` (Erweiterung swark) | site, admin |
| `asset` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `attribute` ([generiert](../../../eng-us/examples/template-operators/attribute.md)) | [Attribut- und Dump-Operatoren](attribute-und-dump-operatoren.md#attribute) | `eZTemplateAttributeOperator` (lib/eztemplate/classes) | site, admin |
| `audit_label` ([generiert](../../../eng-us/examples/template-operators/audit_label.md)) | [Operatoren für Installation, Umgebung, Audit und CSP](operatoren-des-exponential-kernels.md#audit_label) | `expAuditTemplateOperator` (kernel/classes/audit/console) | site, admin |
| `autolink` ([generiert](../../../eng-us/examples/template-operators/autolink.md)) | [Text-Hilfsoperatoren des Kernels](text-hilfsoperatoren-des-kernels.md#autolink) | `eZAutoLinkOperator` (kernel/common) | site, admin |

## B

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `bc_ga_formatNumericDecimal` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#bc_ga_formatnumericdecimal) | `BCWebsiteStatisticsOperators` (Erweiterung bcwebsitestatistics) | site, admin |
| `bc_ga_jsEscapedString` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#bc_ga_jsescapedstring) | `BCWebsiteStatisticsOperators` (Erweiterung bcwebsitestatistics) | site, admin |
| `bc_ga_script_url` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#bc_ga_script_url) | `BCWebsiteStatisticsOperators` (Erweiterung bcwebsitestatistics) | site, admin |
| `bc_ga_urchin` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#bc_ga_urchin) | `BCWebsiteStatisticsOperators` (Erweiterung bcwebsitestatistics) | site, admin |
| `bc_ga_urchinOrder` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#bc_ga_urchinorder) | `BCWebsiteStatisticsOperators` (Erweiterung bcwebsitestatistics) | site, admin |
| `bc_ga_xmlAttributeValue` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#bc_ga_xmlattributevalue) | `BCWebsiteStatisticsOperators` (Erweiterung bcwebsitestatistics) | site, admin |
| `begins_with` ([generiert](../../../eng-us/examples/template-operators/begins_with.md)) | [Array-Operatoren](array-operatoren.md#prüfen-contains-compare-begins_with-und-ends_with) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `bin2hex` ([generiert](../../../eng-us/examples/template-operators/bin2hex.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#bin2hex-und-hex2bin) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `break` ([generiert](../../../eng-us/examples/template-operators/break.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#zeilenumbrüche-wrap-und-break) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |

## C

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `ceil` ([generiert](../../../eng-us/examples/template-operators/ceil.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#abs-ceil-floor-round-int-und-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `charset` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#charset) | `SwarkCharsetOperator` (Erweiterung swark) | site, admin |
| `choose` ([generiert](../../../eng-us/examples/template-operators/choose.md)) | [Logische Operatoren](logische-operatoren.md#choose) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `chr` ([generiert](../../../eng-us/examples/template-operators/chr.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#codepunkte-ord-und-chr) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `chunk_split` ([generiert](../../../eng-us/examples/template-operators/chunk_split.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ränder-und-schreibung-ltrim-rtrim-lcfirst-und-chunk_split) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `cjw_newsletter_preg_replace` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjw_newsletter_preg_replace) | `CjwNewsletterOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjw_newsletter_str_replace` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjw_newsletter_str_replace) | `CjwNewsletterOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjw_newsletter_variable` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjw_newsletter_variable) | `CjwNewsletterOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_abs_url` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_abs_url) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_condition_close` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_condition_close) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_condition_else` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_condition_else) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_condition_open` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_condition_open) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_interests_block` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_interests_block) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_plaintext` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_plaintext) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_rendering_data` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_rendering_data) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_richtext` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_richtext) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_text_underline` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_text_underline) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `cjwnl_text_wrap` | [Newsletter-Operatoren von cjw_newsletter](newsletter-operatoren.md#cjwnl_text_wrap) | `CjwNewsletterRenderingOperators` (Erweiterung cjw_newsletter) | site, admin |
| `class_icon` ([generiert](../../../eng-us/examples/template-operators/class_icon.md)) | [Icon-Operatoren](icon-operatoren.md#class_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `classgroup_icon` ([generiert](../../../eng-us/examples/template-operators/classgroup_icon.md)) | [Icon-Operatoren](icon-operatoren.md#classgroup_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `clear_object_cache` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#clear_object_cache) | `SwarkClearObjectCacheOperator` (Erweiterung swark) | site, admin |
| `compare` ([generiert](../../../eng-us/examples/template-operators/compare.md)) | [Array-Operatoren](array-operatoren.md#prüfen-contains-compare-begins_with-und-ends_with) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `component_content` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `concat` ([generiert](../../../eng-us/examples/template-operators/concat.md)) | [Text-Operatoren](text-operatoren.md#concat) | `eZTemplateTextOperator` (lib/eztemplate/classes) | site, admin |
| `cond` ([generiert](../../../eng-us/examples/template-operators/cond.md)) | [Steueroperatoren](steueroperatoren.md#cond) | `eZTemplateControlOperator` (lib/eztemplate/classes) | site, admin |
| `contains` ([generiert](../../../eng-us/examples/template-operators/contains.md)) | [Array-Operatoren](array-operatoren.md#prüfen-contains-compare-begins_with-und-ends_with) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `content_link` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `content_structure_tree` ([generiert](../../../eng-us/examples/template-operators/content_structure_tree.md)) | [Menü- und Baum-Operatoren](menue-und-baum-operatoren.md#content_structure_tree) | `eZContentStructureTreeOperator` (kernel/common) | site, admin |
| `content_tags` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `controller` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `convert_uudecode` ([generiert](../../../eng-us/examples/template-operators/convert_uudecode.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#convert_uuencode-und-convert_uudecode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `convert_uuencode` ([generiert](../../../eng-us/examples/template-operators/convert_uuencode.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#convert_uuencode-und-convert_uudecode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `cookie` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#cookie) | `SwarkCookieOperator` (Erweiterung swark) | site, admin |
| `count` ([generiert](../../../eng-us/examples/template-operators/count.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#count) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `count_chars` ([generiert](../../../eng-us/examples/template-operators/count_chars.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#zählen-count_words-und-count_chars) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `count_words` ([generiert](../../../eng-us/examples/template-operators/count_words.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#zählen-count_words-und-count_chars) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `crc32` ([generiert](../../../eng-us/examples/template-operators/crc32.md)) | [Digest-Operatoren](digest-operatoren.md#crc32) | `eZTemplateDigestOperator` (lib/eztemplate/classes) | site, admin |
| `csp_nonce` ([generiert](../../../eng-us/examples/template-operators/csp_nonce.md)) | [Operatoren für Installation, Umgebung, Audit und CSP](operatoren-des-exponential-kernels.md#csp_nonce) | `expContentSecurityPolicyOperator` (kernel/common) | site, admin |
| `current_layout` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#current_layout) | `SwarkCurrentLayoutOperator` (Erweiterung swark) | site, admin |
| `current_siteaccess` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#current_siteaccess) | `SwarkCurrentSiteaccessOperator` (Erweiterung swark) | site, admin |
| `currentdate` ([generiert](../../../eng-us/examples/template-operators/currentdate.md)) | [Locale-Operatoren](locale-operatoren.md#currentdate) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |

## D

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `d18n` ([generiert](../../../eng-us/examples/template-operators/d18n.md)) | [Übersetzungsoperatoren i18n, d18n und x18n](i18n-operatoren.md#d18n) | `eZi18nOperator` (kernel/common) | site, admin |
| `datetime` ([generiert](../../../eng-us/examples/template-operators/datetime.md)) | [Locale-Operatoren](locale-operatoren.md#datetime) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `debug` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#debug) | `SwarkDebugOperator` (Erweiterung swark) | site, admin |
| `debug_attributes` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#debug_attributes) | `SwarkDebugAttributesOperator` (Erweiterung swark) | site, admin |
| `dec` ([generiert](../../../eng-us/examples/template-operators/dec.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#inc-und-dec) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `div` ([generiert](../../../eng-us/examples/template-operators/div.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#mul-div-und-mod) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `downcase` ([generiert](../../../eng-us/examples/template-operators/downcase.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#groß--und-kleinschreibung-upcase-downcase-upfirst-und-upword) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `dump` ([generiert](../../../eng-us/examples/template-operators/dump.md)) | [Attribut- und Dump-Operatoren](attribute-und-dump-operatoren.md#dump) | `eZTemplateAttributeOperator` (lib/eztemplate/classes) | site, admin |

## E

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `embed_image` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ends_with` ([generiert](../../../eng-us/examples/template-operators/ends_with.md)) | [Array-Operatoren](array-operatoren.md#prüfen-contains-compare-begins_with-und-ends_with) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `enhanced_link` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `eq` ([generiert](../../../eng-us/examples/template-operators/eq.md)) | [Logische Operatoren](logische-operatoren.md#eq-und-ne) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `exp_can_login_to_siteaccess` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `exp_config` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#exp_config) | `expUITemplateOperators` (Erweiterung expui) | site, admin |
| `exp_environment` ([generiert](../../../eng-us/examples/template-operators/exp_environment.md)) | [Operatoren für Installation, Umgebung, Audit und CSP](operatoren-des-exponential-kernels.md#exp_environment) | `expEnvironmentOperator` (kernel/common) | site, admin |
| `exp_environment_badge` ([generiert](../../../eng-us/examples/template-operators/exp_environment_badge.md)) | [Operatoren für Installation, Umgebung, Audit und CSP](operatoren-des-exponential-kernels.md#exp_environment_badge) | `expEnvironmentOperator` (kernel/common) | site, admin |
| `exp_path` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `exp_url` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `expai_json` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expai_json) | `expAiTemplateOperators` (Erweiterung expai) | admin |
| `expai_money` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expai_money) | `expAiTemplateOperators` (Erweiterung expai) | admin |
| `expai_number` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expai_number) | `expAiTemplateOperators` (Erweiterung expai) | admin |
| `expai_retention` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expai_retention) | `expAiTemplateOperators` (Erweiterung expai) | admin |
| `expai_tokens` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expai_tokens) | `expAiTemplateOperators` (Erweiterung expai) | admin |
| `expinfo` ([generiert](../../../eng-us/examples/template-operators/expinfo.md)) | [Der Operator expinfo](der-expinfo-operator.md#expinfo) | `eZTemplateExpInfoOperator` (lib/eztemplate/classes) | site, admin |
| `expl_first` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expl_first) | `ExplBlockOperator` (Erweiterung explayouts) | site, admin |
| `expl_has` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expl_has) | `ExplBlockOperator` (Erweiterung explayouts) | site, admin |
| `expl_parse` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expl_parse) | `ExplBlockOperator` (Erweiterung explayouts) | site, admin |
| `expl_strip` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#expl_strip) | `ExplBlockOperator` (Erweiterung explayouts) | site, admin |
| `explayouts_render_result` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `explayouts_render_zone` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `explayouts_ui_has_template` | [Operatoren für Layouts, expui und expai](layouts-und-expui-operatoren.md#explayouts_ui_has_template) | `expLayoutsUiTemplateOperators` (Erweiterung explayouts_ui) | site, admin |
| `explode` ([generiert](../../../eng-us/examples/template-operators/explode.md)) | [Array-Operatoren](array-operatoren.md#verbinden-und-aufteilen-implode-und-explode) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `exponential` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `extract` ([generiert](../../../eng-us/examples/template-operators/extract.md)) | [Array-Operatoren](array-operatoren.md#ausschneiden-extract-extract_left-und-extract_right) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `extract_left` ([generiert](../../../eng-us/examples/template-operators/extract_left.md)) | [Array-Operatoren](array-operatoren.md#ausschneiden-extract-extract_left-und-extract_right) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `extract_right` ([generiert](../../../eng-us/examples/template-operators/extract_right.md)) | [Array-Operatoren](array-operatoren.md#ausschneiden-extract-extract_left-und-extract_right) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `exturl` ([generiert](../../../eng-us/examples/template-operators/exturl.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#exturl) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezarchive` | [ezwebin-Operatoren](ezwebin-operatoren.md#ezarchive) | `eZArchive` (Erweiterung ezwebin) | site, admin |
| `ezcreateclasslistgroups` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#ezcreateclasslistgroups) | `eZCreateClassListGroups` (Erweiterung ezwt) | site, admin |
| `ezcss` ([generiert](../../../eng-us/examples/template-operators/ezcss.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#ezcss) | `ezjscPackerTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `ezcss_load` ([generiert](../../../eng-us/examples/template-operators/ezcss_load.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#ezcss_load) | `ezjscPackerTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `ezcss_require` ([generiert](../../../eng-us/examples/template-operators/ezcss_require.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#ezcss_require) | `ezjscPackerTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `ezcssfiles` ([generiert](../../../eng-us/examples/template-operators/ezcssfiles.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#ezcssfiles) | `ezjscPackerTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `ezdesign` ([generiert](../../../eng-us/examples/template-operators/ezdesign.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezdesign) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezhttp` ([generiert](../../../eng-us/examples/template-operators/ezhttp.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezhttp) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezhttp_hasvariable` ([generiert](../../../eng-us/examples/template-operators/ezhttp_hasvariable.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezhttp_hasvariable) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezimage` ([generiert](../../../eng-us/examples/template-operators/ezimage.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezimage) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezini` ([generiert](../../../eng-us/examples/template-operators/ezini.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezini) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezini_hasvariable` ([generiert](../../../eng-us/examples/template-operators/ezini_hasvariable.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezini_hasvariable) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezkeywordlist` | [ezwebin-Operatoren](ezwebin-operatoren.md#ezkeywordlist) | `eZKeywordList` (Erweiterung ezwebin) | site, admin |
| `ezmodule` ([generiert](../../../eng-us/examples/template-operators/ezmodule.md)) | [Operatoren für Einstellungen, Module, Pakete und PDF](weitere-kernel-operatoren.md#ezmodule) | `eZModuleOperator` (kernel/common) | site, admin |
| `ezoe_ini_section` ([generiert](../../../eng-us/examples/template-operators/ezoe_ini_section.md)) | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#ezoe_ini_section) | `eZOETemplateUtils` (Erweiterung ezoe) | site, admin |
| `ezpackage` ([generiert](../../../eng-us/examples/template-operators/ezpackage.md)) | [Operatoren für Einstellungen, Module, Pakete und PDF](weitere-kernel-operatoren.md#ezpackage) | `eZPackageOperator` (kernel/common) | site, admin |
| `ezpagedata` | [ezwebin-Operatoren](ezwebin-operatoren.md#ezpagedata) | `eZPageData` (Erweiterung ezwebin) | site, admin |
| `ezpagedata_append` | [ezwebin-Operatoren](ezwebin-operatoren.md#ezpagedata_append) | `eZPageData` (Erweiterung ezwebin) | site, admin |
| `ezpagedata_set` | [ezwebin-Operatoren](ezwebin-operatoren.md#ezpagedata_set) | `eZPageData` (Erweiterung ezwebin) | site, admin |
| `ezpreference` ([generiert](../../../eng-us/examples/template-operators/ezpreference.md)) | [Operatoren für Einstellungen, Module, Pakete und PDF](weitere-kernel-operatoren.md#ezpreference) | `eZKernelOperator` (kernel/common) | site, admin |
| `ezroot` ([generiert](../../../eng-us/examples/template-operators/ezroot.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezroot) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezscript` ([generiert](../../../eng-us/examples/template-operators/ezscript.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#ezscript) | `ezjscPackerTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `ezscript_load` ([generiert](../../../eng-us/examples/template-operators/ezscript_load.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#ezscript_load) | `ezjscPackerTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `ezscript_require` ([generiert](../../../eng-us/examples/template-operators/ezscript_require.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#ezscript_require) | `ezjscPackerTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `ezscriptfiles` ([generiert](../../../eng-us/examples/template-operators/ezscriptfiles.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#ezscriptfiles) | `ezjscPackerTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `ezsys` ([generiert](../../../eng-us/examples/template-operators/ezsys.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezsys) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `eztagcloud` | [ezwebin-Operatoren](ezwebin-operatoren.md#eztagcloud) | `eZTagCloud` (Erweiterung ezwebin) | site, admin |
| `eztags_parent_string` | [Operatoren für eztags und Sternbewertungen](eztags-und-starrating-operatoren.md#eztags_parent_string) | `eZTagsTemplateFunctions` (Erweiterung eztags) | site, admin |
| `eztagscloud` | [Operatoren für eztags und Sternbewertungen](eztags-und-starrating-operatoren.md#eztagscloud) | `eZTagsCloud` (Erweiterung eztags) | site, admin |
| `eztoc` ([generiert](../../../eng-us/examples/template-operators/eztoc.md)) | [Text-Hilfsoperatoren des Kernels](text-hilfsoperatoren-des-kernels.md#eztoc) | `eZTOCOperator` (kernel/common) | site, admin |
| `ezurl` ([generiert](../../../eng-us/examples/template-operators/ezurl.md)) | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezurl) | `eZURLMultisiteURLOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezurl_path` | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezurl_path) | `eZURLMultisiteOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezurl_roots` | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezurl_roots) | `eZURLMultisiteOperator` (Erweiterung ezurlmultisite) | site, admin |
| `ezurl_siteaccess` | [Operatoren für URL, Design, INI und HTTP](url-operatoren.md#ezurl_siteaccess) | `eZURLMultisiteOperator` (Erweiterung ezurlmultisite) | site, admin |

## F

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `false` ([generiert](../../../eng-us/examples/template-operators/false.md)) | [Logische Operatoren](logische-operatoren.md#not-null-true-und-false) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `fetch` ([generiert](../../../eng-us/examples/template-operators/fetch.md)) | [Die Operatoren fetch und fetch_alias](execute-operatoren.md#fetch) | `eZTemplateExecuteOperator` (lib/eztemplate/classes) | site, admin |
| `fetch_alias` ([generiert](../../../eng-us/examples/template-operators/fetch_alias.md)) | [Die Operatoren fetch und fetch_alias](execute-operatoren.md#fetch_alias) | `eZTemplateExecuteOperator` (lib/eztemplate/classes) | site, admin |
| `fetch_by_starrating` | [Operatoren für eztags und Sternbewertungen](eztags-und-starrating-operatoren.md#fetch_by_starrating) | `ezsrTemplateOperators` (Erweiterung ezstarrating) | site, admin |
| `fetch_starrating_data` | [Operatoren für eztags und Sternbewertungen](eztags-und-starrating-operatoren.md#fetch_starrating_data) | `ezsrTemplateOperators` (Erweiterung ezstarrating) | site, admin |
| `fetch_starrating_stats` | [Operatoren für eztags und Sternbewertungen](eztags-und-starrating-operatoren.md#fetch_starrating_stats) | `ezsrTemplateOperators` (Erweiterung ezstarrating) | site, admin |
| `fieldRelation` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `fieldRelations` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `fieldValue` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `filterChildren` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `filterFieldRelationLocations` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `filterFieldRelations` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `first_set` ([generiert](../../../eng-us/examples/template-operators/first_set.md)) | [Steueroperatoren](steueroperatoren.md#first_set) | `eZTemplateControlOperator` (lib/eztemplate/classes) | site, admin |
| `firstNonEmptyField` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `flag_icon` ([generiert](../../../eng-us/examples/template-operators/flag_icon.md)) | [Icon-Operatoren](icon-operatoren.md#flag_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `float` ([generiert](../../../eng-us/examples/template-operators/float.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#abs-ceil-floor-round-int-und-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `floor` ([generiert](../../../eng-us/examples/template-operators/floor.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#abs-ceil-floor-round-int-und-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |

## G

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `ge` ([generiert](../../../eng-us/examples/template-operators/ge.md)) | [Logische Operatoren](logische-operatoren.md#lt-gt-le-und-ge) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `get_class` ([generiert](../../../eng-us/examples/template-operators/get_class.md)) | [Typ-Operatoren](typ-operatoren.md#get_type-und-get_class) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `get_netgen_open_graph` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `get_type` ([generiert](../../../eng-us/examples/template-operators/get_type.md)) | [Typ-Operatoren](typ-operatoren.md#get_type-und-get_class) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `getdate` | [OW Simple Operator - PHP-Funktionen als Template-Operatoren](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (Erweiterung owsimpleoperator) | site, admin |
| `getenv` | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#getenv) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `getParameter` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `gettime` ([generiert](../../../eng-us/examples/template-operators/gettime.md)) | [Locale-Operatoren](locale-operatoren.md#maketime-makedate-und-gettime) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `gt` ([generiert](../../../eng-us/examples/template-operators/gt.md)) | [Logische Operatoren](logische-operatoren.md#lt-gt-le-und-ge) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |

## H

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `has_access_to_limitation` ([generiert](../../../eng-us/examples/template-operators/has_access_to_limitation.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#has_access_to_limitation) | `ezjscAccessTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `has_any_role` ([generiert](../../../eng-us/examples/template-operators/has_any_role.md)) | [Rollen-Operatoren](rollen-operatoren.md#has_role-has_role_id-und-has_any_role) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_policy` ([generiert](../../../eng-us/examples/template-operators/has_policy.md)) | [Rollen-Operatoren](rollen-operatoren.md#has_policy-und-has_policy_by_user) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_policy_by_user` ([generiert](../../../eng-us/examples/template-operators/has_policy_by_user.md)) | [Rollen-Operatoren](rollen-operatoren.md#has_policy-und-has_policy_by_user) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_role` ([generiert](../../../eng-us/examples/template-operators/has_role.md)) | [Rollen-Operatoren](rollen-operatoren.md#has_role-has_role_id-und-has_any_role) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_role_by_user` ([generiert](../../../eng-us/examples/template-operators/has_role_by_user.md)) | [Rollen-Operatoren](rollen-operatoren.md#has_role_by_user-und-has_role_by_user_id) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_role_by_user_id` ([generiert](../../../eng-us/examples/template-operators/has_role_by_user_id.md)) | [Rollen-Operatoren](rollen-operatoren.md#has_role_by_user-und-has_role_by_user_id) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `has_role_id` ([generiert](../../../eng-us/examples/template-operators/has_role_id.md)) | [Rollen-Operatoren](rollen-operatoren.md#has_role-has_role_id-und-has_any_role) | `eZTemplateRoleOperator` (lib/eztemplate/classes) | site, admin |
| `hasField` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `hash` ([generiert](../../../eng-us/examples/template-operators/hash.md)) | [Array-Operatoren](array-operatoren.md#aufbauen-array-und-hash) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `hasParameter` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `haveToPaginate` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `hebrev` ([generiert](../../../eng-us/examples/template-operators/hebrev.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#str_shuffle-und-hebrev) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `hex2bin` ([generiert](../../../eng-us/examples/template-operators/hex2bin.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#bin2hex-und-hex2bin) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `html_entity_decode` ([generiert](../../../eng-us/examples/template-operators/html_entity_decode.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#html_entity_decode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `htmlentities` ([generiert](../../../eng-us/examples/template-operators/htmlentities.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#htmlentities) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `htmlspecialchars_decode` ([generiert](../../../eng-us/examples/template-operators/htmlspecialchars_decode.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#htmlspecialchars_decode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## I

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `i18n` ([generiert](../../../eng-us/examples/template-operators/i18n.md)) | [Übersetzungsoperatoren i18n, d18n und x18n](i18n-operatoren.md#i18n) | `eZi18nOperator` (kernel/common) | site, admin |
| `ibexa` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ibexa_path` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ibexa_url` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `icon` ([generiert](../../../eng-us/examples/template-operators/icon.md)) | [Icon-Operatoren](icon-operatoren.md#icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `icon_info` ([generiert](../../../eng-us/examples/template-operators/icon_info.md)) | [Icon-Operatoren](icon-operatoren.md#icon_info) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `image` ([generiert](../../../eng-us/examples/template-operators/image.md)) | [Bild-Operatoren](bild-operatoren.md#image) | `eZTemplateImageOperator` (lib/eztemplate/classes) | site, admin |
| `image_link` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `imagefile` ([generiert](../../../eng-us/examples/template-operators/imagefile.md)) | [Bild-Operatoren](bild-operatoren.md#imagefile) | `eZTemplateImageOperator` (lib/eztemplate/classes) | site, admin |
| `implode` ([generiert](../../../eng-us/examples/template-operators/implode.md)) | [Array-Operatoren](array-operatoren.md#verbinden-und-aufteilen-implode-und-explode) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `inc` ([generiert](../../../eng-us/examples/template-operators/inc.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#inc-und-dec) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `indent` ([generiert](../../../eng-us/examples/template-operators/indent.md)) | [Text-Operatoren](text-operatoren.md#indent) | `eZTemplateTextOperator` (lib/eztemplate/classes) | site, admin |
| `insert` ([generiert](../../../eng-us/examples/template-operators/insert.md)) | [Array-Operatoren](array-operatoren.md#bearbeiten-insert-remove-und-replace) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `installation_name` ([generiert](../../../eng-us/examples/template-operators/installation_name.md)) | [Operatoren für Installation, Umgebung, Audit und CSP](operatoren-des-exponential-kernels.md#installation_name) | `ExpInstallationOperator` (kernel/classes) | site, admin |
| `int` ([generiert](../../../eng-us/examples/template-operators/int.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#abs-ceil-floor-round-int-und-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `intro` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `is_array` ([generiert](../../../eng-us/examples/template-operators/is_array.md)) | [Typ-Operatoren](typ-operatoren.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_boolean` ([generiert](../../../eng-us/examples/template-operators/is_boolean.md)) | [Typ-Operatoren](typ-operatoren.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_class` ([generiert](../../../eng-us/examples/template-operators/is_class.md)) | [Typ-Operatoren](typ-operatoren.md#is_class) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_float` ([generiert](../../../eng-us/examples/template-operators/is_float.md)) | [Typ-Operatoren](typ-operatoren.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_integer` ([generiert](../../../eng-us/examples/template-operators/is_integer.md)) | [Typ-Operatoren](typ-operatoren.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_null` ([generiert](../../../eng-us/examples/template-operators/is_null.md)) | [Typ-Operatoren](typ-operatoren.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_numeric` ([generiert](../../../eng-us/examples/template-operators/is_numeric.md)) | [Typ-Operatoren](typ-operatoren.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_object` ([generiert](../../../eng-us/examples/template-operators/is_object.md)) | [Typ-Operatoren](typ-operatoren.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_post_request` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#is_post_request) | `SwarkIsPostRequestOperator` (Erweiterung swark) | site, admin |
| `is_production_system` ([generiert](../../../eng-us/examples/template-operators/is_production_system.md)) | [Operatoren für Installation, Umgebung, Audit und CSP](operatoren-des-exponential-kernels.md#is_production_system) | `ExpInstallationOperator` (kernel/classes) | site, admin |
| `is_set` ([generiert](../../../eng-us/examples/template-operators/is_set.md)) | [Typ-Operatoren](typ-operatoren.md#is_set-und-is_unset) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_string` ([generiert](../../../eng-us/examples/template-operators/is_string.md)) | [Typ-Operatoren](typ-operatoren.md#is_array-is_boolean-is_integer-is_float-is_numeric-is_string-is_object-is_null) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `is_unset` ([generiert](../../../eng-us/examples/template-operators/is_unset.md)) | [Typ-Operatoren](typ-operatoren.md#is_set-und-is_unset) | `eZTemplateTypeOperator` (lib/eztemplate/classes) | site, admin |
| `item_content_link` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `item_image_link` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `item_params` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `item_view_template` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |

## J

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `json_encode` ([generiert](../../../eng-us/examples/template-operators/json_encode.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#json_encode) | `ezjscEncodingTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `json_ld` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |

## K

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `krsort` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#krsort) | `SwarkKRSortOperator` (Erweiterung swark) | site, admin |
| `ksort` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#ksort) | `SwarkKSortOperator` (Erweiterung swark) | site, admin |

## L

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `l10n` ([generiert](../../../eng-us/examples/template-operators/l10n.md)) | [Locale-Operatoren](locale-operatoren.md#l10n) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `language_switcher` ([generiert](../../../eng-us/examples/template-operators/language_switcher.md)) | [Siteaccess-Operatoren](siteaccess-operatoren.md#language_switcher) | `ezpLanguageSwitcherOperator` (kernel/private/eztemplate) | site, admin |
| `latest_tags` | [Operatoren für eztags und Sternbewertungen](eztags-und-starrating-operatoren.md#latest_tags) | `eZTagsTemplateFunctions` (Erweiterung eztags) | site, admin |
| `layout_title` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `lcfirst` ([generiert](../../../eng-us/examples/template-operators/lcfirst.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ränder-und-schreibung-ltrim-rtrim-lcfirst-und-chunk_split) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `le` ([generiert](../../../eng-us/examples/template-operators/le.md)) | [Logische Operatoren](logische-operatoren.md#lt-gt-le-und-ge) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `levenshtein` ([generiert](../../../eng-us/examples/template-operators/levenshtein.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#levenshtein) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `locale` ([generiert](../../../eng-us/examples/template-operators/locale.md)) | [Locale-Operatoren](locale-operatoren.md#locale) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `lt` ([generiert](../../../eng-us/examples/template-operators/lt.md)) | [Logische Operatoren](logische-operatoren.md#lt-gt-le-und-ge) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `ltrim` ([generiert](../../../eng-us/examples/template-operators/ltrim.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ränder-und-schreibung-ltrim-rtrim-lcfirst-und-chunk_split) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## M

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `makedate` ([generiert](../../../eng-us/examples/template-operators/makedate.md)) | [Locale-Operatoren](locale-operatoren.md#maketime-makedate-und-gettime) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `maketime` ([generiert](../../../eng-us/examples/template-operators/maketime.md)) | [Locale-Operatoren](locale-operatoren.md#maketime-makedate-und-gettime) | `eZTemplateLocaleOperator` (lib/eztemplate/classes) | site, admin |
| `max` ([generiert](../../../eng-us/examples/template-operators/max.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#min-und-max) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `md5` ([generiert](../../../eng-us/examples/template-operators/md5.md)) | [Digest-Operatoren](digest-operatoren.md#md5) | `eZTemplateDigestOperator` (lib/eztemplate/classes) | site, admin |
| `merge` ([generiert](../../../eng-us/examples/template-operators/merge.md)) | [Array-Operatoren](array-operatoren.md#hinzufügen-append-prepend-und-merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `metadata` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#metadata) | `xrowMetaDataOperator` (Erweiterung xrowmetadata) | site, admin |
| `metaphone` ([generiert](../../../eng-us/examples/template-operators/metaphone.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#soundex-und-metaphone) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `mimetype_icon` ([generiert](../../../eng-us/examples/template-operators/mimetype_icon.md)) | [Icon-Operatoren](icon-operatoren.md#mimetype_icon) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `min` ([generiert](../../../eng-us/examples/template-operators/min.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#min-und-max) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `mktime` | [OW Simple Operator - PHP-Funktionen als Template-Operatoren](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (Erweiterung owsimpleoperator) | site, admin |
| `mod` ([generiert](../../../eng-us/examples/template-operators/mod.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#mul-div-und-mod) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `modify_view_parameter` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#modify_view_parameter) | `SwarkModifyViewParameterOperator` (Erweiterung swark) | site, admin |
| `module_params` ([generiert](../../../eng-us/examples/template-operators/module_params.md)) | [Operatoren für Einstellungen, Module, Pakete und PDF](weitere-kernel-operatoren.md#module_params) | `eZModuleParamsOperator` (kernel/common) | site, admin |
| `month_overview` ([generiert](../../../eng-us/examples/template-operators/month_overview.md)) | [Text-Hilfsoperatoren des Kernels](text-hilfsoperatoren-des-kernels.md#month_overview) | `eZDateOperatorCollection` (kernel/common) | site, admin |
| `mul` ([generiert](../../../eng-us/examples/template-operators/mul.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#mul-div-und-mod) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |

## N

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `ne` ([generiert](../../../eng-us/examples/template-operators/ne.md)) | [Logische Operatoren](logische-operatoren.md#eq-und-ne) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `ng_image_alias` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ng_query` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ng_render_field` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ng_view_content` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `nglayouts_render_result` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `nglayouts_render_zone` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ngsite` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ngsite_group_fields` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ngsite_language_name` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `ngsite_topic_path` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `nl2br` ([generiert](../../../eng-us/examples/template-operators/nl2br.md)) | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#nl2br) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `node_encode` ([generiert](../../../eng-us/examples/template-operators/node_encode.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#node_encode) | `ezjscEncodingTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `not` ([generiert](../../../eng-us/examples/template-operators/not.md)) | [Logische Operatoren](logische-operatoren.md#not-null-true-und-false) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `null` ([generiert](../../../eng-us/examples/template-operators/null.md)) | [Logische Operatoren](logische-operatoren.md#not-null-true-und-false) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `number_format` ([generiert](../../../eng-us/examples/template-operators/number_format.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#number_format) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## O

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `or` ([generiert](../../../eng-us/examples/template-operators/or.md)) | [Logische Operatoren](logische-operatoren.md#and-und-or) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |
| `ord` ([generiert](../../../eng-us/examples/template-operators/ord.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#codepunkte-ord-und-chr) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |

## P

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `pad` ([generiert](../../../eng-us/examples/template-operators/pad.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#bereinigen-trim-simplify-und-pad) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `pagerfanta` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `parameter` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `parent` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `parsexml` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#parsexml) | `TemplateParseXMLOperator` (Erweiterung enhancedezbinaryfile) | site, admin |
| `path` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `pdf` ([generiert](../../../eng-us/examples/template-operators/pdf.md)) | [Operatoren für Einstellungen, Module, Pakete und PDF](weitere-kernel-operatoren.md#pdf) | `eZPDF` (lib/ezpdf/classes) | site, admin |
| `player` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `player_slide` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `poster` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `poster_slide` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `preg_match` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#preg_match) | `SwarkPregMatchOperator` (Erweiterung swark) | site, admin |
| `preg_replace` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#preg_replace) | `SwarkPregReplaceOperator` (Erweiterung swark) | site, admin |
| `prepend` ([generiert](../../../eng-us/examples/template-operators/prepend.md)) | [Array-Operatoren](array-operatoren.md#hinzufügen-append-prepend-und-merge) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |

## Q

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `quoted_printable_decode` ([generiert](../../../eng-us/examples/template-operators/quoted_printable_decode.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#quoted_printable_encode-und-quoted_printable_decode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `quoted_printable_encode` ([generiert](../../../eng-us/examples/template-operators/quoted_printable_encode.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#quoted_printable_encode-und-quoted_printable_decode) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `quotemeta` ([generiert](../../../eng-us/examples/template-operators/quotemeta.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#backslashes-addslashes-stripslashes-addcslashes-stripcslashes-und-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## R

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `rand` ([generiert](../../../eng-us/examples/template-operators/rand.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#roman-und-rand) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `range` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#range) | `SwarkRangeOperator` (Erweiterung swark) | site, admin |
| `rawurldecode` ([generiert](../../../eng-us/examples/template-operators/rawurldecode.md)) | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#urlencode-urldecode-rawurlencode-rawurldecode) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `rawurlencode` ([generiert](../../../eng-us/examples/template-operators/rawurlencode.md)) | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#urlencode-urldecode-rawurlencode-rawurldecode) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `recaptcha_get_html` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#recaptcha_get_html) | `reCAPTCHATemplateOperator` (Erweiterung recaptcha) | site, admin |
| `recipe_schema` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `redirect` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#redirect) | `SwarkRedirectOperator` (Erweiterung swark) | site, admin |
| `redirect_to_site_root` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `remove` ([generiert](../../../eng-us/examples/template-operators/remove.md)) | [Array-Operatoren](array-operatoren.md#bearbeiten-insert-remove-und-replace) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `remove_array_element` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#remove_array_element) | `SwarkRemoveArrayElementOperator` (Erweiterung swark) | site, admin |
| `render` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `render_esi` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `repeat` ([generiert](../../../eng-us/examples/template-operators/repeat.md)) | [Array-Operatoren](array-operatoren.md#umformen-repeat-reverse-und-unique) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `replace` ([generiert](../../../eng-us/examples/template-operators/replace.md)) | [Array-Operatoren](array-operatoren.md#bearbeiten-insert-remove-und-replace) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `return` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#return) | `SwarkReturnOperator` (Erweiterung swark) | site, admin |
| `reverse` ([generiert](../../../eng-us/examples/template-operators/reverse.md)) | [Array-Operatoren](array-operatoren.md#umformen-repeat-reverse-und-unique) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `ristring` ([generiert](../../../eng-us/examples/template-operators/ristring.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ersetzen-ristring-rstring-und-strtr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `roman` ([generiert](../../../eng-us/examples/template-operators/roman.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#roman-und-rand) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `rot13` ([generiert](../../../eng-us/examples/template-operators/rot13.md)) | [Digest-Operatoren](digest-operatoren.md#rot13) | `eZTemplateDigestOperator` (lib/eztemplate/classes) | site, admin |
| `round` ([generiert](../../../eng-us/examples/template-operators/round.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#abs-ceil-floor-round-int-und-float) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `rsort` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#rsort) | `SwarkRSortOperator` (Erweiterung swark) | site, admin |
| `rstring` ([generiert](../../../eng-us/examples/template-operators/rstring.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ersetzen-ristring-rstring-und-strtr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `rtrim` ([generiert](../../../eng-us/examples/template-operators/rtrim.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ränder-und-schreibung-ltrim-rtrim-lcfirst-und-chunk_split) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## S

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `saveXML` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `serialize` | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#serialize-und-unserialize) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `server` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#server) | `SwarkServerOperator` (Erweiterung swark) | site, admin |
| `set_array_element` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#set_array_element) | `SwarkSetArrayElementOperator` (Erweiterung swark) | site, admin |
| `shorten` ([generiert](../../../eng-us/examples/template-operators/shorten.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#shorten) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `shortenw` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#shortenw) | `SwarkShortenWOperator` (Erweiterung swark) | site, admin |
| `shuffle` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#shuffle) | `SwarkShuffleOperator` (Erweiterung swark) | site, admin |
| `si` ([generiert](../../../eng-us/examples/template-operators/si.md)) | [Der Einheiten-Operator si](der-unit-operator.md#si) | `eZTemplateUnitOperator` (lib/eztemplate/classes) | site, admin |
| `similar_text` ([generiert](../../../eng-us/examples/template-operators/similar_text.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#similar_text) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `simpletags` ([generiert](../../../eng-us/examples/template-operators/simpletags.md)) | [Text-Hilfsoperatoren des Kernels](text-hilfsoperatoren-des-kernels.md#simpletags) | `eZSimpleTagsOperator` (kernel/common) | site, admin |
| `simplify` ([generiert](../../../eng-us/examples/template-operators/simplify.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#bereinigen-trim-simplify-und-pad) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `site_url` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `siteaccess_href` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `siteaccess_menu` ([generiert](../../../eng-us/examples/template-operators/siteaccess_menu.md)) | [Siteaccess-Operatoren](siteaccess-operatoren.md#siteaccess_menu) | `ezpSiteAccessMenuOperator` (kernel/common) | site, admin |
| `siteaccess_menu_key` ([generiert](../../../eng-us/examples/template-operators/siteaccess_menu_key.md)) | [Siteaccess-Operatoren](siteaccess-operatoren.md#siteaccess_menu_key) | `ezpSiteAccessMenuOperator` (kernel/common) | site, admin |
| `siteaccess_url` ([generiert](../../../eng-us/examples/template-operators/siteaccess_url.md)) | [Siteaccess-Operatoren](siteaccess-operatoren.md#siteaccess_url) | `ezpSiteAccessURLOperator` (kernel/common) | site, admin |
| `sort` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#sort) | `SwarkSortOperator` (Erweiterung swark) | site, admin |
| `soundex` ([generiert](../../../eng-us/examples/template-operators/soundex.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#soundex-und-metaphone) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `split_by_length` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#split_by_length) | `SwarkSplitByLengthOperator` (Erweiterung swark) | site, admin |
| `sprintf` ([generiert](../../../eng-us/examples/template-operators/sprintf.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#sprintf-und-vsprintf) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_contains` ([generiert](../../../eng-us/examples/template-operators/str_contains.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#str_contains-str_starts_with-str_ends_with) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_ends_with` ([generiert](../../../eng-us/examples/template-operators/str_ends_with.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#str_contains-str_starts_with-str_ends_with) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_getcsv` ([generiert](../../../eng-us/examples/template-operators/str_getcsv.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#aufteilen-str_split-str_word_count-str_getcsv-und-strtok) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_replace` | [OW Simple Operator - PHP-Funktionen als Template-Operatoren](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (Erweiterung owsimpleoperator) | site, admin |
| `str_rot13` | [OW Simple Operator - PHP-Funktionen als Template-Operatoren](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (Erweiterung owsimpleoperator) | site, admin |
| `str_shuffle` ([generiert](../../../eng-us/examples/template-operators/str_shuffle.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#str_shuffle-und-hebrev) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_split` ([generiert](../../../eng-us/examples/template-operators/str_split.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#aufteilen-str_split-str_word_count-str_getcsv-und-strtok) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_starts_with` ([generiert](../../../eng-us/examples/template-operators/str_starts_with.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#str_contains-str_starts_with-str_ends_with) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `str_word_count` ([generiert](../../../eng-us/examples/template-operators/str_word_count.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#aufteilen-str_split-str_word_count-str_getcsv-und-strtok) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strcasecmp` ([generiert](../../../eng-us/examples/template-operators/strcasecmp.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strcmp-und-strcasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strcmp` ([generiert](../../../eng-us/examples/template-operators/strcmp.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strcmp-und-strcasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strcoll` ([generiert](../../../eng-us/examples/template-operators/strcoll.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strcoll) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strcspn` ([generiert](../../../eng-us/examples/template-operators/strcspn.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strspn-und-strcspn) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strip_tags` ([generiert](../../../eng-us/examples/template-operators/strip_tags.md)) | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#strip_tags) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `stripcslashes` ([generiert](../../../eng-us/examples/template-operators/stripcslashes.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#backslashes-addslashes-stripslashes-addcslashes-stripcslashes-und-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `stripos` ([generiert](../../../eng-us/examples/template-operators/stripos.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strpos-stripos-strrpos-strripos) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `stripslashes` ([generiert](../../../eng-us/examples/template-operators/stripslashes.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#backslashes-addslashes-stripslashes-addcslashes-stripcslashes-und-quotemeta) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `stristr` ([generiert](../../../eng-us/examples/template-operators/stristr.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strstr-und-stristr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strlen` ([generiert](../../../eng-us/examples/template-operators/strlen.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strlen) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strnatcasecmp` ([generiert](../../../eng-us/examples/template-operators/strnatcasecmp.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strnatcmp-und-strnatcasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strnatcmp` ([generiert](../../../eng-us/examples/template-operators/strnatcmp.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strnatcmp-und-strnatcasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strncasecmp` ([generiert](../../../eng-us/examples/template-operators/strncasecmp.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strncmp-und-strncasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strncmp` ([generiert](../../../eng-us/examples/template-operators/strncmp.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strncmp-und-strncasecmp) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strpbrk` ([generiert](../../../eng-us/examples/template-operators/strpbrk.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strpbrk) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strpos` ([generiert](../../../eng-us/examples/template-operators/strpos.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strpos-stripos-strrpos-strripos) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strrchr` ([generiert](../../../eng-us/examples/template-operators/strrchr.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strrchr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strripos` ([generiert](../../../eng-us/examples/template-operators/strripos.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strpos-stripos-strrpos-strripos) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strrpos` ([generiert](../../../eng-us/examples/template-operators/strrpos.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strpos-stripos-strrpos-strripos) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strspn` ([generiert](../../../eng-us/examples/template-operators/strspn.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strspn-und-strcspn) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strstr` ([generiert](../../../eng-us/examples/template-operators/strstr.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#strstr-und-stristr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strtok` ([generiert](../../../eng-us/examples/template-operators/strtok.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#aufteilen-str_split-str_word_count-str_getcsv-und-strtok) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `strtr` ([generiert](../../../eng-us/examples/template-operators/strtr.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ersetzen-ristring-rstring-und-strtr) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `sub` ([generiert](../../../eng-us/examples/template-operators/sub.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#sum-und-sub) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |
| `substr` ([generiert](../../../eng-us/examples/template-operators/substr.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ausschneiden-substr-und-substr_replace) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `substr_compare` ([generiert](../../../eng-us/examples/template-operators/substr_compare.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#substr_compare) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `substr_count` ([generiert](../../../eng-us/examples/template-operators/substr_count.md)) | [PHP-Zeichenkettenfunktionen: suchen und vergleichen](php-zeichenkettenfunktionen-suchen-und-vergleichen.md#substr_count) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `substr_replace` ([generiert](../../../eng-us/examples/template-operators/substr_replace.md)) | [PHP-Zeichenkettenfunktionen: umwandeln](php-zeichenkettenfunktionen-umwandeln.md#ausschneiden-substr-und-substr_replace) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |
| `sum` ([generiert](../../../eng-us/examples/template-operators/sum.md)) | [Arithmetische Operatoren](arithmetische-operatoren.md#sum-und-sub) | `eZTemplateArithmeticOperator` (lib/eztemplate/classes) | site, admin |

## T

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `tag_icon` | [Operatoren für eztags und Sternbewertungen](eztags-und-starrating-operatoren.md#tag_icon) | `eZTagsTemplateFunctions` (Erweiterung eztags) | site, admin |
| `tag_url` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `texttoimage` ([generiert](../../../eng-us/examples/template-operators/texttoimage.md)) | [Bild-Operatoren](bild-operatoren.md#texttoimage) | `eZTemplateImageOperator` (lib/eztemplate/classes) | site, admin |
| `time` | [OW Simple Operator - PHP-Funktionen als Template-Operatoren](owsimpleoperator.md#time-mktime-getdate-str_replace-and-str_rot13) | `PHPFunctionOperator` (Erweiterung owsimpleoperator) | site, admin |
| `title` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `topmenu` ([generiert](../../../eng-us/examples/template-operators/topmenu.md)) | [Menü- und Baum-Operatoren](menue-und-baum-operatoren.md#topmenu) | `eZTopMenuOperator` (kernel/common) | site, admin |
| `tpl_block_template` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `trans` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `treemenu` ([generiert](../../../eng-us/examples/template-operators/treemenu.md)) | [Menü- und Baum-Operatoren](menue-und-baum-operatoren.md#treemenu) | `eZTreeMenuOperator` (kernel/common) | site, admin |
| `trim` ([generiert](../../../eng-us/examples/template-operators/trim.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#bereinigen-trim-simplify-und-pad) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `true` ([generiert](../../../eng-us/examples/template-operators/true.md)) | [Logische Operatoren](logische-operatoren.md#not-null-true-und-false) | `eZTemplateLogicOperator` (lib/eztemplate/classes) | site, admin |

## U

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `unique` ([generiert](../../../eng-us/examples/template-operators/unique.md)) | [Array-Operatoren](array-operatoren.md#umformen-repeat-reverse-und-unique) | `eZTemplateArrayOperator` (lib/eztemplate/classes) | site, admin |
| `unserialize` | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#serialize-und-unserialize) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `upcase` ([generiert](../../../eng-us/examples/template-operators/upcase.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#groß--und-kleinschreibung-upcase-downcase-upfirst-und-upword) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `upfirst` ([generiert](../../../eng-us/examples/template-operators/upfirst.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#groß--und-kleinschreibung-upcase-downcase-upfirst-und-upword) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `upword` ([generiert](../../../eng-us/examples/template-operators/upword.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#groß--und-kleinschreibung-upcase-downcase-upfirst-und-upword) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `uri_path_segment` | [Swark-Operatoren: Arrays, Zeichenketten und URLs](swark-operatoren-arrays-und-zeichenketten.md#uri_path_segment) | `SwarkURIPathSegmentOperator` (Erweiterung swark) | site, admin |
| `urldecode` ([generiert](../../../eng-us/examples/template-operators/urldecode.md)) | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#urlencode-urldecode-rawurlencode-rawurldecode) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `urlencode` ([generiert](../../../eng-us/examples/template-operators/urlencode.md)) | [PHP-Funktionsoperatoren](php-funktionsoperatoren.md#urlencode-urldecode-rawurlencode-rawurldecode) | `eZTemplatePHPOperator` (lib/eztemplate/classes) | site, admin |
| `user_id_by_login` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#user_id_by_login) | `SwarkUserIDByLoginOperator` (Erweiterung swark) | site, admin |
| `user_limitations` | [Operatoren für eztags und Sternbewertungen](eztags-und-starrating-operatoren.md#user_limitations) | `eZTagsTemplateFunctions` (Erweiterung eztags) | site, admin |

## V

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `variable_names` | [Swark-Operatoren: Anfrage und System](swark-operatoren-anfrage-und-system.md#variable_names) | `SwarkVariableNamesOperator` (Erweiterung swark) | site, admin |
| `video_thumbnail` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |
| `vsprintf` ([generiert](../../../eng-us/examples/template-operators/vsprintf.md)) | [PHP-Zeichenkettenfunktionen: kodieren und formatieren](php-zeichenkettenfunktionen-kodieren-und-formatieren.md#sprintf-und-vsprintf) | `eZTemplateStringsOperator` (lib/eztemplate/classes) | site, admin |

## W

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `wash` ([generiert](../../../eng-us/examples/template-operators/wash.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#wash) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |
| `wordtoimage` ([generiert](../../../eng-us/examples/template-operators/wordtoimage.md)) | [Icon-Operatoren](icon-operatoren.md#wordtoimage) | `eZWordToImageOperator` (kernel/common) | site, admin |
| `wrap` ([generiert](../../../eng-us/examples/template-operators/wrap.md)) | [Zeichenketten-Operatoren](zeichenketten-operatoren.md#zeilenumbrüche-wrap-und-break) | `eZTemplateStringOperator` (lib/eztemplate/classes) | site, admin |

## X

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `x18n` ([generiert](../../../eng-us/examples/template-operators/x18n.md)) | [Übersetzungsoperatoren i18n, d18n und x18n](i18n-operatoren.md#x18n) | `eZi18nOperator` (kernel/common) | site, admin |
| `xml_encode` ([generiert](../../../eng-us/examples/template-operators/xml_encode.md)) | [ezjscore-Operatoren im Detail](ezjscore-operatoren.md#xml_encode) | `ezjscEncodingTemplateFunctions` (Erweiterung ezjscore) | site, admin |
| `xrowmetadata_license` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#xrowmetadata_license) | `xrowMetaDataLicenseOperator` (Erweiterung xrowmetadata) | site, admin |
| `xrowmetadata_license_choices` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#xrowmetadata_license_choices) | `xrowMetaDataLicensePickerOperator` (Erweiterung xrowmetadata) | site, admin |
| `xrowmetadata_license_class_settings` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#xrowmetadata_license_class_settings) | `xrowMetaDataLicensePickerOperator` (Erweiterung xrowmetadata) | site, admin |
| `xrowmetadata_license_enabled` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#xrowmetadata_license_enabled) | `xrowMetaDataLicensePickerOperator` (Erweiterung xrowmetadata) | site, admin |
| `xrowmetadata_license_info` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#xrowmetadata_license_info) | `xrowMetaDataLicensePickerOperator` (Erweiterung xrowmetadata) | site, admin |
| `xrowmetadata_license_inherited` | [Operatoren kleinerer Erweiterungen](operatoren-kleiner-erweiterungen.md#xrowmetadata_license_inherited) | `xrowMetaDataLicensePickerOperator` (Erweiterung xrowmetadata) | site, admin |

## Y

| Operator | Dokumentiert in | Klasse | Siteaccesses |
|---|---|---|---|
| `youtube_id` |  | `sevenxThemesMediaOperators` (Erweiterung sevenx_themes_media) | site, admin |

## Verwandte Seiten

- [Template-Operatoren](README.md)
- [Operatorreferenz für Designer](../../../eng-us/help/designers/templates/operator-reference.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
