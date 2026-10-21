---
title: Kompatibilität, Releases und Deployment
audience: developer, admin
category: standards
order: 220
summary: Die Richtlinie zur Abwärtskompatibilität von Exponential 6.0.x, wie Versionen und Releases nummeriert und veröffentlicht werden, und die Standardreihenfolge der Schritte beim Deployment einer Änderung oder einer Aktualisierung.
keywords: Abwärtskompatibilität, BC, inkompatible Änderung, Veraltung, Hinweise zur Aktualisierung, doc/eng-us/bc, Changelog, Release, Version, Tag, semantische Versionierung, ezinfo.php, extension.xml, filelist.md5, Manifest, Deployment, Autoloads, ezcache, PHP-FPM, Velocity, exp:velocity deploy, dbupdate, Aktualisierungsprüfung, backward compatibility, breaking change, deprecation, upgrade notes, semantic versioning, deploy, upgrade check
type: reference
---
# Kompatibilität, Releases und Deployment

Diese Seite richtet sich an Entwickler, die Exponential 6.0.x oder eine Extension ändern, und an Administratoren, die
es aktualisieren und ausrollen. Sie beantwortet drei Fragen: was als inkompatible Änderung gilt und was eine solche
begleiten muss, wie Versionen nummeriert und veröffentlicht werden und in welcher Reihenfolge eine Änderung ausgerollt
wird, damit kein Server alten Code weiter ausführt. Jede Regel sagt **muss** oder **sollte**, wie das Repository sie
umsetzt und wo sie noch nicht erfüllt ist.

## Richtlinie zur Abwärtskompatibilität

### Was als Bruch gilt

Eine Änderung bricht die Kompatibilität, wenn eine Installation oder Extension, die vorher funktionierte, eine Aktion
braucht, um weiter zu funktionieren, oder sich ungefragt anders verhält. Insbesondere:

| Art | Beispiele |
|---|---|
| Einstellungen | eine Einstellung entfernt oder umbenannt, ihre Bedeutung oder ihr Standard geändert, ein Abschnitt in eine andere Datei verschoben |
| Templates | ein Template entfernt oder verschoben, eine Variable, die einem Template nicht mehr übergeben wird, ein geänderter `Source`-Pfad einer Überschreibung |
| PHP-API | eine öffentliche Klasse, Methode oder Funktion entfernt oder umbenannt, eine Signatur oder ein Rückgabetyp geändert, eine Klasse ohne den alten Namen in einen Namensraum verschoben |
| Datenbank | eine Tabelle oder Spalte hinzugefügt, entfernt oder geändert (eine Aktualisierung braucht SQL) |
| Kommandozeile | ein Befehl, eine Option, ein Ausgabeformat oder ein Exit-Code geändert |
| Verhalten | ein Standard geändert (Sicherheitsheader, Cookies, Zugriffsprüfungen), eine Berechtigung, die jetzt verlangt wird, wo vorher keine nötig war |
| Dateien | eine Datei, die Sites kopieren oder einbinden (Ansichten, Cronjob-Teile, Skripte), in ihrer Form geändert |

### Die Regeln

- **Jeder Bruch muss Hinweise zur Aktualisierung in `doc/eng-us/bc/<series>/` haben.** Das Verzeichnis existiert pro
  Reihe, von `doc/eng-us/bc/4.1/` bis `doc/eng-us/bc/6.0/` (84 Seiten in `doc/eng-us/bc/6.0/` zum Zeitpunkt des
  Schreibens). Eine Seite beginnt mit einer Tabelle **Kurz gesagt** mit den Zeilen *Was sich geändert hat*, *Wer
  betroffen ist*, *Wie man es prüft* und *Wie man es behebt*; Beispiele:
  [Befehle, Cronjob-Teile und Modulansichten als Klassen](../../../eng-us/bc/6.0/cli_cronjob_view_abstractions.md),
  [exp:ini](../../../eng-us/bc/6.0/console-exp-ini.md). Änderungen, die viele Bereiche betreffen, werden pro Zeitraum mit einer
  Deployment-Reihenfolge und einer Checklisten-Tabelle gesammelt, zum Beispiel
  [Verhaltensänderungen vom 1. und 2. Oktober 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-10.md).
