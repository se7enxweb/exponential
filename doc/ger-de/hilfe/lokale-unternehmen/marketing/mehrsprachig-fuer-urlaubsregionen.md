---
title: Mehrsprachig für Urlaubsregionen
audience: local-business, businesses, customer
category: local-business
order: 64
summary: Eine zweite oder dritte Sprache für ein lokales Unternehmen in einer Urlaubsregion - welche Seiten Sie zuerst übersetzen, wie Exponential Sprachen zeigt und jeder eigene Adressen gibt, was passiert, wenn eine Übersetzung fehlt, wer übersetzt, Hilfe von Exponential Intelligence für Entwürfe und wie Sie Öffnungszeiten und Hinweise in jeder Sprache aktuell halten.
keywords: mehrsprachig, Sprachen, Übersetzung, Touristen, Gäste, zweite Sprache, Sprachumschalter, Siteaccess, Sprachadressen, Exponential Intelligence, lokales Unternehmen, multilingual, languages, translation, tourists, visitors, second language, language switcher, language addresses, local business
type: help
---
# Mehrsprachig für Urlaubsregionen

Diese Seite ist für lokale Unternehmen, bei denen viele Kunden die Landessprache nicht sprechen: ein Café in einem
Ferienort, ein Fahrradverleih am See, eine Apotheke in Bahnhofsnähe. Exponential hat eine weitreichende Unterstützung
für Mehrsprachigkeit eingebaut: Jede Seite kann Übersetzungen haben, und jede Sprache bekommt eigene Adressen
([Mehrsprachige Unternehmen](../../unternehmen/mehrsprachig/README.md)).

## Inhalt

