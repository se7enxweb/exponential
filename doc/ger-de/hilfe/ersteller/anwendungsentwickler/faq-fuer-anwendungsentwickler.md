---
title: FAQ für Anwendungsentwickler
audience: app-creator, developer
category: app-creation
order: 650
summary: Kurze Antworten auf die häufigsten Fragen von Anwendungsentwicklern - Klassen, die nicht gefunden werden, Änderungen, die unter Velocity nicht ankommen, Rechte, Einstellungen, abgelehnte Formulare, JSON, Cron, Warteschlangen, Tests und wo Sie fragen können.
keywords: FAQ, häufige Fragen, Fehlerbehebung, Klasse nicht gefunden, Velocity neu starten, Zugriff verweigert, Formular-Token, Einstellungen, Warteschlange, Tests, Anwendungsentwickler, questions, troubleshooting, class not found, Velocity restart, access denied, form token, 403, settings not used, cron, queue, tests, app creator
type: help
---
# FAQ für Anwendungsentwickler

Kurze Antworten, jeweils mit der Seite, die das Thema erklärt.

## Meine neue Klasse wird nicht gefunden

Erzeugen Sie die Autoloads neu (`php bin/php/ezpgenerateautoloads.php -e`) und starten Sie die Worker von Velocity neu
([Autoloads und Klassenbenennung](autoloads-und-klassenbenennung.md)).

## Meine Änderung funktioniert unter Apache, aber nicht unter Velocity

Worker halten Klassen und Einstellungen im Speicher: `./console exp:velocity deploy` ([Mit Velocity ausliefern](mit-velocity-ausliefern.md)).

## Meine Ansicht meldet "Zugriff verweigert"

Dem Benutzer fehlt eine Funktion, die in den `functions` der Ansicht steht; fügen Sie die Richtlinie einer Rolle hinzu ([Richtlinienfunktionen und Einschränkungen](richtlinienfunktionen-und-einschraenkungen.md)).

## Mein Formular-POST erhält eine 403

Das Formular-Token fehlt: Senden Sie aus einem vom Template erzeugten Formular mit `method="post"`, oder schicken Sie
das Token aus Ihrem Skript mit ([Formulare, CSRF und HTTP-Eingaben](formulare-csrf-und-http-eingaben.md)).

## Meine Einstellung wird ignoriert

Eine andere Datei gewinnt: `./console exp:ini where <file>/<block>/<key> <siteaccess>`, danach mit `--clear-tag=ini`
leeren ([Einstellungen und INI-Dateien](einstellungen-und-ini-dateien.md)).

## Mein neues Template wird nicht verwendet

Leeren Sie `template-override` und `template`; prüfen Sie die Override-Regel mit den Kommentaren zu Template-Pfaden
([Template-Overrides](../website-ersteller/template-overrides.md)).

## Wie gebe ich JSON zurück?

Mit einer Serverfunktion von ezjscore oder, für andere Programme, über die REST-API ([JSON-Endpunkte mit ezjscore](json-endpunkte-mit-ezjscore.md)).

## Mein Cronjob-Skript wird nicht gefunden

Die Erweiterung ist nur in einigen Siteaccesses aktiv: Starten Sie mit `-s <siteaccess>` und prüfen Sie
`ExtensionDirectories[]` in `cronjob.ini` ([Cronjobs für Anwendungen](cronjobs-fuer-anwendungen.md)).

## Wohin gehört langsame Arbeit?

In einen Job der Warteschlange, einen Cronjob-Teil oder einen Inhalts-Job ([Muster für Hintergrundarbeit](muster-fuer-hintergrundarbeit.md)).

## Wie teste ich ohne Datenbank?

Halten Sie die Logik in einfachen Klassen und testen Sie diese mit PHPUnit ([PHPUnit-Tests ohne Datenbank](phpunit-tests-ohne-datenbank.md)).

## Wo frage ich nach oder melde etwas?

Auf der Seite **Fehler melden** der Hilfe und unter [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md); die
Orte der Community stehen auf [Wer ist die Exponential Foundation?](../../wer-ist-die-exponential-foundation.md).

## Verwandte Seiten

- [Anwendungsentwickler: hier beginnen](anwendungsentwickler-hier-beginnen.md)
- [Lesepfad für Anwendungsentwickler](lesepfad-fuer-anwendungsentwickler.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
