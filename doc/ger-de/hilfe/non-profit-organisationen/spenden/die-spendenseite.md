---
title: Die Spendenseite
audience: non-profit, businesses, editor
category: non-profits
order: 50
summary: Was die Spendenseite einer Non-Profit-Organisation auf einer Exponential-Site sagen und zeigen sollte - Zweck, ein konkretes Beispiel, die Schaltfläche im sichtbaren Bereich und am Ende, wer Sie sind, wohin das Geld geht mit einem Link zu den Jahresveröffentlichungen, andere Wege zu spenden, Datenschutz und Barrierefreiheit - mit einer Seitengliederung, Schreibtipps und einer Checkliste für Redakteure.
keywords: Spendenseite, Spenden-Seite, Fundraising-Texte, Handlungsaufforderung, Schaltfläche, Vertrauen, Jahresveröffentlichungen, andere Wege zu spenden, Überweisung, Barrierefreiheit, Redakteur, giving page, donate page, donation page, fundraising copy, call to action, button, trust, annual publications, other ways to give, bank transfer, accessibility, editor
type: help
---
# Die Spendenseite

Diese Seite richtet sich an Fundraiser oder Redakteure, die die Seite „Spenden“ schreiben. Sie gilt für beide
Spendenwege ([Spenden](README.md)). Exponential gibt Ihnen eine gewöhnliche Seite, einen Schaltflächen-Block und
Datei-Downloads; was einen Spender überzeugt, ist der Inhalt.

## Inhalt

