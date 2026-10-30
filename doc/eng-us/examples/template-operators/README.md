---
title: Template operators
audience: developer, admin
category: examples
order: 0
summary: Every registered template operator with its parameters, an example call and its first use in a shipped template.
keywords: Template operators, examples, reference
type: generated
generated: exp:help:examples
---
# Template operators

Every operator the kernel, its libraries and the bundled extensions register (the `$eZTemplateOperatorArray` of each `eztemplateautoload.php`, and `settings/template.ini` `[PHP] PHPOperatorList[]`), with its parameters and an example. "Used in" says whether a shipped template uses it; its page quotes the first use.

241 operators.

| Operator | Class | Parameters | Used in a shipped template |
|---|---|---|---|
| [abs](abs.md) | eZTemplateArithmeticOperator | 0 |  |
| [action_icon](action_icon.md) | eZWordToImageOperator | ? |  |
| [addcslashes](addcslashes.md) | eZTemplateStringsOperator | ? |  |
| [addslashes](addslashes.md) | eZTemplateStringsOperator | ? |  |
| [alphabet](alphabet.md) | eZAlphabetOperator | ? |  |
| [and](and.md) | eZTemplateLogicOperator | 0 | yes |
| [append](append.md) | eZTemplateArrayOperator | 0 | yes |
| [array](array.md) | eZTemplateArrayOperator | 0 | yes |
| [array_append](array_append.md) | eZTemplateArrayOperator | 0 |  |
| [array_merge](array_merge.md) | eZTemplateArrayOperator | 0 |  |
| [array_prepend](array_prepend.md) | eZTemplateArrayOperator | 0 | yes |
| [array_sum](array_sum.md) | eZTemplateArrayOperator | 0 |  |
| [attribute](attribute.md) | eZTemplateAttributeOperator | 3 | yes |
| [audit_label](audit_label.md) | expAuditTemplateOperator | 0 |  |
| [autolink](autolink.md) | eZAutoLinkOperator | 1 | yes |
| [begins_with](begins_with.md) | eZTemplateArrayOperator | 1 | yes |
| [bin2hex](bin2hex.md) | eZTemplateStringsOperator | ? |  |
| [break](break.md) | eZTemplateStringOperator | 0 | yes |
| [ceil](ceil.md) | eZTemplateArithmeticOperator | 0 |  |
| [choose](choose.md) | eZTemplateLogicOperator | 0 | yes |
| [chr](chr.md) | eZTemplateStringOperator | 0 | yes |
| [chunk_split](chunk_split.md) | eZTemplateStringsOperator | ? |  |
| [class_icon](class_icon.md) | eZWordToImageOperator | ? | yes |
| [classgroup_icon](classgroup_icon.md) | eZWordToImageOperator | ? | yes |
| [compare](compare.md) | eZTemplateArrayOperator | 1 | yes |
| [concat](concat.md) | eZTemplateTextOperator | 0 | yes |
| [cond](cond.md) | eZTemplateControlOperator | 0 | yes |
| [contains](contains.md) | eZTemplateArrayOperator | 1 | yes |
| [content_structure_tree](content_structure_tree.md) | eZContentStructureTreeOperator | 7 |  |
| [convert_uudecode](convert_uudecode.md) | eZTemplateStringsOperator | ? |  |
| [convert_uuencode](convert_uuencode.md) | eZTemplateStringsOperator | ? |  |
| [count](count.md) | eZTemplateArithmeticOperator | 0 | yes |
| [count_chars](count_chars.md) | eZTemplateStringOperator | 0 | yes |
| [count_words](count_words.md) | eZTemplateStringOperator | 0 |  |
| [crc32](crc32.md) | eZTemplateDigestOperator | ? |  |
| [csp_nonce](csp_nonce.md) | expContentSecurityPolicyOperator | 0 |  |
| [currentdate](currentdate.md) | eZTemplateLocaleOperator | 0 | yes |
| [d18n](d18n.md) | eZi18nOperator | 3 | yes |
| [datetime](datetime.md) | eZTemplateLocaleOperator | 2 | yes |
| [dec](dec.md) | eZTemplateArithmeticOperator | 1 | yes |
| [div](div.md) | eZTemplateArithmeticOperator | 0 | yes |
| [downcase](downcase.md) | eZTemplateStringOperator | 0 | yes |
| [dump](dump.md) | eZTemplateAttributeOperator | 3 |  |
| [ends_with](ends_with.md) | eZTemplateArrayOperator | 1 |  |
| [eq](eq.md) | eZTemplateLogicOperator | 0 | yes |
| [exp_demo_mode](exp_demo_mode.md) | expDemoModeOperator | 0 | yes |
| [exp_environment](exp_environment.md) | expEnvironmentOperator | 0 |  |
| [exp_environment_badge](exp_environment_badge.md) | expEnvironmentOperator | 0 | yes |
| [expinfo](expinfo.md) | eZTemplateExpInfoOperator | 0 | yes |
| [explode](explode.md) | eZTemplateArrayOperator | 1 | yes |
| [extract](extract.md) | eZTemplateArrayOperator | 2 | yes |
| [extract_left](extract_left.md) | eZTemplateArrayOperator | 1 | yes |
| [extract_right](extract_right.md) | eZTemplateArrayOperator | 1 | yes |
| [exturl](exturl.md) | eZURLOperator | 2 |  |
| [ezcss](ezcss.md) | ezjscPackerTemplateFunctions | 6 |  |
| [ezcss_load](ezcss_load.md) | ezjscPackerTemplateFunctions | 6 | yes |
| [ezcss_require](ezcss_require.md) | ezjscPackerTemplateFunctions | 6 | yes |
| [ezcssfiles](ezcssfiles.md) | ezjscPackerTemplateFunctions | 3 | yes |
| [ezdesign](ezdesign.md) | eZURLOperator | 2 | yes |
| [ezhttp](ezhttp.md) | eZURLOperator | 2 | yes |
| [ezhttp_hasvariable](ezhttp_hasvariable.md) | eZURLOperator | 2 | yes |
| [ezimage](ezimage.md) | eZURLOperator | 2 | yes |
| [ezini](ezini.md) | eZURLOperator | 2 | yes |
| [ezini_hasvariable](ezini_hasvariable.md) | eZURLOperator | 2 | yes |
| [ezmodule](ezmodule.md) | eZModuleOperator | 1 | yes |
| [ezoe_ini_section](ezoe_ini_section.md) | eZOETemplateUtils | 2 | yes |
| [ezpackage](ezpackage.md) | eZPackageOperator | 2 | yes |
| [ezpreference](ezpreference.md) | eZKernelOperator | 1 | yes |
| [ezroot](ezroot.md) | eZURLOperator | 2 | yes |
| [ezscript](ezscript.md) | ezjscPackerTemplateFunctions | 5 | yes |
| [ezscript_load](ezscript_load.md) | ezjscPackerTemplateFunctions | 5 | yes |
| [ezscript_require](ezscript_require.md) | ezjscPackerTemplateFunctions | 5 | yes |
| [ezscriptfiles](ezscriptfiles.md) | ezjscPackerTemplateFunctions | 3 |  |
| [ezsys](ezsys.md) | eZURLOperator | 2 | yes |
| [eztoc](eztoc.md) | eZTOCOperator | 1 |  |
| [ezurl](ezurl.md) | eZURLOperator | 2 | yes |
| [false](false.md) | eZTemplateLogicOperator | 0 | yes |
| [fetch](fetch.md) | eZTemplateExecuteOperator | 3 | yes |
| [fetch_alias](fetch_alias.md) | eZTemplateExecuteOperator | 2 | yes |
| [first_set](first_set.md) | eZTemplateControlOperator | 0 | yes |
| [flag_icon](flag_icon.md) | eZWordToImageOperator | ? | yes |
| [float](float.md) | eZTemplateArithmeticOperator | 0 |  |
| [floor](floor.md) | eZTemplateArithmeticOperator | 0 | yes |
| [ge](ge.md) | eZTemplateLogicOperator | 1 | yes |
| [get_class](get_class.md) | eZTemplateTypeOperator | 0 | yes |
| [get_type](get_type.md) | eZTemplateTypeOperator | 0 |  |
| [gettime](gettime.md) | eZTemplateLocaleOperator | 1 |  |
| [gt](gt.md) | eZTemplateLogicOperator | 1 | yes |
| [has_access_to_limitation](has_access_to_limitation.md) | ezjscAccessTemplateFunctions | 4 |  |
| [has_any_role](has_any_role.md) | eZTemplateRoleOperator | 3 |  |
| [has_policy](has_policy.md) | eZTemplateRoleOperator | 3 |  |
| [has_policy_by_user](has_policy_by_user.md) | eZTemplateRoleOperator | 3 |  |
| [has_role](has_role.md) | eZTemplateRoleOperator | 2 |  |
| [has_role_by_user](has_role_by_user.md) | eZTemplateRoleOperator | 2 |  |
| [has_role_by_user_id](has_role_by_user_id.md) | eZTemplateRoleOperator | 2 |  |
| [has_role_id](has_role_id.md) | eZTemplateRoleOperator | 2 |  |
| [hash](hash.md) | eZTemplateArrayOperator | 0 | yes |
| [hebrev](hebrev.md) | eZTemplateStringsOperator | ? |  |
| [hex2bin](hex2bin.md) | eZTemplateStringsOperator | ? |  |
| [html_entity_decode](html_entity_decode.md) | eZTemplateStringsOperator | ? |  |
| [htmlentities](htmlentities.md) | eZTemplateStringsOperator | ? |  |
| [htmlspecialchars_decode](htmlspecialchars_decode.md) | eZTemplateStringsOperator | ? |  |
| [i18n](i18n.md) | eZi18nOperator | 3 | yes |
| [icon](icon.md) | eZWordToImageOperator | ? | yes |
| [icon_info](icon_info.md) | eZWordToImageOperator | ? |  |
| [image](image.md) | eZTemplateImageOperator | 0 |  |
| [imagefile](imagefile.md) | eZTemplateImageOperator | 2 |  |
| [implode](implode.md) | eZTemplateArrayOperator | 1 | yes |
| [inc](inc.md) | eZTemplateArithmeticOperator | 1 | yes |
| [indent](indent.md) | eZTemplateTextOperator | 3 | yes |
| [insert](insert.md) | eZTemplateArrayOperator | 2 |  |
| [installation_name](installation_name.md) | ExpInstallationOperator | 1 |  |
| [int](int.md) | eZTemplateArithmeticOperator | 0 | yes |
| [is_array](is_array.md) | eZTemplateTypeOperator | 0 | yes |
| [is_boolean](is_boolean.md) | eZTemplateTypeOperator | 0 | yes |
| [is_class](is_class.md) | eZTemplateTypeOperator | 0 |  |
| [is_float](is_float.md) | eZTemplateTypeOperator | 0 |  |
| [is_integer](is_integer.md) | eZTemplateTypeOperator | 0 |  |
| [is_null](is_null.md) | eZTemplateTypeOperator | 0 | yes |
| [is_numeric](is_numeric.md) | eZTemplateTypeOperator | 0 | yes |
| [is_object](is_object.md) | eZTemplateTypeOperator | 0 | yes |
| [is_production_system](is_production_system.md) | ExpInstallationOperator | 0 |  |
| [is_set](is_set.md) | eZTemplateTypeOperator | 0 | yes |
| [is_string](is_string.md) | eZTemplateTypeOperator | 0 | yes |
| [is_unset](is_unset.md) | eZTemplateTypeOperator | 0 | yes |
| [json_encode](json_encode.md) | ezjscEncodingTemplateFunctions | 1 | yes |
| [l10n](l10n.md) | eZTemplateLocaleOperator | 3 | yes |
| [language_switcher](language_switcher.md) | ezpLanguageSwitcherOperator | 1 |  |
| [lcfirst](lcfirst.md) | eZTemplateStringsOperator | ? |  |
| [le](le.md) | eZTemplateLogicOperator | 1 | yes |
| [levenshtein](levenshtein.md) | eZTemplateStringsOperator | ? |  |
| [locale](locale.md) | eZTemplateLocaleOperator | 0 | yes |
| [lt](lt.md) | eZTemplateLogicOperator | 1 | yes |
| [ltrim](ltrim.md) | eZTemplateStringsOperator | ? |  |
| [makedate](makedate.md) | eZTemplateLocaleOperator | 4 |  |
| [maketime](maketime.md) | eZTemplateLocaleOperator | 7 |  |
| [max](max.md) | eZTemplateArithmeticOperator | 0 | yes |
| [md5](md5.md) | eZTemplateDigestOperator | ? |  |
| [merge](merge.md) | eZTemplateArrayOperator | 0 | yes |
| [metaphone](metaphone.md) | eZTemplateStringsOperator | ? |  |
| [mimetype_icon](mimetype_icon.md) | eZWordToImageOperator | ? | yes |
| [min](min.md) | eZTemplateArithmeticOperator | 0 | yes |
| [mod](mod.md) | eZTemplateArithmeticOperator | 0 | yes |
| [module_params](module_params.md) | eZModuleParamsOperator | 0 | yes |
| [month_overview](month_overview.md) | eZDateOperatorCollection | 3 |  |
| [mul](mul.md) | eZTemplateArithmeticOperator | 0 | yes |
| [ne](ne.md) | eZTemplateLogicOperator | 0 | yes |
| [nl2br](nl2br.md) | eZTemplateNl2BrOperator | 0 | yes |
| [node_encode](node_encode.md) | ezjscEncodingTemplateFunctions | 3 |  |
| [not](not.md) | eZTemplateLogicOperator | 0 | yes |
| [null](null.md) | eZTemplateLogicOperator | 0 | yes |
| [number_format](number_format.md) | eZTemplateStringsOperator | ? |  |
| [or](or.md) | eZTemplateLogicOperator | 0 | yes |
| [ord](ord.md) | eZTemplateStringOperator | 0 |  |
| [pad](pad.md) | eZTemplateStringOperator | 2 |  |
| [pdf](pdf.md) | eZPDF | 1 | yes |
| [prepend](prepend.md) | eZTemplateArrayOperator | 1 | yes |
| [quoted_printable_decode](quoted_printable_decode.md) | eZTemplateStringsOperator | ? |  |
| [quoted_printable_encode](quoted_printable_encode.md) | eZTemplateStringsOperator | ? |  |
| [quotemeta](quotemeta.md) | eZTemplateStringsOperator | ? |  |
| [rand](rand.md) | eZTemplateArithmeticOperator | 0 |  |
| [rawurldecode](rawurldecode.md) | eZTemplatePHPOperator | ? |  |
| [rawurlencode](rawurlencode.md) | eZTemplatePHPOperator | ? | yes |
| [remove](remove.md) | eZTemplateArrayOperator | 2 |  |
| [repeat](repeat.md) | eZTemplateArrayOperator | 1 | yes |
| [replace](replace.md) | eZTemplateArrayOperator | 2 |  |
| [reverse](reverse.md) | eZTemplateArrayOperator | 0 |  |
| [ristring](ristring.md) | eZTemplateStringsOperator | ? | yes |
| [roman](roman.md) | eZTemplateArithmeticOperator | 1 |  |
| [rot13](rot13.md) | eZTemplateDigestOperator | ? |  |
| [round](round.md) | eZTemplateArithmeticOperator | 0 | yes |
| [rstring](rstring.md) | eZTemplateStringsOperator | ? |  |
| [rtrim](rtrim.md) | eZTemplateStringsOperator | ? |  |
| [shorten](shorten.md) | eZTemplateStringOperator | 3 | yes |
| [si](si.md) | eZTemplateUnitOperator | 5 | yes |
| [similar_text](similar_text.md) | eZTemplateStringsOperator | ? |  |
| [simpletags](simpletags.md) | eZSimpleTagsOperator | 1 | yes |
| [simplify](simplify.md) | eZTemplateStringOperator | 1 |  |
| [siteaccess_menu](siteaccess_menu.md) | ezpSiteAccessMenuOperator | 1 | yes |
| [siteaccess_menu_key](siteaccess_menu_key.md) | ezpSiteAccessMenuOperator | 0 | yes |
| [siteaccess_url](siteaccess_url.md) | ezpSiteAccessURLOperator | 1 | yes |
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
| [strip_tags](strip_tags.md) | eZTemplateStringsOperator | ? | yes |
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
| [sub](sub.md) | eZTemplateArithmeticOperator | 0 | yes |
| [substr](substr.md) | eZTemplateStringsOperator | ? |  |
| [substr_compare](substr_compare.md) | eZTemplateStringsOperator | ? |  |
| [substr_count](substr_count.md) | eZTemplateStringsOperator | ? |  |
| [substr_replace](substr_replace.md) | eZTemplateStringsOperator | ? |  |
| [sum](sum.md) | eZTemplateArithmeticOperator | 0 | yes |
| [texttoimage](texttoimage.md) | eZTemplateImageOperator | 12 |  |
| [topmenu](topmenu.md) | eZTopMenuOperator | 2 | yes |
| [transfer_actions](transfer_actions.md) | expKernelTransferOperator | 3 | yes |
| [transfer_related](transfer_related.md) | expKernelTransferOperator | 1 |  |
| [transfer_tool](transfer_tool.md) | expKernelTransferOperator | 1 |  |
| [transfer_tools](transfer_tools.md) | expKernelTransferOperator | 1 | yes |
| [treemenu](treemenu.md) | eZTreeMenuOperator | 9 |  |
| [trim](trim.md) | eZTemplateStringOperator | 1 | yes |
| [true](true.md) | eZTemplateLogicOperator | 0 | yes |
| [unique](unique.md) | eZTemplateArrayOperator | 0 | yes |
| [upcase](upcase.md) | eZTemplateStringOperator | 0 | yes |
| [upfirst](upfirst.md) | eZTemplateStringOperator | 0 | yes |
| [upword](upword.md) | eZTemplateStringOperator | 0 |  |
| [urldecode](urldecode.md) | eZTemplatePHPOperator | ? |  |
| [urlencode](urlencode.md) | eZTemplatePHPOperator | ? | yes |
| [view_parameter](view_parameter.md) | expViewParameterOperator | 0 | yes |
| [vsprintf](vsprintf.md) | eZTemplateStringsOperator | ? |  |
| [wash](wash.md) | eZTemplateStringOperator | 1 | yes |
| [wordtoimage](wordtoimage.md) | eZWordToImageOperator | ? | yes |
| [wrap](wrap.md) | eZTemplateStringOperator | 3 | yes |
| [x18n](x18n.md) | eZi18nOperator | 4 |  |
| [xml_encode](xml_encode.md) | ezjscEncodingTemplateFunctions | 1 |  |

- [All examples](../README.md)

---

Generated by `./console exp:help:examples` from `extension/ezjscore/autoloads/eztemplateautoload.php`, `extension/ezoe/autoloads/eztemplateautoload.php`, `kernel/common/eztemplateautoload.php`, `kernel/private/eztemplate/eztemplateautoload.php`, `lib/ezpdf/classes/eztemplateautoload.php`, `lib/eztemplate/classes/eztemplateautoload.php`. Do not edit this page: change the source and generate again ([how](../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../help/free-documentation.md).
