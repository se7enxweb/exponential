---
title: Warum es Exponential heißt
audience: press, advocate
category: press-media
order: 8
summary: Die Fakten hinter dem Produktnamen - die Ankündigung vom 11. August 2025, die Umbenennung im Repository am nächsten Tag, das erste Release unter dem Namen, was sich geändert hat und was nicht, und wie Sie ohne Spekulation darüber schreiben.
keywords: Name, Umbenennung, Rebranding, Exponential, eZ Publish, Ibexa, August 2025, 6.0.10, Marke, Exponential CMS, warum der Name, name, rename, rebranding, trademark, why the name
type: help
---
# Warum es Exponential heißt

Diese Seite ist für Journalisten, die den Namen des Produkts erklären. Sie hält sich an das, was die Quellen sagen.
Mehrere Reporter waren versucht, die Lücken mit einer Geschichte zu füllen; diese Seite sagt Ihnen, wo die Lücken sind.

## Inhalt

1. [Was geschah](#was-geschah)
2. [Der Wortlaut der Ankündigung](#der-wortlaut-der-ankündigung)
3. [Was sich geändert hat und was nicht](#was-sich-geändert-hat-und-was-nicht)
4. [Wie Sie darüber schreiben](#wie-sie-darüber-schreiben)
5. [Was die Quellen nicht sagen](#was-die-quellen-nicht-sagen)

## Was geschah

| Datum | Ereignis | Quelle |
|---|---|---|
| 11. August 2025 | Die Website des Projekts kündigt den neuen Namen an | <https://exponential.earth/about/project> |
| 12. August 2025 | README, `composer.json` und `lib/version.php` ändern sich im Repository; ein Commit benennt das Produkt in 332 Dateien von `bin/`, `doc/`, `design/` und den Übersetzungen um | [August 2025](../../../eng-us/history/2025/2025-08.md) |
| 28. August 2025 | 6.0.10, das erste Release unter dem Namen Exponential | [Die 6.0.x-Releases](../wer-ist-7x.md#die-releases-60x) |

## Der Wortlaut der Ankündigung

Von <https://exponential.earth/about/project>, zitiert auf [Wer ist 7x?](../wer-ist-7x.md#warum-das-produkt-exponential-heißt):

> "At the specific request of Ibexa we are changing this projects name to 'Exponential' or 'Exponential (CMS)'
> effective as of August, 11th 2025."

Dieselbe Seite und das README tragen den Hinweis: „This project is not associated with the original eZ Publish
software or its original developer, eZ Systems or Ibexa“.

Für die Plattform-Linie ergänzt das README, sie sei umbenannt worden, „to avoid any trademark issues“
([Regeln für Marke und Namen](../geschaeft/marken-und-namensregeln.md#was-die-quellen-nicht-entscheiden)).

## Was sich geändert hat und was nicht

| Geändert | Nicht geändert |
|---|---|
| Der Produktname im Installationsprogramm, in der Administration, auf der Kommandozeile, in den Übersetzungen, der Dokumentation und auf dem Paketserver | PHP-Klassennamen wie `eZContentObject` und `eZINI` |
| Das README und die Paketbeschreibung | Datenbanktabellen wie `ezcontentobject` |
| Die Konstante `EDITION` in `lib/version.php`, die `Exponential` lautet | Skriptnamen wie `bin/php/ezcache.php` |
| | Paketnamen von Erweiterungen wie `ezwebin` und `eztags` |

Die Umbenennung „touched text, not identifiers“ ([Das Produkt heißt Exponential](../../../eng-us/features/6.0/rebranding-to-exponential.md)).
Deshalb sehen Leser im Code noch `eZ`: Es ist ein Präfix echter Bezeichner, und sie umzubenennen würde jede Website und
jede Erweiterung kaputt machen.

## Wie Sie darüber schreiben

```text
Good:   "The project renamed the product Exponential in August 2025, at the request of Ibexa, according to the
         project website."
Good:   "Exponential, as the software has been called since August 2025, ..."
Avoid:  "Exponential, formerly known as eZ Publish, the CMS by Ibexa ..."   (suggests a link to Ibexa)
Avoid:  "eZ Publish rebrands as Exponential"                                 (eZ Publish was not the actor)
Avoid:  "after a trademark dispute"                                          (no source says there was one)
```

Nennen Sie das alte Produkt nur in der Vergangenheitsform ([Über die eZ-Publish-Jahre schreiben](ueber-die-ez-publish-jahre-schreiben.md)).

## Was die Quellen nicht sagen

- Warum Ibexa gefragt hat oder wie die Bitte vorgebracht wurde.
- Ob es irgendein rechtliches Verfahren gab. Schreiben Sie nicht „Streit“, „Klage“ oder „gezwungen“.
- Wer das Wort „Exponential“ gewählt hat oder woran es erinnern soll.

Wenn Ihr Beitrag etwas davon braucht, fragen Sie 7x und die Foundation und nennen Sie die Quelle ihrer Antwort
([Fragen an 7x](fragen-an-7x.md)).

## Verwandte Seiten

- [Nicht mit eZ Systems oder Ibexa verbunden](nicht-verbunden-mit-ez-systems-oder-ibexa.md)
- [Marke und Namen für Medien](marke-und-namen-fuer-medien.md)
- [Das Produkt heißt Exponential](../../../eng-us/features/6.0/rebranding-to-exponential.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
