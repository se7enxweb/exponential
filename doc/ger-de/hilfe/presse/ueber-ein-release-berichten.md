---
title: Über ein Release berichten
audience: press
category: press-media
order: 100
summary: Wie Journalisten und Creator über eine neue Version von Exponential berichten - das Tag bestätigen, das Changelog und die Geschichte lesen, die Neuigkeit für Ihr Publikum auswählen, die Version richtig nennen und nie ein Datum für ein Release drucken, das noch nicht stattgefunden hat.
keywords: Release, neue Version, Changelog, Tag, Release Notes, Berichterstattung, Nachrichten, was ist neu, 6.0.14, 6.0.15, Version, Upgrade, release, new version, release notes, coverage, news, what is new, upgrade
featured: true
type: help
---
# Über ein Release berichten

Diese Seite ist für Journalisten, Nachrichtenredakteure, Blogger und Video-Creator, die über eine neue Version von
Exponential berichten wollen. Sie führt Sie von „ich habe gehört, es gibt eine neue Version“ zu einem Beitrag, bei dem
jeder Fakt eine Quelle hat. Die Regeln sind dieselben, die das Projekt auf sich selbst anwendet: Ein Release existiert,
wenn sein Tag existiert, und eine veröffentlichte Version wird danach nie verändert.

## Inhalt

