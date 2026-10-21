---
title: "Lektion 3.3: Logs pro Website"
audience: trainer, admin, developer
category: training
order: 33
summary: Die Teilnehmenden schicken die Logs der zweiten Website mit UseGlobalLogDir und LogDir in ein eigenes Log-Verzeichnis, weisen es mit einer 404-Anfrage nach und planen LogVarDir für viele Websites. 45 Minuten.
keywords: multisite und seitenpakete, logs pro website, log-verzeichnis, audit-einträge, log-anzeige, multisite and site packages, logs per site, UseGlobalLogDir, LogDir, LogVarDir, VarDir, 404.log, error.log, eZLog, eZSys::logDirectory, audit records, log viewer
type: help
---
# Lektion 3.3: Logs pro Website

Wenn zehn Websites in ein `var/log/error.log` schreiben, dauert das Finden des Fehlers einer Website länger als seine
Behebung. Drei Einstellungen von `site.ini [FileSettings]` geben jeder Website ein eigenes Log-Verzeichnis, auf
derselben Festplatte oder auf einer anderen. Diese Lektion verlegt die Logs der zweiten Website, weist es mit einer
Anfrage nach, die eine Log-Zeile schreibt, und zeigt die Einstellung für einen ganzen Baum von Websites.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 3: Einstellungen, Logs und Caches pro Website](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler, die mehrere Websites betreiben |
| Voraussetzungen | [Lektion 3.1](eine-website-in-einer-eigenen-erweiterung.md): Die Siteaccess-Einstellungen von `site_b` liegen in `site_clubb` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `LogDir`, `UseGlobalLogDir` und `LogVarDir` und ihre Voreinstellungen erklären;
- einem Siteaccess ein eigenes Log-Verzeichnis geben und nachweisen, wohin seine Logs gehen;
- die Logs vieler Websites mit `LogVarDir` in einer Zeile planen;
- nennen, was bleibt, wo es ist: `storage.log`, die Audit-Einträge und Meldungen, die geschrieben werden, bevor der
  Siteaccess bekannt ist.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md).

1. **Die Einstellungen** (6 Minuten), alle in `site.ini [FileSettings]`, für jeden Siteaccess der Website:

   | Schlüssel | Voreinstellung | Bedeutung |
   |---|---|---|
   | `LogDir` | `log` | das Log-Verzeichnis innerhalb von `VarDir`; ein absoluter Pfad wird unverändert verwendet |
   | `UseGlobalLogDir` | `enabled` | `disabled` schreibt die Debug-Logs und die Standard-Logs von `eZLog` in das Log-Verzeichnis der Website statt nach `var/log` |
   | `LogVarDir` | leer | ein Baum für die Logs aller Websites: Das erste Verzeichnis von `VarDir` wird ersetzt, aus `var/example` wird `var_log/example`, sodass das Log-Verzeichnis `var_log/example/log` ist |

   Jede Einstellung behält das bisherige Verhalten bei, wenn sie nicht gesetzt ist.
2. **Eine Zeile für viele Websites** (3 Minuten). Mit einem `VarDir` pro Website geben `UseGlobalLogDir=disabled` und
   `LogVarDir=var_log` im globalen Override jeder Website ihre Logs in `var_log/<site>/log`.
3. **Was bleibt** (3 Minuten). Meldungen, die geschrieben werden, bevor die Anfrage einem Siteaccess zugeordnet ist,
   gehen weiterhin nach `var/log`. Audit-Einträge bleiben innerhalb von `VarDir` (`audit.ini [AuditSettings] LogDir`),
   weil sie zum Speicher der Website gehören, jahrelang aufbewahrt und gegen ihre Hash-Kette geprüft werden. Das
   Setup-Log bleibt in `var/log`.
4. **Velocity und Skripte** (3 Minuten). Die Pfade werden jedes Mal neu abgeleitet, wenn sich der Siteaccess ändert,
   sodass ein Velocity-Worker, der zwei Websites nacheinander bedient, die Fehler jeder Website in ihr eigenes
   Verzeichnis schreibt, und `php bin/php/ezcache.php -s <siteaccess>` und die Cronjobs einer Website in die
   Verzeichnisse dieser Website schreiben.

### Häufige Fragen

**Wohin gehen 404-Antworten?** In `404.log` im Log-Verzeichnis (`var/log` oder das Verzeichnis der Website bei
`UseGlobalLogDir=disabled`), wenn `site.ini [RequestLogSettings] NotFoundLog` auf `enabled` steht, der
Voreinstellung.

**Meine Erweiterung schreibt eine eigene Logdatei. Zieht sie mit um?** Wenn sie über `eZLog` protokolliert oder den
Pfad mit `eZSys::logDirectory()` bildet, ja. Eine, die den Pfad selbst aus `VarDir` und `LogDir` bildet, muss so
geändert werden, dass sie `eZSys::logDirectory()` verwendet.

**Zeigen die Systeminformationen das Log-Verzeichnis der Website?** **Setup** > **Systeminformationen**, Karte
**Speicher**, nennt das Log-Verzeichnis des Siteaccess, in dem sie läuft (der Administration), nicht die der anderen
Websites.

**Können Logs auf eine andere Festplatte gehen?** Ja: ein absolutes `LogDir` oder ein `LogVarDir`, das ein absoluter
Pfad ist.

### Stolperfallen

- **Dasselbe `VarDir` für beide Websites.** In der Übungsumgebung teilen beide Websites ein `VarDir`, sodass
  `LogDir=log` für beide dasselbe Verzeichnis wäre. Die Übung gibt `site_b` ein `LogDir` mit anderem Namen.
