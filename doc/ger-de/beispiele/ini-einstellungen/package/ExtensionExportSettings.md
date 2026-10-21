---
title: package.ini [ExtensionExportSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ExtensionExportSettings] in package.ini: AllowedExtensions, RequirePolicy"
keywords: Einstellung, package.ini, ExtensionExportSettings, INI, setting, AllowedExtensions, RequirePolicy
type: generated
generated: exp:help:examples
---
# package.ini [ExtensionExportSettings]

Der Block `[ExtensionExportSettings]` von `settings/package.ini`, mit 2 Einstellung(en).

Bearbeiten Sie `settings/package.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/package.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/package.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/package.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## AllowedExtensions

> Das Exportieren einer Erweiterung als Paket (Setup > Erweiterungen, der Paketassistent, ezpm.php add) ist standardmäßig  
> abgeschaltet. Jeder Weg lehnt außerdem eine Erweiterung ab, die geheime Werte oder Schlüsselmaterial enthält  
> (eZExtensionPackageHandler::packRefusal()).  
>
> Der Maintainer einer Erweiterung kann Befunde der Prüfung nennen, von denen bekannt ist, dass sie keine Geheimnisse sind (ein Dokumentations-  
> beispiel, das die Prüfung nicht von einem echten Wert unterscheiden kann), in einer Datei package\_check.ini im Wurzelverzeichnis der Erweiterung, einer  
> pro Zeile: AllowedFindings\[\]=<path below the extension>:<setting name or (kind)>, zum Beispiel  
> AllowedFindings\[\]=settings/recaptcha.ini.append.php:PrivateKey. Sie kann nur auslassen, was sie nennt: Schlüsseldateien, ein  
> Block mit privatem Schlüssel, eine Kopie einer Einstellungsdatei und ein Link werden immer abgelehnt. Anleitung:  
> doc/eng-us/bc/6.0/extension-package-secret-check.md  
>
> HANDLUNG ERFORDERLICH zum Exportieren: Tragen Sie die Erweiterung hier ein UND gewähren Sie der Rolle die Richtlinie package/export\_extension.  
>
> Die Erweiterungen, die exportiert werden dürfen. Leer (der Standard): keine. "\*": jede Erweiterung, die die Prüfung durchlässt.  
> Gilt für jeden Weg, die Kommandozeile eingeschlossen.  
> Beispiel:  
> AllowedExtensions\[\]=myextension  
> AllowedExtensions\[\]=\*  

Standard: eine leere Liste.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[ExtensionExportSettings]
# ein leeres "AllowedExtensions[]" zuerst ersetzt die ausgelieferte Liste; ohne es werden die Einträge hinzugefügt
AllowedExtensions[]
*/ ?>
```

## RequirePolicy

> enabled (der Standard): Das Exportieren einer Erweiterung in der Administration braucht außerdem die Richtlinie  
> package/export\_extension (die Rolle Administrator mit allen Funktionen hat sie). disabled: Der Zugriff auf  
> Setup > Erweiterungen oder auf den Paketassistenten genügt. Die Kommandozeile läuft als kein Benutzer und wird nicht gefragt.  

Standard: `enabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/package.ini.append.php
[ExtensionExportSettings]
RequirePolicy=enabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von package.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/package.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
