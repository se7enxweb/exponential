# Zu expcomponents beitragen: an einem Fork der Bibliotheken des Kernels arbeiten

Diese Anleitung ist für Entwickler, die eine der **expcomponents** korrigieren oder verbessern wollen, den Fork der
Zeta Components durch Exponential (Apache License 2.0), die Bibliotheken, die der Kernel für E-Mail, Archive, WebDAV,
Feeds, die REST-Schicht und mehr verwendet. Sie führt durch eine Änderung vom Klon bis zum Release: den Code holen,
die Tests auf mehreren PHP-Versionen ausführen, die Regeln, die eine Änderung einhalten muss, die Commit-Nachricht,
den Pull-Request, wie Releases und Versionen funktionieren und wie die Forks mit dem Original Schritt halten. Was die
Bibliotheken sind und warum sie geforkt werden: [Exponential Components (expcomponents)](../funktionen/6.0/expcomponents.md).

Zeit: 30 Minuten für die erste Einrichtung; eine kleine Korrektur mit ihrem Test ist ein Nachmittag.

## Inhalt

1. [Bevor Sie anfangen](#bevor-sie-anfangen)
2. [Das richtige Repository finden](#das-richtige-repository-finden)
3. [Klonen und installieren](#klonen-und-installieren)
4. [Die Tests ausführen](#die-tests-ausführen)
5. [Die Tests auf jeder unterstützten PHP-Version ausführen](#die-tests-auf-jeder-unterstützten-php-version-ausführen)
6. [Eine Änderung in einer Installation ausprobieren](#eine-änderung-in-einer-installation-ausprobieren)
7. [Die Regeln, die eine Änderung einhält](#die-regeln-die-eine-änderung-einhält)
8. [Lizenzköpfe und Hinweise](#lizenzköpfe-und-hinweise)
9. [Commit-Nachrichten](#commit-nachrichten)
10. [Das ChangeLog](#das-changelog)
11. [Pull-Requests und Review](#pull-requests-und-review)
12. [Releases und Versionen](#releases-und-versionen)
13. [Abgleich mit Upstream](#abgleich-mit-upstream)
14. [Eine Korrektur an das Original zurückgeben](#eine-korrektur-an-das-original-zurückgeben)
15. [Sicherheitsprobleme](#sicherheitsprobleme)
16. [Verwandte Seiten](#verwandte-seiten)

## Bevor Sie anfangen

Sie brauchen:

- **git** und ein Konto bei **GitHub**;
- **PHP** 7.4 oder neuer auf der Kommandozeile, mit den Erweiterungen, die die Tests des Pakets brauchen (die Tests von
  archive zum Beispiel brauchen `bz2`, `posix` und `zlib`; zum Vergleich von Archiven wird GNU tar 1.34 verwendet);
- **Composer** 2;
- für manche Pakete einen Dienst: Die Mail-Tests können einen SMTP- oder IMAP-Server nutzen, die Datenbank-Tests eine
  Datenbank; der Ordner `tests/` des Pakets und sein CI-Workflow sagen, welchen.

Lesen Sie einmal die `README` oder den Ordner `docs/` des Pakets; dort ist die API beschrieben, die Sie ändern.

## Das richtige Repository finden

Jede Bibliothek ist ein Repository: `https://github.com/se7enxweb/expcomponents-<name>`, wobei `<name>` der Paketname
ohne `zetacomponents/` ist: `mail`, `archive`, `webdav`, `mvc-tools`, `console-tools` ... Die Tabelle aller Pakete steht
auf der [Funktionsseite](../funktionen/6.0/expcomponents.md#die-pakete); welche Dateien des Kernels welches Paket
verwenden, in der [Paketreferenz](../spezifikationen/6.0/expcomponents-pakete.md).

Nennt eine Fehlermeldung eine Klasse, verrät das Klassenpräfix das Paket: `ezcMail...` ist mail, `ezcWebdav...` ist
webdav, `ezcMvc...` ist mvc-tools, `ezcConsole...` ist console-tools, `ezcBase...` ist base. In einer Installation gibt
`composer show --path se7enxweb/expcomponents-mail` aus, wo die installierte Kopie liegt.

## Klonen und installieren

Forken Sie das Repository auf GitHub (Schaltfläche **Fork**), dann:

```bash
git clone https://github.com/<sie>/expcomponents-mail.git
cd expcomponents-mail
git remote add origin-se7enxweb https://github.com/se7enxweb/expcomponents-mail.git
git remote add upstream https://github.com/zetacomponents/Mail.git
composer update --no-interaction --prefer-dist
```

`composer update` (nicht `install`: die Bibliotheken liefern keine Lock-Datei) installiert PHPUnit und
`zetacomponents/unit-test`, die Testhilfen, die alle Komponenten teilen, in das eigene `vendor/` des Klons. Der
Standard-Branch ist `master`; legen Sie für Ihre Änderung einen Branch an:

```bash
git switch -c fix-smtp-timeout
```

## Die Tests ausführen

```bash
vendor/bin/phpunit                         # die ganze Suite, wie phpunit.xml.dist sie festlegt
vendor/bin/phpunit --filter SmtpTransport  # eine Testklasse oder -methode
```

Die Suiten sind so eingestellt, dass eine Deprecation den Build scheitern lässt (`convertDeprecationsToExceptions`
oder die PHPUnit-Einstellungen des Pakets), mit `error_reporting=-1`. Ein Test, der eine Deprecation ausgibt, ist ein
fehlschlagender Test, kein Rauschen.

Der CI-Workflow des Pakets (`.github/workflows/tests.yml`, wo der Fork einen hat) prüft außerdem jede Quelldatei auf
Syntax und scheitert an Deprecations zur Übersetzungszeit. Führen Sie dieselbe Prüfung aus, bevor Sie pushen:

```bash
for f in $(find src tests -name '*.php' -not -path 'tests/*/testfiles/*' -not -path 'tests/data/*'); do
    php -d error_reporting=-1 -l "$f" | grep -vi '^No syntax errors' ; done
```

Sie gibt nichts aus, wenn jede Datei sauber ist.

## Die Tests auf jeder unterstützten PHP-Version ausführen

Der Kernel unterstützt PHP 8.0 bis 8.5 und läuft auf 7.4; seine Bibliotheken müssen das auch. Das CI eines Forks führt
die Suite auf jeder Version (7.4, 8.0, 8.1, 8.2, 8.3, 8.4 und 8.5) bei jedem Push und Pull-Request aus. Lokal führen
Sie mindestens die älteste und die neueste Version aus, die Sie haben:

```bash
php7.4 vendor/bin/phpunit
php8.5 vendor/bin/phpunit
```

Die Namen der Programme hängen von Ihrem System ab (`/opt/plesk/php/8.5/bin/php`, `/usr/bin/php8.5`, ein Container).
Fehlt lokal eine Version, pushen Sie Ihren Branch in Ihren eigenen Fork: Dort läuft der Workflow ebenfalls.

Was der PHP-Bereich für den Code bedeutet:

- keine Syntax, die neuer als PHP 7.4 ist, in `src/` (kein `match`, keine Constructor Property Promotion, keine Enums,
  kein `readonly`);
- kein Verhalten, das auf dem neuesten PHP veraltet ist: ausdrücklich nullbare Parameter (`?string $x = null`),
  `#[\ReturnTypeWillChange]`, wo die Methode einer internen Schnittstelle einen Rückgabetyp bekommt, kein `null` an
  String-Funktionen;
- Union Types und andere Funktionen von PHP 8 bleiben draußen, bis der Kernel nicht mehr auf 7.4 läuft.

## Eine Änderung in einer Installation ausprobieren

Um eine Änderung vor ihrem Release an einer echten Site zu prüfen, richten Sie eine Entwicklungsinstallation mit einem
VCS- oder Pfad-Repository in ihrer `composer.json` auf Ihren Branch aus:

```json
"repositories": [
    { "type": "vcs", "url": "https://github.com/<sie>/expcomponents-mail" }
]
```

```bash
composer require --no-update se7enxweb/expcomponents-mail:dev-fix-smtp-timeout
composer update se7enxweb/expcomponents-mail
```

Starten Sie dann die PHP-Worker neu (PHP-FPM oder `./console exp:velocity restart`) und nutzen Sie die Funktion. Nehmen
Sie die beiden Änderungen in der `composer.json` dieser Installation zurück, wenn Sie fertig sind; liefern Sie nie eine
Anforderung `dev-` aus.

## Die Regeln, die eine Änderung einhält

1. **Klassennamen bleiben.** Keine Klasse, keine Schnittstelle und keine Konstante wird in einer 1.x-Linie umbenannt
   oder entfernt. Jeder Aufrufer im Kernel, in Erweiterungen und in Sites verwendet die `ezc`-Namen.
2. **Öffentliche APIs bleiben kompatibel.** Eine öffentliche oder geschützte Methode behält ihren Namen, ihre Parameter
   und deren Bedeutung. Ein neuer optionaler Parameter am Ende, eine neue Methode oder eine neue Option ist in Ordnung;
   ein geänderter Standardwert, den Aufrufer sehen können, braucht einen Hinweis im `ChangeLog` und in den
   Release-Notes.
3. **In bestehende Klassen kommen keine Namensräume.** Die Bibliotheken verwenden den globalen Namensraum; dabei bleibt
   es.
4. **Ein Anliegen je Änderung.** Eine Deprecation-Korrektur, eine Fehlerkorrektur und ein Refactoring sind getrennte
   Commits.
5. **Jede Korrektur hat einen Test**, der ohne sie scheitert, im Ordner `tests/` des Pakets, benannt, wie das Paket
   seine Tests benennt (`*_test.php`).
6. **Keine neue Laufzeitabhängigkeit** ohne Diskussion im Pull-Request. Eine Komponente soll allein installierbar
   bleiben.
7. **Die Forks verlangen einander mit ihren Fork-Namen** (`se7enxweb/expcomponents-base ~1.8`), nicht mit den
   Ursprungsnamen. Eine neue Anforderung an eine andere Komponente nennt deren Fork.
8. **`replace` in `composer.json` bleibt**, mit `"zetacomponents/<name>": "self.version"`.

## Lizenzköpfe und Hinweise

Die Bibliotheken stehen unter der Apache License 2.0, und Apache 2.0 stellt Bedingungen für die Weitergabe geänderten
Codes. Halten Sie sie ein, dann bleibt der Fork ein Fork, den jeder verwenden darf:

- **Entfernen oder ändern Sie nie** den Lizenzkopf einer Quelldatei, die Hinweiszeilen am Anfang von `LICENSE.txt`
  („Zeta Components ...“, „Apache Zeta Components ...“, „Originally developed by eZ Systems as eZ Components ...“),
  `CREDITS` oder die Datei `NOTICE`, die jeder Fork mitliefert (die Hinweise des Originals und der Name des Forks).
- **Änderungen werden festgehalten** (Apache 2.0, Abschnitt 4(b), verlangt, dass geänderte Dateien gut sichtbare
  Hinweise tragen, dass sie geändert wurden): Jede Änderung ist ein Commit mit eigener Nachricht und ein Eintrag im
  `ChangeLog` des Pakets (unten), und die Release-Notes führen sie auf. Fragen Sie die Verantwortlichen im
  Pull-Request, wenn Sie meinen, dass eine Datei mehr braucht.
- **Neue Dateien** tragen denselben Apache-2.0-Kopf wie die Dateien um sie herum, mit dem Projekt Exponential als
  Autor der neuen Datei.
- **Code von anderswo** kommt nur hinein, wenn seine Lizenz mit Apache 2.0 vereinbar ist (MIT, BSD, Apache 2.0);
  GPL-Code kann nicht in diese Bibliotheken kopiert werden. Sagen Sie im Pull-Request, woher er stammt.

Der Kernel, der die Bibliotheken verwendet, steht unter GPL Version 2 oder später; wie sich das mit Apache 2.0
verbindet, erklärt die [Funktionsseite](../funktionen/6.0/expcomponents.md#die-lizenz). Keine Rechtsberatung.

## Commit-Nachrichten

Die Forks verwenden dasselbe Nachrichtenformat wie Exponential: ein Präfix, ein Satz, der sagt, was sich geändert hat,
eine Leerzeile und ein Text, der sagt, warum. Die Nachrichten werden auf Englisch geschrieben, wie im Kernel.

```text
Updated: The SMTP transport gives up after the configured timeout when the server stops answering

A server that accepted the connection and then sent nothing kept the
transport waiting forever, because the read loop ignored the timeout of
ezcMailSmtpTransportOptions. It now checks the stream's timed_out flag
after each read and throws ezcMailTransportException.

- Test: a server stub that answers the greeting and then stays silent.
```

| Präfix | Für |
|---|---|
| `Added:` | eine neue Klasse, Methode, Option oder Testhilfe |
| `Updated:` | eine Korrektur oder eine Änderung bestehenden Verhaltens |
| `Removed:` | etwas Gelöschtes (selten in einer 1.x-Linie) |
| `Renamed:` | etwas Umbenanntes (der Paketname, eine Datei; nie eine öffentliche Klasse) |

Schreiben Sie die Nachricht für einen Leser, der nur das Repository hat. Keine Werkzeugnamen, keine Links auf private
Notizen.

## Das ChangeLog

Jedes Paket hat eine Datei `ChangeLog` in seinem Wurzelverzeichnis, das neueste Release zuerst. Fügen Sie Ihren
Eintrag unter einer Überschrift für das nächste Release hinzu (das Datum setzen die Verantwortlichen beim Release):

```text
1.10.3 - not released yet
^^^^^^^^^^^^^^^^^^^^^^^^^

- Fixed: The SMTP transport gives up after the configured timeout when the
  server stops answering.
```

Die Einträge verwenden `Fixed:`, `Added:`, `Changed:`, `Renamed:` und `Removed:`, wie die bestehenden Einträge der
Datei.

## Pull-Requests und Review

1. Pushen Sie Ihren Branch in Ihren Fork und eröffnen Sie einen Pull-Request gegen `master` von
   `se7enxweb/expcomponents-<name>`.
2. Sagen Sie, was die Änderung tut, warum, wie Sie sie getestet haben (welche PHP-Versionen) und ob ein Aufrufer sie
   bemerken kann.
3. Das CI muss auf jeder PHP-Version der Matrix grün sein.
4. Ein Verantwortlicher prüft sie. Änderungen, nach denen der Kernel ein neueres Release der Bibliothek braucht,
   folgt eine Änderung der Bedingung in der `composer.json` des Kernels, im Repository des Kernels.

## Releases und Versionen

Releases erstellen die Verantwortlichen von 7x und der Exponential Foundation; Mitwirkende setzen keine Tags. Was jeder
Mitwirkende darüber wissen sollte:

- **Ein veröffentlichtes Tag ist dauerhaft.** Ist ein Tag einmal gepusht, wird es nie verschoben, gelöscht oder neu
  angelegt, und ein GitHub-Release wird nie bearbeitet, um seine Dateien zu ersetzen: Packagist und jede Installation,
  die die Version aufgelöst hat, behalten den alten Inhalt. Ein Fehler in einem Release wird mit dem nächsten Release
  korrigiert, dessen Notes sagen, was falsch war.
- **Die nächste Nummer wird aus dem gewählt, was veröffentlicht ist**, nicht aus dem Gedächtnis: Die Verantwortlichen
  listen zuerst die bestehenden Tags und Releases auf:

  ```bash
  git fetch --tags
  git tag -l --sort=version:refname | tail -5    # als Versionen sortiert, nicht als Text
  gh release list --limit 10
  ```

  Eine reine Textsortierung stellt `1.5.10` vor `1.5.9`; sortieren Sie immer als Versionen.
- **Nur die letzte Stelle zählt hoch.** Auf `1.5.9` folgt `1.5.10`, nie `1.6.0`. Eine höhere Stelle zu ändern ist eine
  Entscheidung darüber, was das Release bedeutet, keine Folge davon, dass die Neun erreicht ist.
- **Tags haben kein Präfix `v`** (`1.10.2`), wie im Original.
- **Das erste Release des Forks setzt die Linie des Originals fort:** Auf `1.10.1` im Original folgt `1.10.2` im Fork.
- **Vor einem Tag:** Der Arbeitsbaum ist sauber, die Suite besteht auf jeder PHP-Version der Matrix, das `ChangeLog`
  enthält das Release mit seinem Datum, und `composer.json` ist gültig (`composer validate`).
- **Release-Notes** auf GitHub führen auf, was sich geändert hat, mit den Worten des `ChangeLog`.
- Der Kernel verlangt jeden Fork in der Tilde-Form (`~1.10.2`), daher erreicht ein Patch-Release die Installationen mit
  ihrem nächsten `composer update`, und eine neue Minor-Linie braucht eine Änderung im Kernel.

## Abgleich mit Upstream

Das Projekt Zeta Components veröffentlicht einige Pakete noch. Seine Korrekturen werden in die Forks übernommen:

```bash
git fetch upstream
git log --oneline master..upstream/master     # was das Original hat und der Fork nicht
git switch -c sync-upstream-2026-11
git merge upstream/master
vendor/bin/phpunit
```

- Lösen Sie Konflikte so, dass beide Korrekturen erhalten bleiben; der Umbenennungs-Commit des Forks (Paketname,
  `replace`, Homepage) bleibt.
- Nennen Sie das übernommene Release oder den Commit des Originals in der Nachricht des Merges und im `ChangeLog`.
- Führen Sie die Suite auf der ganzen PHP-Matrix aus; eine Änderung des Originals zielt vielleicht nur auf neuere
  PHP-Versionen.
- Eröffnen Sie den Pull-Request wie bei jeder anderen Änderung.

Vor jedem Release eines Forks prüfen die Verantwortlichen, wie weit der Fork hinter dem Original liegt
(`git rev-list --left-right --count master...upstream/master` gibt die Anzahl „voraus hinterher“ aus), und übernehmen,
was fehlt.

## Eine Korrektur an das Original zurückgeben

Eine Korrektur, die auch außerhalb von Exponential nützt, wird zusätzlich dem Original angeboten: Eröffnen Sie einen
Pull-Request gegen `https://github.com/zetacomponents/<Name>` von einem Branch auf Basis von `master` des Originals,
ohne den Umbenennungs-Commit des Forks. Der Fork wartet nicht darauf, dass das Original sie annimmt.

## Sicherheitsprobleme

Eröffnen Sie für eine Schwachstelle kein öffentliches Issue und keinen öffentlichen Pull-Request. Melden Sie sie
vertraulich per E-Mail an security@se7enx.com, wie die `SECURITY.md` des Kernels sagt. Die Korrektur wird vertraulich
vorbereitet und mit ihren Notes veröffentlicht.

## Verwandte Seiten

- [Exponential Components (expcomponents)](../funktionen/6.0/expcomponents.md)
- [Spezifikation: die expcomponents-Pakete](../spezifikationen/6.0/expcomponents-pakete.md)
- [Upgrade-Hinweise: der Wechsel zu expcomponents](../bc/6.0/expcomponents.md)
- [Code beitragen](../hilfe/gemeinschaft/mitwirken/code-beitragen.md)
- [Das CLA erklärt](../hilfe/gemeinschaft/mitwirken/das-cla-erklaert.md)
