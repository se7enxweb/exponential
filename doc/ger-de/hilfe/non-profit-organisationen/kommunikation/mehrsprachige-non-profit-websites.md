---
title: Mehrsprachige Non-Profit-Websites
audience: non-profit, government, editor, website-creator
category: non-profits
order: 40
summary: Wie eine Non-Profit-Organisation mit einem kleinen Team Menschen in mehreren Sprachen auf Exponential erreicht - entscheiden, welche Inhalte zuerst übersetzt werden, Inhaltssprachen und ein Siteaccess pro Sprache, Ersatzregeln für nicht übersetzte Seiten, der Sprachumschalter, ehrenamtliche Übersetzer mit Einschränkung auf ihre Sprache, Übersetzungsentwürfe von Exponential Intelligence mit Prüfung durch einen Menschen, Schriften von rechts nach links, die Grenze von 62 Sprachen und einfache Sprache.
keywords: mehrsprachig, Sprachen, Übersetzung, Inhaltssprachen, Siteaccess, Ersatzsprache, Sprachumschalter, Übersetzer, ehrenamtliche Übersetzer, rechts nach links, 62 Sprachen, einfache Sprache, Community-Sprachen, multilingual, languages, translation, content languages, SiteLanguageList, fallback, language switcher, translators, volunteer translators, Exponential Intelligence, right-to-left, plain language, community languages
type: help
---
# Mehrsprachige Non-Profit-Websites

Diese Seite richtet sich an Mitarbeitende in der Kommunikation und an den Website-Ersteller. Viele
Non-Profit-Organisationen arbeiten für Menschen, deren Erstsprache nicht die Hauptsprache des Landes ist: Geflüchtete,
migrantische Gemeinschaften, Sprecher von Minderheiten- oder indigenen Sprachen, internationale Unterstützer.
Exponential bietet „Advanced multi-lingual support“, also erweiterte Mehrsprachigkeit (`README.md`, „Main Exponential
features“); die Arbeit besteht vor allem darin, zu entscheiden, was übersetzt wird und wer es aktuell hält.

## Inhalt

