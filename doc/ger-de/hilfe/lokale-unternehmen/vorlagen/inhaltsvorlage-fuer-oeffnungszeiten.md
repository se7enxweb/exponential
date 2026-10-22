---
title: Inhaltsvorlage für Öffnungszeiten
audience: local-business, businesses, customer, editor
category: local-business
order: 97
summary: Texte zum Ausfüllen für Öffnungszeiten auf der Website eines lokalen Unternehmens - reguläre Öffnungszeiten als Textabschnitt oder Matrixzeilen, geteilte Öffnungszeiten, Zeiten nur nach Vereinbarung, Hinweise zu Feiertagen und Schließungen, Saisonzeiten und Notfallhinweise - jedes Mal gleich geschrieben und leicht zu übersetzen.
keywords: Vorlage für Öffnungszeiten, Text für Geschäftszeiten, Hinweisvorlage, Feiertagshinweis, Schließungshinweis, Saisonzeiten, Matrix, Textabschnitt, lokales Unternehmen, opening hours template, business hours text, notice template, holiday notice, closure notice, seasonal hours, text block, local business
type: help
---
# Inhaltsvorlage für Öffnungszeiten

Diese Vorlage ist für die Person, die die Öffnungszeiten eines lokalen Unternehmens in die Website eintippt. Wenn Sie
sie jedes Mal gleich schreiben, sind sie leicht zu lesen, leicht zu übersetzen und leicht mit Ihren Einträgen auf
Kartenplattformen zu vergleichen. Wie die Zeiten gespeichert werden, erklärt
[Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md).

Die Textbausteine unten stehen in Codeblöcken auf Englisch; unter jedem folgt eine deutsche Fassung zum Kopieren.

## Inhalt

