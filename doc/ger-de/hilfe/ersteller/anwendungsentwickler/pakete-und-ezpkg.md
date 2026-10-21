---
title: Pakete und .ezpkg
audience: app-creator, developer, admin
category: app-creation
order: 470
summary: Klassen, Inhalte und Einstellungen mit Paketen zwischen Installationen übertragen - was ein Paket enthält, die Repositories in var/storage/packages, ein Paket mit dem Paket-Assistenten erstellen, ezpm.php auf der Kommandozeile, Installieren und Pakete als Teil einer Anwendung.
keywords: Paket, Paket-Assistent, Export, Import, Inhaltsklassen, Inhaltsobjekte, Seitenpaket, Repository, installieren, Anwendungsentwickler, package, ezpkg, package.xml, package wizard, ezpm, export, import, content classes, content objects, site package, repository, install, app creator
type: help
---
# Pakete und .ezpkg

Diese Seite richtet sich an Anwendungsentwickler, die Inhaltsklassen oder Startinhalte mit ihrer Anwendung ausliefern
oder sie zwischen Entwicklung und Produktion übertragen. Ein **Paket** ist ein Verzeichnis mit einer `package.xml` und
den darin genannten Dateien; exportiert ist es eine einzelne `.ezpkg`-Datei ([Pakete](../../../anleitungen/pakete.md)).

![Setup, Pakete: die Pakete in den Repositories der Installation, mit Import und Export](../../../../eng-us/help/images/creators/packages-list.webp)

## Inhalt

1. [Was ein Paket enthält](#was-ein-paket-enthält)
2. [Wo Pakete liegen](#wo-pakete-liegen)
3. [Ein Paket erstellen](#ein-paket-erstellen)
4. [Auf der Kommandozeile](#auf-der-kommandozeile)
5. [Installieren](#installieren)
6. [Pakete in einer Anwendung](#pakete-in-einer-anwendung)

## Was ein Paket enthält

Inhaltsklassen, Inhaltsobjekte (mit ihren Platzierungen), Erweiterungen, Einstellungsdateien, Bilder und Dokumente. Ein
Paket wird **installiert** (seine Elemente werden angelegt), **importiert** (aufbewahrt, wie ein Seitenpaket) oder
**exportiert** (als `.ezpkg` geschrieben).

## Wo Pakete liegen

In Repositories unter `var/storage/packages/`: `local` für Pakete, die hier erstellt oder importiert wurden, und ein
Verzeichnis je Anbieter (`7x` ...). Seitenpakete, die der Installer verwendet, sind unter **Setup > Pakete** als
**Installer-Quelle** markiert.

## Ein Paket erstellen

**Setup > Pakete > Neues Paket erstellen** (`/package/create`) bietet Assistenten an: Export von Inhaltsobjekten,
Export von Inhaltsklassen, Export von Erweiterungen, Seitenstil. Sie geben die Bestandteile, den Namen, die
Zusammenfassung, die Version, die Lizenz und den Betreuer an sowie das Format (klassisches XML oder JSON); der letzte
Schritt zeigt alles noch einmal zur Prüfung, bevor in `local` geschrieben wird
([Der Paket-Assistent](../../../../eng-us/features/6.0/package-wizard.md)).

## Auf der Kommandozeile

```bash
php ezpm.php list --allow-root-user
php ezpm.php export <package> -d var/tmp --allow-root-user
php ezpm.php import var/tmp/<package>.ezpkg --allow-root-user
php ezpm.php help --allow-root-user
```

Uploads und Importe werden geprüft, bevor irgendetwas geschrieben wird: Eine Datei, die kein Paket ist, zu groß ist oder
einen Pfad außerhalb des Pakets, einen Link oder ein Gerät enthält, wird abgewiesen.

## Installieren

Beim Installieren werden die Klassen und Objekte angelegt; vorhandene Klassen werden über Kennung und Remote-ID
zugeordnet. Prüfen Sie auf der Seite des Pakets, was es tun wird, bevor Sie auf Installieren klicken, und tun Sie es
zuerst auf Staging. Pakete können Inhalte auch als Entwürfe installieren
([JSON-Paketformat](../../../bc/6.0/package-json-format.md)).

## Pakete in einer Anwendung

- Liefern Sie Ihre Klassen als Paket in der Erweiterung aus (`share/` oder ein Ordner `packages/`) und installieren Sie
  es aus Ihrem Installationsbefehl, damit jede Installation dieselben Klassen und Kennungen hat.
- Halten Sie Klassenkennungen und Remote-IDs stabil; Templates und Code hängen davon ab.
- Für Änderungen an Klassen nach dem ersten Release schreiben Sie einen Upgrade-Schritt, statt das Paket neu zu
  installieren.

## Verwandte Seiten

- [Inhaltsklassen für Websites](../website-ersteller/inhaltsklassen-fuer-websites.md)
- [Als Erweiterung paketieren](als-erweiterung-paketieren.md)
- [Kommandozeile des Paketmanagers ezpm](../../../../eng-us/features/6.0/ezpm-package-manager-cli.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
