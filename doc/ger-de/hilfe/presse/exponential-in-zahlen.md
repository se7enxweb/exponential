---
title: Exponential in Zahlen
audience: press, advocate
category: press-media
order: 15
summary: Die Zahlen zu Exponential, die die Dokumentation tatsächlich gezählt hat - Commits, Releases, Pakete, Sprachen, Repositorys, die Ledger der Geschichte und die dokumentierten Velocity-Messungen - jeweils mit Datum, Zählweise und wie Sie neu zählen, dazu die Zahlen, die es nicht gibt.
keywords: Zahlen, Kennzahlen, Statistik, Daten, Zählungen, Commits, Releases, Pakete, Packagist, Sprachen, Locales, Übersetzungen, Repositorys, Velocity-Benchmark, Anfragen pro Sekunde, numbers, figures, statistics, data, counts, packages, languages, locales, translations, repositories, Velocity benchmark, requests per second
type: help
---
# Exponential in Zahlen

Diese Seite ist für Reporter und Datenjournalisten. Sie führt nur Zahlen auf, die die Dokumentation gezählt hat, mit
Datum und Methode, damit Sie sie verantwortungsvoll drucken oder neu zählen können. Zahlen ändern sich mit jedem
Release; drucken Sie immer das Datum mit der Zahl.

## Inhalt

1. [Die gezählten Zahlen](#die-gezählten-zahlen)
2. [Die Velocity-Messungen](#die-velocity-messungen)
3. [Zahlen, die es nicht gibt](#zahlen-die-es-nicht-gibt)
4. [Neu zählen](#neu-zählen)
5. [Eine Zahl drucken](#eine-zahl-drucken)

## Die gezählten Zahlen

Am 19. Oktober 2026 mit dem Befehl in der dritten Spalte erneut gezählt; die erste Zählung vom 18. Oktober 2026 steht
unter [Wer ist 7x?, 7x in Zahlen](../wer-ist-7x.md#7x-in-zahlen).

| Zahl | Wert | Wie gezählt |
|---|---|---|
| Jahre im Urheberrechtshinweis | 1998 bis 2026 (7x und die Foundation); 1999 bis 2014 (eZ Systems AS) | COPYRIGHT.md |
| Ältester Commit im Repository | 2. September 2002 | `git log --reverse` |
| Commits auf `main` | 27.727 | `git rev-list --count origin/main` |
| Übernommene Pull Requests im Verlauf | 1.042 | `git log --grep="Merge pull request"` |
| 6.0.x-Releases | 15 (6.0.0 am 1. Januar 2024 bis 6.0.14 am 7. Juni 2026); 6.0.15 in Entwicklung, kein Tag am 19. Oktober 2026 | `git tag -l 'v6.0.*'` |
| Vom Kernel verlangte se7enxweb-Pakete | 66 am 18. Oktober 2026, 69 am 25. Oktober 2026 (die Zahl wächst, wenn Erweiterungen hinzukommen) | `composer.json` |
| Pakete des Vendors se7enxweb auf Packagist | 219 | <https://packagist.org/packages/list.json?vendor=se7enxweb> |
| Öffentliche Repositorys des GitHub-Kontos se7enxweb | 1.025, davon 95 keine Forks | `gh repo list se7enxweb --limit 1100` |
| Änderungen in der Geschichte seit Dezember 2023 | 6.556 in 53 Repositorys, 1.037 davon vom Team se7enxweb (18. Oktober 2026) | [Ökosystem](../../geschichte/oekosystem.md) |
| Commit-Autoren vor 2016 | 194 verschiedene Namen | `git log --until=2016-01-01 --format=%an` |
| Locale-Dateien (Inhaltssprachen und Länder) | 268, davon 40 von rechts nach links | [Sprachen](../../sprachen.md) |
| Übersetzungen der Administration | 71, alle vollständig (26. Oktober 2026) | [Sprachen](../../sprachen.md) |
| Unterstützte Datenbanken | 6 | README, „Requirements“ |
| Unterstützte PHP-Versionen | 8.0 bis 8.5 | README; [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |

## Die Velocity-Messungen

Die README dokumentiert diese Zahlen für Exponential Velocity, mit schriftlich festgehaltener Methode und Skripten, die
mit dem Projekt ausgeliefert werden ([Thema: wie die Velocity-Zahlen gemessen werden](thema-wie-die-velocity-zahlen-gemessen-werden.md)):

| Zahl | Was sie bedeutet, wie die README es erklärt |
|---|---|
| 0,36 ms pro Seite aus dem Cache | Die eigene Arbeit des Servers für eine Seite aus dem Cache, ein Median über Hunderte von Anfragen |
| Dauerhaft 8.941 Anfragen pro Sekunde | Gemessen auf einer Maschine mit 12 Kernen, die eine echte Seite ausliefert, ohne Fehler bis zu 384 gleichzeitigen Verbindungen |
| Wiederholte Besuche berühren nie das Netzwerk | Mit dem optionalen Navigations-Cache (einem Service Worker) beantwortet der Browser einen erneuten Besuch |

Drucken Sie sie als „vom Projekt dokumentiert“ und behalten Sie die Bedingungen bei. Sie sind kein Vergleich mit einem
anderen Produkt.

## Zahlen, die es nicht gibt

Die Quellen zählen Folgendes nicht. Schätzen Sie es nicht, und betrachten Sie jede Zahl, die Sie anderswo dazu finden,
als ungeprüft, bis ihr Urheber Ihnen sagt, wie sie gezählt wurde:

- Installationen, Websites oder Nutzer von Exponential;
- Kunden von 7x, sein Umsatz oder seine Mitarbeiter;
- das Budget der Foundation oder die Zahl der Spender;
- Entwickler, die Exponential kennen;
- Marktanteil oder Ranglisten;
- Downloads (Packagist zeigt Installationsstatistiken pro Paket; sie zählen Composer-Installationen, keine Websites).

Das Pressekit nennt zwei Zahlen („Active Properties“ und „Forge Projects“) ohne Methode; siehe
[Das Pressekit](das-pressekit.md#fakten-im-kit-die-veraltet-sind).

## Neu zählen

Auf einem Klon des Repositorys:

```bash
git clone https://github.com/se7enxweb/exponential.git && cd exponential
git rev-list --count HEAD
git tag -l 'v6.0.*' --sort=version:refname | wc -l
git log --grep="Merge pull request" --oneline | wc -l
```

Ihre Zählung wird von der Tabelle abweichen, wenn das Repository weitergewachsen ist. Das ist zu erwarten; drucken Sie
Ihre eigene Zählung mit Ihrem Datum ([Zahlen prüfen](zahlen-pruefen.md)).

## Eine Zahl drucken

```text
Gut:     „Das Repository enthält mehr als 27.000 Commits, der älteste vom September 2002
         (Zählung vom 19. Oktober 2026).“
Gut:     „Am 25. Oktober 2026 verlangte die composer.json des Kernels 69 Pakete, die von 7x veröffentlicht werden.“
Meiden:  „Tausende Unternehmen nutzen Exponential.“      (keine Quelle)
Meiden:  „Exponential ist 10-mal schneller als ...“      (kein Vergleich veröffentlicht)
```

## Verwandte Seiten

- [Faktenblatt für Journalisten](faktenblatt-fuer-journalisten.md)
- [Was die Quellen nicht sagen](was-die-quellen-nicht-sagen.md)
- [Zahlen prüfen](zahlen-pruefen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
