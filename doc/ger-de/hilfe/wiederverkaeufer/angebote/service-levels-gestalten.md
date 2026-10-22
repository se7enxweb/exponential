---
title: Service-Levels für Wiederverkäufer gestalten
audience: reseller, partner, hosting
category: reselling
order: 190
summary: Wie ein Wiederverkäufer Service-Levels gestaltet, die er halten kann - warum die Reaktionszeiten der Partnertarife das sind, was 7x Ihnen verspricht, und nicht das, was Sie Ihren Kunden versprechen dürfen, wie Sie Ihre eigenen Zeiten aus Ihrem Personal ableiten, wozu sich das Projekt selbst verpflichtet, und eine Erklärung zu Service-Levels zum Kopieren.
keywords: Service-Level, SLA, Reaktionszeit, Erstreaktion, Lösung, Verfügbarkeit, Partnertarif, Reaktionszeit von 7x, Geschäftszeiten, Bereitschaft, Schweregrad, Gutschriften, Sicherheitskorrekturen, service level, response time, first response, resolution, uptime, partner plan, 7x response time, business hours, on call, severity, credits, security fixes
type: help
---
# Service-Levels für Wiederverkäufer gestalten

Diese Seite ist für Inhaber und Support-Leiter, die die Service-Levels in den Angeboten eines Wiederverkäufers
schreiben. Der häufigste Fehler beim Wiederverkauf ist, das Versprechen eines Lieferanten in den eigenen Vertrag zu
übernehmen. Diese Seite zeigt, wie Sie das vermeiden.

## Inhalt

