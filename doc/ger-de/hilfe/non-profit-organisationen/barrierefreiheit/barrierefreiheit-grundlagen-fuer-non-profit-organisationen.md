---
title: Grundlagen der Barrierefreiheit für Non-Profit-Organisationen
audience: non-profit, editor, website-creator, government
category: non-profits
order: 20
summary: Das Wesentliche zur Barrierefreiheit für ein kleines Team einer Non-Profit-Organisation, das eine Website mit Exponential betreibt, nach Priorität geordnet - Templates mit Landmarks, Sprunglink, Fokus und Kontrast; Alternativtext, Überschriften und Linktexte in Inhalten; barrierefreie Formulare und CAPTCHAs; einfache Sprache; Tests mit der Tastatur und einem automatischen Prüfwerkzeug; und eine kleine Routine, die die Website barrierefrei hält.
keywords: Grundlagen der Barrierefreiheit, Prioritäten, kleines Team, Templates, Landmarks, Sprunglink, Fokus, Kontrast, Alternativtext, Überschriften, Linktext, Formulare, CAPTCHA, einfache Sprache, Tastaturtest, axe, Routine, accessibility basics, skip link, plain language, keyboard testing
type: help
---
# Grundlagen der Barrierefreiheit für Non-Profit-Organisationen

Diese Seite richtet sich an ein kleines Team, das nicht alles auf einmal erledigen kann. Sie bringt die Arbeit in eine
Reihenfolge, die die größten Barrieren zuerst beseitigt. Sie ist keine Konformitäts-Checkliste; dafür siehe
[Zuordnung zu WCAG 2.2](../../verwaltung/barrierefreiheit/wcag-2-2-zuordnung.md).

## Inhalt

