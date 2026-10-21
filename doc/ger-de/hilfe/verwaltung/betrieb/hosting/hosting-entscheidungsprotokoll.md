---
title: Eine Entscheidungsdokumentation zum Hosting
audience: government, hosting, project-manager, admin
category: public-sector
order: 130
summary: Für Projektleitungen, Architektinnen und Architekten in öffentlichen Stellen, die dokumentieren müssen, wie eine Exponential-Website gehostet wird. Eine Vorlage für die Entscheidungsdokumentation mit jeder Wahl, die die Software verlangt, den Möglichkeiten und dem Ort, an dem jede Möglichkeit beschrieben ist, bereit zur Ablage bei einem Änderungsbeirat oder einer Prüfung.
keywords: Entscheidungsdokumentation, Architekturentscheidung, Hosting-Entscheidung, Vorlage, Dokumentation, Änderungsbeirat, Prüfung, Beschaffung, Datenbankwahl, Wahl des Webservers, Datensicherungen, Verfügbarkeit, Speicherort der Daten, decision record, architecture decision, hosting decision, template, documentation, change board, audit, procurement, database choice, web server choice, backups, availability, data location
type: help
---
# Eine Entscheidungsdokumentation zum Hosting

Diese Seite richtet sich an Projektleitungen, Architektinnen, Architekten und IT-Leitungen in öffentlichen Stellen, die
aufschreiben müssen, wie eine Website gehostet wird und warum, sei es für einen Änderungsbeirat, eine Prüfung, eine
Beschaffungsakte oder das Team, das später übernimmt. Sie bietet eine Vorlage für die Entscheidungsdokumentation mit
jeder Wahl, die Exponential verlangt, den von den Quellen unterstützten Möglichkeiten und dem Ort, an dem jede erklärt
ist. Füllen Sie sie einmal pro Website aus und bewahren Sie sie bei der Dokumentation der Website auf. Die Fassung für
Hoster steht unter [Einen Webserver wählen](../../../hosting/server/einen-webserver-waehlen.md) und
[Ein Hosting-Modell wählen](../../../hosting/modelle/ein-hosting-modell-waehlen.md).

## Inhalt

