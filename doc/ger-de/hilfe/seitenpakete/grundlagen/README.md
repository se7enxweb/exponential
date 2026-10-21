---
title: "Grundlagen der Seitenpakete"
audience: admin, developer, hosting, designer
category: install-upgrade
order: 10
summary: Der Überblick über den Grundlagenteil der Hilfe zu Seitenpaketen. Was ein Seiteninstallationspaket ist, die Datei .ezpkg und package.xml, Pakettypen, Installationselemente, Abhängigkeiten, Installationsskripte und wie der Setup-Assistent, der Kickstarter und exp:install Pakete nutzen.
keywords: Seitenpaket, Seiteninstallationspaket, ezpkg, package.xml, Pakettypen, Installationselemente, Pakethandler, Abhängigkeiten, Installationsskripte, Setup-Assistent, Kickstarter, exp:install, Paket-Repository, JSON-Paket, importieren, installieren, site package, package types, install items, package handlers, dependencies, install scripts, setup wizard, package repository, import, install
type: help
---
# Grundlagen der Seitenpakete

Dieser Teil der Hilfe zu Seitenpaketen erklärt, wie ein Paket aufgebaut ist und wie Exponential es liest. Er ist für Administratoren, die
Websites installieren und wissen wollen, was geschieht, und für Entwickler, die Pakete ändern oder schreiben. Jede Aussage auf diesen
Seiten wurde gegen den Kernel-Code von 6.0.15 (`kernel/classes/ezpackage.php` und die Klassen darum herum), `settings/package.ini`, die
Paketquellen und den laufenden Paketserver geprüft.

Wenn Sie nur eine Website installieren wollen, brauchen Sie nichts davon: Der Setup-Assistent, der Kickstarter und `exp:install` erledigen
das für Sie (siehe [Eine Installationsmethode wählen](../../../installation/04-installationsweg-waehlen.md)). Lesen Sie weiter, wenn eine
Installation scheitert, wenn Sie ändern wollen, was eine neue Website enthält, oder wenn Sie ein Paket bauen.

## Inhalt

