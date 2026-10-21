---
title: Die Meldeseite der Hilfe
audience: customer, admin, editor
category: customer-success
order: 18
summary: Die Seite Fehler melden der Hilfe in der Exponential-Administration - wer sie öffnen kann, was sie vorbereitet (den richtigen Tracker, ein entworfenes Issue, sichere Diagnosedaten, den vertraulichen Weg für Sicherheitsprobleme) und wie das Team eines Website-Betreibers sie nutzt.
keywords: Fehler melden, exphelp/report, Hilfe-Center, Issue, Diagnosedaten, Tracker, GitHub, vorausgefülltes Issue, Sicherheit, report an error, help center, issue, diagnostics, tracker, prefilled issue, security
type: help
---
# Die Meldeseite der Hilfe

Jede Exponential-Administration hat ein Hilfe-Center und darin eine Seite namens **Fehler melden** unter
`/exphelp/report`. Sie führt eine Person durch die Meldung eines Fehlers in der Software: welcher Tracker zu verwenden
ist, was zu schreiben ist, ein vorausgefülltes Issue und eine Zusammenfassung der Installation zum Kopieren, und wo ein
Sicherheitsproblem vertraulich zu melden ist ([Das Hilfesystem](../../../../eng-us/features/6.0/help-system.md)). Diese Seite
erklärt aus Sicht des Betreibers, wofür sie da ist.

## Wer sie öffnen kann

Die Meldeseite ist Teil der Hilfe, die die Richtlinie `exphelp/read` erfordert. Die Rolle Administrator hat sie, eine
neue Installation gibt sie der Rolle Editor, und die Hilfe wird nie Besuchern gezeigt, die nicht angemeldet sind
([Das Hilfesystem](../../../../eng-us/features/6.0/help-system.md)). Auf einer Installation aus der Zeit vor dem Hilfesystem
vergibt ein Administrator sie mit einem Befehl, der auf derselben Seite beschrieben ist.

Das Dashboard der Hilfe befindet sich unter `/exphelp/dashboard`; die Schaltfläche **Hilfe** in der Kopfzeile der
Administration öffnet die Hilfeseite, die zu der Seite gehört, auf der Sie gerade sind.

## Was die Seite vorbereitet

| Teil | Was er tut |
|---|---|
| Bevor Sie melden | Verweist auf die Suche der Hilfe, die Logs und die Fehlerübersicht und darauf, es noch einmal zu versuchen: Die Antwort ist vielleicht schon dokumentiert |
| Wo Sie melden | Die in `settings/exphelp.ini [IssueTrackers]` konfigurierten Tracker: Exponential (die Software), Exponential Velocity (der Anwendungsserver) und das Projekt und die Community; dazu das eigene Repository einer Erweiterung, wo es eines gibt |
| Die Meldung schreiben | Felder für einen Titel, was geschah, die Schritte zum Nachstellen und was Sie erwartet haben |
| Diagnosedaten | Eine Zusammenfassung der Installation (Versionen, PHP, Engine, Design) zum Kopieren; „nie Passwörter, Schlüssel, Datenbankhosts oder personenbezogene Daten“ |
| Sicherheitsprobleme | Der vertrauliche Weg: E-Mail an security@se7enx.com, oder die private Meldung von Sicherheitslücken auf GitHub |

Wenn die Meldung geschrieben ist, bereitet die Seite im gewählten Tracker ein neues Issue mit diesem Text vor, fertig
zum Öffnen. Zum Absenden brauchen Sie ein kostenloses GitHub-Konto ([Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)).
Die Diagnosedaten und der Text des Issues bleiben bewusst auf Englisch, „weil die Maintainer sie lesen“
([Das Hilfesystem](../../../../eng-us/features/6.0/help-system.md)).

## Welcher Tracker

Aus `settings/exphelp.ini`:

| Tracker | Für |
|---|---|
| Exponential (die Software) | „Fehler im Kernel, in der Administration, in den mitgelieferten Erweiterungen und in der Dokumentation“ |
| Exponential Velocity | „Fehler des Velocity-Servers selbst: Start, Worker, sein Response-Cache, HTTPS, der Befehl exp:velocity“ |
| Exponential-Projekt und Community | „Das Projekt, die Website, das Ökosystem und alles, was kein Fehler in einer Software ist“ |

Erweiterungen, die im Repository des Kernels ausgeliefert werden (expservices, ezformtoken, ezjscore, ezoe), gehen an
den Software-Tracker; andere aktive Erweiterungen aus der GitHub-Organisation des Projekts werden mit einem eigenen
Tracker angeboten (`[IssueTrackers] BundledExtensions[]` und `ExtensionOwners[]`).

## Wie Ihr Team sie nutzt

- **Nicht für jedes Problem.** Die meisten Probleme liegen in der Einrichtung, den Templates oder dem Hosting der
  Website selbst und gehen an Ihren Administrator, Partner oder Hoster ([Probleme melden](probleme-melden.md)). Die
  Meldeseite ist für einen Fehler in der Software selbst.
- **Meist meldet Ihr Administrator oder Partner**, nachdem er bestätigt hat, dass das Problem nicht lokal ist.
  Redakteure können die Seite trotzdem nutzen, um zu sehen, welche Informationen eine gute Meldung braucht.
- **Zuerst suchen.** Wenn das Problem bereits bekannt ist, ergänzen Sie, was Sie wissen, in diesem Issue, statt ein
  neues zu eröffnen.
- **Den Text vor dem Absenden prüfen.** Die Diagnosedaten lassen Geheimnisse weg, Ihre eigene Beschreibung vielleicht
  nicht: Entfernen Sie Kundennamen, E-Mail-Adressen und alles Vertrauliche.

> **Warnung**
> Ein Sicherheitsproblem gehört nie in ein öffentliches Issue. Die Seite hat dafür einen eigenen Abschnitt; siehe
> [Vertrauliche Sicherheitsmeldungen](vertrauliche-sicherheitsmeldungen.md).

## Verwandte Seiten

- [GitHub-Issues](github-issues.md)
- [Wenn etwas schiefgeht](../../wenn-etwas-schiefgeht.md)
- [Systeminformationen](../../../anleitungen/systeminformationen.md): Ihr **Bericht als Text kopieren** liefert eine ähnliche sichere Zusammenfassung
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
