---
title: Umgang mit Störungen
audience: software-engineer, admin
category: software-engineering
order: 420
summary: Wie Ingenieure auf einen Ausfall, eine Regression oder einen Sicherheitsvorfall auf einer Exponential-Website reagieren - Ersteinschätzung, die Werkzeuge für jedes Symptom (Wartungsmodus, Reparatur aus dem Browser, Fehlerreferenzen, Logs, Audit-Trail, Status von Velocity), vorwärts korrigieren statt Releases neu zu schreiben, vertrauliche Meldung von Schwachstellen und die Nachbereitung.
keywords: Störung, Vorfall, Ausfall, Regression, Ersteinschätzung, Runbook, Wartungsmodus, Reparatur, Fehlerreferenz, Zurückrollen, vorwärts korrigieren, Nachbetrachtung, Sicherheitsvorfall, Schwachstelle, Softwareingenieur, incident, outage, triage, maintenance mode, repair, error reference, rollback, roll forward, postmortem, security incident, vulnerability
type: help
---
# Umgang mit Störungen

Diese Seite richtet sich an Ingenieure in Rufbereitschaft für eine Exponential-Website. Sie gibt eine Abfolge für jede
Störung und verweist auf die Runbooks für die häufigen. Sie ersetzt nicht den Störungsprozess Ihrer Organisation; sie
ergänzt die Schritte, die für Exponential spezifisch sind.

## Inhalt