1. [Priorität 1: die Templates](#priorität-1-die-templates)
2. [Priorität 2: Inhalte](#priorität-2-inhalte)
3. [Priorität 3: Formulare](#priorität-3-formulare)
4. [Priorität 4: Sprache](#priorität-4-sprache)
5. [Testen ohne Budget](#testen-ohne-budget)
6. [Eine kleine Routine](#eine-kleine-routine)
7. [Checkliste der Grundlagen](#checkliste-der-grundlagen)

## Priorität 1: die Templates

Einmal korrigiert, helfen sie auf jeder Seite. Der Website-Ersteller prüft Folgendes
([Barrierefreiheit für Website-Ersteller](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md#was-der-template-autor-steuert)):

| Bereich | So sieht es gut aus |
|---|---|
| Sprache | `<html lang="...">` gesetzt; andere Sprachen ausgezeichnet |
| Landmarks | Kopfbereich, Navigation, Hauptinhalt, Fußbereich |
| Sprunglink | „Zum Inhalt springen“ als Erstes auf der Seite |
| Überschriften | Ein `h1` pro Seite, Überschriften in der richtigen Reihenfolge |
| Fokus | Ein sichtbarer Fokusstil an jedem Link und jedem Bedienelement |
| Kontrast | Text mindestens 4,5:1, großer Text 3:1 |
| Zoom | Funktioniert bei 200 % und auf schmalen Bildschirmen ohne seitliches Scrollen |
| Bewegung | Slider und Animationen beachten `prefers-reduced-motion` |

Wenn Ihre Website ein mitgeliefertes Design verwendet, testen Sie es, bevor Sie davon ausgehen, dass es die
Anforderungen erfüllt: Die öffentlichen Designs sind „nicht geprüft“
([Was Exponential bereitstellt](../../verwaltung/barrierefreiheit/was-exponential-bietet.md#software-gegenüber-website)).

## Priorität 2: Inhalte

Darüber entscheiden die Redakteure jeden Tag ([Checkliste Barrierefreiheit für Redakteure](../../kunden/alltag/checkliste-barrierefreiheit-fuer-redakteure.md)):

- **Alternativtext** für jedes bedeutungstragende Bild; dekorative Bilder bleiben leer
  ([Alternativtext für Bilder](../../verwaltung/barrierefreiheit/alternativtext-fuer-bilder.md)).
  Bildfelder können so eingestellt werden, dass sie ihn verlangen.
- **Überschriften** als Überschriften verwendet, in der richtigen Reihenfolge, nicht wegen der Schriftgröße
  ([Überschriften und Struktur](../../verwaltung/barrierefreiheit/ueberschriften-und-struktur.md)).
- **Linktexte**, die sagen, wohin sie führen ([Linktext](../../verwaltung/barrierefreiheit/linktext.md)).
- **Tabellen** nur für Daten, mit Kopfzeilen ([Tabellen in Inhalten](../../verwaltung/barrierefreiheit/tabellen-in-inhalten.md)).
- **Dokumente** barrierefrei oder mit einer HTML-Fassung ([Barrierefreie Kampagnen und Dokumente](barrierefreie-kampagnen-und-dokumente.md)).

## Priorität 3: Formulare

Ihr Formular für Ehrenamtliche, Ihr Kontaktformular und die Newsletter-Anmeldung sind die Stellen, an denen Menschen
handeln. Aus [Barrierefreiheit für Website-Ersteller, Formulare](../../ersteller/website-ersteller/barrierefreiheit-fuer-website-ersteller.md#formulare):

- Jedes Eingabefeld hat eine sichtbare Beschriftung;
- Pflichtfelder sind im Text gekennzeichnet;
- Fehler werden neben dem Feld angezeigt und oben zusammengefasst;
- CAPTCHAs haben eine barrierefreie Alternative (beide mitgelieferten Dienste bieten eine an).

Bieten Sie außerdem einen anderen Weg an, Sie zu erreichen (E-Mail, Telefon). Siehe
[Barrierefreie Formulare](../../verwaltung/barrierefreiheit/barrierefreie-formulare.md).

## Priorität 4: Sprache

Einfache Sprache hilft Menschen mit kognitiven Beeinträchtigungen, Menschen, die in einer Zweitsprache lesen, und allen,
die es eilig haben ([Einfache Sprache](../../verwaltung/sprachen/einfache-sprache.md),
[Verständlich schreiben](../../verwaltung/sprachen/verstaendlich-schreiben.md)). Zeichnen Sie Passagen in einer
anderen Sprache aus ([Sprache der Seite und von Teilen](../../verwaltung/barrierefreiheit/sprache-der-seite-und-von-teilen.md)).

## Testen ohne Budget

| Test | Zeit | Wie |
|---|---|---|
| Nur Tastatur | 15 Minuten pro wichtiger Seite | Mit der Tabulatortaste durchgehen; erreichen und bedienen Sie alles? Ist der Fokus sichtbar? |
| Automatisches Prüfwerkzeug | 5 Minuten pro Seite | Eine Browsererweiterung auf Basis von axe ([Testen mit axe und der Tastatur](../../verwaltung/barrierefreiheit/mit-axe-und-der-tastatur-testen.md)) |
| Zoom auf 200 % | 5 Minuten | Nichts abgeschnitten, kein seitliches Scrollen |
| Nutzer fragen | Laufend | Ein Link für Rückmeldungen auf jeder Seite; Menschen, denen Sie dienen, als Tester |

Automatische Werkzeuge finden nur einen Teil der Probleme; der Tastaturtest und echte Nutzer finden mehr
([Barrierefreiheit testen](../../ersteller/website-ersteller/barrierefreiheit-testen.md)).

Wichtige Seiten, die Sie zuerst testen: Startseite, Spendenseite, Formular für Ehrenamtliche, Newsletter-Anmeldung,
Kontakt, ein Nachrichtenartikel.

## Eine kleine Routine

| Wann | Was |
|---|---|
| Bei jeder Veröffentlichung | Checkliste der Redakteure (Alternativtext, Überschriften, Links) |
| Jeden Monat | Tastaturtest einer wichtigen Seite |
| Bei jeder Kampagne | Die Kampagnenseite vor dem Start testen |
| Jedes Jahr | Alle wichtigen Seiten testen; die Erklärung aktualisieren ([Erklärung zur Barrierefreiheit](erklaerung-zur-barrierefreiheit-fuer-non-profit-organisationen.md)) |

Siehe auch [Barrierefreiheit pflegen](../../kunden/alltag/barrierefreiheit-pflegen.md).

## Checkliste der Grundlagen

- [ ] Templates anhand der Tabelle in Priorität 1 geprüft.
- [ ] Redakteure zu Alternativtext, Überschriften und Linktexten geschult.
- [ ] Formulare beschriftet, Fehler verständlich, CAPTCHA-Alternative getestet.
- [ ] Wichtige Seiten mit der Tastatur und einem automatischen Prüfwerkzeug getestet.
- [ ] Ein Weg für Rückmeldungen auf jeder Seite.
- [ ] Ein jährlicher Test im Kalender.

## Verwandte Seiten

- [Barrierefreiheit für Non-Profit-Organisationen](README.md)
- [Barrierefreie Inhalte für Redakteure](../../verwaltung/barrierefreiheit/barrierefreie-inhalte-fuer-die-redaktion.md)
- [Farbe und Kontrast](../../verwaltung/barrierefreiheit/farbe-und-kontrast.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