1. [Entscheiden, was zuerst übersetzt wird](#entscheiden-was-zuerst-übersetzt-wird)
2. [Wie Sprachen in Exponential funktionieren](#wie-sprachen-in-exponential-funktionieren)
3. [Nicht übersetzte Seiten](#nicht-übersetzte-seiten)
4. [Der Sprachumschalter](#der-sprachumschalter)
5. [Übersetzer, oft Ehrenamtliche](#übersetzer-oft-ehrenamtliche)
6. [Übersetzungsentwürfe mit Exponential Intelligence](#übersetzungsentwürfe-mit-exponential-intelligence)
7. [Rechts-nach-links und andere Schriften](#rechts-nach-links-und-andere-schriften)
8. [Grenzen](#grenzen)
9. [Checkliste](#checkliste)

## Entscheiden, was zuerst übersetzt wird

„Das Hinzufügen einer Sprache übersetzt nichts und zeigt von sich aus nichts an“
([Eine Sprache hinzufügen](../../kunden/wachstum/eine-sprache-hinzufuegen.md)). Übersetzen Sie in dieser Reihenfolge:

| Priorität | Inhalt | Warum |
|---|---|---|
| 1 | Wie man Hilfe bekommt: Angebote, Kontakt, Öffnungszeiten, Notfallinformationen | Die Menschen, für die Sie da sind, brauchen das am dringendsten |
| 2 | Formulare: das Formular für Hilfeanfragen oder für Ehrenamtliche, mit Beschriftungen und Meldungen | Sonst können sie nicht handeln |
| 3 | Wer Sie sind, Datenschutzerklärung | Vertrauen und rechtliche Grundlagen |
| 4 | Spendenseite | Wenn Sie in dieser Gemeinschaft Spenden sammeln |
| 5 | Nachrichten und Veranstaltungen | Soweit die Kapazität es zulässt |

Ein kurzer übersetzter Bereich, der aktuell ist, ist besser als eine vollständige Übersetzung, die zwei Jahre alt ist.

## Wie Sprachen in Exponential funktionieren

- Jedes Objekt kann eine Übersetzung pro Inhaltssprache haben.
- Ein Siteaccess pro Sprache legt fest, welche Sprachen ein Besucher in welcher Reihenfolge sieht (`SiteLanguageList`).
- Die Administrationsoberfläche hat eigene Sprachen, getrennt von den Inhaltssprachen.

Quellen: [Mehrsprachige Websites](../../ersteller/website-ersteller/mehrsprachige-websites.md),
[Inhaltssprachen und Oberflächensprachen](../../verwaltung/sprachen/inhaltssprachen-und-oberflaechensprachen.md),
[Ein Siteaccess pro Sprache](../../verwaltung/sprachen/ein-siteaccess-pro-sprache.md).

## Nicht übersetzte Seiten

Ist eine Seite noch nicht übersetzt, entscheiden die Reihenfolge in `SiteLanguageList`, `ShowUntranslatedObjects` und
„immer verfügbar“, was der Besucher sieht ([Ersatzregeln für nicht übersetzte Inhalte](../../verwaltung/sprachen/ersatzregeln-fuer-unuebersetzte-inhalte.md)).
Für eine Non-Profit-Organisation ist die übliche Wahl: lieber die Seite in der Hauptsprache zeigen als gar nichts,
mit einem Hinweis in der Sprache des Besuchers, dass die Seite noch nicht übersetzt ist.

## Der Sprachumschalter

Setzen Sie auf jede Seite einen Umschalter, der jede Sprache mit ihrem eigenen Namen bezeichnet („Español“, nicht
„Spanisch“). Die mitgelieferten Designs geben keine `hreflang`-Links aus; Ihr Website-Ersteller ergänzt sie bei Bedarf
([Der Sprachumschalter](../../verwaltung/sprachen/der-sprachumschalter.md)).

## Übersetzer, oft Ehrenamtliche

- Geben Sie jedem Übersetzer eine Rolle, die auf seine Sprache beschränkt ist: Die Richtlinien zum Bearbeiten und
  Übersetzen lassen sich nach Sprache einschränken ([Rollen und Berechtigungen für Übersetzer](../../verwaltung/sprachen/rollen-und-rechte-fuer-uebersetzer.md)).
- Lassen Sie Übersetzungen sensibler Seiten (Angebote, Rechtliches) in einem Freigabeschritt von einer zweiten
  sprachkundigen Person prüfen.
- Führen Sie in jeder Sprache ein kurzes Glossar Ihrer Begriffe ([Glossare von Begriffen](../../verwaltung/sprachen/begriffsglossare.md)).
- Eine feste Routine hält die Übersetzungen im Gleichtakt: [Übersetzungsroutine](../../kunden/alltag/uebersetzungsroutine.md).

## Übersetzungsentwürfe mit Exponential Intelligence

Exponential Intelligence kann Übersetzungsentwürfe erstellen, die ein Mensch prüft; eine Übersetzungssynchronisierung
hält Übersetzungen mit geänderten Ausgangstexten im Gleichtakt; der Assistent veröffentlicht nichts
([Unterstützte Übersetzung mit Exponential Intelligence](../../unternehmen/mehrsprachig/unterstuetzte-uebersetzung-mit-exponential-intelligence.md)).
Dafür ist ein Anbieter nötig, der Geld kosten kann und den Text erhält; entscheiden Sie das zuerst
([Exponential Intelligence](../../kunden/wachstum/exponential-intelligence.md)). Bei Informationen, auf die Menschen
für ihre Sicherheit oder ihre Rechte angewiesen sind, lassen Sie den Entwurf immer von einer Person prüfen, die die
Sprache fließend beherrscht.

## Rechts-nach-links und andere Schriften

Arabisch, Hebräisch, Persisch, Urdu und andere von rechts nach links geschriebene Sprachen werden auf öffentlichen
Websites und in der Administrationsoberfläche unterstützt ([Websites mit Rechts-nach-links-Schrift](../../ersteller/website-ersteller/websites-von-rechts-nach-links.md),
[Rechts-nach-links-Sprachen auf öffentlichen Websites](../../verwaltung/sprachen/rechts-nach-links-sprachen-auf-oeffentlichen-websites.md)).
Wird ein benötigtes Locale nicht mitgeliefert, lässt sich ein eigenes hinzufügen
([Eigene Locales für nicht aufgeführte Sprachen](../../verwaltung/sprachen/eigene-locales-fuer-nicht-gelistete-sprachen.md),
[Minderheiten- und Regionalsprachen](../../verwaltung/sprachen/minderheiten-und-regionalsprachen.md)).

## Grenzen

- Höchstens 62 Inhaltssprachen pro Installation unter 64-Bit-PHP ([Die Grenze von 62 Sprachen](../../verwaltung/sprachen/die-grenze-von-62-sprachen.md)).
  Kaum eine Non-Profit-Organisation kommt in die Nähe.
- Jede Sprache vervielfacht den Bearbeitungsaufwand. Planen Sie Zeit für Übersetzungen im Budget ein ([Budgetposten](../foerdermittel/budgetposten-fuer-hosting-und-wartung.md)).

Schreiben Sie in jeder Sprache klar; einfache Sprache hilft allen, die in einer Zweitsprache lesen
([Einfache Sprache](../../verwaltung/sprachen/einfache-sprache.md)).

## Checkliste

- [ ] Sprachen gemeinsam mit den Gemeinschaften gewählt, für die Sie da sind.
- [ ] Vorrangige Inhalte zuerst übersetzt, Formulare eingeschlossen.
- [ ] Ersatzverhalten festgelegt und getestet.
- [ ] Sprachumschalter auf jeder Seite.
- [ ] Übersetzer mit auf ihre Sprache beschränkten Rollen; sensible Seiten von einer zweiten sprachkundigen Person
  geprüft.
- [ ] Maschinelle Entwürfe immer von einem Menschen geprüft.
- [ ] Mehrsprachigkeitstest durchgeführt ([Eine mehrsprachige Website testen](../../verwaltung/sprachen/eine-mehrsprachige-website-testen.md)).

## Verwandte Seiten

- [Kommunikation für Non-Profit-Organisationen](README.md)
- [Mehrsprachige Unternehmen](../../unternehmen/mehrsprachig/README.md)
- [Sprachen im Überblick](../../verwaltung/sprachen/sprachen-auf-einen-blick.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