- **Neues Verhalten sollte optional sein, Fehlerbehebungen müssen es nicht.** Eine neue Funktion kommt ausgeschaltet
  oder mit einem Standard, der das alte Ergebnis beibehält; Beispiele sind die klassenbasierten Ansichten ("der alte Weg
  funktioniert weiterhin", Ansichten als einfache Dateien laufen weiter) und die Optionen der Velocity-Engine in den
  [Hinweisen zur Aktualisierung der Velocity-Engine](../../../eng-us/bc/6.0/velocity-engine-upgrade-notes.md) ("Nothing changes
  by itself. New, opt-in: ...").
- **Neue Einstellungen müssen einen sicheren Standard haben** in der mitgelieferten `settings/<file>.ini`, dort einen
  Kommentar, einen Abschnitt auf der Funktionsseite in `doc/eng-us/features/6.0/` und eine Zeile im Changelog.
- **Bezeichner werden nicht umbenannt.** Bestehende `eZ*`- und `ezp*`-Klassen, Tabellen, Einstellungen und Pfade
  bleiben (siehe [Regeln für Schichten und Benennung](schichten-und-benennung.md)); die Umbenennung des Produkts 2026 hat
  nur Text geändert ([Das Produkt heißt Exponential](../../funktionen/6.0/umbenennung-in-exponential.md)).
- **Verschobener Code behält seinen Einstiegspunkt.** Als der Code eines Skripts, einer Ansicht oder eines Cronjob-Teils
  in 6.0.15 in eine Klasse wanderte, behielt jede Datei ihren Pfad und wurde zu einem einzeiligen Aufruf, sodass sich
  Includes, Cron-Einträge und die Ausgabe von `--help` nicht änderten.

### Veraltung

- Eine ersetzte API bleibt verfügbar und wird mit dem Ersatz als `@deprecated` markiert (54 `@deprecated`-Tags in
  `kernel/` und `lib/`, zum Beispiel `eZContentObject::contentObjectListRelatingThis()`, ersetzt durch `reverseRelatedObjectList()`, und `eZUser::isLoggedIn()`, das weiterhin funktioniert und über `eZDebug::writeStrict()` einen Veraltungshinweis im Strict-Modus schreibt).
- Ein umbenannter Befehl behält seinen alten Namen als veralteten Alias, der weiterhin funktioniert
  ([Forks von Plattformpaketen und Umbenennungen von Befehlen](../../../eng-us/bc/6.0/platform-package-forks-and-command-renames.md)).
- Eine Entfernung wird im Abschnitt **Removed** des Changelogs und auf einer Seite in `doc/eng-us/bc/` angekündigt.

### Bekannte Ausnahmen

- **Sicherheitsstandards ohne Opt-in geändert.** Im September und Oktober 2026 wurden Sicherheitsheader,
  Session-Cookies mit `HttpOnly` und `SameSite`, eine strengere Upload-Prüfung und neue Zugriffsanforderungen für
  einige entfernte Dienste standardmäßig eingeschaltet
  ([Sicherheitsstandards](../../spezifikationen/6.0/sicherheitsvorgaben-2026-09.md),
  [Verhaltensänderungen vom 16. bis 30. September 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-09b.md)).
  Sicherheitskorrekturen werden wie Fehlerbehebungen behandelt: standardmäßig eingeschaltet, dokumentiert mit dem Weg,
  sie bewusst rückgängig zu machen.
- **Konten ohne eine Zeile in `ezuser_setting`** können sich seit August 2026 nicht anmelden, und Überschreibungsblöcke
  folgen seit Juli 2026 `Priority`
  ([Verhaltensänderungen von Juli und August 2026](../../../eng-us/bc/6.0/behaviour-changes-2026-07-08.md)).
- Aus eZ Publish 4 übernommene `@deprecated`-APIs haben kein Entfernungsdatum; für die Reihe 6.0 ist keine Entfernung
  angekündigt.

## Releases und Versionierung

### Versionsnummern

- Die Kernel-Version steht in `lib/version.php` (`VERSION_MAJOR = 6`, `VERSION_MINOR = 0`, `VERSION_RELEASE = 15`)
  und in der Datenbankzeile `ezsite_data` `ezpublish-version`, die das Update-SQL schreibt (zum Beispiel
  `6.0.15stable`).
- Releases der Reihe 6.0 werden mit `v6.0.<n>` getaggt. Der neueste Tag im Repository ist `v6.0.14`; die Reihe 6.0.15
  ist in Entwicklung und hat noch keinen Tag ([Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)).
- **Nur die letzte Stelle zählt weiter.** Nach `x.y.9` kommt `x.y.10`, nie `x.(y+1).0`; eine höhere Stelle zu
  verändern ist eine Entscheidung darüber, was das Release bedeutet. Tags werden mit
  `git tag -l 'v*' --sort=version:refname` (oder `sort -V`) sortiert, weil eine einfache Namenssortierung `0.1.10` vor
  `0.1.8` einordnet. Die Regel ist für Extensions in [Extensions, Abschnitt 7](../../anleitungen/extensions.md) und für die
  Velocity-Engine in [ihrem Changelog](../../../changelogs/extensions/exponential-velocity.md) festgehalten.

### Tags sind dauerhaft

**Ein veröffentlichter Tag darf nie verschoben, gelöscht oder neu erstellt werden, und ein veröffentlichtes Release
darf nie ersetzt werden.** Die Pakete werden über Composer installiert, das eine Version einmal erfasst; ein geänderter
Tag würde zwei verschiedenen Paketen dieselbe Nummer geben. Ein fehlerhaftes Release wird durch das nächste korrigiert,
dessen Hinweise sagen, was falsch war. Vor dem Taggen: Der Arbeitsbaum ist sauber, die Tests laufen durch, die
Versionsfelder sind erhöht, das Manifest stimmt.

### Changelogs

- Eine Seite pro Release in `doc/changelogs/<series>/<version>.md` (`doc/changelogs/6.0/6.0.0.md` bis `6.0.15.md`),
  Extensions in `doc/changelogs/extensions/`. Einträge sind gruppiert als **Added**, **Updated**, **Removed** und
  **Renamed**; 6.0.15 zusätzlich nach Zeitraum. Jede Seite beginnt mit **Upgrading** und verlinkt die Seiten in
  `doc/eng-us/bc/`.
- Commit-Betreffzeilen verwenden dieselben Präfixe (`Added:`, `Updated:`, `Fixed:`, `Removed:`, `Renamed:`); der
  CI-Job *Commit messages* von `.github/workflows/quality.yml` prüft sie mit `.githooks/check-commit-range` (für externe
  Mitwirkende nur eine Warnung).
- Die vollständige Historie pro Commit wird in `doc/eng-us/history/ledger/` geführt.

### Versionen von Extensions

Ein Release einer Extension **muss** seine Version ohne das `v` in `ezinfo.php` (`'Version'`) und `extension.xml`
(`<metadata><version>`) schreiben, und in `composer.json`, falls diese eine `version` hat, in dem Commit, auf den der
Tag zeigt; nie zuerst taggen und danach erhöhen. Prüfen Sie mit `php -l ezinfo.php` und
`xmllint --noout extension.xml`. Extensions, die im Kernel-Repository ausgeliefert werden, tragen die Kernel-Version.
Referenz: [Metadaten von Extensions](../../spezifikationen/6.0/extension-metadaten.md).

### Das Datei-Manifest

`share/filelist.md5` listet die Prüfsumme jeder ausgelieferten Datei auf (etwa 9.500 Zeilen). Die Aktualisierungsprüfung
im Admin und `php bin/php/checkmanifest.php --all` vergleichen eine Installation damit. Es **muss** zu den Dateien jedes
Commits auf dem Hauptbranch passen: Der CI-Job *File manifest (share/filelist.md5)* in `.github/workflows/quality.yml`
schlägt auf `main` fehl (und warnt bei einem Pull Request), wenn es nicht passt, und
`php bin/php/checkmanifest.php --fix` (oder `make manifest-fix`) aktualisiert es. Eine Extension mit einer eigenen
`share/filelist.md5` aktualisiert sie im selben Release. Siehe
[Prüfung der Dateikonsistenz](../../funktionen/6.0/dateikonsistenzpruefung.md).

### Bekannte Lücken

- Die Versionskonstanten in `lib/version.php` sagen bereits `15` mit `VERSION_STATE = 'stable'`, obwohl 6.0.15 nicht
  veröffentlicht ist.
- `doc/changelogs/README.md` verweist noch auf eine alte Release-Seite des Upstreams statt auf `doc/changelogs/6.0/`.

## Deployment-Standard

Die beiden Leitfäden sind das Verfahren; dieser Abschnitt ist die Zusammenfassung und die Begründung der Reihenfolge.
[Deployment](../../anleitungen/bereitstellung.md) behandelt das Ausliefern einer Site mit Apache und PHP-FPM, Velocity oder
FrankenPHP und das Ausrollen einer Änderung; [Aktualisierung](../../anleitungen/aktualisieren.md) behandelt den Umstieg von 4.x,
5.x oder einer früheren 6.0.x.

### Eine Code-Änderung ausrollen

Nach einer Änderung an einer PHP-Klasse, einem Operator oder einer INI-Datei **müssen** die Schritte in dieser
Reihenfolge laufen:

1. **Die Klassen-Maps neu erzeugen.** `php bin/php/ezpgenerateautoloads.php -e` (Extensions); mit `-k` für den Kernel,
   wenn eine Kernel-Klasse hinzugefügt oder umbenannt wurde. `-k` durchläuft die gesamte Installation, daher werden
   Arbeitskopien darin über `.autoloadignore` oder `--exclude=<dir>` ausgeschlossen.
2. **Den Einstellungs-Cache leeren.** `php bin/php/ezcache.php --clear-tag=ini --allow-root-user` (der Tag leert
   sowohl den Cache `ini` als auch `global_ini`).
3. **Template-Caches leeren**, wenn sich Templates oder Template-Pfade geändert haben: `template`,
   `template-override`, `translation`, `design_base` (`--clear-id=...`).
4. **Den neuen Code in den Webserver laden.** Laden Sie den PHP-FPM-Dienst neu, der die Site tatsächlich ausliefert
   (auf einem Server mit mehreren PHP-Versionen ist das nicht immer der mit dem Namen `php-fpm`;
   `velocity.ini [DeploySettings] PhpFpmService`, ausgeliefert mit `auto`), und starten Sie Velocity neu
   (`./console exp:velocity restart`), dessen Worker die beim Aufwärmen geladenen Klassen behalten.
5. **Die Seiten-Caches zuletzt leeren**: den Cache der Inhaltsansichten (`--clear-id=content`), den HTTP-Cache und den
   Antwort-Cache von Velocity (`./console exp:velocity cache clear`). Werden sie vor Schritt 4 geleert, kann alter Code
   eine Seite erneut zwischenspeichern.

`./console exp:velocity deploy` führt all das in der richtigen Reihenfolge aus (vierzehn Schritte, PASS, FAIL oder SKIP
pro Schritt, Exit-Status 1 beim ersten Fehler); `--dry-run` zeigt die Schritte, `--kernel` fügt die Klassen-Map des
Kernels hinzu, `--packer` leert gepackte Skripte und Styles. Einzelheiten:
[Eine PHP-Änderung ausrollen](../../../eng-us/bc/6.0/velocity-engines.md#deploying-a-php-change-expvelocity-deploy).
Eine Änderung an einem Template oder Stylesheet braucht nur die Template-Caches und den Antwort-Cache von Velocity,
keinen Neustart.

### Eine Installation aktualisieren

1. **Sichern** Sie Dateien und Datenbank.
2. **Bringen Sie den neuen Code an seinen Platz**, erzeugen Sie die Klassen-Maps neu und leeren Sie alle Caches
   (`php bin/php/ezcache.php --clear-all --allow-root-user`), laden Sie dann PHP-FPM neu und starten Sie Velocity neu.
3. **Wenden Sie das Datenbank-Update einmal, vollständig und in der richtigen Reihenfolge an**:
   `update/database/<driver>/6.0/dbupdate-6.0.0-6.0.15.sql` (Dateien für MySQL, PostgreSQL und SQLite existieren; ab
   5.4 zuerst `dbupdate-5.4.0-6.0.0.sql`), dann die Skripte in `update/common/scripts/6.0/`, die der Leitfaden nennt
   (`createaudittables.php`, `movetrashrecords.php`; das Verzeichnis liefert auch `createapikeytable.php` mit).
4. **Lesen Sie die Checkliste jedes durchlaufenen Zeitraums** in `doc/eng-us/bc/6.0/` und die Changelogs.
5. **Prüfen Sie die Extensions**: `./console exp:checkclasses` meldet Klassen, die PHP nicht laden will.
6. **Führen Sie die Aktualisierungsprüfung aus**: Setup > System Upgrade, *Überprüfe Dateikonsistenz* gegen
   `share/filelist.md5` und *Überprüfe Datenbankkonsistenz* gegen die Schemadateien; beide lesen nur. Aus einer Shell:
   `php bin/php/checkmanifest.php --all --extensions` ([Die Aktualisierungsprüfung](../../anleitungen/upgrade-pruefung.md)).

### Regeln

- Ein Deployment **muss** jede PHP-Laufzeit erreichen, die die Site ausliefert; nur Caches zu leeren lässt PHP-FPM und
  Velocity auf den alten Klassen.
- Datenbankänderungen **müssen** als SQL für jeden unterstützten Treiber in `update/database/<driver>/6.0/` zusammen mit
  dem Schema in `share/db_schema.dba` ausgeliefert werden und **müssen** sagen, wie auf einer Datenbank vorzugehen ist,
  die einige davon bereits hat (für die Datei von 6.0.15: Eine Anweisung für eine bereits vorhandene Spalte schlägt mit
  deren Namen fehl, und nur diese Anweisung wird übersprungen).
- Eine neue Klasse, auf die eine ausgelieferte INI-Datei verweist, **sollte** im Kernel mit `class_exists()` abgesichert
  werden, weil ein Velocity-Worker, der vor dem Deployment gestartet wurde, sie nicht kennt.

### Bekannte Lücken

- Die Datenbank-Updatedateien werden von Hand angewendet; es gibt keinen Migrations-Runner, der festhält, welche
  Anweisungen gelaufen sind.
- `exp:velocity deploy` deckt PHP-FPM und Velocity auf einer Maschine ab; mehrere Webserver werden einer nach dem
  anderen ausgerollt.

## Verwandte Seiten

- [Kernel-Architektur](kernel-architektur.md), [Regeln für Schichten und Benennung](schichten-und-benennung.md)
- [Deployment](../../anleitungen/bereitstellung.md), [Aktualisierung](../../anleitungen/aktualisieren.md), [Die Aktualisierungsprüfung](../../anleitungen/upgrade-pruefung.md)
- [Velocity-Engines](../../../eng-us/bc/6.0/velocity-engines.md), [Caches](../../anleitungen/caches.md)
