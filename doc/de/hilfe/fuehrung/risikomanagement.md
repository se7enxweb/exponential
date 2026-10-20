---
title: Risikomanagement für Exponential-Projekte
audience: project-manager
category: leadership
order: 110
summary: Ein Risikoregister für Exponential-Projekte - die üblichen Risiken beim Aufbau und Betrieb einer Site, jeweils mit Eintrittswahrscheinlichkeit, Auswirkung und einer Gegenmaßnahme, die auf eine tatsächliche Funktion, Seite oder einen Befehl des Produkts verweist.
keywords: Risiko, Risikoregister, Risikomanagement, Eintrittswahrscheinlichkeit, Auswirkung, Gegenmaßnahme, Backup, Upgrade, Sicherheit, Audit, Herstellerabhängigkeit, Abhängigkeiten, Kompetenzen, Deployment, Konfigurationsabweichung, Notfallplan, risk, risk register, risk management, likelihood, impact, mitigation, security, vendor lock-in, dependencies, skills, deployment, configuration drift, contingency
type: help
---
# Risikomanagement für Exponential-Projekte

Diese Seite richtet sich an Projektleiter, die ein Risikoregister führen. Sie listet die Risiken auf, die immer wieder
auftreten, wenn eine Organisation eine Site mit Exponential aufbaut, in Betrieb nimmt und betreibt, und nennt für
jedes eine **Gegenmaßnahme, die das Produkt oder seine Dokumentation bereits bietet**: eine Funktion, eine Seite in der
Administration, einen Befehl oder ein Verfahren.

Die Spalten für Eintrittswahrscheinlichkeit und Auswirkung sind ein **Ausgangspunkt** für Ihre eigene Einschätzung,
keine Messwerte. Die Quellen veröffentlichen keine Statistiken über Vorfälle; die tatsächlichen Werte bestimmt die
Lage Ihres eigenen Projekts. Jede Gegenmaßnahme dagegen verweist auf die Seite, die sie beschreibt.

## Inhalt

