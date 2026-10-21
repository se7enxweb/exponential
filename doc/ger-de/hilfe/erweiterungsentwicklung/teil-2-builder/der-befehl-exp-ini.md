---
title: Einstellungen mit exp:ini ändern
audience: developer, designer, app-creator
category: extension-development
order: 50
summary: Jedes Verb des Einstellungsbefehls - get, set, add, rem, clear, toggle, copy, move, move-all, where, list, scopes - mit seinen Ebenen, Probeläufen, Sicherungen, JSON-Ausgabe und Exit-Codes, und Rezepte für häufige Aufgaben eines Builders.
keywords: exp:ini, INI-Befehl, Einstellungen auf der Kommandozeile, Probelauf, Sicherung, Ebene, ini command, settings command line, get, set, add, rem, clear, toggle, copy, move, move-all, where, list, scopes, dry-run, backup, json
type: help
---
# Einstellungen mit exp:ini ändern

Teil 2, Kapitel 5. `./console exp:ini` (das Skript `bin/php/ini.php`) liest und ändert Einstellungen in jeder Ebene von
der Kommandozeile aus. Es schreibt Dateien Zeile für Zeile und behält Kommentare, Leerzeilen und Reihenfolge bei; es
schreibt atomar, legt eine Sicherung an und leert danach den INI-Cache. Es ist das wichtigste Werkzeug des Builders,
und alles auf dieser Seite stammt aus seiner eigenen `--help`.

## Die Syntax

```text
./console exp:ini <action> <arguments> [options]
```

Eine Einstellung wird `file/Block/Variable` geschrieben (die Endung `.ini` ist optional); Arrays enden auf `[]`,
Hash-Einträge auf `[key]`. Zwei andere Schreibweisen werden ebenfalls angenommen: `site.ini:SiteSettings.SiteName` und
`"site.ini [SiteSettings] SiteName"`.

## Lesen

| Aktion | Beispiel | Zeigt |
|---|---|---|
| `get` | `exp:ini get site.ini/SiteSettings/SiteName` | den wirksamen Wert (oder, mit einer Ebene, den Wert, den die Datei dieser Ebene liefert) |
| `where` | `exp:ini where site.ini/SiteSettings/SiteName` | jede Datei, die ihn setzt, in Ladereihenfolge, und das Ergebnis |
| `list` | `exp:ini list site.ini/DebugSettings` | die Variablen eines Blocks oder die Blöcke einer Datei |
| `scopes` | `exp:ini scopes extension` | die Ebenen dieser Installation und das Verzeichnis, in das jede schreibt |
| `actions` | `exp:ini actions` | die eingebauten Aktionen und die von Erweiterungen registrierten |

`-s <siteaccess>` wertet `get`, `where` und `list` so aus, wie dieser Siteaccess sie sieht. Werte von Variablen, deren
Name `Password`, `Secret`, `Token` oder `Salt` enthält oder auf `Key` endet, werden maskiert, außer Sie geben
`--show-secrets` an.

## Schreiben

| Aktion | Beispiel | Tut |
|---|---|---|
| `set` | `exp:ini set site.ini/SiteSettings/SiteName "My site" global` | setzt eine Variable oder einen Hash-Eintrag |
| `add` | `exp:ini add 'site.ini/ExtensionSettings/ActiveExtensions[]' mysite_settings global` | hängt an ein Array an |
| `rem` | `exp:ini rem site.ini/SiteSettings/SiteName global` | entfernt eine Variable, einen Array-Wert oder einen Hash-Eintrag |
| `clear` | `exp:ini clear 'site.ini/RegionalSettings/SiteLanguageList[]' siteaccess:mysite` | schreibt die Rücksetzzeile, sodass die Werte früherer Dateien entfallen |
| `toggle` | `exp:ini toggle site.ini/DebugSettings/DebugOutput global` | schaltet zwischen enabled/disabled, true/false, yes/no, 1/0 um |
| `copy` | `exp:ini copy site.ini/DesignSettings/SiteDesign siteaccess:site siteaccess:ger` | kopiert eine Einstellung zwischen Ebenen |
| `move` | `exp:ini move site.ini/DesignSettings siteaccess:mysite extension:mysite_settings` | verschiebt einen Block oder eine ganze Datei |
| `move-all` | `exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension` | verschiebt jede Datei einer Ebene |

