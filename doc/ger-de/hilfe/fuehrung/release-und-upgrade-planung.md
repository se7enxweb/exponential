---
title: Release- und Upgrade-Planung
audience: project-manager, admin
category: leadership
order: 90
summary: Wie Exponential-Versionen nummeriert und veröffentlicht werden, wozu sich das Projekt verpflichtet und wie Sie das Upgrade einer Site von einer beliebigen 6.0.x (oder älteren) Version auf die aktuelle Produktlinie planen, terminieren und überprüfen.
keywords: Release, Upgrade, Version, Versionierung, Tag, unveränderlich, Changelog, Verhaltensänderungen, Upgrade-Prüfung, Wartungsmodus, Backup, PHP-Version, Packagist, Planung, Zeitplan, Rückfall, Rollback, versioning, immutable, behavior changes, bc, upgrade check, maintenance mode, backup, PHP version, planning, schedule, rollback
type: help
---
# Release- und Upgrade-Planung

Diese Seite richtet sich an Projektleiter und Administratoren, die entscheiden müssen, **wann** eine Site auf eine
neue Version von Exponential umzieht, **was** dieser Umzug umfasst und **woran** man erkennt, dass er gelungen ist.
Sie erklärt, wie das Projekt seine Releases nummeriert und veröffentlicht und wozu es sich verpflichtet, und sie macht
aus dem Upgrade-Kapitel des Installationshandbuchs einen Plan, den Sie in einen Zeitplan übernehmen können: eine
Entscheidungstabelle, einen Ablauf, eine Checkliste und ein durchgespieltes Beispiel.

Die Befehle Schritt für Schritt werden hier nicht vollständig wiederholt. Sie stehen in
[11. Upgrade](../../installation/11-aktualisieren.md) und im [Upgrade-Leitfaden](../../anleitungen/aktualisieren.md); diese Seite sagt
Ihnen, welche Teile Sie lesen sollten und wer was tut.

## Inhalt

