---
title: Bundles für Schulung und Demo
audience: bundler, trainer, sales-marketing
category: bundling
order: 350
summary: Wie Sie Exponential für Schulungsräume, Workshops, Messen und öffentliche Demos bündeln - SQLite und exp:install für Tempo, die Pakete mit Demo-Inhalten, das Zurücksetzen einer Website zwischen Gruppen, veröffentlichte Passwörter nur mit Demo-Modus, Offline-Tauglichkeit und was Sie Teilnehmern und Interessenten sagen sollten.
keywords: Schulung, Schulungsraum, Training, Workshop, Demo, öffentliche Demo, Messe, Demo-Inhalte, zurücksetzen, education, classroom, training, workshop, demo, public demo, trade show, SQLite, exp:install, demo content, reset, kickstarter, EXP_DEMO_MODE, offline, bundle
type: help
---
# Bundles für Schulung und Demo

Diese Seite richtet sich an Trainer, Schulen, Partner und Vertriebsteams, die Exponential bündeln, damit Menschen es
ausprobieren können: ein Image für den Schulungsraum, ein Workshop-Laptop, ein Messe-Kiosk, eine öffentliche
Demo-Website.

## Inhalt

1. [Was diese Bundles brauchen](#was-diese-bundles-brauchen)
2. [Eine schnelle, in sich geschlossene Installation](#eine-schnelle-in-sich-geschlossene-installation)
3. [Inhalte zum Vorführen](#inhalte-zum-vorführen)
4. [Zwischen Gruppen zurücksetzen](#zwischen-gruppen-zurücksetzen)
5. [Passwörter](#passwörter)
6. [Was Sie Teilnehmern und Interessenten sagen](#was-sie-teilnehmern-und-interessenten-sagen)

## Was diese Bundles brauchen

| Bedarf | Wie |
|---|---|
| Startet in Minuten, auf einem Laptop | SQLite, kein Datenbankserver ([SQLite-Bundles](sqlite-bundles.md)) |
| Funktioniert ohne das Netzwerk des Veranstaltungsorts | vorab importierte Pakete ([Offline-Bundles](offline-bundles.md)) |
| Etwas zum Vorführen | ein Seitenpaket mit Demo-Inhalten |
| Derselbe Zustand für jede Gruppe | ein Zurücksetzen, das neu installiert |
| Sicher, falls das Image in fremde Hände gerät | keine echten Daten, keine wiederverwendeten Passwörter |

## Eine schnelle, in sich geschlossene Installation

```bash
php bin/php/console exp:install
```

installiert "das Multisite-Paket auf **SQLite**, in `eng-US`, mit den Siteaccesses `site` und `admin`, erreichbar per
URL" ([Installation mit einem Befehl](../../funktionen/6.0/installation-mit-einem-befehl.md)). Auf einem Laptop kann Velocity es
ausliefern; die ausgelieferte Engine `php` "braucht nichts, was einer Installation fehlen könnte" und eignet sich für
die Entwicklung ([Velocity-Engines](../../bc/6.0/velocity-engines.md#kurz-gesagt)).

## Inhalte zum Vorführen

- `sevenx_multisite` installiert die 7x-Multisite mit Demo-Inhalten; `cjw_multisite` die CJW-Starter-Website mit einer
  englischen und einer deutschen Website
  ([4.3](../../installation/04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)).
- Für Lektionen, die leer beginnen, verwenden Sie `sevenx_multisite_clean`.
- Auf Exponential Platform ist die Nexus-Demo (`exponential-media`) eine vollständige Website mit Layouts
  ([Platform-Distributionen als Basis](platform-distributionen-als-basis.md)).

## Zwischen Gruppen zurücksetzen

- Mit `exp:install`: Führen Sie es erneut mit `--force` aus; die Standard-Datenbankaktion `remove` leert zuerst die
  Datenbank.
- Mit dem Kickstarter: "Dieselbe `kickstart.ini` mit `DatabaseAction=remove` und `run --force` ersetzt die Datenbank
  und die Einstellungen erneut. Verwenden Sie das, um eine Test- oder Schulungswebsite zurückzusetzen"
  ([6.10](../../installation/06-kickstarter.md#610-erneut-ausführen-und-fortsetzen)).
- Mit Images: Stellen Sie den Snapshot des Images wieder her.

Leeren Sie nach einem Zurücksetzen die Caches, wenn die Website lief
([Caches und Autoloads nach dem Bauen](caches-und-autoloads-nach-dem-bauen.md)).

## Passwörter

| Situation | Passwort |
|---|---|
| Jeder Teilnehmer erhält eine eigene Installation | vom Installer erzeugt; der Teilnehmer liest `var/log/initial-admin-password` |
| Ein gemeinsamer Schulungsraum oder eine öffentliche Demo mit veröffentlichter Anmeldung | das veröffentlichte Passwort, installiert mit `EXP_DEMO_MODE=1` ([Demo-Modus in Bundles](demomodus-in-bundles.md)) |
| Rechner des Trainers | erzeugt, vom Trainer aufbewahrt |

Verwenden Sie ein Demo-Passwort nie irgendwo im Ernstbetrieb: "Ein solches Passwort darf nie auf einer echten Website
wiederverwendet werden"
([Das Standard-Administratorpasswort](../standard-administratorpasswort.md#site-pakete-und-demo-inhalte)).

## Was Sie Teilnehmern und Interessenten sagen

- Dass die Website eine Vorführung ist, regelmäßig zurückgesetzt wird und kein Ort für private Daten ist.
- Dass Exponential freie Software unter der GPL ist und die Dokumentation frei unter der GFDL: Sie dürfen beides mit
  nach Hause nehmen ([Freie Dokumentation](../freie-dokumentation.md)).
- Wohin es danach geht: [Erste Schritte](../erste-schritte.md), die Foren <https://share.exponential.earth/forums>.

Schulungspartner haben einen eigenen Bereich: [Schulungspartner](../partner/schulungspartner.md),
[Einen Schulungskurs durchführen](../partner/eine-schulung-durchfuehren.md),
[Partner für Studierende und Bildung](../partner/partner-fuer-studierende-und-bildung.md).

## Verwandte Seiten

- [Eine Demo-Installation einrichten](../presse/eine-demo-installation-einrichten.md)
- [Eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../hosting/server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md)
- [Demo-Modus in Bundles](demomodus-in-bundles.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
