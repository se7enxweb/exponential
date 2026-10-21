---
title: "Lektion 6.2: Eine Multisite mit exp:install installieren"
audience: trainer, admin, developer
category: training
order: 62
summary: Die Teilnehmenden installieren eine frische Website mit Host-Zuordnung in einem Befehl, prüfen sie vorher mit --print und --dry-run, lesen die Zusammenfassung und die Passwortdatei und testen die drei Siteaccesses mit curl. 55 Minuten.
keywords: multisite und seitenpakete, installation über die konsole, host-zuordnung, zusammenfassung, exit-status, administratorpasswort, multisite and site packages, exp:install, bin/php/install.php, --print, --dry-run, --access=host, --host, --admin-host, --editor-host, --url, --package, --db-action, EXP_INSTALL_DB_PASSWORD, initial-admin-password, exit status, summary
type: help
---
# Lektion 6.2: Eine Multisite mit exp:install installieren

`exp:install` installiert Exponential mit einem Befehl und ohne vorher eine Datei zu schreiben. Jede Option hat eine
Voreinstellung, sodass der Befehl allein das Standard-Seitenpaket auf SQLite installiert. In dieser Lektion geben die
Teilnehmenden ihm die Optionen, die eine Multisite braucht, Hostnamen für die Website, die Administration und den
Editor, prüfen sie, bevor etwas geschrieben wird, und lesen, was der Befehl meldet.

> **Hinweis**
> Führen Sie diese Lektion in einem frischen Code-Verzeichnis aus, nie in der Installation der Module 1 bis 5 und nie
> auf einer Live-Website.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md) |
| Dauer | 55 Minuten (15 Präsentation, 40 Übung; die Installation selbst dauert einige Minuten) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Administratoren, Entwickler und Hoster |
| Voraussetzungen | [Lektion 6.1](eine-engine-drei-frontends.md); ein frisches Verzeichnis mit dem Code ([Train the Trainer, Lektion 2.2](../../../../../eng-us/help/trainers/train-the-trainer/module-02-building-the-lab/getting-the-code-for-a-class.md)) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- mit `php bin/php/console exp:install` und den Optionen für die Host-Zuordnung installieren;
- die Konfiguration mit `--print` und den ganzen Lauf mit `--dry-run` prüfen;
- die Zusammenfassung lesen, das erzeugte Passwort finden und die Passwortdatei nach der ersten Anmeldung entfernen;
- den Schutz vor dem Überschreiben einer Installation, `--force` und `--db-action` erklären.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [7. Die Installation über die Konsole: exp:install](../../../../installation/07-installation-ueber-die-konsole.md).

1. **Was er tut** (4 Minuten). Er prüft jede Option, bevor etwas geschrieben wird, wählt das Administratorpasswort,
   baut die elf Abschnitte einer `kickstart.ini`, legt sie an ihren Platz (eine vorhandene wird beiseitegelegt), führt
   den Kickstarter aus, speichert die Konfiguration maskiert als `var/log/exp-install-<date>.ini`, löscht die temporäre
   Datei und gibt eine Zusammenfassung aus.
2. **Die Multisite-Optionen** (5 Minuten). `--access=host` mit `--host` und `--admin-host` (beide Pflicht und
   verschieden); der Editor bekommt `edit.<host without www.>`, sofern `--editor-host` nichts anderes sagt. `--url` ist
   die Adresse, aus der die Links jedes Siteaccess gebildet werden: Setzen Sie sie immer. `--package` wählt das
   Seitenpaket (`sevenx_multisite` als Voreinstellung; `sevenx_multisite_clean` ohne Demo-Inhalte).
3. **Prüfungen vor dem Lauf** (3 Minuten). `--print` zeigt die Konfiguration mit maskierten Passwörtern und schreibt
   nichts; `--dry-run` prüft die Datenbank und holt die Pakete in ein temporäres Repository, dann hält es an:
   "Dry-run completed: database and remote packages verified. Stopped after SiteDetails, nothing written."
4. **Das Passwort und der Schutz** (3 Minuten). Ohne `--password` wird ein Passwort mit 24 Zeichen erzeugt, in der
   Zusammenfassung angezeigt und nach `var/log/initial-admin-password` geschrieben (Modus 0600). Über einer vorhandenen
   Installation verweigert der Befehl, sofern nicht `--force`; das voreingestellte `--db-action=remove` leert dann die
   Datenbank. Übergeben Sie bei Datenbankservern das Passwort in `EXP_INSTALL_DB_PASSWORD`, nicht als Option, die in
   der Prozessliste sichtbar ist.

### Häufige Fragen

**Warum `--url` auf einem Laptop?** Ohne sie zeigt jede Adresse auf `http://localhost`, was nur stimmt, wenn die
Website auf demselben Rechner geöffnet wird.

**Startet `exp:install` einen Webserver?** Nein. Die Adressen in der Zusammenfassung funktionieren, sobald etwas das
Wurzelverzeichnis der Installation ausliefert, zum Beispiel Velocity wie in Lektion 2.4 von Train the Trainer.

**Kann er MySQL oder PostgreSQL verwenden?** Ja: `--db=mysql` oder `--db=pgsql` mit `--db-host`, `--db-name` und
`--db-user`; die Datenbank muss vorher existieren. SQLite braucht nichts.

**Wozu dient der Exit-Status?** `0` bedeutet installiert (oder ein bestandener Probelauf, `--print`, `--help`); `1`
alles andere, sodass ein Skript `$?` prüfen kann.

### Stolperfallen

- **Als root arbeiten.** Von root angelegte Dateien kann der Webserver später nicht ändern. Arbeiten Sie als der
  Benutzer, der die Website ausliefert.
