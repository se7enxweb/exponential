---
title: Eine Website über einen mehrjährigen Vertrag aktuell halten
audience: government, project-manager, admin
category: public-sector
order: 80
summary: Ein Jahresrhythmus, mit dem eine öffentliche Stelle eine Exponential-Website über einen Vertrag von mehreren Jahren aktuell hält - Updates, Plattform, Zugänge, Inhaltsprüfungen, Dokumentation und Schulung - mit den Seiten der Administration und den Befehlen, die jede Aufgabe unterstützen.
keywords: Lebenszyklusmanagement, mehrjähriger Vertrag, Wartungsrhythmus, Jahresplan, Updates, Zustandsprüfungen, Systeminformationen, Inhaltsprüfungen, Dokumentation, Governance, öffentlicher Sektor, lifecycle management, multi-year contract, maintenance rhythm, yearly plan, updates, health checks, system information, content checks, documentation, governance, public sector
type: help
---
# Eine Website über einen mehrjährigen Vertrag aktuell halten

Diese Seite richtet sich an das Vertragsmanagement und die für die Website verantwortliche Person einer öffentlichen
Stelle. Viele öffentliche Websites werden gut gebaut und dann sich selbst überlassen, bis sie zum Risiko werden. Diese
Seite gibt einen Rhythmus, der die Website Jahr für Jahr aktuell hält, mit den Werkzeugen, die Exponential für jede
Aufgabe bietet. Die betrieblichen Einzelheiten stehen in [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)
und [10. Nach der Installation: das Betriebshandbuch](../../../../installation/10-nach-der-installation.md).

## Inhalt

