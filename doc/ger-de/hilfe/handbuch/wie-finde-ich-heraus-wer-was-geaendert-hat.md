---
title: Wie finde ich heraus, wer was wann geändert hat?
audience: admin
category: manual
order: 220
summary: Öffnen Sie Audit > Konsole, filtern Sie nach Login, Objekt (node:275), Ereignisname oder Zeit, und lesen Sie jedes Ereignis mit Akteur, Objekt, Ergebnis und Anfrage; Jetzt prüfen belegt, dass die Aufzeichnung nicht verändert wurde.
keywords: Audit, Audit-Trail, Audit-Konsole, wer hat geändert, Verlauf, Akteur, Ereignis, Filter, Jetzt prüfen, audit/read, Compliance, audit trail, audit console, who changed, history, actor, event, filter, verify now, compliance
context: audit/console
type: help
---
# Wie finde ich heraus, wer was wann geändert hat?

Jede Änderung im System wird in den Audit-Trail geschrieben: wer sie vorgenommen hat, an welchem Objekt, wann, von
welcher Adresse und mit welchem Ergebnis. Die **Audit-Konsole** findet die Ereignisse, die Sie brauchen.

## Wer das tun kann

Benutzer, deren Rolle die Richtlinie `audit/read` hat. Sie kann auf Kanäle beschränkt werden (zum Beispiel `content`,
aber nicht `access`), sodass Sie nur die Ereignisse sehen, die Ihre Rolle erlaubt.

## Schritte

1. Klicken Sie im oberen Menü auf **Audit** (oder **Mehr** > **Audit**).
   Das **Audit-Dashboard** öffnet sich mit dem Zustand, den Zahlen von heute und der Woche, Sicherheitsereignissen und
   Warnungen.
2. Klicken Sie im Menü **Audit** links auf **Konsole**.
3. Grenzen Sie die Liste ein:
   - **Login** oder **Benutzer-ID**: die Aktionen einer Person.
   - **Objekt**: ein Objekt oder Knoten, zum Beispiel `node:275`.
   - **Ereignisname**: eine Art von Ereignis, zum Beispiel `content.*` für Inhaltsänderungen.
   - **Von** und **Bis**: ein Zeitraum.
   - **Kanal**, **Ergebnis**, **Schweregrad mindestens**, **Adresse**, **Suchen** für den Rest.
4. Klicken Sie auf **Filtern**.
   Jede Zeile zeigt **Zeit**, **Ereignis**, **Akteur**, **Objekt**, **Ergebnis** und **Anfrage**.
5. Klicken Sie auf ein Ereignis, um alle seine Details zu sehen.
6. Um das Ergebnis aufzubewahren, klicken Sie auf **Exportieren**.
7. Um zu prüfen, dass niemand die Aufzeichnung verändert hat, klicken Sie auf **Jetzt prüfen**. Jeder Kanal sollte
   „intact“ zeigen.

![Die Audit-Konsole mit dem Filterformular und der Liste der Ereignisse mit Zeit, Ereignis, Akteur, Objekt, Ergebnis und Anfrage](../../../eng-us/help/manual/images/audit-console.webp)

## Was schiefgehen kann

- **Keine Ereignisse für eine Änderung, von der Sie wissen, dass sie stattgefunden hat.** Ihre Rolle darf diesen Kanal
  nicht lesen, oder der Zeitraum ist falsch. Leeren Sie den Filter mit **Zurücksetzen** und versuchen Sie einen
  größeren Zeitraum.
- **Ein Kanal zeigt nach Jetzt prüfen etwas anderes als „intact“.** Die gespeicherte Aufzeichnung passt nicht zu ihrer
  Hash-Kette. Ändern Sie nichts; benachrichtigen Sie sofort die Person, die für die Sicherheit verantwortlich ist.
- **Der Akteur ist „anonymous“.** Die Änderung kam von einem Cronjob oder einem Skript auf dem Server, nicht von einem
  angemeldeten Benutzer. Die Spalte **Anfrage** sagt, wovon.

## Verwandte Seiten

- [Der Audit-Trail](../../../eng-us/features/6.0/audit-trail.md)
- [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Verlauf von Inhalten](../../anleitungen/inhaltsverlauf.md)
- [Benutzerhandbuch](README.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
