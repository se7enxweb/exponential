---
title: "Lektion 4.1: Eine Änderung bereitstellen"
audience: trainer, hosting, admin
category: training
order: 41
summary: Die Teilnehmenden liefern eine PHP- oder INI-Änderung mit exp:velocity deploy aus, lesen den Probelauf Schritt für Schritt, erklären, warum die Reihenfolge der Schritte wichtig ist, und behandeln reine Template-Änderungen ohne Neustart. 45 Minuten.
keywords: hosting und betrieb, bereitstellung, exp:velocity deploy, probelauf, autoloads, INI-cache, PHP-FPM neu laden, PhpFpmReload, WarmUpURL, template-änderung, exp:cache clear, antwort-cache, hosting and operations, deploy, dry run, template change, response cache
type: help
---
# Lektion 4.1: Eine Änderung bereitstellen

Bei persistenten Servern wird eine geänderte PHP-Klasse oder INI-Datei nicht von selbst wirksam, und Caches, die zum
falschen Zeitpunkt geleert werden, füllen sich wieder mit Seiten aus dem alten Code. Das Ergebnis sieht genau so aus
wie „die Änderung hat nicht funktioniert“. Diese Lektion vermittelt den einen Befehl, der jeden Schritt in der richtigen
Reihenfolge ausführt, und die kleinere Routine für Templates.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 4: Bereitstellung und Cron](README.md) |
| Dauer | 45 Minuten (20 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren, Support-Mitarbeitende im Hosting und Entwickler, die bereitstellen |
| Voraussetzungen | [Modul 2](../modul-02-velocity/README.md); eine Schulungsinstallation, ausgeliefert von Velocity |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `exp:velocity deploy --dry-run` ausführen und jeden Schritt erklären, den es ausgibt;
- erklären, warum Caches mit dargestellter Ausgabe erst geleert werden, wenn PHP-FPM und Velocity den neuen Code
  ausführen;
- die richtigen Optionen wählen: `--kernel`, `--no-velocity`, `--no-fpm`, `--fpm-reload`, `--packer`;
- eine Template- oder Stylesheet-Änderung ohne Neustart bereitstellen;
- Adressen zum Vorwärmen setzen, damit nicht der erste Besucher die Caches neu aufbaut.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Änderungen bereitstellen](../../../hosting/betrieb/aenderungen-ausrollen.md),
[Exponential bereitstellen, Abschnitt 7](../../../../anleitungen/bereitstellung.md#7-eine-php-änderung-mit-einem-befehl-ausliefern) und
[Kapitel 8, Abschnitt 8.3.14](../../../../installation/08-die-website-ausliefern.md#8314-eine-änderung-bereitstellen-expvelocity-deploy).

1. **Das Problem** (3 Minuten). Worker halten Klassen, INI-Werte und kompilierte Templates im Speicher. Ein
   PHP-FPM-Pool mit OPcache kann alten Code behalten, bis er neu validiert oder neu lädt.
2. **Die Schritte** (6 Minuten). Zeigen Sie die Tabelle der Schritte aus *Änderungen bereitstellen*:
   Erweiterungs-Autoloads, Kernel-Autoloads (nur mit `--kernel`), INI-Caches, Template-Caches, Engine-Archiv, PHP-FPM,
   Velocity neu starten, Inhalts-, HTTP- und Template-Block-Caches, den Antwort-Cache, Anfragen zum Vorwärmen. „Die
   Reihenfolge ist der springende Punkt.“
3. **Optionen** (4 Minuten). `--no-velocity` für eine Website, die nur von Apache oder nginx ausgeliefert wird,
   `--no-fpm` für Velocity allein, `--fpm-reload` nach einer Änderung an `php.ini` oder am Pool, `--packer`, wenn sich
   eine Funktion `ezjscServer_*` geändert hat. Mit `PhpFpmReload=auto` wartet die Bereitstellung auf die
   Neuvalidierung von OPcache, statt einen gemeinsam genutzten Master neu zu laden.
4. **Templates** (3 Minuten). Änderungen an Templates oder Stylesheets brauchen keinen Neustart: Leeren Sie die
   Template-Caches, dann den Antwort-Cache.
5. **Einen Lauf lesen** (4 Minuten). Jeder Schritt gibt `PASS`, `FAIL` oder `SKIP` mit seiner Dauer aus; der erste
   Fehler stoppt den Lauf mit Rückgabewert 1, und die nicht ausgeführten Schritte werden als `NOT RUN` aufgelistet.

### Häufige Fragen

**Kann ich einfach alle Caches leeren?** Wer jeden Cache leert und die Server vergisst, lässt die alten Klassen in FPM
und Velocity im Speicher. Verwenden Sie die Bereitstellung.

**Warum stand beim Schritt zum Neuladen SKIP?** Er wird mit einem Hinweis übersprungen, wenn kein Pool gefunden wurde,
wenn die Bereitstellung nicht als root läuft oder wenn es kein `systemctl` gibt. Setzen Sie auf einer Website nur mit
Velocity `[DeploySettings] PhpFpmService=disabled`.

**Ist eine Bereitstellung während der Besuchszeiten sicher?** Ein Neustart von Velocity verliert nichts, was `graceful`
behalten würde; ein Neuladen von PHP-FPM lässt jede Website dieses Masters auf die am längsten laufende Anfrage warten.
Planen Sie `--fpm-reload` als Serverwartung ein.

**Was ist mit mehreren Webservern?** *Änderungen bereitstellen* hat einen Abschnitt über große Änderungen und mehrere
Knoten; die Bereitstellung läuft auf jedem Knoten.

### Stolperfallen

- **Die Bereitstellung als root ausführen** bei einer Website, deren Dateien einem anderen Benutzer gehören. Die
  Caches, die sie schreibt, gehören dann möglicherweise root. Führen Sie sie, wo möglich, als Benutzer der Website aus.
- **Den Probelauf im Kurs auslassen.** Beginnen Sie immer mit `--dry-run`; er ist das Lehrmittel.
- **Erwarten, dass eine Template-Änderung einen Neustart braucht.** Sie braucht keinen, und ein Neustart im Kurs
  verschwendet Zeit.

## Praktische Übung

Verwenden Sie Ihre eigene Schulungsinstallation, ausgeliefert von Velocity, und ein Terminal im Wurzelverzeichnis der
Installation.

1. Sehen Sie sich eine Bereitstellung vorab an:

   ```bash
   php bin/php/console exp:velocity deploy --dry-run
   ```

2. Zählen Sie die Schritte und suchen Sie den, der den Antwort-Cache von Velocity leert. Notieren Sie seine Nummer.
3. Lesen Sie den PHP-FPM-Schritt und notieren Sie, ob er neu laden, warten oder überspringen würde, und warum.
4. Nehmen Sie eine harmlose INI-Änderung an Ihrer Website vor, zum Beispiel einen neuen Website-Namen:

   ```bash
   php bin/php/console exp:ini get site.ini/SiteSettings/SiteName
   php bin/php/console exp:ini set site.ini/SiteSettings/SiteName "Training site (deploy test)" global
   ```

5. Laden Sie die Startseite neu. Notieren Sie, ob der neue Name schon erscheint.
6. Führen Sie die Bereitstellung wirklich aus: `php bin/php/console exp:velocity deploy`.
7. Lesen Sie die Ausgabe: jeder Schritt `PASS` oder `SKIP`, die Dauer jedes Schritts.
8. Laden Sie die Startseite neu und prüfen Sie den Website-Namen.
9. Setzen Sie mit `exp:ini set` und dem Wert aus Schritt 4 den alten Namen zurück und führen Sie die Bereitstellung
   erneut aus.
10. Leeren Sie nur, was eine Template-Änderung braucht:

    ```bash
    php bin/php/console exp:cache clear --id=template
    php bin/php/console exp:velocity cache clear
    ```

## Erwartetes Ergebnis

- Schritt 1 listet jeden Schritt mit `DRY` markiert auf und ändert nichts.
- Schritt 5: Der alte Name erscheint möglicherweise noch (Worker und Caches halten den alten Wert).
- Schritt 6 endet mit jedem Schritt `PASS` oder `SKIP`; Schritt 8 zeigt den neuen Namen.
- Nach Schritt 9 ist der ursprüngliche Name zurück.
- Der Trainer prüft das, indem er nach der Nummer des Schritts für den Antwort-Cache und nach dem Grund fragt, den der
  PHP-FPM-Schritt angegeben hat.

![Ein Wartungsfenster, geplant vor einer Bereitstellung](../../../../../eng-us/help/images/trainers/ho-maint.webp)

## Quiz

1. Warum werden der Inhalts-View-Cache und der Antwort-Cache gegen Ende einer Bereitstellung geleert?
2. Welche Option fügen Sie hinzu, wenn eine Kernel-Klasse hinzugefügt oder umbenannt wurde?
   A. `--packer` B. `--kernel` C. `--rebuild-phar` D. `--no-autoload`
3. Eine Website wird nur von Apache mit PHP-FPM ausgeliefert. Welche Option überspringt den Velocity-Schritt?
4. Was tut eine Bereitstellung, wenn ein Schritt fehlschlägt?
5. Was genügt nach einer reinen Template-Änderung?

<details>
<summary>Antworten</summary>

1. Würden sie früher geleert, würde jede in der Zwischenzeit angefragte Seite vom alten Code dargestellt und erneut
   zwischengespeichert ([Änderungen bereitstellen](../../../hosting/betrieb/aenderungen-ausrollen.md#die-schritte-und-ihre-reihenfolge)).
2. **B.** `--kernel` erzeugt auch die Kernel-Autoloads neu.
3. `--no-velocity`.
4. Sie stoppt mit Rückgabewert 1; die nicht ausgeführten Schritte werden als `NOT RUN` aufgelistet.
5. Die Template-Caches leeren und danach den Antwort-Cache von Velocity; kein Neustart.

</details>

## Weiterführende Informationen

- [Änderungen bereitstellen](../../../hosting/betrieb/aenderungen-ausrollen.md): die Sicht des Hosters, Einstellungen pro
  Umgebung.
- [Exponential bereitstellen](../../../../anleitungen/bereitstellung.md): der Rundgang von einer frischen Installation bis zu
  HTTPS.
- [Durch die Bereitstellung verwaltet](../../../../funktionen/6.0/durch-deployment-verwaltet.md): Konfiguration, die die
  Bereitstellung verwaltet.
- [Der Befehl exp:ini](../../../../funktionen/6.0/exp-ini-befehl.md): Einstellungen auf der Kommandozeile lesen und
  schreiben.

## Verwandte Seiten

- [Modul 4: Bereitstellung und Cron](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
