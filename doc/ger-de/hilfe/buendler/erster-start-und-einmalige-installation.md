---
title: Erster Start und einmalige Installation
audience: bundler, hosting
category: bundling
order: 230
summary: Warum ein Bundle die Website beim ersten Start beim Empfänger installiert und nie zur Build-Zeit oder bei jedem Start, der dokumentierte Test "bereits installiert", ein Ablauf für den ersten Start mit Besitzrechten, Geheimnissen, der Installation, der Passwortübergabe und dem Aufräumen und was ein Bundle nie enthalten darf, wenn es Ihre Hände verlässt.
keywords: erster Start, einmalige Installation, Einstiegspunkt, bereits installiert, Geheimnisse, first boot, install once, entry point, already installed, DatabaseSettings, settings/override, idempotent, cloud-init, systemd, container, image, secrets, initial-admin-password
type: help
---
# Erster Start und einmalige Installation

Diese Seite richtet sich an Bündler von Images, Containern und Appliances. Die Installer schreiben Geheimnisse,
eindeutige Schlüssel und die Adressen des Empfängers in die Installation. Ein Bundle sollte daher den Code
installationsbereit ausliefern und die Installation **einmal** ausführen, beim ersten Start beim Empfänger.

## Inhalt

1. [Warum nicht zur Build-Zeit installieren](#warum-nicht-zur-build-zeit-installieren)
2. [Warum nicht bei jedem Start installieren](#warum-nicht-bei-jedem-start-installieren)
3. [Der Test auf eine bestehende Installation](#der-test-auf-eine-bestehende-installation)
4. [Ein Ablauf für den ersten Start](#ein-ablauf-für-den-ersten-start)
5. [Was nicht im Bundle sein darf](#was-nicht-im-bundle-sein-darf)

## Warum nicht zur Build-Zeit installieren

Eine Installation, die auf Ihrer Build-Maschine entsteht und an viele Empfänger geht, gäbe jedem Empfänger:

- dasselbe Administratorpasswort oder eines, das Ihr Build-Protokoll gesehen hat;
- dieselben Datenbank-Zugangsdaten in `settings/siteaccess/*/site.ini.append.php`;
- dasselbe Website-Geheimnis der E-Mail-Einstellungen, das `CreateSites` nach
  `settings/override/mailpreferences.ini.append.php` schreibt
  ([4.4](../../installation/04-installationsweg-waehlen.md#einstellungen));
- die URL und Hostnamen Ihres Builds statt ihrer eigenen.

Die Ausnahme ist eine öffentliche Demo, die nach Zeitplan von Grund auf neu gebaut wird
([Bundles für Schulung und Demo](bundles-fuer-schulung-und-demo.md)).

## Warum nicht bei jedem Start installieren

`exp:install` verwendet standardmäßig `--db-action=remove`, das zuerst die Datenbank leert. Ein Einstiegspunkt, der bei
jedem Start installiert, zerstört die Website bei jedem Neustart. "Führen Sie die Installation in einem Container
einmal aus (zum Beispiel, wenn `settings/override/site.ini.append.php` kein `[DatabaseSettings]` hat), nicht bei
jedem Start" ([6.11](../../installation/06-kickstarter.md#611-in-ci-und-containern)).

## Der Test auf eine bestehende Installation

```bash
grep -q '^\[DatabaseSettings\]' settings/override/site.ini.append.php 2>/dev/null
```

"Der Test ... ist derselbe, mit dem `exp:install` selbst entscheidet, dass ein Verzeichnis bereits eine Installation
enthält" ([4.6](../../installation/04-installationsweg-waehlen.md#46-unbeaufsichtigte-installationen-ci-und-container)). Er muss die
dauerhafte Kopie von `settings/override/` lesen: In einem Container liegt dieses Verzeichnis auf einem Volume
([Container-Images](container-images.md)).

## Ein Ablauf für den ersten Start

1. **Warten Sie auf die Datenbank** (nur bei Server-Engines), bis sie Verbindungen annimmt.
2. **Prüfen Sie die Besitzrechte** von `var/`, `settings/` und `design/` für den Benutzer der Website
   ([Dateirechte und Besitz von var/](dateirechte-und-besitz-von-var.md)).
3. **Überspringen Sie den Rest, wenn bereits installiert** (der Test oben).
4. **Lesen Sie die Geheimnisse** aus dem Geheimnisspeicher der Plattform in `EXP_INSTALL_DB_PASSWORD` und, falls Sie
   eines setzen, das Administratorpasswort.
5. **Installieren Sie** als Benutzer der Website: `exp:install` mit URL und E-Mail des Empfängers oder den Kickstarter
   mit einer erzeugten Vorlage.
6. **Prüfen Sie den Exit-Status**; bei `1` bewahren Sie `var/log/setup.log` auf und brechen ab. Der Wartungsmodus
   bleibt aktiv, sodass Besucher eine 503-Seite sehen, keine halb gebaute Website.
7. **Übergeben Sie das Passwort**: Zeigen Sie `var/log/initial-admin-password` einmal über den Kanal Ihrer Plattform
   an (eine Konsolenmeldung, eine Panel-Seite, eine sichere E-Mail) und löschen Sie die Datei dann.
8. **Entfernen Sie Installationsdateien**: eine erzeugte `kickstart.ini`, temporäre Paketkopien.
9. **Starten Sie die Auslieferung** ([Webserver- und Engine-Wahl](webserver-und-engine-wahl.md)) und den
   Cron-Zeitplan ([Cronjobs](../hosting/betrieb/cronjobs.md)).

Ein Container-Einstiegspunkt, der die Schritte 3, 5 und 9 ausführt, steht unter
[Container-Images](container-images.md#ein-einstiegspunkt).

## Was nicht im Bundle sein darf

- [ ] Keine `settings/override/*.ini.append.php` mit Datenbank-Zugangsdaten, Mailpasswörtern oder Geheimnissen Ihres
      Builds.
- [ ] Kein von einem Installer-Lauf geschriebenes `settings/siteaccess/`.
- [ ] Keine `kickstart.ini` (die Vorlagen `kickstart.ini-dist` und `kickstart.ini--example` sind in Ordnung).
- [ ] Keine `var/log/initial-admin-password`, keine Protokolle, keine Caches Ihres Builds.
- [ ] Keine SQLite-Datenbankdatei mit Inhalten, es sei denn, es ist bewusst ein Demo-Bundle.
- [ ] Keine `config.env.php` Ihrer Build-Maschine; sie nennt die Umgebung der Maschine (`EXP_ENV`) und wird
      absichtlich nicht versioniert (`config.env.php.example`).
- [ ] Keine `.git`-Verzeichnisse, die Sie nicht ausliefern wollten.

## Verwandte Seiten

- [Unbeaufsichtigte Installation mit exp:install](unbeaufsichtigte-installation-mit-exp-install.md)
- [Bereitstellung automatisieren](../hosting/bereitstellung/bereitstellung-automatisieren.md)
- [Sicherheitsvorgaben eines Bundles](sicherheitsvorgaben-eines-bundles.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
