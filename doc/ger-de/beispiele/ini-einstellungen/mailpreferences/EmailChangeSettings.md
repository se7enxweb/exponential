---
title: mailpreferences.ini [EmailChangeSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [EmailChangeSettings] in mailpreferences.ini: Confirm, NotifyOldAddress"
keywords: Einstellung, mailpreferences.ini, EmailChangeSettings, INI, setting, Confirm, NotifyOldAddress
type: generated
generated: exp:help:examples
---
# mailpreferences.ini [EmailChangeSettings]

Der Block `[EmailChangeSettings]` von `settings/mailpreferences.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/mailpreferences.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/mailpreferences.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/mailpreferences.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/mailpreferences.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## Confirm

> enabled (the default): a new e-mail address of an account (user/edit, the  
> admin's edit, the account services) takes effect only once it is confirmed  
> with the link sent to the new address; the account keeps its old address  
> until then. disabled: the address changes at once, as before.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[EmailChangeSettings]
Confirm=enabled
*/ ?>
```

## NotifyOldAddress

> enabled: the old address gets a notice that a change was asked for.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/mailpreferences.ini.append.php
[EmailChangeSettings]
NotifyOldAddress=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von mailpreferences.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/mailpreferences.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
