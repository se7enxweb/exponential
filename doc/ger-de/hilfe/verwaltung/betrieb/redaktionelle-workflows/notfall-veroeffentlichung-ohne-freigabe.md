---
title: Notfallumgehung beim Veröffentlichen
audience: government, admin, project-manager
category: public-sector
order: 90
summary: Wie eine öffentliche Stelle dringende Informationen (eine Notfallmeldung, eine Schließung, eine Meldung zur öffentlichen Sicherheit) veröffentlichen kann, ohne auf die normale Freigabe zu warten, und dabei die Umgehung klein, bewusst und im Audit-Trail nachvollziehbar hält.
keywords: Notfallveröffentlichung, dringende Bekanntmachung, Notfallzugriff, Freigabe umgehen, ausgeschlossene Benutzergruppen, Krisenkommunikation, öffentliche Sicherheit, Audit, Freigabe-Workflow, nachträgliche Prüfung, emergency publishing, urgent notice, break glass, bypass approval, excluded user groups, crisis communication, public safety, approval workflow, after-the-fact review
type: help
---
# Notfallumgehung beim Veröffentlichen

Diese Seite richtet sich an Webverantwortliche und Kommunikationsleitungen, die dringende öffentliche Informationen sofort veröffentlichen können
müssen, auch wenn keine freigebende Person erreichbar ist, und dabei trotzdem ihre Steuerungsregeln einhalten. Exponential hat keinen eigenen
Notfallmodus; die Umgehung wird aus dem Feld **Gruppen ausschließen** des Freigabe-Ereignisses und dem Audit-Trail gebaut.
Quellen: [Workflows](../../../../anleitungen/workflows.md) und [Audit-Trail](../../../../../eng-us/features/6.0/audit-trail.md).

## Inhalt
1. [Der Entwurf: erst Notfallzugriff, dann Prüfung](#der-entwurf-erst-notfallzugriff-dann-prüfung)
2. [Einrichten](#einrichten)
3. [Verwenden](#verwenden)
4. [Nachträglich prüfen](#nachträglich-prüfen)
5. [Alternativen](#alternativen)
6. [Verwandte Seiten](#verwandte-seiten)

## Der Entwurf: erst Notfallzugriff, dann Prüfung

- Eine Benutzergruppe „Notfallveröffentlichung“ ist im Freigabe-Ereignis unter **Gruppen ausschließen** eingetragen: „Benutzer in diesen Gruppen
  veröffentlichen ohne Freigabe“ ([Workflows, Abschnitt 3.2](../../../../anleitungen/workflows.md#32-das-ereignis-freigeben-einrichten)).
- Die Gruppe ist im Normalbetrieb **leer** oder enthält zwei oder drei namentlich benannte Personen in Bereitschaft.
- Jemanden in die Gruppe aufzunehmen, ist der eigentliche Notfallzugriff. Es ist eine Aktion eines Administrators, und sie wird erfasst.
- Jede Veröffentlichung wird im Kanal `content` des Audit-Trails erfasst, sodass die anschließende Prüfung die Fakten hat.

## Einrichten

1. Legen Sie unter Benutzer die Benutzergruppe „Notfallveröffentlichung“ an. Geben Sie ihr keine Rollen: Die Mitglieder behalten ihre eigenen
   Redaktionsrollen; die Gruppe nimmt sie nur von der Freigabe aus.
2. Fügen Sie sie unter **Gruppen ausschließen** in jedem Freigabe-Ereignis hinzu, das öffentliche Inhalte schützt.
3. Machen Sie das Hinzufügen eines Mitglieds sichtbar. Ein Konto einer Gruppe hinzuzufügen bedeutet, eine Platzierung hinzuzufügen; schalten Sie
   die Audit-Ereignisse **Ort hinzugefügt** und **Hauptort geändert** ein:

   ```ini
   # settings/override/audit.ini.append.php
   [AuditEventSettings]
   Enabled[]=content.node.add
   Enabled[]=content.node.main
   ```

   ([Sicherheit und Audit, Schritt 3](../../../../anleitungen/sicherheit-und-audit.md#3-das-audit-protokoll-lesen-5-minuten)).
   Das Entfernen einer Platzierung wird standardmäßig erfasst.
4. Schreiben Sie das Verfahren auf: wer die Umgehung genehmigen darf, wer das Mitglied hinzufügt, wie lange die Mitgliedschaft dauert, wer prüft.

## Verwenden

1. Die genehmigende Führungskraft bittet einen Administrator, die Redakteurin oder den Redakteur zu „Notfallveröffentlichung“ hinzuzufügen (eine
   Platzierung des Benutzers in der Gruppe hinzufügen). Bei einer ständigen Bereitschaftsgruppe entfällt dieser Schritt.
2. Die Redakteurin oder der Redakteur veröffentlicht die Meldung; sie geht sofort live.
3. Der Administrator entfernt die zusätzliche Platzierung wieder, sobald die Meldung veröffentlicht ist.
4. Wenn Caches die Änderung verzögern, leeren Sie sie wie bei jeder dringenden Änderung ([Veralteter Cache](../vorfaelle/veralteter-cache.md)).

Ist nicht einmal eine Redakteurin oder ein Redakteur erreichbar, kann ein Administrator (voller Zugriff) veröffentlichen; die eigenen Inhalte der
Freigebenden und Administratoren unterliegen demselben Audit-Nachweis.

## Nachträglich prüfen

Innerhalb der vereinbarten Frist (zum Beispiel am nächsten Arbeitstag):

```bash
./console exp:audit search --name='content.*' --from='<time of the incident>' --limit=50 --allow-root-user
./console exp:audit search --name='content.node.*' --from='<time of the incident>' --allow-root-user
```

Oder im Tab **Audit**, **Konsole**, gefiltert nach dem Kanal `content` und dem Zeitfenster. Prüfen Sie: Wer wurde der Gruppe hinzugefügt, was wurde
veröffentlicht, wann endete die Mitgliedschaft. Die regulär freigebende Person liest dann die veröffentlichte Meldung und hält die nachträgliche
Freigabe in Ihrem Ticket fest.

## Alternativen

| Alternative | Wann |
|---|---|
| Eine ständige Bereitschaftsgruppe unter Gruppen ausschließen | häufiges dringendes Veröffentlichen (zum Beispiel ein Amt für öffentliche Sicherheit) |
| Eine Gruppe von Freigebenden, die groß genug ist, um zu jeder Uhrzeit erreichbar zu sein | wenn keine Umgehung akzeptabel ist |
| Eine vorab freigegebene Seite, die nur wieder sichtbar gemacht wird | bekannte Szenarien (Schließungen): vorab vorbereiten und freigeben, verborgen halten, bei Bedarf sichtbar machen (`content/hide`) |
| Wartungsmodus mit einer Meldung | die ganze Website muss eine einzige Meldung zeigen ([Wartungsmodus](../verfuegbarkeit/wartungsmodus-und-geplante-ausfallzeiten.md)) |

## Verwandte Seiten
- [Vier-Augen-Prinzip beim Veröffentlichen](vier-augen-prinzip-beim-veroeffentlichen.md)
- [Überblick über Freigabe-Workflows](freigabe-workflows-im-ueberblick.md)
- [Grundlagen der Reaktion auf Vorfälle](../vorfaelle/grundlagen-der-vorfallsbehandlung.md)
- [Audit-Trail](../../../../../eng-us/features/6.0/audit-trail.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
