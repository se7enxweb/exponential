---
title: Datenbankeinstellungen lesen und ändern
audience: dba
category: database-administration
order: 7
summary: Wie Sie die Datenbankeinstellungen lesen, die eine Exponential-Website wirklich verwendet, die Datei finden, aus der jeder Wert kommt, sie sicher in den Override-Dateien mit exp:ini ändern und jeden Prozess (PHP-FPM, Velocity-Worker, Cronjobs) die Änderung übernehmen lassen.
keywords: exp:ini, exp:ini get, exp:ini where, exp:ini set, exp:ini list, Override, settings/override/site.ini.append.php, Siteaccess, INI-Cache, clear-tag=ini, Velocity-Neustart, maskierte Geheimnisse, Datenbankpasswort ändern, Datenbankhost ändern, override, INI cache, Velocity restart, secrets masked, change database password, change database host
type: help
---
# Datenbankeinstellungen lesen und ändern

Diese Seite richtet sich an Datenbankadministratoren, die die Verbindungseinstellungen einer laufenden Website lesen oder
ändern müssen: ein neues Passwort, ein neuer Host nach einem Umzug, eine andere Engine. Sie beantwortet: wo die Werte
liegen, wie Sie den wirksamen Wert und die Datei sehen, aus der er kommt, wie Sie eine Änderung schreiben und was Sie neu
starten, damit jeder Prozess sie verwendet. Quellen: [9.1](../../installation/09-datenbanken.md#91-wie-exponential-einen-datenbanktreiber-auswählt)
und [exp:ini](../../bc/6.0/console-exp-ini.md).

## Inhalt

1. [Wo die Werte liegen](#wo-die-werte-liegen)
2. [Den wirksamen Wert lesen](#den-wirksamen-wert-lesen)
3. [Die Datei finden, aus der ein Wert kommt](#die-datei-finden-aus-der-ein-wert-kommt)
4. [Einen Wert ändern](#einen-wert-ändern)
5. [Jeden Prozess den Wert lesen lassen](#jeden-prozess-den-wert-lesen-lassen)
6. [Das Passwort ändern](#das-passwort-ändern)
7. [Häufige Fehler](#häufige-fehler)

## Wo die Werte liegen

| Datei | Geltungsbereich | Wer sie schreibt |
|---|---|---|
| `settings/site.ini` | die mitgelieferten Standards | das Release; bearbeiten Sie sie nie |
| `settings/override/site.ini.append.php` | die ganze Installation | die Installer, Sie |
| `settings/siteaccess/<name>/site.ini.append.php` | ein Siteaccess | Sie, wenn ein Siteaccess eine andere Datenbank braucht |
| `extension/<ext>/settings/site.ini.append.php` | eine aktive Erweiterung | die Erweiterung (zum Beispiel die Oracle-Aliase) |

`settings/override/` gewinnt gegen jede Erweiterung. Die Override-Dateien enthalten das Datenbankpasswort: Halten Sie sie
aus der Versionsverwaltung heraus ([Die Datenbank absichern](die-datenbank-absichern.md)).

## Den wirksamen Wert lesen

```bash
php bin/php/console exp:ini get site.ini/DatabaseSettings/DatabaseImplementation --allow-root-user
php bin/php/console exp:ini get site.ini/DatabaseSettings/Database --allow-root-user
php bin/php/console exp:ini list site.ini/DatabaseSettings global --allow-root-user
```

Die Konsole gibt zuerst auf dem Fehlerkanal eine Zeile aus, die das ausgeführte Skript nennt, dann allein den Wert, zum
Beispiel `sqlite3`. Werte, deren Name nach einem Geheimnis aussieht (darunter `Password`), werden als `********` angezeigt;
`--show-secrets` zeigt sie, verwenden Sie es also nur, wo niemand über Ihre Schulter mitliest.

In der Administration zeigt **Setup > Systeminformationen** in der Karte Datenbank und im Teil zur Datenbankverbindung die
Engine, den Server, den Datenbanknamen und den Zeichensatz; Datenbankbenutzer und Passwort werden dort nie angezeigt
([Systeminformationen](../../anleitungen/systeminformationen.md)).

## Die Datei finden, aus der ein Wert kommt

```bash
php bin/php/console exp:ini where site.ini/DatabaseSettings/Server --allow-root-user
php bin/php/console exp:ini where site.ini/DatabaseSettings/Server admin --allow-root-user   # for the siteaccess admin
```

`where` listet jede Datei, die die Variable setzt, in der Reihenfolge, in der die Einstellungen geladen werden, und den
wirksamen Wert. Zeigt ein geänderter Wert noch den alten, ging die Änderung in eine Datei oder einen Block, der nicht
gelesen wird.

## Einen Wert ändern

Bearbeiten Sie `settings/override/site.ini.append.php` mit einem Editor, oder lassen Sie `exp:ini` die Datei schreiben und
die Änderung festhalten:

```bash
php bin/php/console exp:ini set site.ini/DatabaseSettings/Server db2.example.internal global --dry-run --allow-root-user
php bin/php/console exp:ini set site.ini/DatabaseSettings/Server db2.example.internal global --allow-root-user
```

`--dry-run` zeigt die Änderung, ohne zu schreiben. Der Geltungsbereich `global` schreibt nach `settings/override/`;
`siteaccess:<name>` schreibt die Datei dieses Siteaccess. Geben Sie auf einer geteilten Maschine kein Passwort auf der
Kommandozeile an: Es ist in der Prozessliste und der Shell-Historie sichtbar; bearbeiten Sie stattdessen die Datei.

## Jeden Prozess den Wert lesen lassen

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/console exp:velocity restart --allow-root-user       # only when Velocity serves the site
```

- Leeren Sie den INI-Cache mit `--clear-tag=ini`.
- Laden Sie den PHP-FPM-Pool, der die Website ausliefert, sanft neu, sodass kein Worker die alte Verbindung behält
  ([Bereitstellen, den richtigen PHP-FPM neu laden](../../anleitungen/bereitstellung.md#34-nach-jeder-php-änderung-das-richtige-php-fpm-neu-laden)).
- Die Worker von Velocity lesen `site.ini` beim Start; starten Sie sie neu.
- Cronjobs und Konsolenbefehle lesen die Einstellungen bei jedem Lauf.

## Das Passwort ändern

1. Setzen Sie auf dem Datenbankserver das neue Passwort für das Konto der Website und lassen Sie das alte gültig, wenn die
   Engine das erlaubt (oder planen Sie ein kurzes Wartungsfenster).
2. Schreiben Sie das neue Passwort in `settings/override/site.ini.append.php`.
3. Leeren Sie den INI-Cache, laden Sie PHP-FPM neu, starten Sie Velocity neu.
4. Laden Sie eine Seite und melden Sie sich in der Administration an.
5. Entfernen Sie das alte Passwort auf dem Server.

Verwendet die Website den DFS-Cluster, hat dessen Metadatendatenbank ihre eigene Anmeldung in
`file.ini [eZDFSClusteringSettings]` und in `config.cluster.php`
([Clustering: DFS und seine Metadatendatenbank](clustering-dfs-und-die-datenbank.md)).

## Häufige Fehler

| Symptom | Ursache |
|---|---|
| Jede Inhaltsseite scheitert, `error.log` sagt "Database implementation not supported" | Ein falsch geschriebener Alias in `DatabaseImplementation` |
| SQLite: Die Website meldet fehlende Tabellen | Ein falscher Wert für `Database`: SQLite legt eine neue, leere Datei an, statt zu scheitern |
| Die Änderung erscheint auf der Kommandozeile, aber nicht im Browser | Der INI-Cache wurde nicht geleert, oder Velocity wurde nicht neu gestartet |
| Ein Siteaccess verwendet noch den alten Server | Ein Wert in `settings/siteaccess/<name>/site.ini.append.php` überschreibt den globalen; prüfen Sie mit `exp:ini where` |

## Verwandte Seiten

- [Die Referenz zu DatabaseSettings](referenz-databasesettings.md)
- [Datenbankfehler beheben](datenbankfehler-beheben.md)
- [Wo die Verbindung gespeichert ist](../hosting/control-panels/datenbanken-und-benutzer.md#wo-die-verbindung-gespeichert-wird) (für Hosting-Unternehmen)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