1. [Die Seiten dieses Teils](#die-seiten-dieses-teils)
2. [Reihenfolge zum Lesen](#reihenfolge-zum-lesen)
3. [Die Grundlagen in zehn Zeilen](#die-grundlagen-in-zehn-zeilen)
4. [Wo der Code liegt](#wo-der-code-liegt)
5. [Verwandte Seiten](#verwandte-seiten)

## Die Seiten dieses Teils

| Seite | Was sie beantwortet |
|---|---|
| [Was ein Seiteninstallationspaket ist](was-ein-seiteninstallationspaket-ist.md) | Was ein Seitenpaket ist, was es mitbringt und wie es sich von den Paketen unterscheidet, die es verlangt |
| [Die Datei .ezpkg](die-datei-ezpkg.md) | Das Archiv: sein Aufbau, die Prüfungen beim Hochladen und Importieren und wie Sie eines von Hand öffnen |
| [Referenz zu package.xml](referenz-zu-package-xml.md) | Jedes Element und Attribut von `package.xml`, wie `eZPackage` es liest und schreibt |
| [Pakettypen](pakettypen.md) | `site`, `sitestyle`, `contentclass`, `contentobject`, `extension`: `TypeList` und die Typen, die der Index tatsächlich nutzt |
| [Installationselemente und Handler](installationselemente-und-handler.md) | Elemente in `<install>`, `HandlerAlias` und was jeder Pakethandler tut |
| [Abhängigkeiten und die Installationsreihenfolge](abhaengigkeiten-und-die-installationsreihenfolge.md) | `<requires>`, `min-version` und die Reihenfolge, in der Dinge abgerufen und installiert werden |
| [Installationsskripte in einem Seitenpaket](installationsskripte-in-einem-seitenpaket.md) | `<settings><settings-file>` und die Funktionen, die der Installer aufruft |
| [Wie der Setup-Assistent Pakete nutzt](wie-der-setup-assistent-pakete-nutzt.md) | Die Schritte für Seitentypen, Sprachoptionen der Pakete und das Anlegen der Websites |
| [Wie der Kickstarter und die Konsole Pakete nutzen](wie-der-kickstarter-und-die-konsole-pakete-nutzen.md) | `kickstart.ini [site_types] Site_package` und `exp:install --package` |
| [Das lokale Paket-Repository](das-lokale-paket-repository.md) | `var/storage/packages/`, die Herstellerordner, `local`, `.cache` und eine Offline-`index.xml` |
| [Klassische und JSON-Pakete](klassische-und-json-pakete.md) | Die zwei Speicherformate eines Pakets |
| [Importieren oder installieren](importieren-oder-installieren.md) | `install_type` und was Importieren, Installieren, Deinstallieren und Entfernen bedeuten |

## Reihenfolge zum Lesen

- **Neu bei Paketen:** Beginnen Sie mit [Was ein Seiteninstallationspaket ist](was-ein-seiteninstallationspaket-ist.md), dann
  [Die Datei .ezpkg](die-datei-ezpkg.md) und [Importieren oder installieren](importieren-oder-installieren.md).
- **Eine Installation ist beim Abrufen von Paketen gescheitert:** [Wie der Setup-Assistent Pakete nutzt](wie-der-setup-assistent-pakete-nutzt.md),
  dann [Wenn der Paketserver nicht erreichbar ist](../paketserver/wenn-der-paketserver-nicht-erreichbar-ist.md).
- **Sie schreiben oder ändern ein Paket:** [Referenz zu package.xml](referenz-zu-package-xml.md),
  [Installationselemente und Handler](installationselemente-und-handler.md),
  [Abhängigkeiten und die Installationsreihenfolge](abhaengigkeiten-und-die-installationsreihenfolge.md) und
  [Installationsskripte in einem Seitenpaket](installationsskripte-in-einem-seitenpaket.md), dann die
  [Anforderungen an die Dokumentation](../anforderungen/README.md).

## Die Grundlagen in zehn Zeilen

1. Ein **Paket** ist ein Verzeichnis mit einer Definitionsdatei (`package.xml` oder `package.json` im JSON-Format) und den Dateien, die sie
   nennt.
2. Als eine Datei ausgeliefert, ist ein Paket ein mit gzip komprimiertes tar-Archiv mit der Endung `.ezpkg`.
3. Jedes Paket hat einen **Typ** (`<type value="..."/>`); ein **Seitenpaket** hat den Typ `site`.
4. Ein Seitenpaket **verlangt** andere Pakete (`<require type="ezpackage" name="..." min-version="..."/>`): Inhaltsklassen, Inhalte,
   Erweiterungen, Seitenstile.
5. Ein Seitenpaket kann **Installationsskripte** mitbringen (`<settings><settings-file filename="..."/>`), die Siteaccesses, Einstellungen,
   Rollen und Voreinstellungen einrichten.
6. Die Pakete, die es verlangt, bringen **Installationselemente** mit (`<install><item type="..."/>`), jedes von einem **Pakethandler**
   behandelt, der in `settings/package.ini [PackageSettings] HandlerAlias[]` genannt ist.
7. **Importieren** legt ein Paket ins lokale Repository, `var/storage/packages/<vendor>/<name>/`; auf der Website ändert sich nichts.
8. **Installieren** führt die Installationselemente aus. Pakete mit `install_type="import"` (jedes Seitenpaket des Index von 6.0.15) haben
   selbst nichts zu installieren.
9. Der Setup-Assistent, der Kickstarter und `exp:install` führen alle dieselben Schritte aus `kernel/setup/steps/ezstep_data.php` aus: Sie
   finden das Seitenpaket, importieren es und seine Anforderungen, installieren die Anforderungen und rufen die Installationsskripte auf.
10. Pakete kommen vom **Paketserver**, dessen Adresse `settings/package.ini [RepositorySettings] RemotePackagesIndexURL` ist; siehe
    [Der Paketserver](../paketserver/README.md).

## Wo der Code liegt

| Was | Wo |
|---|---|
| Das Paketmodell: lesen, schreiben, importieren, installieren, deinstallieren | `kernel/classes/ezpackage.php` (`eZPackage`) |
| Prüfungen des Archivs, bevor irgendetwas geschrieben wird | `kernel/classes/ezpackageuploadinspector.php` |
| Versionsvergleich (`min-version`) | `kernel/classes/ezpackageversion.php` |
| Handler für Installationselemente | `kernel/classes/packagehandlers/*/` |
| Assistenten zum Erstellen von Paketen | `kernel/classes/packagecreators/*/` |
| Schritt-Installer der Administration | `kernel/classes/packageinstallers/*/` |
| Die Paketansichten der Administration | `kernel/package/` (Einstiegspunkte), `kernel/private/classes/views/package/` |
| Die Setup-Schritte, die Pakete nutzen | `kernel/setup/steps/ezstep_site_types.php`, `ezstep_package_language_options.php`, `ezstep_create_sites.php` |
| Der Paketmanager für die Kommandozeile | `ezpm.php` im Wurzelverzeichnis der Installation, Code in `kernel/private/classes/commands/ezpm.php` |
| Einstellungen | `settings/package.ini` |

## Verwandte Seiten

- [Seitenpakete: hier beginnen](../README.md)
- [Glossar](../glossar-der-seitenpakete.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md): 7x und die Exponential Foundation ermutigen Sie ausdrücklich, Ihre eigenen
  Seitenpakete beim gemeinsamen Paketserver einzureichen.
- [Anleitung zu Paketen](../../../anleitungen/pakete.md): **Setup > Pakete** in der Administration
- [Was ein Exponential-Paket ist](../../designer/erweiterungen/was-ein-exponential-paket-ist.md): die Sicht des Designers
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