1. [So verwenden Sie dieses Register](#so-verwenden-sie-dieses-register)
2. [Die Skalen](#die-skalen)
3. [Das Risikoregister](#das-risikoregister)
4. [Risiken im Detail](#risiken-im-detail)
5. [Rhythmus der Risikoüberprüfung](#rhythmus-der-risikoüberprüfung)
6. [Ein leeres Register zum Kopieren](#ein-leeres-register-zum-kopieren)

## So verwenden Sie dieses Register

1. **Kopieren** Sie das Register in das eigene Dokument Ihres Projekts (das leere Formular steht am Ende dieser Seite).
2. **Entfernen** Sie, was nicht zutrifft: Eine Site ohne Shop streicht die Risiken des E-Commerce.
3. **Bewerten** Sie jedes Risiko für Ihr Projekt: Eintrittswahrscheinlichkeit und Auswirkung, mit einer Begründung.
4. **Weisen** Sie jedem Risiko und jeder Gegenmaßnahme einen Verantwortlichen aus Ihrem Team **zu**.
5. **Überprüfen** Sie das Register im unten genannten Rhythmus und immer dann, wenn eine Schranke passiert wird.

Risiken, die Ihre Organisation betreffen (Budget, Personalwechsel, Beschaffung, Recht), stehen nicht auf dieser
Seite. Ergänzen Sie sie; diese Seite behandelt nur, wozu sich das Produkt und seine Dokumentation äußern können.

## Die Skalen

| Wert | Eintrittswahrscheinlichkeit | Auswirkung |
|---|---|---|
| 1 Niedrig | Während des Projekts unwahrscheinlich | Unannehmlichkeit; kein Besucher und kein Redakteur bemerkt etwas |
| 2 Mittel | Kann durchaus einmal passieren | Redakteure oder Besucher sind eine Zeit lang betroffen; Arbeit muss wiederholt werden |
| 3 Hoch | Zu erwarten, wenn nichts unternommen wird | Datenverlust, ein Sicherheitsvorfall, ein verpasster Livegang oder eine Site, die sich nicht pflegen lässt |

**Priorität** = Eintrittswahrscheinlichkeit x Auswirkung. Behandeln Sie 6 und 9 zuerst.

## Das Risikoregister

Die Werte in den Spalten W (Eintrittswahrscheinlichkeit, im Formular L) und A (Auswirkung, im Formular I) sind
vorgeschlagene Standardwerte für ein typisches Projekt.

| ID | Risiko | W | A | Gegenmaßnahme im Produkt | Wo sie beschrieben ist |
|---|---|---|---|---|---|
| R01 | Daten gehen verloren: ein fehlgeschlagenes Upgrade, eine versehentliche Löschung, eine defekte Festplatte | 2 | 3 | Datenbank und Dateien gemeinsam im Wartungsmodus sichern; eine Wiederherstellung auf einem anderen Rechner testen; der Backup-Manager von git_manager für Teams ohne Shell-Zugang | [Eine Site betreiben, „Back up and restore“](../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen), [10.9](../../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung), [git_manager](../../../en/us/features/6.0/extensions/git_manager.md) |
| R02 | Inhalte versehentlich entfernt | 2 | 2 | Entfernte Inhalte landen zuerst im Papierkorb; der Papierkorb hält fest, wer sie wo entfernt hat; das Audit-Protokoll verzeichnet jede Entfernung | [Die Papierkorb-Ansicht](../../../en/us/bc/6.0/trash.md), [Das Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md) |
| R03 | Ein Upgrade beschädigt die Site | 2 | 3 | Generalprobe auf einer Kopie; die Upgrade-Prüfung vorher und nachher; Überprüfungsbefehle; die Regel „nie einen Installer über eine bestehende Site ausführen“ | [Release- und Upgrade-Planung](release-und-upgrade-planung.md), [11.7](../../installation/11-aktualisieren.md#117-das-upgrade-überprüfen), [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| R04 | Eine Verhaltensänderung überrascht die Benutzer nach einem Upgrade (zum Beispiel Konten, die sich nicht mehr anmelden können) | 2 | 2 | Changelog und Hinweise zu Verhaltensänderungen jedes durchlaufenen Zeitraums lesen; das Upgrade-Kapitel listet die häufigsten Änderungen auf | [11.6 Schritt 3](../../installation/11-aktualisieren.md#schritt-3-die-checkliste-jedes-durchlaufenen-zeitraums-lesen) |
| R05 | Eine bekannte Sicherheitslücke in einem Release, das Sie betreiben | 2 | 3 | Sicherheitskorrekturen erscheinen als nächstes 6.0.x-Release; Releases verfolgen; `composer audit`; Prüfung der Dateikonsistenz nach jedem Update | [SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md), [Sicherheitshärtung](../sicherheitshaertung.md#veraltete-abhängigkeiten-und-die-lieferkette) |
| R06 | Ein Sicherheitsproblem, das Sie gefunden haben, wird öffentlich, bevor es behoben ist | 1 | 3 | Vertraulich melden, wie SECURITY.md es beschreibt, nie in einem öffentlichen Tracker | [Wenn etwas schiefgeht, „Sicherheitsprobleme“](../wenn-etwas-schiefgeht.md#sicherheitsprobleme) |
| R07 | Brute-Force-Angriffe oder Credential Stuffing auf die Anmeldung | 2 | 3 | Behandlung fehlgeschlagener Anmeldungen, zweistufige Anmeldung, ein Konto pro Person; Audit-Alarmregel für Brute Force | [Sicherheitshärtung](../sicherheitshaertung.md#brute-force-und-credential-stuffing-bei-der-anmeldung), [Zwei-Faktor-Anmeldung](../../anleitungen/zwei-faktor-anmeldung.md), [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md) |
| R08 | Jemand erhält mehr Rechte als vorgesehen | 2 | 3 | Rollen aus beschränkten Richtlinien; `PreventPrivilegeEscalation` (standardmäßig aktiv) verhindert, dass Benutzer Rechte vergeben, die sie selbst nicht haben; die Rollenliste markiert Vollzugriff und Rollen, die Rollen ändern können | [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md) |
| R09 | Eine Änderung, die niemand erklären kann („Wer war das?“) | 2 | 2 | Das Audit-Protokoll, standardmäßig aktiv: Kanäle für Inhalte, Zugriff, System und Commerce, als Hash-Kette verknüpft und überprüfbar | [Das Audit-Protokoll](../../../en/us/features/6.0/audit-trail.md) |
| R10 | Cross-Site-Scripting über Templates oder Erweiterungen | 2 | 3 | Content Security Policy, in der Administration standardmäßig im Modus „nur berichten“, Berichte gesammelt in `csp.log`; schrittweise auf enforce umstellen | [Content Security Policy](../../../en/us/features/6.0/content-security-policy.md) |
| R11 | Debug-Ausgaben oder Fehlerdetails werden Besuchern angezeigt | 2 | 2 | Die Gesundheitsprüfungen der Systeminformationen melden Debug-Ausgaben, Entwicklungseinstellungen und `display_errors` | [Systeminformationen, „Gesundheitsprüfungen“](../../anleitungen/systeminformationen.md#3-zustandsprüfungen), [13.4](../../installation/13-sicherheitshaertung.md#134-debug-ausgabe-und-fehleranzeige) |
| R12 | In der Administration geänderte Einstellungen gehen beim nächsten Deployment verloren oder unterscheiden sich zwischen Servern | 2 | 2 | `ManagedByDeployment`: Die Konfiguration eines Siteaccess kommt ausschließlich aus der Versionsverwaltung | [ManagedByDeployment](../../../en/us/features/6.0/managed-by-deployment.md) |
| R13 | Eine Änderung geht mit veralteten Caches live, und die alte Version wird ausgeliefert | 2 | 2 | `exp:velocity deploy` führt Autoloads, Caches, PHP-Neuladen und Velocity-Neustart in der richtigen Reihenfolge aus und hält beim ersten Fehler an | [Deployment, Abschnitt 7](../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern), [11.6](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) |
| R14 | Cronjobs laufen nicht mehr, und niemand bemerkt es (keine Indizierung, keine Benachrichtigungen, keine Audit-Alarme) | 2 | 2 | Die Cronjob-Prüfung der Systeminformationen; das Audit-Dashboard warnt, wenn sein Cronjob-Teil eine Stunde lang nicht gelaufen ist; die Seite Cronjobs zeigt die letzten Läufe | [Cronjobs](../../anleitungen/cronjobs.md), [Systeminformationen](../../anleitungen/systeminformationen.md#3-zustandsprüfungen) |
| R15 | Logs füllen die Festplatte | 1 | 2 | Log-Rotation; Log-Gesundheit gemessen an einem Speicherbudget (standardmäßig 256 MB) | [Log-Viewer, „Log-Gesundheit“](../../../en/us/features/6.0/log-viewer.md#log-health), [Log-Rotation](../../../en/us/features/6.0/log-rotation.md) |
| R16 | Defekte Links, fehlende Dateien oder ungenutzte Assets nach einer Inhaltsmigration | 2 | 2 | Inhaltsprüfungen: defekte Relationen, fehlende Dateien, nicht referenzierte Objekte, Speicherdateien | [Inhaltsprüfungen](../../../en/us/features/6.0/content-checks.md) |
| R17 | Eine benötigte PHP-Version ist beim Hoster nicht verfügbar | 2 | 2 | Die PHP-Tabelle frühzeitig prüfen; PHP 8.0 ist die älteste unterstützte Version, 8.1 bis 8.5 werden unterstützt | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen), [Systemanforderungen](../../installation/02-voraussetzungen.md) |
| R18 | Eine Erweiterung lädt nicht mit dem PHP des Servers | 2 | 2 | `exp:checkclasses` meldet jede Klasse, deren Laden PHP verweigert | [11.6 Schritt 4](../../installation/11-aktualisieren.md#schritt-4-die-eigenen-extensions-prüfen) |
| R19 | Eine Erweiterung eines Drittanbieters ist unsicher oder wird nicht gepflegt | 2 | 2 | Ihren Code vor der Installation prüfen; Erweiterungen sind Git-Repositorys mit sichtbarer Historie | [Sicherheitshärtung](../sicherheitshaertung.md#veraltete-abhängigkeiten-und-die-lieferkette), [Erweiterungen](../../anleitungen/extensions.md) |
| R20 | Abhängigkeit: Die Site kann nicht zu einem anderen Hoster, einer anderen Datenbank oder einem anderen Team umziehen | 1 | 3 | Code unter GPL v2 oder später, vollständige Git-Historie, Auswahl an Datenbanken und Betriebssystemen, Selbst-Hosting | [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md), [Datenbankunterstützung](../datenbankunterstuetzung.md), [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md) |
| R21 | Zu wenige Personen mit Exponential-Kenntnissen | 2 | 2 | Siehe [Verfügbarkeit von Fachkräften](#verfügbarkeit-von-fachkräften) unten: eine ehrliche Antwort, keine Funktion | [Mit Git entwickeln](../mit-git-entwickeln.md), [Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community) |
| R22 | Wissen bleibt bei einer einzigen Person („Bus-Faktor“) | 2 | 3 | Arbeit in Git mit geprüften Pull-Requests; Dokumentation im Repository; das Übergabedokument | [Ein Team mit Git führen](ein-team-mit-git-fuehren.md), [Projektvorlagen](projektvorlagen.md) |
| R23 | Ein Release Ihrer eigenen Erweiterung wird mit einem Fehler veröffentlicht | 2 | 2 | Vor dem Taggen prüfen; ein veröffentlichter Tag wird nie verschoben; die Korrektur ist die nächste Version | [Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen) |
| R24 | Personenbezogene Daten landen in Logs, Berichten oder Fehlermeldungen | 2 | 2 | Logs maskieren Adressen und Geheimnisse; der Systembericht lässt Passwörter und Schlüssel weg; die Hinweise zu Fehlermeldungen sagen, was wegzulassen ist | [Request-Logs, „Datenschutz“](../../../en/us/features/6.0/request-logs.md#privacy), [Log-Viewer](../../../en/us/features/6.0/log-viewer.md), [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#eine-gute-fehlerbeschreibung) |
| R25 | Test-E-Mails erreichen während der Entwicklung echte Personen | 2 | 2 | Der Datei-Transport für E-Mails schreibt Mails in Dateien; die Systeminformationen weisen auf `Transport=file` hin | [10.4](../../installation/10-nach-der-installation.md#104-mail-der-datei-transport-und-echter-versand), [Systeminformationen](../../anleitungen/systeminformationen.md#3-zustandsprüfungen) |
| R26 | Die Site ist nach einer Änderung langsamer | 2 | 1 | `exp:benchmark` mit gespeicherten Ausgangswerten endet bei einer Verschlechterung mit Status 1 | [Benchmarking](../../anleitungen/benchmarking.md#9-läufe-vergleichen-baselines-und-regressionen) |
| R27 | Redakteure können während der Wartung nicht arbeiten, oder Besucher sehen Fehler | 2 | 1 | Wartungsmodus: eine 503-Seite, die nie gecacht wird, mit einer Nachricht und einem Endzeitpunkt | [Wartungsmodus](../../../en/us/features/6.0/maintenance-mode.md) |
| R28 | Anforderungen an die Barrierefreiheit werden nicht erfüllt | 2 | 2 | Die getesteten WCAG-Regeln des Designs admin4; Unterstützung für Schreibrichtung von rechts nach links; eigene Prüfungen für Ihre eigenen Templates | [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md#prüfungen-der-barrierefreiheit), [Von rechts nach links](../../../en/us/features/6.0/right-to-left.md) |
| R29 | Ein Schreibassistent sendet Inhalte entgegen den Vorgaben an einen externen Dienst | 1 | 2 | Exponential Intelligence ist inaktiv, bis es aktiviert wird; es schreibt nur Entwürfe; eine Datenrichtlinie je Anbieter (Region `us`, `eu` oder `local`) | [expai](../../../en/us/features/6.0/extensions/expai.md) |
| R30 | Die Administration ist für jeden im Internet erreichbar | 2 | 2 | Die Administration auf einem eigenen Host, beschränkt per Netzwerk oder VPN; request-shield vorgeschaltet | [Sicherheitshärtung](../sicherheitshaertung.md#checkliste), [13.5](../../installation/13-sicherheitshaertung.md#135-der-administrations-siteaccess) |

## Risiken im Detail

### Datenverlust (R01, R02)

Die eigene Anleitung des Produkts ist unmissverständlich: „Testen Sie mindestens einmal eine Wiederherstellung auf
einem anderen Server; ein Backup, das Sie nie wiederhergestellt haben, ist eine Hoffnung“
([Eine Site betreiben](../../anleitungen/betrieb-einer-website.md#6-sichern-und-wiederherstellen)). Machen Sie „Wiederherstellung
getestet“ zu einer Zeile in jeder Livegang- und Upgrade-Checkliste, mit Datum und Namen.

Ein Backup umfasst **beide** Orte, an denen Exponential seinen Zustand hält: die Datenbank und die Dateien (`var/`,
`settings/override/`, `settings/siteaccess/`, Ihre Erweiterungen und Designs). Die Dateien in `settings/override/`
enthalten Datenbankpasswörter, daher muss das Backup selbst geschützt werden ([Sicherheitshärtung](../sicherheitshaertung.md#sicherungen-und-wiederherstellung)).

### Upgrades (R03, R04, R17, R18)

Das meiste Upgrade-Risiko ist im Voraus bekannt, weil das Projekt es aufschreibt: Jedes Changelog beginnt mit einem
Abschnitt „Upgrading“, und die Hinweise zu Verhaltensänderungen sagen, was zu prüfen ist. Die Gegenmaßnahme liegt in
der Planung: erst lesen, dann schätzen. Siehe [Release- und Upgrade-Planung](release-und-upgrade-planung.md).

### Sicherheit (R05 bis R11, R30)

Die Hilfeseite [Sicherheitshärtung](../sicherheitshaertung.md) geht die Bedrohungen einzeln durch und endet mit einer
Checkliste; die [13.16 Go-live-Checkliste](../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang) des
Installationshandbuchs geht Einstellung für Einstellung vor. Nehmen Sie beide in Ihre Livegang-Schranke auf. Für die
Überwachung nach dem Livegang führt dieselbe Seite auf, welches Log oder welcher Tab was festhält („Watching: logs,
the request classifier and the audit trail“).

### Abhängigkeit (R20)

Diese Eigenschaften sind dokumentiert und überprüfbar:

- Der Code steht unter GPL v2 oder später ([LICENSE.md](https://github.com/se7enxweb/exponential/blob/main/LICENSE.md),
  [COPYRIGHT.md](https://github.com/se7enxweb/exponential/blob/main/COPYRIGHT.md)); „Die Nutzung von Exponential
  unter den Bedingungen der GNU GPL ist kostenlos“ (README, zitiert unter [Wer ist 7x?](../wer-ist-7x.md#open-source-lizenzen)).
- Die vollständige Git-Historie ist öffentlich, und die empfohlene Installation besteht aus Git-Klonen ([Mit Git entwickeln](../mit-git-entwickeln.md)).
- Mehrere Datenbanken und Betriebssysteme werden unterstützt ([Datenbankunterstützung](../datenbankunterstuetzung.md),
  [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md)).
- Die Dokumentation ist frei unter der GNU FDL ([Freie Dokumentation](../freie-dokumentation.md)).

Was Ihnen das Produkt nicht abnehmen kann: die Abhängigkeit von Ihrem eigenen individuellen Code. Halten Sie ihn mit
Dokumentation in Ihren eigenen Git-Repositorys, dann ist die Übergabe möglich.

### Verfügbarkeit von Fachkräften

Eine ehrliche Antwort für Ihr Register: Die Quellen sagen nicht, wie viele Entwickler oder Agenturen mit Exponential
arbeiten. Dokumentiert ist Folgendes:

- Die Codebasis des Projekts reicht bis 1999 zurück, und die Dokumentation, die Schulungsbücher und die Enzyklopädie
  der früheren Jahre bleiben unter freien Lizenzen verfügbar ([Wer ist 7x?](../wer-ist-7x.md#die-community)).
- Die Community hat ein Forum, einen Telegram-Chat und Issue-Tracker ([Orte der Community](../wer-ist-die-exponential-foundation.md#orte-der-community)).
- 7x bietet Leistungen für neue Sites, Migrationen und die Betreuung bestehender Sites an ([Wer ist 7x?](../wer-ist-7x.md#wie-sie-mitmachen-können)).
- Die Hilfeseiten sind so geschrieben, dass ein Benutzer in die Rolle eines Entwicklers hineinwachsen kann ([Mit Git entwickeln](../mit-git-entwickeln.md)).

Gegenmaßnahmen, die Sie selbst in der Hand haben: Planen Sie Zeit für die Einarbeitung ein, stellen Sie einem neuen
Entwickler bei den ersten Aufgaben einen erfahrenen zur Seite, und halten Sie das eigene Wissen des Projekts in Git und
in der Dokumentation fest.

### Wissenskonzentration (R22)

Machen Sie Reviews zum Teil der Arbeit (jede Änderung ist ein Pull-Request, den eine zweite Person liest), halten Sie
Einstellungen unter Versionsverwaltung und führen Sie ein Übergabedokument ab der ersten Woche, nicht erst ab der
letzten ([Projektvorlagen](projektvorlagen.md)).

## Rhythmus der Risikoüberprüfung

| Wann | Was überprüft wird | Wer |
|---|---|---|
| Projektstart | Jedes Risiko bewerten; Verantwortliche zuweisen | Projektleiter mit dem Team |
| Jede Schranke | Risiken der nächsten Phase; Vergangenes schließen | Projektleiter |
| Vor dem Livegang | Insbesondere R01, R03, R05 bis R11, R13, R14, R30 | Projektleiter, Administrator |
| Wöchentlich nach dem Livegang | Die wöchentliche Checkliste aus [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md#10-eine-wöchentliche-checkliste) | Administrator |
| Jedes Upgrade | R03, R04, R17, R18 | Administrator, Entwickler |

## Ein leeres Register zum Kopieren

```text
Risk register: <project name>          Version: <date>          Owner: <role>

Scales: Likelihood 1 low / 2 medium / 3 high; Impact 1 low / 2 medium / 3 high; Priority = L x I

| ID  | Risk                     | L | I | P | Mitigation (feature, page, procedure) | Owner  | Due     | Status |
|-----|--------------------------|---|---|---|---------------------------------------|--------|---------|--------|
| R01 | <risk>                   |   |   |   | <mitigation, link>                    | <role> | <date>  | open   |
| R02 |                          |   |   |   |                                       |        |         |        |

Closed risks:
| ID | Closed on | Why |
```

## Verwandte Seiten

- [Release- und Upgrade-Planung](release-und-upgrade-planung.md), [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md)
- [Checklisten zu Barrierefreiheit, Sicherheit und Compliance](checklisten-barrierefreiheit-sicherheit-und-compliance.md)
- [Sicherheitshärtung](../sicherheitshaertung.md), [Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
- [Eine Site betreiben](../../anleitungen/betrieb-einer-website.md)
- [Ein Projekt messen: Kennzahlen aus den eigenen Logs und dem Audit-Protokoll des Produkts](kennzahlen-aus-logs-und-audit.md)
- [Projektvorlagen](projektvorlagen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
