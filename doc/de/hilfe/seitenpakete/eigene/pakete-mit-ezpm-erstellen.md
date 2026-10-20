---
title: Pakete mit ezpm erstellen
audience: developer, hosting
category: install-upgrade
order: 91
summary: Die Pakete, die Ihr Seitenpaket verlangt, mit ezpm.php aus einer Shell bauen - anlegen, Klassen, Inhalte oder eine Erweiterung hinzufügen, den Hersteller und andere Felder setzen, exportieren, prüfen und kontrollieren - in einer Form, die Sie skripten und für jedes Release erneut ausführen können.
keywords: ezpm, ezpm.php, create, add, set, export, validate, check, convert, import, list, info, ezcontentclass, ezcontentobject, ezextension, Hersteller, repack_extension.sh, Kommandozeile, vendor, command line
type: help
---
# Pakete mit ezpm erstellen

Diese Seite ist für Entwickler und Hoster, die ein Skript dem Browser vorziehen. `ezpm.php` im Wurzelverzeichnis der Installation (auch
`php bin/php/console ezpm`) ist der Paketmanager für die Kommandozeile; er erstellt dieselben Pakete für Klassen, Inhalte und Erweiterungen
wie der [Paketassistent](pakete-mit-dem-assistenten-erstellen.md), und weil er ein Befehl ist, lässt sich der Export Ihrer Demo-Inhalte für
jedes Release mit denselben Optionen wiederholen.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, das fertige Seitenpaket per Pull Request an
se7enxweb/exponential.packages.exponential.earth einzureichen: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).

## Inhalt

