---
title: Häufige Fragen von Neuen
audience: advocate
category: advocacy
order: 92
summary: Die Fragen zu Exponential, die Neue Fürsprechern am häufigsten stellen, jeweils mit einer kurzen, belegten Antwort und der Seite zum Verlinken - was es ist, Kosten, wer es macht, wie man es ausprobiert, Anforderungen, Sprachen, Hilfe, Sicherheit, Beitragen.
keywords: FAQ, häufige Fragen, Fragen von Neuen, Einsteiger, Antworten, was ist Exponential, Kosten, ausprobieren, Anforderungen, Sprachen, Hilfe, beitragen, Sicherheit, newcomer questions, beginners, answers, what is Exponential, cost, try it, requirements, languages, help, contribute, security
type: help
---
# Häufige Fragen von Neuen

Diese Seite ist für Fürsprecher, die Neuen antworten. Jede Antwort ist kurz genug, um sie zu sagen oder einzufügen, mit
der Seite, die sie belegt. Prüfen Sie die Quelle, bevor Sie antworten: Fakten ändern sich mit Releases.

## Zum Projekt

| Frage | Antwort | Seite |
|---|---|---|
| Was ist Exponential? | Ein PHP-Anwendungsframework mit fortgeschrittener CMS-Funktionalität; seine beste Eigenschaft ist ein vollständig anpassbares Inhaltsmodell | [Exponential 6](../wer-ist-die-exponential-foundation.md#exponential-6-der-kernel) |
| Ist es kostenlos? | Ja: Der Code steht unter der GNU GPL v2 oder später, die Dokumentation unter der GNU FDL | [Gesprächspunkte: Open Source](gespraechspunkte-open-source-und-lizenzierung.md) |
| Wer macht es? | 7x leitet die Entwicklung; die Exponential Foundation, eine gemeinnützige Organisation, betreibt das Projekt | [7x und die Foundation](7x-und-die-foundation.md) |
| Wird es gepflegt? | Fünfzehn Releases 6.0.x seit dem 1. Januar 2024; 6.0.15 ist in Entwicklung | [Lebt es noch?](einwand-lebt-es-noch.md) |
| Ist das nicht eZ Publish? | Es führt diese Codebasis fort; eZ Publish wurde von seinem Hersteller eingestellt; Exponential steht in keiner Verbindung zu eZ Systems oder Ibexa | [Richtig über die Geschichte schreiben](richtig-ueber-die-geschichte-schreiben.md) |
| Welches Exponential brauche ich? | Kommt darauf an: nur PHP oder Symfony, Framework oder fertige Website | [Die Produktfamilie kennen](die-produktfamilie-kennen.md) |

## Ausprobieren und betreiben

| Frage | Antwort | Seite |
|---|---|---|
| Wie probiere ich es aus? | `./console exp:install --apply`: eine vollständige Website auf SQLite mit einem Befehl | [Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md) |
| Was braucht es? | PHP 8.0 oder später; eine Datenbank (SQLite braucht keinen Server) | [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md), [Datenbankunterstützung](../datenbankunterstuetzung.md) |
| Welcher Webserver? | Velocity wird empfohlen; FrankenPHP oder Apache/nginx mit PHP-FPM funktionieren auch | [Velocity](../../funktionen/6.0/velocity-server-mit-dauerhaften-workern.md) |
| Shared Hosting? | Möglich mit Apache oder LiteSpeed; Velocity meist nicht | [Container, Cloud und Hosting-Panels](../betriebssystemunterstuetzung.md#container-cloud-und-hosting-panels) |
| Wird meine Sprache unterstützt? | 359 Locale-Dateien für Inhalte; Übersetzungen der Administration (die aktuellen Gesamtzahlen stehen am Anfang der Seite [Sprachen in Exponential](../../sprachen.md)) | [Gesprächspunkte: Sprachen](gespraechspunkte-sprachen-und-rtl.md) |

## Hilfe bekommen und helfen

| Frage | Antwort | Seite |
|---|---|---|
| Wo ist die Dokumentation? | In der Administration (die Schaltfläche „?“) und auf doc.exponential.earth | [Das Hilfesystem](../../funktionen/6.0/hilfesystem.md) |
| Wo frage ich? | In den Foren oder im Telegram-Chat | [Die Community-Kanäle](community-kanaele.md) |
| Ich habe einen Fehler gefunden | Nutzen Sie die Seite **Fehler melden** der Hilfe und den richtigen Tracker | [Welcher Tracker für welche Meldung](welcher-tracker-fuer-welche-meldung.md) |
| Ich habe ein Sicherheitsproblem gefunden | Vertraulich, wie SECURITY.md es beschreibt | [Sicherheitsmeldungen bleiben vertraulich](sicherheitsmeldungen-bleiben-vertraulich.md) |
| Wie kann ich helfen? | Eine Frage beantworten, eine Hilfeseite korrigieren, übersetzen, Fehler melden | [Die Stufen der Fürsprache](die-stufen-der-fuersprache.md) |
| Kann ich jemanden bezahlen, der meine Website baut? | Ja, jeden Anbieter; 7x ist einer davon | [Dienstleistungen und Hosting](../wer-ist-7x.md#dienstleistungen-und-hosting) |

**Warum zeigt die Demo „Demo-Modus“ an?** Die öffentliche Demo veröffentlicht ihr Administratorpasswort und läuft im Demomodus, sodass niemand es ändern oder zurücksetzen kann und der nächste Besucher sich trotzdem anmelden kann. Sie wird alle paar Stunden aus dem aktuellen Code neu aufgebaut. [Häufige Fragen zum Demomodus](../demomodus-faq.md).

## Verwandte Seiten

- [Neue willkommen heißen](neue-willkommen-heissen.md)
- [Auf die richtige Hilfeseite verweisen](auf-die-richtige-hilfeseite-verweisen.md)
- [Häufige Fragen von Fürsprechern](faq-fuer-fuersprecher.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