Setzen Sie `--` vor einen Wert, der mit einem Bindestrich beginnt: `exp:ini set site.ini/MySettings/Offset -- -1 global`.

## Ebenen

| Ebenenwort | Geschriebene Datei |
|---|---|
| `global` (oder `override`) | `settings/override/<file>.ini.append.php` |
| `siteaccess:<sa>` (oder nur `<sa>`) | `settings/siteaccess/<sa>/<file>.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/<file>.ini.append.php` |
| `extension:<ext>:siteaccess:<sa>` | `extension/<ext>/settings/siteaccess/<sa>/<file>.ini.append.php` |
| `default` | `settings/<file>.ini`; ohne `--allow-default` verweigert (verwenden Sie es nicht) |

Erweiterungen können in `ini.ini [IniCommandSettings] ScopeProviders[]` weitere Ebenen registrieren.

## Optionen, die Sie schützen

| Option | Standard | Wirkung |
|---|---|---|
| `--dry-run` | aus | den Diff zeigen, nichts schreiben |
| `--backup` / `--no-backup` | Sicherung | die Datei zuerst nach `var/backup/ini/<timestamp>/` kopieren |
| `--clear-cache` / `--no-clear-cache` | leeren | den INI-Cache nach einem Schreibvorgang leeren |
| `--create` / `--no-create` | anlegen bei set, add, clear, toggle, copy | eine fehlende Datei und einen fehlenden Block anlegen |
| `--json` | aus | ein JSON-Objekt: ok, code, action, message, warnings, data |
| `--root=<dir>` | die Installation | mit dem Einstellungsbaum einer anderen Installation arbeiten (ein Checkout, eine Kopie) |

Exit-Codes: `0` erledigt oder nichts zu ändern, `1` Bedienungsfehler, `2` nicht gefunden, `3` verweigert, `4`
Schreiben fehlgeschlagen. Skripte können sich darauf verlassen.

## Rezepte

**Die Debug-Ausgabe nur für Ihre eigene Adresse einschalten:**

```bash
./console exp:ini set site.ini/DebugSettings/DebugOutput enabled global
./console exp:ini set site.ini/DebugSettings/DebugByIP enabled global
./console exp:ini add 'site.ini/DebugSettings/DebugIPList[]' 192.0.2.10 global
```

(`192.0.2.10` ist eine Dokumentationsadresse; verwenden Sie Ihre.)

**Einer Website eine eigene Sprachliste geben:**

```bash
./console exp:ini clear 'site.ini/RegionalSettings/SiteLanguageList[]' siteaccess:mysite
./console exp:ini add 'site.ini/RegionalSettings/SiteLanguageList[]' eng-US siteaccess:mysite
./console exp:ini add 'site.ini/RegionalSettings/SiteLanguageList[]' ger-DE siteaccess:mysite
```

**Herausfinden, warum die Einstellung einer Erweiterung ignoriert wird:**

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions -s mysite
```

**Eine Änderung in einem Checkout vor dem Deployment prüfen:**

```bash
./console exp:ini where site.ini/DesignSettings/SiteDesign --root=/path/to/checkout
```

## Aus einem Skript

Dank `--json` und der Exit-Codes lässt sich `exp:ini` sicher in Deployment-Skripten verwenden:

```bash
./console exp:ini get site.ini/SiteSettings/SiteName --json
```

Lesen Sie die Felder `ok` und `data`, statt Text zu parsen.

## Verwandte Seiten

- Zurück: [Einstellungen in einer Erweiterung](erweiterungseinstellungen-fuer-builder.md)
- Weiter: [Einstellungen in der Administration](einstellungen-in-der-administration.md)
- [Einstellungen auf der Kommandozeile ändern](../../../../eng-us/features/6.0/exp-ini-command.md)
- [Konsole: exp:ini](../../../bc/6.0/console-exp-ini.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
