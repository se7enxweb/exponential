---
title: Server- und VM-Images
audience: bundler, hosting
category: bundling
order: 290
summary: Wie Sie Exponential in einem Cloud-Server- oder VM-Image bündeln - die Betriebssysteme, die das Projekt aufführt, was zur Build-Zeit installiert wird, was dem ersten Start überlassen bleibt, die Dienste, die ein Image starten muss (Webserver oder Velocity, Cron, Backups), und eine Checkliste für das Image.
keywords: Server-Image, virtuelle Maschine, Cloud-Image, Marktplatz, Betriebssystem, Stufe, erster Start, Backups, server image, virtual machine, VM, cloud image, marketplace, operating system, tier, first boot, cloud-init, systemd, Velocity, PHP-FPM, cron, backups, bundle
type: help
---
# Server- und VM-Images

Diese Seite richtet sich an Bündler, die einen fertigen Server veröffentlichen: ein Image für einen Cloud-Marktplatz,
eine Vorlage für virtuelle Maschinen, einen vorinstallierten dedizierten Server. Die Hinweise sind allgemein;
Exponential liefert keine Build-Dateien für Images aus.

## Inhalt

1. [Das Betriebssystem wählen](#das-betriebssystem-wählen)
2. [Zur Build-Zeit](#zur-build-zeit)
3. [Beim ersten Start](#beim-ersten-start)
4. [Dienste, die das Image betreibt](#dienste-die-das-image-betreibt)
5. [Checkliste für das Image](#checkliste-für-das-image)

## Das Betriebssystem wählen

[Betriebssystemunterstützung](../betriebssystemunterstuetzung.md) ordnet Systeme in vier Stufen ein, von "hier getestet" bis "heute
nicht möglich". Bauen Sie Images auf einem System der Stufe 1 oder 2 ([Die vier Stufen](../betriebssystemunterstuetzung.md#die-vier-stufen)).
Hinweise zu Red Hat Enterprise Linux und seinen Nachbauten, zu Debian und Ubuntu sowie zu Plesk stehen in
[2.9 Hinweise zu Betriebssystemen](../../installation/02-voraussetzungen.md#29-hinweise-zu-betriebssystemen).

PHP: Exponential 6.0.15 läuft auf PHP 8.0 bis 8.5; "Velocity, der empfohlene Webserver, braucht PHP 8.1"
([Datenbankunterstützung](../datenbankunterstuetzung.md#versionen-am-code-überprüft)). Die erforderlichen
PHP-Erweiterungen stehen in [2.3](../../installation/02-voraussetzungen.md#23-php-extensions).

## Zur Build-Zeit

1. Installieren Sie PHP, seine Erweiterungen, Composer und, falls das Image einen mitbringt, den Datenbankserver.
2. Legen Sie den Systembenutzer der Website an.
3. Legen Sie den Code aus Ihrer Lock-Datei an seinen Platz, als dieser Benutzer
   ([Den Code beschaffen](den-code-fuer-ein-bundle-beschaffen.md)).
4. Importieren Sie Ihre Seitenpakete ([Offline-Bundles](offline-bundles.md)).
5. Erzeugen Sie die Autoloads neu ([Caches und Autoloads nach dem Bauen](caches-und-autoloads-nach-dem-bauen.md)).
6. Installieren Sie die Webserver-Konfiguration oder die Velocity-Unit
   ([Webserver- und Engine-Wahl](webserver-und-engine-wahl.md)).
7. Installieren Sie die Crontab für `runcronjobs.php`, deaktiviert, bis die Website installiert ist
   ([Cronjobs](../hosting/betrieb/cronjobs.md)).
8. Fügen Sie das Skript für den ersten Start hinzu; lassen Sie jedes Geheimnis weg.
9. Räumen Sie auf: Paket-Caches, Shell-Historie, Protokolle, SSH-Hostschlüssel, falls Ihre Plattform sie neu erzeugt.

## Beim ersten Start

Das Skript für den ersten Start tut, was
[Erster Start und einmalige Installation](erster-start-und-einmalige-installation.md#ein-ablauf-für-den-ersten-start) beschreibt: auf die
Datenbank warten, die Besitzrechte prüfen, überspringen, wenn bereits installiert, Geheimnisse von der Plattform lesen
(Instanz-Metadaten, ein User-Data-Feld von cloud-init), `exp:install` mit URL und E-Mail des Empfängers ausführen, das
Passwort übergeben und `var/log/initial-admin-password` löschen, die Auslieferung starten, Cron aktivieren.

Wenn die URL des Empfängers beim ersten Start nicht bekannt ist, fragen Sie sie entweder im Startformular Ihrer
Plattform ab, oder installieren Sie auf der Adresse des Servers und dokumentieren Sie, wie `SiteURL` danach geändert
wird ([10.2 Siteaccesses und Website-Adressen](../../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).

## Dienste, die das Image betreibt

| Dienst | Hinweise |
|---|---|
| Velocity oder Apache/nginx mit PHP-FPM | ein Benutzer für alle oder eine gemeinsame Gruppe ([Dateirechte](dateirechte-und-besitz-von-var.md)) |
| Der Datenbankserver, falls enthalten | eigene Backups ([Datenbank-Backups je Engine](../hosting/betrieb/datenbank-backups-je-engine.md)) |
| Cron für `runcronjobs.php` | die Crontab des Benutzers der Website |
| Backups | die Datenbank und `var/storage`, `settings/override`, `settings/siteaccess` |
| Protokollrotation | `var/log` und die Protokolle je Website ([Protokollansicht und -rotation](../hosting/betrieb/log-viewer-und-log-rotation.md)) |
| Zustandsprüfung | eine HTTP-Prüfung, die 503 während der Installation als "wird installiert" wertet ([Zustandsprüfungen und Verfügbarkeit](../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)) |

## Checkliste für das Image

- [ ] Betriebssystem der Stufe 1 oder 2; PHP und Erweiterungen mit `exp:velocity ext check` geprüft.
- [ ] Code aus der Lock-Datei; Besitzrechte gesetzt; Autoloads frisch.
- [ ] Keine Geheimnisse, keine installierte Website, keine `kickstart.ini`, keine Protokolle im Image.
- [ ] Der erste Start installiert einmal, als Benutzer der Website, und übergibt das Passwort.
- [ ] Cron, Backups, Protokollrotation und die Zustandsprüfung sind eingerichtet und dokumentiert.
- [ ] Sicherheitsvorgaben angewendet ([Sicherheitsvorgaben eines Bundles](sicherheitsvorgaben-eines-bundles.md)).
- [ ] Lizenzhinweise und das Quellcode-Angebot sind im Image und in seinem Eintrag
      ([GPL-Pflichten](gpl-pflichten-beim-weitergeben.md)).
- [ ] Der Update-Weg ist dokumentiert ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).

## Verwandte Seiten

- [VPS-Hosting](../hosting/modelle/vps-hosting.md), [Dedizierte Server](../hosting/modelle/dedizierte-server.md)
- [Checkliste für einen neuen Server](../hosting/checklisten/checkliste-fuer-einen-neuen-server.md)
- [Appliances](appliances.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
