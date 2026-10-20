---
title: Template operator (Interpreter plugin)
audience: developer
category: standards
order: 313
summary: A class that adds operators to the template language: it lists them in operatorList() and computes them in modify(). 321 instances in Exponential.
keywords: design pattern, template-operator, plugins, pattern catalogue
type: catalogue
---
# Template operator (Interpreter plugin)

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

A class that adds operators to the template language: it lists them in operatorList() and computes them in modify().

| | |
|---|---|
| Group | Plugins |
| Instances | 321 |
| Recognized by | Registered in an eztemplateautoload.php ($eZTemplateOperatorArray[]), or has operatorList() and modify(). |
| Extension point | autoloads/eztemplateautoload.php of an extension, enabled by site.ini/template.ini [TemplateSettings] ExtensionAutoloadPath[]. |

## Classes by area

### extension/bcwebsitestatistics (1)

[All patterns of extension/bcwebsitestatistics](area-extension-bcwebsitestatistics.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `BCWebsiteStatisticsOperators` | class | `extension/bcwebsitestatistics/autoloads/bcwebsitestatisticsoperators.php:30` | – | – |

### extension/cjw_newsletter (2)

[All patterns of extension/cjw_newsletter](area-extension-cjw-newsletter.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `CjwNewsletterOperators` | class | `extension/cjw_newsletter/autoloads/cjwnewsletteroperators.php:21` | php preg_replace \| str_replace tpl operator | – |
| `CjwNewsletterRenderingOperators` | class | `extension/cjw_newsletter/classes/rendering/cjwnewsletterrenderingoperators.php:24` | The template operators of the newsletter rendering (cjw_newsletter 4.2.0): | – |

### extension/enhancedezbinaryfile (1)

[All patterns of extension/enhancedezbinaryfile](area-extension-enhancedezbinaryfile.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `TemplateParseXMLOperator` | class | `extension/enhancedezbinaryfile/autoloads/templateparsexmloperator.php:18` | – | – |

### extension/explayouts (1)

[All patterns of extension/explayouts](area-extension-explayouts.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ExplBlockOperator` | class | `extension/explayouts/classes/explblockoperator.php:13` | Template operators for {explblock} extraction. | – |

### extension/explayouts_ui (1)

[All patterns of extension/explayouts_ui](area-extension-explayouts-ui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expLayoutsUiTemplateOperators` | class | `extension/explayouts_ui/classes/explayoutsuitemplateoperators.php:11` | Template operators of explayouts_ui. | – |

### extension/expui (1)

[All patterns of extension/expui](area-extension-expui.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expUITemplateOperators` | class | `extension/expui/classes/expuitemplateoperators.php:17` | – | – |

### extension/ezdemo (4)

[All patterns of extension/ezdemo](area-extension-ezdemo.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZArchive` | class | `extension/ezdemo/autoloads/ezarchive.php:28` | – | – |
| `eZKeywordList` | class | `extension/ezdemo/autoloads/ezkeywordlist.php:28` | – | – |
| `eZPageData` | class | `extension/ezdemo/autoloads/ezpagedata.php:40` | – | – |
| `eZTagCloud` | class | `extension/ezdemo/autoloads/eztagcloud.php:28` | – | – |

### extension/ezfind (1)

[All patterns of extension/ezfind](area-extension-ezfind.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezfTemplateOperators` | class | `extension/ezfind/classes/ezfTemplateOperators.php:13` | Operators for eZFind | – |

### extension/ezflow (5)

[All patterns of extension/ezflow](area-extension-ezflow.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZFeedReader` | class | `extension/ezflow/autoloads/ezfeedreader.php:32` | eZFeedReader class impelement feedreader tpl operator methods | – |
| `eZJSON` | class | `extension/ezflow/autoloads/ezjson.php:32` | eZJSON class impelement json tpl operator methods | – |
| `eZPageLink` | class | `extension/ezflow/autoloads/ezpagelink.php:32` | eZPageLink class implements pagelink tpl operator methods | – |
| `eZRed5StreamListOperator` | class | `extension/ezflow/autoloads/ezred5streamlist.php:32` | eZRed5StreamListOperator class impelement red5list tpl operator methods | – |
| `eZUnserialize` | class | `extension/ezflow/autoloads/ezunserialize.php:32` | eZUnserialize class impelement unserialize tpl operator methods | – |

### extension/ezjscore (3)

[All patterns of extension/ezjscore](area-extension-ezjscore.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezjscAccessTemplateFunctions` | class | `extension/ezjscore/autoloads/ezjscaccesstemplatefunctions.php:40` | Custom has access to call that also lets you check that user has access to provided limitation(s) | – |
| `ezjscEncodingTemplateFunctions` | class | `extension/ezjscore/autoloads/ezjscencodingtemplatefunctions.php:44` | ezjscAjaxContent related template operators | – |
| `ezjscPackerTemplateFunctions` | class | `extension/ezjscore/autoloads/ezjscpackertemplatefunctions.php:86` | ezjsPacker related template operators For merging and packing javascript or stylesheet files together to reduce size and number of files(as in reduce… | – |

### extension/ezoe (1)

[All patterns of extension/ezoe](area-extension-ezoe.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZOETemplateUtils` | class | `extension/ezoe/autoloads/ezoetemplateutils.php:37` | – | – |

### extension/ezstarrating (1)

[All patterns of extension/ezstarrating](area-extension-ezstarrating.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezsrTemplateOperators` | class | `extension/ezstarrating/autoloads/ezsrtemplateoperators.php:27` | – | – |

### extension/eztags (2)

[All patterns of extension/eztags](area-extension-eztags.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTagsCloud` | class | `extension/eztags/autoloads/eztagscloud.php:6` | eZTagsCloud class implements eztagscloud template operator method | – |
| `eZTagsTemplateFunctions` | class | `extension/eztags/autoloads/eztagstemplatefunctions.php:6` | eZTagsTemplateFunctions class implements eztags template operator methods | – |

### extension/ezurlmultisite (2)

[All patterns of extension/ezurlmultisite](area-extension-ezurlmultisite.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZURLMultisiteOperator` | class | `extension/ezurlmultisite/classes/ezurlmultisiteoperator.php:3` | – | – |
| `eZURLMultisiteURLOperator` | class extends eZURLOperator | `extension/ezurlmultisite/classes/ezurlmultisiteurloperator.php:24` | The kernel's eZURLOperator with a multisite-aware ezurl. | – |

### extension/ezwt (1)

[All patterns of extension/ezwt](area-extension-ezwt.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZCreateClassListGroups` | class | `extension/ezwt/autoloads/ezcreateclasslistgroups.php:28` | – | – |

### extension/hcaptcha (1)

[All patterns of extension/hcaptcha](area-extension-hcaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `hCaptchaTemplateOperator` | class | `extension/hcaptcha/autoloads/hcaptchatemplateoperator.php:22` | hCaptcha extension for Exponential Written by 7x Based on works by: Bruce Morrison | – |

### extension/owsimpleoperator (1)

[All patterns of extension/owsimpleoperator](area-extension-owsimpleoperator.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `OWSimpleOperator` | class | `extension/owsimpleoperator/autoloads/owsimpleoperator.php:21` | – | – |

### extension/recaptcha (1)

[All patterns of extension/recaptcha](area-extension-recaptcha.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `reCAPTCHATemplateOperator` | class | `extension/recaptcha/autoloads/recaptchatemplateoperator.php:22` | reCAPTCHA extension for Exponential Written by Bruce Morrison | – |

### extension/sevenx_themes_media (1)

[All patterns of extension/sevenx_themes_media](area-extension-sevenx-themes-media.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `sevenxThemesMediaOperators` | class | `extension/sevenx_themes_media/autoloads/sevenxthemesmediaoperators.php:58` | – | – |

### extension/str_replace (1)

[All patterns of extension/str_replace](area-extension-str-replace.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `MyStrReplaceOperator` | class | `extension/str_replace/autoloads/str_replace_controloperator.php:3` | – | – |

### extension/swark (1)

[All patterns of extension/swark](area-extension-swark.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `SwarkOperator` | class | `extension/swark/autoloads/classes/SwarkOperator.php:22` | – | – |

### extension/xrowmetadata (3)

[All patterns of extension/xrowmetadata](area-extension-xrowmetadata.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `xrowMetaDataLicenseOperator` | class | `extension/xrowmetadata/autoloads/xrowmetadatalicenseoperator.php:37` | The template operator xrowmetadata_license: the content license of a node, or of the page being shown, ready for the page head and a visible notice. | – |
| `xrowMetaDataLicensePickerOperator` | class | `extension/xrowmetadata/autoloads/xrowmetadatalicensepickeroperator.php:14` | Template operators of the license picker (xrowMetaDataLicensePicker), for templates that have no metadata attribute content at hand, such as the clas… | – |
| `xrowMetaDataOperator` | class | `extension/xrowmetadata/autoloads/xrowmetadataoperator.php:3` | – | – |

### kernel/classes (1)

[All patterns of kernel/classes](area-kernel-classes.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ExpInstallationOperator` | class | `kernel/classes/expinstallationoperator.php:10` | Template operators for exposing the installation name to templates: | – |

### kernel/classes/audit (1)

[All patterns of kernel/classes/audit](area-kernel-classes-audit.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expAuditTemplateOperator` | class | `kernel/classes/audit/console/expaudittemplateoperator.php:13` | The template operator audit_label (doc/bc/6.0/audit.md, "Template operator and fetch"): the human label of an audit event name from the taxonomy regi… | – |

### kernel/common (19)

[All patterns of kernel/common](area-kernel-common.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `expContentSecurityPolicyOperator` | class | `kernel/common/expcontentsecuritypolicyoperator.php:21` | The template operator csp_nonce(): the nonce of the Content Security Policy of this request, for an inline script a template cannot move into a file… | – |
| `expEnvironmentOperator` | class | `kernel/common/expenvironmentoperator.php:29` | – | – |
| `eZAlphabetOperator` | class | `kernel/common/ezalphabetoperator.php:19` | – | – |
| `eZAutoLinkOperator` | class | `kernel/common/ezautolinkoperator.php:12` | – | – |
| `eZContentStructureTreeOperator` | class | `kernel/common/ezcontentstructuretreeoperator.php:17` | – | – |
| `eZDateOperatorCollection` | class | `kernel/common/ezdateoperatorcollection.php:12` | – | – |
| `eZi18nOperator` | class | `kernel/common/ezi18noperator.php:18` | – | – |
| `eZKernelOperator` | class | `kernel/common/ezkerneloperator.php:17` | – | – |
| `eZModuleOperator` | class | `kernel/common/ezmoduleoperator.php:19` | – | – |
| `eZModuleParamsOperator` | class | `kernel/common/ezmoduleparamsoperator.php:32` | – | – |
| `eZPackageOperator` | class | `kernel/common/ezpackageoperator.php:18` | – | – |
| `ezpSiteAccessMenuOperator` | class | `kernel/common/ezpsiteaccessmenuoperator.php:24` | Template operators of the administration's site menu (ezpSiteAccessMenu): | – |
| `ezpSiteAccessURLOperator` | class | `kernel/common/ezpsiteaccessurloperator.php:22` | Template operator siteaccess_url: the address of a siteaccess's front page (ezpSiteAccessURL::root()), with the scheme, host, port and siteaccess pat… | – |
| `eZSimpleTagsOperator` | class | `kernel/common/ezsimpletagsoperator.php:12` | – | – |
| `eZTOCOperator` | class | `kernel/common/eztocoperator.php:18` | – | – |
| `eZTopMenuOperator` | class | `kernel/common/eztopmenuoperator.php:19` | – | – |
| `eZTreeMenuOperator` | class | `kernel/common/eztreemenuoperator.php:12` | – | – |
| `eZURLOperator` | class | `kernel/common/ezurloperator.php:18` | – | – |
| `eZWordToImageOperator` | class | `kernel/common/ezwordtoimageoperator.php:17` | – | – |

### kernel/private (1)

[All patterns of kernel/private](area-kernel-private.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `ezpLanguageSwitcherOperator` | class | `kernel/private/eztemplate/ezplanguageswitcheroperator.php:12` | – | – |

### lib/ezpdf (1)

[All patterns of lib/ezpdf](area-lib-ezpdf.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZPDF` | class | `lib/ezpdf/classes/ezpdf.php:22` | – | – |

### lib/eztemplate (19)

[All patterns of lib/eztemplate](area-lib-eztemplate.md)

| Class | Kind | File | Description | Extension point |
|---|---|---|---|---|
| `eZTemplateArithmeticOperator` | class | `lib/eztemplate/classes/eztemplatearithmeticoperator.php:40` | – | – |
| `eZTemplateArrayOperator` | class | `lib/eztemplate/classes/eztemplatearrayoperator.php:28` | – | – |
| `eZTemplateAttributeOperator` | class | `lib/eztemplate/classes/eztemplateattributeoperator.php:56` | – | – |
| `eZTemplateControlOperator` | class | `lib/eztemplate/classes/eztemplatecontroloperator.php:26` | – | – |
| `eZTemplateDigestOperator` | class | `lib/eztemplate/classes/eztemplatedigestoperator.php:21` | – | – |
| `eZTemplateExecuteOperator` | class | `lib/eztemplate/classes/eztemplateexecuteoperator.php:18` | – | – |
| `eZTemplateExpInfoOperator` | class | `lib/eztemplate/classes/eztemplateexpinfooperator.php:36` | – | – |
| `eZTemplateImageOperator` | class | `lib/eztemplate/classes/eztemplateimageoperator.php:24` | – | – |
| `eZTemplateLocaleOperator` | class | `lib/eztemplate/classes/eztemplatelocaleoperator.php:30` | – | – |
| `eZTemplateLogicOperator` | class | `lib/eztemplate/classes/eztemplatelogicoperator.php:69` | – | – |
| `eZTemplateNl2BrOperator` | class | `lib/eztemplate/classes/eztemplatenl2broperator.php:21` | – | – |
| `eZTemplateOperator` | class | `lib/eztemplate/classes/eztemplateoperator.php:20` | – | – |
| `eZTemplatePHPOperator` | class | `lib/eztemplate/classes/eztemplatephpoperator.php:30` | – | – |
| `eZTemplateRoleOperator` | class | `lib/eztemplate/classes/eztemplateroleoperator.php:24` | The eZTemplateRoleOperator class provides template operators for checking user role membership | – |
| `eZTemplateStringOperator` | class | `lib/eztemplate/classes/eztemplatestringoperator.php:22` | – | – |
| `eZTemplateStringsOperator` | class | `lib/eztemplate/classes/eztemplatestringsoperator.php:80` | – | – |
| `eZTemplateTextOperator` | class | `lib/eztemplate/classes/eztemplatetextoperator.php:18` | – | – |
| `eZTemplateTypeOperator` | class | `lib/eztemplate/classes/eztemplatetypeoperator.php:36` | – | – |
| `eZTemplateUnitOperator` | class | `lib/eztemplate/classes/eztemplateunitoperator.php:39` | – | – |

## Template operators (76)

### extension/bcwebsitestatistics (1)

| Operators | Class | Registered in |
|---|---|---|
| bc_ga_urchin, bc_ga_urchinOrder, bc_ga_xmlAttributeValue, bc_ga_jsEscapedString, bc_ga_formatNumericDecimal, bc_ga_script_url | `BCWebsiteStatisticsOperators` | `extension/bcwebsitestatistics/autoloads/eztemplateautoload.php:34` |

### extension/cjw_newsletter (2)

| Operators | Class | Registered in |
|---|---|---|
| cjw_newsletter_preg_replace, cjw_newsletter_str_replace, cjw_newsletter_variable | `CjwNewsletterOperators` | `extension/cjw_newsletter/autoloads/eztemplateautoload.php:16` |
| cjwnl_condition_open, cjwnl_condition_else, cjwnl_condition_close, cjwnl_plaintext, cjwnl_richtext, cjwnl_interests_block, cjwnl_abs_url, cjwnl_text_underline, cjwnl_text_wrap, cjwnl_rendering_data | `CjwNewsletterRenderingOperators` | `extension/cjw_newsletter/autoloads/eztemplateautoload.php:24` |

### extension/enhancedezbinaryfile (1)

| Operators | Class | Registered in |
|---|---|---|
| parsexml | `TemplateParseXMLOperator` | `extension/enhancedezbinaryfile/autoloads/eztemplateautoload.php:7` |

### extension/explayouts_ui (1)

| Operators | Class | Registered in |
|---|---|---|
| explayouts_ui_has_template | `expLayoutsUiTemplateOperators` | `extension/explayouts_ui/autoloads/eztemplateautoload.php:3` |

### extension/expui (1)

| Operators | Class | Registered in |
|---|---|---|
| exp_config | `expUITemplateOperators` | `extension/expui/autoloads/eztemplateautoload.php:8` |

### extension/ezdemo (4)

| Operators | Class | Registered in |
|---|---|---|
| ezkeywordlist | `eZKeywordList` | `extension/ezdemo/autoloads/eztemplateautoload.php:30` |
| ezarchive | `eZArchive` | `extension/ezdemo/autoloads/eztemplateautoload.php:33` |
| eztagcloud | `eZTagCloud` | `extension/ezdemo/autoloads/eztemplateautoload.php:36` |
| ezpagedata, ezpagedata_set, ezpagedata_append | `eZPageData` | `extension/ezdemo/autoloads/eztemplateautoload.php:39` |

### extension/ezfind (1)

| Operators | Class | Registered in |
|---|---|---|
| solr_escape, solr_quotes_escape | `ezfTemplateOperators` | `extension/ezfind/autoloads/eztemplateautoload.php:12` |

### extension/ezflow (5)

| Operators | Class | Registered in |
|---|---|---|
| unserialize | `eZUnserialize` | `extension/ezflow/autoloads/eztemplateautoload.php:29` |
| json | `eZJSON` | `extension/ezflow/autoloads/eztemplateautoload.php:32` |
| feedreader | `eZFeedReader` | `extension/ezflow/autoloads/eztemplateautoload.php:35` |
| red5list | `eZRed5StreamListOperator` | `extension/ezflow/autoloads/eztemplateautoload.php:38` |
| pagelink | `eZPageLink` | `extension/ezflow/autoloads/eztemplateautoload.php:41` |

### extension/ezjscore (3)

| Operators | Class | Registered in |
|---|---|---|
| ezscript, ezscript_require, ezscript_load, ezscriptfiles, ezcss, ezcss_require, ezcss_load, ezcssfiles | `ezjscPackerTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:18` |
| json_encode, xml_encode, node_encode | `ezjscEncodingTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:29` |
| has_access_to_limitation | `ezjscAccessTemplateFunctions` | `extension/ezjscore/autoloads/eztemplateautoload.php:36` |

### extension/ezoe (1)

| Operators | Class | Registered in |
|---|---|---|
| ezoe_ini_section | `eZOETemplateUtils` | `extension/ezoe/autoloads/eztemplateautoload.php:13` |

### extension/ezstarrating (1)

| Operators | Class | Registered in |
|---|---|---|
| fetch_starrating_data, fetch_starrating_stats, fetch_by_starrating | `ezsrTemplateOperators` | `extension/ezstarrating/autoloads/eztemplateautoload.php:14` |

### extension/eztags (2)

| Operators | Class | Registered in |
|---|---|---|
| eztags_parent_string, latest_tags, user_limitations, tag_icon | `eZTagsTemplateFunctions` | `extension/eztags/autoloads/eztemplateautoload.php:10` |
| eztagscloud | `eZTagsCloud` | `extension/eztags/autoloads/eztemplateautoload.php:14` |

### extension/ezurlmultisite (2)

| Operators | Class | Registered in |
|---|---|---|
| ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable | `eZURLMultisiteURLOperator` | `extension/ezurlmultisite/autoloads/eztemplateautoload.php:14` |
| ezurl_path, ezurl_siteaccess, ezurl_roots | `eZURLMultisiteOperator` | `extension/ezurlmultisite/autoloads/eztemplateautoload.php:21` |

### extension/ezwebin (4)

| Operators | Class | Registered in |
|---|---|---|
| ezkeywordlist | `eZKeywordList` | `extension/ezwebin/autoloads/eztemplateautoload.php:30` |
| ezarchive | `eZArchive` | `extension/ezwebin/autoloads/eztemplateautoload.php:33` |
| eztagcloud | `eZTagCloud` | `extension/ezwebin/autoloads/eztemplateautoload.php:36` |
| ezpagedata, ezpagedata_set, ezpagedata_append | `eZPageData` | `extension/ezwebin/autoloads/eztemplateautoload.php:39` |

### extension/ezwt (1)

| Operators | Class | Registered in |
|---|---|---|
| ezcreateclasslistgroups | `eZCreateClassListGroups` | `extension/ezwt/autoloads/eztemplateautoload.php:29` |

### extension/hcaptcha (1)

| Operators | Class | Registered in |
|---|---|---|
| – | `hCaptchaTemplateOperator` | `extension/hcaptcha/autoloads/eztemplateautoload.php:3` |

### extension/recaptcha (1)

| Operators | Class | Registered in |
|---|---|---|
| – | `reCAPTCHATemplateOperator` | `extension/recaptcha/autoloads/eztemplateautoload.php:23` |

### extension/str_replace (1)

| Operators | Class | Registered in |
|---|---|---|
| ezstr_replace | `MyStrReplaceOperator` | `extension/str_replace/autoloads/eztemplateautoload.php:7` |

### extension/xrowmetadata (3)

| Operators | Class | Registered in |
|---|---|---|
| metadata | `xrowMetaDataOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:4` |
| xrowmetadata_license | `xrowMetaDataLicenseOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:11` |
| xrowmetadata_license_enabled, xrowmetadata_license_choices, xrowmetadata_license_info, xrowmetadata_license_inherited, xrowmetadata_license_class_settings | `xrowMetaDataLicensePickerOperator` | `extension/xrowmetadata/autoloads/eztemplateautoload.php:19` |

### kernel/common (21)

| Operators | Class | Registered in |
|---|---|---|
| ezurl, ezroot, ezdesign, ezimage, exturl, ezsys, ezhttp, ezhttp_hasvariable, ezini, ezini_hasvariable | `eZURLOperator` | `kernel/common/eztemplateautoload.php:14` |
| i18n, x18n, d18n | `eZi18nOperator` | `kernel/common/eztemplateautoload.php:18` |
| alphabet | `eZAlphabetOperator` | `kernel/common/eztemplateautoload.php:21` |
| month_overview | `eZDateOperatorCollection` | `kernel/common/eztemplateautoload.php:24` |
| autolink | `eZAutoLinkOperator` | `kernel/common/eztemplateautoload.php:27` |
| simpletags | `eZSimpleTagsOperator` | `kernel/common/eztemplateautoload.php:30` |
| treemenu | `eZTreeMenuOperator` | `kernel/common/eztemplateautoload.php:33` |
| content_structure_tree | `eZContentStructureTreeOperator` | `kernel/common/eztemplateautoload.php:36` |
| wordtoimage, mimetype_icon, class_icon, classgroup_icon, action_icon, icon, flag_icon, icon_info | `eZWordToImageOperator` | `kernel/common/eztemplateautoload.php:39` |
| ezpreference | `eZKernelOperator` | `kernel/common/eztemplateautoload.php:44` |
| siteaccess_url | `ezpSiteAccessURLOperator` | `kernel/common/eztemplateautoload.php:47` |
| siteaccess_menu, siteaccess_menu_key | `ezpSiteAccessMenuOperator` | `kernel/common/eztemplateautoload.php:50` |
| module_params | `eZModuleParamsOperator` | `kernel/common/eztemplateautoload.php:57` |
| topmenu | `eZTopMenuOperator` | `kernel/common/eztemplateautoload.php:60` |
| ezpackage | `eZPackageOperator` | `kernel/common/eztemplateautoload.php:63` |
| eztoc | `eZTOCOperator` | `kernel/common/eztemplateautoload.php:66` |
| ezmodule | `eZModuleOperator` | `kernel/common/eztemplateautoload.php:69` |
| installation_name, is_production_system | `ExpInstallationOperator` | `kernel/common/eztemplateautoload.php:72` |
| audit_label | `expAuditTemplateOperator` | `kernel/common/eztemplateautoload.php:75` |
| exp_environment, exp_environment_badge | `expEnvironmentOperator` | `kernel/common/eztemplateautoload.php:78` |
| csp_nonce | `expContentSecurityPolicyOperator` | `kernel/common/eztemplateautoload.php:81` |

### kernel/private (1)

| Operators | Class | Registered in |
|---|---|---|
| language_switcher | `ezpLanguageSwitcherOperator` | `kernel/private/eztemplate/eztemplateautoload.php:13` |

### lib/ezpdf (1)

| Operators | Class | Registered in |
|---|---|---|
| pdf | `eZPDF` | `lib/ezpdf/classes/eztemplateautoload.php:14` |

### lib/eztemplate (17)

| Operators | Class | Registered in |
|---|---|---|
| array, hash, array_prepend, prepend, array_append, append, array_merge, merge, contains, compare, extract, extract_left, extract_right, begins_with, ends_with, implode, explode, repeat, reverse, insert, remove, replace, unique, array_sum | `eZTemplateArrayOperator` | `lib/eztemplate/classes/eztemplateautoload.php:13` |
| fetch, fetch_alias | `eZTemplateExecuteOperator` | `lib/eztemplate/classes/eztemplateautoload.php:42` |
| l10n, locale, datetime, currentdate, maketime, makedate, gettime | `eZTemplateLocaleOperator` | `lib/eztemplate/classes/eztemplateautoload.php:44` |
| attribute, dump | `eZTemplateAttributeOperator` | `lib/eztemplate/classes/eztemplateautoload.php:46` |
| nl2br | `eZTemplateNl2BrOperator` | `lib/eztemplate/classes/eztemplateautoload.php:48` |
| concat, indent | `eZTemplateTextOperator` | `lib/eztemplate/classes/eztemplateautoload.php:50` |
| si | `eZTemplateUnitOperator` | `lib/eztemplate/classes/eztemplateautoload.php:52` |
| lt, gt, le, ge, eq, ne, null, not, true, false, or, and, choose | `eZTemplateLogicOperator` | `lib/eztemplate/classes/eztemplateautoload.php:54` |
| is_array, is_boolean, is_integer, is_float, is_numeric, is_string, is_object, is_class, is_null, is_set, is_unset, get_type, get_class | `eZTemplateTypeOperator` | `lib/eztemplate/classes/eztemplateautoload.php:59` |
| cond, first_set | `eZTemplateControlOperator` | `lib/eztemplate/classes/eztemplateautoload.php:64` |
| sum, sub, inc, dec, div, mod, mul, max, min, abs, ceil, floor, round, int, float, count, roman, rand | `eZTemplateArithmeticOperator` | `lib/eztemplate/classes/eztemplateautoload.php:67` |
| texttoimage, image, imagefile | `eZTemplateImageOperator` | `lib/eztemplate/classes/eztemplateautoload.php:77` |
| upcase, downcase, count_words, count_chars, trim, break, wrap, upfirst, upword, simplify, trim, wash, chr, ord, shorten, pad | `eZTemplateStringOperator` | `lib/eztemplate/classes/eztemplateautoload.php:83` |
| crc32, md5, rot13 | `eZTemplateDigestOperator` | `lib/eztemplate/classes/eztemplateautoload.php:101` |
| expinfo | `eZTemplateExpInfoOperator` | `lib/eztemplate/classes/eztemplateautoload.php:106` |
| has_role, has_role_id, has_any_role, has_role_by_user, has_role_by_user_id, has_policy, has_policy_by_user | `eZTemplateRoleOperator` | `lib/eztemplate/classes/eztemplateautoload.php:110` |
| – | `eZTemplateStringsOperator` | `lib/eztemplate/classes/eztemplateautoload.php:115` |
