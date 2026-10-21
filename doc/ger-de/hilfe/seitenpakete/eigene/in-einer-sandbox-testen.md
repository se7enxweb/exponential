---
title: In einer Sandbox testen
audience: developer, partner, hosting
category: install-upgrade
order: 95
summary: Ein Seitenpaket dort testen, wo nichts verloren gehen kann - ein eigener Checkout mit eigener SQLite-Datenbank - in vier Runden - die Archive prüfen, ein Probelauf, eine vollständige Installation, ein Blick auf das Ergebnis - und danach aufräumen.
keywords: Sandbox, Testinstallation, Probelauf, exp:install, --dry-run, --print, kickstarter run --dry-run, SQLite, ezpm validate, ezpm check, setup.log, kickstart.log, Test eines Seitenpakets, sandbox, test install, dry run, site package test
type: help
---
# In einer Sandbox testen

Diese Seite ist für Entwickler, die kurz davor sind, ein Seitenpaket zu veröffentlichen oder per Pull Request zu senden. Ein Seitenpaket
schreibt bei der Installation eine Datenbank und einen ganzen Satz von Einstellungen; der einzige sichere Ort, es auszuprobieren, ist eine
Installation, die genau dafür existiert. Diese Seite zeigt, wie Sie eine anlegen, was Sie testen und wie Sie sie wieder entfernen.

7x und die Exponential Foundation ermutigen Sie ausdrücklich, Ihr getestetes Seitenpaket per Pull Request an
se7enxweb/exponential.packages.exponential.earth einzureichen: siehe [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md).
Prüfende werden fragen, wie Sie es getestet haben.

## Inhalt