- **Ein Wert mit Leerzeichen.** `--db mysql` wird abgelehnt: Jeder Wert braucht `=`.
- **Kurze Optionen.** `-f` wird abgelehnt; der Befehl kennt nur lange Optionen.
- **Das Passwort verlieren.** Es wird einmal angezeigt. Es steht außerdem in `var/log/initial-admin-password`, bis Sie
  die Datei löschen.

## Praktische Übung

In einem frischen Code-Verzeichnis, zum Beispiel `multisite-course`, als der Benutzer, der die Website ausliefern
wird.

1. Geben Sie die Konfiguration für die Host-Zuordnung aus:

   ```bash
   php bin/php/console exp:install --print --access=host \
       --host=www.club.localhost --admin-host=admin.club.localhost \
       --url=http://www.club.localhost:8087 --title="Club" --email=webmaster@example.invalid
   ```

2. Suchen Sie in der Ausgabe `Access`, `AccessHostname`, `AdminAccessHostname`, `EditorAccessHostname`,
   `Site_package` und `DatabaseAction`.
3. Probieren Sie einen Fehler: Führen Sie denselben Befehl mit `--admin-host=www.club.localhost` aus. Lesen Sie die
   Meldung und `echo $?`.
4. Führen Sie denselben Befehl wie in Schritt 1 mit `--dry-run` statt `--print` aus. Warten Sie auf die Zeile
   "Dry-run completed".
5. Installieren Sie: derselbe Befehl ohne `--print` oder `--dry-run`. Notieren Sie, wie lange es dauert.
6. Lesen Sie die Zusammenfassung: die Website, die Admin-Anmeldung, die Editor-Adresse, das Passwort und seinen
   Hinweis, die Siteaccesses.
7. Führen Sie den Installationsbefehl aus Schritt 5 ein zweites Mal aus und lesen Sie die Ablehnung. Nichts wurde
   angefasst.
8. Liefern Sie das Verzeichnis auf Port 8087 aus wie in Lektion 2.4 von Train the Trainer und testen Sie dann die drei
   Hosts:

   ```bash
   curl -sI -H 'Host: www.club.localhost' http://127.0.0.1:8087/ | head -1
   curl -sI -H 'Host: admin.club.localhost' http://127.0.0.1:8087/ | head -1
   curl -sI -H 'Host: edit.club.localhost' http://127.0.0.1:8087/ | head -1
   ```

9. Lesen Sie die Host-Zuordnung, die die Installation geschrieben hat:
   `php bin/php/console exp:ini get 'site.ini/SiteAccessSettings/HostMatchMapItems[]'`.
10. Melden Sie sich in der Administration an (Browser oder `curl`), ändern Sie das Administratorpasswort und löschen
    Sie dann `var/log/initial-admin-password`.

## Erwartetes Ergebnis

- Schritt 2: `Access=hostname` in `[site_access]`, die drei Hostnamen, der Editor unter `edit.club.localhost`,
  `Site_package=sevenx_multisite`.
- Schritt 3 hält mit "--host and --admin-host must differ" und Exit-Status 1 an.
- Schritt 5 endet mit dem Zusammenfassungsblock "Exponential is installed".
- Schritt 7: "This directory already holds an installation ... Re-run with --force ..." und Exit-Status 1.
- Schritt 8: Der öffentliche Host antwortet mit `200` (oder einer Weiterleitung zur Startseite), die Administration
  und der Editor antworten mit ihren Anmeldeseiten oder einer Weiterleitung dorthin.
- Schritt 9 listet die drei Hosts mit `site`, `admin` und `editor`.
- Der Trainer prüft Schritt 6 und Schritt 8 auf dem Bildschirm einer Person.

## Quiz

1. Welche zwei Optionen verlangt `--access=host`?
2. Was tut `--print`, was `--dry-run` nicht tut, und umgekehrt?
3. Wohin wird ein erzeugtes Administratorpasswort geschrieben, und was tun Sie mit der Datei?
4. Sie führen `exp:install` im selben Verzeichnis erneut aus. Was geschieht?
   A. Er installiert neu B. Er verweigert ohne `--force` C. Er macht ein Upgrade D. Er stellt eine Frage
5. Warum ein Datenbankpasswort in `EXP_INSTALL_DB_PASSWORD` übergeben?

<details>
<summary>Antworten</summary>

1. `--host` und `--admin-host`, und sie müssen verschieden sein.
2. `--print` zeigt die Konfiguration, ohne die Datenbank oder das Netzwerk anzufassen; `--dry-run` verbindet sich
   außerdem mit der Datenbank und holt die Pakete, dann hält es an, bevor etwas geschrieben wird
   ([7.6](../../../../installation/07-installation-ueber-die-konsole.md#76-probelauf-und-ausgabe)).
3. In `var/log/initial-admin-password` (Modus 0600); ändern Sie das Passwort nach der ersten Anmeldung und löschen Sie
   die Datei.
4. **B.**
5. Eine Option ist für jeden Benutzer des Rechners in der Prozessliste sichtbar und bleibt in der Shell-Historie.

</details>

## Weiterführende Informationen

- [7. Die Installation über die Konsole: exp:install](../../../../installation/07-installation-ueber-die-konsole.md).
- [Exponential mit einem Befehl installieren](../../../../../eng-us/features/6.0/install-in-one-command.md).
- [exp:install für Hoster](../../../hosting/bereitstellung/exp-install-fuer-hoster.md).
- [8. Die Website ausliefern](../../../../installation/08-die-website-ausliefern.md) und
  [10. Nach der Installation](../../../../installation/10-nach-der-installation.md).

## Verwandte Seiten

- [Modul 6: Websites mit dem Kickstarter und exp:install installieren](README.md)
- [Multisite und Seitenpakete: Kursübersicht](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
