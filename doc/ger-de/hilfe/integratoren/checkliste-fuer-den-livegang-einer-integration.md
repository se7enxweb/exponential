---
title: Checkliste für den Livegang einer Integration
audience: integrator, admin, project-manager
category: integration
order: 510
summary: Die Checkliste für den Livegang einer Exponential-Integration - Bereitschaft, die Erstbefüllung, das Einschalten von Zeitplänen und Hooks, Überwachung, die ersten Läufe, Rückabwicklung und die ersten Tage der Hypercare - mit den Befehlen für jeden Schritt.
keywords: Livegang, Start, Checkliste, Umstellung, Erstbefüllung, Zeitplan, Cronjob, Überwachung, Rückabwicklung, Rollback, Hypercare, Bereitschaft, Produktion, go-live, launch, checklist, cut-over, initial load, schedule, monitoring, rollback, readiness, production
type: help
---
# Checkliste für den Livegang einer Integration

Diese Seite ist für das Team, das eine Integration in Produktion bringt. Arbeiten Sie sie der Reihe nach durch; jeder
Abschnitt endet mit einem Entscheidungspunkt. Die Schritte für die Website als Ganzes stehen unter
[Livegang einer neuen Kundenwebsite](../hosting/checklisten/checkliste-fuer-den-start-einer-kunden-website.md).

## Inhalt

1. [Eine Woche vorher](#eine-woche-vorher)
2. [Am Vortag](#am-vortag)
3. [Livegang](#livegang)
4. [Erste Läufe](#erste-läufe)
5. [Rückabwicklung](#rückabwicklung)
6. [Hypercare](#hypercare)

## Eine Woche vorher

- [ ] Abnahmefälle in Staging bestanden; Freigabe festgehalten.
- [ ] Sicherheitsprüfung bestanden ([Checkliste](sicherheits-checkliste-fuer-integrationen.md)).
- [ ] Zugangsdaten für die Produktion angelegt und aufbewahrt; Dienstkonten und Rollen eingerichtet.
- [ ] Die Integrationserweiterung veröffentlicht; die zu installierende Version ist ein Tag.
- [ ] Überwachungsprüfungen gegen die Produktion konfiguriert, Alarme gehen an die richtigen Personen
      ([Überwachung](integrationen-ueberwachen.md)).
- [ ] Runbook von einem Betreiber geprüft ([Übergabe](uebergabe-einer-integration.md)).
- [ ] Die Verantwortlichen der anderen Systeme kennen den Termin und sind am Tag erreichbar.
- [ ] Plan für die Rückabwicklung geschrieben (siehe unten) und ein Sicherungsverfahren getestet
      ([Sicherungen: was zu sichern ist](../hosting/betrieb/backups-was-gesichert-werden-muss.md)).

**Entscheidung**: los / verschieben.

## Am Vortag

- [ ] Sicherung von Datenbank und `var/storage` angelegt und geprüft.
- [ ] Deployment in Staging mit denselben Befehlen geprobt.
- [ ] Ein Probelauf jedes Imports auf einer frischen Kopie der Produktion: Zahlen plausibel.
- [ ] Cronjob-Zeilen vorbereitet, aber noch nicht aktiv.

## Livegang

1. Deployen Sie die Erweiterung und ihre Einstellungen:

   ```bash
   php bin/php/ezpgenerateautoloads.php -e
   php bin/php/console exp:velocity deploy --allow-root-user
   ```

2. Legen Sie die Tabellen der Erweiterung an (mit ihrem Installationsbefehl) und prüfen Sie sie.
3. Prüfen Sie die Schnittstellen von außen (ein REST-Aufruf mit dem Produktionsschlüssel, ein `expservices`-Aufruf, die
   Feed-Adresse).
4. Führen Sie die **Erstbefüllung** von Hand aus, als Benutzer der Website, und behalten Sie die Ausgabe:

   ```bash
   nice -n 15 php bin/php/console ext:myintegration:importproducts --file=var/import/articles.csv -s <siteaccess> \
       2>&1 | tee var/log/myintegration-initial-load.log
   ```

5. Leeren Sie, was die Befüllung nicht selbst leert ([Caching nach Importen](caching-nach-importen.md#rezepte)); bauen
   Sie den Suchindex neu auf, wenn die Indizierung verzögert war.
6. Verbinden Sie Workflows mit Triggern, schalten Sie Listener und Senken ein.
7. Aktivieren Sie die Cronjob-Zeilen; prüfen Sie sie unter **Setup > Cronjobs**.

**Entscheidung**: weiter / zurückabwickeln.

## Erste Läufe

- [ ] Der erste geplante Lauf jedes Flusses hat stattgefunden, und seine Zusammenfassungszeile ist plausibel.
- [ ] Der zweite Lauf jedes Imports meldet (fast) nur `unchanged`.
- [ ] Die Teams der anderen Systeme bestätigen den Eingang der ersten ausgehenden Änderungen.
- [ ] Keine neuen Fehler mit dem Namen der Integration in `error.log`; keine Ablehnungen im Audit.
- [ ] Die Überwachung zeigt grün; ein Alarm wurde absichtlich ausgelöst und ist angekommen.

## Rückabwicklung

Schreiben Sie vor dem Livegang auf, wie Sie jeden Fluss stoppen und rückgängig machen:

| Schritt | Wie |
|---|---|
| Den Fluss stoppen | Seine Cronjob-Teile anhalten; seinen Workflow unter Setup > Trigger trennen; die Senke aus dem Kanal entfernen |
| Zugriff entziehen | Die API-Schlüssel der Integration widerrufen; das Dienstkonto deaktivieren |
| Importierte Inhalte rückgängig machen | Objekte über das Präfix ihrer Remote-ID mit einem vorbereiteten, getesteten Skript entfernen oder verbergen; oder die Sicherung wiederherstellen, wenn sich seitdem nichts anderes geändert hat |
| Die anderen informieren | Die Verantwortlichen der anderen Systeme und die Redakteure |

## Hypercare

- [ ] Tägliche Prüfung der Zusammenfassungszeilen jedes Flusses für den vereinbarten Zeitraum (zwei bis vier Wochen).
- [ ] Schwellenwerte an die echten Volumen angepasst.
- [ ] Fehler behoben und als neue Versionen veröffentlicht, nicht auf dem Server gepatcht.
- [ ] Offene Punkte am Ende der Hypercare mit Verantwortlichen übergeben.

## Verwandte Seiten

- [Playbook für Integrationsprojekte](playbook-fuer-integrationsprojekte.md)
- [Integrationen testen](integrationen-testen.md)
- [Qualitätstore und Abnahme](../fuehrung/qualitaetsschranken-und-abnahme.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
