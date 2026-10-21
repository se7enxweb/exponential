---
title: uiswitch.ini [UISwitch_language]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [UISwitch_language] in uiswitch.ini: Class, Enabled, GroupList, GroupLabel, GroupValues_english, GroupValues_major, GroupValues_regional, AllLanguages ..."
keywords: Einstellung, uiswitch.ini, UISwitch_language, INI, setting, Class, Enabled, GroupList, GroupLabel, GroupValues_english, GroupValues_major, GroupValues_regional, AllLanguages, ListUntranslated, AllowedValues, RememberPreference
type: generated
generated: exp:help:examples
---
# uiswitch.ini [UISwitch_language]

Der Block `[UISwitch_language]` von `settings/uiswitch.ini`, mit 11 Einstellung(en).

Bearbeiten Sie `settings/uiswitch.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/uiswitch.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/uiswitch.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Class

> Die Anzeigesprache der Oberfläche: site.ini \[RegionalSettings\] Locale für die Anfragen dieses Benutzers  
> (Übersetzungen, Datums- und Zahlenformate). Richtlinie: uiswitch/language.  

Standard: `expUISwitchLanguageProvider`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
Class=expUISwitchLanguageProvider
*/ ?>
```

## Enabled

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
Enabled=true
*/ ?>
```

## GroupList

> Die Sprachen, die ein Benutzer wählen darf, gruppiert angezeigt: zuerst die Gruppen aus GroupList\[\] in ihrer Reihenfolge (jede  
> GroupValues\_<group>\[\] in der angegebenen Reihenfolge, nach der Zahl der Administratoren weltweit geordnet, die sie brauchen; warum die  
> Standards so sind, wie sie sind: doc/eng-us/features/6.0/ui-switch-languages.md), dann jede angebotene Sprache von A bis Z nach ihrem  
> Namen. Eine Locale wird nur angeboten, wenn share/locale/<locale>.ini existiert; die eigene Locale des Siteaccess wird immer  
> angeboten, und ihre Wahl hebt den Schalter auf. Eine Sprache ohne Übersetzung der Oberfläche behält englische  
> Texte mit ihren eigenen Datums- und Zahlenformaten und sagt das in der Liste („noch keine Übersetzung“).  

Standard: eine Liste mit 3: `english`, `major`, `regional`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# ein leeres "GroupList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupList[]
GroupList[]=english
GroupList[]=major
GroupList[]=regional
*/ ?>
```

## GroupLabel

> Die Überschriften der Gruppen (übersetzt im Kontext kernel/uiswitch/language).  

Standard: ein Hash mit 3: `english=English`, `major=Major world languages`, `regional=Regional variants`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# ein leeres "GroupLabel[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupLabel[]
GroupLabel[english]=English
GroupLabel[major]=Major world languages
GroupLabel[regional]=Regional variants
*/ ?>
```

## GroupValues_english

Standard: eine Liste mit 5: `eng-US`, `eng-GB`, `eng-AU`, `eng-CA`, `eng-IN`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# ein leeres "GroupValues_english[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
GroupValues_english[]
GroupValues_english[]=eng-US
GroupValues_english[]=eng-GB
GroupValues_english[]=eng-AU
GroupValues_english[]=eng-CA
GroupValues_english[]=eng-IN
*/ ?>
```

## GroupValues_major

Standard: eine Liste mit 19: `esl-ES`, `chi-CN`, `fre-FR`, `ger-DE`, `por-BR`, `jpn-JP`, `rus-RU`, `ara-SA`, `hin-IN`, `ita-IT`, `ind-ID`, `tur-TR` ....

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# ein leeres "GroupValues_major[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

Standard: eine Liste mit 10: `esl-MX`, `esl-US`, `fre-CA`, `fre-CH`, `ger-AT`, `ger-CH`, `por-PT`, `chi-TW`, `chi-HK`, `ara-EG`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# ein leeres "GroupValues_regional[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
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

> enabled: Jede Locale aus share/locale (ohne die Formatvarianten wie eng-GB@euro) wird angeboten und  
> nach den Gruppen von A bis Z aufgeführt („Alle Sprachen“). disabled: nur die Gruppen und AllowedValues\[\]; die, die keine Gruppe nennt, werden  
> von A bis Z als „Weitere Sprachen“ aufgeführt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
AllLanguages=enabled
*/ ?>
```

## ListUntranslated

> enabled: Sprachen ohne Übersetzung der Oberfläche (share/translations/<locale>/translation.ts) werden  
> ebenfalls angeboten, als „noch keine Übersetzung“ markiert. disabled: nur Englisch und die übersetzten Sprachen.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
ListUntranslated=enabled
*/ ?>
```

## AllowedValues

> Weitere anzubietende Locales außerhalb der Gruppen (eine Formatvariante wie eng-GB@euro oder eine eigene Liste, wenn  
> GroupList\[\] leer ist und AllLanguages=disabled). Eine Testsprache (TestLanguage=true in ihrer Locale-Datei, etwa  
> Lorem Ipsum, qlo-ZZ) steht nie in einer Gruppe oder in der Liste von A bis Z von AllLanguages; nur wer sie hier nennt, bietet sie an.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
# ein leeres "AllowedValues[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedValues[]
*/ ?>
```

## RememberPreference

> enabled: Die Wahl wird auch in den Einstellungen des Benutzers gehalten und kehrt bei der nächsten Anmeldung zurück.  
> disabled (Standard): Sie endet mit der Sitzung, beim Abmelden.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/uiswitch.ini.append.php
[UISwitch_language]
RememberPreference=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von uiswitch.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/uiswitch.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
