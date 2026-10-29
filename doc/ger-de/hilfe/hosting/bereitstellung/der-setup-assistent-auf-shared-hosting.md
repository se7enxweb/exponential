---
title: Der Setup-Assistent auf Shared Hosting
audience: hosting
category: hosting
order: 50
summary: Wie Kunden ohne Shell-Zugang Exponential im Browser mit dem Setup-Assistenten installieren, und was der Hoster vorbereitet - Hochladen des Codes, beschreibbare Verzeichnisse, die Datei .htaccess, HTTPS, die Datenbank, das Festhalten im Wartungsmodus während des Laufs und eine kickstart.ini, die Seiten vorfüllt.
keywords: Setup-Assistent, Shared Hosting, Installation im Browser, keine Shell, FTP, CheckValidity, .htaccess_root, .htaccess, Systemtest, Verzeichnisberechtigungen, Festhalten im Wartungsmodus, kickstart.ini, vorfüllen, HTTPS, setup wizard, shared hosting, browser install, no shell, system check, directory permissions, maintenance hold, pre-fill
type: help
---
# Der Setup-Assistent auf Shared Hosting

Diese Seite ist für Hoster, deren Kunden Exponential selbst im Browser installieren, typischerweise auf Shared Hosting ohne
Shell-Zugang, und für die Support-Mitarbeitenden, die ihnen helfen. Sie beantwortet: was der Hoster vorbereiten muss, was der Kunde
sieht und wie der Assistent die Website schützt, während er läuft. Die Referenz Seite für Seite ist
[Kapitel 5](../../../installation/05-setup-assistent.md).

## Inhalt