1. [Wessen Versprechen ist es?](#wessen-versprechen-ist-es)
2. [Wozu sich das Projekt und 7x verpflichten](#wozu-sich-das-projekt-und-7x-verpflichten)
3. [Ihre eigenen Zeiten ableiten](#ihre-eigenen-zeiten-ableiten)
4. [Verfügbarkeit](#verfügbarkeit)
5. [Sicherheitskorrekturen](#sicherheitskorrekturen)
6. [Eine Erklärung zu Service-Levels zum Kopieren](#eine-erklärung-zu-service-levels-zum-kopieren)

## Wessen Versprechen ist es?

> **Hinweis**
> Die Reaktionszeiten der Partnertarife (≤ 36 h für Student, Now und Starter, ≤ 24 h für Basic, ≤ 12 h für
> Professional und Silver, ≤ 4 h für Gold, ≤ 3 h für Platinum) sind das, was **7x Ihnen**, dem Partner, für Ihre
> Tickets als GitHub-Issues verspricht. Sie sind **keine** Reaktionszeiten, die Sie Ihren Kunden versprechen dürfen.

Drei Gründe:

1. **Das Problem Ihres Kunden erreicht 7x erst nach Ihnen.** Ihre eigene Diagnosezeit kommt zuerst.
2. **Nicht veröffentlicht:** ob Reaktionszeit die erste Antwort oder die Lösung bedeutet, Geschäfts- oder
   Kalenderstunden, und die Zeitzone ([Support-Stufen und Reaktionszeiten](../../partner/support-stufen-und-reaktionszeiten.md)).
3. **Eine Reaktion ist keine Fehlerbehebung.** Ein Fehler in Exponential wird im nächsten Release 6.0.x behoben
   (`SECURITY.md`), das Sie dann einspielen.

Ihr Versprechen beruht also auf Ihrem eigenen Personal. Der Partner-Support ist Ihre Rückendeckung.

## Wozu sich das Projekt und 7x verpflichten

| Quelle | Verpflichtung |
|---|---|
| `SECURITY.md` | Eine erste Antwort auf eine vertrauliche Sicherheitsmeldung „within one business day“ (innerhalb eines Werktags); angenommene Probleme werden auf `main` behoben und mit der nächsten Version 6.0.x veröffentlicht; „a published tag is never changed“ (ein veröffentlichter Tag wird nie geändert) |
| Community-Support | Keine: „its developers are not obligated to provide free support“ (seine Entwickler sind nicht verpflichtet, kostenlosen Support zu leisten, <https://exponential.earth/support>) |
| Partnertarif | Tickets und Reaktionszeiten je Stufe, Ihnen gegenüber ([Partnertarife und Preise](../../partner/partnertarife-und-preise.md#die-tarife)) |
| Das Projekt | Kein Release-Kalender, kein Ende der Unterstützung für 6.0, keine Service-Level-Vereinbarung ([Release-Linie und Unterstützung](../../kunden/gesund-halten/release-linie-und-support.md#was-das-projekt-nicht-veröffentlicht)) |

## Ihre eigenen Zeiten ableiten

Gehen Sie von Ihrem Team aus, nicht von einem Ziel:

```text
Staff who can handle severity 1 for this customer ........ ____ people
Hours they are reachable ................................. ____ (days, hours, time zone)
Measured median first response last quarter ............. ____ h
Measured 90th percentile first response .................. ____ h
Promise = 90th percentile, rounded up .................... ____ h
```

| Versprechen | Braucht mindestens |
|---|---|
| Nächster Werktag | Eine geschulte Person und eine Vertretung |
| Derselbe Werktag für Schweregrad 1 und 2 | Zwei Personen, die die Website kennen; Runbooks |
| Vier Stunden rund um die Uhr für Schweregrad 1 | Einen bezahlten Bereitschaftsplan mit mehreren Personen und eine Überwachung, die sie weckt |

Messen Sie Erstreaktion und Lösung getrennt und versprechen Sie eine Lösung nur für Dinge, die Sie selbst in der Hand
haben (eine Datensicherung wiederherstellen, ein Update zurückrollen), nie für Fehlerbehebungen im Projekt.

## Verfügbarkeit

- Versprechen Sie Verfügbarkeit nur für das, was Sie betreiben, gemessen mit Ihrer eigenen Überwachung
  ([Health-Checks und Verfügbarkeit](../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
- Nehmen Sie angekündigte Wartungsfenster aus; nutzen Sie für sie den Wartungsmodus
  ([Wartungsmodus](../../../anleitungen/wartungsmodus.md)).
- Wenn Sie das Hosting eines anderen weiterverkaufen, kann Ihr Verfügbarkeitsversprechen nicht besser sein als
  seines.
- Optionen für Service-Gutschriften und deren Formulierung stehen unter [SLA-Optionen](../../hosting/geschaeft/sla-optionen-fuer-exponential-hosting.md).

## Sicherheitskorrekturen

Der einzige Weg, auf dem eine Website eine Fehlerbehebung erhält, ist ein Update auf das neueste Release 6.0.x. Ein
realistisches Versprechen:

```text
When an Exponential release carries a security fix, we apply it to your site within <n> business days
of its publication, after a test on <staging / a copy>. Critical issues: within <n> business days.
```

Legen Sie `<n>` danach fest, wie lange ein getestetes Update über alle Ihre Kunden tatsächlich dauert
([Kunden aktuell halten](../kunden/kunden-aktuell-halten.md)).

## Eine Erklärung zu Service-Levels zum Kopieren

```text
Service levels for <Customer>, effective <date>
Support hours: <days, hours, time zone>
First response: S1 <time>, S2 <time>, S3 <time>, S4 <time> (within support hours unless stated)
Severity 1 outside support hours: <yes, phone <role number> / no>
Availability of the hosting we operate: <percentage> per month, excluding announced maintenance
Backups: <frequency>, kept <days>; restore tested <frequency>
Security releases: applied within <n> business days of publication
Measurement and reporting: <monthly report>
Not covered: fixes in Exponential itself (reported upstream, applied when released), third-party services
```

## Verwandte Seiten

- [Support-Stufen und Reaktionszeiten](../../partner/support-stufen-und-reaktionszeiten.md)
- [SLA-Vorlage](../../kunden/partner-und-hoster/sla-vorlage.md)
- [Support-Stufen, die Sie anbieten](support-stufen-die-sie-anbieten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
