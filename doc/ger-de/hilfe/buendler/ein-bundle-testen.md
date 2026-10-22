---
title: Ein Bundle testen
audience: bundler, developer
category: bundling
order: 430
summary: Was Sie vor einem Bundle-Release testen - den Build, eine saubere Installation mit jeder Datenbank und PHP-Version, die Sie zusagen, den ersten Start, eine Offline-Installation, Rechte, Sicherheitsprüfungen, Integrität, ein Upgrade vom vorigen Bundle-Release sowie eine Testmatrix und ein Testprotokoll zum Aufbewahren.
keywords: Test, testen, Bundle, Testmatrix, saubere Installation, Probelauf, Rauchtest, Exit-Status, Upgrade-Test, test, testing, bundle, test matrix, clean install, dry run, smoke test, setup.log, exit status, checkclasses, checkmanifest, upgrade test, offline, PHP versions, databases
type: help
---
# Ein Bundle testen

Diese Seite richtet sich an Bündler, die ein Release vor der Auslieferung testen. Ein Bundle kombiniert viele Teile;
der einzige Beweis, dass sie zusammen funktionieren, ist, das Bundle so zu installieren und zu verwenden, wie es ein
Empfänger tun würde.

## Inhalt

1. [Die Testmatrix](#die-testmatrix)
2. [Prüfungen am Build](#prüfungen-am-build)
3. [Installationstests](#installationstests)
4. [Prüfungen an der laufenden Website](#prüfungen-an-der-laufenden-website)
5. [Upgrade-Test](#upgrade-test)
6. [Das Testprotokoll](#das-testprotokoll)

## Die Testmatrix

Führen Sie auf, was Sie zusagen, und testen Sie jede zugesagte Kombination, nicht mehr:

```text
PHP:        8.x, 8.y
Database:   SQLite 3, PostgreSQL <version>
Server:     Velocity (qbix) / Apache + PHP-FPM / nginx + PHP-FPM
Install:    exp:install / kickstarter / setup wizard
Network:    online / offline
Upgrade:    from bundle <previous version>
```

Die CI des Projekts testet PostgreSQL 15 und MySQL 8.0 und führt für die anderen die Unit-Tests der Treiber aus
([Datenbankunterstützung](../datenbankunterstuetzung.md#auf-einen-blick)); eine Kombination, die Ihr Bundle darüber hinaus
zusagt, müssen Sie selbst nachweisen.

## Prüfungen am Build

| Prüfung | Befehl oder Methode | Bestanden, wenn |
|---|---|---|
| Composer | `composer validate --no-check-publish`; `composer install --no-dev` aus der Lock-Datei | keine Fehler |
| PHP-Erweiterungen | `php bin/php/console exp:velocity ext check` | nichts fehlt |
| Klassen | `php bin/php/console exp:checkclasses` | keine Klasse, die PHP nicht laden will |
| Integrität | `php bin/php/checkmanifest.php --all` | Exit `0` oder nur Ihre aufgeführten Änderungen |
| Lizenzen | `composer licenses --no-dev` | kein Paket ohne eine von Ihnen geklärte Lizenz |
| Nichts Geheimes ausgeliefert | das Artefakt auf `settings/override/*.ini.append.php`, `kickstart.ini`, `var/log/` untersuchen | nichts vorhanden |

## Installationstests

1. **Probelauf:** `php bin/php/console exp:install --dry-run ...` oder `exp:kickstarter run --dry-run`; Exit `0`.
2. **Installation:** Exit `0`; `var/log/setup.log` endet mit einer RESULT-Zeile ohne Fehler. Das Protokoll enthält
   "Zustandsprüfungen nach der Installation (Datenbank, deklarierte Tabellen, Inhaltsklassen, Objekte,
   Inhaltswurzel, Namenslisten, Einstellungen, Siteaccesses, Besitzrechte der Dateien)"
   ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md#was-die-logs-ihnen-sagen)).
3. **Zweiter Start:** Der Test auf eine bestehende Installation überspringt die Installation; die Daten sind noch da.
4. **Offline:** dieselbe Installation ohne ausgehendes Netzwerk ([Offline-Bundles](offline-bundles.md)).
5. **Fehlerfall:** Ein falsches Datenbankpasswort ergibt Exit `1`, aktiven Wartungsmodus und einen lesbaren Grund in
   `var/log/setup.log`.

## Prüfungen an der laufenden Website

- [ ] Die Website und die Administration antworten unter den dokumentierten Adressen.
- [ ] Mit dem übergebenen Passwort anmelden; es ändern; die Datei `var/log/initial-admin-password` ist verschwunden.
- [ ] Einen Artikel mit einem Bild veröffentlichen; er erscheint auf der Website; Bildvarianten werden erzeugt.
- [ ] Die Suche findet ihn.
- [ ] Cron läuft (`runcronjobs.php`) als Benutzer der Website ohne Rechtefehler.
- [ ] `/.git/config`, `/settings/site.ini`, `/composer.json` werden nicht ausgeliefert
      ([13.16](../../installation/13-sicherheitshaertung.md#1316-checkliste-für-den-livegang)).
- [ ] Kein Hinweisbalken **Demo-Modus** (außer in Demo-Bundles).
- [ ] **Info > Über** und **Info > Copyright** zeigen die Versionen und Lizenzen.
- [ ] Keine Dateien unter `var/` gehören root.
- [ ] Nach einem Neustart des gesamten Bundles gilt alles oben Genannte weiterhin.

## Upgrade-Test

1. Installieren Sie das vorige Bundle-Release; fügen Sie einige Inhalte hinzu.
2. Wenden Sie Ihr Update so an, wie es die Empfänger tun werden
   ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).
3. Prüfen Sie die Versionszeile (erwarteter Wert für die Linie 6.0.15: `6.0.15stable`, Abfrage in
   [11.2](../../installation/11-aktualisieren.md#112-welche-version-betreiben-sie)), die Inhalte, die Anmeldung und die Prüfungen
   oben.
4. Folgen Sie [11.7 Das Upgrade überprüfen](../../installation/11-aktualisieren.md#117-das-upgrade-überprüfen).

## Das Testprotokoll

Bewahren Sie je Release auf: die Matrix mit Ergebnissen, `var/log/setup.log` und `var/log/kickstart.log` jeder
Installation, das Build-Protokoll
([Lock-Dateien und reproduzierbare Builds](lock-dateien-und-reproduzierbare-builds.md#das-build-protokoll)) und die Namen der
Tester. Partner, die Exponential-Releases selbst testen, können Ergebnisse zurückmelden
([Releases testen](../partner/releases-testen.md)).

## Verwandte Seiten

- [Checkliste für die Bundle-Veröffentlichung](checkliste-fuer-die-bundle-veroeffentlichung.md)
- [Testen in einer Sandbox (Seitenpakete)](../seitenpakete/eigene/in-einer-sandbox-testen.md)
- [Benchmarking einer gehosteten Website](../hosting/leistung/benchmark-einer-gehosteten-website.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
