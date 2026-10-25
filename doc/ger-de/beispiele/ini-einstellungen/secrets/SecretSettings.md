---
title: secrets.ini [SecretSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [SecretSettings] in secrets.ini: KeyFile, Secrets"
keywords: Einstellung, secrets.ini, SecretSettings, INI, setting, KeyFile, Secrets
type: generated
generated: exp:help:examples
---
# secrets.ini [SecretSettings]

Der Block `[SecretSettings]` von `settings/secrets.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/secrets.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/secrets.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/secrets.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/secrets.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## KeyFile

> The key file, used when the environment variable EXP\_SECRET\_KEY is not set: made on first use (32 random bytes,  
> base64, mode 0600). Relative to the installation, or absolute. It must not be in a path the web server serves.  
> A backup of the database alone cannot decrypt the secrets: keep the key file (or EXP\_SECRET\_KEY) with it.  

Standard: `var/secrets/exp_secret.key`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/secrets.ini.append.php
[SecretSettings]
KeyFile=var/secrets/exp_secret.key
*/ ?>
```

## Secrets

> Secrets set in the settings, by name (in the site's own settings, never in a repository), asked after the store:  
> Secrets\[<name>\]=<value>  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/secrets.ini.append.php
[SecretSettings]
# ein leeres "Secrets[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
Secrets[]
*/ ?>
```

## Siehe auch

- [Alle Blöcke von secrets.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/secrets.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
