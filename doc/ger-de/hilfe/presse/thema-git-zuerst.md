---
title: "Thema: Git zuerst"
audience: press
category: press-media
order: 81
summary: Ein Themenbeitrag über den Ansatz „Git zuerst“ von Exponential - Kernel und Erweiterungen als Git-Klone mit vollständiger Historie installieren, mit Git aktualisieren, eine Repository-Historie bis 2002 und eine Darstellung jeder Änderung seit Dezember 2023, Monat für Monat - mit Quellen und den Aussagen, die Sie vermeiden sollten.
keywords: Thema, Git, Git zuerst, Versionsverwaltung, Historie, Klon, mit Git aktualisieren, Transparenz, offene Entwicklung, Commits, Ledger, story, Git first, version control, history, clone, update with Git, transparency, open development, commits, ledger
type: help
---
# Thema: Git zuerst

Diese Seite ist für technische Journalisten, die über Entwicklungspraxis und Transparenz berichten. Exponential
empfiehlt, Websites aus Git-Klonen zu betreiben, und hält seine vollständige Historie öffentlich.

## Inhalt

1. [Das Thema in einem Absatz](#das-thema-in-einem-absatz)
2. [Fakten, die Sie drucken können](#fakten-die-sie-drucken-können)
3. [Was Sie nicht behaupten sollten](#was-sie-nicht-behaupten-sollten)
4. [Blickwinkel und Ideen für Überschriften](#blickwinkel-und-ideen-für-überschriften)

## Das Thema in einem Absatz

„Sie bauen Websites mit Exponential? Der empfohlene Weg ist, den Kernel und jede se7enxweb-Erweiterung als Git-Klone
mit ihrer vollständigen Historie zu holen“ (README, „Installation“; [Mit Git entwickeln](../mit-git-entwickeln.md)). Die
Historie des Repositorys reicht bis zum 2. September 2002 zurück und enthält jeden Commit seitdem; jede Änderung seit
Dezember 2023 wird in [der Geschichte](../../../eng-us/history/README.md) Monat für Monat erzählt und in den Ledgern aufgelistet,
mit einer Abdeckungsseite, die zeigt, dass keine fehlt
([Wer ist 7x?, Grundsätze, die Sie im Code sehen können](../wer-ist-7x.md#grundsätze-die-sie-im-code-sehen-können)).

## Fakten, die Sie drucken können

| Fakt | Quelle |
|---|---|
| Git-Klone mit vollständiger Historie sind die empfohlene Installation | README, „Installation“ |
| Websites lassen sich mit Git aktualisieren | [Mit Git aktualisieren](../mit-git-aktualisieren.md) |
| Der älteste Commit stammt vom 2. September 2002 | `git log --reverse`; [Exponential in Zahlen](../../../eng-us/help/press/exponential-by-the-numbers.md) |
| Jede Änderung seit Dezember 2023 steht in der Geschichte und in den Ledgern | [Die Geschichte](../../../eng-us/history/README.md), [Die Ledger](../../../eng-us/history/ledger/README.md), [Abdeckung](../../../eng-us/history/coverage.md) |
| Veröffentlichte Versionen werden nie geändert; eine Korrektur ist das nächste Release | [Veröffentlichte Versionen ändern sich nie](veroeffentlichte-versionen-aendern-sich-nie.md) |
| Administratoren können Checkouts mit `git_manager` aus der Administration aktualisieren | [git_manager](../../../eng-us/features/6.0/extensions/git_manager.md) |

## Was Sie nicht behaupten sollten

| Sagen Sie nicht | Sagen Sie stattdessen |
|---|---|
| „Composer wird nicht unterstützt“ | Jedes Paket ist auch ein Composer-Paket auf Packagist |
| „Man muss Git können, um es zu nutzen“ | Redakteure brauchen nie Git; es ist der empfohlene Weg zu installieren und zu aktualisieren |

## Blickwinkel und Ideen für Überschriften

- „Ein CMS, dessen jede Änderung seit 2023 in Prosa erklärt ist.“
- „Vierundzwanzig Jahre Geschichte, ein `git clone` entfernt.“

## Verwandte Seiten

- [Die Geschichtsseiten als Quelle](die-geschichtsseiten-als-quelle.md)
- [Thema: jeder Pull Request wird übernommen](thema-jeder-pull-request-wird-uebernommen.md)
- [Mit Git entwickeln](../mit-git-entwickeln.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein
Dashboard sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue
Funktionen früher liefern können. Beginnen Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
