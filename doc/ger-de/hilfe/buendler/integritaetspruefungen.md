---
title: Integritätsprüfungen für ein Bundle
audience: bundler, hosting, developer
category: bundling
order: 410
summary: Wie ein Bündler nachweist, dass die Exponential-Dateien in einem Bundle die veröffentlichten sind - das Dateimanifest share/filelist.md5, bin/php/checkmanifest.php und seine Optionen, die Dateikonsistenzprüfung der Administration, Manifeste von Erweiterungen, Prüfsummen von Paketen und Downloads und was mit beabsichtigten Änderungen geschieht.
keywords: Integrität, Manifest, Dateikonsistenz, Upgrade-Prüfung, Prüfsumme, integrity, manifest, share/filelist.md5, checkmanifest.php, --all, --extensions, --csv, --staged, --fix, file consistency check, upgrade check, SHA256SUMS, checksum, bundle
type: help
---
# Integritätsprüfungen für ein Bundle

Diese Seite richtet sich an Bündler, die zeigen und prüfen möchten, dass das Exponential in ihrem Bundle unverändert
ist, und an Supportteams, die wissen müssen, ob eine Website im Einsatz es noch ist.

## Inhalt

1. [Das Dateimanifest](#das-dateimanifest)
2. [checkmanifest.php](#checkmanifestphp)
3. [Die Prüfung in der Administration](#die-prüfung-in-der-administration)
4. [Erweiterungen mit eigenem Manifest](#erweiterungen-mit-eigenem-manifest)
5. [Prüfsummen dessen, was Sie herunterladen und ausliefern](#prüfsummen-dessen-was-sie-herunterladen-und-ausliefern)
6. [Beabsichtigte Änderungen](#beabsichtigte-änderungen)

## Das Dateimanifest

`share/filelist.md5` führt "`md5  path`" für jede Datei der Distribution auf; "die System-Upgrade-Prüfung der
Administrationsoberfläche vergleicht die Dateien damit, sodass ein veralteter Eintrag dort als geänderte Datei
erscheint" (`bin/php/checkmanifest.php`, Kopf). Die eingebaute Hilfe verwendet es in einer Installation ohne `.git`
auch als Dateiliste ([Mitzuliefernde Dokumentation](mitzuliefernde-dokumentation.md)).

## checkmanifest.php

Das Skript "braucht keinen Kernel und keine Datenbank", es läuft also in jedem Build-Schritt:

| Befehl | Tut |
|---|---|
| `php bin/php/checkmanifest.php --all` | jeder Eintrag existiert und hat seine Prüfsumme; Git-Dateien, die im Manifest fehlen, Zeilen außer der Reihe und fehlerhafte Zeilen sind Warnungen |
| `php bin/php/checkmanifest.php --all --extensions` | prüft außerdem die `share/filelist.md5`, die jede Erweiterung mitbringen kann |
| `php bin/php/checkmanifest.php --all --csv` | die Befunde als CSV, "dieselben Spalten wie der Download der Upgrade-Prüfung" |
| `php bin/php/checkmanifest.php --staged` | die für den nächsten Commit vorgemerkten Dateien (für die Entwicklung) |
| `php bin/php/checkmanifest.php --fix` | schreibt veraltete Prüfsummen neu, ergänzt fehlende Dateien, entfernt gelöschte |

"Exit-Code 0, wenn alles übereinstimmt, sonst 1." Führen Sie `--all` auf dem gebauten Bundle aus und halten Sie das
Ergebnis im Build-Protokoll fest
([Lock-Dateien und reproduzierbare Builds](lock-dateien-und-reproduzierbare-builds.md#das-build-protokoll)).

> **Hinweis**
> `--fix` passt das Manifest an Ihre Dateien an. Bei einem Bundle würde das Ihre Änderungen vor der Upgrade-Prüfung
> des Empfängers verbergen. Verwenden Sie es nur in einem Repository, das Sie pflegen, nie, um ein verändertes Bundle
> "bestehen" zu lassen.

## Die Prüfung in der Administration

**Setup > System Upgrade > Überprüfe Dateikonsistenz** in der Administration vergleicht die Installation mit dem
Manifest und listet jede Zeile, die nicht mehr übereinstimmt, als geänderte Datei auf
([Metadaten von Erweiterungen: Dateimanifest](../../spezifikationen/6.0/extension-metadaten.md#dateimanifest-sharefilelistmd5)).
Empfänger und Supportmitarbeiter können sie jederzeit ausführen; der CSV-Download hat dieselben Spalten wie `--csv`.

## Erweiterungen mit eigenem Manifest

Einige Erweiterungen liefern eine eigene `share/filelist.md5` aus (zum Beispiel `cjw_newsletter`). Eine solche
Erweiterung frischt ihr Manifest "im selben Release wie jede Dateiänderung" auf, sonst listet die Prüfung ihre Dateien
als geändert. Prüfen Sie sie mit `--all --extensions`. Wenn Sie Erweiterungen mit einem Manifest veröffentlichen,
halten Sie es aktuell ([Erweiterungs-Bundles](erweiterungs-bundles.md)).

## Prüfsummen dessen, was Sie herunterladen und ausliefern

| Element | Quelle der Prüfsumme |
|---|---|
| Release-Dateien von Velocity | `SHA256SUMS` im Release ([Velocity-Pakete](../../funktionen/6.0/velocity-pakete-und-binaerdateien.md#eine-form-wählen)) |
| FrankenPHP-Binärdatei | `exp:velocity install` prüft einen festgelegten SHA-256 ([FrankenPHP](../../bc/6.0/frankenphp.md)) |
| Seitenpakete | Ihre eigene Aufzeichnung jeder `.ezpkg`; Pakete können sich auf dem öffentlichen Server an Ort und Stelle ändern |
| Die Artefakte Ihres Bundles | veröffentlichen Sie mit jedem Release eine eigene `SHA256SUMS` |

## Beabsichtigte Änderungen

Ein Bundle, das Kernel-Dateien ändert, zeigt diese Dateien für immer als geändert an. Ziehen Sie vor:

1. eine Einstellung oder ein Override-Template in Ihrer eigenen Erweiterung;
2. ein zweites Erweiterungsverzeichnis für Ihren Code
   ([Zusätzliche Erweiterungsverzeichnisse](../../bc/6.0/zusaetzliche-erweiterungsverzeichnisse.md));
3. die Änderung beim Projekt einzureichen ([Korrekturen zurückgeben](bundle-korrekturen-zurueckgeben.md)).

Ist eine Kernel-Änderung unvermeidlich, führen Sie sie in Ihren Release-Hinweisen auf, damit die
Dateikonsistenzprüfung eines Empfängers nachvollziehbar bleibt.

## Verwandte Seiten

- [Eine Kundeninstallation überprüfen](../partner/eine-kundeninstallation-pruefen.md)
- [Ein Bundle testen](ein-bundle-testen.md)
- [Checkliste für die Bundle-Veröffentlichung](checkliste-fuer-die-bundle-veroeffentlichung.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