1. [Wenige Seiten gut übersetzen](#wenige-seiten-gut-übersetzen)
2. [Wie Sprachen in Exponential funktionieren](#wie-sprachen-in-exponential-funktionieren)
3. [Fehlende Übersetzungen](#fehlende-übersetzungen)
4. [Wer übersetzt](#wer-übersetzt)
5. [Sprachen im Einklang halten](#sprachen-im-einklang-halten)
6. [Checkliste](#checkliste)

## Wenige Seiten gut übersetzen

Gäste aus dem Ausland brauchen das Wesentliche, nicht Ihre Firmengeschichte. Übersetzen Sie zuerst:

| Priorität | Seite |
|---|---|
| 1 | Startseite: was Sie sind, wo, wann geöffnet |
| 1 | Öffnungszeiten und Standort, mit Anfahrt |
| 1 | Speisekarte oder Preisliste |
| 2 | Kontaktseite und Beschriftungen des Formulars |
| 2 | wie man bucht oder bestellt (auch wenn es ein Link zu einem externen Dienst ist) |
| 3 | über uns, Neuigkeiten |

Wählen Sie die Sprachen, die Ihre Gäste tatsächlich sprechen; fragen Sie ein paar Wochen lang an der Theke nach. Eine
gut gepflegte zusätzliche Sprache ist besser als drei vernachlässigte.

## Wie Sprachen in Exponential funktionieren

Aus [Mehrsprachigkeit planen](../../unternehmen/mehrsprachig/mehrsprachigkeit-planen.md) und
[Mehrsprachige Websites](../../ersteller/website-ersteller/mehrsprachige-websites.md):

- Ihr Ersteller fügt die Sprachen und einen **Siteaccess** pro Sprache hinzu, zum Beispiel `example.com/en/...` und
  `example.com/de/...`, oder einen Host pro Sprache
  ([Sprachumschalter und Adressen pro Sprache](../../ersteller/website-ersteller/sprachumschalter-und-sprach-urls.md)).
- Ein **Sprachumschalter** in der Kopfzeile lässt Besucher auf derselben Seite die Sprache wechseln.
- Übersetzungen einer Seite sind Versionen desselben Objekts: Fotos, Preise und Kartenpunkt werden geteilt, die Texte
  werden übersetzt.
- Jede Sprache kann ihre eigene Sitemap haben (`googlesitemapsmultilingual`,
  [Sitemaps und robots.txt](../gefunden-werden/sitemaps-und-robots-txt.md)).

Eine Sprache später hinzuzufügen, ist Routinearbeit ([Eine Sprache hinzufügen](../../kunden/wachstum/eine-sprache-hinzufuegen.md)).

## Fehlende Übersetzungen

Was Besucher sehen, wenn eine Seite nicht übersetzt ist, hängt von den Einstellungen ab: die Seite in der Hauptsprache
oder nichts ([Was Besucher sehen, wenn eine Übersetzung fehlt](../../unternehmen/mehrsprachig/mehrsprachigkeit-planen.md#was-besucher-sehen-wenn-eine-übersetzung-fehlt)).
Für eine kleine Website ist es freundlicher, die Hauptsprache zu zeigen, als eine fehlende Seite. Entscheiden Sie das
mit Ihrem Ersteller.

## Wer übersetzt

| Möglichkeit | Hinweis |
|---|---|
| Eine Mitarbeiterin oder ein Mitarbeiter, die oder der die Sprache spricht | am besten für den Ton; braucht Zeit |
| Ein professioneller Übersetzer | für die Hauptseiten, einmalig |
| Exponential Intelligence | der in die Administration eingebaute Schreibassistent schreibt **Entwürfe** von Übersetzungen, die eine Person prüft und veröffentlicht ([Exponential Intelligence](../../kunden/wachstum/exponential-intelligence.md), [Unterstützte Übersetzung](../../unternehmen/mehrsprachig/unterstuetzte-uebersetzung-mit-exponential-intelligence.md)) |

Exponential Intelligence verwendet einen externen KI-Anbieter, den Ihr Administrator gewählt hat; es schreibt nur
Entwürfe und veröffentlicht nie selbst ([KI-Entwürfe verantwortungsvoll nutzen](../../kunden/wachstum/ki-entwuerfe-verantwortungsvoll-nutzen.md)).
Lassen Sie jemanden, der die Sprache kennt, Speisekarte und Öffnungszeiten vor dem Veröffentlichen lesen; ein falsches
Wort auf einer Speisekarte oder bei einem Allergiehinweis zählt.

## Sprachen im Einklang halten

Das Risiko einer mehrsprachigen Website ist, dass die anderen Sprachen zurückfallen. Insbesondere:

- **Öffnungszeiten und Feiertagshinweise**: Ändern Sie sie am selben Tag in jeder Sprache. Eine strukturierte Klasse
  für Öffnungszeiten ([Öffnungszeiten ohne besonderen Datentyp](../karten-und-oeffnungszeiten/oeffnungszeiten-ohne-besonderen-datentyp.md#möglichkeit-2-eine-kleine-klasse-für-öffnungszeiten))
  hilft, weil die Zeiten geteilt werden und nur die Namen der Tage und die Hinweise übersetzt werden.
- **Preise**: in einem Preisfeld gepflegt, das alle Sprachen teilen.
- **Angebote**: Übersetzen Sie sie, oder lassen Sie sie nur in der Hauptsprache; lassen Sie kein drei Monate altes
  Angebot in einer Sprache stehen.

Eine Routine: [Übersetzungsroutine](../../kunden/alltag/uebersetzungsroutine.md).

## Checkliste

- [ ] Sprachen anhand echter Fragen von Gästen gewählt.
- [ ] Seiten der Priorität 1 übersetzt und von jemandem geprüft, der die Sprache spricht.
- [ ] Sprachumschalter auf Smartphones sichtbar.
- [ ] Verhalten bei fehlenden Übersetzungen entschieden.
- [ ] Feiertagshinweise in jeder Sprache geschrieben.
- [ ] Unternehmenseinträge zeigen den richtigen Link pro Sprache, wenn die Plattform es zulässt.

## Verwandte Seiten

- [Checkliste für Mehrsprachigkeit](../../unternehmen/mehrsprachig/checkliste-mehrsprachigkeit.md)
- [Währungen, Datumsangaben und Formate](../../unternehmen/mehrsprachig/waehrungen-datumsangaben-und-formate.md)
- [Sprachen](../../sprachen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
