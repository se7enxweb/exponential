---
title: Die Konfiguration mit ManagedByDeployment sperren
audience: government, admin, developer
category: public-sector
order: 340
summary: Schritt für Schritt - ManagedByDeployment für den Siteaccess der Administration in der Produktion einschalten, sodass Einstellungen, Templates, Erweiterungen und Pakete sich nur über die Versionsverwaltung ändern lassen, und prüfen, was gesperrt ist und was offen bleibt.
keywords: ManagedByDeployment, Konfigurationssperre, Änderungssteuerung, Einstellungseditor, Template-Editor, Pakete, Versionsverwaltung, Deployment, Produktion, Siteaccess, configuration lock, change control, settings editor, template editor, packages, version control, deployment, production
type: help
---
# Die Konfiguration mit ManagedByDeployment sperren

Diese Aufgabe ist für Administratoren, die möchten, dass jede Änderung an der Konfiguration in der Produktion die
Versionsverwaltung und das Änderungsgremium durchläuft. Hintergrund:
[Konfiguration, die vom Deployment verwaltet wird](../../../../../eng-us/help/government/operations/upgrades-and-change/managed-by-deployment.md).

## Inhalt
1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Prüfen, ob es funktioniert hat](#prüfen-ob-es-funktioniert-hat)
4. [Fallstricke](#fallstricke)
5. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie beginnen

- Einstellungen und Templates der Site sind in der Versionsverwaltung, und Änderungen erreichen die Produktion durch ein
  Deployment.
- Niemand verlässt sich in der Produktion auf den Einstellungs- oder Template-Editor im Browser (verlegen Sie diese
  Arbeit auf Test).
- Eine genehmigte Änderung für die Sperre selbst.

## Schritte

1. Fügen Sie in der Versionsverwaltung den Einstellungen des Siteaccess der Administration in der Produktion hinzu (nie
   aus der Administration geschrieben):

   ```ini
   # settings/siteaccess/<admin siteaccess>/site.ini.append.php
   [ConfigurationSettings]
   ManagedByDeployment=enabled
   ```

   Um nur die Produktion zu sperren, während Test bearbeitbar bleibt, setzen Sie es in die Variantendatei der Produktion
   (`site.ini.prod.append.php`, mit `EXP_ENV=prod`)
   ([Umgebungsspezifische Einstellungen einrichten](umgebungsspezifische-einstellungen-einrichten.md)). Sperren Sie jeden
   Siteaccess, den ein Administrator verwenden kann.
2. Rollen Sie aus, dann `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` und neu laden
   (`./console exp:velocity deploy --allow-root-user`).

## Prüfen, ob es funktioniert hat

- **Setup > Ini Einstellungen** zeigt Einstellungen, bietet aber kein Bearbeiten an; `/settings/edit` antwortet wie eine
  abgeschaltete Ansicht (standardmäßig 404).
- **Setup > Extensions** bietet keine Aktivierung an; Hochladen und Installieren von Paketen sind verschwunden; der
  Template-Editor wird nicht angeboten.
- **Setup > Cache-Verwaltung**, **Systeminformationen** und **Logdateien** funktionieren weiterhin.
- In der Shell funktionieren `./console exp:ini` und `bin/php/*.php` weiterhin (die Werkzeuge des Deployments).

## Fallstricke

- Rollen, Workflows, Sektionen und Zustände sind Daten in der Datenbank: nicht gesperrt; steuern Sie sie mit Rollen und
  dem Audit-Protokoll.
- Das Installieren eines Pakets von der Kommandozeile schreibt weiterhin in die Datenbank.
- Die Dateiberechtigungen des Release-Verzeichnisses bleiben der stärkere Schutz
  ([Datei- und Verzeichnisberechtigungen prüfen](datei-und-verzeichnisberechtigungen-pruefen.md)).

## Verwandte Seiten
- [Änderungsmanagement und Änderungsgremien](../../../../../eng-us/help/government/operations/upgrades-and-change/change-management-and-advisory-boards.md)
- [Konfiguration unter Versionsverwaltung](../../../../../eng-us/help/government/operations/hosting/configuration-under-version-control.md)
- [Vom Deployment verwaltet (Funktion)](../../../../../eng-us/features/6.0/managed-by-deployment.md)
- [Die Administration beschränken (Hosting)](../../../hosting/sicherheit/die-administration-einschraenken.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
