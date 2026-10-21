---
title: "Lektion 2.1: Anforderungen an einen Schulungsrechner"
audience: trainer, admin
category: training
order: 21
summary: Prüfen Sie vor dem Kurs, ob der Laptop einer Teilnehmerin oder eines Teilnehmers oder ein Schulungsserver Exponential ausführen kann: PHP-Version, die nötigen PHP-Erweiterungen, Werte in php.ini, Composer und Git, mit den Befehlen, die zeigen, was fehlt. 40 Minuten.
keywords: anforderungen, übungsumgebung, laptop, PHP 8, php-erweiterungen, php -m, intl, xsl, sqlite3, composer, git, velocity ext check, systemprüfung, requirements, training lab, PHP extensions, system check
type: help
---
# Lektion 2.1: Anforderungen an einen Schulungsrechner

Die meiste Zeit in einem praktischen Kurs geht in der ersten halben Stunde verloren, an einem Laptop, dem eine
PHP-Erweiterung fehlt. Diese Lektion gibt dem Trainer die genauen Anforderungen von Exponential 6 und die Befehle, um
einen Rechner in zwei Minuten zu prüfen, damit die Prüfung Tage vor dem Kurs stattfindet und nicht währenddessen.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 2: Die Übungsumgebung aufbauen](README.md) |
| Dauer | 40 Minuten (15 Präsentation, 25 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Übungsumgebungen vorbereiten; Administratoren, die ihnen helfen |
| Voraussetzungen | Ein Terminal auf dem zu prüfenden Rechner; Grundkenntnisse der Kommandozeile |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die PHP-Version nennen, die Exponential braucht, und die höhere, die Velocity braucht;
- die PHP-Erweiterungen aufzählen, die die Prüfungen des Setups als kritisch behandeln;
- einen Rechner mit `php -v` und `php -m` prüfen und das Ergebnis lesen;
- eine einseitige Checkliste für die Teilnehmenden vor dem Kurs schreiben.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zeigen Sie die Checklistentabelle aus [Voraussetzungen, 2.1](../../../../installation/02-voraussetzungen.md#21-die-checkliste)
   (5 Minuten). Die wichtigen Zahlen: PHP 8.0 als Minimum für Exponential und für Exponential Velocity mit `sockets`;
   SQLite 3 braucht keinen Datenbankserver; Composer 2.x.
2. Gehen Sie die nötigen Erweiterungen durch (5 Minuten): `dom`, `libxml`, `simplexml`, `xsl`, `mbstring`, `iconv`,
   `intl`, `pcre`, `json`, `reflection`, `spl`, `session`, `zlib`, eine Datenbankerweiterung (für eine
   Übungsumgebung: `sqlite3`) und `gd` oder `convert` von ImageMagick.
3. Zeigen Sie `php -m` auf Ihrem eigenen Rechner und suchen Sie zwei davon in der Liste (2 Minuten).
4. Erklären Sie, warum SQLite die richtige Datenbank für einen Kurs ist (3 Minuten): kein Server, kein Passwort, eine
   Datei pro Installation, und sie ist die erste Datenbank in der offiziellen Rangfolge.

### Häufige Fragen

**Windows-Laptops?** Prüfen Sie unter [Unterstützte Betriebssysteme](../../../betriebssystemunterstuetzung.md), was das Betriebssystem
bereitstellen muss. Wenn Sie unsicher sind, bereiten Sie eine Ausweichlösung vor: eine vorinstallierte Kopie pro
Teilnehmer auf einem Rechner, den Sie kontrollieren.

**Brauchen die Teilnehmenden Velocity?** Nein. Der eingebaute Server von PHP, den Velocity als seine Engine `php`
steuert, reicht für eine Übungsumgebung. Die Engine `qbix` von Velocity braucht PHP 8.0 oder neuer (ab Release
0.0.4.54) und `sockets`.

**Welche Werte in `php.ini` zählen?** `memory_limit` 64M oder mehr, `max_execution_time` 30, `date.timezone` gesetzt,
`file_uploads` an, `allow_url_fopen` an.

### Stolperfallen

- **Zwei PHPs.** Das PHP der Kommandozeile und das PHP des Webservers können sich unterscheiden. Für eine
  Übungsumgebung, die der eingebaute Server bereitstellt, zählt nur das PHP der Kommandozeile.
- **`intl` und `xsl` fehlen.** Diese beiden fehlen auf frischen Systemen am häufigsten; die Prüfung des Setups geht
  ohne sie nicht weiter.
- **Kein `date.timezone`.** Setzen Sie es vor dem Kurs in `php.ini`.

## Praktische Übung

Prüfen Sie einen Rechner: Ihren Laptop, den Laptop einer Teilnehmerin oder eines Teilnehmers oder den
Schulungsserver.

1. Öffnen Sie ein Terminal und führen Sie `php -v` aus. Notieren Sie die Version (sie muss 8.0 oder neuer sein; 8.1
   oder neuer, wenn Sie die Engine `qbix` von Velocity verwenden werden).
2. Führen Sie `php -m` aus und vergleichen Sie die Ausgabe mit der Liste oben. Schreiben Sie jede fehlende Erweiterung
   auf.
3. Führen Sie `php -i | grep -E 'memory_limit|max_execution_time|date.timezone|file_uploads|allow_url_fopen'` aus und
   vergleichen Sie die Werte mit den Hinweisen für Trainer.
4. Führen Sie `composer --version` und `git --version` aus. Beide müssen antworten.
5. Liegt auf dem Rechner schon ein Exponential-Checkout mit Velocity, führen Sie in seinem Wurzelverzeichnis
   `php bin/php/console exp:velocity ext check` aus und lesen Sie die Zeilen mit `ok`.
6. Schreiben Sie eine Checkliste für die Teilnehmenden mit höchstens zehn Zeilen: PHP-Version, die Erweiterungen, die
   Werte in `php.ini`, Composer, Git und wo man vor dem Kurs Hilfe bekommt.

## Erwartetes Ergebnis

- Eine Liste mit der PHP-Version des Rechners, den fehlenden Erweiterungen (idealerweise keine) und den Werten in
  `php.ini`.
- Eine Checkliste für die Teilnehmenden mit zehn oder weniger Zeilen, der man allein folgen kann.
- Der Trainer prüft: Stehen `intl`, `xsl` und `sqlite3` auf der Liste? Stimmt das PHP-Minimum?

## Quiz

1. Was ist die minimale PHP-Version für Exponential 6, und für Velocity?
2. Nennen Sie vier PHP-Erweiterungen, die die kritischen Tests des Setups verlangen.
3. Warum ist SQLite die übliche Wahl für eine Übungsumgebung?
4. Welcher Befehl listet die PHP-Erweiterungen des PHP der Kommandozeile auf?

<details>
<summary>Antworten</summary>

1. PHP 8.0 für Exponential und für Velocity mit `sockets`
   ([Voraussetzungen](../../../../installation/02-voraussetzungen.md#21-die-checkliste)).
2. Vier beliebige von `dom`, `xsl`, `mbstring`, `iconv`, `intl`, `session`, `zlib`, eine Datenbankerweiterung, `gd`
   (oder ImageMagick).
3. Es braucht keinen Datenbankserver und kein Passwort; jede Installation ist eine Datei; es ist die Voreinstellung
   von `exp:install`.
4. `php -m`.

</details>

## Weiterführende Literatur

- [Voraussetzungen](../../../../installation/02-voraussetzungen.md): das vollständige Kapitel.
- [Unterstützte Betriebssysteme](../../../betriebssystemunterstuetzung.md): was jedes Betriebssystem bereitstellen muss.
- [Unterstützte Datenbanken](../../../datenbankunterstuetzung.md): die Datenbanken und ihre Rangfolge.

## Verwandte Seiten

- [Modul 2: Die Übungsumgebung aufbauen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