1. [Wie Exponential-Releases funktionieren](#wie-exponential-releases-funktionieren)
2. [Wozu sich das Projekt verpflichtet](#wozu-sich-das-projekt-verpflichtet)
3. [Wo Sie nachlesen, was sich geändert hat](#wo-sie-nachlesen-was-sich-geändert-hat)
4. [Entscheiden, wann ein Upgrade erfolgt](#entscheiden-wann-ein-upgrade-erfolgt)
5. [Der Upgrade-Plan, Phase für Phase](#der-upgrade-plan-phase-für-phase)
6. [Rollen und Übergaben während eines Upgrades](#rollen-und-übergaben-während-eines-upgrades)
7. [Upgrade-Checkliste](#upgrade-checkliste)
8. [Durchgespieltes Beispiel: von 6.0.12 auf die aktuelle Produktlinie](#durchgespieltes-beispiel-von-6012-auf-die-aktuelle-produktlinie)
9. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)
10. [Eigene Erweiterungen veröffentlichen](#eigene-erweiterungen-veröffentlichen)

## Wie Exponential-Releases funktionieren

### Die bisherigen Versionen

Exponential 6.0.0 wurde am 1. Januar 2024 veröffentlicht, 6.0.14 am 7. Juni 2026. Die Produktlinie 6.0.15 ist in
Entwicklung: Das [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) vermerkt „Not released yet“ (noch nicht
veröffentlicht) und listet auf, was die Linie enthält. Die vollständige Liste der Daten steht unter
[Wer ist 7x?](../wer-ist-7x.md#die-releases-60x), entnommen aus der Release-Übersicht und den Git-Tags.

Fakten aus dieser Tabelle, die ein Planer braucht:

| Release | Warum es für die Planung wichtig ist | Quelle |
|---|---|---|
| 6.0.7 | Das letzte Release für PHP 7.4 | [11. Upgrade](../../installation/11-aktualisieren.md#schritt-3-die-checkliste-jedes-durchlaufenen-zeitraums-lesen) |
| 6.0.8 | Benötigt PHP 8.1 | ebenda |
| 6.0.10 | Das erste Release mit dem Namen Exponential: der Produktname in Templates, Übersetzungen und Overrides | ebenda, [Das Produkt heißt Exponential](../../../eng-us/features/6.0/rebranding-to-exponential.md) |
| 6.0.13 | Sicherheitskorrekturen und Prüfungen der Sortierspalten | ebenda |
| 6.0.14 | MongoDB als Datenbank und die Dateiprüfung beim Upgrade (`share/filelist.md5`) | [Changelog 6.0.14](../../../changelogs/6.0/6.0.14.md) |
| Linie 6.0.15 | Velocity, das Design admin4, das Audit-Protokoll, Exponential Layouts, Exponential Intelligence, das Hilfesystem und vieles mehr | [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md) |

> **Hinweis**
> Die Quellen veröffentlichen keinen festen Release-Kalender. Die 6.0.x-Tags erschienen in Abständen von etwa einem
> bis fünf Monaten. Das README des Profils se7enxweb, zitiert unter [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#die-foundation-und-7x),
> nennt „monatliche Releases“ unter den Arbeiten, die das Sponsoring von 7x finanziert. Planen Sie mit den Tags, die
> es gibt, nicht mit einem Datum.

### Die Regeln, denen jedes Release folgt

Aus [Releases und Versionsnummern](../wer-ist-die-exponential-foundation.md#releases-und-versionsnummern) und
[SECURITY.md](https://github.com/se7enxweb/exponential/blob/main/SECURITY.md):

- **Eine veröffentlichte Version ändert sich nie.** „Ein veröffentlichter Tag wird nie verändert: Eine Korrektur für
  ein Problem in `v6.0.14` oder einem früheren Tag erreicht Sie als nächstes Release, nicht als korrigierte Kopie des
  alten“ (SECURITY.md).
- **Die letzte Stelle zählt weiter.** Nach `0.1.9` kommt `0.1.10`. Versionen werden mit
  `git tag -l 'v*' --sort=version:refname` sortiert, nie nach Namen, denn nach Namen steht `0.1.10` vor `0.1.8`
  ([Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).
- **Jedes Paket ist auf GitHub und Packagist.** Der Kernel ist das Composer-Paket `se7enxweb/exponential`, und jede
  Erweiterung hat ihr eigenes Repository und Paket ([Den Code beziehen](../../installation/03-den-code-beschaffen.md)).
- **Erweiterungen erscheinen eigenständig.** Jede Erweiterung trägt ihre Version in `ezinfo.php` und `extension.xml`,
  und **Setup > Extensions** zeigt sie an ([Erweiterungen](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)).

Was das für Ihren Plan bedeutet:

- Eine Version, die Sie getestet haben, ist genau die Version, die Sie ausrollen. Niemand kann sie Ihnen unter den
  Händen verändern.
- Ein Problem in einem Release wird nie „an Ort und Stelle geflickt“. Warten Sie auf die nächste Version oder fragen
  Sie danach.
- Die eigenen Aufzeichnungen Ihres Projekts darüber, „welche Version wo läuft“, können sich zuverlässig auf
  Versionsnummern stützen.

## Wozu sich das Projekt verpflichtet

Das Projekt veröffentlicht kein einzelnes Dokument mit einer „Support-Richtlinie“. Die Zusagen sind über mehrere
Dateien verteilt und unter [Die Support-Zusage](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung)
zusammengetragen. Kurz gesagt:

| Thema | Zusage | Quelle |
|---|---|---|
| Gepflegte Linie | „Exponential 6.0 ist die gepflegte Linie“ | SECURITY.md |
| Sicherheitskorrekturen | Auf `main` erstellt und mit dem nächsten 6.0.x-Release veröffentlicht | SECURITY.md |
| Ältere Linien | 5.x, 4.x, 3.x und ältere werden nicht unterstützt | SECURITY.md |
| Veröffentlichte 6.0.x-Tags | Behoben durch ein Upgrade auf die neueste 6.0.x | SECURITY.md |
| Sicherheitsmeldungen | Vertraulich; „Eine erste Antwort können Sie innerhalb eines Werktags erwarten“ | SECURITY.md |
| PHP | 8.0 ist die älteste unterstützte Version; 8.1 bis 8.5 werden unterstützt; die CI testet 8.1 bis 8.5 und prüft, dass 8.0 startet | [PHP-Versionen](../wer-ist-die-exponential-foundation.md#php-versionen) |

Zwei Folgen für die Planung:

1. **Planen Sie Budget dafür ein, aktuell zu bleiben.** Eine Site, die auf einem alten 6.0.x-Tag bleibt, erhält ihre
   Sicherheitskorrekturen nur durch den Umzug auf eine neuere 6.0.x. Behandeln Sie „Upgrade auf die neueste 6.0.x“
   als wiederkehrende Aufgabe, nicht als einmaliges Projekt.
2. **Planen Sie PHP zusammen mit dem Code.** Die PHP-Version des Servers und die Exponential-Version bewegen sich
   gemeinsam. Prüfen Sie die PHP-Tabelle, bevor Sie einen Termin zusagen, und fragen Sie Ihren Hosting-Anbieter
   frühzeitig.

## Wo Sie nachlesen, was sich geändert hat

| Dokument | Wofür | Wo |
|---|---|---|
| Changelog je Release | Was eine Version enthält, mit einem Abschnitt „Upgrading“ am Anfang | `doc/changelogs/6.0/` ([6.0.14](../../../changelogs/6.0/6.0.14.md), [6.0.15](../../../changelogs/6.0/6.0.15.md)) |
| Hinweise zu Verhaltensänderungen | Was sich anders verhält und was zu prüfen ist, je Zeitraum | `doc/eng-us/bc/6.0/`, zum Beispiel [Juli und August 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-07-08.md), [16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md), [1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md) |
| Release-Notes der Erweiterungen | Was ein Release einer Erweiterung geändert hat | [Release-Notes der Erweiterungen](../../../changelogs/extensions/README.md) |
| Die Geschichte | Die Geschichte jeder Änderung seit Dezember 2023, nach Monaten | [Die Geschichte](../../../eng-us/history/README.md) |
| Das Upgrade-Kapitel | Die Tabelle „Von ... Lesen ...“, die die Hinweise jedes durchlaufenen Zeitraums auflistet | [11.6 Schritt 3](../../installation/11-aktualisieren.md#schritt-3-die-checkliste-jedes-durchlaufenen-zeitraums-lesen) |

> **Tipp**
> Geben Sie der Person, die die Release-Notes liest, einen festen Platz im Plan, bevor irgendeine Schätzung abgegeben
> wird. Die Hinweise sagen, welche Konten sich womöglich nicht mehr anmelden können, welche Einstellungen ihren
> Standardwert geändert haben und welche Erweiterungen Aufmerksamkeit brauchen. Diese Befunde verändern die Schätzung.

## Entscheiden, wann ein Upgrade erfolgt

Wählen Sie mit dieser Tabelle zwischen „jetzt“, „nächstes Zeitfenster“ und „ein Projekt planen“.

| Situation | Empfehlung | Warum |
|---|---|---|
| Für Ihre Linie gibt es ein Sicherheits-Release | Upgrade im nächstmöglichen Zeitfenster | Korrekturen erreichen Sie nur über das nächste Release (SECURITY.md) |
| Sie betreiben 6.0.x und sind ein oder zwei Releases zurück | Ein Routine-Upgrade planen | Teil C des [Upgrade-Leitfadens](../../anleitungen/aktualisieren.md) behandelt es |
| Sie betreiben 6.0.7 oder älter auf PHP 7.4 | Zuerst den PHP-Umstieg planen | 6.0.8 benötigt PHP 8.1 |
| Sie betreiben 5.4 oder 5.90 | Ein Projekt planen | Teil B, dann Teil C; Datenbank-Update-Dateien für jeden Schritt |
| Sie betreiben 3.10 bis 5.3 | Ein Migrationsprojekt planen | Teil A, dann B, dann C; lesen Sie [14. Migration von der Linie 4.x](../../installation/14-migration-von-4x.md) |
| Sie haben eigene Patches in Kernel-Dateien | Zeit für das Mergen einplanen | Halten Sie sie auf Ihrem eigenen Git-Branch ([Mit Git entwickeln](../mit-git-entwickeln.md#auf-dem-neuesten-stand-bleiben)) |
| Eine benötigte Funktion gibt es nur in der Entwicklungslinie | Entscheiden, ob Sie `main` betreiben oder auf einen Tag warten | `main` bewegt sich jeden Tag; ein Tag ändert sich nie |

Fragen, die Sie beantworten sollten, bevor Sie einen Termin zusagen:

- Welche Version läuft heute, im Code und in der Datenbank? ([11.2](../../installation/11-aktualisieren.md#112-welche-version-betreiben-sie))
- Welche PHP-Version läuft, und welche braucht das Ziel?
- Welche Erweiterungen sind installiert, und welche davon sind Ihre eigenen?
- Wer kann die Site vom Netz nehmen, und wann darf sie offline sein?
- Wo findet die Generalprobe statt: auf einer Kopie der Site auf einem anderen Rechner?

## Der Upgrade-Plan, Phase für Phase

| Phase | Was geschieht | Wer | Erledigt, wenn | Lesen |
|---|---|---|---|---|
| 1. Bestandsaufnahme | Codeversion, Datenbankversion, PHP, Datenbank-Engine, Webserver und Erweiterungen festhalten | Administrator | Die Bestandsaufnahme liegt schriftlich vor | **Setup > Systeminformationen**; [11.2](../../installation/11-aktualisieren.md#112-welche-version-betreiben-sie) |
| 2. Lesen | Jedes Changelog und jeden Hinweis zu Verhaltensänderungen zwischen Ihrer Version und dem Ziel lesen | Entwickler, Administrator | Es gibt eine Liste mit „zu prüfenden Punkten“ | [11.6 Schritt 3](../../installation/11-aktualisieren.md#schritt-3-die-checkliste-jedes-durchlaufenen-zeitraums-lesen) |
| 3. Schätzen | Aus der Liste Aufgaben mit Verantwortlichen machen | Projektleiter | Die Aufgaben haben Verantwortliche und eine Reihenfolge | [Schätzen und Abgrenzen mit Erweiterungen](schaetzen-und-abgrenzen-mit-erweiterungen.md) |
| 4. Vorab-Prüfung | Die Upgrade-Prüfung auf der Live-Site ausführen und den Bericht aufbewahren | Administrator | Der Bericht ist gespeichert | [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) |
| 5. Generalprobe | Eine Kopie der Site aktualisieren, jede Überprüfung ausführen | Entwickler, Administrator | Die Kopie besteht die Prüfungen | [11.6](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand), [11.7](../../installation/11-aktualisieren.md#117-das-upgrade-überprüfen) |
| 6. Abnahme | Redakteure testen die Kopie mit ihren echten Aufgaben | Redakteure, Projektleiter | Die Abnahme ist freigegeben | [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md) |
| 7. Ankündigung | Redakteure und Besucher über das Zeitfenster informieren | Projektleiter | Das Zeitfenster ist angekündigt | [Wartungsmodus](../../../eng-us/features/6.0/maintenance-mode.md) |
| 8. Sicherung | Datenbank und Dateien, zum selben Zeitpunkt | Administrator | Ein Backup existiert **und** wurde einmal anderswo wiederhergestellt | [11.1](../../installation/11-aktualisieren.md#111-bevor-sie-beginnen) |
| 9. Upgrade | Wartungsmodus an, Code, Datenbank-Update, Caches, Neuladen, Wartungsmodus aus | Administrator | Jeder Schritt hat das erwartete Ergebnis ausgegeben | [11.6](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand) |
| 10. Überprüfung | Die Prüfungen aus 11.7, erneut die Upgrade-Prüfung, Smoke-Test der Redakteure | Administrator, Redakteure | Die Prüfungen sind bestanden | [11.7](../../installation/11-aktualisieren.md#117-das-upgrade-überprüfen) |
| 11. Abschluss | Die neuen Versionen festhalten, die Erkenntnisse ablegen | Projektleiter | Das Übergabedokument ist aktualisiert | [Projektvorlagen](projektvorlagen.md) |

### Die eine Regel

**Führen Sie niemals einen Installer über eine bestehende Site aus.** Der Setup-Assistent, der Kickstarter und
`exp:install` legen eine neue Datenbank an. Ein Upgrade ersetzt den Code und aktualisiert die bestehende Datenbank an
Ort und Stelle ([11.1](../../installation/11-aktualisieren.md#111-bevor-sie-beginnen)). Nehmen Sie diese Regel in die
Einweisung aller auf, die während des Zeitfensters am Server arbeiten.

### Das Zeitfenster

Was das Zeitfenster umfasst, in der Reihenfolge von [11.6](../../installation/11-aktualisieren.md#116-von-einer-beliebigen-60x-auf-den-heutigen-stand):

1. Wartungsmodus an: `php bin/php/maintenance.php on --message="Upgrade in progress" --until=30m`. Jede
   Seitenanfrage wird mit der Wartungsseite beantwortet (HTTP 503, nie gecacht).
2. Der neue Code: ein Checkout des Tags, danach `composer install`.
3. Die Klassenzuordnungen und die Caches: `php bin/php/ezpgenerateautoloads.php -e`, danach `php bin/php/ezcache.php --clear-all`.
4. Das Neuladen von PHP und unter Velocity `exp:velocity deploy`, das jeden Schritt in der richtigen Reihenfolge
   ausführt und beim ersten Fehler anhält.
5. Die Datenbank-Update-Datei für die Linie, **einmal, vollständig und in der richtigen Reihenfolge** angewendet, und
   das Skript für die Audit-Tabellen.
6. Die Überprüfungsbefehle aus 11.7.
7. Wartungsmodus aus.

Der [Upgrade-Leitfaden](../../anleitungen/aktualisieren.md) empfiehlt, „für eine kleine Site zwanzig Minuten einzuplanen“, und
stellt fest, dass „der riskante Schritt die Datenbank ist“. Ihr eigenes Zeitfenster hängt von Ihrer Site ab; die
Generalprobe in Phase 5 zeigt Ihnen, wie lange es wirklich dauert. Tragen Sie diese gemessene Zeit in den Plan ein,
keine Schätzung.

## Rollen und Übergaben während eines Upgrades

| Aufgabe | Projektleiter | Administrator | Entwickler | Redakteure |
|---|---|---|---|---|
| Bestandsaufnahme | Fordert sie an | Erstellt sie | Ergänzt eigene Erweiterungen | |
| Release-Notes lesen | Plant es ein | Liest die Hinweise zu Server und Einstellungen | Liest die Hinweise zu Code und Templates | |
| Generalprobe | Reserviert die Kopie und die Zeit | Führt sie durch | Korrigiert eigenen Code | |
| Abnahme | Führt sie durch, gibt frei | | Behebt, was fehlschlägt | Testen ihre täglichen Aufgaben |
| Ankündigung | Schreibt sie | Schaltet den Wartungsmodus | | Erhalten sie |
| Zeitfenster | Behält die Zeit und die Go/No-go-Entscheidung | Führt die Schritte aus | In Bereitschaft | Bearbeiten nichts |
| Überprüfung | Hält das Ergebnis fest | Führt die Prüfungen aus | Prüft eigene Funktionen | Smoke-Test |

Wer sich während des Zeitfensters in der Administration anmeldet, braucht die Richtlinien, die die Seiten verlangen:
Die Upgrade-Prüfung benötigt `setup/setup`, die Systeminformationen benötigen `setup/system_info`
([Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md), [Systeminformationen](../../anleitungen/systeminformationen.md)).

## Upgrade-Checkliste

Übernehmen Sie sie in Ihr Ticket oder Ihren Plan.

```text
Upgrade of <site name> from <current version> to <target version>
Window: <date, start, end>     Owner: <name of role>     Go/no-go by: <role>

Before
[ ] Code version and database version recorded (Setup > System information; 11.2)
[ ] PHP version of the server checked against the target
[ ] Extension list recorded (Setup > Extensions), own extensions marked
[ ] Every changelog and behavior change note between the versions read; list of checks written
[ ] Upgrade check run on the live site, report downloaded
[ ] Rehearsal on a copy done; time measured: <minutes>
[ ] Editors accepted the copy
[ ] Window announced to editors and, if needed, visitors
[ ] Backup of database and files taken, and restored once on another machine

During
[ ] Maintenance mode on, status checked
[ ] New code in place (tag <tag>), composer install finished
[ ] Autoloads regenerated, caches cleared
[ ] PHP reloaded / exp:velocity deploy passed
[ ] Database update applied once, whole, in order; audit tables script run
[ ] Version row in the database shows the target

After
[ ] ezcache --clear-all ends without an error
[ ] exp:checkdbfiles prints nothing
[ ] exp:checkclasses reports no class PHP refuses to load
[ ] exp:checkmanifest --all exit status 0 (or only files changed on purpose)
[ ] Front page loads, admin sign-in works, a test item can be published
[ ] Content cache and, under Velocity, the response cache cleared after the reload
[ ] Maintenance mode off
[ ] New versions recorded in the handover document
```

## Durchgespieltes Beispiel: von 6.0.12 auf die aktuelle Produktlinie

Eine fiktive Site läuft mit 6.0.12 auf PHP 8.3 mit MySQL und Apache mit PHP-FPM. Der Projektleiter möchte sie auf die
aktuelle Produktlinie bringen.

1. **Bestandsaufnahme.** Die Systeminformationen zeigen 6.0.12. PHP 8.3 liegt im unterstützten Bereich, ein
   PHP-Umstieg ist also nicht nötig.
2. **Lesen.** Die Tabelle in [11.6 Schritt 3](../../installation/11-aktualisieren.md#schritt-3-die-checkliste-jedes-durchlaufenen-zeitraums-lesen)
   nennt für diesen Weg: das Changelog 6.0.13 (Sicherheitskorrekturen, Prüfungen der Sortierspalten, mit den Hinweisen
   zur Härtung), das Changelog 6.0.14 (MongoDB, die Dateiprüfung beim Upgrade) und für die Linie 6.0.15 deren Changelog
   sowie die Hinweise zu Verhaltensänderungen von Juli und August, vom 16. bis 30. September (mit den
   Sicherheitsvorgaben) und vom 1. und 2. Oktober 2026, außerdem die Hinweise zu den E-Mail-Einstellungen.
3. **Befunde, die zu Aufgaben werden.** Das Upgrade-Kapitel nennt die Änderungen, auf die man am häufigsten stößt:
   Konten ohne Zeile in `ezuser_setting` können sich nicht anmelden; Override-Blöcke folgen `Priority`;
   Sicherheits-Header, Session-Cookies mit `HttpOnly`/`SameSite` und eine strengere Upload-Prüfung sind standardmäßig
   aktiv; ältere Erweiterungen wurden auf einen einheitlichen Standard gebracht; die Standardwerte des statischen Caches
   haben sich geändert. Jeder Punkt wird zu einer Prüfung mit einem Verantwortlichen: „Konten ohne Einstellungszeile
   finden“ (Administrator), „Prioritäten in override.ini prüfen“ (Entwickler), „prüfen, dass nichts die
   Administration in einen Frame einbettet“ (Entwickler).
4. **Das Audit-Protokoll kommt hinzu.** Ab 6.0.15 ist es standardmäßig eingeschaltet; das Zeitfenster enthält
   `php update/common/scripts/6.0/createaudittables.php`, und der Plan ergänzt eine Aufgabe, die Cronjob-Gruppe
   `frequent` einzuplanen, damit das Audit indiziert wird ([Das Audit-Protokoll](../../../eng-us/features/6.0/audit-trail.md)).
5. **Generalprobe** auf einer Kopie, dann Abnahme durch zwei Redakteure, dann das Zeitfenster.

## Wenn etwas schiefgeht

Das Upgrade-Kapitel enthält eine Tabelle mit Symptomen und Abhilfen ([11.8](../../installation/11-aktualisieren.md#118-wenn-etwas-schiefgeht)).
Legen Sie für den Plan im Voraus fest:

- **Wer über den Rückfall entscheidet**, und bis wann. Ein Rückfall bedeutet „das Backup wiederherstellen“ für die
  Datenbank und „den vorherigen Tag auschecken“ für den Code; er ist nur möglich, wenn das Backup getestet wurde.
- **Eine SQL-Datei, die auf halbem Weg fehlgeschlagen ist**: das Backup wiederherstellen und die Datei erneut von
  Anfang an anwenden (11.8).
- **Redakteure können sich nicht anmelden**: die Änderung an `ezuser_setting` vom August 2026 (11.6, Schritt 3).
- **Ein Fehler in Exponential selbst**: Melden Sie ihn wie unter
  [Wenn etwas schiefgeht](../wenn-etwas-schiefgeht.md#einen-fehler-in-exponential-melden) beschrieben, und denken Sie
  daran, dass die Korrektur als nächste Version kommt.

## Eigene Erweiterungen veröffentlichen

Wenn Ihr Projekt eigene Erweiterungen entwickelt, veröffentlichen Sie sie nach den Regeln des Projekts
([Erweiterungen, „Release it“](../../anleitungen/extensions.md#7-die-extension-veröffentlichen)):

1. Wählen Sie die Nummer anhand dessen, was bereits veröffentlicht ist (`git fetch --tags`, `git tag -l 'v*' --sort=version:refname`).
2. Tragen Sie sie in `ezinfo.php` und `extension.xml` ein (und in `composer.json`, falls diese eine Version enthält).
3. Aktualisieren Sie die Datei `share/filelist.md5` der Erweiterung, falls sie eine mitliefert.
4. Committen Sie und taggen Sie dann diesen Commit. Taggen Sie nie zuerst.
5. Prüfen Sie, dass Packagist die neue Version anzeigt.
6. Ist eine veröffentlichte Version fehlerhaft, verschieben Sie ihren Tag nicht: Veröffentlichen Sie die nächste und
   sagen Sie, was falsch war.

Mehr zur Teamarbeit rund um Tags finden Sie unter [Ein Team mit Git führen](ein-team-mit-git-fuehren.md).

## Verwandte Seiten

- [11. Upgrade](../../installation/11-aktualisieren.md) und der [Upgrade-Leitfaden](../../anleitungen/aktualisieren.md)
- [Die Upgrade-Prüfung](../../anleitungen/upgrade-pruefung.md) und [Prüfung der Dateikonsistenz](../../../eng-us/features/6.0/file-consistency-check.md)
- [Wartungsmodus](../../../eng-us/features/6.0/maintenance-mode.md)
- [Wer ist die Exponential Foundation?](../wer-ist-die-exponential-foundation.md#die-zusage-zur-unterstützung): die Support-Zusage
- [Qualitätsschranken und Abnahme](qualitaetsschranken-und-abnahme.md), [Risikomanagement für Exponential-Projekte](risikomanagement.md)
- [Projektvorlagen](projektvorlagen.md): Übergabedokument und Livegang-Checkliste
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
