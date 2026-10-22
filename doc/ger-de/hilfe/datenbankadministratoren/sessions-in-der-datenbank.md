---
title: Sessions in der Datenbank
audience: dba
category: database-administration
order: 35
summary: Wann Exponential Sessions in der Tabelle ezsession hält und wann in PHPs eigenem Speicher, was die Tabelle enthält, wie Sie umstellen, wie abgelaufene Zeilen entfernt werden (ezsessiongc.php und das Cronjob-Skript session_gc.php, das kein mitgelieferter Teil ausführt), was ForceStart kostet und warum mehrere Webserver Datenbank-Sessions oder festes Routing brauchen.
keywords: Sessions, Sitzungen, ezsession, ezpSessionHandlerDB, ezpSessionHandlerPHP, Session-Handler, SessionTimeout, ForceStart, ezsessiongc, session_gc.php, Cronjob, Garbage Collection, ezuservisit, mehrere Webserver, Sticky Sessions, Abfrage-Cache ExcludeTables, sessions, garbage collection, several web servers, sticky sessions, query cache ExcludeTables
type: help
---
# Sessions in der Datenbank

Diese Seite richtet sich an Datenbankadministratoren, die entscheiden, ob eine Exponential-Website ihre Sessions in der
Datenbank hält, und die die Tabelle `ezsession` betreuen, wenn sie das tut. Sie beantwortet: welcher Handler Sessions wo hält,
was die Tabelle enthält, wie abgelaufene Zeilen entfernt werden und was sich mit mehreren Webservern ändert. Quelle:
[Sessions](../../anleitungen/sitzungen.md) und `settings/site.ini [Session]`.

## Inhalt

1. [Zwei Handler](#zwei-handler)
2. [Die Tabelle ezsession](#die-tabelle-ezsession)
3. [Auf den Datenbank-Handler umstellen](#auf-den-datenbank-handler-umstellen)
4. [Abgelaufene Sessions entfernen](#abgelaufene-sessions-entfernen)
5. [Anonyme Sessions und ForceStart](#anonyme-sessions-und-forcestart)
6. [Mehrere Webserver](#mehrere-webserver)
7. [Checkliste](#checkliste)

## Zwei Handler

`site.ini [Session] Handler` entscheidet:

| Wert | Wo Sessions gehalten werden | Administration |
|---|---|---|
| leer (der Standard): `ezpSessionHandlerPHP` | PHPs eigener Session-Speicher (`session.save_handler`, meist Dateien auf dem Webserver) | **Setup > Sitzungen** zeigt jüngste Anmeldungen aus `ezuservisit`, kann aber keine Sessions auflisten oder entfernen |
| `ezpSessionHandlerDB` | die Tabelle `ezsession` der Datenbank der Website | **Setup > Sitzungen** listet, zählt und entfernt Sessions |

Eine Session endet `[Session] SessionTimeout` Sekunden nach ihrer letzten Verwendung (Standard 259200, drei Tage). Mit dem
PHP-Handler entfernt PHP abgelaufene Sessions selbst (`session.gc_maxlifetime`, das Exponential auf `SessionTimeout` setzt).
`ezuservisit` (letzter Besuch, Zahl der Anmeldungen, fehlgeschlagene Versuche) wird mit beiden Handlern geführt.

## Die Tabelle ezsession

Aus `kernel/sql/mysql/kernel_schema.sql`:

| Spalte | Bedeutung |
|---|---|
| `session_key` | der Primärschlüssel (32 Zeichen) |
| `expiration_time` | wann die Session endet; indiziert |
| `user_id` | der angemeldete Benutzer oder der anonyme Benutzer; indiziert |
| `user_hash` | ein an den Benutzer gebundener Hash |
| `data` | die serialisierten Session-Daten |

Session-Schlüssel sind Zugangsdaten: Wer einen hat, ist als dieser Benutzer angemeldet. Behandeln Sie die Tabelle wie einen
Passwortspeicher; die Administration zeigt die Schlüssel nie, nur einen kurzen Verweis. Der SQL-Abfrage-Cache speichert
Lesezugriffe auf `ezsession` nie (`querycache.ini ExcludeTables[]=ezsession`).

## Auf den Datenbank-Handler umstellen

```ini
# settings/override/site.ini.append.php
[Session]
Handler=ezpSessionHandlerDB
```

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
./console exp:velocity deploy --allow-root-user
```

Alle melden sich einmal neu an: Von PHP gehaltene Sessions werden nicht übernommen. Der Änderungshinweis
[ezpSessionHandlerDB unter PHP 8](../../bc/6.0/ezpsessionhandlerdb-php8-korrektur-und-tests.md) behandelt eine Korrektur an diesem
Handler.

## Abgelaufene Sessions entfernen

Mit dem Datenbank-Handler bleiben abgelaufene Zeilen in `ezsession`, bis etwas sie entfernt:

- **Setup > Sitzungen, Abgelaufene / alte Sitzungen entfernen**: entfernt die abgelaufenen Zeilen und die Warenkörbe, die sie
  hinterlassen; niemand wird abgemeldet.
- **`php bin/php/ezsessiongc.php`** (`exp:ezsessiongc`): dasselbe aus einer Shell, "as defined by
  site.ini[Session]SessionTimeout".
- **`cronjobs/session_gc.php`**: das Cronjob-Skript. Kein Teil der mitgelieferten `settings/cronjob.ini` führt es aus; fügen Sie
  es in Ihrem Override einem Teil hinzu:

  ```ini
  # settings/override/cronjob.ini.append.php
  [CronjobPart-infrequent]
  Scripts[]=session_gc.php
  ```

Achten Sie auf die Zahl "abgelaufen, aber noch in der Tabelle" unter **Setup > Sitzungen**: Sie ist rot, wenn es welche gibt.

## Anonyme Sessions und ForceStart

`[Session] ForceStart=enabled` öffnet für jeden anonymen Besucher eine Session. Mit dem Datenbank-Handler schreibt das für
jeden Besuch eine Zeile; setzen Sie es nur, wenn anonyme Besucher gezählt werden müssen, und bemessen Sie die Tabelle und ihr
Aufräumen dafür.

## Mehrere Webserver

Mit dem PHP-Handler kennt jeder Webserver nur seine eigenen Sessions. Auf mehreren Servern halten Sie Sessions entweder in der
Datenbank (jeder Server liest dieselbe Tabelle) oder leiten jeden Benutzer anhand des Session-Cookies an einen Server
([Mehrere Webknoten, Sessions](../hosting/betrieb/mehrere-web-knoten.md#sitzungen)). SQLite kann nicht die Datenbank einer
Website mit mehreren Servern sein, Datenbank-Sessions bedeuten dort also eine Server-Engine.

## Checkliste

- [ ] Der Handler ist bewusst gewählt: PHP-Dateien auf einem Server, die Datenbank für mehrere.
- [ ] Mit dem Datenbank-Handler läuft `session_gc.php` aus einem Teil oder `ezsessiongc.php` aus Cron.
- [ ] `ForceStart` bleibt ausgeschaltet, sofern keine anonymen Sessions gebraucht werden.
- [ ] Sicherungen von `ezsession` werden als Geheimnisse behandelt, oder die Tabelle wird aus Kopien für andere herausgelassen.

## Verwandte Seiten

- [Sessions: wer angemeldet ist](../../anleitungen/sitzungen.md)
- [Aufräumen: Cronjobs und Warteschlangentabellen](aufraeumen-cronjobs-und-warteschlangen.md)
- [13.7 Sessions und Cookies](../../installation/13-sicherheitshaertung.md#137-sitzungen-und-cookies)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
