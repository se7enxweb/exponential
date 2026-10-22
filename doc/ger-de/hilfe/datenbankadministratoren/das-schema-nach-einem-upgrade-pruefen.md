---
title: Das Schema nach einem Upgrade prüfen
audience: dba
category: database-administration
order: 32
summary: Wie Sie bestätigen, dass eine Exponential-Datenbank das Schema ihres Releases hat - die Prüfung der Datenbankkonsistenz unter Setup > Upgrade-Prüfung und die Arten von Befunden, die sie meldet, die Prüfung der MongoDB-Collections, ezsqldiff.php gegen share/db_schema.dba, exp:checkdbfiles und der Oracle-Befehl schema-diff - und wie Sie auf jeden Befund sicher reagieren.
keywords: Upgrade-Prüfung, Datenbankkonsistenz, Schemaprüfung, fehlende Tabelle, geänderte Tabelle, Tabelle nicht im Schema, Engine-Hinweise, ezsqldiff, share/db_schema.dba, exp:checkdbfiles, schema-diff, MongoDB-Collections, Entfernt etwas, system.upgrade.run, upgrade check, database consistency, schema check, missing table, changed table
type: help
---
# Das Schema nach einem Upgrade prüfen

Diese Seite richtet sich an Datenbankadministratoren, die eine Exponential-Datenbank nach einem Upgrade, einer
Wiederherstellung oder einem Engine-Wechsel prüfen. Sie beantwortet: welche Werkzeuge die Datenbank mit dem Schema des Releases
vergleichen, was jede Art von Befund bedeutet und auf welche Befunde Sie reagieren. Die Werkzeuge lesen nur: Keines davon
ändert die Datenbank. Quelle: [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md#6-die-prüfung-der-datenbankkonsistenz-ausführen) und
[11.7 Das Upgrade prüfen](../../installation/11-aktualisieren.md#117-das-upgrade-überprüfen).

## Inhalt

1. [Die Werkzeuge](#die-werkzeuge)
2. [Die Prüfung der Datenbankkonsistenz](#die-prüfung-der-datenbankkonsistenz)
3. [Jede Art von Befund](#jede-art-von-befund)
4. [Auf MongoDB](#auf-mongodb)
5. [Aus der Shell: ezsqldiff.php](#aus-der-shell-ezsqldiffphp)
6. [Auf Oracle: schema-diff](#auf-oracle-schema-diff)
7. [Checkliste](#checkliste)

## Die Werkzeuge

| Werkzeug | Vergleicht | Engines |
|---|---|---|
| **Setup > Upgrade-Prüfung**, "Überprüfe Datenbankkonsistenz" | die Datenbank mit `share/db_schema.dba` und den Schemas aktiver Erweiterungen | MySQL, MariaDB, PostgreSQL, SQLite, MongoDB (Collections) und jede Engine, deren Schema-Handler in `dbschema.ini` registriert ist |
| `bin/php/ezsqldiff.php` | zwei Schemas, Dateien oder Datenbanken | `mysql`, `postgresql` |
| `exp:checkdbfiles` | die Update-Dateien auf der Festplatte mit dem Upgrade-Weg | alle |
| `ext:ezoracle:schema-diff` | das Oracle-Schema mit `share/db_schema.dba` | Oracle |

## Die Prüfung der Datenbankkonsistenz

Drücken Sie in der Administration unter **Setup > Upgrade-Prüfung** auf **Überprüfe Datenbankkonsistenz**. Sie liest
`share/db_schema.dba` und die `share/db_schema.dba` jeder aktiven Erweiterung, die eine hat, liest das Schema der Datenbank und
vergleicht beide mit dem Schema-Handler der Engine (`dbschema.ini [SchemaSettings]`). Sie liest außerdem die Schemadateien
installierter, aber inaktiver Erweiterungen, nur um zu sagen, zu welcher von ihnen eine Tabelle gehört. Auf dem
Demonstrationsserver (SQLite, 11 Schemadateien) dauert das etwa 0,1 Sekunden.

Die Seite zeigt Zahlen (Tabellen mit zu prüfendem SQL, fehlende Tabellen, Tabellen nicht im Schema, geänderte Tabellen, Tabellen
inaktiver Erweiterungen, Engine-Hinweise), dann eine aufklappbare Karte je Tabelle mit den Unterschieden und dem SQL für diese
Tabelle. **Alle Anweisungen** enthält das ganze SQL als einen Block. Die Seite führt nichts davon aus. Jeder Lauf wird als
`system.upgrade.run` in den Audit-Trail geschrieben.

## Jede Art von Befund

| Art | Bedeutung | Was zu tun ist |
|---|---|---|
| **Fehlende Tabelle** | mit Exponential oder einer aktiven Erweiterung ausgeliefert, nicht in der Datenbank; die Funktion, die sie verwendet, scheitert | ihre `CREATE`-Anweisung nach einer Sicherung ausführen, oder die Update-Datei oder das Skript, das sie hätte anlegen sollen |
| **Tabelle nicht im Schema** | in gar keinem Schema: meist eine Tabelle Ihrer eigenen Erweiterung ohne Schemadatei oder einer entfernten Erweiterung | lassen Sie sie, sofern Sie nicht wissen, dass ihre Daten nicht mehr gebraucht werden; das `DROP` wird nur der Vollständigkeit halber gezeigt, markiert mit **Entfernt etwas** |
| **Tabelle einer nicht aktiven Extension** | vom Schema einer installierten, inaktiven Erweiterung genannt (zum Beispiel `ezfind_elevate_configuration` von ezfind) | die Erweiterung aktivieren oder die Tabelle lassen; dafür wird kein SQL geschrieben |
| **Geänderte Tabelle** | Felder oder Indizes weichen von der mitgelieferten Definition ab | lesen Sie jede Anweisung; führen Sie die aus, denen Sie zustimmen, nach einer Sicherung; mit **Entfernt etwas** markierte Anweisungen können Ihre Daten entfernen |
| **Engine-Hinweise** | der Schema-Handler schreibt für den Unterschied kein SQL (auf SQLite sind zum Beispiel `text` und `longtext` dasselbe) | nichts |

Die Regel: Fehlende Tabellen sind meist echte Probleme; zusätzliche und geänderte Tabellen sind meist Ihre eigene Arbeit.
Führen Sie **Alle Anweisungen** nie als Ganzes aus.

## Auf MongoDB

Die Prüfung vergleicht Collections: fehlende Collections nach Funktion gruppiert, Collections, die nicht im Schema stehen, und
den `mongosh`-Befehl, der die fehlenden anlegt. Führen Sie danach `bin/mongodb/create_indexes.js` aus
([MongoDB: Grenzen und Betrieb](mongodb-grenzen-und-betrieb.md#upgrades-ohne-sql-dateien)).

## Aus der Shell: ezsqldiff.php

```bash
php bin/php/ezsqldiff.php --type=mysql --user=USER --password='<password>' share/db_schema.dba DATABASE --allow-root-user
echo $?
```

Das Referenzschema kommt **zuerst**. Die Ausgabe ist das SQL, das Ihre Datenbank in die Referenz verwandeln würde: Eine Zeile
`CREATE TABLE` oder `ADD` nennt etwas, das ein Update-Schritt hätte anlegen sollen; eine Zeile `DROP TABLE` eine Tabelle, die nur
Ihre Datenbank hat (die Tabellen Ihrer eigenen Erweiterungen erscheinen so). Lesen Sie sie, führen Sie sie nicht aus. Exit-Code
0, wenn die Schemas übereinstimmen, 1, wenn sie sich unterscheiden; `--check-only` gibt nichts aus und setzt nur den Exit-Code,
nützlich in einem Deploy-Skript ([Schemawerkzeuge](schemawerkzeuge-dump-insert-diff.md#ezsqldiffphp)).

Verwandte Prüfungen in der Shell:

```bash
php bin/php/console exp:checkdbfiles --no-verify-branches     # update files complete: no output
php bin/php/console exp:system:info --allow-root-user         # exits 1 when a health check fails
```

## Auf Oracle: schema-diff

```bash
php bin/php/console ext:ezoracle:schema-diff --allow-root-user
```

Es vergleicht die Datenbank mit `share/db_schema.dba` (`--file=<dba>` für eine andere Datei) und gibt das Upgrade-SQL aus;
danach kompiliert `ext:ezoracle:recompile` ungültige Objekte neu
([Wartungsbefehle und Cronjobs für Oracle](oracle-wartungsbefehle.md)).

## Checkliste

- [ ] Die Versionszeile passt zum Code.
- [ ] Die Konsistenzprüfung listet keine fehlende Tabelle.
- [ ] Geänderte und zusätzliche Tabellen sind erklärt (eigene Erweiterungen, entfernte Erweiterungen).
- [ ] `exp:checkdbfiles` gibt nichts aus.
- [ ] Keine Anweisung wurde ohne Sicherung und Grund ausgeführt.

## Verwandte Seiten

- [Upgrades und die SQL-Update-Dateien](upgrades-und-sql-update-dateien.md)
- [Das .dba-Schemaformat](das-dba-schemaformat.md)
- [Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
