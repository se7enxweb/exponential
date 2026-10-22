---
title: Pakete für Linux-Distributionen
audience: bundler, hosting, developer
category: bundling
order: 310
summary: Was ein Paketbetreuer einer Linux-Distribution wissen sollte, bevor er Exponential als deb oder rpm paketiert - das Projekt veröffentlicht kein Distributionspaket des CMS (Velocity hat deb- und rpm-Pakete), die Installation schreibt in ihren eigenen Baum, wie sich Composer-Abhängigkeiten zu Distributionsrichtlinien verhalten, was der Post-Install-Schritt tun und lassen sollte und Updates.
keywords: Linux-Distribution, Paketierung, Paketbetreuer, Post-Install, Updates, Linux distribution, packaging, deb, rpm, Debian, Ubuntu, Fedora, Enterprise Linux, FHS, /usr/share, /var/lib, Composer, vendor, post-install, Velocity package, bundle
type: help
---
# Pakete für Linux-Distributionen

Diese Seite richtet sich an Paketbetreuer, die Exponential mit `apt` oder `dnf` installierbar machen möchten. Sie sagt,
was es gibt und was Paketbetreuer selbst entscheiden müssen. Das Projekt veröffentlicht **kein deb- oder rpm-Paket des
Exponential-CMS**; es veröffentlicht aber deb- und rpm-Pakete von **Velocity**, seinem Webserver.

## Inhalt

