---
title: Änderungsmanagement und Änderungsbeiräte
audience: government, admin, project-manager, hosting
category: public-sector
order: 40
summary: Wie Sie Exponential unter dem Änderungsmanagement einer öffentlichen Stelle betreiben - welche Änderungen an einen Änderungsbeirat gehen, welche Standard- oder Routineänderungen sind, welche Nachweise die Plattform für jede liefert (Probeläufe, Upgrade-Prüfung, Audit-Trail) und eine Vorlage für den Änderungsdatensatz.
keywords: Änderungsmanagement, Änderungsbeirat, CAB, ITIL, Änderungsantrag, Standardänderung, normale Änderung, Notfalländerung, Änderungsdatensatz, Freigabe, Nachweis, Probelauf, Audit-Trail, Bereitstellung, change management, change advisory board, change request, standard change, normal change, emergency change, change record, approval, evidence, dry run, audit trail, deploy
type: help
---
# Änderungsmanagement und Änderungsbeiräte

Diese Seite richtet sich an Change-Manager, Verantwortliche für Dienste und Administratoren in öffentlichen Stellen,
die einen Änderungsbeirat (CAB) oder einen ähnlichen Freigabeschritt betreiben. Sie ordnet die Änderungen, die eine
Exponential-Website erlebt, nach Arten, sagt, welche üblicherweise den Beirat brauchen, und zeigt die Nachweise, die
die Plattform für jede erzeugt. Änderungsprozesse unterscheiden sich zwischen Organisationen; passen Sie die
Kategorien an Ihre an.

