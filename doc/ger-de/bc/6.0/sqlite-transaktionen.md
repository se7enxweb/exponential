# SQLite: Transaktionen warten in einer Schlange auf die Schreibsperre

Lesen Sie diese Seite, wenn Ihre Installation unter SQLite läuft (der Standard einer schnellen lokalen Installation).
Sie erklärt, was sich daran geändert hat, wie SQLite-Transaktionen beginnen, ob ein früheres Verschieben eines
Teilbaums Ihre Daten beschädigt hat, und die eine Einstellung, die Sie anpassen können. Die Änderung wurde am
2026-10-01 eingeführt und am 2026-10-02 abgeschlossen. Installationen mit MySQL, PostgreSQL, Oracle und MongoDB sind
nicht betroffen.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Transaktionen beginnen mit `BEGIN IMMEDIATE` und warten auf die Schreibsperre, statt mit „database is locked“ fehlzuschlagen. Das Verschieben eines Knotens schneidet bei den verschobenen Pfaden kein Zeichen mehr ab. |
| Wer betroffen ist | Nur SQLite-Installationen. |
| Wie Sie es prüfen | `grep -n "SQLiteTransactionWait" settings/site.ini`, dann öffnen Sie einige Knoten, die Sie früher unter SQLite verschoben haben. |
| Wie Sie es beheben | Für neue Arbeit ist nichts zu ändern. Verschieben Sie einen beschädigten Teilbaum erneut. |

## Was sich geändert hat

- **Transaktionen beginnen mit `BEGIN IMMEDIATE`.** Veröffentlichen und Bearbeiten neben anderen Schreibenden
  (asynchrones Veröffentlichen, Inhaltsjobs, Cronjobs) schlagen nicht mehr mit „database is locked“ fehl.
- **Der Beginn einer Transaktion wartet, bis er an der Reihe ist.** Er wartet auf die Schreibenden vor ihm, bis zu
  `[DatabaseSettings] SQLiteTransactionWait` Sekunden (Standard 25), statt nach dem Busy-Timeout von SQLite von
  5 Sekunden fehlzuschlagen. Ein Beginn, der die Sperre nie erhält, schlägt sofort mit dem Grund fehl und schreibt
  nichts: Entweder wird die ganze Transaktion übernommen oder nichts.
- **`subString()` ohne Länge gibt den Rest der Zeichenkette zurück.** Bisher entfernte das Verschieben eines Knotens
  unter SQLite das letzte Zeichen von `path_string` und `path_identification_string` des verschobenen Teilbaums.

## So prüfen Sie es

1. Stellen Sie sicher, dass die Einstellung vorhanden ist:

   ```bash
   grep -n "SQLiteTransactionWait" settings/site.ini
   ```

   Erwartete Ausgabe:

   ```
   81:SQLiteTransactionWait=25
   ```

2. Haben Sie mit einer früheren Version unter SQLite Teilbäume verschoben, öffnen Sie einige der verschobenen Knoten
   über ihren URL-Alias. Bei jedem Nachfahren eines solchen Teilbaums wurden `path_string` und
   `path_identification_string` um ein Zeichen gekürzt. Ein Knoten, dessen URL-Alias sich nicht auflöst, war
   betroffen.

## So beheben Sie es

- **Beschädigter Teilbaum:** Verschieben Sie den Teilbaum erneut. Das Verschieben baut die Pfade mit dem korrigierten
  `subString()` neu auf.
- **Lange laufende Schreibende:** Gibt der Beginn einer Transaktion weiterhin auf, erhöhen Sie die Wartezeit. Halten
  Sie sie unter dem Anfrage-Timeout des Webservers (Velocity: `Q.webserver.requestTimeout`, 30 s, sofern nicht
  geändert; PHP-FPM: `request_terminate_timeout`) und erhöhen Sie beide gemeinsam.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/site.ini` | `DatabaseSettings` | `SQLiteTransactionWait` | `25` (Sekunden) | Installation |

Überschreiben Sie sie in `settings/override/site.ini.append.php` und leeren Sie dann den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

## Verwandte Seiten

- [Datenbankunterstützung](../../hilfe/datenbankunterstuetzung.md): die unterstützten Datenbanken in ihrer offiziellen Reihenfolge, SQLite zuerst
- [Spezifikation: Datenbanktreiber (SQLite und Oracle)](../../spezifikationen/6.0/datenbanktreiber-sqlite-oracle.md)
- [SQLite-Datenbankunterstützung](../../funktionen/6.0/sqlite-datenbank.md)
- [Spezifikation: der SQLite3-Datenbanktreiber](../../spezifikationen/6.0/sqlite3-datenbanktreiber.md)
- [SQLite für Exponential Platform: kein Datenbankserver nötig](../../funktionen/6.0/plattform-sqlite-installation.md)
- [Platform-SQLite-Installer](../../spezifikationen/6.0/platform-sqlite-installer.md)
- Geschichte: [Januar 2024, 1. bis 15.](../../geschichte/2024/2024-01a.md), [Januar 2024, 16. bis 31.](../../geschichte/2024/2024-01b.md),
  [April 2026](../../geschichte/2026/2026-04.md), [Juni 2026, 16. bis 30.](../../geschichte/2026/2026-06b.md)
