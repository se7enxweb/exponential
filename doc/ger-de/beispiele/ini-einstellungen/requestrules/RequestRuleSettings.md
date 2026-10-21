---
title: requestrules.ini [RequestRuleSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RequestRuleSettings] in requestrules.ini: Enabled, RuleList, RuleProviders, FactProviders, MaxRewrites, ConditionHandlers, ActionHandlers"
keywords: Einstellung, requestrules.ini, RequestRuleSettings, INI, setting, Enabled, RuleList, RuleProviders, FactProviders, MaxRewrites, ConditionHandlers, ActionHandlers
type: generated
generated: exp:help:examples
---
# requestrules.ini [RequestRuleSettings]

Der Block `[RequestRuleSettings]` von `settings/requestrules.ini`, mit 7 Einstellung(en).

Bearbeiten Sie `settings/requestrules.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/requestrules.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/requestrules.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/requestrules.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Enabled

> true oder false. Mit false wird keine Regel gefragt; der Kernel verhält sich wie vor  
> dieser Funktion.  

Standard: `true`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[RequestRuleSettings]
Enabled=true
*/ ?>
```

## RuleList

> Die gefragten Regeln, in Reihenfolge. Leer: keine Regel (und keine Kosten). Ein Siteaccess-  
> Override, das mit RuleList\[\] (leer) beginnt, ersetzt die Liste.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[RequestRuleSettings]
# ein leeres "RuleList[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RuleList[]
*/ ?>
```

## RuleProviders

> In PHP berechnete Regeln: Klassen, die ezpRequestRuleProvider implementieren. Ihre  
> Regeln kommen nach den Regeln aus RuleList.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[RequestRuleSettings]
# ein leeres "RuleProviders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
RuleProviders[]
*/ ?>
```

## FactProviders

> Tatsachen, die jeder Anfrage hinzugefügt werden, für Bedingungen wie Conditions\[fact:<name>\]:  
> Klassen, die ezpRequestFactProvider implementieren.  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[RequestRuleSettings]
# ein leeres "FactProviders[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
FactProviders[]
*/ ?>
```

## MaxRewrites

> Wie oft eine Anfrage umgeschrieben werden darf (Action=rewrite), bevor sie mit  
> „nicht gefunden“ beantwortet wird. Beendet eine Umschreibschleife.  

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[RequestRuleSettings]
MaxRewrites=3
*/ ?>
```

## ConditionHandlers

> Bedingungen und Aktionen nach Namen, zusätzlich zu den eingebauten (in der  
> Anleitung aufgeführt). Eine Erweiterung fügt ihre eigenen hinzu:  
>   ConditionHandlers\[customer\_tier\]=myCustomerTierCondition  
>   ActionHandlers\[paywall\]=myPaywallAction  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[RequestRuleSettings]
# ein leeres "ConditionHandlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ConditionHandlers[]
*/ ?>
```

## ActionHandlers

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/requestrules.ini.append.php
[RequestRuleSettings]
# ein leeres "ActionHandlers[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
ActionHandlers[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von requestrules.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/requestrules.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
