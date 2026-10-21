---
title: Einstellungen mit ManagedByDeployment sperren
audience: government, admin, developer
category: public-sector
order: 360
summary: Schritt für Schritt - ManagedByDeployment für den Siteaccess der Administration in der Produktion einschalten, damit die Weboberfläche keine Einstellungen, Templates, Erweiterungen oder Pakete ändern kann, nachdem diese Einstellungen in die Versionsverwaltung verlegt wurden, und die Sperre prüfen.
keywords: ManagedByDeployment, Konfigurationssperre, Änderungsmanagement, Einstellungseditor, Template-Editor, Setup-Assistent, Pakete, RAD, Versionsverwaltung, Produktion, exp:ini, öffentlicher Sektor, configuration lock, change management, settings editor, template editor, setup wizard, packages, version control, production, public sector
type: help
---
# Einstellungen mit ManagedByDeployment sperren

Für Administratoren und Entwickler von Behörden mit Änderungsmanagement. Mit eingeschaltetem Schalter kann die
Administration keine Dateien `*.ini.append.php` oder `.tpl` mehr schreiben, keine Erweiterungen aktivieren, weder den
Setup-Assistenten noch Code-Generatoren ausführen und keine Pakete installieren
([Konfiguration, die vom Deployment verwaltet wird](../../../../../eng-us/help/government/security/managed-by-deployment.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Jede Einstellung und jedes Template, das die Administration auf dem Server geändert hat, ist in der
  Versionsverwaltung. Vergleichen Sie zuerst `settings/siteaccess/` und die Design-Ordner des Servers mit dem Repository.
- Geheimnisse bleiben in `settings/override/`, außerhalb der Versionsverwaltung.
- Die Kommandozeile funktioniert weiter (`exp:ini`, `bin/php/*.php`); Administratoren brauchen einen Änderungsprozess
  für Einstellungen.

## Schritte

1. In der versionierten `site.ini.append.php` des Siteaccess der Administration (oder in den versionierten
   Einstellungen einer Erweiterung):

   ```ini
   [ConfigurationSettings]
   ManagedByDeployment=enabled
   ```

2. Rollen Sie wie üblich aus; leeren Sie den INI-Cache und laden Sie PHP neu (oder
   `./console exp:velocity deploy --allow-root-user`).

## Prüfen, ob es funktioniert hat

- **Setup > Ini Einstellungen** zeigt Werte, ohne Links zum Bearbeiten.
- `/setup/rad`, `/setup/extensions` und der Template-Editor antworten wie eine abgeschaltete Ansicht (standardmäßig 404).
- Pakete: Die Liste bietet weder Hochladen noch Installieren an.
- Das Schreiben einer Einstellung aus der Debug-Leiste wird abgelehnt.

Schlüssel, die der Code bei der ersten Verwendung erzeugt (Audit-Schlüssel, Geheimnis der E-Mail-Einstellungen), werden
weiterhin geschrieben.

## Verwandte Seiten

- [Konfiguration, die vom Deployment verwaltet wird](../../../../../eng-us/features/6.0/managed-by-deployment.md)
- [Bereitstellung](../../../../anleitungen/bereitstellung.md)
- [Dateiberechtigungen und Eigentümer](../../../../../eng-us/help/government/security/file-permissions-and-ownership.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