1. [So verwenden Sie die Vorlage](#so-verwenden-sie-die-vorlage)
2. [Die Vorlage](#die-vorlage)
3. [Fakten, die Sie von der laufenden Website sammeln](#fakten-die-sie-von-der-laufenden-website-sammeln)
4. [Aktuell halten](#aktuell-halten)
5. [Checkliste](#checkliste)

## So verwenden Sie die Vorlage

- Eine Dokumentation pro Website (oder pro Installation, die mehrere Websites ausliefert).
- Jede Zeile: die Entscheidung, der Grund und das Risiko, das Sie in Kauf nehmen. „Wir haben X gewählt, weil unser
  Datenbankteam X betreibt“ ist ein völlig ausreichender Grund.
- Schreiben Sie Namen von Teams, nicht von Personen, damit die Dokumentation nicht mit Personalwechseln veraltet.
- Nennen Sie Zahlen nur, wenn Sie sie gemessen oder entschieden haben; kennzeichnen Sie Annahmen als „Beispielannahme“.

## Die Vorlage

| Entscheidung | Möglichkeiten in den Quellen | Unsere Wahl | Grund | In Kauf genommenes Risiko | Lesen |
|---|---|---|---|---|---|
| Hosting-Standort und -Modell | eigenes Rechenzentrum; Behörden- oder souveräne Cloud; virtuelle Maschine; Container; Kubernetes (Stufe 3); verwaltetes Hosting | | | | [Hosting im eigenen Rechenzentrum](hosting-im-eigenen-rechenzentrum.md), [Hosting in der Behörden- und souveränen Cloud](verwaltungs-und-souveraene-cloud.md) |
| Betriebssystem | Stufe 1 oder 2 empfohlen | | | | [Unterstützte Betriebssysteme](../../../betriebssystemunterstuetzung.md) |
| PHP-Version | 8.0 oder neuer, Velocity eingeschlossen | | | | [PHP-Versionen und -Erweiterungen](../../../hosting/grundlagen/php-versionen-und-erweiterungen.md) |
| Datenbank | 1. SQLite 3 · 2. PostgreSQL · 3. MongoDB · 4. MariaDB · 5. Oracle · 6. MySQL | | | | [Eine Datenbank in der offiziellen Reihenfolge wählen](datenbankwahl-in-der-offiziellen-reihenfolge.md) |
| Webserver oder Engine | Velocity (empfohlen); FrankenPHP; Apache oder nginx mit PHP-FPM | | | | [Wahl des Webservers](wahl-des-webservers.md) |
| TLS-Terminierung | die Engine; der Webserver; ein zentraler Proxy | | | | [TLS und HTTPS](../../../hosting/sicherheit/tls-und-https.md) |
| Zugang zur Administration | eigener Hostname; Netzwerkbeschränkung; VPN | | | | [Die Administration von der öffentlichen Website trennen](administrations-und-oeffentliche-siteaccesses-trennen.md) |
| Umgebungen | Entwicklung, Test, Produktion mit `EXP_ENV` | | | | [Umgebungen für Entwicklung, Test und Produktion](umgebungen-entwicklung-test-produktion.md) |
| Konfigurationskontrolle | Versionsverwaltung; `ManagedByDeployment` | | | | [Konfiguration unter Versionsverwaltung](konfiguration-unter-versionskontrolle.md) |
| Anzahl der Webserver | einer; mehrere hinter einem Load Balancer | | | | [Übersicht zur Hochverfügbarkeit](../verfuegbarkeit/hochverfuegbarkeit-im-ueberblick.md) |
| Caches vor PHP | Antwort-Cache von Velocity; HTTP-Cache; statischer Cache (ausgeschaltet ausgeliefert) | | | | [Antwort-Cache von Velocity und HTTP-Cache](../verfuegbarkeit/velocity-antwortcache-und-http-cache.md) |
| Datensicherungen | was, wie oft, wo, wie lange | | | | [Was zu sichern ist](../../../../../eng-us/help/government/operations/backups-and-recovery/what-to-back-up.md) |
| Wiederherstellungsziele | RPO und RTO, wie von der Organisation entschieden | | | | [RPO und RTO als Entscheidungen](../../../../../eng-us/help/government/operations/backups-and-recovery/rpo-and-rto-as-decisions.md) |
| E-Mail | sendmail, SMTP, Datei (Test) | | | | [E-Mail-Zustellung](../../../hosting/betrieb/mailzustellung.md) |
| Suche | eingebaut; Solr über ezfind | | | | [Such-Backends](../../../hosting/grundlagen/such-backends.md) |
| Speicherort der Daten | Inventar jedes Orts, an dem Daten liegen | | | | [Speicherort der Daten und was wo gespeichert wird](datenstandort-und-was-wo-gespeichert-wird.md) |
| Aktive Erweiterungen mit Kontakt zu Dritten | keine, oder aufgeführt und begründet | | | | [Datenschutzstandards](../../../../../eng-us/standards/privacy.md) |
| Überwachung | externe Verfügbarkeitsprüfung; interne Statusbefehle | | | | [Zustandsprüfungen und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md) |

## Fakten, die Sie von der laufenden Website sammeln

Hängen Sie diese Ausgaben an die Dokumentation an; sie zeigen, was wirklich läuft, nicht was geplant war:

```bash
php -v | head -1
./console exp:velocity ext check --allow-root-user
./console exp:system:info --json --allow-root-user > system-info.json
grep -rn "DatabaseImplementation" settings/override settings/siteaccess/*/site.ini.append.php
./console exp:cache status --allow-root-user
```

`exp:system:info` gibt die Fakten und Zustandsprüfungen von **Setup > Systeminformationen** aus
([Arbeitsspeicher, CPU und Festplatte bemessen](../../../hosting/grundlagen/speicher-cpu-und-festplatte-dimensionieren.md)). Prüfen Sie das
JSON, bevor Sie es außerhalb des IT-Teams ablegen; es beschreibt Ihren Server im Detail. Der Supportbericht der Seite
ist so gebaut, dass er weglässt, was nicht weitergegeben werden sollte
([Systeminformationen](../../../../anleitungen/systeminformationen.md)).

## Aktuell halten

Aktualisieren Sie die Dokumentation bei jeder Änderung, die eine Zeile berührt: eine neue PHP-Version, ein Umzug der
Datenbank, ein zweiter Webserver, eine neue Erweiterung, die Kontakt zu Dritten aufnimmt. Ein Änderungsantrag, der die
Dokumentation nicht erwähnt, ist ein Hinweis darauf, dass sie veraltet ist. Prüfen Sie sie mindestens einmal im Jahr,
zusammen mit den Entscheidungen zur Wiederherstellung und einem Wiederherstellungstest.

## Checkliste

- [ ] Jede Zeile hat eine Wahl, einen Grund und ein in Kauf genommenes Risiko.
- [ ] Die Fakten der laufenden Website sind angehängt und passen zu den Entscheidungen.
- [ ] Die Dokumentation nennt Teams, nicht Personen.
- [ ] Sie ist dort abgelegt, wo der Änderungsbeirat und die Prüfenden sie finden.
- [ ] Ein Termin für die Überprüfung ist festgelegt.

## Verwandte Seiten

- [Hosting](README.md): Übersicht dieses Ordners
- [Hosting-Tarife gestalten](../../../hosting/geschaeft/hosting-tarife-gestalten.md) für die Sicht eines Anbieters auf dieselben Entscheidungen
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück und
entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte 2027
geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen Sie mit
[Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
