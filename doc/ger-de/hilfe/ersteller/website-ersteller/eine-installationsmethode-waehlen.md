---
title: Eine Installationsmethode wählen
audience: website-creator, admin
category: website-creation
order: 60
summary: Welche der drei Installationsmethoden (Setup-Assistent, Kickstarter, Konsoleninstallation) und welches Seitenpaket Sie für eine neue Website wählen, mit den Befehlen und den Entscheidungen, die jede Methode abfragt.
keywords: Installation, installieren, Setup-Assistent, Kickstarter, Konsoleninstallation, Seitenpaket, Demo-Inhalte, neue Website, Website-Ersteller, install, installation, setup wizard, kickstarter, exp:install, console install, site package, sevenx_multisite, clean, demo content, new site, website creator
type: help
---
# Eine Installationsmethode wählen

Diese Seite richtet sich an Website-Ersteller, die Exponential für eine neue Website installieren wollen. Sie
beantwortet, welche der drei Installationsmethoden zu Ihnen passt, welches Seitenpaket Sie wählen und welche Optionen
für eine Website wichtig sind. Das Installationshandbuch enthält alle Einzelheiten:
[Eine Installationsmethode wählen](../../../installation/04-installationsweg-waehlen.md).

## Inhalt

1. [Ein Installer, drei Oberflächen](#ein-installer-drei-oberflächen)
2. [Welche Methode für welche Situation](#welche-methode-für-welche-situation)
3. [Das Seitenpaket wählen](#das-seitenpaket-wählen)
4. [Die Optionen, die für eine Website wichtig sind](#die-optionen-die-für-eine-website-wichtig-sind)
5. [Nach der Installation](#nach-der-installation)

## Ein Installer, drei Oberflächen

Alle drei Methoden führen dieselben Installationsschritte aus und enden im selben Schritt, `CreateSites`, daher schreiben
sie dieselben Einstellungen und dieselbe Datenbank. Sie unterscheiden sich darin, wie sie ihre Fragen stellen
([4.2 Die drei Methoden auf einen Blick](../../../installation/04-installationsweg-waehlen.md#42-die-drei-methoden-im-überblick)):

| Methode | Sie beantworten | Braucht während der Installation einen Webserver | Am besten für |
|---|---|---|---|
| Setup-Assistent | Formulare im Browser, eine Seite pro Schritt | ja | eine erste Installation, zum Ausprobieren, Hoster ohne Shell-Zugang |
| Kickstarter (`exp:kickstarter`) | eine geprüfte Datei `kickstart.ini` | nein | wiederholbare Installationen, Staging-Kopien, Offline-Paketsätze |
| Konsoleninstallation (`exp:install`) | Kommandozeilenoptionen, jede mit einem Standardwert | nein | die schnellste Installation; Skripte, CI, Demos |

## Welche Methode für welche Situation

| Ihre Situation | Wählen Sie |
|---|---|
| Sie wollen Exponential in wenigen Minuten auf Ihrem eigenen Rechner laufen sehen | `exp:install` mit seinen Standardwerten (SQLite, `sevenx_multisite`) |
| Sie übergeben die Website an einen Kunden und wollen jede Entscheidung auf einer Seite erklärt haben | den Setup-Assistenten ([Kapitel 5](../../../installation/05-setup-assistent.md)) |
| Sie bauen dieselbe Website für Staging und Produktion oder für mehrere Kunden | den Kickstarter mit einer `kickstart.ini` pro Umgebung ([Kapitel 6](../../../installation/06-kickstarter.md)) |
| Ihr Hosting hat keinen Shell-Zugang | den Setup-Assistenten |
| Sie testen in CI | `exp:install --dry-run`, dann `exp:install` ([Kapitel 7](../../../installation/07-installation-ueber-die-konsole.md)) |

## Das Seitenpaket wählen

Ein Seitenpaket ist der Ausgangsbestand an Inhalten und Einstellungen: Klassen, ein Design, Demo-Inhalte, Rollen. Der
Paketindex dieser Version bietet elf an; die, die Sie am ehesten wählen
([4.3 Seitenpakete](../../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)):

| Paket | Was Sie bekommen | Wählen Sie es, wenn |
|---|---|---|
| `sevenx_multisite` | die Standard-Multisite-Installation mit Demo-Inhalten | Sie erkunden oder auf der Demo aufbauen wollen |
| `sevenx_multisite_clean` | dasselbe, ohne Demo-Inhalte | Sie eine echte Website bauen und selbst befüllen |
| `sevenx_site`, `sevenx_site_clean` | das einfachere Website Interface | eine kleine Website |
| `ezwebin_site`, `ezflow_site`, `ezdemo_site` (und `_clean`), `plain_site` | die älteren Pakete | Sie eine darauf gebaute Website weiterführen |

> **Tipp**
> Installieren Sie das vollständige Paket einmal auf Ihrem Laptop zum Lernen und das `_clean`-Paket für die echte
> Website.

## Die Optionen, die für eine Website wichtig sind

Bei `exp:install` hat jede Option einen Standardwert; für eine echte Website setzen Sie mindestens diese
([7.3 Optionen](../../../installation/07-installation-ueber-die-konsole.md#73-optionen)):

| Option | Warum sie wichtig ist |
|---|---|
| `--url=https://www.example.com` | die Adresse jedes Siteaccess, Mail-Links und Feeds werden daraus gebildet |
| `--access=url\|host\|port` | wie Besucher jeden Siteaccess erreichen; `host` ist am besten für Produktion und HTTPS ([Livegang: Hosts und HTTPS](livegang-hosts-und-https.md)) |
| `--host`, `--admin-host` | die Hostnamen der öffentlichen Website und der Administration, mit `--access=host` |
| `--language`, `--languages` | die Hauptsprache und die weiteren Sprachen ([Eine mehrsprachige Website bauen](mehrsprachige-websites.md)) |
| `--title` | der Website-Name, verwendet in Titeln und Mails |
| `--db=...` mit `--db-user` | die Datenbank; ein Datenbankbenutzer nur für Exponential ([Datenbankunterstützung](../../datenbankunterstuetzung.md)) |
| `--email` | eine echte Adresse: sie wird zu `[MailSettings] AdminEmail` |
| `--package` | das Seitenpaket, siehe oben |

Ein Beispiel für eine produktionsartige Installation mit Hostnamen, ausgeführt als Benutzer des Webservers:

```bash
EXP_INSTALL_DB_PASSWORD='<database password>' \
sudo -u www-data php bin/php/console exp:install \
  --db=pgsql --db-user=exponential --db-name=exponential \
  --package=sevenx_multisite_clean --language=eng-US --languages=ger-DE \
  --title="Example" --url=https://www.example.com \
  --access=host --host=www.example.com --admin-host=admin.example.com \
  --email=webmaster@example.com --dry-run
```

Entfernen Sie `--dry-run`, wenn die Prüfung erfolgreich ist. Das Passwort steht in `EXP_INSTALL_DB_PASSWORD` statt in
einer Option, damit es weder in der Prozessliste noch im Shell-Verlauf erscheint.

> **Warnung**
> `--db-action=remove` ist der Standard und leert zuerst die Datenbank. Richten Sie eine Installation niemals auf eine
> Datenbank, die Inhalte enthält, die Sie behalten wollen.

## Nach der Installation

1. Notieren Sie das Administratorpasswort, das die Installation einmal anzeigt, melden Sie sich an und ändern Sie es
   ([Das Standard-Administratorpasswort](../../standard-administratorpasswort.md)).
2. Liefern Sie die Website aus ([Hosting für eine Website](hosting-fuer-eine-website.md)).
3. Arbeiten Sie die Checkliste für die erste Woche in [Nach der Installation](../../../installation/10-nach-der-installation.md)
   ab.

## Verwandte Seiten

- [Ihre erste Website Schritt für Schritt](ihre-erste-website-schritt-fuer-schritt.md)
- [Hosting für eine Website](hosting-fuer-eine-website.md)
- [Seitenpakete und die mitgelieferten Themes](seitenpakete-und-mitgelieferte-themes.md)
- [Installationshandbuch](../../../installation/README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
