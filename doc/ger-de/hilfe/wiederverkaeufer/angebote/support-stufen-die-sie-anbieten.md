---
title: Support-Stufen, die Sie anbieten
audience: reseller, partner, hosting
category: reselling
order: 200
summary: Wie ein Wiederverkäufer den Support strukturiert, den er verkauft - die Grenze zwischen Hosting-Support und Anwendungs-Support, Stufenoptionen von Basis bis Entwicklung, was jede Stufe von Ihrem Team braucht, wie Partner-Support und bezahlter Support von 7x hinter Ihren Stufen stehen, und ein Stufenblatt zum Kopieren.
keywords: Support-Stufen, Support-Level, Hosting-Support, Anwendungs-Support, verwaltet, Entwicklung, First-Level, Second-Level, Eskalation, Partner-Tickets, Stufenblatt, support tiers, support levels, hosting support, application support, managed, development, first line, second line, escalation, partner tickets, tier sheet
type: help
---
# Support-Stufen, die Sie anbieten

Diese Seite ist für Support-Leiter und Produktmanager bei Wiederverkäufern. Sie macht aus dem Support, den Sie leisten
können, Stufen, die Kunden verstehen und die Ihr Team liefern kann. Reaktionszeiten werden unter
[Service-Levels gestalten](service-levels-gestalten.md) behandelt.

## Inhalt

1. [Zwei Arten von Support](#zwei-arten-von-support)
2. [Stufenoptionen](#stufenoptionen)
3. [Support-Ebenen](#support-ebenen)
4. [Was Kunden selbst tun können](#was-kunden-selbst-tun-können)
5. [Ein Stufenblatt zum Kopieren](#ein-stufenblatt-zum-kopieren)

## Zwei Arten von Support

| Hosting-Support | Anwendungs-Support |
|---|---|
| Der Server, PHP, die Engine, der Datenbankserver | Inhaltsklassen, Templates, Design |
| Datensicherungen und Wiederherstellungen | Die Erweiterungen und der eigene Code des Kunden |
| TLS-Zertifikate | Redaktionelle Fragen („Wie veröffentliche ich?“) |
| Mailversand vom Server | Datenkorrekturen innerhalb der Inhalte |
| Einspielen von Exponential-Updates, wo vereinbart | Upgrades, die Codeänderungen brauchen |

Quelle: [Support-Stufen für Exponential-Hosting](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md). Schreiben Sie diese Grenze in
Ihre Supportbedingungen; die meisten Reibungen entstehen, wenn sie unklar bleibt.

## Stufenoptionen

| Stufe | Enthält typischerweise | Passt zu | Ihr Team braucht |
|---|---|---|---|
| Basis | Hosting-Support per Ticket, Geschäftszeiten | Shared-Tarifen | Betriebspersonal |
| Standard | Hosting-Support, schnellere Erstreaktion, Telefon oder Chat | VPS-Tarifen | Mehr Betriebspersonal |
| Verwaltet | Hosting-Support plus Exponential-Updates, Optimierung von Cache und Leistung, Wiederherstellung auf Anfrage | Managed-Tarifen | Jemanden, der für Updates zuständig ist |
| Anwendung | Verwaltet plus Hilfe bei Templates, Erweiterungen und Konfiguration, nach Vereinbarung | Kunden ohne Entwickler | Exponential-Entwickler |
| Entwicklung | Projektarbeit: Migrationen, neue Funktionen, Erweiterungen | Als Projekt abgerechnet | Ein Entwicklungsteam |

## Support-Ebenen

```text
Customer
  -> 1st line (your company): takes the request, collects the facts, answers known questions
  -> 2nd line (your company): developers and administrators who know the site
  -> 3rd line (outside):
       - your partner support tickets at 7x, by level
       - paid support from 7x or another supplier
       - the Exponential issue tracker for reproducible bugs
       - security@se7enx.com, privately, for vulnerabilities
```

Der Third-Level ist im Vertrag Ihres Kunden nie als Versprechen sichtbar. Die Eskalation ist unter
[Eskalation für Wiederverkäufer](../kunden/eskalation.md) beschrieben.

## Was Kunden selbst tun können

Jedes Ticket, das ein Kunde allein lösen kann, spart Ihnen Geld. Verweisen Sie auf:

| Bedarf | Selbsthilfe |
|---|---|
| Wie man bearbeitet und veröffentlicht | [Erste Schritte](../../erste-schritte.md), [Inhalte bearbeiten](../../inhalte-bearbeiten.md) |
| Was der Fehler ist | Die Fehlerreferenz auf der Fehlerseite, die Ihr Personal nachschlägt ([Einen Fehler anhand seiner Referenz finden](../../../anleitungen/fehler-anhand-der-referenz-finden.md)) |
| Ob die Website gesund ist | [Kunden: hier beginnen](../../kunden/kunden-hier-beginnen.md) und die dort aufgeführten Seiten für Betreiber |
| Wann man wen anruft | [Wann Sie wen anrufen](../../kunden/partner-und-hoster/wann-sie-wen-anrufen.md) |

Sie dürfen Kunden auch angepasste Kopien dieser Seiten unter Ihrer eigenen Marke übergeben
([Dokumentation, die Sie Ihren Kunden geben können](../kunden/dokumentation-fuer-ihre-kunden.md)).

## Ein Stufenblatt zum Kopieren

```text
Tier: ______________   Price per month: $______
Kind: hosting support / application support / both
Channels: ticket / e-mail / phone (S1 only) / chat
Hours: ______________ (time zone ______)
First response: S1 ____ S2 ____ S3 ____ S4 ____
Included: ______________________________________
Not included: __________________________________
Escalation to 3rd line: by us, not by the customer
Report: monthly / quarterly / none
```

## Verwandte Seiten

- [Support-Stufen für Exponential-Hosting](../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)
- [Wiederverkäufer von Supportverträgen](../modelle/wiederverkaeufer-von-supportvertraegen.md)
- [Ein Partner-Support-Ticket eröffnen](../../partner/support-ticket-eroeffnen.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
