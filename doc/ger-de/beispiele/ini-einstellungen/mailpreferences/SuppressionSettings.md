---
title: mailpreferences.ini [SuppressionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SuppressionSettings] in mailpreferences.ini: RetentionDays, Reasons, Listeners"
keywords: Einstellung, mailpreferences.ini, SuppressionSettings, INI, setting, RetentionDays, Reasons, Listeners
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [SuppressionSettings]

Der Block `[SuppressionSettings]` von `settings/mailpreferences.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## RetentionDays

> Tage, die eine gesperrte Adresse gesperrt bleibt; 0 ist für immer (der Standard:  
> Nur der Hash wird gespeichert, die Adresse lässt sich daraus nicht lesen).  

Standard: `0`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[SuppressionSettings]
RetentionDays=0
*/ ?>
```

## Reasons

> Gründe, mit denen eine Adresse gesperrt werden kann.  

Standard: eine Liste mit 6: `bounce`, `complaint`, `unsubscribe_all`, `legal`, `admin`, `bridge`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[SuppressionSettings]
# ein leeres "Reasons[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Reasons[]
Reasons[]=bounce
Reasons[]=complaint
Reasons[]=unsubscribe_all
Reasons[]=legal
Reasons[]=admin
Reasons[]=bridge
*/ ?>
```

## Listeners

> Klassen, die benachrichtigt werden, wenn eine Adresse gesperrt oder entsperrt wird, damit die eigene  
> Sperrliste eines anderen Systems dieser gleich bleibt (cjw\_newsletter fügt seine  
> Blacklist hier hinzu): static suppressionAdded( $email, $hash, $reason ) und  
> suppressionLifted( $hash, $email or null, $reason ).  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[SuppressionSettings]
# ein leeres "Listeners[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Listeners[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