1. [Wann der Assistent die richtige Wahl ist](#wann-der-assistent-die-richtige-wahl-ist)
2. [Was der Hoster vorbereitet](#was-der-hoster-vorbereitet)
3. [Was der Kunde tut](#was-der-kunde-tut)
4. [Während er läuft](#während-er-läuft)
5. [Nach dem Assistenten](#nach-dem-assistenten)
6. [Seiten mit kickstart.ini vorfüllen](#seiten-mit-kickstartini-vorfüllen)

## Wann der Assistent die richtige Wahl ist

Das Installationsbuch empfiehlt ihn für Shared Hosting ohne Shell-Zugang: Er braucht nur einen Browser und FTP-Zugang
([4.5](../../../installation/04-installationsweg-waehlen.md#45-welche-methode-für-welche-situation)). Er ist außerdem die einzige Methode, die
jede Entscheidung auf einer Seite mit einer Erklärung zeigt. Wenn Sie für den Kunden Befehle ausführen können, ist
[exp:install](exp-install-fuer-hoster.md) schneller und skriptfähig.

## Was der Hoster vorbereitet

| Element | Warum |
|---|---|
| PHP 8.0 oder später mit den erforderlichen Erweiterungen; Apache mit `mod_rewrite` und PHP-FPM ist die klassische Einrichtung für Shared Hosting | [Anforderungen auf einen Blick](../grundlagen/anforderungen-auf-einen-blick.md) |
| Der Code, mit `vendor/` (den Abhängigkeiten), im Webspace des Kunden | der Assistent kann Composer nicht ausführen; laden Sie ein vollständiges Release hoch |
| `settings/`, `var/` und `design/` für den Benutzer beschreibbar, als der PHP läuft | der Systemtest prüft `[directory_permissions] CheckList` ([Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)) |
| `.htaccess` an seinem Platz: eine Kopie der mitgelieferten `.htaccess_root` | die Rewrite-Regeln, die verhindern, dass interne Dateien ausgeliefert werden |
| HTTPS auf der Domain, bevor der Assistent geöffnet wird | das Datenbankpasswort und das Administratorpasswort reisen in versteckten Feldern mit jeder Seite ([5.1](../../../installation/05-setup-assistent.md#wie-sich-der-assistent-ihre-antworten-merkt)) |
| Eine Datenbank und ihr Benutzer, oder nichts für SQLite 3 | SQLite braucht nur ein beschreibbares `var/storage/sqlite3/` |

> **Tipp**
> Wenn `.htaccess` fehlt, verlangt die Seite **Seitensicherheit** des Assistenten sie mit `cp .htaccess_root .htaccess` und dem Hinweis,
> dass Sie die Datei ohne Shell-Zugang mit einem FTP-Client kopieren oder Ihren Hosting-Anbieter darum bitten müssen
> ([5.3.13](../../../installation/05-setup-assistent.md#5313-seitensicherheit)). Legen Sie sie vorab an, dann wird die Seite übersprungen.

Die Bereitstellung des Hosters kann das mit der Konsole erledigen, bevor der Kunde den Assistenten öffnet. Als root
braucht die Kopie `--allow-root-user` und sollte dem Benutzer des Kontos gegeben werden:

```bash
cd /path/to/the/installation
php bin/php/console exp:setup:htaccess --apply --allow-root-user --owner=<konto-benutzer>:<konto-gruppe>
php bin/php/console exp:setup:config --apply --allow-root-user --owner=<konto-benutzer>:<konto-gruppe>   # optional
```

Ohne `--owner` erhält eine neue Datei den Eigentümer der Vorlage, aus der sie kopiert wird, niemals root. Einzelheiten:
[.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md).

## Was der Kunde tut

1. Er öffnet die Adresse der Website. Solange `[SiteAccessSettings] CheckValidity=true` gilt (der mitgelieferte Wert), erscheint die erste
   Seite des Assistenten, welchen Pfad man auch öffnet ([5.1](../../../installation/05-setup-assistent.md#51-wie-der-assistent-startet)).
2. Er geht die Seiten durch: Willkommen, Systemtest, Mail, Datenbank, Sprachen, Seitenpaket, Siteaccesses, Details der Website,
   Administrator und die Installation selbst ([5.3](../../../installation/05-setup-assistent.md#53-die-seiten-schritt-für-schritt)).
3. Er wählt ein Administratorpasswort. Der Assistent lehnt ein kurzes oder bekanntes ab
   ([Das Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
4. Er erreicht die Seite **Beendet** mit den Adressen der neuen Website.

Wenn der Systemtest scheitert, gibt er Ursache und Abhilfe aus, bei Berechtigungen die genauen Befehle `chmod` und `chown`; auf Shared
Hosting kann diese oft nur der Hoster ausführen.

## Während er läuft

Der Assistent hält die Website für den Browser fest, der ihn gestartet hat
([5.2](../../../installation/05-setup-assistent.md#52-die-browser-sperre-und-der-wartungsmodus)):

- Der erste abgeschickte Schritt schreibt `var/maintenance.json` mit einer Frist von 15 Minuten, die jeder weitere Schritt erneuert.
- Jeder andere Besucher erhält HTTP 503 mit dem Hinweis, dass die Website eingerichtet wird.
- Die Seite **Beendet** beendet das Festhalten; ein verlassener Assistent hält die Website nicht vom Netz: Sobald die Frist abgelaufen
  ist, zählt die Markierung nicht mehr.

## Nach dem Assistenten

- `CreateSites` schreibt `CheckValidity=false` nach `settings/override/site.ini.append.php`; von da an wird die Website normal
  ausgeliefert, und der Assistent ist nicht mehr erreichbar.
- Auf einer installierten Website zeigt `setup/init` nur „Diese Website ist bereits installiert“ und schreibt nichts
  ([5.2](../../../installation/05-setup-assistent.md#der-assistent-auf-einer-installierten-site)).
- Richten Sie die Crontab für den Kunden ein, wenn Ihr Panel ihn das nicht tun lässt ([Cron und Shell-Zugang](../control-panels/cron-und-shell-zugang.md)).
- Gehen Sie die [Checkliste für den Start](../checklisten/checkliste-fuer-den-start-einer-kunden-website.md) durch.

## Seiten mit kickstart.ini vorfüllen

Der Assistent liest `kickstart.ini` im Wurzelverzeichnis der Installation: Wenn er einen Abschnitt für einen Schritt findet, füllt er diese
Seite vor oder überspringt sie, wenn der Abschnitt `Continue=true` sagt
([4.1](../../../installation/04-installationsweg-waehlen.md#41-ein-installer-drei-frontends)). Ein Hoster kann so die Datenbankseite
mit der Datenbank füllen, die er angelegt hat, sodass der Kunde das Datenbankpasswort nie sieht. Entfernen Sie die Datei nach der
Installation; sie enthält Passwörter im Klartext.

**Eine öffentliche Demo über den Assistenten.** Der Setup-Assistent liest `EXP_DEMO_MODE=1` aus dem PHP des Webservers (zum Beispiel `SetEnv` in `.htaccess`); seine Seite **Administrator der Seite** zeigt dann einen Hinweis **Demo-Modus** und akzeptiert das veröffentlichte Passwort, auch `publish`. [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Ein-Klick-Installer](ein-klick-installer-fuer-exponential.md)
- [Shared Hosting](../modelle/shared-hosting.md)
- [Apache mit PHP-FPM](../server/apache-mit-php-fpm.md)
- [.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
