---
title: Exponential im öffentlichen Sektor betreiben
audience: government, admin, hosting, project-manager
category: public-sector
order: 10
summary: Der Betriebsteil des Bereichs Behörden: Hosting, Verfügbarkeit, Datensicherungen, Überwachung, Vorfälle, Aktualisierungen und Änderungen, Personen und Berechtigungen sowie redaktionelle Workflows, geschrieben für interne IT-Teams und die Verantwortlichen, die für eine öffentliche Website einstehen.
keywords: Betrieb, öffentlicher Sektor, Behörden-IT, interne IT, Hosting, Verfügbarkeit, Datensicherung, Wiederherstellung, Überwachung, Vorfall, Aktualisierung, Änderungsmanagement, Rollen, Berechtigungen, Freigaben, Workflows, Kontinuität, operations, public sector, government IT, hosting, backup, recovery, monitoring, incident, upgrade, change management, roles, permissions, approvals
type: help
---
# Exponential im öffentlichen Sektor betreiben

Dieser Teil des Bereichs Behörden richtet sich an die Menschen, die eine Website des öffentlichen Sektors nach dem
Livegang am Laufen halten: das interne IT-Team, das Webteam, die Verantwortlichen für den Dienst und die
Projektleitung, die einem Änderungsbeirat oder einer Prüfung Rechenschaft schuldet. Er erklärt, wie Exponential
gehostet, verfügbar gehalten, gesichert, überwacht, aktualisiert und gesteuert wird und wie Berechtigungen der
Beschäftigten und redaktionelle Freigaben eingerichtet werden. Jede Seite baut auf der eigenen Dokumentation der
Installation auf: den Leitfäden in `doc/eng-us/guides/`, den Funktionsseiten in `doc/eng-us/features/6.0/`, dem
Installationshandbuch in `doc/eng-us/install/` und den Seiten für Hoster unter
[Hosting](../../hosting/hosting-anbieter-hier-beginnen.md). Diese Seiten werden verlinkt, nicht wiederholt; die Seiten
hier ergänzen, was sich ändert, wenn der Betreiber eine öffentliche Stelle ist: Änderungsbeiräte, Beschaffungsregeln,
Pflichten zur Kontinuität, Prüfungen und die Erwartung, dass alles dokumentiert wird.