## Inhalt
1. [Drei Arten von Änderungen](#drei-arten-von-änderungen)
2. [Welche Änderung welche ist](#welche-änderung-welche-ist)
3. [Nachweise, die die Plattform liefert](#nachweise-die-die-plattform-liefert)
4. [Eine Vorlage für den Änderungsdatensatz](#eine-vorlage-für-den-änderungsdatensatz)
5. [Notfalländerungen](#notfalländerungen)
6. [Verwandte Seiten](#verwandte-seiten)

## Drei Arten von Änderungen

| Art | Bedeutung in vielen Änderungsprozessen | Freigabe |
|---|---|---|
| Standard | vorab freigegeben, geringes Risiko, nach einem schriftlichen Ablauf wiederholt | keine pro Änderung; der Ablauf wurde einmal freigegeben |
| Normal | vom Beirat bewertet und freigegeben | der Beirat |
| Notfall | sofort nötig, um den Dienst wiederherzustellen oder eine Lücke zu schließen | eine Notfall-Freigebende oder ein Notfall-Freigebender, nachträglich geprüft |

## Welche Änderung welche ist

| Änderung an einer Exponential-Website | Übliche Art | Warum |
|---|---|---|
| Inhalte veröffentlichen | keine Änderung; durch den redaktionellen Workflow gesteuert | [Überblick über Freigabe-Workflows](../redaktionelle-workflows/freigabe-workflows-im-ueberblick.md) |
| Caches leeren, einen Cronjob-Teil ausführen, Logs rotieren | Standard | umkehrbar, dokumentiert ([Betriebsaufgaben](../../../../../eng-us/help/government/tasks/operations/README.md)) |
| Einen Benutzer hinzufügen, einer Gruppe eine vorhandene Rolle zuweisen | Standard (nach Ihrem Eintrittsablauf) | im Audit-Trail aufgezeichnet |
| Eine neue Rolle, eine neue Sektion, ein geänderter Workflow | normal | ändert, wer was tun darf |
| Eine Änderung an Einstellungen (INI) | normal | ändert das Verhalten; mit [ManagedByDeployment](verwaltet-durch-bereitstellung.md) kommt sie über die Versionsverwaltung |
| Eine Änderung an Template oder Design | normal | ändert jede Seite |
| Eine neue oder aktualisierte Erweiterung | normal | neuer Code ([Aktualisierungen von Erweiterungen](aktualisierung-von-erweiterungen.md)) |
| Eine Aktualisierung von Exponential | normal, oft umfangreich | Datenbankaktualisierung, neuer Code ([Überblick über Aktualisierungen](aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md)) |
| Aktualisierung von PHP oder Datenbankserver | normal | [Aktualisierungen von PHP und Datenbank](php-und-datenbank-aktualisierungen.md) |
| Ein Release mit Sicherheitskorrekturen | normal, beschleunigt oder Notfall | [Sicherheitsupdates und Offenlegung](sicherheitsaktualisierungen-und-offenlegung.md) |
| Wartungsmodus für ein geplantes Fenster | Teil der freigegebenen Änderung | [Wartungsfenster](wartungsfenster.md) |

## Nachweise, die die Plattform liefert

| Nachweis | Befehl oder Seite | Verwendung in der Änderung |
|---|---|---|
| Was eine Bereitstellung tun wird | `./console exp:velocity deploy --dry-run --allow-root-user` (vierzehn Schritte, jeder mit DRY markiert) | dem Antrag beifügen |
| Was das Leeren eines Caches tun wird | `php bin/php/cache.php clear --all --dry-run --allow-root-user` | Standardablauf |
| Was ein Update installieren wird | **Setup > Updates und Pakete**, **Vorschau (Probelauf)** (ezupdate) | dem Antrag beifügen |
| Code weicht vom Release ab | **Setup > Upgrade-Prüfung**, **Überprüfe Dateikonsistenz**; `php bin/php/checkmanifest.php --all` | vorher und nachher |
| Datenbankschema weicht ab | **Überprüfe Datenbankkonsistenz** auf derselben Seite | vorher und nachher |
| Wer es wann getan hat | der Kanal `system` des Audit-Trails (Schreibvorgänge an Einstellungen, Leeren von Caches, Bereitstellungen, Wartung) | Abschluss der Änderung |
| Der Zustand des Servers | **Setup > Systeminformationen**, **Bericht herunterladen (.txt)** | vorher und nachher |

Die Seiten der Upgrade-Prüfung sind unter [Die Upgrade-Prüfung](../../../../anleitungen/upgrade-pruefung.md) beschrieben;
Läufe werden im Audit-Trail als `system.upgrade.run` aufgezeichnet.

## Eine Vorlage für den Änderungsdatensatz

| Feld | Beispiel (Platzhalter) |
|---|---|
| Zusammenfassung | „Exponential auf www.example.gov von 6.0.14 auf das nächste Release 6.0.x aktualisieren“ |
| Grund | im Changelog aufgeführte Sicherheitskorrekturen |
| Art und Risiko | normal; mittel (Datenbankaktualisierung) |
| Umgebungen | auf test.example.gov am <Datum> getestet, Ergebnis beigefügt |
| Plan | Wartungsfenster, Datensicherung, Schritte, Prüfungen ([Überblick über Aktualisierungen](aktualisierungen-im-oeffentlichen-sektor-im-ueberblick.md)) |
| Rücknahme | die in Schritt 1 erstellte Datensicherung wiederherstellen ([Rücknahme planen](planung-der-ruecknahme.md)) |
| Nachweise vorher | Upgrade-Prüfung, Bericht der Systeminformationen, Ausgabe des Probelaufs |
| Nachweise nachher | dieselben, plus die Einträge des Audit-Trails aus dem Wartungsfenster |
| Kommunikation | Hinweis an die Öffentlichkeit und an die Redaktion ([Wartungsfenster](wartungsfenster.md)) |

## Notfalländerungen

Auch eine Notfalländerung hinterlässt einen Datensatz: Der Audit-Trail erfasst Schreibvorgänge an Einstellungen, das
Leeren von Caches, Wartung und Bereitstellungen mit Zeitpunkt und Benutzer, sodass sich die nachträgliche Prüfung
darauf stützen kann. Listen Sie auf, was im Fenster geschehen ist:

```bash
./console exp:audit search --channel=system --from='<start of the window>' --to='<end>' --limit=100 --allow-root-user
```

Die Filter von `search` sind in [Abschnitt 3.3 des Audit-Leitfadens](../../../../bc/6.0/audit.md#33-der-befehl-expaudit)
aufgeführt.

## Verwandte Seiten
- [Staging und Freigabe](staging-und-freigabe.md)
- [Durch die Bereitstellung verwaltete Konfiguration](verwaltet-durch-bereitstellung.md)
- [Funktionstrennung](../personen-und-berechtigungen/funktionstrennung.md)
- [Änderungen bereitstellen (Hosting)](../../../hosting/betrieb/aenderungen-ausrollen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
