---
title: "Exportieren und importieren mit ezpm"
audience: designer, admin
category: theme-extensions
order: 410
summary: Mit ezpm.php, dem Paketmanager auf der Kommandozeile, ein Paket anlegen, Klassen, Inhalte oder eine Erweiterung hinzufügen, es als .ezpkg exportieren und es anderswo importieren, prüfen und installieren.
keywords: ezpm, ezpm.php, Kommandozeile, create, add, export, import, install, list, info, check, validate, convert, ezcontentclass, ezextension, command line
type: help
---
# Exportieren und importieren mit ezpm

Diese Seite ist für Designer, die die Shell bevorzugen oder Pakete in einem Skript bauen. `ezpm.php` ist die
Kommandozeilen-Oberfläche des Paketmanagers; alle Befehle beginnen im Wurzelverzeichnis der Installation.
`php ezpm.php help` listet die Befehle auf, `php ezpm.php help <command>` erklärt einen.

## Die Befehle, die Sie brauchen

| Befehl | Tut |
|---|---|
| `create NAME [SUMMARY [VERSION [INSTALLTYPE]]]` | Ein leeres Paket (standardmäßig `install`, oder `import`) |
| `add PACKAGE ITEM [PARAMETERS]` | Fügt Klassen (`ezcontentclass` mit einer durch Kommas getrennten Liste von Klassenbezeichnern), Inhalte (`ezcontentobject`) oder Erweiterungen (`ezextension`) hinzu |
| `export PACKAGE -d DIR` | Schreibt das `.ezpkg` in ein Verzeichnis (`--format=json` für JSON) |
| `import FILE` | Importiert ein Archiv, mit denselben Prüfungen wie die Upload-Seite |
| `check PACKAGE` | Was eine Installation hier vorfinden würde, nach Remote-ID; nichts wird geschrieben |
| `install PACKAGE` | Installiert es (`--as-drafts` für Inhalte als Entwürfe) |
| `validate SOURCE` | Prüft ein Archiv, ein Verzeichnis oder ein Repository-Paket, ohne es zu importieren |
| `list`, `info PACKAGE` | Was vorhanden ist |

## Schritt für Schritt: ein Klassenpaket für Ihr Theme

```bash
php ezpm.php create mytheme_classes 'Content classes of My Theme' 1.0.0 install
php ezpm.php add mytheme_classes ezcontentclass article,event
php ezpm.php export mytheme_classes -d var/tmp
```

Erwartet: `var/tmp/mytheme_classes-1.0.0-1.ezpkg` (der Dateiname enthält die Release-Nummer, mit der das Paket angelegt
wurde).

## Schritt für Schritt: das Theme selbst als Paket

```bash
php ezpm.php create mytheme_pkg 'My Theme' 1.0.0 install
php ezpm.php add mytheme_pkg ezextension mytheme
php ezpm.php export mytheme_pkg -d var/tmp
```

Dafür muss das Exportieren von Erweiterungen eingeschaltet sein, und zuerst läuft die Geheimnisprüfung
([Eine Erweiterung als Paket exportieren](eine-erweiterung-als-paket-exportieren.md)).

## Schritt für Schritt: auf der empfangenden Installation

```bash
php ezpm.php validate /path/to/mytheme_classes-1.0.0-1.ezpkg
php ezpm.php import /path/to/mytheme_classes-1.0.0-1.ezpkg
php ezpm.php check mytheme_classes
php ezpm.php install mytheme_classes
```

Lesen Sie die Ausgabe von `check` vor `install`: Sie listet Konflikte auf (zum Beispiel eine vorhandene Klasse).

## Verwandte Seiten

- [ezpm: der Paketmanager auf der Kommandozeile](../../../../eng-us/features/6.0/ezpm-package-manager-cli.md)
- [Ihr Paket auf einer Testseite installieren](ihr-paket-auf-einer-testseite-installieren.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für
Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen früher
bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