1. [Schreibregeln](#schreibregeln)
2. [Reguläre Öffnungszeiten als Textabschnitt](#reguläre-öffnungszeiten-als-textabschnitt)
3. [Reguläre Öffnungszeiten als Matrixzeilen](#reguläre-öffnungszeiten-als-matrixzeilen)
4. [Hinweise](#hinweise)
5. [Sonderfälle](#sonderfälle)
6. [Bevor Sie veröffentlichen](#bevor-sie-veröffentlichen)

## Schreibregeln

| Regel | Beispiel |
|---|---|
| Ein Format für Uhrzeiten, das Ihre Kunden verwenden | `8:00 - 18:00` oder `8 am - 6 pm`, nie beides |
| Tage überall gleich ausgeschrieben oder abgekürzt | `Montag`, nicht hier `Mo` und dort `Montag` |
| Tage mit denselben Zeiten zusammenfassen | `Montag bis Freitag` |
| „geschlossen“ für geschlossene Tage schreiben | lassen Sie keinen Tag weg |
| Vollständige Daten in Hinweisen | `Montag, 25. Dezember`, nicht „nächsten Montag“ |
| Dieselbe Formulierung wie in Ihren Einträgen | damit Besucher nicht verwirrt werden |

## Reguläre Öffnungszeiten als Textabschnitt

```text
Monday to Friday    8:00 - 18:00
Saturday            9:00 - 14:00
Sunday              closed
```

> Montag bis Freitag &nbsp; 8:00 - 18:00
> Samstag &nbsp; 9:00 - 14:00
> Sonntag &nbsp; geschlossen

Mit Mittagspause:

```text
Monday to Friday    8:00 - 12:30 and 14:00 - 18:00
Saturday            9:00 - 13:00
Sunday and public holidays   closed
```

> Montag bis Freitag &nbsp; 8:00 - 12:30 und 14:00 - 18:00
> Samstag &nbsp; 9:00 - 13:00
> Sonntag und Feiertage &nbsp; geschlossen

## Reguläre Öffnungszeiten als Matrixzeilen

Wenn Ihre Website eine Klasse für Öffnungszeiten mit einer Matrix hat (Spalten Tag, Öffnet, Schließt, Hinweis):

| Tag | Öffnet | Schließt | Hinweis |
|---|---|---|---|
| Montag | 8:00 | 18:00 | |
| Dienstag | 8:00 | 18:00 | |
| Mittwoch | 8:00 | 12:30 | nachmittags geschlossen |
| Donnerstag | 8:00 | 18:00 | |
| Freitag | 8:00 | 20:00 | lange Öffnung |
| Samstag | 9:00 | 14:00 | |
| Sonntag | | | geschlossen |

Ein Tag mit Mittagspause bekommt zwei Zeilen mit demselben Tagesnamen.

## Hinweise

Feiertag:

```text
Closed on <weekday> <date> (<holiday name>). Open again <weekday> <date> from <time>.
```

> Am &lt;Wochentag&gt;, &lt;Datum&gt; (&lt;Name des Feiertags&gt;) geschlossen. Ab &lt;Wochentag&gt;, &lt;Datum&gt;
> wieder ab &lt;Uhrzeit&gt; geöffnet.

Mehrere Tage:

```text
Holiday closure: closed from <weekday> <date> to <weekday> <date>.
Open again <weekday> <date> from <time>. Orders placed in this time are sent on <date>.
```

> Betriebsferien: geschlossen von &lt;Wochentag&gt;, &lt;Datum&gt; bis &lt;Wochentag&gt;, &lt;Datum&gt;.
> Ab &lt;Wochentag&gt;, &lt;Datum&gt; wieder ab &lt;Uhrzeit&gt; geöffnet. In dieser Zeit aufgegebene Bestellungen
> werden am &lt;Datum&gt; verschickt.

Geänderte Öffnungszeiten für einen Zeitraum:

```text
Summer hours from <date> to <date>: Monday to Saturday <time> - <time>, Sunday closed.
```

> Sommeröffnungszeiten vom &lt;Datum&gt; bis &lt;Datum&gt;: Montag bis Samstag &lt;Uhrzeit&gt; - &lt;Uhrzeit&gt;,
> Sonntag geschlossen.

Ungeplante Schließung:

```text
Today, <weekday> <date>, we are closed because of <short reason>. We are sorry.
Open again <weekday> <date>. Urgent questions: <phone or e-mail>.
```

> Heute, &lt;Wochentag&gt;, &lt;Datum&gt;, sind wir wegen &lt;kurzer Grund&gt; geschlossen. Wir bitten um
> Entschuldigung. Ab &lt;Wochentag&gt;, &lt;Datum&gt; wieder geöffnet. Dringende Fragen: &lt;Telefon oder E-Mail&gt;.

Denken Sie daran, jeden Hinweis nach seinem Datum zu entfernen, oder geben Sie ihm ein Datum „gültig bis“, wenn die
Klasse eines hat ([Feiertags- und Saisonöffnungszeiten](../karten-und-oeffnungszeiten/feiertags-und-saisonoeffnungszeiten.md)).

## Sonderfälle

| Unternehmen | Ergänzen Sie |
|---|---|
| Praxis oder Friseursalon mit Terminen nur zu bestimmten Zeiten | `Donnerstag 14:00 - 19:00 nur nach Vereinbarung` |
| Handwerk | `Bürozeiten` für das Telefon und `Notdienst: <Telefon>`, falls angeboten |
| Restaurant | eigene Zeiten für die `Küche`: `Küche geöffnet bis 21:30` |
| Marktstand | den Ort mit den Tagen: `Samstag 8:00 - 13:00, Marktplatz` ([Mehrere Standorte](../karten-und-oeffnungszeiten/mehrere-standorte.md)) |
| Saisonbetrieb | die Saison: `Geöffnet von <Monat> bis <Monat>` |

## Bevor Sie veröffentlichen

- [ ] Jeder Wochentag erscheint.
- [ ] Die Zeiten stimmen mit dem überein, was an der Tür steht.
- [ ] Dieselben Zeiten stehen auf jeder Karten- und Suchplattform.
- [ ] In jede Sprache der Website übersetzt.
- [ ] Hinweise haben vollständige Daten und jemanden, der sie entfernt.

## Verwandte Seiten

- [Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md)
- [Feiertags- und Saisonöffnungszeiten](../karten-und-oeffnungszeiten/feiertags-und-saisonoeffnungszeiten.md)
- [Vorlagen für lokale Unternehmen](README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
