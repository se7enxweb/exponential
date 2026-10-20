---
title: Patterns in kernel/classes/uiswitch
audience: developer
category: standards
order: 508
summary: Every class, view, fetch function, operator and plugin point of kernel/classes/uiswitch with its design patterns.
keywords: kernel/classes/uiswitch, design patterns, classes, extension points
type: catalogue
---
# Patterns in kernel/classes/uiswitch

> **Note**
> This page is generated from the source code by `php bin/php/exppatterncatalogue.php --write`. Do not edit it by hand:
> change the code or the generator, then run the command again. How the patterns are recognized: [the pattern catalogue](README.md#how-the-catalogue-is-built).

Every class, interface and trait of `kernel/classes/uiswitch` with the patterns it was recognized as, followed by the module views, policy functions, fetch functions, template operators, cronjobs, event hooks and INI plugin points of the area. [All patterns](README.md).

## Classes (6)

| Class | Kind | Patterns | File | Description |
|---|---|---|---|---|
| `expUISwitch` | class | [Factory and static factory methods](factory.md), [Utility (static helper)](utility.md) | `kernel/classes/uiswitch/expuiswitch.php:28` | The UI switch framework: a registry of providers (uiswitch.ini [UISwitchSettings] ProviderList[]), the user's choices kept in the session, and the ea… |
| `expUISwitchDesignProvider` | class extends expUISwitchProvider | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/uiswitch/expuiswitchdesignprovider.php:25` | The design of the administration interface (the "UI chrome"): admin4l, admin4, the classic admin design, AdminUI or another installed administration… |
| `expUISwitchLanguageCatalogue` | class | – | `kernel/classes/uiswitch/expuiswitchlanguagecatalogue.php:24` | What the interface language choice knows about every locale of the installation: its names (share/locale/.ini LanguageName and InternationalLanguageN… |
| `expUISwitchLanguageProvider` | class extends expUISwitchProvider | [INI plugin point (extension point)](ini-plugin-point.md), [Strategy / handler](strategy-handler.md) | `kernel/classes/uiswitch/expuiswitchlanguageprovider.php:27` | The display language of the administration interface: what site.ini [RegionalSettings] Locale of the siteaccess sets (the translations of the interfa… |
| `expUISwitchProvider` | abstract class implements expUISwitchProviderInterface | [Strategy / handler](strategy-handler.md), [Template Method / abstract base class](template-method.md) | `kernel/classes/uiswitch/expuiswitchprovider.php:18` | The common part of a UI switch provider: its settings group uiswitch.ini [UISwitch_], the allow-list from AllowedValues[] (each value checked by the… |
| `expUISwitchProviderInterface` | interface | [Interface (contract)](interface.md) | `kernel/classes/uiswitch/expuiswitchproviderinterface.php:19` | One switchable preference of the administration interface (the "UI chrome"): the display language, the design, or what an extension adds. |
