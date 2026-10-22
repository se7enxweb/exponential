---
title: Eine Website bereitstellen
audience: cms-developer
category: cms-development
order: 40
summary: Für CMS-Entwickler, wie eine Website vom Repository auf Staging und Live gelangt und Änderungen sicher ausgeliefert werden. Die Seite behandelt die Wahl des Webservers, was eine Bereitstellung überträgt, Einstellungen je Umgebung, exp:velocity deploy für PHP-Änderungen, die leichteren Schritte für Templates und Einstellungen, den Wartungsmodus, das Sperren der Konfiguration auf bereitgestellten Websites und die Prüfungen danach.
keywords: Bereitstellung, bereitstellen, Deployment, Release, Staging, Live, Produktion, exp:velocity deploy, Velocity, FrankenPHP, Apache, PHP-FPM, Neuladen, Autoloads, Caches, EXP_ENV, config.env.php, Umgebungseinstellungen, Wartungsmodus, ManagedByDeployment, Live-Gang, deploy, deployment, release, production, reload, environment settings, maintenance mode, go live
type: help
---
# Eine Website bereitstellen

Diese Seite richtet sich an Entwickler, die ein Projekt vom Repository auf Staging und Live bringen und nach dem Start
Änderungen ausliefern. Die Regel hinter jedem Schritt: Der Server muss den neuen Code ausführen, **bevor** die Caches
geleert werden, die fertige Seiten halten, sonst wird alte Ausgabe erneut zwischengespeichert.

## Inhalt

