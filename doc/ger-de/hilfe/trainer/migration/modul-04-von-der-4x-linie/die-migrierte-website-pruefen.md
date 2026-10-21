---
title: "Lektion 4.4: Die migrierte Website überprüfen"
audience: trainer, admin, developer
category: training
order: 44
summary: Die Teilnehmenden überprüfen eine migrierte Website so, wie Kapitel 14.12 es beschreibt - Schema gegen die Referenz, Zählungen gegen die alte Datenbank, URL-Aliase, Bilder, die Upgrade-Prüfung der Administration, jeden Siteaccess, Bearbeiten, Anmelden, Cronjobs und Logs - und erkennen die häufigen Probleme und ihre Lösungen. 45 Minuten.
keywords: migration zu exponential, überprüfen, überprüfung, zählungen, upgrade-prüfung, datenbankkonsistenz, suchindex, logs, häufige probleme, migrating to exponential, verify, verification, ezsqldiff, db_schema.dba, counts, verify_aliases, updateniceurls, upgrade check, database consistency, search index, cron, logs, common issues
type: help
---
# Lektion 4.4: Die migrierte Website überprüfen

Eine Migration ist abgeschlossen, wenn sie überprüft ist, nicht wenn das letzte Skript endet. Diese Lektion
vermittelt die zehn Prüfungen aus Kapitel 14.12 und die Tabelle häufiger Probleme aus Kapitel 14.13, damit die
Teilnehmenden einen ausgelassenen Schritt von einem echten Problem unterscheiden können und wissen, wo die Lösung
steht.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Migration zu Exponential](../README.md) |
| Modul | [Modul 4: Von der 4.x-Linie](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 4.3](dateien-einstellungen-und-code.md); ein Administratorkonto und Shell-Zugang zu einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Prüfungen aus Kapitel 14.12 aufzählen und die nur lesenden davon ausführen;
- die Ausgabe eines Schemavergleichs lesen und sagen, ob eine Zeile einen ausgelassenen Schritt bedeutet;
- die Zählungen der alten und der migrierten Datenbank vergleichen;
- in der Tabelle aus Kapitel 14.13 die Lösung für fünf häufige Probleme finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Kapitel 14.12](../../../../installation/14-migration-von-4x.md#1412-die-inhalte-nach-der-migration-prüfen)
und [14.13](../../../../installation/14-migration-von-4x.md#1413-häufige-probleme).

1. **Schema gegen die Referenz** (3 Minuten). `share/db_schema.dba` ist das Schema der aktuellen Version;
   `ezsqldiff.php` gibt das SQL aus, das die Datenbank in die Referenz überführen würde. Lesen Sie die Ausgabe, führen
   Sie sie nicht aus: Eine Zeile mit `CREATE TABLE` oder `ADD` benennt einen ausgelassenen Schritt, eine Zeile mit
   `DROP TABLE` eine Tabelle, die nur Ihre Datenbank hat (oft die Ihrer eigenen Erweiterung). Die **Datenbankkonsistenz
   Überprüfung** der Administration zeigt dieselbe Art von SQL.
2. **Zählungen** (2 Minuten). Objekte, Attribute und Knoten müssen genau übereinstimmen; `ezurlalias_ml` wächst, wenn
   Aliase neu aufgebaut werden.
3. **Aliase, Bilder, Siteaccesses** (3 Minuten). `updateniceurls.php --update-nodes`, dann `verify_aliases.php`; eine
   Seite jeder Klasse mit Bildern öffnen; `eztemplatecheck.php -s<siteaccess>` und die URL-Liste aus Lektion 4.1.
4. **Bearbeiten, Benutzer, Cron, Logs** (3 Minuten). In jeder Sprache ein Objekt jeder Klasse mit Rich Text, Bild und
   Beziehung anlegen, bearbeiten und veröffentlichen; sich als Redakteur mit einem alten Hash-Typ anmelden und prüfen,
   dass er sich in Typ 7 geändert hat; die Cronjobs ausführen; die Logs bleiben ruhig.
5. **Häufige Probleme** (4 Minuten). Wählen Sie fünf Zeilen aus 14.13 und zeigen Sie Ursache und Lösung: doppelter
   Eintrag im Index der Remote-ID, unbekannte Spalte aus einem Reparaturskript, `ArgumentCountError` im Konstruktor
   eines Datentyps, `?` statt Zeichen mit Akzent, Benutzer, die sich nicht anmelden können.

### Häufige Fragen

**Die Suche findet nach der Migration nichts.** Bauen Sie den Index neu auf, statt dem alten zu vertrauen:
`php bin/php/updatesearchindex.php --clean` (14.11). Das ist meist der längste Einzelschritt und kann nach dem
Umschalten abgeschlossen werden, wenn die Suche eine Weile unvollständig sein darf (14.15).

**Können wir das SQL ausführen, das `ezsqldiff.php` ausgibt?** Nein. Reparieren Sie, indem Sie die Sicherung einspielen
und den ausgelassenen Schritt ausführen (14.12).

**Veraltete Seiten nach dem Umschalten?** Alte Caches oder ein Cache, der vor dem Reload mit altem Code gefüllt wurde:
`php bin/php/ezcache.php --clear-all --purge` nach dem PHP-Reload (14.13).

### Stolperfallen

- **Während der Übung eine Reparaturoption auslösen.** `verify_aliases.php` ohne `--fix` meldet nur; die
  Teilnehmenden verwenden es hier so.
- **Zählungen nach dem Neuaufbau der Aliase vergleichen.** Die Anzahl der Aliase wächst; vergleichen Sie Objekte,
  Attribute und Knoten.
- **Bearbeitungstests überspringen.** Viele Probleme zeigen sich erst, wenn jemand veröffentlicht.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation und ein Administratorkonto. Jeder Schritt liest nur; nichts wird
repariert oder geändert.

1. Öffnen Sie in der Administration **Setup > Upgrade-Prüfung** und klicken Sie auf **Überprüfe Datenbankkonsistenz**.
   Lesen Sie das Ergebnis: Gibt es SQL, das die Datenbank angleichen würde? Führen Sie es nicht aus.
2. Prüfen Sie im Wurzelverzeichnis der Installation die URL-Alias-Tabellen und lesen Sie den Bericht (ohne `--fix`):

   ```bash
   php bin/php/verify_aliases.php --verbose
   ```

3. Zählen Sie Objekte, Attribute und Knoten:

   ```bash
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT COUNT(*) FROM ezcontentobject;"
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT COUNT(*) FROM ezcontentobject_attribute;"
   sqlite3 -readonly var/storage/sqlite3/exponential.db "SELECT COUNT(*) FROM ezcontentobject_tree;"
   ```

   Vergleichen Sie sie mit den Zahlen, die Sie in Lektion 4.1 notiert haben.
4. Prüfen Sie die Templates Ihres öffentlichen Siteaccess mit `php bin/php/eztemplatecheck.php -s<siteaccess>`.
5. Sehen Sie sich das Ende des Fehler-Logs an: `tail -20 var/log/error.log` (und `var/<VarDir>/log/error.log`, falls
   vorhanden).
6. Öffnen Sie eine Seite Ihrer Schulungswebsite mit einem Bild und prüfen Sie, dass das Bild angezeigt wird.
7. Beantworten Sie in Ihren Notizen anhand der Tabelle aus [Kapitel 14.13](../../../../installation/14-migration-von-4x.md#1413-häufige-probleme):
   Was sind Ursache und Lösung von „Illegal mix of collations“ unter MySQL 8, „Call to undefined function each()“,
   „Admin has no menus, YUI is not defined“, „Ein Template-Override gewinnt nicht mehr“ und „Benutzer können sich
   nicht anmelden“?
8. Schreiben Sie Ihren eigenen Überprüfungsbogen: die zehn Prüfungen aus 14.12 als Checkliste mit einer Spalte
   „Ergebnis“.

## Erwartetes Ergebnis

- Schritt 1 zeigt das Ergebnis der Datenbankkonsistenz; auf einer frischen Installation gibt es wenig oder kein SQL.
- Schritt 2 meldet den Zustand der Alias-Tabellen, ohne sie zu ändern.
- Schritt 3 liefert dieselben Zahlen wie in Lektion 4.1, sofern seitdem keine Inhalte hinzugekommen sind.
- Schritt 7: fünf Ursachen und Lösungen, jeweils mit dem Abschnitt aus Kapitel 14, der die Details enthält.
- Der Trainer prüft die Antworten aus Schritt 7 und einen Überprüfungsbogen.

## Quiz

1. `ezsqldiff.php` gibt für eine Kernel-Tabelle eine Zeile mit `CREATE TABLE` aus. Was bedeutet das, und was tun Sie?
2. Welche Zählungen müssen zwischen der alten und der migrierten Datenbank genau übereinstimmen?
   A. Objekte, Attribute und Knoten B. Nur URL-Aliase C. Sitzungen D. Cache-Dateien
3. Wie prüfen Sie, dass ein alter Passwort-Hash bei der Anmeldung aktualisiert wurde?
4. Welcher Befehl baut den Suchindex nach einer Migration neu auf?
5. Richtig oder falsch: `verify_aliases.php` ohne `--fix` ändert die Alias-Tabellen.

<details>
<summary>Antworten</summary>

1. Ein Schritt wurde ausgelassen; spielen Sie die Sicherung ein und führen Sie den ausgelassenen Schritt aus, führen
   Sie nicht das ausgegebene SQL aus
   ([Kapitel 14.12](../../../../installation/14-migration-von-4x.md#1412-die-inhalte-nach-der-migration-prüfen)).
2. **A.** `ezurlalias_ml` wächst, wenn Aliase neu aufgebaut werden (Kapitel 14.12).
3. Melden Sie sich als Redakteur mit einem alten Hash-Typ an und prüfen Sie, dass sich `password_hash_type` in 7
   geändert hat (Kapitel 14.12).
4. `php bin/php/updatesearchindex.php --clean`
   ([Kapitel 14.11](../../../../installation/14-migration-von-4x.md#1411-suche)).
5. **Falsch.** `--fix` repariert, was sich sicher reparieren lässt; ohne die Option meldet das Skript nur (Kapitel
   14.12).

</details>

## Weiterführende Informationen

- [Kapitel 14.12: Die Inhalte nach der Migration überprüfen](../../../../installation/14-migration-von-4x.md#1412-die-inhalte-nach-der-migration-prüfen):
  die zehn Prüfungen.
- [Kapitel 14.13: Häufige Probleme](../../../../installation/14-migration-von-4x.md#1413-häufige-probleme): Symptome,
  Ursachen und Lösungen.
- [Kapitel 12: Fehlerbehebung](../../../../installation/12-fehlerbehebung.md): weitere Symptome laufender Websites.

## Verwandte Seiten

- [Modul 4: Von der 4.x-Linie](README.md)
- [Migration zu Exponential: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
