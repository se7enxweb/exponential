---
title: Eine Installationsmethode wählen, für eine Person
audience: individual, website-creator
category: personal-sites
order: 1631
summary: Welche der drei Installationsmethoden von Exponential zu einer Privatperson passt - der Einrichtungsassistent für Shared Hosting ohne Shell, exp:install für einen schnellen Start auf dem eigenen Computer oder einem kleinen Server, der Kickstarter für wiederholbare Installationen - mit den Optionen, die für eine persönliche Website zählen, und den Fehlern, die Sie vermeiden sollten.
keywords: Installation, Installationsmethode, Einrichtungsassistent, exp:install, Installation über die Konsole, Kickstarter, kickstart.ini, SQLite, Probelauf, Shared Hosting, FTP, keine Shell, erste Installation, Privatperson, install, install method, setup wizard, console install, dry run, no shell
type: help
---
# Eine Installationsmethode wählen, für eine Person

Diese Seite ist für Privatpersonen, die Exponential gleich installieren. Es gibt drei Methoden; alle drei führen
dieselben Installationsschritte aus und kommen zum selben Ergebnis. Sie unterscheiden sich darin, wie sie ihre Fragen
stellen ([Eine Installationsmethode wählen](../../ersteller/website-ersteller/eine-installationsmethode-waehlen.md),
[Kapitel 4 des Installationsbuchs](../../../installation/04-installationsweg-waehlen.md)).

## Inhalt

1. [Die drei Methoden](#die-drei-methoden)
2. [Welche für Sie](#welche-für-sie)
3. [Der Schnellstart auf dem eigenen Computer](#der-schnellstart-auf-dem-eigenen-computer)
4. [Der Einrichtungsassistent auf Shared Hosting](#der-einrichtungsassistent-auf-shared-hosting)
5. [Optionen, die für eine echte Website zählen](#optionen-die-für-eine-echte-website-zählen)
6. [Fehler, die Sie vermeiden sollten](#fehler-die-sie-vermeiden-sollten)

## Die drei Methoden

| Methode | Sie beantworten | Braucht | Am besten für |
|---|---|---|---|
| **Einrichtungsassistent** | Formulare im Browser, eine Seite pro Schritt | Einen Webserver während der Installation; einen Browser und FTP | Eine erste Installation; Hoster ohne Shell-Zugang |
| **Installation über die Konsole** (`exp:install`) | Kommandozeilenoptionen, jede mit einem Standardwert | Eine Shell | Die schnellste Installation; Ihren eigenen Computer; einen kleinen Server |
| **Kickstarter** (`exp:kickstarter`) | Eine geprüfte Datei `kickstart.ini` | Eine Shell | Das Wiederholen derselben Installation (eine Testkopie und die echte Website) |

## Welche für Sie

| Ihre Lage | Wählen Sie |
|---|---|
| Sie möchten Exponential heute Abend auf Ihrem Laptop laufen sehen | `exp:install` mit seinen Standardwerten |
| Ihr Hoster bietet nur FTP und ein Kontrollzentrum | Den Einrichtungsassistenten |
| Sie haben einen kleinen virtuellen Server mit Shell | `exp:install`, mit den Optionen unten |
| Sie bauen die Website beim Lernen mehrmals neu auf | Den Kickstarter, damit jeder Neuaufbau gleich ist |

## Der Schnellstart auf dem eigenen Computer

Mit installiertem PHP und Composer und geholtem Code ([Den Code beschaffen](../../../installation/03-den-code-beschaffen.md)):

```bash
php bin/php/console exp:install --dry-run
php bin/php/console exp:install --apply
```

Die Standardwerte sind SQLite und das Standard-Seitenpaket `sevenx_multisite`, mit Demo-Inhalten zum Erkunden
([Installation über die Konsole](../../../installation/07-installation-ueber-die-konsole.md)). Liefern Sie es dann aus
([Die Website ausliefern](../../../installation/08-die-website-ausliefern.md)) und melden Sie sich an
([Standard-Administratorpasswort](../../standard-administratorpasswort.md)).

## Der Einrichtungsassistent auf Shared Hosting

1. Laden Sie den Code einschließlich `vendor/` per FTP hoch. Ohne Shell können Sie Composer beim Hoster nicht
   ausführen; bereiten Sie `vendor/` also auf Ihrem eigenen Computer vor und laden Sie es mit dem Rest hoch
   ([Shared Hosting, Installation ohne Shell](../../hosting/modelle/shared-hosting.md#installation-ohne-shell)).
2. Öffnen Sie die Adresse der Website im Browser; der Assistent startet.
3. Folgen Sie seinen Seiten: Systemprüfungen, Datenbank (SQLite braucht keine Vorbereitung), Seitenpaket, Sprachen,
   Angaben zur Website.
4. Melden Sie sich am Ende an und ändern Sie das Administratorpasswort
   ([Das Administratorpasswort ändern](../../administratorpasswort-aendern.md)).

Seit 6.0.15 arbeitet der Assistent mit einer einzigen Datenbank, die Ihnen gehört, und braucht keine Rechte, die ein
Shared-Hosting-Anbieter nicht vergibt
([Eine saubere Installation, die zu Shared Hosting passt](../../../funktionen/6.0/standardwerte-einer-sauberen-installation.md)). Das
vollständige Kapitel: [Der Einrichtungsassistent](../../../installation/05-setup-assistent.md).

## Optionen, die für eine echte Website zählen

Für `exp:install` auf einem Server setzen Sie mindestens ([7.3 Optionen](../../../installation/07-installation-ueber-die-konsole.md#73-optionen)):

| Option | Warum |
|---|---|
| `--package=sevenx_multisite_clean` | Keine Demo-Inhalte in der echten Website |
| `--url=https://www.example.com` | Links, E-Mails und Feeds werden daraus gebildet |
| `--access=host` mit `--host`, `--admin-host` | Getrennte Adressen für die Website und die Administration; am besten mit HTTPS |
| `--title` | Der Name der Website |
| `--email` | Eine echte Adresse; sie wird zur E-Mail-Adresse des Administrators |
| `--language` | Die Hauptsprache Ihrer Website |

## Fehler, die Sie vermeiden sollten

- **Eine Website mit echten Inhalten neu installieren.** Eine Installation ersetzt die Datenbank. Sichern Sie zuerst
  und richten Sie eine Installation nie auf Ihre Live-Datenbank ([Sicherungen für eine Person](../im-betrieb/sicherungen-fuer-eine-person.md)).
- **Das Standardpasswort des Administrators behalten.** Ändern Sie es in der ersten Minute.
- **Die echte Website auf dem vollen Paket mit Demo-Inhalten bauen.** Demo-Inhalte später zu löschen ist mühsam;
  verwenden Sie für die echte Website das `_clean`-Paket.
- **Webserver und Installation auf einem Server als verschiedene Benutzer ausführen**, sodass Dateien später nicht
  beschreibbar sind ([Beschreibbare Verzeichnisse und Berechtigungen](../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).

## Verwandte Seiten

- [Einrichtung für Privatpersonen](README.md)
- [Ein Seitenpaket und ein Theme wählen](ein-seitenpaket-und-ein-theme-waehlen.md)
- [Eine Installationsmethode wählen (Website-Ersteller)](../../ersteller/website-ersteller/eine-installationsmethode-waehlen.md)
- [Nach der Installation](../../../installation/10-nach-der-installation.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