1. [Eine Gliederung, die funktioniert](#eine-gliederung-die-funktioniert)
2. [Umsetzung in Exponential](#umsetzung-in-exponential)
3. [Schreibtipps](#schreibtipps)
4. [Andere Wege zu spenden](#andere-wege-zu-spenden)
5. [Barrierefreiheit](#barrierefreiheit)
6. [Checkliste für die Spendenseite](#checkliste-für-die-spendenseite)

## Eine Gliederung, die funktioniert

| Teil | Was er sagt | Beispieltext (ersetzen) |
|---|---|---|
| Überschrift | Was eine Spende bewirkt, in einer Zeile | „Ihre Spende hält das Hilfetelefon auch nachts erreichbar“ |
| Beispiel | Ein echtes, konkretes Ergebnis | „Eine Spende von [Betrag] bezahlt [eine konkrete Sache]“ |
| Schaltfläche | Spenden, im sichtbaren Bereich ohne Scrollen | „Jetzt spenden“ |
| Wer wir sind | Rechtlicher Name, die gesetzlich vorgeschriebenen Registerangaben, ein Kontakt | |
| Wohin das Geld geht | Zwei oder drei Zeilen mit einem Link zur neuesten Jahresveröffentlichung | „Lesen Sie unseren Jahresbericht (PDF)“ |
| Andere Wege zu spenden | Überweisung, Sachspenden, Ehrenamt, Vermächtnis | |
| Datenschutz | Wer die Zahlung abwickelt, ein Link zur Datenschutzerklärung | |
| Schaltfläche erneut | Am Ende | „Jetzt spenden“ |

## Umsetzung in Exponential

| Element | Wie | Quelle |
|---|---|---|
| Die Seite | Ein Artikel oder Ordner der mitgelieferten Klassen oder ein Layout | [Inhaltsklassen für Websites](../../ersteller/website-ersteller/inhaltsklassen-fuer-websites.md) |
| Die Schaltflächen | Der Layouts-Block **Button / Link** oder ein Linkfeld, das als Schaltfläche angezeigt wird | [Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md) |
| Die Jahresveröffentlichung | Ein Dateiobjekt (Klasse `file`), im Text verlinkt | [Dateien und Downloads](../../ersteller/website-ersteller/dateien-und-downloads.md), [Jahresveröffentlichungen als Downloads](../transparenz/jahresveroeffentlichungen-als-downloads.md) |
| Ein Foto | Ein Bild mit Alternativtext | [Bilder und Bildvarianten](../../ersteller/website-ersteller/bilder-und-bildvarianten.md) |
| Vorschauen beim Teilen | Titel, Beschreibung und Bild für soziale Netzwerke | [Metadaten und Teilen in sozialen Netzwerken](../../ersteller/website-ersteller/metadaten-und-teilen-in-sozialen-medien.md) |

Verwenden Sie für einen zeitlich begrenzten Spendenaufruf ein Kampagnenlayout, das sich nach Datum ein- und
ausschaltet ([Kampagnen und Spendenaufrufe](../kommunikation/kampagnen-und-spendenaufrufe.md)).

## Schreibtipps

- **Beginnen Sie mit den Menschen, denen Sie helfen, nicht mit Ihrer Organisation.**
- **Ein konkretes Beispiel schlägt drei Statistiken.** Erfinden Sie keine Zahlen; verwenden Sie Ihre eigenen, mit
  ihrer Quelle.
- **Kurze Absätze, einfache Wörter.** Siehe [Einfache Sprache](../../verwaltung/sprachen/einfache-sprache.md) und
  [Verständlich schreiben](../../verwaltung/sprachen/verstaendlich-schreiben.md).
- **Eine klare Handlung.** Zwei Schaltflächen, beide „Spenden“, nicht fünf verschiedene Links.
- **Ehrlich bei Gebühren und Ablauf.** Sagen Sie, wo Spender weitermachen und was sie erhalten werden.
- **Exponential Intelligence kann Entwürfe schreiben**, aber ein Mensch prüft und veröffentlicht; sie schreibt nur
  Entwürfe ([KI-Entwürfe verantwortungsvoll nutzen](../../kunden/wachstum/ki-entwuerfe-verantwortungsvoll-nutzen.md)).

## Andere Wege zu spenden

Manche Spender geben lieber nicht online. Führen Sie die Alternativen mit dem auf, was dafür nötig ist:

| Weg | Was Sie zeigen |
|---|---|
| Überweisung | Die Kontodaten, wie Ihre Bank sie angibt, und den zu verwendenden Verwendungszweck |
| Sachspenden | Was Sie annehmen, ein Kontaktformular ([Formulare mit dem Information Collector](../../ersteller/website-ersteller/formulare-mit-dem-informationssammler.md)) |
| Ehrenamt | Ein Link zum Formular für Ehrenamtliche ([Ehrenamtliche](../ehrenamtliche/README.md)) |
| Regelmäßige Spenden | Die monatliche Option der externen Plattform, falls Sie eine nutzen |
| Testamentsspenden | Eine Ansprechperson, kein Formular |

## Barrierefreiheit

- Die Schaltfläche ist ein echter Link oder eine echte Schaltfläche mit klarem Text, kein Bild von Text
  ([Linktext](../../verwaltung/barrierefreiheit/linktext.md)).
- Die Farben erfüllen die Kontrastanforderungen ([Farbe und Kontrast](../../verwaltung/barrierefreiheit/farbe-und-kontrast.md)).
- Das PDF der Jahresveröffentlichung ist barrierefrei, oder es gibt eine HTML-Fassung
  ([Dokumente und PDFs](../../verwaltung/barrierefreiheit/dokumente-und-pdfs.md)).
- Der gesamte Weg funktioniert mit der Tastatur ([Grundlagen der Barrierefreiheit für Non-Profit-Organisationen](../barrierefreiheit/barrierefreiheit-grundlagen-fuer-non-profit-organisationen.md)).

## Checkliste für die Spendenseite

- [ ] Die Überschrift sagt, was eine Spende bewirkt.
- [ ] Ein echtes Beispiel, keine erfundenen Zahlen.
- [ ] Spenden-Schaltfläche im sichtbaren Bereich und am Ende.
- [ ] Rechtlicher Name und Registerangaben wie vorgeschrieben angezeigt.
- [ ] Link zur neuesten Jahresveröffentlichung.
- [ ] Andere Wege zu spenden aufgeführt.
- [ ] Der Datenschutzhinweis nennt den Zahlungsanbieter.
- [ ] Auf einem Smartphone und mit der Tastatur getestet.

## Verwandte Seiten

- [Spenden](README.md)
- [Vertrauen der Spender und Dank](vertrauen-der-spender-und-dank.md)
- [Spenden- und Fundraising-Seiten](../../unternehmen/gemeinnuetzige/spenden-und-spendenaufrufe.md#was-eine-gute-spendenseite-enthält)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
