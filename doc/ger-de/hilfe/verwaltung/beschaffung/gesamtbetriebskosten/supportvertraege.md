---
title: Supportverträge in den Kosten einer Exponential-Website
audience: government, project-manager, partner, hosting
category: public-sector
order: 80
summary: Die Arten von Support, die es für Exponential gibt (kostenlose Hilfe der Community, Partner-Support, bezahlte Hilfe von 7x, der eigene Vertrag eines Lieferanten), was jede laut Veröffentlichung verspricht, wie Sie den benötigten Support festlegen und wie Sie Support-Angebote in den Gesamtbetriebskosten vergleichen.
keywords: Supportvertrag, Wartungsvertrag, SLA, Reaktionszeit, Schweregrad, Support-Stufen, Support der Community, bezahlter Support, Partner, 7x, Kostenvergleich, support contract, maintenance contract, response time, severity, support levels, community support, paid support, partner, cost comparison
type: help
---
# Supportverträge in den Kosten einer Exponential-Website

Diese Seite richtet sich an Projektleitungen, die entscheiden müssen, welchen Support sie einkaufen und wie sie Angebote
vergleichen. Freie Software kommt mit kostenloser Hilfe der Community, aber ohne Zusage einer Reaktionszeit; diese Zusage
muss bei jemandem eingekauft werden. Die Fakten dazu, was jede Quelle verspricht, stammen aus [Support erhalten](../../../projekt/unterstuetzung-erhalten.md),
[Support-Stufen und Reaktionszeiten](../../../partner/support-stufen-und-reaktionszeiten.md) und `SECURITY.md`.

## Inhalt
1. [Quellen des Supports](#quellen-des-supports)
2. [Zuerst festlegen, was Sie brauchen](#zuerst-festlegen-was-sie-brauchen)
3. [Was ein Support-Angebot angeben sollte](#was-ein-support-angebot-angeben-sollte)
4. [Angebote nach Kosten vergleichen](#angebote-nach-kosten-vergleichen)
5. [Fallstricke](#fallstricke)

## Quellen des Supports

| Quelle | Zusage | Kosten |
|---|---|---|
| Community (Foren, Telegram, Discord, Slack) | keine: „seine Entwickler sind nicht verpflichtet, kostenlosen Support zu leisten“ | kostenlos |
| Sicherheitskanal des Projekts | erste Antwort auf eine vertrauliche Sicherheitsmeldung innerhalb eines Werktags | kostenlos |
| Partner-Support (für Partner, vom Programm) | Reaktionszeiten je Partnerstufe, wie veröffentlicht | Teil der eigenen Kosten des Partners |
| Bezahlte Hilfe von 7x | mit 7x vereinbart | von 7x angeboten ([Wer ist 7x?](../../../wer-ist-7x.md)) |
| Der Supportvertrag Ihres Lieferanten | was der Vertrag sagt | im Angebot beziffert |

Das Projekt Exponential veröffentlicht kein eigenes SLA ([SLA-Optionen](../../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md)).

## Zuerst festlegen, was Sie brauchen

Bevor Sie nach Preisen fragen, schreiben Sie auf:

| Punkt | Ihre Entscheidung |
|---|---|
| Support-Zeiten | Geschäftszeiten, erweitert, rund um die Uhr |
| Schweregrade | was kritisch ist (Website nicht erreichbar, Sicherheit), hoch, normal, niedrig ([Support-Stufen](../../../hosting/geschaeft/support-stufen-fuer-exponential-hosting.md)) |
| Ziele für Reaktion und Lösung je Schweregrad | Lücken für Ihren Bedarf |
| Umfang | Code der Website, Erweiterungen, Hosting, Hilfe zu Inhalten, Fragen zur Schulung |
| Sicherheitsreleases | innerhalb einer von Ihnen festgelegten Frist eingespielt |
| Kanal | Ticketsystem, Telefon für kritische Probleme |
| Berichte | monatlicher Bericht über Tickets und Zeiten |

## Was ein Support-Angebot angeben sollte

- Ob Reaktionszeit die erste Antwort oder die Lösung bedeutet, ob Geschäfts- oder Kalenderstunden gemeint sind, und die
  Zeitzone. Die Partnerseite weist darauf hin, dass diese in den veröffentlichten Stufen des Programms nicht festgelegt
  sind; Ihr Vertrag muss sie festlegen.
- Wie der Lieferant die Zusage personell abdeckt: Die Hinweise für Partner warnen davor, je eine kürzere Reaktion zu
  versprechen, als das eigene Personal leisten kann.
- Was ausgeschlossen ist (Ihre eigenen Änderungen, Dienste Dritter, Fehler in Inhalten).
- Wie Sicherheitsprobleme an das Projekt eskaliert werden ([Eskalationen und Sicherheitsprobleme](../../../partner/eskalationen-und-sicherheitsprobleme.md)).
- Dass das Support-Personal nie per E-Mail oder in Tickets nach Passwörtern fragt und personenbezogene Daten wie
  vereinbart behandelt ([Kundendaten schützen](../../../partner/kundendaten-schuetzen.md)).

## Angebote nach Kosten vergleichen

```text
Offer   Fixed fee/period   Included hours   Rate beyond   Critical response   Hours covered   Security patch time
A       <...>              <...>            <...>         <...>               <...>           <...>
B       <...>              <...>            <...>         <...>               <...>           <...>
Expected use per period (your estimate): <hours>  -> expected cost A: <...>  B: <...>
```

Schätzen Sie die erwartete Nutzung aus Ihrem Proof of Concept und aus der Zahl der Redaktionsmitglieder und Websites.
Bepreisen Sie den Support für den ganzen Zeithorizont des TCO-Modells, einschließlich des letzten Jahres vor dem
Ausstieg.

## Fallstricke

- Eine Reaktionszeit ohne das Personal dahinter kaufen.
- Für Support einer Versionslinie bezahlen, die keine Sicherheitskorrekturen erhält (5.x und älter, `SECURITY.md`).
- Sich bei einem öffentlichen Dienst mit Verfügbarkeitspflichten auf die Hilfe der Community verlassen.
- Ein Support-Umfang, der den eigenen Code ausschließt, den derselbe Lieferant geschrieben hat.

## Verwandte Seiten
- [Gesamtbetriebskosten: Übersicht](README.md)
- [Einen Support-Partner bewerten](../bewertung/einen-support-partner-bewerten.md)
- [Support erhalten](../../../projekt/unterstuetzung-erhalten.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
