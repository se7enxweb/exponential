---
title: uiswitch.ini [UISwitch_language]
audience: developer, admin
category: examples
summary: "The settings of [UISwitch_language] in uiswitch.ini: Class, Enabled, GroupList, GroupLabel, GroupValues_english, GroupValues_major, GroupValues_regional, AllLanguages ..."
keywords: uiswitch.ini, UISwitch_language, INI, setting, Class, Enabled, GroupList, GroupLabel, GroupValues_english, GroupValues_major, GroupValues_regional, AllLanguages, ListUntranslated, AllowedValues, RememberPreference
type: generated
generated: exp:help:examples
---
# uiswitch.ini [UISwitch_language]

The block `[UISwitch_language]` of `settings/uiswitch.ini`, with 11 setting(s).

Never edit `settings/uiswitch.ini` itself: an update replaces it. Put the settings you change in an override, which wins over the shipped file:

- `settings/override/uiswitch.ini.append.php` for every siteaccess,
- `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` for one siteaccess,
- `extension/<extension>/settings/uiswitch.ini.append.php` in an extension (read when the extension is active).

## Class

> The display language of the interface: site.ini \[RegionalSettings\] Locale for this user's requests  
> (translations, date and number formats). Policy: uiswitch/language.  

Default: `expUISwitchLanguageProvider`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
Class=expUISwitchLanguageProvider
*/ ?>
```

## Enabled

Default: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
Enabled=true
*/ ?>
```

## GroupList

> The languages a user may choose, shown grouped: first the groups of GroupList\[\] in their order (each  
> GroupValues\_<group>\[\] in the order given, ranked by how many administrators worldwide need them; why the  
> defaults are what they are: doc/en/us/features/6.0/ui-switch-languages.md), then every offered language A-Z by its  
> name. A locale is offered only when share/locale/<locale>.ini exists; the siteaccess's own Locale is always  
> offered and choosing it clears the switch. A language without a translation of the interface keeps English  
> texts with its own date and number formats, and says so in the list ("no translation yet").  

Default: a list of 3: `english`, `major`, `regional`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# an empty "GroupList[]" first replaces the shipped list; without it the items are added
GroupList[]
GroupList[]=english
GroupList[]=major
GroupList[]=regional
*/ ?>
```

## GroupLabel

> The group headings (translated in the context kernel/uiswitch/language).  

Default: a hash of 3: `english=English`, `major=Major world languages`, `regional=Regional variants`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# an empty "GroupLabel[]" first replaces the shipped list; without it the items are added
GroupLabel[]
GroupLabel[english]=English
GroupLabel[major]=Major world languages
GroupLabel[regional]=Regional variants
*/ ?>
```

## GroupValues_english

Default: a list of 5: `eng-US`, `eng-GB`, `eng-AU`, `eng-CA`, `eng-IN`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# an empty "GroupValues_english[]" first replaces the shipped list; without it the items are added
GroupValues_english[]
GroupValues_english[]=eng-US
GroupValues_english[]=eng-GB
GroupValues_english[]=eng-AU
GroupValues_english[]=eng-CA
GroupValues_english[]=eng-IN
*/ ?>
```

## GroupValues_major

Default: a list of 19: `esl-ES`, `chi-CN`, `fre-FR`, `ger-DE`, `por-BR`, `jpn-JP`, `rus-RU`, `ara-SA`, `hin-IN`, `ita-IT`, `ind-ID`, `tur-TR` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# an empty "GroupValues_major[]" first replaces the shipped list; without it the items are added
GroupValues_major[]
GroupValues_major[]=esl-ES
GroupValues_major[]=chi-CN
GroupValues_major[]=fre-FR
GroupValues_major[]=ger-DE
GroupValues_major[]=por-BR
GroupValues_major[]=jpn-JP
GroupValues_major[]=rus-RU
GroupValues_major[]=ara-SA
GroupValues_major[]=hin-IN
GroupValues_major[]=ita-IT
GroupValues_major[]=ind-ID
GroupValues_major[]=tur-TR
GroupValues_major[]=kor-KR
GroupValues_major[]=dut-NL
GroupValues_major[]=pol-PL
GroupValues_major[]=vie-VN
GroupValues_major[]=ukr-UA
GroupValues_major[]=fas-IR
GroupValues_major[]=heb-IL
*/ ?>
```

## GroupValues_regional

Default: a list of 10: `esl-MX`, `esl-US`, `fre-CA`, `fre-CH`, `ger-AT`, `ger-CH`, `por-PT`, `chi-TW`, `chi-HK`, `ara-EG`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# an empty "GroupValues_regional[]" first replaces the shipped list; without it the items are added
GroupValues_regional[]
GroupValues_regional[]=esl-MX
GroupValues_regional[]=esl-US
GroupValues_regional[]=fre-CA
GroupValues_regional[]=fre-CH
GroupValues_regional[]=ger-AT
GroupValues_regional[]=ger-CH
GroupValues_regional[]=por-PT
GroupValues_regional[]=chi-TW
GroupValues_regional[]=chi-HK
GroupValues_regional[]=ara-EG
*/ ?>
```

## AllLanguages

> enabled: every locale of share/locale (without the format variants such as eng-GB@euro) is offered and listed  
> A-Z after the groups ("All languages"). disabled: only the groups and AllowedValues\[\]; those no group names are  
> listed A-Z as "More languages".  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
AllLanguages=enabled
*/ ?>
```

## ListUntranslated

> enabled: languages without a translation of the interface (share/translations/<locale>/translation.ts) are  
> offered too, marked "no translation yet". disabled: only English and the translated languages.  

Default: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
ListUntranslated=enabled
*/ ?>
```

## AllowedValues

> More locales to offer, outside the groups (a format variant such as eng-GB@euro, or a list of its own when  
> GroupList\[\] is empty and AllLanguages=disabled). A test language (TestLanguage=true in its locale file, such as  
> Lorem Ipsum, qlo-ZZ) is never in a group or in the A-Z list of AllLanguages; only naming it here offers it.  

Default: an empty list.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# an empty "AllowedValues[]" first replaces the shipped list; without it the items are added
AllowedValues[]
*/ ?>
```

## RememberPreference

> enabled: the choice is also kept in the user's preferences and comes back at the next sign-in.  
> disabled (default): it ends with the session, at logout.  

Default: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
RememberPreference=disabled
*/ ?>
```

## Related

- [All blocks of uiswitch.ini](README.md)
- [All INI files](../README.md)
- [The settings page](../../../guides/settings-page.md)

---

Generated by `./console exp:help:examples` from `settings/uiswitch.ini`. Do not edit this page: change the source and generate again ([how](../../README.md#how-the-generated-pages-are-made)). Free documentation: [GFDL 1.3 or later](../../../help/free-documentation.md).
