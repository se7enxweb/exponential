---
title: exp:updateniceurls
audience: developer, admin
category: examples
summary: Importiert alte URL-Aliase und aktualisiert die URL-Aliase aller Knoten (--update-nodes).
keywords: Konsole, Befehl, exp:updateniceurls, updateniceurls, console, command, CLI, updateniceurls.php
type: generated
generated: exp:help:examples
---
# exp:updateniceurls

Importiert alte URL-Aliase und aktualisiert die URL-Aliase aller Knoten (--update-nodes).

Skript `bin/php/updateniceurls.php`, Code in `kernel/private/classes/commands/updateniceurls.php`. Führen Sie es im Wurzelverzeichnis der Installation aus, als der Benutzer, unter dem der Webserver läuft:

```bash
./console exp:updateniceurls --help
php bin/php/updateniceurls.php --help
```

## Optionen

| Option | Wert | Hilfe |
|---|---|---|
| `--db-host` | erforderlich | Datenbankhost |
| `--db-user` | erforderlich | Datenbankbenutzer |
| `--db-password` | erforderlich | Datenbankpasswort |
| `--db-database` | erforderlich | Datenbankname |
| `--db-type` | erforderlich | Datenbanktreiber, Alias für --db-driver |
| `--db-driver` | erforderlich | Datenbanktreiber |
| `--sql` |  | SQL-Abfragen anzeigen |
| `--no-import` |  | Deaktiviert alle Importroutinen. Um bestimmte zu aktivieren, die Optionen --import-* verwenden. |
| `--import` |  | Aktiviert alle Importroutinen. |
| `--import-nodes` |  | Aktiviert den Import von URLs aus den alten Knotendaten. |
| `--import-aliases` |  | Aktiviert den Import alter URLs (System und angepasst). |
| `--import-redirections` |  | Aktiviert den Import von URLs, die auf die richtige URL weiterleiten (d. h. Verlauf). |
| `--import-wildcards` |  | Aktiviert den Import von URLs, die mit Platzhaltern auf die richtige URL weiterleiten (d. h. Verlauf). |
| `--no-update-nodes` |  | Deaktiviert das Aktualisieren der URLs der Knoten von Inhaltsobjekten. |
| `--update-nodes` |  | Aktiviert das Aktualisieren der URLs der Knoten von Inhaltsobjekten. |
| `--verify-data` |  | Prüft die Datenbank, nachdem neue Daten eingefügt wurden; nur zur Fehlersuche verwenden. |
| `--interactive` |  | Aktiviert den interaktiven Modus für --verify-data, der die Ausführung bei Datenbankfehlern anhält und eine Prüfung von Hand erlaubt. |
| `--backup-tables` | erforderlich | Sichert nach jeder Phase (Import oder Update) die Tabellen ezurlalias und ezurlalias_ml, die Sicherungstabellen tragen den Originalnamen mit dem bei dieser Option angegebenen Suffix. Hinweis: nur zur Fehlersuche und nur mit MySQL verwenden. |
| `--column-width` | erforderlich | Die ungefähre Breite des Ausgabeblocks, Standard 72. |
| `--fetch-limit` | erforderlich | Die Anzahl der auf einmal geholten Einträge; ein höherer Wert kann die Gesamtzeit verkürzen, erhöht aber auch den Speicherverbrauch, Standard 200. |

Aus den Optionsdefinitionen im Code gelesen (`startup()`, `getOptions()`, `options()` oder `ezcConsoleOption`). Jedes Skript nimmt außerdem die Standardoptionen des Skript-Frameworks (`--help`, `--siteaccess`, `--debug`, `--quiet`, `--allow-root-user` ...).

## Siehe auch

- [Alle Konsolenbefehle](README.md)

---

Erzeugt von `./console exp:help:examples --language=ger-de` aus `bin/php/updateniceurls.php`, `kernel/private/classes/commands/updateniceurls.php`. Bearbeiten Sie diese Seite nicht: Ändern Sie die Quelle oder die deutsche Übersetzung und erzeugen Sie sie neu ([wie](../README.md#so-entstehen-die-generierten-seiten)). Freie Dokumentation: [GFDL 1.3 oder später](../../hilfe/freie-dokumentation.md).