1. [Die Befehle, die Sie brauchen](#die-befehle-die-sie-brauchen)
2. [Ein Klassenpaket](#ein-klassenpaket)
3. [Ein Inhaltspaket](#ein-inhaltspaket)
4. [Ein Erweiterungspaket](#ein-erweiterungspaket)
5. [Felder setzen](#felder-setzen)
6. [Exportieren, prüfen, kontrollieren](#exportieren-prüfen-kontrollieren)
7. [Ein Release-Skript](#ein-release-skript)

## Die Befehle, die Sie brauchen

Aus `php ezpm.php help` und `kernel/private/classes/commands/ezpm.php`:

| Befehl | Aufruf | Tut |
|---|---|---|
| `create` | `create NAME [SUMMARY [VERSION [INSTALLTYPE]]]` | ein neues, leeres Paket; `INSTALLTYPE` ist `install` (Standard) oder `import` |
| `add` | `add PACKAGE ITEM [ITEMPARAMETERS]...` | fügt `ezcontentclass`, `ezcontentobject` (Alias `ezcontentsubtree`), `group` und Elemente anderer Handler wie `ezextension` hinzu |
| `set` | `set PACKAGE ATTRIBUTE ATTRIBUTEVALUE` | `summary`, `description`, `vendor`, `priority`, `type`, `extension`, `source`, `version`, `licence`, `state` |
| `export` | `export PACKAGE [-d DIRECTORY] [--format=xml\|json]` | schreibt das Archiv `.ezpkg` |
| `validate` | `validate SOURCE` | prüft ein Archiv, ein Verzeichnis oder einen Paketnamen, ohne zu importieren |
| `check` | `check PACKAGE [--all]` | was eine Installation hier vorfinden würde, nach Remote-ID; schreibt nichts |
| `list`, `info` | `list`, `info PACKAGE` | die Pakete der Repositorys, die Details eines Pakets |

Globale Optionen umfassen `-s`/`--siteaccess`, `-l`/`--login` mit `-p`/`--password` und `-r`/`--repos`; die vollständige Tabelle steht auf
[ezpm: der Paketmanager auf der Kommandozeile](../../../../en/us/features/6.0/ezpm-package-manager-cli.md).

> **Warnung**
> Führen Sie ezpm auf einer Testinstallation aus. `install` und `import` ändern die Installation; `create`, `add`, `set` und `export` schreiben
> nur Pakete, aber `add` von Inhalten liest den ganzen Teilbaum und kann eine Weile dauern.

## Ein Klassenpaket

```bash
php ezpm.php create acme_classes 'Acme content classes' 1.0.0 install
php ezpm.php add acme_classes ezcontentclass article,folder,acme_product
php ezpm.php set acme_classes vendor acme
php ezpm.php set acme_classes type contentclass
php ezpm.php export acme_classes -d var/tmp
```

`add ezcontentclass` nimmt ein Argument: Klassen-IDs oder Bezeichner, durch Kommas getrennt. Ein unbekannter stoppt den Befehl mit der
Meldung, dass die Inhaltsklasse mit diesem Bezeichner nicht existiert.

## Ein Inhaltspaket

```bash
php ezpm.php create acme_site_democontent 'Acme demo content' 1.0.0 install
php ezpm.php add acme_site_democontent ezcontentsubtree /content/demo \
    --exclude-classes --exclude-templates --language=eng-US,ger-DE
php ezpm.php set acme_site_democontent vendor acme
php ezpm.php set acme_site_democontent type contentobject
php ezpm.php export acme_site_democontent -d var/tmp
```

Der Ausgangspunkt ist eine Knoten-ID oder ein URL-Pfad; alles darunter wird aufgenommen. Die Optionen von `add ezcontentobject`:
`--include-classes`/`--exclude-classes`, `--include-templates`/`--exclude-templates`, `--node-main`/`--node-selected`,
`--siteaccess=sa,sa2`, `--language=loc1,loc2`, `--current-version`/`--all-versions`, `--minimal-template-set`. Lassen Sie die Klassen weg,
wenn Ihr Klassenpaket sie mitbringt, damit sie an einer Stelle definiert sind.

## Ein Erweiterungspaket

`bin/repack_extension.sh` im Quell-Repository von 7x zeigt die Abfolge, die das Team von 7x für ein Erweiterungspaket nutzte:

```bash
php ezpm.php create acme_extension 'Acme extension' '1.0.0' 'install'
php ezpm.php add acme_extension ezextension acme
php ezpm.php set acme_extension vendor acme
php ezpm.php set acme_extension type extension
php ezpm.php set acme_extension state stable
```

Das Exportieren einer Erweiterung ist standardmäßig aus: `add ezextension` verweigert, bis die Erweiterung in
`package.ini [ExtensionExportSettings] AllowedExtensions[]` steht (oder `*`), und es verweigert immer eine Erweiterung mit geheimen Werten
oder Schlüsselmaterial ([Mit ezpm exportieren](../../../../en/us/help/designers/extensions/exporting-with-ezpm.md)).

## Felder setzen

`set` ändert ein Feld der Paketdefinition und eröffnet, wie `add` und `delete`, laut Hilfe des Befehls ein neues Release, wenn noch keines
offen ist. `create` beginnt jedes Paket mit Release `1`, der Lizenz `GPL`, dem Zustand `alpha`, ohne Hersteller und mit dem angemeldeten
Benutzer (falls vorhanden) als Betreuer. Setzen Sie mindestens:

| Attribut | Beispiel | Warum |
|---|---|---|
| `vendor` | `acme` | das Repository-Verzeichnis, in das das Paket importiert wird |
| `type` | `contentclass`, `contentobject`, `extension` | der Typ, den der Index und die Richtlinien nutzen |
| `licence` | `GPL-2.0-or-later` | die Lizenz, die die Paketseite zeigt ([Lizenz und Dokumente](../anforderungen/lizenz-und-dokumente.md)) |
| `state` | `stable` | einer aus `package.ini [PackageSettings] StateList[]` |

## Exportieren, prüfen, kontrollieren

```bash
php ezpm.php export acme_classes -d var/tmp                 # classic format
php ezpm.php export acme_classes -d var/tmp --format=json   # JSON format, .json.ezpkg
php ezpm.php validate var/tmp/acme_classes-1.0.0-1.ezpkg
php ezpm.php check acme_site_democontent --all
```

`export` nennt das Archiv `<name>-<version>-<release>.ezpkg` (`eZPackage::exportName()`). Ein Paketserver und eine `index.xml` nutzen meist
`<name>.ezpkg`, den Namen, den `build.sh` vergibt: Entpacken Sie den Export nach `packages/<name>/` Ihres Quell-Repositorys und lassen Sie
`build.sh` das veröffentlichte Archiv erstellen, damit jedes Paket eines Release auf dieselbe Weise gebaut wird
([Mit build.sh neu bauen](mit-build-sh-neu-bauen.md)). Die zwei Formate sind auf [Klassische und JSON-Pakete](../grundlagen/klassische-und-json-pakete.md)
erklärt.

## Ein Release-Skript

Halten Sie die Befehle in Ihrem Quell-Repository, damit das nächste Release sie genau wiederholt:

```bash
#!/usr/bin/env bash
set -euo pipefail
cd /path/to/test-installation
php ezpm.php create acme_site_democontent 'Acme demo content' "$1" install
php ezpm.php add acme_site_democontent ezcontentsubtree /content/demo --exclude-classes
php ezpm.php set acme_site_democontent vendor acme
php ezpm.php export acme_site_democontent -d "$HOME/acme-build"
```

Führen Sie es für einen Paketnamen aus, der noch nicht in den Repositorys der Testinstallation liegt (`php ezpm.php list` zeigt sie), damit
jeder Lauf mit einem leeren Paket beginnt.

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [ezpm: der Paketmanager auf der Kommandozeile](../../../../en/us/features/6.0/ezpm-package-manager-cli.md)
- [Das JSON-Paketformat](../../../../en/us/bc/6.0/package-json-format.md)
- [Klassen und Inhalte ändern](klassen-und-inhalte-aendern.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
