---
title: Wartungsmodus und geplante Ausfallzeiten
audience: government, hosting, admin, project-manager
category: public-sector
order: 100
summary: Für öffentliche Stellen, die geplante Ausfallzeiten einer Website ankündigen und steuern müssen. Wie der Wartungsmodus von Exponential Besuchern mit einer 503-Seite und einem erwarteten Ende antwortet, wer weiterhin hineinkommt, wie Überwachung und Suchmaschinen ihn sehen und wie Sie Wartungsfenster in Ihren Änderungskalender und Ihre Informationspflicht einfügen.
keywords: Wartungsmodus, geplante Ausfallzeit, Wartungsfenster, 503, Retry-After, maintenance.php, exp:maintenance, Setup > Wartung, allow-ip, allow-admin, Änderungskalender, öffentliche Bekanntmachung, Barrierefreiheit des Hinweises, maintenance mode, planned downtime, maintenance window, Setup > Maintenance, change calendar, public notice, accessibility of notice
type: help
---
# Wartungsmodus und geplante Ausfallzeiten

Diese Seite richtet sich an Betriebsteams und Projektleitungen in öffentlichen Stellen, die Ausfallzeiten für
Aktualisierungen, Importe oder Wiederherstellungen planen und die Öffentlichkeit darüber informieren müssen. Sie
beantwortet, wie der Wartungsmodus funktioniert, was Besucher, Überwachungssysteme und Suchmaschinen sehen, wer
weiterhin hineinkommt und wie ein Wartungsfenster in Ihren Änderungskalender passt. Die vollständige Anleitung ist
[Wartungsmodus: die Website offline nehmen und zurückholen](../../../../anleitungen/wartungsmodus.md); die
Funktionsseite ist [Wartungsmodus](../../../../funktionen/6.0/wartungsmodus.md).

## Inhalt

