---
title: Die Systemkarte für Hoster
audience: hosting, admin
category: hosting
order: 135
summary: Wie Systemadministratoren und Support-Mitarbeitende ./console exp:expfetch auf einem gehosteten Server nutzen - die Hinweise zum Zustand bei der Anmeldung, --watch für Last, Speicher und Velocity-Worker und exp:info --json für Überwachung, Inventare und den Vergleich von Servern. Nur lesend, schnell und frei von Geheimnissen.
keywords: exp:expfetch, exp:info, Systemkarte, Zustandshinweise, --watch, --json, Überwachung, Inventar, Velocity-Worker, Last, Speicher, Festplatte, PHP-Supportende, OPcache, Hosting, Support, system card, health hints, monitoring, inventory
type: help
---
# Die Systemkarte für Hoster

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende, die Exponential-Server betreuen. Die Systemkarte
(`./console exp:expfetch`) zeigt auf einem Bildschirm, was auf einer Maschine läuft und was Aufmerksamkeit braucht. Die
vollständige Referenz ist [Die Systemkarte](../../../funktionen/6.0/systemkarte.md).

## Bei der Anmeldung: die Hinweise lesen

```bash
cd /var/www/example.org && ./console exp:expfetch --allow-root-user
```

Die Karte endet mit einem Block **Hints**, der schwerwiegendste zuerst. Die Hinweise, auf die ein Hoster am häufigsten
reagiert:

| Hinweis | Was zu tun ist |
|---|---|
| eine Festplatte ist zu 90 % voll (Fehler ab 97 %) | alte Caches und Logs aufräumen (`./console exp:cache`, `./console exp:logs`) oder Platz schaffen, bevor Uploads fehlschlagen |
| Speicher zu 90 % belegt, Last über dem Doppelten der Kerne | vor einem Deployment oder einer Neuindizierung nachsehen, was läuft (`top`); die Zahl der Velocity-Worker senken |
| PHP verliert bald die Sicherheitsunterstützung oder hat sie verloren | das PHP-Upgrade planen ([Betriebssystem- und PHP-Unterstützung](../../betriebssystemunterstuetzung.md)) |
| OPcache aus, empfohlene Erweiterung fehlt, `memory_limit` zu niedrig | die php.ini korrigieren (die Karte zeigt das PHP der Kommandozeile; prüfen Sie das PHP des Webservers unter **Setup > Systeminformationen**) |
| die Datenbank antwortet nicht | den Datenbankserver und `site.ini [DatabaseSettings]` prüfen |
| Demomodus an, Debug-Ausgabe für alle | richtig für einen Demo-Server, falsch für eine Kunden-Website |
| seit zwei Tagen kein Cronjob | die Crontab und die Cronjob-Logs prüfen ([Cronjobs](cronjobs.md)) |
| Composer-Pakete widersprechen sich | vor dem nächsten Update **Setup > Updates** ansehen ([Automatische Updates mit ezupdate](automatische-updates-mit-ezupdate.md)) |

Fügen Sie `--allow-root-user` hinzu, wenn Sie als root arbeiten, wie bei jedem Konsolenbefehl.

## Live: --watch

```bash
./console exp:expfetch --watch=2 --section=system,engine
```

Die Karte zeichnet sich wie `top` alle zwei Sekunden an derselben Stelle neu: Last, Speicher, Festplatte und die
laufenden Velocity-Worker werden aktualisiert, der Rest bleibt stehen. Strg-C beendet sie. Nützlich, während ein
Deployment, ein Import oder ein Lasttest läuft.

## Überwachung und Inventare: --json

```bash
./console exp:info --json --no-logo > /var/backups/inventory/$(hostname)-$(date +%F).json
./console exp:expfetch --json | jq '[.hints[] | select(.severity=="error")] | length'
```

Das JSON enthält jede Angabe mit einem stabilen Schlüssel und einem Schweregrad, die Hinweise und die Dauer jedes
Anbieters. Eine Überwachungsprüfung kann bei der Zahl der Hinweise vom Typ `error` Alarm schlagen; ein nächtliches
`exp:info --json` liefert je Server ein Inventar, das sich zwischen zwei Tagen oder zwei Maschinen gut vergleichen
lässt. Ein langsamer Teil (eine Datenbank, die hängt) zeigt `n/a (timeout)`, statt die Prüfung aufzuhalten: Jeder
Anbieter hat ein Zeitbudget.

## Sicher zum Einfügen

Die Karte gibt nie ein Passwort, einen Schlüssel, ein Token, einen Datenbankbenutzer oder die vollständige
Datenbankadresse aus, auch nicht die Werte der envvars von Velocity. Sie können sie unverändert an ein Ticket anhängen.
Siehe [No secrets, ever](../../../funktionen/6.0/systemkarte.md#niemals-geheimnisse).

## Verwandte Seiten

- [Die Systemkarte](../../../funktionen/6.0/systemkarte.md)
- [Überblick über die Überwachung](ueberblick-ueber-die-ueberwachung.md)
- [Zustandsprüfungen und Verfügbarkeit](zustandspruefungen-und-verfuegbarkeit.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
