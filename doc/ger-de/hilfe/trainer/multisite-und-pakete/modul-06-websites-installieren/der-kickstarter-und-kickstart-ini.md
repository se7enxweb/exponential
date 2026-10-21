---
title: "Lektion 6.3: Der Kickstarter und kickstart.ini"
audience: trainer, admin, developer
category: training
order: 63
summary: Die Teilnehmenden schreiben kickstart.ini mit exp:kickstarter ini --yes, prüfen die mit REVIEW markierten Werte, setzen die Port-Zuordnung für drei Siteaccesses, führen einen Probelauf und eine Installation aus und lesen kickstart.log und setup.log. 55 Minuten.
keywords: multisite und seitenpakete, kickstarter, probelauf, port-zuordnung, wartungsmodus, multisite and site packages, exp:kickstarter, kickstart.ini, ini --yes, run --dry-run, run --force, --list-steps, REVIEW, Continue=true, site_details, site_access, DatabaseAction, kickstart.log, setup.log, maintenance mode
type: help
---
# Lektion 6.3: Der Kickstarter und kickstart.ini

Der Kickstarter installiert aus einer Datei, `kickstart.ini`, die geprüft, aufbewahrt und erneut ausgeführt werden
kann. Das macht ihn zur Methode für Installationen, die exakt wiederholt werden müssen: Staging-Kopien, Testsysteme,
Übungsumgebungen. Die Teilnehmenden schreiben die Datei, prüfen sie, setzen die Port-Zuordnung für die Website, die
Administration und den Editor und installieren daraus.

