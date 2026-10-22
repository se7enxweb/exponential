---
title: Playbook für Integrationsprojekte
audience: integrator, project-manager, partner
category: integration
order: 430
summary: Ein Playbook Phase für Phase für Integrationsprojekte rund um Exponential - Analyse, Schnittstellenentwurf, Umgebungen und Zugänge, Aufbau, Test, Livegang, Hypercare und Übergabe - mit Ergebnissen, den beteiligten Rollen auf beiden Seiten und den zu steuernden Risiken.
keywords: Projekt, Playbook, Integrationsprojekt, Phasen, Analyse, Schnittstellenspezifikation, Umgebungen, Aufbau, Test, Livegang, Hypercare, Übergabe, Rollen, RACI, Risiken, Projektmanagement, project, integration project, phases, discovery, interface specification, environments, build, go-live, handover, roles, risks, project management
type: help
---
# Playbook für Integrationsprojekte

Diese Seite ist für Projektleiter bei Integrationsunternehmen und für die Projektmanager der Kunden. Sie überträgt das
[Playbook für Implementierungsprojekte](../partner/leitfaden-fuer-implementierungsprojekte.md) des Partnerbereichs auf
Projekte, deren Hauptgegenstand die Verbindung zwischen Systemen ist. Es ist ein Playbook, keine Methode, der Sie
folgen müssen: Passen Sie es an Ihre Verträge an.

## Inhalt

