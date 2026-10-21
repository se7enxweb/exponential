---
title: logviewer.ini [RedactionSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [RedactionSettings] in logviewer.ini: MaskEmails, MaskIPs, MaskSecrets, SecretSettings, SecretGroups"
keywords: Einstellung, logviewer.ini, RedactionSettings, INI, setting, MaskEmails, MaskIPs, MaskSecrets, SecretSettings, SecretGroups
type: generated
generated: exp:help:examples
---
# logviewer.ini [RedactionSettings]

Der Block `[RedactionSettings]` von `settings/logviewer.ini`, mit 5 Einstellung(en).

Bearbeiten Sie `settings/logviewer.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/logviewer.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/logviewer.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/logviewer.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## MaskEmails

> Was der Viewer zeigt, maskieren: E-Mail-Adressen, IP-Adressen und Geheimnisse (Passwörter in Adressen und  
> Paaren, Tokens, Schlüssel, Sitzungs-IDs, Cookies). Ein Benutzer mit setup/unmasklogs kann das Maskieren für  
> sich auf der Seite abschalten; die Geheimnisse der Einstellungen unten nie.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RedactionSettings]
MaskEmails=enabled
*/ ?>
```

## MaskIPs

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RedactionSettings]
MaskIPs=enabled
*/ ?>
```

## MaskSecrets

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RedactionSettings]
MaskSecrets=enabled
*/ ?>
```

## SecretSettings

> Die Einstellungen, deren Werte nie angezeigt werden, auch unmaskiert nicht: <file>;<group>;<variable>. Jeder ihrer Werte,  
> der in einer Logzeile vorkommt, wird durch \*\*\* ersetzt. Variablen, deren Name sagt, dass sie ein Geheimnis enthalten (Password,  
> Secret, Token, Key ...), in diesen Gruppen sind ebenfalls abgedeckt.  

Standard: eine Liste mit 2: `site.ini;DatabaseSettings;Password`, `site.ini;MailSettings;TransportPassword`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RedactionSettings]
# ein leeres "SecretSettings[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecretSettings[]
SecretSettings[]=site.ini;DatabaseSettings;Password
SecretSettings[]=site.ini;MailSettings;TransportPassword
*/ ?>
```

## SecretGroups

Standard: eine Liste mit 2: `site.ini;DatabaseSettings`, `site.ini;MailSettings`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/logviewer.ini.append.php
[RedactionSettings]
# ein leeres "SecretGroups[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
SecretGroups[]
SecretGroups[]=site.ini;DatabaseSettings
SecretGroups[]=site.ini;MailSettings
*/ ?>
```

## Siehe auch

- [Alle Blöcke von logviewer.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/logviewer.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
