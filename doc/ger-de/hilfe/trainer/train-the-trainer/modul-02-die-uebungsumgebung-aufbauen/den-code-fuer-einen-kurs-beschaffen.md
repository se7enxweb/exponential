---
title: "Lektion 2.2: Den Code für einen Kurs beschaffen"
audience: trainer, admin, developer
category: training
order: 22
summary: Wählen Sie, wie die Teilnehmenden den Code von Exponential bekommen, einen Git-Klon mit Composer für Entwicklerkurse, composer create-project für Kurse für Redakteure und Administratoren, und legen Sie alle Teilnehmenden auf dasselbe Release fest. 40 Minuten.
keywords: git clone, composer install, composer create-project, release, tag, version, übungsumgebung, se7enxweb, preferred-install, source, quellcode, getting the code, training lab
type: help
---
# Lektion 2.2: Den Code für einen Kurs beschaffen

Es gibt drei Wege, Exponential auf einen Rechner zu bringen, und sie passen zu verschiedenen Kursen. Ein
Entwicklerkurs will Git-Klone mit ihrer Geschichte; ein Kurs für Redakteure braucht nur eine funktionierende Website.
Diese Lektion zeigt jeden Weg und wie Sie sicherstellen, dass alle Teilnehmenden genau dieselbe Version haben; das hält
die Übungen reproduzierbar.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Train the Trainer](../README.md) |
| Modul | [Modul 2: Die Übungsumgebung aufbauen](README.md) |
| Dauer | 40 Minuten (10 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Trainer, die Übungsumgebungen vorbereiten |
| Voraussetzungen | [Lektion 2.1](anforderungen-an-einen-schulungsrechner.md); Git und Composer auf dem Rechner |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- für einen bestimmten Kurs zwischen `git clone` mit `composer install` und `composer create-project` wählen;
- ein gewähltes Release statt der Entwicklungslinie installieren;
- die neuesten Release-Tags in der richtigen Reihenfolge auflisten;
- prüfen, dass die Erweiterungen als Git-Klone angekommen sind, wenn sie das sollen.

## Hinweise für Trainer

### So präsentieren Sie es

1. Zeigen Sie die drei Wege aus [Den Code beschaffen, 3.2](../../../../installation/03-den-code-beschaffen.md#32-drei-wege-zum-code)
   (5 Minuten): A. `git clone` und `composer install` (empfohlen, um Websites zu bauen), B. `composer create-project`
   (eine Website, die Sie nur betreiben, eine schnelle Erprobung), C. `composer require` in ein leeres Verzeichnis.
2. Empfehlen Sie pro Kurs (3 Minuten): Entwickler, Theme-Design und die Entwicklung mit Layouts: Weg A. Redakteure,
   Website-Administration, Hosting: Weg B mit einem festen Release.
3. Zeigen Sie, wie man ein Release festlegt (2 Minuten): `composer create-project se7enxweb/exponential:v6.0.14 exponential`,
   oder bei einem Klon `git switch -c my-site v6.0.14` vor `composer install`.

### Häufige Fragen

**`main` oder ein Release?** Für einen Kurs ein Release: Alle Teilnehmenden sehen dasselbe, und Ihre Probe passt dazu.
Verwenden Sie `main` nur für einen Kurs über das Mitwirken.

**Warum ist `git status` voller nicht verfolgter Erweiterungen?** Die Erweiterungen sind eigene Git-Repositorys, die
Composer nach `extension/<name>` klont; sie gehören nicht zum Repository des Kernels. Das ist zu erwarten.

**Wie sehe ich das neueste Release?** `git tag -l 'v6.*' --sort=version:refname | tail -3`. Sortieren Sie immer nach
Version: Nach Namen kommt `v6.0.10` vor `v6.0.8`.

### Stolperfallen

- **Ein langsames Netz.** Der vollständige Klon enthält die ganze Geschichte (mehr als 27.000 Commits). Bereiten Sie
  den Code für einen Kurs in einem langsamen Netz im Voraus auf jedem Rechner oder auf einem gemeinsamen Laufwerk vor.
- **Gemischte Versionen.** Eine Person auf `main`, der Rest auf einem Release: Die Übungen unterscheiden sich. Prüfen
  Sie mit `git describe --tags`.
- **Als root ausführen.** Dateien, die `root` angelegt hat, kann der Webserver später nicht ändern. Installieren Sie
  als normaler Benutzer.

## Praktische Übung

Bereiten Sie auf einem Rechner den Code für einen Entwicklerkurs (Weg A) und für einen Kurs für Redakteure (Weg B) vor.

1. Für Weg A, einmal pro Benutzerkonto: `composer config --global 'preferred-install.se7enxweb/*' source`.
2. `git clone https://github.com/se7enxweb/exponential.git dev-course`, dann `cd dev-course`.
3. Listen Sie die neuesten Releases auf: `git fetch origin --tags` und
   `git tag -l 'v6.*' --sort=version:refname | tail -3`. Wählen Sie das neueste.
4. `git switch -c training <the tag>` und dann `composer install`.
5. Prüfen Sie: `ls -d extension/*/.git | wc -l` gibt die Zahl der als Klone installierten Erweiterungen aus;
   `git describe --tags` nennt das Release.
6. Für Weg B, in einem anderen Verzeichnis: `composer create-project se7enxweb/exponential:<the tag> editor-course`.
7. Schreiben Sie die beiden genauen Befehlsfolgen mit dem verwendeten Tag auf Ihr Trainerblatt.

## Erwartetes Ergebnis

- `dev-course` ist ein Klon auf dem Zweig `training`, angelegt aus dem gewählten Release, mit den Erweiterungen als
  Git-Klone.
- `editor-course` enthält dasselbe Release, aus Archiven installiert.
- Das Trainerblatt enthält die beiden Befehlsfolgen mit einem festen Tag.

## Quiz

1. Welcher Weg, den Code zu beschaffen, wird für Entwicklerkurse empfohlen?
2. Warum sollten alle Teilnehmenden eines Kurses dasselbe Release verwenden?
3. Warum ist `git tag -l 'v6.*' | tail -1` ein falscher Weg, das neueste Release zu finden?
4. Nach `composer install` in einem Klon listet `git status` viele nicht verfolgte Verzeichnisse unter `extension/`
   auf. Ist das ein Problem?

<details>
<summary>Antworten</summary>

1. `git clone` mit `composer install` (Weg A).
2. Damit jede Übung dasselbe Ergebnis liefert wie die Probe des Trainers.
3. Es sortiert nach Namen, sodass `v6.0.10` vor `v6.0.8` kommt; verwenden Sie `--sort=version:refname`
   ([Mit Git aktualisieren](../../../mit-git-aktualisieren.md)).
4. Nein. Die Erweiterungen sind eigene Repositorys und gehören nicht zum Repository des Kernels.

</details>

## Weiterführende Literatur

- [Den Code beschaffen](../../../../installation/03-den-code-beschaffen.md): alle drei Wege im Detail.
- [Mit Git entwickeln](../../../mit-git-entwickeln.md): mit den Klonen arbeiten.
- [Mit Git aktualisieren](../../../mit-git-aktualisieren.md): eine Übungsumgebung zwischen den Kursen aktuell halten.

## Verwandte Seiten

- [Modul 2: Die Übungsumgebung aufbauen](README.md)
- [Train the Trainer: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
