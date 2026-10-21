---
title: site.ini [ConfigurationSettings]
audience: developer, admin
category: examples
summary: "Die Einstellungen von [ConfigurationSettings] in site.ini: ManagedByDeployment"
keywords: Einstellung, site.ini, ConfigurationSettings, INI, setting, ManagedByDeployment
type: generated
generated: exp:help:examples
---
# site.ini [ConfigurationSettings]

Der Block `[ConfigurationSettings]` von `settings/site.ini`, mit 1 Einstellung(en).

Bearbeiten Sie `settings/site.ini` nie selbst: Ein Update ersetzt die Datei. Schreiben Sie die Einstellungen, die Sie ändern, in ein Override, das Vorrang vor der ausgelieferten Datei hat:

- `settings/override/site.ini.append.php` für jeden Siteaccess,
- `settings/siteaccess/<siteaccess>/site.ini.append.php` für einen Siteaccess,
- `extension/<extension>/settings/site.ini.append.php` in einer Erweiterung (gelesen, wenn die Erweiterung aktiv ist).

## ManagedByDeployment

> enabled: Die Konfiguration dieses Siteaccess wird vom Deployment verwaltet  
> (Versionskontrolle), und die Administrationsoberfläche ändert sie auf dem Server nicht.  
> In einer Web-Anfrage antworten die Ansichten, die Einstellungen, Templates, erzeugten Code oder  
> Pakete schreiben (settings/edit, Editoren für Werkzeugleisten und Menüs, Template-Editor, Setup-  
> Assistenten und Code-Generatoren, Paket hochladen/installieren/deinstallieren/erstellen/exportieren;  
> die Liste ist expManagedConfiguration::LOCKED\_VIEWS), wie eine in \[SiteAccessRules\] abgeschaltete  
> Ansicht und sind in den Menüs ausgeblendet, und weder  
> eZINI::save() noch expIniEditor::save() schreibt eine Einstellungsdatei. Das gilt  
> für PHP-FPM, Apache und die Anwendungsserver, die PHP auf der Kommando-  
> zeile ausführen (Velocity, der eingebaute Server). Nur lesende Ansichten bleiben verfügbar. Kommando-  
> zeilenskripte sind nicht betroffen. Setzen Sie es in einer versionierten Einstellungsdatei, nie  
> aus der Administration. Siehe doc/eng-us/features/6.0/managed-by-deployment.md.  

Standard: `disabled`.

```ini
<?php /* #?ini charset="utf-8"?
# settings/override/site.ini.append.php
[ConfigurationSettings]
ManagedByDeployment=disabled
*/ ?>
```

## Siehe auch

- [Alle Blöcke von site.ini](README.md)
- [Alle INI-Dateien](../README.md)
- [Die Einstellungsseite](../../../anleitungen/einstellungsseite.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `settings/site.ini`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../../hilfe/freie-dokumentation.md).
