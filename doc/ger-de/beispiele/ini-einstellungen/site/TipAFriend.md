---
title: site.ini [TipAFriend]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [TipAFriend] in site.ini: MaxRequestsPerTimeframe, TimeFrame, FromEmail"
keywords: Einstellung, site.ini, TipAFriend, INI, setting, MaxRequestsPerTimeframe, TimeFrame, FromEmail
type: generated
generated: exp:help:examples
---
# site.ini [TipAFriend]

Der Block `[TipAFriend]` von `settings/site.ini`, mit 3 Einstellung(en).

> Die Einstellungen steuern, wie viele Mails an eine E-Mail-Adresse gesendet werden dürfen  

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaxRequestsPerTimeframe

Standard: `5`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TipAFriend]
MaxRequestsPerTimeframe=5
*/ ?>
```

## TimeFrame

Standard: `1`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TipAFriend]
TimeFrame=1
*/ ?>
```

## FromEmail

> in Stunden  
> E-Mail-Adresse, die in Mails von tipafriend als Absender verwendet wird; nötig, wenn Sie  
> SMTP-Server mit geschlossenem Relay verwenden  
> Weisen Sie FromEmail einen nicht leeren Wert zu, damit sie als Absenderadresse verwendet wird.  

Standard: leer.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[TipAFriend]
FromEmail=
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
