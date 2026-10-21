---
title: Einen Link zu einem externen Dienst hinzufügen
audience: government, editor
category: public-sector
order: 330
summary: Für Redakteure - von einer Seite auf einen Online-Dienst in einem anderen System verlinken (ein Antragsportal, einen Bezahldienst, ein Buchungssystem), mit einem klaren Linktext, einem Hinweis, dass der Besucher die Site verlässt, und einem Link, der dauerhaft funktioniert.
keywords: externer Link, Online-Dienst, Antragsportal, Bezahlung, Buchung, Linktext, URL-Feld, Linkprüfung, URL-Verwaltung, Site verlassen, external link, online service, application portal, payment, booking, link text, URL field, ezurl, link check, Link management, leaving the site
type: help
---
# Einen Link zu einem externen Dienst hinzufügen

Diese Seite ist für Redakteure, die Besucher von der Website zu einem System schicken, in dem sie etwas beantragen,
bezahlen oder buchen. Die Seite erklärt, das externe System wickelt den Vorgang ab
([Auf Online-Vorgänge verlinken](../../aufbauen/leistungskatalog/auf-online-verfahren-verlinken.md)).

## Inhalt

1. [Bevor Sie beginnen](#bevor-sie-beginnen)
2. [Schritte](#schritte)
3. [Das Ergebnis prüfen](#das-ergebnis-prüfen)
4. [Fallstricke](#fallstricke)

## Bevor Sie beginnen

- Sie haben die genaue Startadresse des Dienstes, von seinem Betreiber genannt und nicht nach dem Anmelden aus dem
  Browser kopiert (solche Adressen enthalten oft Sitzungsdaten).
- Sie wissen, was Besucher brauchen, bevor sie beginnen (Unterlagen, einen Ausweis, eine Zahlungskarte).

## Schritte

**In einem URL-Feld** (zum Beispiel **Apply online** einer Dienstleistung oder die Klasse **Link**):

1. **Bearbeiten** Sie die Seite; fügen Sie die Adresse in das URL-Feld ein; füllen Sie dessen Textfeld aus, falls es
   eines hat („Bewohnerparkausweis beantragen“).
2. **Zur Veröffentlichung schicken**.

**Im Rich Text:**

1. Markieren Sie die Wörter, die der Link sein sollen, wählen Sie die Link-Schaltfläche, wählen Sie eine externe
   Adresse und fügen Sie sie ein.
2. Schreiben Sie den Linktext so, dass er auch ohne Zusammenhang verständlich ist: „Bewohnerparkausweis beantragen
   (auf der Website von <Name des Dienstes>)“.

**Vor dem Link**, im Text: was bereitzuhalten ist, wie lange es dauert, und die Alternative für Menschen, die den
Dienst nicht online nutzen können.

## Das Ergebnis prüfen

- [ ] Der Link öffnet den Anfang des Dienstes, abgemeldet, in einem privaten Fenster.
- [ ] Der Linktext sagt, wohin er führt.
- [ ] **Setup > URL-Verwaltung** (`/url/list`) listet die Adresse; die Linkprüfung markiert sie nach ihrem nächsten
  Lauf als gültig.

## Fallstricke

| Fallstrick | So vermeiden Sie ihn |
|---|---|
| „Hier klicken“ | der Linktext nennt den Dienst |
| In einem neuen Fenster öffnen, ohne es zu sagen | schreiben Sie „(öffnet in einem neuen Fenster)“ oder erzwingen Sie es nicht |
| Eine Adresse mit Sitzungs- oder Tracking-Code | verwenden Sie die saubere Startadresse |
| Dieselbe Adresse auf vielen Seiten eingetippt | verwenden Sie ein URL-Feld oder ein Link-Objekt; die URL-Tabelle ist gemeinsam, sodass die Korrektur eines Eintrags jede Seite repariert, die ihn verwendet ([Defekte Links finden und reparieren](defekte-links-finden-und-reparieren.md)) |

## Verwandte Seiten

- [Aufgaben zum Veröffentlichen](README.md)
- [Eine Dienstleistungsseite veröffentlichen](eine-dienstleistungsseite-veroeffentlichen.md)
- [Link- und Inhaltsprüfungen](../../../ersteller/website-ersteller/link-und-inhaltspruefungen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
