# Dateikonsistenzprüfung und die Dateiliste des Releases

Diese Seite ist für Administratoren, die wissen möchten, ob die Dateien einer Installation nach der Installation geändert
wurden, und für Betreuer, die Releases erstellen. Setup > System Upgrade > **Überprüfe Dateikonsistenz** vergleicht jede
Datei mit einer Liste von Prüfsummen, die mit jedem Release ausgeliefert wird, `share/filelist.md5`. Seit April 2026
erstellen und prüfen zwei kleine Skripte diese Liste, sodass ein Release immer eine Liste trägt, die zu seinen Dateien
passt, und Sie dieselbe Prüfung auf der Kommandozeile ausführen können.

## Warum Sie das wollen

- Nach einer Aktualisierung, um sofort zu sehen, ob etwas zurückgeblieben ist oder von Hand bearbeitet wurde.
- Nach einem Vorfall, um zu sehen, ob eine Datei geändert wurde, ohne dass es jemand bemerkt hat.
- Als Prüfung vor dem Release Ihres eigenen Forks: Wird die Liste nicht erneuert, zeigt die Prüfung in der
  Administrationsoberfläche jede geänderte Datei als „geändert“.

## Eine Installation prüfen

Führen Sie sie aus dem Document Root aus, das der Webserver verwendet, denn die Pfade in der Liste sind relativ dazu:

```bash
cd /path/to/docroot
bash bin/shell/verifyfiles.sh            # summary and the files that differ
bash bin/shell/verifyfiles.sh --quiet    # exit status only (0 = all files match)
```

Exit-Status `0` bedeutet, dass jede Datei übereinstimmt. `1` bedeutet, dass sich etwas geändert hat, fehlt oder die
Liste nicht gelesen werden konnte. Das Skript führt `md5sum --check` auf `share/filelist.md5` aus.

Im Browser verwenden Sie **Setup > Upgrade-Prüfung > Überprüfe Dateikonsistenz**; sie liest dieselbe Datei und die
`share/filelist.md5` jeder aktiven Extension, die eine mitbringt. Seit Oktober 2026 gruppiert sie, was sie findet
(geändert, fehlend, unlesbar; Dateien, die git verfolgt, die das Manifest aber nicht aufführt, fehlerhafte und
unsortierte Zeilen als Hinweise), zeigt die Daten des Manifests und welche Extensions es abdeckt, hat eine Suche und
Filter, sagt, wie mit jedem Befund umzugehen ist, und bietet das Ergebnis als CSV oder Text an. Die Anleitung:
[Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md).

`php bin/php/checkmanifest.php` liest die Manifeste mit derselben Klasse wie die Seite (`expFileConsistencyReport`),
sodass beide auf demselben Baum übereinstimmen:

```bash
php bin/php/checkmanifest.php --all                        # the manifest of Exponential
php bin/php/checkmanifest.php --all --extensions           # and every extension's own manifest
php bin/php/checkmanifest.php --all --extensions --csv     # the findings as CSV
```

Eine Zeile des Manifests ist „32 Hexadezimalziffern, zwei Leerzeichen, ein Pfad relativ zum Verzeichnis des Manifests“.
Eine Zeile, die auf CRLF endet, wird wie eine gelesen, die auf LF endet. Eine Zeile, die nicht diese Form hat, einen Pfad
außerhalb der Installation nennt oder einen Pfad wiederholt, ist eine fehlerhafte Zeile: Sie wird übersprungen und
gemeldet, nie befolgt. Das Manifest einer Extension darf mit den Zeilen `name:`, `version:` und `files_count:` beginnen.

## Die Liste für ein Release erstellen (Betreuer)

```bash
cd /path/to/docroot
bash bin/shell/generatefilelist.sh --help      # every option and exclusion key (instant)
bash bin/shell/generatefilelist.sh --dry-run   # count what would be listed
bash bin/shell/generatefilelist.sh --list      # what is left out, and why
bash bin/shell/generatefilelist.sh             # write share/filelist.md5
```

Auf einer großen Installation mit vielen Extension-Checkouts gehen `--dry-run` und `--list` den ganzen Baum durch und
können Minuten dauern (gemessen: mehr als zwei Minuten auf einer großen Entwicklungsinstallation); führen Sie sie aus
einer Shell ohne kurzes Zeitlimit aus.

Die Standards sind das, was ein Release will: Alles, was Teil der Installation ist, wird gehasht, und alles, was es nicht
ist, wird ausgelassen. Standardmäßig ausgelassen:

| Schlüssel | Was übersprungen wird |
|---|---|
| `git` | `.git`, `.svn`, `.hg` des Wurzelverzeichnisses und jeder Extension, die ein eigener Checkout ist |
| `var`, `tmp` | was die Installation schreibt, während sie läuft |
| `vendor`, `extension_vendor` | `vendor/`, `node_modules/` |
| `caches` | Caches von Werkzeugen im Wurzelverzeichnis (`.phpunit.cache/`, `.sass-cache/`, `.pytest_cache/`) |
| `local` | Einstellungen von Editoren und Werkzeugen pro Rechner (`.idea/`, `.vscode/`, `.cursor/` und ähnliche) |
| `work` | ein privates Arbeitsverzeichnis der Betreuer, das nicht Teil eines Releases ist (der genaue Name wird von `--help` ausgegeben) |
| `backups` | `*~`, `#*#`, `.#*`, `*.orig`, `*.rej`, `*.bak`, `*.swp`, `*.swo`, `*.save` |
| `logs` | `*.log` |
| `os` | `.DS_Store`, `Thumbs.db`, `desktop.ini` |

Immer ausgeschlossen: `*.pyc`-Dateien, die temporäre `filelist.md5.tmp.*` und `share/filelist.md5` selbst (eine Liste
kann nicht ihre eigene Prüfsumme enthalten).

| Option | Bedeutung |
|---|---|
| `--dry-run` | Nur zählen, nichts schreiben. |
| `--list` | Zeigen, was ausgeschlossen ist (schließt `--dry-run` ein). |
| `--include=<key>` | Eine ausgeschlossene Gruppe wieder aufnehmen, zum Beispiel `--include=vendor`, um `vendor/` zu hashen. Ein unbekannter Schlüssel gibt die Liste der bekannten Schlüssel aus. |
| `--exclude=<value>` | Einen Ausschluss hinzufügen; kann wiederholt und mit `--include` kombiniert werden. Drei Formen: Ein einfacher Name wie `scratch` ist ein Verzeichnis, wo immer es liegt; ein Muster für `find -path` wie `./scratch/*` wird so verwendet, wie es angegeben ist; ein Datei-Glob wie `*.log` passt in jeder Tiefe auf Dateinamen. |
| `--extensions` | Auch `extension/*/share/filelist.md5` für Extensions neu schreiben, die bereits eine mitbringen (standardmäßig aus). |
| `--untracked` | In einem Git-Checkout werden standardmäßig nur verfolgte Dateien gehasht; dies hasht auch Dateien, die git nicht verfolgt. |

### Aus dem richtigen Verzeichnis ausführen

Wenn Ihre öffentliche Website aus einem Document Root ausgeliefert wird, das aus symbolischen Links auf die echte
Installation gebaut ist, erzeugen Sie die Liste in dem Verzeichnis, das der Webserver tatsächlich verwendet, nicht in der
echten Installation: Das aktuelle Verzeichnis von PHP während einer Webanfrage ist das Document Root, und dort sucht die
Prüfung der Administrationsoberfläche nach den Dateien.

## Release-Regeln für Betreuer

1. Schließen Sie zuerst jede Änderung ab und erzeugen Sie dann die Liste; jede spätere Bearbeitung lässt die Datei als
   geändert erscheinen.
2. Committen Sie die Liste in derselben Änderung, die die Version anhebt.
3. Eine Extension mit eigener `share/filelist.md5` braucht eine eigene Erneuerung in ihrem eigenen Release.

Im Release 6.0.14 (Juni 2026) wurde die Liste zwischen dem 5. und 7. Juni siebenmal committet
(`git log --format="%ad %h" --date=short v6.0.13..v6.0.14 -- share/filelist.md5`), während die letzten Korrekturen
eingingen; das ist das Symptom dafür, dass Schritt 1 in der falschen Reihenfolge getan wurde, und der Grund für die
Regel.

Die vollständigen Einzelheiten der Optionen sind die eigene Hilfe des Skripts: `bash bin/shell/generatefilelist.sh --help`.

## Verwandte Seiten

- [Paketlizenzen und Semantic Versioning](paketlizenzen-und-versionen.md), [das Produkt heißt Exponential](umbenennung-in-exponential.md)
- [Changelog 6.0.14](../../../changelogs/6.0/6.0.14.md)
- Chronik: [April 2026](../../geschichte/2026/2026-04.md), [Mai 2026](../../geschichte/2026/2026-05.md), [Juni 2026, erste Hälfte](../../geschichte/2026/2026-06a.md), [Juni 2026, zweite Hälfte](../../geschichte/2026/2026-06b.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