1. [Was Besucher und Maschinen sehen](#was-besucher-und-maschinen-sehen)
2. [Ein- und ausschalten](#ein--und-ausschalten)
3. [Wer weiterhin hineinkommt](#wer-weiterhin-hineinkommt)
4. [Ein Fenster in einer öffentlichen Stelle planen](#ein-fenster-in-einer-öffentlichen-stelle-planen)
5. [Was der Wartungsmodus nicht tut](#was-der-wartungsmodus-nicht-tut)
6. [Checkliste](#checkliste)

## Was Besucher und Maschinen sehen

Solange die Wartung eingeschaltet ist, wird jede Anfrage an die Front-Controller mit der Wartungsseite beantwortet,
bevor die Einstellungen oder die Datenbank angefasst werden:

- HTTP-Status `503 Service Unavailable` mit einem `Retry-After`-Header (die verbleibenden Sekunden oder 120, wenn kein
  Ende gesetzt ist);
- `Cache-Control: no-store` und `noindex`, damit kein Cache und keine Suchmaschine die Seite behält;
- Bilder, Stylesheets und Skripte werden weiterhin ausgeliefert, damit die Seite ihr Aussehen behält.

Die Seite ist die `errors/maintenance.html` des aktiven Themes, wenn eine aktive Extension eine solche hat, sonst
`share/maintenance.html`. Prüfen Sie, dass die Seite Ihres Themes in einfacher Sprache gehalten ist und angibt, wann
der Dienst zurückkehrt; die Vorschau der Seite in der Administration zeigt genau, was Besucher erhalten.

Externe Verfügbarkeitsmonitore sehen einen 503 mit `Retry-After`. Konfigurieren Sie sie so, dass sie geplante Wartung
von einem Ausfall unterscheiden ([Zustandsprüfungen und Verfügbarkeit](../../../hosting/betrieb/zustandspruefungen-und-verfuegbarkeit.md)).

## Ein- und ausschalten

In der Administration: **Setup > Wartung** (`/setup/maintenance`) mit einer Nachricht, einer erwarteten Dauer, der
Angabe, wer weiterhin hineinkommt, einer Vorschau und einer erforderlichen Bestätigung vor **Website offline nehmen**.
Dafür ist das Recht `setup/administrate` nötig. Auf der Kommandozeile:

```bash
php bin/php/maintenance.php on --message="Back at 06:00" --until=2h --allow-admin --allow-root-user
php bin/php/maintenance.php status --allow-root-user
php bin/php/maintenance.php off --allow-root-user
```

`./console exp:maintenance on|off|status` ist dasselbe über die Konsole. Die Wartung endet nie von selbst: Wenn das
erwartete Ende verstrichen ist, zeigt die Seite eine Warnung, und jemand muss sie ausschalten. Jede Änderung wird im
Prüfprotokoll als `system.maintenance.change` festgehalten, und **Setup > Wartung** listet die letzten Änderungen auf.

## Wer weiterhin hineinkommt

| Wer | Wie |
|---|---|
| Die Administration unter `/admin` | von der Seite aus immer; von der Kommandozeile nur mit `--allow-admin` |
| Ihre eigene Adresse | das Häkchen **Meine eigene Adresse sieht die Website weiterhin** oder `--allow-ip` |
| Testerinnen und Tester | **Weitere Adressen, die die Website weiterhin sehen** oder `--allow-ip=a,b` |
| Angemeldete Benutzer auf der öffentlichen Website | werden nicht durchgelassen: Über die Wartung wird entschieden, bevor die Sitzung gelesen wird |

Hinter einem Proxy ist die Adresse diejenige, die PHP sieht; lassen Sie die Adresse zu, die Ihr Webserver
protokolliert ([Anleitung zum Wartungsmodus](../../../../anleitungen/wartungsmodus.md)).

## Ein Fenster in einer öffentlichen Stelle planen

| Schritt | Was zu tun ist |
|---|---|
| Einplanen | im Änderungskalender, freigegeben vom Änderungsbeirat, außerhalb der Hauptzeiten des Dienstes |
| Ankündigen | auf der Website, in den Kanälen, die Ihre Organisation nutzt, mit Datum, Uhrzeit und erwartetem Ende |
| Abhängigkeiten prüfen | Online-Formulare, Terminbuchungen oder Newsletter, die nicht verfügbar sein werden; andere Dienste, die auf die Website verlinken |
| Vorbereiten | eine Datensicherung, eine Shell auf dem Server, der Plan für die Rücknahme ([Wartungsfenster](../aktualisierungen-und-aenderungen/wartungsfenster.md)) |
| Durchführen | einschalten, arbeiten, von einer zugelassenen Adresse prüfen, ausschalten |
| Abschließen | `curl` erhält für alle 200; Caches vor der Website geleert; das Änderungsticket aktualisiert |

Cronjobs laufen während eines Fensters weiter, weil sie in einer Shell laufen. Halten Sie die Crontab selbst an, wenn
ein Job nicht laufen darf, während Sie die Website ändern (ein Import, ein Neuaufbau des Suchindex).

## Was der Wartungsmodus nicht tut

- Er erstellt keine Datensicherung; erstellen Sie zuerst eine ([Was zu sichern ist](../datensicherung-und-wiederherstellung/was-gesichert-werden-muss.md)).
- Er hält keine Cronjobs an.
- Er ist lokal auf die `var/maintenance.json` einer Installation beschränkt: Bei mehreren Webservern schalten Sie ihn
  auf jedem Knoten ein.
- Bereitstellungen schalten ihn weder ein noch aus; öffnen Sie für eine Bereitstellung, die die Datenbank oder viele
  Dateien auf einmal ändert, zuerst ein Fenster.

## Checkliste

- [ ] Das Fenster ist freigegeben und angekündigt.
- [ ] Die Wartungsseite gibt in einfachen Worten an, wann der Dienst zurückkehrt.
- [ ] Die Überwachung behandelt einen 503 mit `Retry-After` als geplant.
- [ ] Cronjobs, die nicht laufen dürfen, sind angehalten.
- [ ] Nach dem Fenster: 200 für alle, Caches geleert, `var/maintenance.json` entfernt.

## Verwandte Seiten

- [Verfügbarkeit](README.md): Übersicht dieses Ordners
- [Wartungsmodus für Hoster](../../../hosting/betrieb/wartungsmodus.md)
- [Verfügbarkeitsziele und Service-Level](verfuegbarkeitsziele-und-service-level.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
