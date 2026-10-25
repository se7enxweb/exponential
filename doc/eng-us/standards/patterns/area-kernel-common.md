---
title: Patterns in kernel/common
audience: developer
category: standards
order: 521
summary: Every class, view, fetch function, operator and plugin point of kernel/common with its design patterns.
keywords: kernel/common, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/common

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/common` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (26)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expContentSecurityPolicyOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/expcontentsecuritypolicyoperator.php:21` | The template operator csp_nonce(): the nonce of the Content Security Policy of this request, for an inline script a template cannot move into a file… |
| `expDemoModeOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/expdemomodeoperator.php:21` | The template operator exp_demo_mode(): true while demo mode is on (the environment variable EXP_DEMO_MODE=1, expDemoMode). |
| `expEnvironmentOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/expenvironmentoperator.php:29` | – |
| `expKernelTransferOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/expkerneltransferoperator.php:29` | The template operators of the transfer registry (expKernelTransferRegistry; doc/eng-us/features/6.0/transfer-registry.md). |
| `expViewParameterOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/expviewparameteroperator.php:21` | The template operator view_parameter: a value as the value of a view parameter, /(name)/, written as expViewParameters::encode() writes it ("~2F" for… |
| `eZAlphabetOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezalphabetoperator.php:19` | – |
| `eZAutoLinkOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezautolinkoperator.php:12` | – |
| `eZContentStructureTreeOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezcontentstructuretreeoperator.php:17` | – |
| `eZDateOperatorCollection` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezdateoperatorcollection.php:12` | – |
| `eZi18nOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezi18noperator.php:18` | – |
| `eZKernelOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezkerneloperator.php:17` | – |
| `eZModuleOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezmoduleoperator.php:19` | – |
| `eZModuleParamsOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezmoduleparamsoperator.php:32` | – |
| `eZObjectForwarder` | class | [Template function (Interpreter plugin)](template-function.md) | `kernel/common/ezobjectforwarder.php:18` | – |
| `eZPackageOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezpackageoperator.php:18` | – |
| `ezpI18n` | class | [Utility (static helper)](utility.md) | `kernel/common/ezpi18n.php:12` | – |
| `ezpSiteAccessMenuOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezpsiteaccessmenuoperator.php:24` | Template operators of the administration's site menu (ezpSiteAccessMenu): |
| `ezpSiteAccessURLOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezpsiteaccessurloperator.php:22` | Template operator siteaccess_url: the address of a siteaccess's front page (ezpSiteAccessURL::root()), with the scheme, host, port and siteaccess pat… |
| `eZSimpleTagsOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezsimpletagsoperator.php:12` | – |
| `eZTemplateDesignResource` | class extends eZTemplateFileResource | [Singleton / instance()](singleton.md) | `kernel/common/eztemplatedesignresource.php:18` | – |
| `eZTemplatesStatisticsReporter` | class | [Utility (static helper)](utility.md) | `kernel/common/eztemplatesstatisticsreporter.php:19` | – |
| `eZTOCOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/eztocoperator.php:18` | – |
| `eZTopMenuOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/eztopmenuoperator.php:19` | – |
| `eZTreeMenuOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/eztreemenuoperator.php:12` | – |
| `eZURLOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezurloperator.php:18` | – |
| `eZWordToImageOperator` | class | [Template operator (Interpreter plugin)](template-operator.md) | `kernel/common/ezwordtoimageoperator.php:17` | – |

## Template operators (24)

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
| exp_demo_mode | `expDemoModeOperator` | `kernel/common/eztemplateautoload.php:81` |
| csp_nonce | `expContentSecurityPolicyOperator` | `kernel/common/eztemplateautoload.php:84` |
| view_parameter | `expViewParameterOperator` | `kernel/common/eztemplateautoload.php:87` |
| transfer_tools, transfer_actions, transfer_related, transfer_tool | `expKernelTransferOperator` | `kernel/common/eztemplateautoload.php:90` |