> **Hinweis**
> `kickstart.ini` enthält Passwörter im Klartext. Halten Sie sie bei Modus 0600, committen Sie sie nie und verschieben
> Sie sie nach der Installation aus dem Wurzelverzeichnis der Installation.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md) |
| Dauer | 55 Minuten (15 Präsentation, 40 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren, Entwickler und Hoster |
| Voraussetzungen | [Lektion 6.2](installieren-mit-exp-install.md); ein weiteres frisches Code-Verzeichnis |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- `kickstart.ini` mit `exp:kickstarter ini --yes` schreiben und die mit `# REVIEW` markierten Werte prüfen;
- die Abschnitte, `Continue=true` und die Schlüssel von `[site_access]` und `[site_details]` erklären;
- eine Datei mit `run --list-steps` und `run --dry-run` prüfen und mit `run --force` installieren;
- `var/log/kickstart.log` und `var/log/setup.log` lesen und nach einem Fehler mit der Wartungsseite umgehen.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [6. Der Kickstarter](../../../../installation/06-kickstarter.md).

1. **Zwei Unterbefehle** (3 Minuten). `ini` schreibt oder bearbeitet `kickstart.ini` (interaktiv, mit `--yes` aus
   eingebauten Voreinstellungen oder mit `--defaults` aus `kickstart.ini-dist`); `run` installiert daraus. Beide gibt
   es auch als `php bin/php/kickstarter.php`.
2. **Die Datei** (5 Minuten). Ein Abschnitt pro Schritt; die Kommandozeile braucht jeden Abschnitt von
   `[email_settings]` bis `[site_admin]`, jeweils mit `Continue=true`. Kein Leerraum vor einem Schlüssel, und ein
   Kommentar hinter einem Wert wird Teil des Werts. `--yes` schreibt `Type=sqlite3`, `Site_package=sevenx_multisite`,
   `Access=url`, `DatabaseAction=ignore` und eine leere `URL`; die Kopfzeilen listen die zu prüfenden Werte auf (beide
   Schlüssel `Database`, `DatabaseAction`, `URL`, `Email` und `Password` des Administrators).
3. **Zugang** (3 Minuten). `[site_access] Access` ist `url`, `port` oder `hostname`, geschrieben als
   `MatchOrder=uri`, `port` oder `host`. `[site_details]` enthält die Werte für jede Art: `Access`, `AdminAccess`,
   `EditorAccess` für Pfade; `AccessPort`, `AdminAccessPort`, `EditorAccessPort` für Ports; die Schlüssel
   `...Hostname` für Hosts. Geben Sie zur Sicherheit alle drei einer Art an.
4. **Ausführen** (4 Minuten). `run --list-steps` gibt die Schritttabelle aus; `run --dry-run` führt `DatabaseChoice`
   bis `SiteDetails` aus, importiert das Paket in ein temporäres Repository und schreibt nichts; `run --force`
   installiert. Ohne `--force` hält ein Lauf an, der `CreateSites` enthält. Während er arbeitet, zeigt die Website die
   Wartungsseite; nach einem fehlgeschlagenen Schritt bleibt sie im Wartungsmodus bis zum nächsten erfolgreichen Lauf
   oder `php bin/php/maintenance.php off`.

### Häufige Fragen

**Was tut `DatabaseAction`?** `remove` leert zuerst die Datenbank, `ignore` fügt zu dem hinzu, was da ist (richtig für
eine neue, leere Datenbank), `skip` fügt nichts ein und schreibt nur die Einstellungen neu. Darum geht es in Lektion
6.4.

**Warum ist `URL` nach `--yes` leer?** Die Voreinstellungen raten nichts über Ihre Adresse; leer bedeutet
`http://localhost`. Setzen Sie sie immer.

**Kann ich bei einem späteren Schritt beginnen?** `--start-step` ist nur für die Diagnose gedacht; ein Start, der
Ergebnisse früherer Schritte braucht, wird abgelehnt, mit Angabe des frühesten Schritts, der funktioniert.

**Ich habe die Datei bearbeitet und sehe alte Werte.** `ini` und `run` löschen die zwischengespeicherten Kopien
`var/cache/ini/kickstart-*.php` selbst; der Assistent im Browser tut das nicht.

### Stolperfallen

- **`--from-installed`.** Es füllt die Datenbankschlüssel aus einer vorhandenen Installation: Die Datei zeigt dann
  auf die Datenbank dieser Installation. Verwenden Sie es im Kurs nicht.
- **Ein eingerückter Schlüssel.** Er wird nicht gelesen, und der Schritt schlägt wegen eines fehlenden Werts fehl.
- **Das Administratorpasswort.** Ein leeres, kurzes oder bekanntes Passwort wird nie installiert; der Lauf erzeugt
  eines, gibt es einmal aus und schreibt `var/log/initial-admin-password`.

## Praktische Übung

In einem zweiten frischen Code-Verzeichnis, zum Beispiel `kickstart-course`, als der Benutzer, der die Website
ausliefern wird.

1. Schreiben Sie die Datei aus den eingebauten Voreinstellungen:
   `php bin/php/console exp:kickstarter ini --yes`. Prüfen Sie den Modus: `ls -l kickstart.ini` zeigt `-rw-------`.
2. Lesen Sie die Kopfzeilen und jede Zeile `# REVIEW`.
3. Bearbeiten Sie `kickstart.ini` in einem Texteditor:
   - `[site_access]`: `Access=port`;
   - `[site_details]`: `Title=Kickstart course`, `URL=http://127.0.0.1:9000`, `AccessPort=9000`,
     `AdminAccessPort=9001`, `EditorAccessPort=9002`;
   - `[site_admin]`: `Email=webmaster@example.invalid`, `Password` leer gelassen.
4. Zeigen Sie die Schritte an: `php bin/php/console exp:kickstarter run --list-steps`.
5. Führen Sie ohne `--force` aus: `php bin/php/console exp:kickstarter run`. Lesen Sie die Meldung und den
   Exit-Status.
6. Probelauf: `php bin/php/console exp:kickstarter run --dry-run`. Warten Sie auf "Dry-run completed".
7. Installieren Sie: `php bin/php/console exp:kickstarter run --force`. Achten Sie auf "Maintenance mode on" am Anfang
   und die Zusammenfassung am Ende, mit dem erzeugten Passwort.
8. Lesen Sie die Port-Einstellungen, die der Lauf geschrieben hat:
   `php bin/php/console exp:ini list site.ini/PortAccessSettings global` und
   `php bin/php/console exp:ini get site.ini/SiteAccessSettings/MatchOrder`.
9. Lesen Sie die Logs: `tail -n 40 var/log/setup.log` und `grep -c '\*\*\*\*\*\*\*\*' var/log/kickstart.log` (die
   maskierten Passwörter).
10. Verschieben Sie `kickstart.ini` aus dem Wurzelverzeichnis der Installation an einen sicheren Ort (oder löschen Sie
    sie, wenn Ihr Trainer das sagt).

## Erwartetes Ergebnis

- Schritt 1: eine `kickstart.ini` mit Modus 0600 und Kopfzeilen, die die zu prüfenden Werte auflisten.
- Schritt 5 hält mit "The CreateSites step will modify the database and site settings." und "Re-run with --force ..."
  an, Exit-Status 1.
- Schritt 7 gibt die Zusammenfassung mit dem Seitenpaket, dem Titel und den URLs aus und einmal das erzeugte Passwort.
- Schritt 8 zeigt `9000`, `9001` und `9002`, der Website, der Administration und dem Editor zugeordnet, und
  `MatchOrder=port` (`uri;port`, wenn die Erweiterung `exp_adminui` vorhanden ist, für ihren Pfad `/adminui`).
- Schritt 9: `setup.log` endet mit dem Ergebnis des Laufs; `kickstart.log` enthält die Passwörter nur maskiert.
- Der Trainer prüft die Schritte 7 und 8 auf einem Bildschirm.

## Quiz

1. Welcher Unterbefehl schreibt `kickstart.ini`, und welcher installiert daraus?
2. Was muss für die Kommandozeile jeder Abschnitt von `[email_settings]` bis `[site_admin]` enthalten?
3. Was sagt `run` ohne `--force`, wenn die Schritte `CreateSites` enthalten?
   A. Nichts; er installiert B. Er hält an und verlangt `--force` C. Er führt einen Probelauf aus D. Er öffnet den
   Editor
4. Wie wird `Access=port` in die Einstellungen geschrieben?
5. Warum `kickstart.ini` nach der Installation bei Modus 0600 und außerhalb des Wurzelverzeichnisses der Installation
   halten?

<details>
<summary>Antworten</summary>

1. `ini` schreibt sie, `run` installiert daraus.
2. `Continue=true` ([6.3](../../../../installation/06-kickstarter.md#63-wie-sich-jeder-schritt-auf-der-kommandozeile-verhält)).
3. **B.**
4. Als `MatchOrder=port`, mit den Ports in `[PortAccessSettings]`.
5. Sie enthält Passwörter im Klartext, und ein später gestarteter Assistent im Browser würde sie lesen.

</details>

## Weiterführende Informationen

- [6. Der Kickstarter](../../../../installation/06-kickstarter.md): jeder Schlüssel, Beispieldateien, CI und Container.
- [Kickstarter: eine ganze Website aus einer Datei installieren](../../../../../eng-us/features/6.0/kickstarter-cli.md).
- [Der Kickstarter für Hoster](../../../hosting/bereitstellung/der-kickstarter-fuer-hoster.md).
- [Wartungsmodus](../../../../../eng-us/features/6.0/maintenance-mode.md).

## Verwandte Seiten

- [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
