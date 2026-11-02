---
title: exp:expstatictargets
audience: developer, admin
category: examples
summary: Listet die Ziele des statischen Veröffentlichens (staticcache.ini [Target_<id>]) mit Siteaccess, Links und Ordner und prüft jedes.
keywords: Konsole, Befehl, exp:expstatictargets, expstatictargets, console, command, CLI, expstatictargets.php, exp:static:targets
type: generated
generated: exp:help:examples
---
# exp:expstatictargets

Listet die Ziele des statischen Veröffentlichens (staticcache.ini [Target_<id>]) mit Siteaccess, Links und Ordner und prüft jedes.

Skript `bin/php/expstatictargets.php`, Code in `kernel/private/classes/commands/expstatictargets.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:expstatictargets --help
./console exp:static:targets --help
php bin/php/expstatictargets.php --help
```

## Hilfetext

Was `--help` ausgibt, so wie der Befehl es schreibt:

```text
The targets of static publishing
Every target of staticcache.ini [StaticPublishSettings] TargetList[] with its siteaccess, how its links
are written, its public address and its folder, and what is wrong with its settings. Changes nothing;
exit 1 when a target has an error.

./console exp:static:targets
./console exp:static:targets --format=json
./console exp:static:targets --export=all > migrations/20261101120000_static_targets.ini

--export prints targets as an INI migration (also for a site package): their [Target_<id>] blocks and
TargetList[]. Credentials travel as their secret names, never their values.
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--format` | erforderlich | text (Standard) oder json (die Maschinenausgabe exp-static/1) |
| `--export` | erforderlich | diese Ziele (IDs durch Kommas getrennt, oder all) als INI-Migration ausgeben und nichts ändern |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/expstatictargets.php`, `kernel/private/classes/commands/expstatictargets.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