## Inhalt
1. [Die acht Ordner](#die-acht-ordner)
2. [Schritt-für-Schritt-Aufgaben](#schritt-für-schritt-aufgaben)
3. [Lesereihenfolge](#lesereihenfolge)
4. [Was Sie vor dem Start brauchen](#was-sie-vor-dem-start-brauchen)
5. [Wie die Fakten geprüft wurden](#wie-die-fakten-geprüft-wurden)
6. [Verwandte Seiten](#verwandte-seiten)

## Die acht Ordner

| Ordner | Beantwortet |
|---|---|
| [Hosting](hosting/README.md) | Wo und wie Exponential betrieben wird: im eigenen Rechenzentrum, in einer Behörden- oder souveränen Cloud, in Containern; welche Datenbank, welcher Webserver; getrennte Umgebungen und eine eigene Administration; wo Daten liegen. |
| [Verfügbarkeit](verfuegbarkeit/README.md) | Die Website erreichbar halten: mehrere Webserver, gemeinsamer Speicher, Datenbank-Replikate, die Caches vor PHP, geplante Ausfallzeiten mit dem Wartungsmodus, Kapazität. |
| [Datensicherung und Wiederherstellung](datensicherung-und-wiederherstellung/README.md) | Was je Datenbank-Engine zu sichern ist; Wiederherstellungstests; ein Plan zur Notfallwiederherstellung; Wiederherstellungszeitpunkt und Wiederherstellungsdauer als Entscheidungen Ihrer Organisation. |
| [Überwachung](ueberwachung/README.md) | Der Log-Viewer, die Fehlerübersicht und der Zustand der Logs, die Anfrage-Logs (`404.log`, `security.log`), CSP-Berichte, Audit-Warnungen, Cronjobs, Systeminformationen und externe Verfügbarkeitsprüfungen. |
| [Vorfälle](vorfaelle/README.md) | Runbooks für schlechte Tage: Website ausgefallen, Datenbank nicht erreichbar, verunstaltete Seiten, ein kompromittiertes Konto, Fluten von Sondierungsanfragen, eine fehlgeschlagene Bereitstellung, ein massenhafter Inhaltsfehler, veraltete Caches, Probleme mit E-Mails. |
| [Aktualisierungen und Änderungen](aktualisierungen-und-aenderungen/README.md) | Sicher aktualisieren, unveränderliche Releases, Änderungsbeiräte, Staging und Freigabe, durch die Bereitstellung verwaltete Konfiguration, Rücknahme. |
| [Personen und Berechtigungen](personen-und-berechtigungen/README.md) | Rollen, Richtlinien und Einschränkungen; Rollenentwürfe nach dem Prinzip der geringsten Rechte für Webteams, Redaktionen der Fachbereiche, Freigebende, Prüfende und externe Dienstleister; Eintritt, Wechsel und Austritt; Anmeldung in zwei Schritten; Überprüfung der Zugänge. |
| [Redaktionelle Workflows](redaktionelle-workflows/README.md) | Freigabe vor dem Veröffentlichen, der Posteingang für die Zusammenarbeit, das Vier-Augen-Prinzip, rechtliche Prüfung, mehrsprachige Freigaben, ein Notfall-Umgehungsweg, der dokumentiert bleibt, Verantwortung für Inhalte. |

## Schritt-für-Schritt-Aufgaben

Zwei Aufgabenordner enthalten kurze, nummerierte Abläufe, die auf die Ordner oben zurückverweisen:

- [Betriebsaufgaben](../../../../eng-us/help/government/tasks/operations/README.md): Wartungsfenster, Datensicherungen und Wiederherstellungen, Caches,
  Cronjobs, Logs, das Prüfprotokoll, Upgrade-Prüfungen, Zertifikate, Replikate.
- [Aufgaben zu Personen und Berechtigungen](../../../../eng-us/help/government/tasks/people-and-permissions/README.md): Rollen für einen Fachbereich,
  Freigebende, Eintritte und Austritte, Zurücksetzen von Passwörtern, externe Dienstleister, Überprüfung der
  Administrationszugänge.

## Lesereihenfolge

1. **Neu im Betrieb von Exponential:** [Hosting](hosting/README.md), danach
   [Datensicherung und Wiederherstellung](datensicherung-und-wiederherstellung/README.md) und [Überwachung](ueberwachung/README.md).
2. **Vorbereitung auf den Livegang:** [Personen und Berechtigungen](personen-und-berechtigungen/README.md) und
   [Redaktionelle Workflows](redaktionelle-workflows/README.md), damit Zugänge und Freigaben vom ersten Tag an stimmen.
3. **Den Dienst betreiben:** [Vorfälle](vorfaelle/README.md) und
   [Aktualisierungen und Änderungen](aktualisierungen-und-aenderungen/README.md), mit den
   [Betriebsaufgaben](../../../../eng-us/help/government/tasks/operations/README.md) griffbereit.
4. **Planung für Wachstum oder strengere Kontinuitätspflichten:** [Verfügbarkeit](verfuegbarkeit/README.md).

Projektleitungen, die nur den Überblick brauchen, können die README jedes Ordners lesen: Jede enthält eine einzeilige
Antwort pro Seite.

## Was Sie vor dem Start brauchen

- **Shell-Zugang** zum Installationsverzeichnis (dem Ordner mit `index.php` und `console`) für die Befehle. Führen Sie
  sie als Benutzer der Website aus; wenn Sie `root` sind, brauchen die PHP-Skripte `--allow-root-user`, wie
  [Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md) erklärt.
- **Ein Administratorkonto** in der Administrationsoberfläche für die Klickpfade. Das Setup-Menü (`/setup/menu`)
  enthält Cache-Verwaltung, Wartung, Cronjobs, Logdateien, Systeminformationen und Upgrade-Prüfung; das Menü Benutzer
  enthält Rollen und Richtlinien sowie Benutzerkonten prüfen.
- **Eine Testkopie** der Website. Viele Abläufe hier (Wiederherstellungstests, Aktualisierungen, Rollenänderungen)
  sollten zuerst dort ausprobiert werden;
  [Umgebungen: Entwicklung, Test und Produktion](hosting/umgebungen-entwicklung-test-produktion.md) zeigt, wie eine Installation
  Einstellungen pro Umgebung trägt.

## Wie die Fakten geprüft wurden

Jeder Befehl, jede Einstellung und jede Bezeichnung der Administration auf diesen Seiten wurde am Code und an der
Dokumentation des Repositorys geprüft, die Bezeichnungen der Administration zusätzlich an Bildschirmen des
Demonstrationsservers. Wo Exponential keine eingebaute Funktion für etwas hat, das eine öffentliche Stelle erwarten
könnte (zum Beispiel ein automatisches Umschalten der Datenbank bei Ausfall oder ein Feld für ein Prüfdatum von
Inhalten), sagt die Seite das klar und nennt die Möglichkeiten. Keine Seite nennt eine echte Regierung, Behörde oder
einen echten Kunden; Beispiele verwenden Platzhalter wie `example.gov` und „das Ministerium für Beispiele“.

Die Einstiegsseite des Bereichs, [Behörden: hier beginnen](../verwaltungen-hier-beginnen.md), ordnet diesen Teil zwischen
den anderen Teilen des Bereichs Behörden ein.

## Verwandte Seiten
- [Hoster: hier beginnen](../../hosting/hosting-anbieter-hier-beginnen.md): die Sicht des Hosting-Anbieters auf dieselben Themen.
- [Eine Website betreiben](../../../anleitungen/betrieb-einer-website.md): Caches, Cronjobs, Datensicherungen und Reparaturen im Alltag.
- [Sicherheit und Audit](../../../anleitungen/sicherheit-und-audit.md): Härtung, Rollen und das Prüfprotokoll.
- [Sicherheitshärtung](../../sicherheitshaertung.md): die Bedrohungen einzeln betrachtet.
- [Kurs für Website-Administratoren](../../../../eng-us/help/trainers/site-administrators/README.md): ein Schulungskurs zu denselben Aufgaben.
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
