---
title: Eine Demo-Installation einrichten
audience: press
category: press-media
order: 153
summary: Wie ein Creator eine eigene Installation von Exponential zum Filmen oder Streamen aufbaut - den Code mit Git und Composer holen, mit einem Befehl auf SQLite installieren, ausliefern, mit erfundenen Inhalten und Demokonten kameratauglich machen und zwischen den Aufnahmen zurücksetzen. Jeder Befehl stammt aus den Kapiteln zur Installation.
keywords: Demo-Installation, installieren, exp:install, SQLite, Composer, Git, Entwicklungslinie, Demoinhalte, Demokonto, zurücksetzen, kameratauglich, Creator, Video, demo installation, install, development line, demo content, demo account, reset, camera-ready, creator, video
type: help
---
# Eine Demo-Installation einrichten

Diese Seite ist für Creator, die Exponential vor der Kamera zeigen möchten. Jedes Demo-Skript in diesem Bereich läuft auf
einer **Demo-Installation, die Sie kontrollieren**: Ihrem eigenen Computer oder einem eigenen Testserver, mit erfundenen
Inhalten. Nehmen Sie nie die Live-Website eines Kunden oder Arbeitgebers auf, und nie eine gemeinsam genutzte
Installation, deren Einstellungen jemand anderem gehören ([Sicherheit bei Live-Demos](../fuersprecher/sicherheit-bei-live-demos.md)).

Die Befehle hier stammen aus der Installationsanleitung und den daneben verlinkten Funktionsseiten. Führen Sie sie
selbst aus, bevor Sie aufnehmen; tippen Sie sie nicht zum ersten Mal vor der Kamera.

## Inhalt

