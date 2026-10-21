---
title: Mehrsprachigkeit planen
audience: businesses, project-manager, sales-marketing
category: business-solutions
order: 10
summary: Die Entscheidungen, die ein Unternehmen trifft, bevor es mit Exponential in mehreren Sprachen veröffentlicht – welche Sprachen und Märkte, Adressen je Sprache, Ausweichsprachen für nicht übersetzte Inhalte, wer übersetzt und welche Grenzen Sie kennen sollten.
keywords: mehrsprachig, Planung, Sprachen, Märkte, Ausweichsprache, nicht übersetzt, Übersetzer, Adressen je Sprache, Grenze von 62 Sprachen, Unternehmen, multilingual, planning, languages, markets, SiteLanguageList, fallback, untranslated, translators, addresses per language, hreflang, limit of 62 languages, business
type: help
---
# Mehrsprachigkeit planen

Diese Seite richtet sich an Inhaber, Marketingverantwortliche und Projektmanager, die eine Website oder einen Shop in
mehr als einer Sprache planen. Sie führt die Entscheidungen auf, die vor dem Aufbau zu treffen sind, erklärt jede in
unternehmerischen Begriffen und verweist auf die technische Seite für das Team, das sie umsetzt. Der ausführlichere
Planungsleitfaden für Projektmanager ist
[Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md).

## Inhalt