1. [Was als Release zählt](#was-als-release-zählt)
2. [Der Stand am 18. Oktober 2026](#der-stand-am-18-oktober-2026)
3. [Fünf Schritte vom Hinweis zum Beitrag](#fünf-schritte-vom-hinweis-zum-beitrag)
4. [Die Neuigkeit für Ihr Publikum auswählen](#die-neuigkeit-für-ihr-publikum-auswählen)
5. [Was ein Release-Beitrag nicht sagen darf](#was-ein-release-beitrag-nicht-sagen-darf)
6. [Verwandte Seiten](#verwandte-seiten)

## Was als Release zählt

Eine Version von Exponential ist veröffentlicht, wenn ihr Git-Tag (zum Beispiel `v6.0.14`) im Repository
[github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential) veröffentlicht ist. Das Tag ist auch die
Version, die Composer von Packagist installiert. Arbeit, die übernommen, aber nicht getaggt ist, gehört zur
**Entwicklungslinie**, über die Sie nur berichten dürfen, wenn Sie das klar sagen ([Über 6.0.15 berichten, solange es in Entwicklung ist](ueber-6-0-15-in-entwicklung-berichten.md)).

Das Projekt erklärt: "A published tag is never changed: a fix for a problem in `v6.0.14` or an earlier tag
reaches you as the next release, not as a corrected copy of the old one"
([SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md)). Für Sie heißt das, ein Release-Beitrag bleibt wahr: Die Dateien hinter einer
Versionsnummer ändern sich nicht, nachdem Sie darüber geschrieben haben ([Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md)).

## Der Stand am 18. Oktober 2026

| Fakt | Wert | Quelle |
|---|---|---|
| Neueste veröffentlichte Version von Exponential | 6.0.14, Tag `v6.0.14`, 7. Juni 2026 | [Changelog 6.0.14](../../../changelogs/6.0/6.0.14.md), [Release-Übersicht](../../../eng-us/release-overview) |
| Erstes Release der Linie 6.0 | 6.0.0, 1. Januar 2024 | [Changelog 6.0.0](../../../changelogs/6.0/6.0.0.md) |
| Zahl der 6.0.x-Releases | 15 (6.0.0 bis 6.0.14) | [Wer ist 7x?, Die Releases 6.0.x](../wer-ist-7x.md#die-releases-60x) |
| Die Entwicklungslinie | 6.0.15, nicht veröffentlicht, kein Datum | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) |

Prüfen Sie diese Angaben am Tag der Veröffentlichung erneut; bis dahin kann ein neueres Tag existieren
([Ein Release selbst prüfen](ein-release-selbst-pruefen.md)).

## Fünf Schritte vom Hinweis zum Beitrag

1. **Bestätigen Sie das Tag.** Sehen Sie unter [GitHub Releases](https://github.com/se7enxweb/exponential/releases) nach
   oder führen Sie die Befehle unter [Ein Release selbst prüfen](ein-release-selbst-pruefen.md) aus. Kein Tag, kein Release.
2. **Lesen Sie das Changelog dieser Version.** Jede 6.0.x-Version hat eine Seite in `doc/changelogs/6.0/`, zum Beispiel
   [6.0.14](../../../changelogs/6.0/6.0.14.md). Lesen Sie zuerst „In short“, dann „Upgrading“
   ([Ein Changelog lesen](ein-changelog-lesen.md)).
3. **Lesen Sie die Geschichte für den Zusammenhang.** Die [Geschichte](../../../eng-us/history/README.md) erzählt Monat für Monat,
   was jede Änderung möglich macht, mit dem Commit dahinter ([Die Geschichtsseiten als Quelle](die-geschichtsseiten-als-quelle.md)).
4. **Öffnen Sie die Funktionsseite** jedes Punkts, den Sie beschreiben wollen. Das Changelog verlinkt sie.
5. **Bitten Sie um eine Stellungnahme, wenn Sie ein Zitat wollen.** Zitate kommen nur von 7x oder der Exponential
   Foundation, schriftlich ([Richtlinie für Zitate](richtlinie-fuer-zitate.md), [Ein Interview anfragen](ein-interview-anfragen.md)).

## Die Neuigkeit für Ihr Publikum auswählen

| Ihr Publikum | Wonach Sie im Changelog suchen | Beispiel aus 6.0.14 |
|---|---|---|
| Allgemeine Leser oder Wirtschaftsleser | Eine Hauptänderung und was sie bedeutet | "MongoDB 8 is a database next to MySQL, PostgreSQL and SQLite" |
| Fachpresse | Datenbanken, PHP-Versionen, Engines, APIs, Upgrade-Schritte | Das Feld für den Datenbanknamen im Einrichtungsassistenten; portables SQL für SQLite und PostgreSQL |
| Inhaber und Administratoren von Websites | Der Abschnitt „Upgrading“ | "No action needed for MySQL, PostgreSQL and SQLite sites" |
| Entwickler | Hinzugefügte Dateien, Skripte, Template-Operatoren | `bin/shell/generatefilelist.sh` und `share/filelist.md5` |

Alle vier Beispiele sind aus dem [Changelog 6.0.14](../../../changelogs/6.0/6.0.14.md) zitiert. Vorlagen für jede Art von
Beitrag stehen unter [Vorlagen für Release-Beiträge](vorlagen-fuer-release-beitraege.md).

## Was ein Release-Beitrag nicht sagen darf

| Schreiben Sie nicht | Schreiben Sie stattdessen |
|---|---|
| „6.0.15 erscheint am ...“ | „6.0.15 ist in Entwicklung; das Projekt hat kein Datum angekündigt.“ |
| „Version 6.0.14 wurde aktualisiert, um ... zu beheben“ | „Die Korrektur ist in der nächsten Version“ (veröffentlichte Versionen ändern sich nie) |
| „eZ Publish 6.0.14“ | „Exponential 6.0.14“ ([Marken- und Namensregeln](../geschaeft/marken-und-namensregeln.md#das-produkt-exponential)) |
| Eine Nutzerzahl, einen Marktanteil oder ein Vielfaches an Geschwindigkeit | Lassen Sie es weg; die Quellen nennen keine ([Aussagen, die Sie nicht machen dürfen](../fuersprecher/aussagen-die-sie-nicht-machen-duerfen.md#zahlen)) |
| Ein Zitat, das Sie aus den Notes zusammengesetzt haben | Zitieren Sie das Changelog wörtlich mit seinem Link oder bitten Sie um eine Stellungnahme |

Gehen Sie vor der Veröffentlichung die [Checkliste für die Release-Berichterstattung](checkliste-release-berichterstattung.md) durch.

## Verwandte Seiten

- [Checkliste für die Release-Berichterstattung](checkliste-release-berichterstattung.md)
- [Wo Changelogs und Release Notes stehen](wo-changelogs-und-release-notes-stehen.md)
- [Wie Versionsnummern funktionieren](wie-versionsnummern-funktionieren.md)
- [Über ein Release schreiben](../fuersprecher/ueber-ein-release-schreiben.md) (die Fassung dieser Seite für Fürsprecher)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
