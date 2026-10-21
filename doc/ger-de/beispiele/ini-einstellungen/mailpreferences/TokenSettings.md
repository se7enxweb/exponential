---
title: mailpreferences.ini [TokenSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TokenSettings] in mailpreferences.ini: TTL, RequestLinkTTL, RequestLinkLimit"
keywords: Einstellung, mailpreferences.ini, TokenSettings, INI, setting, TTL, RequestLinkTTL, RequestLinkLimit
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [TokenSettings]

Der Block `[TokenSettings]` von `settings/mailpreferences.ini`, mit 3 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## TTL

> Lebensdauer der Links in Sekunden, pro Zweck; 0 läuft nie ab. Die  
> Abmelde- und Verwaltungslinks in Mails müssen lange nach dem Versand der Mail  
> funktionieren (CAN-SPAM: mindestens 30 Tage, CASL: 60 Tage). Ein Wert für  
> unsubscribe oder manage zwischen 0 und 5184000 (60 Tage) wird auf 60 Tage angehoben,  
> mit einer Warnung auf der Statusseite.  

Standard: ein Hash mit 3: `unsubscribe=0`, `manage=0`, `confirm=604800`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[TokenSettings]
# ein leeres "TTL[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
TTL[]
TTL[unsubscribe]=0
TTL[manage]=0
TTL[confirm]=604800
*/ ?>
```

## RequestLinkTTL

> Die Seite „Link zusenden“: die Lebensdauer des Links, den sie sendet, und wie viele  
> Links eine Adresse pro Stunde anfordern kann.  

Standard: `86400`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[TokenSettings]
RequestLinkTTL=86400
*/ ?>
```

## RequestLinkLimit

Standard: `3`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[TokenSettings]
RequestLinkLimit=3
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
