---
title: Märkte mit Rechts-nach-links-Schrift
audience: businesses, sales-marketing, website-creator
category: business-solutions
order: 50
summary: Was ein Unternehmen wissen muss, bevor es mit Exponential auf Arabisch, Hebräisch, Persisch, Urdu und in anderen Sprachen mit Rechts-nach-links-Schrift verkauft oder veröffentlicht – welche Locales es gibt, was Administration und Inhalte von selbst tun, was das öffentliche Design leisten muss und wie Sie testen.
keywords: Rechts-nach-links, RTL, Arabisch, Hebräisch, Persisch, Urdu, Jiddisch, Schreibrichtung, Administration, Design, Testen, Naher Osten, Unternehmen, right to left, Arabic, Hebrew, Persian, Yiddish, dir, text direction, TextDirection, administration, design, testing, Middle East, business
type: help
---
# Märkte mit Rechts-nach-links-Schrift

Diese Seite richtet sich an Inhaber, Marketingverantwortliche und Projektmanager, die eine Website, einen Shop oder eine
Sprachversion in einer Schrift planen, die von rechts nach links geschrieben wird. Sie klärt die Erwartungen: was
Exponential von selbst tut, was Ihr Designteam bauen muss und was ein muttersprachlicher Leser vor dem Start prüfen
muss. Die technischen Anleitungen sind [Websites mit Rechts-nach-links-Schrift](../../../../eng-us/help/creators/website-creators/right-to-left-sites.md)
für Website-Ersteller und [Sprachen mit Rechts-nach-links-Schrift in der Administration](../../../../eng-us/features/6.0/right-to-left.md).

![Oberflächensprachen der Administration](../../../../eng-us/help/images/help/bu-rtl.webp)

## Inhalt