1. [Gehen Sie von Märkten aus, nicht von Sprachen](#gehen-sie-von-märkten-aus-nicht-von-sprachen)
2. [Wie Exponential Sprachen anzeigt](#wie-exponential-sprachen-anzeigt)
3. [Adressen je Sprache](#adressen-je-sprache)
4. [Was Besucher sehen, wenn eine Übersetzung fehlt](#was-besucher-sehen-wenn-eine-übersetzung-fehlt)
5. [Wer übersetzt und wer freigibt](#wer-übersetzt-und-wer-freigibt)
6. [Grenzen, die Sie kennen sollten, bevor Sie etwas zusagen](#grenzen-die-sie-kennen-sollten-bevor-sie-etwas-zusagen)
7. [Die Entscheidungstabelle](#die-entscheidungstabelle)

## Gehen Sie von Märkten aus, nicht von Sprachen

Eine Sprache verursacht wiederkehrende Kosten: Jede Seite, die Sie später veröffentlichen, braucht eine Übersetzung,
jede Korrektur ebenso. Bevor Sie eine hinzufügen, halten Sie für jeden Markt fest:

- [ ] Welche Kunden oder Mitglieder er erreicht und was Sie von ihnen erwarten.
- [ ] Welche Inhalte sie brauchen: die ganze Website, den Shop oder nur die wichtigsten Seiten (Produkte, Kontakt,
      AGB).
- [ ] Wer die Sprache schreibt und prüft: Mitarbeitende, eine Agentur, ein Partner oder ein maschineller Entwurf mit
      menschlicher Prüfung.
- [ ] Ob sich Preise, Steuern, Versand und Rechtstexte in diesem Markt unterscheiden (siehe
      [Währungen, Datumsangaben und Formate](waehrungen-datumsangaben-und-formate.md) und den Bereich
      [Online-Shops und kleine Geschäfte](../shops/README.md)).

Eine kleinere Zahl gut gepflegter Sprachen dient Kunden meist besser als viele halb übersetzte.

## Wie Exponential Sprachen anzeigt

Aus [Inhaltssprachen](../../../anleitungen/inhaltssprachen.md):

- **Eine hinzugefügte Sprache übersetzt nichts und ändert keine Website-Adresse.** Redakteure übersetzen Inhalte; ein
  Administrator fügt die Sprache der Sprachliste (`SiteLanguageList`) jedes Siteaccess hinzu, der sie zeigen soll.
- **Jeder Siteaccess zeigt ein Objekt in der ersten Sprache seiner Liste, die das Objekt hat.**
- **Jedes Objekt hat eine Hauptsprache**, die, in der es angelegt oder die später gewählt wurde.
- **Eine Übersetzung zu entfernen löscht Inhalte endgültig**; eine ganze Sprache lässt sich nur entfernen, wenn nichts
  sie verwendet, deshalb löscht das nie Inhalte.

Die meisten mehrsprachigen Websites haben deshalb einen öffentlichen Siteaccess je Sprache, die alle eine Datenbank
und einen Inhaltsbaum teilen ([Eine mehrsprachige Website aufbauen](../../ersteller/website-ersteller/mehrsprachige-websites.md)).

## Adressen je Sprache

| Form | Beispiel | Geeignet für |
|---|---|---|
| Pfad je Sprache | `example.com/de/` | Ein Zertifikat, ein Host; am einfachsten |
| Host je Sprache | `example.de`, `de.example.com` | Länderdomains |
| Host und Pfad | `example.com/de` auf einem Host, andere Hosts anderswo | Gemischte Einrichtungen |

Aus [Sprachumschalter und Adressen je Sprache](../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md#adressen-pro-sprache).
Die Startseite kann außerdem einmalig auf die Sprache des Besuchers weiterleiten, gewählt nach Host und
Browsersprache, mit einer Adresse je Sprache und ohne Cookie
([Siteaccess nach Host-Präfix und die Weiterleitung zur Sprache](../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md)).

> **Hinweis**
> Suchmaschinen verstehen `hreflang`-Links zwischen Sprachversionen. Exponential gibt sie nicht von sich aus aus; das
> Design erzeugt sie aus dem Ergebnis des Sprachumschalters
> ([Suchmaschinen informieren](../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md#suchmaschinen-informieren)).
> Setzen Sie das auf die Aufgabenliste des Designteams.

## Was Besucher sehen, wenn eine Übersetzung fehlt

Entscheiden Sie das je Sprach-Siteaccess:

| Sie möchten | Einstellung (für das technische Team) |
|---|---|
| Englisch zeigen, wo Deutsch fehlt | Deutsch zuerst, dann Englisch in der Sprachliste |
| Verbergen, was nicht übersetzt ist | Nur Deutsch in der Liste, nicht übersetzte Objekte werden nicht gezeigt |
| Alles in seiner Hauptsprache zeigen, wenn nichts anderes passt | `ShowUntranslatedObjects=enabled` |
| Ein Logo oder ein PDF erscheint in jeder Sprache unverändert | Das Objekt als „immer verfügbar“ markieren |

Quelle: [Ausweichsprachen und nicht übersetzte Inhalte](../../ersteller/website-ersteller/mehrsprachige-websites.md#fallbacks-und-nicht-übersetzte-inhalte).
Bei einem Shop kann das Verbergen nicht übersetzter Produkte Dinge verbergen, die Kunden kaufen möchten; sie in einer
anderen Sprache zu zeigen, kann verwirren. Entscheiden Sie notfalls je Produktkategorie.

## Wer übersetzt und wer freigibt

- Übersetzer lassen sich auf ihre Sprache beschränken: Die Richtlinien zum Bearbeiten, Übersetzen und Anlegen von
  Inhalten lassen sich im Rolleneditor jeweils nach **Sprache** beschränken
  ([7. Inhalte im Editor übersetzen](../../../anleitungen/inhaltssprachen.md#7-inhalte-im-editor-übersetzen)).
- Ein Freigabe-Workflow kann Übersetzungen zurückhalten, bis ein Sprachverantwortlicher sie freigibt
  ([Übersetzungsablauf](uebersetzungsablauf.md)).
- Ein Sprachmodell kann Entwürfe vorbereiten, die ein Mensch prüft
  ([Unterstützte Übersetzung mit Exponential Intelligence](unterstuetzte-uebersetzung-mit-exponential-intelligence.md)).

## Grenzen, die Sie kennen sollten, bevor Sie etwas zusagen

| Grenze | Wert | Quelle |
|---|---|---|
| Inhaltssprachen je Installation | höchstens 62 unter 64-Bit-PHP, 30 unter 32-Bit-PHP | [Sprachen, Abschnitt 11](../../../sprachen.md#11-die-grenze-von-62-inhaltssprachen) |
| Eine Sprache, die hinzugefügt und nie verwendet wurde | belegt ihren Platz trotzdem, bis sie entfernt wird | ebenda |
| Administration in der eigenen Sprache des Redakteurs | vollständig bei 19 Übersetzungen; teilweise oder Englisch bei den übrigen | [Sprachen, Abschnitt 2](../../../sprachen.md#2-unterstützungsstufen) |
| Die Standard-Inhaltssprache einer Website mit Inhalten ändern | kann Objekte verbergen, die es in der neuen Sprache nicht gibt | [10.14 Mehrsprachige Websites](../../../installation/10-nach-der-installation.md#1014-mehrsprachige-sites) |

## Die Entscheidungstabelle

```text
Sprachplan für <Organisation>                                  <Datum>

| Markt | Sprache (Locale) | Adresse | Ausweichsprache | Nicht übersetzt: verbergen/zeigen | Übersetzer | Freigebender |
|-------|------------------|---------|-----------------|-----------------------------------|------------|--------------|
|       |                  |         |                 |                                   |            |              |

Inhalte im Umfang je Sprache: <ganze Website / Shop / wichtigste Seiten>
Sprachen mit Rechts-nach-links-Schrift: <keine / Liste>
Maschinell unterstützte Entwürfe: <ja, geprüft von ... / nein>
Genutzte Inhaltssprachen: <n> von höchstens 62
```

## Verwandte Seiten

- [Sprachen und Locales](sprachen-und-locales.md)
- [Übersetzungsablauf](uebersetzungsablauf.md)
- [Checkliste Mehrsprachigkeit](checkliste-mehrsprachigkeit.md)
- [Mehrere Marken planen](../multisite/mehrere-marken-planen.md)
- [Multisite- und mehrsprachige Projekte planen](../../fuehrung/multisite-und-mehrsprachigkeit-planen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
