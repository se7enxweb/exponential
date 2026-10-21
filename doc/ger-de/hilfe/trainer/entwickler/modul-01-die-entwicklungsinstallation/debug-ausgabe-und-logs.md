---
title: "Lektion 1.3: Debug-Ausgabe und Logs"
audience: trainer, developer
category: training
order: 13
summary: Die Teilnehmenden schalten für ihre Entwicklungsinstallation die Debug-Ausgabe, die verwendeten Templates und die Template-Pfadkommentare ein, lesen die Exp Debug bar, verfolgen das Fehlerlog und schreiben eigene Debug-Zeilen. 45 Minuten.
keywords: entwickler, debug-ausgabe, DebugOutput, ShowUsedTemplates, ShowTemplatePathComments, Exp Debug bar, error.log, eZDebug, eZLog, exp:logs, DebugByIP, fehlerlog, logdatei, developers course, debug output, log file
type: help
---
# Lektion 1.3: Debug-Ausgabe und Logs

Eine Live-Website verbirgt ihre Fehler. Eine Entwicklungsinstallation muss sie zeigen. In dieser Lektion schalten die
Teilnehmenden die Debug-Ausgabe mit `exp:ini` ein, lesen den Bericht, den die Exp Debug bar unter jeder Seite zeigt,
verfolgen die Logdateien, während sie neu laden, und schreiben eigene Debug-Zeilen aus PHP und aus einem Template.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 1: Die Entwicklungsinstallation](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Einsteiger |
| Zielgruppe | PHP-Entwickler, die neu bei Exponential sind |
| Voraussetzungen | [Lektion 1.2](eine-entwicklungsinstallation-mit-git.md): eine laufende Entwicklungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Debug-Ausgabe, die verwendeten Templates und die Template-Pfadkommentare mit `exp:ini` ein- und ausschalten;
- die Teile Fehler, Templates, Zeitmessung und SQL des Debug-Berichts lesen;
- `var/log/error.log` mit `tail` oder `exp:logs` verfolgen, während sie ein Problem nachstellen;
- Debug-Zeilen mit `eZDebug` und eine eigene Logdatei mit `eZLog::write()` schreiben;
- die Debug-Ausgabe auf einem gemeinsam genutzten Rechner auf die eigene Adresse oder den eigenen Benutzer beschränken.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: [Debug-Ausgabe und Logs für Entwickler](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md),
[Die Exp Debug bar](../../../../../eng-us/features/6.0/exp-debug-bar.md) und [Der Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md).

1. **Die Einstellungen** (4 Minuten). Alle in `site.ini`; zeigen Sie die Standardwerte:

   | Block | Einstellung | Standard | Bedeutung |
   |---|---|---|---|
   | `[DebugSettings]` | `DebugOutput` | `disabled` | Den Debug-Bericht überhaupt anzeigen |
   | `[DebugSettings]` | `DebugByIP` + `DebugIPList[]` | `disabled` | Nur für diese Adressen |
   | `[DebugSettings]` | `DebugByUser` + `DebugUserIDList[]` | `disabled` | Nur für diese Benutzer-IDs |
   | `[TemplateSettings]` | `ShowUsedTemplates` | `disabled` | Jedes Template auflisten, das die Seite verwendet hat |
   | `[TemplateSettings]` | `ShowTemplatePathComments` | `disabled` | HTML-Kommentare, die das Template jedes Teils nennen |
   | `[TemplateSettings]` | `TemplateCompile` | `enabled` | Cache der kompilierten Templates |
   | `[DatabaseSettings]` | `SQLOutput` | `disabled` | Jede SQL-Abfrage mit ihrer Dauer |

2. **Einschalten** (3 Minuten). Zeigen Sie die Befehle `exp:ini set` aus der Übung und die Option `--dry-run`.
   `exp:ini` schreibt `settings/override/site.ini.append.php` und legt eine Sicherung an.
3. **Der Bericht** (4 Minuten). Laden Sie eine Seite neu: zuerst die Fehler, dann die Template-Tabelle (angefordertes
   Template, verwendetes Template, Override), Zeitmesspunkte, SQL. Eine Abfrage, die hundertmal läuft, ist eine
   Schleife, die einmal holen sollte.
4. **Logs** (2 Minuten). `var/log/error.log` erhält immer die Fehler (`[DebugSettings] AlwaysLog[]=error`).
   `deprecated.log` ist die Aufgabenliste für die nächste PHP-Version. **Setup** > **Logdateien** zeigt dieselben
   Dateien im Browser.
5. **Eigene Zeilen** (2 Minuten). `eZDebug::writeDebug()`, `writeNotice()`, `writeWarning()`, `writeError()`, mit
   `__METHOD__` als Kennzeichnung; `{debug-log var=$node msg='...'}` in einem Template.

### Häufige Fragen

**Können Besucher die Debug-Ausgabe sehen?** Ja, alle, die die Seite öffnen können, solange `DebugByIP` oder
`DebugByUser` sie nicht einschränkt. Die Debug-Ausgabe verrät Pfade, SQL und Einstellungen: niemals auf einer
Live-Website.

**Warum erscheint meine Template-Änderung nicht?** Mit `TemplateCompile=enabled` wird die kompilierte Fassung
verwendet, bis der Template-Cache geleert wird. Entwickler setzen beim Bearbeiten oft `TemplateCompile=disabled`.

**Wo ist das Log eines Kommandozeilenskripts?** In denselben Dateien unter `var/log/`; `[DebugSettings]
ScriptDebugOutput` fügt Skripten die Debug-Ausgabe hinzu.

### Stolperfallen

- **Vergessen, den INI-Cache zu leeren.** Führen Sie nach einer Änderung der Einstellungen
  `php bin/php/ezcache.php --clear-tag=ini` aus; sonst werden die alten Werte verwendet.
- **Im Code vergessene Debug-Zeilen.** Bitten Sie die Teilnehmenden, jedes `writeDebug()` zu entfernen, bevor sie
  committen.
- **Personenbezogene Daten in Logs.** Schreiben Sie niemals Passwörter, Schlüssel oder personenbezogene Daten in eine
  Logdatei.

## Praktische Übung

Auf der Entwicklungsinstallation aus Lektion 1.2, aus ihrem Wurzelverzeichnis.

1. Sehen Sie nach, woher die Einstellung kommt: `php bin/php/console exp:ini where site.ini/DebugSettings/DebugOutput`.
2. Sehen Sie sich die Änderung vorab an: `php bin/php/console exp:ini set site.ini/DebugSettings/DebugOutput enabled global --dry-run`.
3. Schalten Sie die drei Einstellungen ein:

   ```bash
   php bin/php/console exp:ini set site.ini/DebugSettings/DebugOutput enabled global
   php bin/php/console exp:ini set site.ini/TemplateSettings/ShowUsedTemplates enabled global
   php bin/php/console exp:ini set site.ini/TemplateSettings/ShowTemplatePathComments enabled global
   php bin/php/ezcache.php --clear-tag=ini
   php bin/php/ezcache.php --clear-tag=template
   ```

4. Laden Sie die Startseite der Website neu. Öffnen Sie die Debug-Leiste und suchen Sie die Liste der verwendeten
   Templates.
5. Sehen Sie sich den Quelltext der Seite an und suchen Sie nach `START`. Schreiben Sie das Template der Fußzeile auf.
6. Verfolgen Sie in einem zweiten Terminal das Fehlerlog: `tail -f var/log/error.log`.
7. Öffnen Sie eine Adresse, die es nicht gibt, zum Beispiel `/site/content/view/full/999999`, und beobachten Sie
   sowohl die Seite als auch das Terminal.
8. Listen Sie die Logdateien auf, die der Viewer öffnen darf: `php bin/php/console exp:logs --list`.
9. Zeigen Sie die letzten fünf Einträge des Fehlerlogs über den Viewer an:
   `php bin/php/console exp:logs --tail=var/log/error.log --lines=5`.
10. Schalten Sie `ShowTemplatePathComments` mit demselben Befehl und `disabled` wieder aus und leeren Sie den
    Template-Cache.

## Erwartetes Ergebnis

- Schritt 1 listet `settings/site.ini` mit `DebugOutput=disabled` und nach Schritt 3 einen wirksamen Override mit
  `enabled`.
- Schritt 4: Die Debug-Leiste erscheint unten auf jeder Seite, ihre Template-Liste nennt `pagelayout.tpl` und die
  Templates der Knotenansicht.
- Schritt 5: Kommentare wie `<!-- START design/.../footer.tpl -->` im Quelltext.
- Schritt 7: die Seite „nicht gefunden“; je nach Einstellungen erhält `404.log` oder `error.log` eine Zeile.
- Der Trainer prüft die Ausgabe von `exp:ini where` bei zwei Teilnehmenden.

## Quiz

1. Welche Einstellung zeigt den Debug-Bericht unter jeder Seite?
   A. `[TemplateSettings] Debug` B. `[DebugSettings] DebugOutput` C. `[DatabaseSettings] SQLOutput` D. `[DebugSettings] AlwaysLog[]`
2. Welche zwei Einstellungen beschränken die Debug-Ausgabe auf bestimmte Personen?
3. Was sollte auf jede Änderung der Einstellungen mit `exp:ini set` folgen?
4. Welche Methode von `eZDebug` schreibt auch ohne Debug-Ausgabe in `error.log`?
5. Richtig oder falsch: Template-Pfadkommentare können für einen Feed oder einen Live-Siteaccess bedenkenlos
   eingeschaltet bleiben.

<details>
<summary>Antworten</summary>

1. **B.** `DebugOutput=enabled` zeigt den Bericht, die Exp Debug bar.
2. `DebugByIP` mit `DebugIPList[]` und `DebugByUser` mit `DebugUserIDList[]`.
3. Das Leeren des INI-Caches, `php bin/php/ezcache.php --clear-tag=ini`.
4. `eZDebug::writeError()`: Fehler werden immer geloggt (`AlwaysLog[]=error`).
5. **Falsch.** Sie fügen jedem Template Markup hinzu; nur für die Entwicklung
   ([Template-Pfadkommentare](../../../../../eng-us/features/6.0/template-path-comments.md)).

</details>

## Weiterführende Literatur

- [Debug-Ausgabe und Logs für Entwickler](../../../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md):
  jede Einstellung und jede Logdatei.
- [Die Exp Debug bar](../../../../../eng-us/features/6.0/exp-debug-bar.md): ihre Tabs und ihr Einstellungs-Tab.
- [Der Log-Viewer](../../../../../eng-us/features/6.0/log-viewer.md) und
  [Einen Fehler über seine Referenz finden](../../../../anleitungen/fehler-anhand-der-referenz-finden.md).

## Verwandte Seiten

- [Modul 1: Die Entwicklungsinstallation](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