1. [Welche Sprachen](#welche-sprachen)
2. [Was von selbst funktioniert](#was-von-selbst-funktioniert)
3. [Was Ihr Design leisten muss](#was-ihr-design-leisten-muss)
4. [Redakteure, die von rechts nach links arbeiten](#redakteure-die-von-rechts-nach-links-arbeiten)
5. [Budget und Zeitplan](#budget-und-zeitplan)
6. [Testen vor dem Start](#testen-vor-dem-start)

## Welche Sprachen

[Sprachen in Exponential, Abschnitt 9](../../../../eng-us/languages.md#9-right-to-left-languages) führt 40 Locale-Dateien mit
Rechts-nach-links-Schrift auf. Darunter:

| Sprache | Locales | Schrift |
|---|---|---|
| Arabisch | 24 Länder-Locales, von `ara-SA` und `ara-EG` bis `ara-MA` und `ara-AE` | Arabisch |
| Hebräisch | `heb-IL` | Hebräisch |
| Jiddisch | `yid-US` | Hebräisch |
| Persisch, Dari | `fas-IR`, `fas-AF` | Arabisch |
| Urdu | `urd-PK`, `urd-IN` | Arabisch |
| Paschtu, Sindhi, Kaschmiri, Uigurisch, Zentralkurdisch, Westpandschabi | je eine Locale oder mehr | Arabisch |
| Syrisch, N'Ko, Dhivehi | `syr-IQ`, `syr-SY`, `nqo-GN`, `div-MV` | Syrisch, N'Ko, Thaana |

Die Schreibrichtung einer Locale ergibt sich aus einer Zeile in ihrer Locale-Datei, `[LanguageSettings]
TextDirection=rtl`; eine Datei ohne diese Zeile ist links nach rechts. Kurdisch (Kurmandschi) und Usbekisch in
lateinischer Schrift werden von links nach rechts geschrieben.

> **Hinweis**
> Die Seite für Website-Ersteller und die Funktionsseite zur Administration sagen, dass 28 Locale-Dateien die
> Einstellung tragen; die Referenzseite [Sprachen in Exponential](../../../../eng-us/languages.md#9-right-to-left-languages), die
> die Locale-Dateien vom 18. Oktober 2026 zählt, führt 40 auf (40 Dateien in `share/locale/` tragen die Zeile).
> Zitieren Sie die Referenzseite.

## Was von selbst funktioniert

| Bereich | Verhalten | Quelle |
|---|---|---|
| Die Administration | Dreht sich um, wenn die Oberflächensprache von rechts nach links läuft: `dir="rtl"`, Seitenspalten getauscht, Text von rechts | [Rechts-nach-links (Administration)](../../../../eng-us/features/6.0/right-to-left.md) |
| Seiten der Administration mit Links-nach-rechts-Schrift | „Byte für Byte dasselbe Markup“ wie vorher | ebenda |
| Inhalte in einer Sprache mit Rechts-nach-links-Schrift | Mit `dir="rtl"` gekennzeichnet, wo sie angezeigt und bearbeitet werden, auch auf einer Seite mit Links-nach-rechts-Schrift | [Sprachen, Abschnitt 9](../../../../eng-us/languages.md#9-right-to-left-languages) |
| Die Online-Editoren | Schalten ihre Schreibrichtung für Texte mit Rechts-nach-links-Schrift um | [Websites mit Rechts-nach-links-Schrift](../../../../eng-us/help/creators/website-creators/right-to-left-sites.md#content-in-a-right-to-left-language) |
| Formate | Datumsangaben, Zahlen und Währung folgen der Locale | [Währungen, Datumsangaben und Formate](waehrungen-datumsangaben-und-formate.md) |

So liest sich eine hebräische Übersetzung innerhalb einer englischen Website richtig, und arabischsprachige Redakteure
können eine gespiegelte Administration verwenden.

## Was Ihr Design leisten muss

Das Design der öffentlichen Website gehört zu Ihrem Projekt: Es „tut, was seine Templates und Stylesheets sagen“
([Websites mit Rechts-nach-links-Schrift](../../../../eng-us/help/creators/website-creators/right-to-left-sites.md)). Für das Designteam
bedeutet das:

- Die Schreibrichtung aus der Locale der Website im Seitenrahmen ausgeben, wie es die Pagelayouts der Administration
  tun.
- Stylesheets mit logischen Eigenschaften schreiben (Anfang und Ende statt links und rechts), sodass „ein Stylesheet
  beide Richtungen bedient; keine `-rtl.css`-Kopie zu pflegen“.
- Symbole spiegeln, die entlang der Zeile zeigen, etwa Pfeile für „Weiter“.
- Menüs prüfen, die auf schmalen Bildschirmen zusammenklappen; die Anleitung merkt an, dass sie „oft als Erstes
  kaputtgehen“.

Wenn Sie mit einem mitgelieferten Theme beginnen, prüfen Sie dessen Stylesheets auf Rechts-nach-links, bevor Sie es
wählen ([Ein Design oder Theme wählen](../../../../eng-us/help/creators/website-creators/choosing-a-design-or-theme.md#decision-checklist)).

## Redakteure, die von rechts nach links arbeiten

Zwei Sprachen mit Rechts-nach-links-Schrift haben eine vollständige Übersetzung der Administration: Hebräisch
(`heb-IL`) und Arabisch (`ara-SA`, modernes Hocharabisch, auf das jedes arabische Land ausweicht). Beide sind
maschinell unterstützt entstanden, und die Referenzseite sagt, dass sie „eine Prüfung durch Muttersprachler brauchen,
bevor sich eine Website darauf verlässt“ ([Sprachen, Abschnitt 9](../../../../eng-us/languages.md#9-right-to-left-languages)).
Redakteure für Persisch, Urdu und die anderen Sprachen mit Rechts-nach-links-Schrift arbeiten in einer englischen (oder
anderen) Administration und schreiben die Inhalte in ihrer Sprache.

## Budget und Zeitplan

Planen Sie diese Punkte ausdrücklich ein; sie werden leicht vergessen:

| Punkt | Warum |
|---|---|
| Designarbeit für Rechts-nach-links | Ihr öffentliches Design, nicht das Produkt, entscheidet, wie die Seiten aussehen |
| Eine Prüfung der Seiten durch Muttersprachler | Gemischter Text (Zahlen, lateinische Produktnamen in arabischem Text) muss ein Leser bestätigen |
| Eine Prüfung der Übersetzung der Administration | Wenn Redakteure auf Hebräisch oder Arabisch arbeiten |
| Schriftarten | Die Schrift muss von den Schriftarten abgedeckt sein, die Ihr Design lädt ([Sprachen, Abschnitt 3](../../../../eng-us/languages.md#3-every-locale) behandelt Schriften und Schriftarten) |
| Übersetzte Rechtstexte | Geprüft von einer für diesen Markt qualifizierten Person |

## Testen vor dem Start

Aus der Checkliste für Website-Ersteller, in unternehmerischen Begriffen:

- [ ] Die Seiten der Website mit Rechts-nach-links-Schrift sind gespiegelt: Spalten, Menüs und Text beginnen rechts.
- [ ] Gemischter Text (Zahlen, lateinische Wörter in arabischem oder hebräischem Text) liest sich richtig.
- [ ] Formulare (Kasse, Registrierung, Kontakt) richten Beschriftungen, Eingabefelder und Fehlermeldungen an der
      Anfangsseite aus.
- [ ] Symbole, die entlang der Zeile zeigen, zeigen in die richtige Richtung.
- [ ] Die Websites mit Links-nach-rechts-Schrift sehen genau so aus wie vorher.
- [ ] Alles wurde auch in Telefonbreite geprüft.
- [ ] Ein muttersprachlicher Leser hat die Inhalte und die Kasse abgenommen.

## Verwandte Seiten

- [Sprachen und Locales](sprachen-und-locales.md)
- [Checkliste Mehrsprachigkeit](checkliste-mehrsprachigkeit.md)
- [Websites mit Rechts-nach-links-Schrift](../../../../eng-us/help/creators/website-creators/right-to-left-sites.md)
- [Sprachen mit Rechts-nach-links-Schrift in der Administration](../../../../eng-us/features/6.0/right-to-left.md)
- [Sprachen in Exponential](../../../../eng-us/languages.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