1. [Wählen, wie die Website ausgeliefert wird](#wählen-wie-die-website-ausgeliefert-wird)
2. [Was eine Bereitstellung überträgt](#was-eine-bereitstellung-überträgt)
3. [Einstellungen je Umgebung](#einstellungen-je-umgebung)
4. [Die erste Bereitstellung](#die-erste-bereitstellung)
5. [Eine PHP-Änderung ausliefern](#eine-php-änderung-ausliefern)
6. [Templates, Styles und Einstellungen ausliefern](#templates-styles-und-einstellungen-ausliefern)
7. [Wartungsmodus](#wartungsmodus)
8. [Die Konfiguration auf bereitgestellten Websites sperren](#die-konfiguration-auf-bereitgestellten-websites-sperren)
9. [Nach jeder Bereitstellung](#nach-jeder-bereitstellung)

## Wählen, wie die Website ausgeliefert wird

| Server | Wann |
|---|---|
| Exponential Velocity (Engine `qbix`) | die empfohlene Engine: ein PHP-Anwendungsserver, der mit Exponential ausgeliefert wird, mit persistenten Workern, eigenem Antwort-Cache und HTTPS |
| FrankenPHP | die produktionsreife Alternative |
| Apache oder nginx mit PHP-FPM | wo der Stack des Hosts feststeht |

Jeder mit seiner Einrichtung: [Exponential bereitstellen](../../anleitungen/bereitstellung.md) und
[Einen Webserver wählen](../hosting/server/einen-webserver-waehlen.md).

## Was eine Bereitstellung überträgt

| Kommt aus | Überträgt |
|---|---|
| Composer und Git | den Kernel und die mitgelieferten Erweiterungen in den Versionen Ihrer `composer.json` und `composer.lock` |
| Ihren Repositorys | Ihre Erweiterungen: Design, Templates, Einstellungen, Code, Pakete |
| Dem Rechner selbst | `settings/override/` (Datenbank, E-Mail, Hosts), `config.env.php`, `var/` (Speicher, Caches, Logs) |
| Der Datenbank | Inhalte, Klassen, Benutzer, Layouts |

Inhalte werden nie von der Entwicklung auf Live bereitgestellt: Redakteure erstellen sie auf Live. Klassen und Layouts
gelangen über Pakete und Übertragungsdateien auf Live, einmal angewendet
([Seitenpakete und Installer](seitenpakete-und-installer.md),
[Layouts für CMS-Entwickler](layouts-fuer-cms-entwickler.md#layouts-zwischen-installationen)).

## Einstellungen je Umgebung

`EXP_ENV` benennt in `config.env.php` (kopiert aus `config.env.php.example`, außerhalb der Versionsverwaltung) die
Umgebung eines Rechners, und jede Einstellungsdatei kann eine Variante dafür haben, etwa
`settings/override/site.ini.test.append.php`. E-Mail-Transport, Adressen der Website und Debug-Einstellungen
unterscheiden sich dann je Rechner, ohne Änderungen nach jeder Bereitstellung
([Einstellungen je Umgebung](../../funktionen/6.0/umgebungseinstellungen.md)).

## Die erste Bereitstellung

1. Prüfen Sie den Rechner: PHP-Version und -Erweiterungen, beschreibbare Verzeichnisse, Datenbank
   ([Anforderungen auf einen Blick](../hosting/grundlagen/anforderungen-auf-einen-blick.md)).
2. Holen Sie den Code (`composer install` aus Ihrem Projekt), fügen Sie Ihre Erweiterungen hinzu, schreiben Sie
   `settings/override/`.
3. Installieren oder importieren Sie die Datenbank, dann Ihre Pakete und Layouts.
4. Erzeugen Sie die Autoloads, leeren Sie die Caches, starten Sie den Server.
5. Planen Sie die Cronjobs ein ([Cronjobs](cronjobs.md)) und richten Sie Sicherungen ein
   ([Was zu sichern ist](../hosting/betrieb/backups-was-gesichert-werden-muss.md)).
6. Gehen Sie die [Checkliste für den Projektstart](checkliste-fuer-den-projektstart.md) durch.

## Eine PHP-Änderung ausliefern

Ein Befehl erledigt jeden Schritt in der richtigen Reihenfolge und hält beim ersten Fehler an:

```bash
./console exp:velocity deploy --dry-run --allow-root-user   # what it would do
./console exp:velocity deploy --allow-root-user             # the usual case
./console exp:velocity deploy --kernel --allow-root-user    # a kernel class was added or renamed
```

Er erzeugt die Autoloads neu, leert die INI- und Template-Caches, lässt PHP-FPM und Velocity den neuen Code ausführen,
leert dann die Inhalts- und Antwort-Caches und wärmt die ersten Seiten vor. Optionen wie `--no-fpm`, `--no-velocity`
und `--packer` (leert zusätzlich gepackte Skripte und Styles) erklärt
[Eine PHP-Änderung mit einem Befehl ausliefern](../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern) und für
Hoster [Änderungen bereitstellen](../hosting/betrieb/aenderungen-ausrollen.md).

## Templates, Styles und Einstellungen ausliefern

| Änderung | Schritte |
|---|---|
| Ein vorhandenes Template | `./console exp:cache clear --id=template --allow-root-user`, dann `./console exp:velocity cache clear --allow-root-user` |
| Ein neues Template oder eine neue Override-Regel | `ini`, `template-override` und `content` leeren, dann den Cache von Velocity |
| Ein Stylesheet oder Skript | mit `--packer` bereitstellen oder die Caches für Packer und Template-Blöcke leeren; fügen Sie Asset-Links nie `?v=` hinzu |
| Eine INI-Datei | `--clear-tag=ini`, Velocity neu starten |

Wenn unklar ist, welcher Cache, siehe
[Caching für CMS-Entwickler](caching-fuer-cms-entwickler.md#welcher-cache-nach-welcher-änderung).

## Wartungsmodus

Für Änderungen, bei denen die Website geschlossen sein muss (eine Datenbankaktualisierung, ein großer Import):

```bash
./console exp:maintenance on --allow-root-user
./console exp:maintenance status --allow-root-user
./console exp:maintenance off --allow-root-user
```

`php bin/php/maintenance.php on --message="Back at 06:00" --until=2h` setzt eine Nachricht und ein erwartetes Ende; die
Administration bleibt erreichbar. **Setup** > **Maintenance** tut dasselbe im Browser
([Wartungsmodus](../../funktionen/6.0/wartungsmodus.md)).

## Die Konfiguration auf bereitgestellten Websites sperren

Wenn Einstellungen unter Versionsverwaltung liegen, gingen Änderungen in der Administration bei der nächsten
Bereitstellung verloren. Mit `site.ini [ConfigurationSettings] ManagedByDeployment=enabled` im
Administrations-Siteaccess sind Einstellungseditor, Template-Editor, Aktivierung von Erweiterungen, Paketinstallation
und die Code-Generatoren im Browser geschlossen; Kommandozeilenskripte und die Bereitstellung schreiben weiterhin
([Konfiguration, die die Bereitstellung verwaltet](../../funktionen/6.0/durch-deployment-verwaltet.md)).

## Nach jeder Bereitstellung

- Die Startseite und einige tiefe Seiten antworten mit 200 (`curl -sI https://www.example.com/`).
- `./console exp:velocity status` oder der Status des Webservers zeigt die erwarteten Prozesse.
- `./console exp:cache status --allow-root-user` zeigt die erwarteten Ebenen.
- `var/log/error.log` hat keine neuen Fehler.
- Die Cronjobs laufen weiterhin.

## Verwandte Seiten

- [Velocity für Hoster](../hosting/server/velocity-fuer-hoster.md)
- [Aktualisieren](../../anleitungen/aktualisieren.md)
- [Leistung](leistung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
