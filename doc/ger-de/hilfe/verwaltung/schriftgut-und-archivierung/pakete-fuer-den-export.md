---
title: Pakete für den Export
audience: government, admin, developer
category: public-sector
order: 130
summary: Für Administratoren und Entwickler - wie Pakete von Exponential Inhaltsobjekte, Klassen und Dateien aus einer Installation heraustragen (der Paketassistent, .ezpkg in klassischem XML oder JSON, Herunterladen, Export mit ezpm.php), die beteiligten Rechte und ihr Nutzen und ihre Grenzen für Übergaben und Archive.
keywords: Pakete, ezpkg, Paketassistent, Export von Inhaltsobjekten, Export von Inhaltsklassen, JSON-Paketformat, package/export, package/create, ezpm.php, Herunterladen, Übergabe, Archiv, ManagedByDeployment, öffentlicher Sektor, packages, package wizard, content object export, content class export, JSON package format, download, transfer, archive, public sector
type: help
---
# Pakete für den Export

Diese Seite richtet sich an Administratoren und Entwickler. Ein **Paket** ist ein Verzeichnis mit einer `package.xml`
(oder `package.json`) und den Dateien, die sie nennt: Inhaltsklassen, Inhaltsobjekte, Erweiterungen, Einstellungen,
Bilder und Dokumente. Pakete lassen sich aus einer Installation erstellen und als eine `.ezpkg`-Datei exportieren
([Pakete](../../../anleitungen/pakete.md)).

## Inhalt

1. [Ein Inhaltspaket erstellen](#ein-inhaltspaket-erstellen)
2. [Es herunterladen](#es-herunterladen)
3. [Rechte und Sperren](#rechte-und-sperren)
4. [Nutzen und Grenzen für Schriftgut](#nutzen-und-grenzen-für-schriftgut)
5. [Verwandte Seiten](#verwandte-seiten)

## Ein Inhaltspaket erstellen

**Setup > Pakete** (`/package/list`), **Neues Paket erstellen** (`/package/create`): eine Kachel pro Assistent, den
Ihre Rolle verwenden darf, darunter **Export von Inhaltsobjekten** und **Export von Inhaltsklassen**. Der Assistent
fragt nach den Bestandteilen, dann nach Name, Zusammenfassung, Version, Lizenz und Maintainer sowie dem Speicherformat
(Classic XML oder JSON) und schreibt das Paket in das Repository `local`. Auf der Website ändert sich nichts
([Der Paketassistent](../../../funktionen/6.0/paketassistent.md)).

## Es herunterladen

- **Herunterladen** in der Liste oder auf der Seite des Pakets sendet ein mit gzip komprimiertes tar
  `<name>-<version>.ezpkg`; **Als JSON herunterladen** sendet das JSON-Format. Das Paket selbst wird nur gelesen.
- Von einer Shell aus: `php ezpm.php export <name> -d var/tmp --allow-root-user`.
- Pakete werden anderswo durch Hochladen und Installieren eingespielt, mit denselben Prüfungen des Archivs.

## Rechte und Sperren

| Aktion | Richtlinie |
|---|---|
| auflisten, lesen, herunterladen | `package/list`, `package/read`, `package/export` |
| erstellen | `package/create` |
| hochladen, installieren, entfernen | `package/import`, `package/install`, `package/remove` |

Mit `ManagedByDeployment=enabled` im Siteaccess der Administration sind das Erstellen, Exportieren, Hochladen,
Installieren und Deinstallieren von Paketen in der Weboberfläche gesperrt; die Kommandozeile funktioniert weiterhin
([Vom Deployment verwaltete Konfiguration](../../../funktionen/6.0/durch-deployment-verwaltet.md)).

## Nutzen und Grenzen für Schriftgut

- Gut geeignet, um eine Menge von Inhaltsobjekten mit ihren Dateien und Klassen in eine andere Exponential-Installation
  zu übertragen, zum Beispiel eine Kopie als Schriftgut, die als getrennte, schreibgeschützte Website geführt wird.
- Ein Paket enthält die Objekte so, wie sie exportiert wurden, nicht ihre Versionsgeschichte oder Audit-Einträge.
- Das Format ist spezifisch für Exponential; für die Langzeiterhaltung bewahren Sie zusätzlich ein Format auf, das Ihr
  Archiv annimmt (siehe „Dateiformate für die Langzeiterhaltung“).
- Pakete können Einstellungsdateien enthalten; prüfen Sie ein Paket, bevor Sie es an jemanden weitergeben.

## Verwandte Seiten

- [Pakete](../../../anleitungen/pakete.md)
- [Das JSON-Paketformat](../../../bc/6.0/package-json-format.md)
- [Inhalte exportieren](inhalte-exportieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