- **Berechtigungen.** Der Webserver (und Velocity) muss das neue Verzeichnis anlegen und beschreiben können.
- **Zu früh nachsehen.** Eine Logdatei erscheint erst, wenn etwas geschrieben wird. Verwenden Sie die 404-Anfrage der
  Übung.

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Lektion 3.2. Ersetzen Sie `http://127.0.0.1:8087` durch die Adresse
Ihrer Übungsumgebung.

1. Lesen Sie die wirksamen Pfade für `site_b`:

   ```bash
   php bin/php/console exp:ini where site.ini/FileSettings/VarDir site_b
   php bin/php/console exp:ini get site.ini/FileSettings/LogDir -s site_b
   php bin/php/console exp:ini get site.ini/RequestLogSettings/NotFoundLog -s site_b
   ```

2. Geben Sie `site_b` ein eigenes Log-Verzeichnis, in den Siteaccess-Einstellungen der Erweiterung:

   ```bash
   php bin/php/console exp:ini set site.ini/FileSettings/UseGlobalLogDir disabled extension:site_clubb:siteaccess:site_b
   php bin/php/console exp:ini set site.ini/FileSettings/LogDir log_site_b extension:site_clubb:siteaccess:site_b
   ```

3. Starten Sie Velocity neu, wenn er die Übungsumgebung ausliefert.
4. Fragen Sie `site_b` nach einer Seite, die nicht existiert, dann die Hauptwebsite:

   ```bash
   curl -sI http://127.0.0.1:8087/site_b/no-such-page | head -1
   curl -sI http://127.0.0.1:8087/site/no-such-page | head -1
   ```

5. Suchen Sie das neue Verzeichnis unterhalb des `VarDir` aus Schritt 1 (zum Beispiel `ls var/site/log_site_b/`) und
   lesen Sie die letzte Zeile seiner `404.log`.
6. Lesen Sie die letzte Zeile von `var/log/404.log` (oder aus dem Log-Verzeichnis der Hauptwebsite) und vergleichen
   Sie die Felder `siteaccess=`.
7. Fragen Sie auch `site_b_ger` nach einer fehlenden Seite. Wohin geht seine Zeile, und warum?
8. Schreiben Sie die einzeilige Einrichtung für einen Server mit fünfzig Websites auf, jede mit `VarDir=var/<site>`.

## Erwartetes Ergebnis

- Schritt 4 antwortet zweimal mit `404`.
- Schritt 5: `log_site_b/404.log` existiert mit einer Zeile für `/site_b/no-such-page` und `siteaccess=site_b`.
- Schritt 6: Die Zeile der Hauptwebsite steht im globalen Log-Verzeichnis, mit `siteaccess=site`.
- Schritt 7: Die Zeile von `site_b_ger` geht in das globale Verzeichnis, weil die Einstellung nur für `site_b`
  gesetzt wurde (sie müsste in das `override/` der Erweiterung aus Lektion 3.2, um beide abzudecken).
- Schritt 8: `UseGlobalLogDir=disabled` und `LogVarDir=var_log` in `[FileSettings]` des globalen Overrides.
- Der Trainer prüft Schritt 5 in einer Übungsumgebung am Beamer.

## Quiz

1. Was ändert `UseGlobalLogDir=disabled`?
2. `VarDir=var/shop` und `LogVarDir=var_log`. Wo liegt das Log-Verzeichnis?
   A. `var/shop/log` B. `var_log/shop/log` C. `var_log/log` D. `var/log`
3. Welche Einträge ziehen mit diesen Einstellungen nie um, und warum?
4. Eine Erweiterung schreibt ihr Log mit einem Pfad, der aus `VarDir` gebildet wird. Was sollte sie stattdessen
   verwenden?
5. Welche Datei erhält die 404-Antworten einer Website mit eigenem Log-Verzeichnis?

<details>
<summary>Antworten</summary>

1. Die Debug-Logs und die Standard-Logs von `eZLog` gehen in das Log-Verzeichnis der Website statt nach `var/log`.
2. **B.** Das erste Verzeichnis von `VarDir` wird durch den Baum ersetzt, dann wird `LogDir` (`log`) angehängt.
3. Die Audit-Einträge: Sie gehören zum Speicher der Website, werden jahrelang aufbewahrt und gegen ihre Hash-Kette
   geprüft ([Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md#was-die-logs-liest)).
4. `eZSys::logDirectory()` oder `eZLog::write()` ohne Verzeichnis.
5. `404.log` im Log-Verzeichnis dieser Website, wenn `[RequestLogSettings] NotFoundLog` auf `enabled` steht
   ([Anfrage-Logs](../../../../funktionen/6.0/anfrageprotokolle.md)).

</details>

## Weiterführende Informationen

- [Logs, INI-Cache und Ablaufzeitstempel pro Website](../../../../funktionen/6.0/log-und-cache-pfade-fuer-mehrere-websites.md).
- [Anfrage-Logs](../../../../funktionen/6.0/anfrageprotokolle.md): `404.log` und `security.log`.
- [Die Log-Anzeige](../../../../funktionen/6.0/log-betrachter.md): die Logs unter **Setup** > **Logdateien** lesen.
- [Log-Anzeige und Rotation](../../../hosting/betrieb/log-viewer-und-log-rotation.md): die Sicht des Hosters.

## Verwandte Seiten

- [Modul 3: Einstellungen, Logs und Caches pro Website](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