1. [Die ersten zehn Minuten](#die-ersten-zehn-minuten)
2. [Symptome und die Werkzeuge dafür](#symptome-und-die-werkzeuge-dafür)
3. [Den Schaden stoppen](#den-schaden-stoppen)
4. [Beheben: vorwärts korrigieren](#beheben-vorwärts-korrigieren)
5. [Sicherheitsvorfälle](#sicherheitsvorfälle)
6. [Nach der Störung](#nach-der-störung)

## Die ersten zehn Minuten

1. **Bestätigen und eingrenzen.** Rufen Sie die Startseite und die Anmeldung der Administration von außen auf. Die
   Startseite kann aus einem Cache ausgeliefert werden, während die Datenbank ausgefallen ist; die Anmeldeseite sagt Ihnen
   also mehr ([Zustandsprüfungen und Verfügbarkeit](../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).
2. **Prüfen Sie die Server.** `./console exp:velocity status` für Velocity; sonst den PHP-FPM-Dienst und den
   Datenbankserver.
3. **Lesen Sie die neuesten Fehler.** Setup > Logdateien oder `./console exp:logs`; die Fehlerreferenz eines Besuchers
   führt zu den Zeilen seiner Anfrage ([Einen Fehler anhand seiner Referenz finden](../../anleitungen/fehler-anhand-der-referenz-finden.md)).
4. **Fragen Sie, was sich geändert hat.** Der Audit-Trail hält Bereitstellungen, Schreibvorgänge an Einstellungen und das
   Leeren von Caches mit Wer und Wann fest: `./console exp:audit tail --allow-root-user`.
5. **Entscheiden Sie**: zuerst den Schaden stoppen oder direkt beheben?

## Symptome und die Werkzeuge dafür

| Symptom | Erstes Werkzeug | Runbook |
|---|---|---|
| Die Website antwortet nicht | Serverstatus, `error.log` | [Website ausgefallen](../hosting/runbooks/website-nicht-erreichbar.md) |
| Seiten sind langsam | Debug-Leiste für Ihre Adresse, `exp:benchmark kernel` | [Langsame Website](../hosting/runbooks/langsame-website.md) |
| Eine leere Seite oder PHP-Ausgabe statt der Website, `vendor/` fehlt | Reparatur aus dem Browser ([Reparatur](../../funktionen/6.0/reparatur-im-browser.md)) | [Website ausgefallen](../hosting/runbooks/website-nicht-erreichbar.md) |
| Velocity startet nicht | `exp:velocity status`, sein Log | [Velocity startet nicht](../hosting/runbooks/velocity-startet-nicht.md) |
| Eine Änderung "hat nicht gewirkt" | die Reihenfolge der Bereitstellung: Code geladen, bevor Caches geleert wurden | [Eine Änderung bereitstellen](eine-aenderung-bereitstellen.md) |
| Festplatte voll | Größen von `var/` unter Setup > Caches, Log-Archive | [Festplatte voll](../hosting/runbooks/festplatte-voll.md) |
| Ein Zertifikat ist abgelaufen | die HTTPS-Konfiguration der Engine | [Ablauf von Zertifikaten](../hosting/runbooks/ablauf-von-zertifikaten.md) |
| Ein Verdacht auf Kompromittierung | `security.log`, der Audit-Trail | [Kompromittierte Website](../hosting/runbooks/kompromittierte-website.md) |

## Den Schaden stoppen

- **Der Wartungsmodus** nimmt die öffentliche Website mit `503` und `Retry-After` vom Netz und bringt sie mit einem Befehl
  zurück:

  ```bash
  php bin/php/maintenance.php on --message="Back at 06:00" --until=2h --allow-root-user
  php bin/php/maintenance.php off --allow-root-user
  ```

  ([Wartungsmodus](../../funktionen/6.0/wartungsmodus.md)).
- **Schalten Sie** die Erweiterung oder Einstellung **ab**, die die Änderung eingeführt hat, über `settings/override/` und
  ein Leeren des INI-Cache (und einen Neustart von Velocity).
- **Stellen Sie** aus der Sicherung **wieder her**, wenn Daten beschädigt sind
  ([Aus der Sicherung wiederherstellen](../hosting/runbooks/aus-einem-backup-wiederherstellen.md)).

## Beheben: vorwärts korrigieren

Korrigieren Sie vorwärts mit einem neuen Commit und, bei einem Release, einer neuen Version. Ein veröffentlichter Tag
oder ein Release wird nie verschoben, ersetzt oder neu erstellt, selbst wenn es die Ursache ist; die Notes der nächsten
Version sagen, was an der vorherigen falsch war
([Versionsnummern und unveränderliche Tags](versionsnummern-und-unveraenderliche-tags.md)). Bei einer Installation aus
Git-Klonen können Sie eine Korrektur nutzen, bevor sie veröffentlicht ist ([Mit Git entwickeln](../mit-git-entwickeln.md)).

## Sicherheitsvorfälle

- Eine Schwachstelle in Exponential selbst wird **vertraulich** an die Adresse in `SECURITY.md` gemeldet, nicht im
  öffentlichen Issue-Tracker. Geben Sie Version, Engine und Datenbank an, was ein Angreifer tun kann, und die Schritte zum
  Reproduzieren, und lassen Sie Passwörter, Geheimnisse und personenbezogene Daten weg.
- Prüfen Sie die Integrität des Audit-Trails (`./console exp:audit verify --allow-root-user`, Exit-Status 1 bei
  gebrochener Kette), bevor Sie sich auf ihn als Beweis verlassen.
- Wechseln Sie die Geheimnisse, die ein Angreifer gesehen haben könnte, in `settings/override/`.

## Nach der Störung

- Schreiben Sie den zeitlichen Ablauf, die Ursache, was sie entdeckt hat und was sie früher entdeckt hätte, auf.
- Fügen Sie den Test hinzu, der sie gefunden hätte ([Teststrategie](teststrategie.md)).
- War die Ursache eine Verhaltensänderung, prüfen Sie, ob ihre Upgrade-Hinweise in `doc/eng-us/bc/6.0/` sagten, was zu
  tun ist; wenn nicht, korrigieren Sie sie ([Changelogs und Upgrade-Hinweise](changelogs-und-bc-hinweise.md)).
- Melden Sie einen Fehler der Software öffentlich, mit der Diagnose, die die Seite **Einen Fehler melden** der Hilfe
  vorbereitet ([Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md)).

## Verwandte Seiten

- [Beobachtbarkeit: Logs, Debug-Leiste und Audit-Trail](beobachtbarkeit-logs-debug-und-audit.md)
- [Runbooks für Hoster](../hosting/runbooks/README.md)
- [Fehlersuche](../../installation/12-fehlerbehebung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