1. [Die Sandbox anlegen](#die-sandbox-anlegen)
2. [Runde 1: die Archive](#runde-1-die-archive)
3. [Runde 2: ein Probelauf](#runde-2-ein-probelauf)
4. [Runde 3: eine vollständige Installation](#runde-3-eine-vollständige-installation)
5. [Runde 4: das Ergebnis ansehen](#runde-4-das-ergebnis-ansehen)
6. [Aufräumen](#aufräumen)

## Die Sandbox anlegen

Nutzen Sie einen eigenen Checkout von Exponential 6.0, nie das Verzeichnis einer Website, auf die es ankommt:

```bash
git clone https://github.com/se7enxweb/exponential.git sandbox-acme
cd sandbox-acme
composer install
```

Die Wege, an den Code zu kommen, und was `composer install` tut, stehen in [3. Den Code holen](../../../installation/03-den-code-beschaffen.md).

Richten Sie sie auf den Server, der Ihre Testpakete enthält ([Eine Website auf Ihren Paketserver richten](eine-website-auf-ihren-paketserver-richten.md)):

```ini
# settings/override/package.ini.append.php
[RepositorySettings]
RemotePackagesIndexURL=https://packages.example.com/exponential/6.0/6.0.15
```

SQLite ist die Standarddatenbank von `exp:install` und braucht keinen Server: Die Datenbank ist eine Datei in `var/storage/sqlite3/`, was die
Sandbox leicht wegwerfbar macht.

> **Warnung**
> `exp:install --db-action=remove` (der Standard) leert die Datenbank zuerst, und `--force` ersetzt die Datenbank und die Einstellungen einer
> vorhandenen Installation. In einer Sandbox wollen Sie genau das; anderswo nicht.

## Runde 1: die Archive

Auf Ihrer Build-Maschine, bevor irgendetwas veröffentlicht wird:

```bash
tar -tzf acme_site.ezpkg | head                  # package.xml at the top
php ezpm.php validate /path/to/acme_site.ezpkg   # in the sandbox: the kernel's checks, nothing imported
```

Siehe [Mit build.sh neu bauen](mit-build-sh-neu-bauen.md#das-archiv-prüfen) und
[Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md).

## Runde 2: ein Probelauf

```bash
php bin/php/console exp:install --package=acme_site --print
php bin/php/console exp:install --package=acme_site --dry-run
```

`--print` zeigt die Konfiguration und hält an. `--dry-run` verbindet sich mit der Datenbank und holt Ihr Seitenpaket und seine Anforderungen
nach `var/storage/packages/dryrun/`, das wieder entfernt wird; es schreibt weder Datenbank noch Einstellungen
([7.6](../../../installation/07-installation-ueber-die-konsole.md#76-probelauf-und-ausgabe)). Es belegt, dass sich Ihr Index lesen lässt, jedes Archiv herunterlädt
und importiert und jedes `<require>` gefunden wird. Mit einer `kickstart.ini`: `php bin/php/console exp:kickstarter run --dry-run` tut
dasselbe ([6.7](../../../installation/06-kickstarter.md#67-probeläufe)).

## Runde 3: eine vollständige Installation

```bash
php bin/php/console exp:install --package=acme_site --url=http://localhost:8080 --random-password
```

Führen Sie es als der Benutzer aus, als der das PHP des Webservers läuft, damit die angelegten Dateien beschreibbar bleiben
([7.3](../../../installation/07-installation-ueber-die-konsole.md#73-optionen)). Lesen Sie dann die Logs:

| Log | Wonach Sie suchen |
|---|---|
| `var/log/setup.log` | jeden Schritt, Wiederholungen beim Herunterladen, Warnungen Ihres Installerskripts |
| `var/log/kickstart.log` | den Lauf des Kickstarters (mit `exp:kickstarter run`) |
| die am Ende ausgegebene Zusammenfassung | Adresse der Website, Anmeldung der Administration, Siteaccesses |

Ein Schritt der Nachinstallation, der die Kette abbricht, wird gemeldet: `eZSiteInstaller::abortedStep()` behält seine Nummer und Funktion
([Das Installerskript ändern](das-installerskript-aendern.md#schritte-der-nachinstallation)).

## Runde 4: das Ergebnis ansehen

- [ ] Die Startseite und einige Inhaltsseiten werden dargestellt, in jedem Siteaccess und jeder Sprache, die das Paket anlegt.
- [ ] Die Administration öffnet sich; **Setup > Pakete** führt Ihre Pakete als installiert oder importiert auf, ohne Warnungen.
- [ ] Die Inhaltsklassen sind die, die Sie erwarten (die Klassenliste der Administration), mit ihren Namen in jeder Sprache.
- [ ] Die Rollen und Richtlinien, die Ihr Installer anlegt, existieren, und ein Testbenutzer jeder Rolle kann tun, was er soll.
- [ ] Seiten, die Layouts nutzen, zeigen ihre Blöcke.
- [ ] Installieren Sie ein zweites Mal mit der Variante `_clean`, wenn Sie eine ausliefern.
- [ ] Wiederholen Sie die vollständige Installation noch einmal von vorn: Ein Installer, der nur beim ersten Mal funktioniert, scheitert für
      die nächste Person.

## Aufräumen

Eine zurückgelassene Sandbox belegt Speicherplatz und betreibt vielleicht einen Server:

1. Stoppen Sie jeden Server, den Sie dafür gestartet haben (`exp:velocity stop` oder den Prozess `php -S`).
2. Entfernen Sie das Verzeichnis der Sandbox, sobald Sie behalten haben, was Sie brauchen (Logs, Screenshots).
3. Entfernen Sie die Testdatenbank, wenn Sie statt SQLite einen Datenbankserver genutzt haben.

Halten Sie kurz fest, was Sie auf welchem Release getestet haben: Es gehört in die Beschreibung des Pull Requests
([Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md#die-installation-zuerst-in-einer-sandbox-testen)).

## Verwandte Seiten

- [Ein eigenes Seitenpaket erstellen](README.md)
- [Ihr Seitenpaket beitragen](../ihr-seitenpaket-beitragen.md)
- [Ein Paket vor der Veröffentlichung prüfen](../anforderungen/ein-paket-vor-der-veroeffentlichung-pruefen.md)
- [Wie der Kickstarter und die Konsole Pakete nutzen](../grundlagen/wie-der-kickstarter-und-die-konsole-pakete-nutzen.md)
- [Ihr Paket auf einer Testwebsite installieren](../../designer/erweiterungen/ihr-paket-auf-einer-testseite-installieren.md)
- [7. Die Installation über die Konsole](../../../installation/07-installation-ueber-die-konsole.md)
- [Seitenpakete: hier beginnen](../README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