1. [Was das Projekt veröffentlicht](#was-das-projekt-veröffentlicht)
2. [Wie die Installation aufgebaut ist](#wie-die-installation-aufgebaut-ist)
3. [Composer und die Richtlinien der Distribution](#composer-und-die-richtlinien-der-distribution)
4. [Der Post-Install-Schritt](#der-post-install-schritt)
5. [Updates über den Paketmanager](#updates-über-den-paketmanager)
6. [Bevor Sie veröffentlichen](#bevor-sie-veröffentlichen)

## Was das Projekt veröffentlicht

| Element | Form | Quelle |
|---|---|---|
| Exponential 6 | Git-Tags und das Composer-Paket `se7enxweb/exponential` | [3.1](../../installation/03-den-code-beschaffen.md#31-versionen-tags-und-branches) |
| Erweiterungen | Composer-Pakete, eines je Erweiterung | `composer.json` |
| Velocity | Composer-Paket, deb für Debian 12/13 und Ubuntu 22.04/24.04, rpm für Enterprise Linux 9/10, Docker-Images, Binärdateien | [Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#betriebssystempakete) |

Die Velocity-Pakete sind ein funktionierendes Vorbild dafür, wie das Projekt paketiert: architekturunabhängig, "Bei der
Installation startet nichts", ein Systembenutzer `qbix`, Einstellungen in `/etc/default/exponential-velocity`.

## Wie die Installation aufgebaut ist

Exponential ist "ein Verzeichnis voller PHP-Dateien", dessen Document-Root das Wurzelverzeichnis der Installation ist
([Webserver-Beispiele](../../beispiele/webserver/README.md)). Es schreibt in seinen eigenen Baum:

| Verzeichnis | Geschrieben | Hinweis |
|---|---|---|
| `var/` | immer | Caches, Protokolle, Uploads, Autoload-Arrays, die SQLite-Datenbank |
| `settings/`, `settings/override`, `settings/siteaccess` | von den Installern und der Administration | `ManagedByDeployment=enabled` hindert die Administration daran, Einstellungen zu schreiben |
| `design/`, `extension/` | wenn Pakete aus der Administration installiert werden | |
| `autoload/`, `var/autoload/` | von `ezpgenerateautoloads.php` | Arrays für Kernel und Erweiterungen |

Quelle: [Beschreibbare Verzeichnisse und Rechte](../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md#was-beschreibbar-sein-muss).

Was das für ein Paket bedeutet:

- Installieren Sie den Baum an einer Stelle (zum Beispiel unter `/usr/share/<name>/`) und **verlinken oder binden** Sie
  die beschreibbaren Teile nach `/var/lib/<name>/` und `/etc/<name>/` erst, nachdem Sie getestet haben, dass jeder
  Schreiber den Links folgt. Die Dokumentation beschreibt keinen aufgeteilten Aufbau; diesen Test verantworten Sie.
- `site.ini [FileSettings] VarDir` wird vom Installer je Siteaccess gesetzt (`var/<package>` oder `var/site`), und
  zusätzliche Erweiterungsverzeichnisse lassen sich mit `[ExtensionSettings] AdditionalExtensionDirectories[]`
  hinzufügen ([Zusätzliche Erweiterungsverzeichnisse](../../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md)). Auf diese
  Einstellungen kann sich ein Aufbau stützen.
- Die Webserver-Konfiguration, die Sie ausliefern, muss das Routing von `.htaccess_root` beibehalten.

## Composer und die Richtlinien der Distribution

Exponential hängt von vielen Composer-Paketen ab (`vendor/`) und von Erweiterungen, die ein Composer-Plugin nach
`extension/` installiert. Distributionen unterscheiden sich darin, ob sie mitgelieferte Abhängigkeiten akzeptieren.
Entscheiden Sie gemäß den Richtlinien Ihrer Distribution und beachten Sie:

- der Satz an Erweiterungen ist durch `require` in der `composer.json` festgelegt; erforderliche Erweiterungen
  wegzulassen ändert das Produkt ([Erweiterungen hinzufügen und entfernen](erweiterungen-hinzufuegen-und-entfernen.md));
- `composer licenses` liefert die Lizenz jedes Composer-Pakets für Ihre Copyright-Datei
  ([Komponenten Dritter](komponenten-dritter-und-ihre-lizenzen.md)).

## Der Post-Install-Schritt

Ein Distributionspaket sollte nicht von sich aus eine Website installieren:

- **Tun Sie:** den Systembenutzer anlegen, die Besitzrechte der beschreibbaren Verzeichnisse setzen, eine deaktivierte
  Webserver- und Cron-Konfiguration installieren, die Autoloads neu erzeugen, ausgeben, wo die Dokumentation und der
  Installationsbefehl zu finden sind.
- **Tun Sie nicht:** eine Datenbank anlegen, `exp:install` mit Standardwerten ausführen oder einen Webserver starten.
  Der Administrator führt die Installation einmal aus, mit seiner eigenen URL, Datenbank und E-Mail
  ([Unbeaufsichtigte Installation mit exp:install](unbeaufsichtigte-installation-mit-exp-install.md)).

## Updates über den Paketmanager

Ein Paket-Update ersetzt Code; Updates von Exponential haben eine zweite Hälfte, die Datenbank-Update-Dateien und die
Checkliste jedes Zeitraums ([Kapitel 11.6](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand)). Das Paket kann:

- nach dem Update die Caches leeren und die Autoloads neu erzeugen
  ([Caches und Autoloads](caches-und-autoloads-nach-dem-bauen.md));
- dem Administrator mitteilen, dass ein Datenbank-Update fällig sein könnte, mit dem Link;
- nie Datenbank-Updates unbeaufsichtigt auf einer Website ausführen, die es nicht kennt.

## Bevor Sie veröffentlichen

- [ ] Die Copyright-Datei führt Exponential (GPL-2.0-or-later), jede Erweiterung und jedes mitgelieferte Paket auf.
- [ ] Ein Quellpaket ist verfügbar, wie es Distributionen ohnehin verlangen.
- [ ] Paketname und Beschreibung folgen [Ein Bundle benennen und gestalten](ein-bundle-benennen-und-gestalten.md).
- [ ] Installation, Upgrade und Entfernung sind auf einem sauberen System getestet.
- [ ] Sicherheitskorrekturen haben einen Weg: Sie verfolgen die Releases und bauen neu
      ([Updates und Upgrade-Pfade](updates-und-upgrade-pfade.md)).

## Verwandte Seiten

- [Betriebssystemunterstützung](../betriebssystemunterstuetzung.md)
- [Velocity-Pakete, Docker-Images und Binärdateien](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md)
- [GPL-Pflichten beim Weitergeben](gpl-pflichten-beim-weitergeben.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
