---
title: Externe Spendenplattform, Schritt für Schritt
audience: non-profit, businesses, editor, website-creator
category: non-profits
order: 30
summary: Den einfachsten Spendenweg auf einer Exponential-Website aufbauen - eine Spendenplattform wählen, eine Spendenseite, ein Spenden-Button in der Kopfzeile oder mit dem Button-Block von Layouts, Links von Kampagnenseiten, Spender darauf hinweisen, dass sie die Website verlassen, die Datenschutzerklärung, eine Dankesseite für die Rückkehr, nur einbetten, was Ihre Content Security Policy erlaubt, und testen.
keywords: externe Spendenplattform, Spenden-Button, Spendenseite, Link, Button-Block, Einbetten, Dankesseite, Datenschutzerklärung, Testen, external donation platform, donate button, donate page, link, Layouts button block, ezurl, embed, iframe, Content Security Policy, thank-you page, privacy notice, testing
type: help
---
# Externe Spendenplattform, Schritt für Schritt

Diese Seite richtet sich an den Redakteur oder Website-Ersteller, der den Spendenweg einrichtet, den die meisten kleinen
Non-Profit-Organisationen nutzen: einen Spenden-Button auf Ihrer Website, der zu einer anderswo gehosteten Spendenseite
führt. Keine Zahlungsdaten berühren Ihre Website, und die Plattform übernimmt regelmäßige Spenden und Quittungen, soweit
sie diese anbietet.

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritt 1: die Spendenseite](#schritt-1-die-spendenseite)
3. [Schritt 2: der Spenden-Button](#schritt-2-der-spenden-button)
4. [Schritt 3: Links aus Kampagnen und dem Newsletter](#schritt-3-links-aus-kampagnen-und-dem-newsletter)
5. [Schritt 4: die Rückkehrseite](#schritt-4-die-rückkehrseite)
6. [Das Formular einer Plattform einbetten](#das-formular-einer-plattform-einbetten)
7. [Schritt 5: Datenschutz und Testen](#schritt-5-datenschutz-und-testen)
8. [Checkliste](#checkliste)

## Bevor Sie beginnen

- Wählen Sie die Plattform selbst: Vergleichen Sie Gebühren, Auszahlungszeiten, regelmäßige Spenden, Quittungen und
  Datenexport anhand der veröffentlichten Bedingungen. Diese Hilfe nennt weder Plattformen noch Gebühren.
- Lassen Sie das Plattformkonto auf den Namen Ihrer Organisation eröffnen, mit mindestens zwei Personen, die sich
  anmelden können.
- Legen Sie die Adresse Ihrer Spendenseite auf der Plattform fest; auf sie werden Sie verlinken.

## Schritt 1: die Spendenseite

Legen Sie in der Hauptnavigation eine normale Seite (einen Artikel oder einen Ordner mit Text aus den mitgelieferten
Klassen) namens **Spenden** an. Sie erklärt, warum man spenden sollte, bevor sie darum bittet;
[Die Spendenseite](die-spendenseite.md) listet auf, was hineingehört. Setzen Sie den Link zur Plattform weit nach oben
und noch einmal ans Ende.

Geben Sie der Seite eine kurze Adresse wie `/Donate`; Exponential bildet die Adresse aus dem Namen, und Sie können einen
Alias hinzufügen ([URL-Aliase und sprechende URLs](../../ersteller/website-ersteller/url-aliasse-und-sprechende-urls.md)).

## Schritt 2: der Spenden-Button

Wählen Sie, was Ihr Design anbietet:

| Wo | Wie | Quelle |
|---|---|---|
| In einem Layout (Startseite, Kampagnenseite) | Der Standardblock **Button / Link** (`button`) von Exponential Layouts | [Layouts-Referenz](../../../bc/6.0/layouts.md), [Landingpages mit Layouts](../../ersteller/website-ersteller/landingpages-mit-dem-layouts-editor.md) |
| In einer Seitenklasse | Ein Linkfeld (`ezurl`), das das Template als Button anzeigt | [Datentypen auswählen](../../ersteller/website-ersteller/datentypen-waehlen.md) |
| In der Kopfzeile jeder Seite | Die gemeinsame Kopf- und Fußzeile | [Gemeinsame Kopf- und Fußzeile](../../ersteller/website-ersteller/gemeinsamer-kopf-und-fussbereich.md) |
| Im Menü | Ein Menüpunkt, der auf die Spendenseite zeigt | [Menüs und Navigation](../../ersteller/website-ersteller/menues-und-navigation.md) |

Lassen Sie den Button auf Ihre eigene Spendenseite zeigen, nicht direkt auf die Plattform: Die Seite erklärt, die
Plattform sammelt.

## Schritt 3: Links aus Kampagnen und dem Newsletter

- Jede Kampagnenseite trägt ihren eigenen Spenden-Button; siehe [Kampagnen und Spendenaufrufe](../kommunikation/kampagnen-und-spendenaufrufe.md).
- Newsletter-Ausgaben verlinken auf die Spendenseite ([Unterstützer-Newsletter](../kommunikation/unterstuetzer-newsletter-mit-cjw-newsletter.md)).
- Wenn die Plattform es erlaubt, dem Link einen Code zur Unterscheidung von Kampagnen anzuhängen, verwenden Sie einen pro
  Kampagne, damit Sie sehen, welcher Aufruf gewirkt hat.

## Schritt 4: die Rückkehrseite

Die meisten Plattformen können Spender nach der Spende an eine Adresse Ihrer Wahl zurückschicken. Legen Sie auf Ihrer
Website eine Seite **Danke** an und teilen Sie der Plattform deren Adresse mit. Halten Sie sie aus dem Menü heraus.
Was darauf steht, lesen Sie unter [Vertrauen der Spender und Dank](vertrauen-der-spender-und-dank.md).

## Das Formular einer Plattform einbetten

Manche Plattformen bieten ein Formular zum Einbetten in Ihre Seite an. Bevor Sie das tun:

- Das Formular lädt ein Skript oder einen Frame von der Plattform. Eine Website, die eine Content Security Policy
  sendet, muss die Hosts der Plattform erlauben, sonst erscheint das Formular nicht
  ([Content Security Policy](../../../funktionen/6.0/content-security-policy.md)).
- Ein Skript hinzuzufügen ist Template-Arbeit für den Website-Ersteller, nichts, was man in Rich Text einfügt
  ([Stylesheets und Skripte](../../ersteller/website-ersteller/stylesheets-und-skripte.md)).
- Ein eingebettetes Formular sendet trotzdem Daten an die Plattform; nennen Sie sie in Ihrer Datenschutzerklärung
  ([Karten und Einbettungen](../../verwaltung/datenschutz/karten-und-einbettungen.md) erklärt dieselbe Frage für andere
  Einbettungen).
- Prüfen Sie, ob das eingebettete Formular per Tastatur und Screenreader bedienbar ist; wenn nicht, verlinken Sie
  stattdessen.

Ein einfacher Link ist simpler, schneller und leichter barrierefrei zu gestalten.

## Schritt 5: Datenschutz und Testen

- [ ] Die Datenschutzerklärung nennt die Plattform und was sie erhält.
- [ ] Die Spendenseite sagt neben dem Button, dass Spender auf der Website der Plattform weitermachen.
- [ ] Auf einem Telefon und einem Computer testen: Button, Plattformseite, eine kleine echte Spende, Rückkehr auf die
  Dankesseite.
- [ ] Nur mit der Tastatur testen ([Tastatur und Fokus](../../verwaltung/barrierefreiheit/tastatur-und-fokus.md)).
- [ ] Eine zweite Person bitten, den Weg ohne Hilfe zu durchlaufen.

## Checkliste

- [ ] Plattformkonto auf den Namen der Organisation, zwei Personen mit Zugang.
- [ ] Spendenseite live, verlinkt im Menü und in der Kopfzeile.
- [ ] Button-Blöcke auf der Startseite und den Kampagnenseiten.
- [ ] Dankesseite als Rücksprungadresse der Plattform eingetragen.
- [ ] Datenschutzerklärung aktualisiert.
- [ ] Eine Person benannt, die die Spenden auf der Plattform wöchentlich prüft.

## Verwandte Seiten

- [Spenden](README.md)
- [Spendenwege im Überblick](spendenwege-im-ueberblick.md)
- [Spenden- und Fundraising-Seiten](../../unternehmen/gemeinnuetzige/spenden-und-spendenaufrufe.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
