---
title: Template-Operatoren
audience: developer, admin
category: examples
order: 0
summary: Jeder registrierte Template-Operator mit seinen Parametern, einem Beispielaufruf und seiner ersten Verwendung in einem ausgelieferten Template.
keywords: Beispiele, Referenz, Template-Operatoren, examples, reference
type: generated
generated: exp:help:examples
---
# Template-Operatoren

Jeder Operator, den der Kernel, seine Bibliotheken und die mitgelieferten Erweiterungen registrieren (das `$eZTemplateOperatorArray` jeder `eztemplateautoload.php` und `settings/template.ini` `[PHP] PHPOperatorList[]`), mit seinen Parametern und einem Beispiel. „Verwendet in“ gibt an, ob ein ausgeliefertes Template ihn verwendet; seine Seite zitiert die erste Verwendung.

243 Operatoren.

| Operator | Klasse | Parameter | In einem ausgelieferten Template verwendet |
|---|---|---|---|
| [abs](abs.md) | eZTemplateArithmeticOperator | 0 |  |
| [action_icon](action_icon.md) | eZWordToImageOperator | ? |  |
| [addcslashes](addcslashes.md) | eZTemplateStringsOperator | ? |  |
| [addslashes](addslashes.md) | eZTemplateStringsOperator | ? |  |
| [alphabet](alphabet.md) | eZAlphabetOperator | ? |  |
| [and](and.md) | eZTemplateLogicOperator | 0 | ja |
| [append](append.md) | eZTemplateArrayOperator | 0 | ja |
| [array](array.md) | eZTemplateArrayOperator | 0 | ja |
| [array_append](array_append.md) | eZTemplateArrayOperator | 0 |  |
| [array_merge](array_merge.md) | eZTemplateArrayOperator | 0 |  |
| [array_prepend](array_prepend.md) | eZTemplateArrayOperator | 0 | ja |
| [array_sum](array_sum.md) | eZTemplateArrayOperator | 0 |  |
| [attribute](attribute.md) | eZTemplateAttributeOperator | 3 | ja |
| [audit_label](audit_label.md) | expAuditTemplateOperator | 0 |  |
| [autolink](autolink.md) | eZAutoLinkOperator | 1 | ja |
| [begins_with](begins_with.md) | eZTemplateArrayOperator | 1 | ja |
| [bin2hex](bin2hex.md) | eZTemplateStringsOperator | ? |  |
| [break](break.md) | eZTemplateStringOperator | 0 | ja |
| [ceil](ceil.md) | eZTemplateArithmeticOperator | 0 |  |
| [choose](choose.md) | eZTemplateLogicOperator | 0 | ja |
| [chr](chr.md) | eZTemplateStringOperator | 0 | ja |
| [chunk_split](chunk_split.md) | eZTemplateStringsOperator | ? |  |
| [class_icon](class_icon.md) | eZWordToImageOperator | ? | ja |
| [classgroup_icon](classgroup_icon.md) | eZWordToImageOperator | ? | ja |
| [compare](compare.md) | eZTemplateArrayOperator | 1 | ja |
| [concat](concat.md) | eZTemplateTextOperator | 0 | ja |
| [cond](cond.md) | eZTemplateControlOperator | 0 | ja |
| [contains](contains.md) | eZTemplateArrayOperator | 1 | ja |
| [content_structure_tree](content_structure_tree.md) | eZContentStructureTreeOperator | 7 |  |
| [convert_uudecode](convert_uudecode.md) | eZTemplateStringsOperator | ? |  |
| [convert_uuencode](convert_uuencode.md) | eZTemplateStringsOperator | ? |  |
| [count](count.md) | eZTemplateArithmeticOperator | 0 | ja |
| [count_chars](count_chars.md) | eZTemplateStringOperator | 0 | ja |
| [count_words](count_words.md) | eZTemplateStringOperator | 0 |  |
| [crc32](crc32.md) | eZTemplateDigestOperator | ? |  |
| [csp_nonce](csp_nonce.md) | expContentSecurityPolicyOperator | 0 |  |
| [currentdate](currentdate.md) | eZTemplateLocaleOperator | 0 | ja |
| [d18n](d18n.md) | eZi18nOperator | 3 | ja |
| [datetime](datetime.md) | eZTemplateLocaleOperator | 2 | ja |
| [dec](dec.md) | eZTemplateArithmeticOperator | 1 | ja |
| [div](div.md) | eZTemplateArithmeticOperator | 0 | ja |
| [downcase](downcase.md) | eZTemplateStringOperator | 0 | ja |
| [dump](dump.md) | eZTemplateAttributeOperator | 3 |  |
| [ends_with](ends_with.md) | eZTemplateArrayOperator | 1 |  |
| [eq](eq.md) | eZTemplateLogicOperator | 0 | ja |
| [exp_demo_mode](exp_demo_mode.md) | expDemoModeOperator | 0 | ja |
| [exp_environment](exp_environment.md) | expEnvironmentOperator | 0 |  |
| [exp_environment_badge](exp_environment_badge.md) | expEnvironmentOperator | 0 | ja |
| [exp_smarty_render](exp_smarty_render.md) | expSmartyOperator | 1 |  |
| [exp_twig_render](exp_twig_render.md) | expTwigOperator | 1 |  |
| [expinfo](expinfo.md) | eZTemplateExpInfoOperator | 0 | ja |
| [explode](explode.md) | eZTemplateArrayOperator | 1 | ja |
| [extract](extract.md) | eZTemplateArrayOperator | 2 | ja |
| [extract_left](extract_left.md) | eZTemplateArrayOperator | 1 | ja |
| [extract_right](extract_right.md) | eZTemplateArrayOperator | 1 | ja |
| [exturl](exturl.md) | eZURLOperator | 2 |  |
| [ezcss](ezcss.md) | ezjscPackerTemplateFunctions | 6 |  |
| [ezcss_load](ezcss_load.md) | ezjscPackerTemplateFunctions | 6 | ja |
| [ezcss_require](ezcss_require.md) | ezjscPackerTemplateFunctions | 6 | ja |
| [ezcssfiles](ezcssfiles.md) | ezjscPackerTemplateFunctions | 3 | ja |
| [ezdesign](ezdesign.md) | eZURLOperator | 2 | ja |
| [ezhttp](ezhttp.md) | eZURLOperator | 2 | ja |
| [ezhttp_hasvariable](ezhttp_hasvariable.md) | eZURLOperator | 2 | ja |
| [ezimage](ezimage.md) | eZURLOperator | 2 | ja |
| [ezini](ezini.md) | eZURLOperator | 2 | ja |
| [ezini_hasvariable](ezini_hasvariable.md) | eZURLOperator | 2 | ja |
| [ezmodule](ezmodule.md) | eZModuleOperator | 1 | ja |
| [ezoe_ini_section](ezoe_ini_section.md) | eZOETemplateUtils | 2 | ja |
| [ezpackage](ezpackage.md) | eZPackageOperator | 2 | ja |
| [ezpreference](ezpreference.md) | eZKernelOperator | 1 | ja |
| [ezroot](ezroot.md) | eZURLOperator | 2 | ja |
| [ezscript](ezscript.md) | ezjscPackerTemplateFunctions | 5 | ja |
| [ezscript_load](ezscript_load.md) | ezjscPackerTemplateFunctions | 5 | ja |
| [ezscript_require](ezscript_require.md) | ezjscPackerTemplateFunctions | 5 | ja |
| [ezscriptfiles](ezscriptfiles.md) | ezjscPackerTemplateFunctions | 3 |  |
| [ezsys](ezsys.md) | eZURLOperator | 2 | ja |
| [eztoc](eztoc.md) | eZTOCOperator | 1 |  |
| [ezurl](ezurl.md) | eZURLOperator | 2 | ja |
| [false](false.md) | eZTemplateLogicOperator | 0 | ja |
| [fetch](fetch.md) | eZTemplateExecuteOperator | 3 | ja |
| [fetch_alias](fetch_alias.md) | eZTemplateExecuteOperator | 2 | ja |
| [first_set](first_set.md) | eZTemplateControlOperator | 0 | ja |
| [flag_icon](flag_icon.md) | eZWordToImageOperator | ? | ja |
| [float](float.md) | eZTemplateArithmeticOperator | 0 |  |
| [floor](floor.md) | eZTemplateArithmeticOperator | 0 | ja |
| [ge](ge.md) | eZTemplateLogicOperator | 1 | ja |
| [get_class](get_class.md) | eZTemplateTypeOperator | 0 | ja |
| [get_type](get_type.md) | eZTemplateTypeOperator | 0 |  |
| [gettime](gettime.md) | eZTemplateLocaleOperator | 1 |  |
| [gt](gt.md) | eZTemplateLogicOperator | 1 | ja |
| [has_access_to_limitation](has_access_to_limitation.md) | ezjscAccessTemplateFunctions | 4 |  |
| [has_any_role](has_any_role.md) | eZTemplateRoleOperator | 3 |  |
| [has_policy](has_policy.md) | eZTemplateRoleOperator | 3 |  |
| [has_policy_by_user](has_policy_by_user.md) | eZTemplateRoleOperator | 3 |  |
| [has_role](has_role.md) | eZTemplateRoleOperator | 2 |  |
| [has_role_by_user](has_role_by_user.md) | eZTemplateRoleOperator | 2 |  |
| [has_role_by_user_id](has_role_by_user_id.md) | eZTemplateRoleOperator | 2 |  |
| [has_role_id](has_role_id.md) | eZTemplateRoleOperator | 2 |  |
| [hash](hash.md) | eZTemplateArrayOperator | 0 | ja |
| [hebrev](hebrev.md) | eZTemplateStringsOperator | ? |  |
| [hex2bin](hex2bin.md) | eZTemplateStringsOperator | ? |  |
| [html_entity_decode](html_entity_decode.md) | eZTemplateStringsOperator | ? |  |
| [htmlentities](htmlentities.md) | eZTemplateStringsOperator | ? |  |
| [htmlspecialchars_decode](htmlspecialchars_decode.md) | eZTemplateStringsOperator | ? |  |
| [i18n](i18n.md) | eZi18nOperator | 3 | ja |
| [icon](icon.md) | eZWordToImageOperator | ? | ja |
| [icon_info](icon_info.md) | eZWordToImageOperator | ? |  |
| [image](image.md) | eZTemplateImageOperator | 0 |  |
| [imagefile](imagefile.md) | eZTemplateImageOperator | 2 |  |
| [implode](implode.md) | eZTemplateArrayOperator | 1 | ja |
| [inc](inc.md) | eZTemplateArithmeticOperator | 1 | ja |
| [indent](indent.md) | eZTemplateTextOperator | 3 | ja |
| [insert](insert.md) | eZTemplateArrayOperator | 2 |  |
| [installation_name](installation_name.md) | ExpInstallationOperator | 1 |  |
| [int](int.md) | eZTemplateArithmeticOperator | 0 | ja |
| [is_array](is_array.md) | eZTemplateTypeOperator | 0 | ja |
| [is_boolean](is_boolean.md) | eZTemplateTypeOperator | 0 | ja |
| [is_class](is_class.md) | eZTemplateTypeOperator | 0 |  |
| [is_float](is_float.md) | eZTemplateTypeOperator | 0 |  |
| [is_integer](is_integer.md) | eZTemplateTypeOperator | 0 |  |
| [is_null](is_null.md) | eZTemplateTypeOperator | 0 | ja |
| [is_numeric](is_numeric.md) | eZTemplateTypeOperator | 0 | ja |
| [is_object](is_object.md) | eZTemplateTypeOperator | 0 | ja |
| [is_production_system](is_production_system.md) | ExpInstallationOperator | 0 |  |
| [is_set](is_set.md) | eZTemplateTypeOperator | 0 | ja |
| [is_string](is_string.md) | eZTemplateTypeOperator | 0 | ja |
| [is_unset](is_unset.md) | eZTemplateTypeOperator | 0 | ja |
| [json_encode](json_encode.md) | ezjscEncodingTemplateFunctions | 1 | ja |
| [l10n](l10n.md) | eZTemplateLocaleOperator | 3 | ja |
| [language_switcher](language_switcher.md) | ezpLanguageSwitcherOperator | 1 |  |
| [lcfirst](lcfirst.md) | eZTemplateStringsOperator | ? |  |
| [le](le.md) | eZTemplateLogicOperator | 1 | ja |
| [levenshtein](levenshtein.md) | eZTemplateStringsOperator | ? |  |
| [locale](locale.md) | eZTemplateLocaleOperator | 0 | ja |
| [lt](lt.md) | eZTemplateLogicOperator | 1 | ja |
| [ltrim](ltrim.md) | eZTemplateStringsOperator | ? |  |
| [makedate](makedate.md) | eZTemplateLocaleOperator | 4 |  |
| [maketime](maketime.md) | eZTemplateLocaleOperator | 7 |  |
| [max](max.md) | eZTemplateArithmeticOperator | 0 | ja |
| [md5](md5.md) | eZTemplateDigestOperator | ? |  |
| [merge](merge.md) | eZTemplateArrayOperator | 0 | ja |
| [metaphone](metaphone.md) | eZTemplateStringsOperator | ? |  |
| [mimetype_icon](mimetype_icon.md) | eZWordToImageOperator | ? | ja |
| [min](min.md) | eZTemplateArithmeticOperator | 0 | ja |
| [mod](mod.md) | eZTemplateArithmeticOperator | 0 | ja |
| [module_params](module_params.md) | eZModuleParamsOperator | 0 | ja |
| [month_overview](month_overview.md) | eZDateOperatorCollection | 3 |  |
| [mul](mul.md) | eZTemplateArithmeticOperator | 0 | ja |
| [ne](ne.md) | eZTemplateLogicOperator | 0 | ja |
| [nl2br](nl2br.md) | eZTemplateNl2BrOperator | 0 | ja |
| [node_encode](node_encode.md) | ezjscEncodingTemplateFunctions | 3 |  |
| [not](not.md) | eZTemplateLogicOperator | 0 | ja |
| [null](null.md) | eZTemplateLogicOperator | 0 | ja |
| [number_format](number_format.md) | eZTemplateStringsOperator | ? |  |
| [or](or.md) | eZTemplateLogicOperator | 0 | ja |
| [ord](ord.md) | eZTemplateStringOperator | 0 |  |
| [pad](pad.md) | eZTemplateStringOperator | 2 |  |
| [pdf](pdf.md) | eZPDF | 1 | ja |
| [prepend](prepend.md) | eZTemplateArrayOperator | 1 | ja |
| [quoted_printable_decode](quoted_printable_decode.md) | eZTemplateStringsOperator | ? |  |
| [quoted_printable_encode](quoted_printable_encode.md) | eZTemplateStringsOperator | ? |  |
| [quotemeta](quotemeta.md) | eZTemplateStringsOperator | ? |  |
| [rand](rand.md) | eZTemplateArithmeticOperator | 0 |  |
| [rawurldecode](rawurldecode.md) | eZTemplatePHPOperator | ? |  |
| [rawurlencode](rawurlencode.md) | eZTemplatePHPOperator | ? | ja |
| [remove](remove.md) | eZTemplateArrayOperator | 2 |  |
| [repeat](repeat.md) | eZTemplateArrayOperator | 1 | ja |
| [replace](replace.md) | eZTemplateArrayOperator | 2 |  |
| [reverse](reverse.md) | eZTemplateArrayOperator | 0 |  |
| [ristring](ristring.md) | eZTemplateStringsOperator | ? | ja |
| [roman](roman.md) | eZTemplateArithmeticOperator | 1 |  |
| [rot13](rot13.md) | eZTemplateDigestOperator | ? |  |
| [round](round.md) | eZTemplateArithmeticOperator | 0 | ja |
| [rstring](rstring.md) | eZTemplateStringsOperator | ? |  |
| [rtrim](rtrim.md) | eZTemplateStringsOperator | ? |  |
| [shorten](shorten.md) | eZTemplateStringOperator | 3 | ja |
| [si](si.md) | eZTemplateUnitOperator | 5 | ja |
| [similar_text](similar_text.md) | eZTemplateStringsOperator | ? |  |
| [simpletags](simpletags.md) | eZSimpleTagsOperator | 1 | ja |
| [simplify](simplify.md) | eZTemplateStringOperator | 1 |  |
| [siteaccess_menu](siteaccess_menu.md) | ezpSiteAccessMenuOperator | 1 | ja |
| [siteaccess_menu_key](siteaccess_menu_key.md) | ezpSiteAccessMenuOperator | 0 | ja |
| [siteaccess_url](siteaccess_url.md) | ezpSiteAccessURLOperator | 1 | ja |
| [soundex](soundex.md) | eZTemplateStringsOperator | ? |  |
| [sprintf](sprintf.md) | eZTemplateStringsOperator | ? |  |
| [str_contains](str_contains.md) | eZTemplateStringsOperator | ? |  |
| [str_ends_with](str_ends_with.md) | eZTemplateStringsOperator | ? |  |
| [str_getcsv](str_getcsv.md) | eZTemplateStringsOperator | ? |  |
| [str_shuffle](str_shuffle.md) | eZTemplateStringsOperator | ? |  |
| [str_split](str_split.md) | eZTemplateStringsOperator | ? |  |
| [str_starts_with](str_starts_with.md) | eZTemplateStringsOperator | ? |  |
| [str_word_count](str_word_count.md) | eZTemplateStringsOperator | ? |  |
| [strcasecmp](strcasecmp.md) | eZTemplateStringsOperator | ? |  |
| [strcmp](strcmp.md) | eZTemplateStringsOperator | ? |  |
| [strcoll](strcoll.md) | eZTemplateStringsOperator | ? |  |
| [strcspn](strcspn.md) | eZTemplateStringsOperator | ? |  |
| [strip_tags](strip_tags.md) | eZTemplateStringsOperator | ? | ja |
| [stripcslashes](stripcslashes.md) | eZTemplateStringsOperator | ? |  |
| [stripos](stripos.md) | eZTemplateStringsOperator | ? |  |
| [stripslashes](stripslashes.md) | eZTemplateStringsOperator | ? |  |
| [stristr](stristr.md) | eZTemplateStringsOperator | ? |  |
| [strlen](strlen.md) | eZTemplateStringsOperator | ? |  |
| [strnatcasecmp](strnatcasecmp.md) | eZTemplateStringsOperator | ? |  |
| [strnatcmp](strnatcmp.md) | eZTemplateStringsOperator | ? |  |
| [strncasecmp](strncasecmp.md) | eZTemplateStringsOperator | ? |  |
| [strncmp](strncmp.md) | eZTemplateStringsOperator | ? |  |
| [strpbrk](strpbrk.md) | eZTemplateStringsOperator | ? |  |
| [strpos](strpos.md) | eZTemplateStringsOperator | ? |  |
| [strrchr](strrchr.md) | eZTemplateStringsOperator | ? |  |
| [strripos](strripos.md) | eZTemplateStringsOperator | ? |  |
| [strrpos](strrpos.md) | eZTemplateStringsOperator | ? |  |
| [strspn](strspn.md) | eZTemplateStringsOperator | ? |  |
| [strstr](strstr.md) | eZTemplateStringsOperator | ? |  |
| [strtok](strtok.md) | eZTemplateStringsOperator | ? |  |
| [strtr](strtr.md) | eZTemplateStringsOperator | ? |  |
| [sub](sub.md) | eZTemplateArithmeticOperator | 0 | ja |
| [substr](substr.md) | eZTemplateStringsOperator | ? |  |
| [substr_compare](substr_compare.md) | eZTemplateStringsOperator | ? |  |
| [substr_count](substr_count.md) | eZTemplateStringsOperator | ? |  |
| [substr_replace](substr_replace.md) | eZTemplateStringsOperator | ? |  |
| [sum](sum.md) | eZTemplateArithmeticOperator | 0 | ja |
| [texttoimage](texttoimage.md) | eZTemplateImageOperator | 12 |  |
| [topmenu](topmenu.md) | eZTopMenuOperator | 2 | ja |
| [transfer_actions](transfer_actions.md) | expKernelTransferOperator | 3 | ja |
| [transfer_related](transfer_related.md) | expKernelTransferOperator | 1 |  |
| [transfer_tool](transfer_tool.md) | expKernelTransferOperator | 1 |  |
| [transfer_tools](transfer_tools.md) | expKernelTransferOperator | 1 | ja |
| [treemenu](treemenu.md) | eZTreeMenuOperator | 9 |  |
| [trim](trim.md) | eZTemplateStringOperator | 1 | ja |
| [true](true.md) | eZTemplateLogicOperator | 0 | ja |
| [unique](unique.md) | eZTemplateArrayOperator | 0 | ja |
| [upcase](upcase.md) | eZTemplateStringOperator | 0 | ja |
| [upfirst](upfirst.md) | eZTemplateStringOperator | 0 | ja |
| [upword](upword.md) | eZTemplateStringOperator | 0 |  |
| [urldecode](urldecode.md) | eZTemplatePHPOperator | ? |  |
| [urlencode](urlencode.md) | eZTemplatePHPOperator | ? | ja |
| [view_parameter](view_parameter.md) | expViewParameterOperator | 0 | ja |
| [vsprintf](vsprintf.md) | eZTemplateStringsOperator | ? |  |
| [wash](wash.md) | eZTemplateStringOperator | 1 | ja |
| [wordtoimage](wordtoimage.md) | eZWordToImageOperator | ? | ja |
| [wrap](wrap.md) | eZTemplateStringOperator | 3 | ja |
| [x18n](x18n.md) | eZi18nOperator | 4 |  |
| [xml_encode](xml_encode.md) | ezjscEncodingTemplateFunctions | 1 |  |

- [Alle Beispiele](../README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezoe/autoloads/eztemplateautoload.php`, `kernel/common/eztemplateautoload.php`, `kernel/private/eztemplate/eztemplateautoload.php`, `lib/ezpdf/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateautoload.php`, `lib/smarty/classes/eztemplateautoload.php`, `lib/twig/classes/eztemplateautoload.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