1. [Die Phasen](#die-phasen)
2. [Rollen auf beiden Seiten](#rollen-auf-beiden-seiten)
3. [Zu steuernde Risiken](#zu-steuernde-risiken)
4. [Faustregeln](#faustregeln)

## Die Phasen

| Phase | Ergebnisse | Seiten |
|---|---|---|
| 1. Analyse | Systeme, Datenflüsse, Richtungen, Mengen, Zeitpunkte, Verantwortliche, Randbedingungen; das Analyse-Arbeitsblatt ausgefüllt | [Analyse-Arbeitsblatt](arbeitsblatt-zur-bestandsaufnahme-einer-integration.md), [Was Integratoren tun](was-integratoren-mit-exponential-tun.md) |
| 2. Schnittstellenentwurf | Eine Schnittstellenspezifikation pro Fluss: Schnittstelle, Zugangsdaten, Abbildung, Schlüssel, Fehler, Überwachung | [Vorlage für eine Schnittstellenspezifikation](vorlage-fuer-eine-schnittstellenspezifikation.md), [Eine Integrationsschnittstelle wählen](eine-integrationsschnittstelle-waehlen.md) |
| 3. Schätzung und Vereinbarung | Schätzung mit Annahmen, Leistungsbeschreibung, Abnahmekriterien | [Schätzen](integrationsaufwand-schaetzen.md), [Leistungsbeschreibungen](leistungsbeschreibungen-fuer-integrationen.md) |
| 4. Umgebungen und Zugänge | Entwicklungs- und Staging-Installationen aus Git; Testinstanzen der anderen Systeme; Dienstkonten; gespeicherte Zugangsdaten | [Mit Git entwickeln](../mit-git-entwickeln.md), [Dienstkonten](dienstkonten-rollen-und-richtlinien.md) |
| 5. Aufbau | Die Integrationserweiterung: Abbildung, Client, Import oder Sender, Hooks, Cronjob-Teile, Tests | [Eine Integration als Erweiterung bauen](eine-integration-als-erweiterung-bauen.md), [Einen Import schreiben](ein-importskript-schreiben.md) |
| 6. Test | Unit- und Vertragstests in der CI, Probeläufe auf einer Kopie, der Idempotenztest, End-to-End in Staging, Sicherheitsprüfung | [Testen](integrationen-testen.md), [Sicherheits-Checkliste](sicherheits-checkliste-fuer-integrationen.md) |
| 7. Livegang | Erstbefüllung, Zeitpläne an, Überwachung an, Rückfallplan bereit | [Checkliste zum Livegang](checkliste-fuer-den-livegang-einer-integration.md) |
| 8. Hypercare | Zwei bis vier Wochen enger Beobachtung; Korrekturen; Feinabstimmung der Schwellenwerte | [Überwachung](integrationen-ueberwachen.md) |
| 9. Übergabe | Runbook, Inventar der Zugangsdaten, Dokumentation, Schulung der Betreiber | [Übergabe einer Integration](uebergabe-einer-integration.md) |
| 10. Betrieb | Upgrades, Schlüsselrotation, Abgleich, Änderungen der anderen Systeme | [Upgrades](upgrades-ohne-defekte-integrationen.md) |

## Rollen auf beiden Seiten

| Rolle | Integrator | Kunde |
|---|---|---|
| Projektleitung | plant, berichtet, steuert den Umfang | entscheidet Prioritäten, unterzeichnet die Abnahme |
| Integrationsarchitekt | entwirft Flüsse und Schnittstellen | liefert die Systemlandschaft und Randbedingungen |
| Integrationsentwickler | baut und testet die Erweiterung | |
| Verantwortlicher für jedes andere System | | stellt Testinstanzen, Zugangsdaten, API-Dokumentation und einen Ansprechpartner, der innerhalb eines Tages antwortet |
| Datenverantwortlicher | | entscheidet über Stammdaten, Abbildung und Regeln für Löschungen |
| Betrieb | bereitet Runbook und Überwachung vor | betreibt nach der Übergabe (oder vergibt es) |
| Sicherheit und Datenschutz | folgt der Sicherheits-Checkliste | genehmigt Datenflüsse und Auftragsverarbeiter |

Die häufigste Ursache für Verzögerungen ist nicht der Code: Es ist das Warten auf den Zugang zur Testinstanz des
anderen Systems, auf seine Zugangsdaten oder auf eine Antwort zu seinen Daten. Tragen Sie diese Termine als
Lieferungen des Kunden in den Plan ein.

## Zu steuernde Risiken

| Risiko | Gegenmaßnahme |
|---|---|
| Die API des anderen Systems ist nicht dokumentiert oder ändert sich | Vertragstests mit aufgezeichneten Antworten; ein benannter Ansprechpartner; eine Klausel zur Ankündigung von Änderungen in der Vereinbarung |
| Die Datenqualität an der Quelle ist schlechter als angenommen | Probelauf mit echten Daten in der Analyse; ein Budgetposten für Bereinigungsregeln; ein Bericht über abgewiesene Datensätze |
| Die Mengen sind größer als angenommen | in der Analyse messen; Batch und Delta von Anfang an einplanen |
| Keine Testinstanz des anderen Systems | ein Testdouble für die Entwicklung; ein kurzes, beaufsichtigtes Zeitfenster in der Produktion mit Nur-Lese-Zugangsdaten |
| Redakteure ändern importierte Felder | Zuständigkeit für jedes Feld in der Abbildung; Rollen, die es verhindern; Schulung |
| Abhängigkeit von Schlüsselpersonen | alles im Repository und in der Dokumentation der Erweiterung; Pairing |
| Stiller Ausfall nach dem Livegang | Überwachung mit Alarmen vor dem Livegang, nicht nach dem ersten Vorfall |

## Faustregeln

- Bauen Sie zuerst den schmalsten durchgehenden Fluss (ein Datensatz, ein Feld, den ganzen Weg), dann verbreitern Sie
  ihn.
- Vereinbaren Sie die Regel für Löschungen vor dem ersten Import; sie ist die am häufigsten vergessene Entscheidung.
- Gehen Sie nie an einem Freitag oder vor einem Feiertag live.
- Planen Sie Hypercare im Budget ein; dann werden die Schwellenwerte abgestimmt.
- Beziehen Sie das eigene Team des Kunden ein; es wird die Integration betreiben.

## Verwandte Seiten

- [Playbook für Implementierungsprojekte](../partner/leitfaden-fuer-implementierungsprojekte.md)
- [Ein Exponential-Projekt planen](../fuehrung/ein-exponential-projekt-planen.md)
- [Projektvorlagen](../fuehrung/projektvorlagen.md)
- [Risikomanagement](../fuehrung/risikomanagement.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
