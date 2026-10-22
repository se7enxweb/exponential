---
title: Übergabe einer Integration
audience: integrator, admin, project-manager
category: integration
order: 460
summary: Eine Integration an die Menschen übergeben, die sie betreiben werden - das Übergabepaket (Code, Dokumentation, Runbook, Überwachung, Verzeichnis der Zugangsdaten), die Abschnitte des Runbooks, die sichere Übergabe von Zugangsdaten, Schulung und die Checkliste, die das Projekt abschließt.
keywords: Übergabe, Runbook, Betrieb, Dokumentation, Verzeichnis der Zugangsdaten, Übergabe von Geheimnissen, Schulung, Wissenstransfer, Verantwortung, Abnahme, Projektende, handover, hand-over, operations, credentials inventory, secret transfer, training, knowledge transfer, ownership, acceptance, end of project
type: help
---
# Übergabe einer Integration

Diese Seite ist für Integratoren, die ein Projekt abschließen, und für die Betreiber des Kunden, die es übernehmen.
Eine Integration, die nur ihr Erbauer versteht, ist ein Risiko für den Kunden; die Übergabe macht daraus etwas, das
die Betreiber betreiben, ändern und bei Bedarf einem anderen Dienstleister geben können. Kunden dürfen jederzeit zu
einem anderen Partner wechseln
([Playbook für Implementierungsprojekte](../partner/leitfaden-fuer-implementierungsprojekte.md#partnerspezifische-prüfungen-je-phase));
eine gute Übergabe macht das möglich.

## Inhalt

1. [Das Übergabepaket](#das-übergabepaket)
2. [Das Runbook](#das-runbook)
3. [Zugangsdaten](#zugangsdaten)
4. [Schulung](#schulung)
5. [Die Übergabe-Checkliste](#die-übergabe-checkliste)

## Das Übergabepaket

| Element | Inhalt |
|---|---|
| Code | Die Integrationserweiterung(en) im Repository des Kunden, mit Historie; die veröffentlichte Version installiert |
| Schnittstellenspezifikationen | Eine pro Fluss, auf den gebauten Stand gebracht |
| Verzeichnis der Einstellungen | Jede Einstellung, die die Integration liest, ihr Wert pro Umgebung und wo sie gesetzt ist (`settings/override`, Siteaccess, Erweiterung) |
| Verzeichnis der Zugangsdaten | Jedes Zugangsdatum: wofür es ist, welches Konto, wo es aufbewahrt wird, wann es abläuft, wer es rotieren darf (nie die Geheimnisse selbst im Dokument) |
| Dienstkonten und Rollen | Konten, Gruppen, Rollen und ihre Richtlinien |
| Zeitpläne | Crontab-Zeilen, Cronjob-Teile, Worker der Warteschlange |
| Überwachung | Prüfungen, Schwellenwerte, Empfänger der Alarme |
| Runbook | Siehe unten |
| Tests | Wie man sie ausführt; die Abnahmefälle |
| Offene Punkte | Bekannte Probleme, geplante Änderungen, der Termin der nächsten Schlüsselrotation |

## Das Runbook

Das Runbook beantwortet die Fragen des Betreibers um 3 Uhr morgens. Ein Abschnitt pro Fluss, und in jedem:

```text
Flow: ERP products into Exponential
1  What it does (one paragraph) and who owns the data
2  Schedule: 02:15 full, hourly delta 07:00-19:00 (cronjob parts myintegration_import, myintegration_delta)
3  How to see that it ran: var/log/myintegration.log summary line; Setup > Cronjobs
4  Alerts and what they mean
   4.1 "No run since ..."          -> check crontab, Setup > Cronjobs (paused?), the part's output
   4.2 "Stopped: N rows for M"     -> the ERP file is truncated; ask the ERP team; do not force the run
   4.3 "failed > 0"                -> exp:logs --search=erp-product --file=var/log/error.log
5  How to run it by hand: php bin/php/console ext:myintegration:importproducts --file=... --dry-run -s <siteaccess>
6  How to pause and resume: Setup > Cronjobs, Pause on the part's card
7  How to rotate the credential: steps, and where it is stored
8  Contacts: ERP team, integrator support, escalation
```

## Zugangsdaten

- Übergeben Sie Geheimnisse über einen mit dem Kunden vereinbarten Kanal (seinen Passwortmanager oder
  Geheimnisspeicher), nie per E-Mail oder Chat.
- **Rotieren** Sie nach der Übergabe jedes Zugangsdatum, das Mitarbeiter des Integrators kannten: neue API-Schlüssel,
  neue Passwörter, neue Signaturgeheimnisse; widerrufen Sie die alten und prüfen Sie das Audit auf ihre Nutzung.
- Entfernen Sie Konten von Mitarbeitern des Integrators aus der Administration und von den Servern, sofern kein
  Wartungsvertrag sie erhält; dann halten Sie sie persönlich, nie gemeinsam genutzt.

## Schulung

| Zielgruppe | Inhalt | Dauer |
|---|---|---|
| Betreiber | Durchgang durch das Runbook, ein simulierter Ausfall pro Fluss, Anhalten und Fortsetzen, Protokolle und Audit | Ein halber Tag |
| Redakteure | Welche Felder importiert werden und nicht bearbeitet werden dürfen; wie man Datenprobleme meldet | Eine Stunde |
| Entwickler (falls der Kunde welche hat) | Die Struktur der Erweiterung, Tests, Deployment, das Upgrade-Verfahren | Ein halber Tag |

## Die Übergabe-Checkliste

- [ ] Code im Repository des Kunden; die installierte Version entspricht einem Release-Tag.
- [ ] Schnittstellenspezifikationen, Verzeichnisse der Einstellungen und Zugangsdaten aktualisiert und übergeben.
- [ ] Runbook von einem Betreiber geprüft, der die Integration nicht gebaut hat.
- [ ] Überwachungsalarme gehen an die Betreiber des Kunden.
- [ ] Zugangsdaten nach der Übergabe rotiert; Zugang des Integrators entfernt oder durch einen Wartungsvertrag
  geregelt.
- [ ] Betreiber geschult; ein simulierter Ausfall von ihnen behoben.
- [ ] Abnahme unterzeichnet; offene Punkte mit Verantwortlichen aufgelistet.

## Verwandte Seiten

- [Integrationen überwachen](integrationen-ueberwachen.md)
- [Die Website an Redakteure übergeben](../ersteller/website-ersteller/die-website-an-die-redaktion-uebergeben.md)
- [Playbook für Integrationsprojekte](playbook-fuer-integrationsprojekte.md)
- [Kundendaten schützen](../partner/kundendaten-schuetzen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