1. [Entscheiden, welche Version Sie zeigen](#entscheiden-welche-version-sie-zeigen)
2. [Den Code holen](#den-code-holen)
3. [Mit einem Befehl installieren](#mit-einem-befehl-installieren)
4. [Die Website ausliefern](#die-website-ausliefern)
5. [Kameratauglich machen](#kameratauglich-machen)
6. [Zwischen den Aufnahmen zurücksetzen](#zwischen-den-aufnahmen-zurücksetzen)
7. [Wenn etwas fehlschlägt](#wenn-etwas-fehlschlägt)
8. [Eine Demo, an der sich andere anmelden](#eine-demo-an-der-sich-andere-anmelden)

## Entscheiden, welche Version Sie zeigen

| Sie möchten zeigen | Was Sie brauchen | Sagen Sie vor der Kamera |
|---|---|---|
| Das neueste Release | Den Tag `v6.0.14` (7. Juni 2026) | „Exponential 6.0.14“ |
| Was diese Hilfe beschreibt: das Design admin4, `exp:install`, Velocity, Exponential Intelligence in der Distribution, den aktuellen Layout-Editor | Die Entwicklungslinie, Zweig `main` | „Die Entwicklungslinie 6.0.15, kein Release, Stand <Datum>“ |

Die meisten Bildschirme in der Hilfe gehören zur Produktlinie 6.0.15, deren Changelog mit „Not released yet“ beginnt
([Changelog von 6.0.15](../../../changelogs/6.0/6.0.15.md)). `exp:install`, Velocity und das Design admin4 sind dort als in
dieser Linie hinzugefügt aufgeführt. Nennen Sie für 6.0.15 nie ein Erscheinungsdatum.

## Den Code holen

Sie brauchen Git, Composer 2 und PHP wie in den Anforderungen aufgeführt
([Anforderungen](../../installation/02-voraussetzungen.md), [Mit Git entwickeln](../mit-git-entwickeln.md#was-sie-brauchen)):

```bash
git clone https://github.com/se7enxweb/exponential.git exponential
cd exponential
git switch -c my-demo            # your own branch on main; for the release: git switch -c my-demo v6.0.14
composer install
```

`composer install` ist nötig, bevor Exponential laufen kann (`README.md`). Es holt die Erweiterungen und Bibliotheken.

## Mit einem Befehl installieren

Auf der Entwicklungslinie installiert ein Befehl das Multisite-Paket auf SQLite in `eng-US`, ohne Datenbankserver
([Exponential mit einem Befehl installieren](../../funktionen/6.0/installation-mit-einem-befehl.md)):

```bash
php bin/php/console exp:install --dry-run    # checks the configuration, installs nothing
php bin/php/console exp:install --apply              # installs
```

- Viele Installationen legen die Abkürzung `ln -s bin/php/console console` an, danach ist `./console exp:install --apply`
  derselbe Befehl; die Abkürzung wird nicht mitgeliefert ([Die Installation über die Konsole](../../installation/07-installation-ueber-die-konsole.md)).
- Ohne Optionen ist der Administrator `admin` mit einem erzeugten Passwort aus 24 Zeichen, das in der Zusammenfassung
  ausgegeben und in `var/log/initial-admin-password` geschrieben wird. **Nehmen Sie diese Zusammenfassung nicht auf**:
  Sie zeigt das Passwort.
- Der Befehl verweigert die Ausführung über einer bestehenden Installation, wenn Sie nicht `--force` hinzufügen.
- Unter 6.0.14 verwenden Sie stattdessen den Setup-Assistenten oder den Kickstarter
  ([Eine Installationsmethode wählen](../../installation/04-installationsweg-waehlen.md)).

## Die Website ausliefern

`exp:install` startet keinen Webserver. Liefern Sie das Wurzelverzeichnis der Installation mit Ihrem üblichen Webserver
aus ([Die Website ausliefern](../../installation/08-die-website-ausliefern.md)) oder, auf der Entwicklungslinie, mit Velocity:

```bash
php bin/php/console exp:velocity start
php bin/php/console exp:velocity status    # lists the addresses to open
```

Das Engine-Paket für den mitgelieferten Server wird separat installiert; das
[Demo-Skript: Velocity](demo-skript-velocity.md) behandelt es.

## Kameratauglich machen

```text
[ ] Signed in once as admin; password changed; var/log/initial-admin-password deleted
[ ] A demo editor account with an invented name (for example "Demo Editor") and the Editor role
[ ] A second content language added (Setup > Languages) so items show a dashed badge to translate
[ ] One folder with a few items of invented text and your own or freely licensed images
[ ] At least one layout in the Layouts tab (Layouts > New layout, any type, a name)
[ ] The Interface box in the right column lists German, Hebrew and Arabic
[ ] Optional: Exponential Intelligence switched on with a key you can revoke (see its demo script)
[ ] Browser: a separate profile, zoom readable on a phone, notifications off, bookmarks bar hidden
[ ] Nothing on screen with real names, e-mail addresses, keys, passwords or server addresses
```

In der deutschen Administration heißen die Stellen **Setup** > **Sprachen**, der Reiter **Layouts** mit **Neues
Layout** und der Kasten **Oberfläche**. Dieselbe Liste für Vertriebsdemos steht in
[Demo-Skripte, Vor jeder Demo](../geschaeft/demo-drehbuecher.md#vor-jeder-demo-die-checkliste-zur-vorbereitung). Regeln zum Passwort
bei der ersten Anmeldung: [Das Standardpasswort des Administrators](../standard-administratorpasswort.md).

## Zwischen den Aufnahmen zurücksetzen

| Übrig geblieben | So räumen Sie auf |
|---|---|
| Entwürfe aus der letzten Aufnahme | **Dashboard** > **Meine Entwürfe**, verwerfen Sie sie |
| Ein Layout-Entwurf | Öffnen Sie das Layout, **Verwerfen**, bestätigen |
| Eine Testübersetzung | Entfernen Sie sie vom Element ([Sprachen](../sprachen.md#eine-übersetzung-entfernen)) |
| Oberfläche auf Arabisch oder Hebräisch | **Oberfläche**, Englisch, **Anwenden** |
| Vom Assistenten geschriebene Entwürfe | Verwerfen Sie sie im Reiter **Intelligence** |
| Alles andere | Neu installieren mit `--force`, nur auf dem Demorechner |

## Wenn etwas fehlschlägt

| Symptom | Was Sie tun |
|---|---|
| „This directory already holds an installation ...“ | Fügen Sie `--force` nur auf einer Demo hinzu, die Sie löschen dürfen |
| Die Website zeigt nach einem fehlgeschlagenen Lauf die Wartungsseite | Beheben Sie die Ursache und führen Sie den Befehl erneut aus, oder `php bin/php/maintenance.php off` |
| Rechtefehler nach einer Installation als root | Übergeben Sie die Dateien an den Benutzer des Webservers |
| Die Zusammenfassung ist nicht erschienen | Lesen Sie `var/log/setup.log` |

Vollständige Tabelle: [Die Installation über die Konsole, Was schiefgehen kann](../../installation/07-installation-ueber-die-konsole.md#711-was-schiefgehen-kann).

## Eine Demo, an der sich andere anmelden

Eine Aufnahme auf Ihrem eigenen Rechner braucht nichts weiter. Eine Demo im Internet, deren Administratoranmeldung Sie
veröffentlichen (damit Zuschauer sie selbst ausprobieren können), ist eine **öffentliche Demo**: Betreiben Sie sie mit
der Umgebungsvariablen `EXP_DEMO_MODE=1`.

- Installieren Sie mit der Variablen, damit `exp:install` das Passwort behält, das Sie veröffentlichen, statt eines zu
  erzeugen (beliebige Länge, auch `publish`; ein leeres wird abgelehnt):

  ```bash
  EXP_DEMO_MODE=1 php bin/php/console exp:install --apply --password='<the password you publish>'
  ```

- Behalten Sie die Variable danach für das PHP, die Cronjobs und die Konsole der Website. Solange sie gesetzt ist, kann
  niemand das Administratorpasswort ändern oder zurücksetzen, sodass sich der nächste Zuschauer noch anmelden kann, und
  die Administration zeigt ein Banner **Demo-Modus**.
- Bauen Sie die Demo regelmäßig neu auf: Der Demomodus schützt nur das Administratorpasswort, nicht die Inhalte.
- Setzen Sie die Variable nie für eine echte Website, und verwenden Sie das veröffentlichte Passwort nirgendwo sonst.

Wie Sie sie für jede Art von Server setzen: [Demomodus für Hoster](../hosting/sicherheit/demomodus-fuer-hoster.md).

## Verwandte Seiten

- [Demomodus](../demomodus.md)

- [Geheimnisse vom Bildschirm fernhalten](geheimnisse-vom-bildschirm-fernhalten.md)
- [Demo-Skript: Installation von null an](demo-skript-installation-von-null.md)
- [Demos als Fürsprecher geben](../fuersprecher/demos-zeigen.md)
- [Erste Schritte](../erste-schritte.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