1. [Der Rhythmus](#der-rhythmus)
2. [Jeden Monat](#jeden-monat)
3. [Jedes Quartal](#jedes-quartal)
4. [Jedes Jahr](#jedes-jahr)
5. [Wer wofür verantwortlich ist](#wer-wofür-verantwortlich-ist)
6. [Berichte des Lieferanten](#berichte-des-lieferanten)

## Der Rhythmus

| Zeitraum | Schwerpunkt |
|---|---|
| Laufend | Sicherheitskorrekturen, sobald sie veröffentlicht werden |
| Monatlich | Zustand, Logdateien, Sicherungen |
| Vierteljährlich | Überprüfung der Zugänge, Inhaltsprüfungen, routinemäßiges Update |
| Jährlich | Plattform, Dokumentation, Schulung, Ausstiegsplan, Überprüfung des Vertrags |

## Jeden Monat

- **Zustand.** **Setup > Systeminformationen** (`/setup/info`) zeigt Zustandsprüfungen und was Aufmerksamkeit braucht
  ([Systeminformationen](../../../../anleitungen/systeminformationen.md)).
- **Logdateien.** **Setup > Logdateien** listet die Logdateien mit einer Fehlerübersicht und dem Zustand der Logs
  ([Log-Betrachter](../../../../../eng-us/features/6.0/log-viewer.md)).
- **Audit.** Das Audit-Dashboard (`/audit/dashboard`) zeigt Sicherheitsereignisse und Alarme
  ([Audit-Protokoll](../../../../../eng-us/features/6.0/audit-trail.md)).
- **Sicherungen.** Prüfen Sie, dass sie gelaufen sind, und stellen Sie hin und wieder eine wieder her
  ([Wiederherstellung und Wiederherstellungstests](../../../hosting/betrieb/wiederherstellung-und-wiederherstellungstests.md)).
- **Cronjobs.** **Setup > Cronjobs** zeigt, ob die geplanten Teile gelaufen sind ([Cronjobs](../../../../anleitungen/cronjobs.md)).

Hoster haben eine passende Liste: [Monatlicher Betrieb](../../../hosting/checklisten/checkliste-fuer-den-monatlichen-betrieb.md).

## Jedes Quartal

- **Überprüfung der Zugänge.** Wer hat welche Rolle; Ausgeschiedene entfernen. **Benutzerkonten prüfen**
  (`/user/hygiene`) findet Konten, die seit einer Anzahl von Tagen nicht benutzt wurden ([Benutzerkonten prüfen](../../../../../eng-us/features/6.0/user-hygiene.md)); Rollen stehen unter
  `/role/list` ([Rollen und Richtlinien](../../../../anleitungen/rollen-und-richtlinien.md)). Der Abschnitt mit den Checklisten
  für Verwaltungen hat eine Checkliste für die vierteljährliche Überprüfung der Zugänge.
- **Inhaltsprüfungen.** Defekte Links und Beziehungen, nicht referenzierte Objekte, fehlende Dateien
  ([Inhaltsprüfungen](../../../../../eng-us/features/6.0/content-checks.md), [Fehlende Dateien](../../../../anleitungen/fehlende-dateien.md)).
- **Routinemäßiges Update** auf das neueste 6.0.x, falls noch nicht geschehen ([Upgrades im Haushalt planen](upgrades-im-haushalt-planen.md)).

## Jedes Jahr

- **Plattform.** Werden PHP, Datenbank und Betriebssystem in ihren Versionen noch unterstützt? ([Unterstützung von PHP und Plattform](php-und-plattformunterstuetzung.md))
- **Sicherheitsüberprüfung.** Härtungseinstellungen noch wie vereinbart ([Sicherheitshärtung](../../../sicherheitshaertung.md));
  Hoster: [Sicherheitsüberprüfung](../../../hosting/checklisten/checkliste-fuer-die-sicherheitspruefung.md).
- **Barrierefreiheit.** Wichtige Seiten und Templates nach Designänderungen erneut testen
  ([Die Barrierefreiheit testen](../../../../../eng-us/help/creators/website-creators/testing-accessibility.md)); Ihre Erklärung zur
  Barrierefreiheit aktualisieren.
- **Dokumentation.** Ist die Dokumentation der Website (Inhaltsmodell, Rollen, Einstellungen, Betriebshandbücher)
  aktuell? Liegt sie in Ihren Händen, nicht nur bei dem Lieferanten?
- **Schulung.** Neue Beschäftigte geschult, Material nach Upgrades aktualisiert (der Teil zur Schulung dieses
  Abschnitts zur Beschaffung).
- **Ausstiegsplan.** Einen Exporttest durchführen und den Plan aktualisieren (der Teil zu Ausstiegsplänen dieses
  Abschnitts zur Beschaffung).
- **Überprüfung des Vertrags.** Service-Levels eingehalten? Kosten wie geplant?

Die Checkliste für die jährliche Überprüfung im Abschnitt mit den Checklisten für Verwaltungen fasst diese Punkte auf
einer Seite zusammen.

## Wer wofür verantwortlich ist

| Aufgabe | Typischerweise verantwortlich |
|---|---|
| Sicherheitskorrekturen und routinemäßige Updates | der Lieferant oder die hauseigene Administration der Website |
| Plattform | der Host |
| Überprüfung der Zugänge | die für die Website verantwortliche Person, mit der Administration der Website |
| Inhaltsprüfungen | die redaktionelle Leitung |
| Barrierefreiheit | die für die Website verantwortliche Person, mit dem Design |
| Dokumentation und Ausstiegsplan | das Vertragsmanagement |

Schreiben Sie die Verantwortlichen in den Vertrag oder die Leistungsbeschreibung. Eine Aufgabe ohne Verantwortliche
wird nicht erledigt.

## Berichte des Lieferanten

Verlangen Sie einen kurzen regelmäßigen Bericht, zum Beispiel:

```text
Site report - <site> - <period>
Versions: Exponential <x>, extensions <list>, PHP <x>, database <x>   (from Setup > System information)
Updates applied: <release, date>        Security updates: <release, date applied>
Backups: <last successful>, restore test <date>
Incidents: <list>                        Open risks: <list>
Next period: <planned work>
```

## Verwandte Seiten

- [Der Lebenszyklus des Produkts](README.md)
- [Eine Website betreiben](../../../../anleitungen/betrieb-einer-website.md)
- [Ihre Website pflegen](../../../../../eng-us/help/creators/website-creators/maintaining-your-website.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
