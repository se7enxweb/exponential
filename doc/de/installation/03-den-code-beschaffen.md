# 3. Den Code beschaffen

Dieses Kapitel bringt den Code von Exponential auf Ihren Rechner. Es gibt drei Wege: ein `git clone` gefolgt von
`composer install`, bei dem jedes se7enxweb-Paket als Git-Klon mit seiner vollständigen Historie ankommt (**der
empfohlene Weg für alle, die Websites bauen**), `composer create-project` (der schnellste Weg zu einer Website, die
Sie nur betreiben) und `composer require` in ein leeres Verzeichnis (wenn die Installation ein eigenes Projekt sein
soll). Alle drei führen zu denselben Dateien. Das Kapitel erklärt, welche Version Sie auf welchem Weg erhalten und wie
Sie eine auswählen, wie die Extensions als Composer-Pakete ankommen, wie Sie das optionale Paket Exponential Velocity
hinzufügen und was unter PHP 8.0 bis 8.3 anders ist, und schließt mit einem Rundgang durch jeden Eintrag der obersten
Ebene des Installationsverzeichnisses und den Rechten, die Sie vor der Installation setzen müssen.

[Inhalt](README.md) · Zurück: [2. Voraussetzungen](02-voraussetzungen.md) · Weiter: [4. Eine Installationsmethode wählen](04-installationsweg-waehlen.md)

## 3.1 Versionen, Tags und Branches

Exponential wird als Composer-Paket
[`se7enxweb/exponential`](https://packagist.org/packages/se7enxweb/exponential) aus dem Git-Repository
[github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential) veröffentlicht.

| Was | Name | Liefert Ihnen |
|---|---|---|
| Release-Tags | `v6.0.0` bis `v6.0.14` (die Linie 6.0); ältere Linien verwenden Tags ohne `v`, etwa `4.4.0` | eine exakte, dauerhafte Release |
| Standard-Branch | `main` | die aktuelle Entwicklung der Linie 6.0, derzeit 6.0.15 |
| Release-Branches | `6.0.7` bis `6.0.14` | den Stand jeder Release-Linie |

Der neueste Release-Tag zum Zeitpunkt der Erstellung ist `v6.0.14`; der Branch `main` bezeichnet sich selbst als
`6.0.15stable` (`lib/version.php`) und enthält die in [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md)
beschriebenen Änderungen. Um zu sehen, was derzeit veröffentlicht ist:

```bash
git ls-remote --tags https://github.com/se7enxweb/exponential.git 'v6*'
```

oder, innerhalb eines Klons, nach `git fetch --tags`:

```bash
git tag -l 'v*' --sort=version:refname | tail -5
```

was die fünf neuesten Tags ausgibt, den neuesten zuletzt:

```text
v6.0.10
v6.0.11
v6.0.12
v6.0.13
v6.0.14
```

Sortieren Sie immer nach Version (`--sort=version:refname` oder `sort -V`), niemals nach Namen: Nach Namen sortiert
steht `v6.0.10` vor `v6.0.8`, und ein Skript, das die letzte Zeile einer Namenssortierung nimmt, wählt die falsche
Release. Die Releases des Projekts sind mit ihren Release Notes unter
[GitHub Releases](https://github.com/se7enxweb/exponential/releases) aufgeführt.

**Ein Release-Tag ändert sich nie.** Einmal veröffentlicht, sind ein Tag und die Paketversion, die Packagist daraus
gebaut hat, dauerhaft; eine Korrektur kommt immer als nächste Version. Sie können einen Tag festlegen und sich darauf
verlassen, jedes Mal dieselben Dateien zu erhalten; genau das macht eine festgelegte Version zur richtigen Wahl für
den Produktivbetrieb.

**Welche Version welches PHP braucht.** Lesen Sie dies, bevor Sie wählen, denn Composer verweigert eine Version,
deren PHP-Anforderung Ihr PHP nicht erfüllt:

| Version | `"php"` in ihrer `composer.json` | Unter PHP 8.0 |
|---|---|---|
| `v6.0.0` bis `v6.0.7` | `^7.4 \|\| ^8.1` | nicht installierbar |
| `v6.0.8` bis `v6.0.14` | `^8.1` und spätere 8.x | nicht installierbar |
| 6.0.15 (`main`, `dev-main` für Composer) | `^8.0` und spätere 8.x | installierbar |

Kein veröffentlichter Tag akzeptiert PHP 8.0: Verwenden Sie unter PHP 8.0 `main`, bis es einen Tag der Linie 6.0.15
gibt.

`composer.json` gibt `main` den Branch-Alias `6.0.x-dev` (`"extra": { "branch-alias": { "dev-main": "6.0.x-dev" } }`).
Aus `main` installiert, hat das Root-Paket dann sowohl die Version `6.0.x-dev` als auch `dev-main`, sodass ein Paket,
das `se7enxweb/exponential` mit einer Versionsbedingung wie `^6.0.14` verlangt (wie expqueue), dagegen aufgelöst
werden kann. Ohne den Alias erfüllt `dev-main` keine Versionsbedingung, und eine solche Installation bricht mit
„could not be resolved“ ab.

## 3.2 Drei Wege zum Code

| Weg | Liefert Ihnen | Am besten geeignet für |
|---|---|---|
| [A. git clone und composer install](#a-git-clone-und-composer-install-empfohlen) | den Kernel und jede se7enxweb-Extension als Git-Klone mit ihrer vollständigen Historie | **das Bauen von Websites**: Entwicklungsrechner, Staging und jede Website, deren Code Sie ändern (empfohlen) |
| [B. composer create-project](#b-composer-create-project) | Release-Archive ohne Historie (oder, mit zwei Optionen, Klone) | eine Website, die Sie nur betreiben, ein schnelles Ausprobieren |
| [C. composer require in ein leeres Verzeichnis](#c-composer-require-in-ein-leeres-verzeichnis) | den Kernel als Paket Ihres eigenen Projekts | eine Installation, die als eigenes Projekt verwaltet wird |

### A. git clone und composer install (empfohlen)

Der empfohlene Weg für alle, die Websites mit Exponential bauen. Mit Git-Klonen sehen Sie, was sich geändert hat
(`git status`, `git diff`), warum eine Zeile dasteht (`git log`, `git blame`) und welche Änderung etwas kaputt gemacht
hat (`git bisect`); Sie können eine Korrektur nutzen, bevor sie veröffentlicht ist, Ihre eigenen Änderungen auf einem
Branch halten, den Updates nie überschreiben, und Korrekturen als Pull Requests zurückschicken.
[Mit Git entwickeln](../../en/us/help/develop-with-git.md) erklärt jeden dieser Punkte Schritt für Schritt.

Weisen Sie Composer zuerst, einmal pro Benutzerkonto, an, jedes `se7enxweb/*`-Paket als Git-Klon und alles andere
(die Zeta Components und andere Bibliotheken) als Release-Archiv zu installieren:

```bash
composer config --global 'preferred-install.se7enxweb/*' source
```

Klonen Sie dann den Kernel, legen Sie einen eigenen Branch an und installieren Sie:

```bash
git clone https://github.com/se7enxweb/exponential.git
cd exponential
git switch -c my-site               # or: git switch -c my-site v6.0.14, to build on a release instead of main
composer install
```

Der Klon enthält den Kernel mit seiner gesamten Historie (mehr als 27.000 Commits, etwa 370 MB), die mit dem Kernel
ausgelieferten Extensions (`ezjscore`, `ezoe`, `ezformtoken`, `expservices`) und die Designs. `composer install`
fügt `vendor/` hinzu und klont jede Extension, die `composer.json` verlangt, nach `extension/<name>`, jede als eigenes
Git-Repository (67 auf `main`; die erste Installation dauert einige Minuten). Diese Extensions gehören nicht zum
Repository des Kernels, daher führt `git status` sie als nicht verfolgt auf; das ist zu erwarten. Jede steht auf dem
Release-Tag, den Composer gewählt hat, sodass `git -C extension/<name> status` `HEAD detached at <tag>` meldet: Das
ist normal, und ein Branch ist nur ein `git switch -c` entfernt.

Prüfen Sie, ob die Extensions Klone sind:

```bash
composer status -v                  # "No local changes"
ls -d extension/*/.git | wc -l      # the number of extensions installed as clones
git -C extension/eztags log --oneline -3
```

Ohne die Einstellung `preferred-install` kommen die Extensions als Archive an; `composer reinstall 'se7enxweb/*'`
macht sie später zu Klonen ([Mit Git entwickeln](../../en/us/help/develop-with-git.md#the-extensions)). Um die Einstellung beim
Projekt statt bei Ihrem Konto zu halten, führen Sie den Befehl `composer config` ohne `--global` innerhalb des Klons
aus; er wird dann auf Ihrem Branch in `composer.json` geschrieben.

Um später zu aktualisieren, auf Ihrem Branch:

```bash
git fetch origin --tags
git merge origin/main               # or the release you move to: git merge v6.0.<n>
composer update
```

`composer update`, nicht `composer install`: Die `composer.json` des Kernels hat sich geändert, und die Lock-Datei
(Ihre eigene, sie gehört nicht zum Repository) muss ihr folgen. `composer update se7enxweb/<package>` aktualisiert eine
einzelne Extension. Immer zuerst Git, dann Composer. [Mit Git aktualisieren](../../en/us/help/update-with-git.md) beschreibt
das Vorgehen auf der Kommandozeile und in der Administration (Git Manager, dann „Updates und Pakete“), für jeden
Rhythmus von stündlich bis nur zu jeder Release, sowie ein automatisches Update mit cron oder einem systemd-Timer.
Composer verweigert das Aktualisieren eines Klons mit nicht committeten Änderungen oder mit Commits, die nur auf Ihrem
Rechner existieren, sodass Ihre Arbeit nie überschrieben wird;
[Wenn Composer sich weigert](../../en/us/help/develop-with-git.md#when-composer-refuses) führt die Meldungen auf und was zu tun
ist. Den Code zu bewegen ist nur die Hälfte eines Upgrades: Die Datenbank-Update-Dateien aus
[Kapitel 11](11-aktualisieren.md) sind die andere Hälfte.

### B. composer create-project

Der schnellste Weg zu einer Website, die Sie nur betreiben. Composer lädt das Paket herunter, macht das Verzeichnis zu
Ihrem Projekt und installiert alle Abhängigkeiten:

```bash
cd /var/www
composer create-project se7enxweb/exponential exponential
cd exponential
```

Ohne Versionsangabe nimmt Composer die neueste stabile Release. Um eine festzulegen oder dem Entwicklungs-Branch zu
folgen:

```bash
composer create-project se7enxweb/exponential:v6.0.14 exponential
composer create-project se7enxweb/exponential:dev-main exponential
```

`create-project` behandelt die heruntergeladene `composer.json` als Root-Paket, daher gelten ihr
`config.allow-plugins` (das `se7enxweb/exponential-legacy-installer` zulässt) und ihr Skript `post-install-cmd`: Die
Class Maps werden am Ende erzeugt (`php bin/php/ezpgenerateautoloads.php`). Fügen Sie auf einem Produktivserver
`--no-dev` hinzu, wodurch die Testwerkzeuge weggelassen werden.

Standardmäßig kommt jedes Paket, auch der Kernel, als Release-Archiv ohne Historie an. Um auch mit `create-project`
Klone zu erhalten, geben Sie beide Optionen an:

```bash
composer create-project se7enxweb/exponential exponential --prefer-source --keep-vcs
```

Ohne `--keep-vcs` löscht Composer am Ende den `.git`-Ordner des Kernels. Eine aus Archiven installierte Website lässt
sich später ohne Neuinstallation in Klone umwandeln: [Mit Git entwickeln](../../en/us/help/develop-with-git.md#step-3-turn-an-existing-site-into-clones).

### C. composer require in ein leeres Verzeichnis

Wenn die Installation ein eigenes Projekt mit Ihrer eigenen `composer.json` sein soll:

```bash
mkdir exponential
cd exponential
composer require se7enxweb/exponential
```

Das Paket hat den Typ `ezpublish-legacy`. Das Installer-Plugin (`LegacyKernelInstaller`) installiert ein Paket dieses
Typs in das Projektverzeichnis (`.`), sodass die Kernel-Dateien in dem Verzeichnis landen, in dem Sie sich befinden,
nicht in `vendor/`. In einem Symfony-Projekt (einem, dessen `composer.json` `extra.symfony-app-dir` setzt) kommt es
stattdessen nach `ezpublish_legacy/`; ein Projekt kann mit `extra.ezpublish-legacy-dir` ein anderes Verzeichnis
angeben.

Zwei Dinge unterscheiden sich von den Wegen A und B:

- Ihre neue `composer.json` lässt das Installer-Plugin noch nicht zu. Composer 2.2 und neuer fragt, ob
  `se7enxweb/exponential-legacy-installer` vertraut werden soll; antworten Sie mit Ja, oder fügen Sie es vorher hinzu:

  ```bash
  composer config allow-plugins.se7enxweb/exponential-legacy-installer true
  ```

  Wenn Sie mit Nein antworten, landet der Kernel in `vendor/se7enxweb/exponential/` und die Extensions ebenfalls in
  `vendor/`, wo nichts sie findet.

- Composer führt nur die Skripte des Root-Pakets aus, und das ist jetzt Ihres. Erzeugen Sie die Class Maps nach jeder
  Installation und jedem Update selbst:

  ```bash
  php bin/php/ezpgenerateautoloads.php
  ```

### Das Ergebnis prüfen

Gleich, welchen Weg Sie gewählt haben:

```bash
php bin/php/console --version
ls vendor/autoload.php
```

Die erste Zeile der Konsolenausgabe nennt die Version:

```text
console (Exponential) 6.0.15stable
```

Fehlt `vendor/autoload.php`, ist Composer nicht fertig geworden; führen Sie `composer install` erneut aus und lesen Sie
seine Ausgabe ab dem ersten Fehler, nicht ab der letzten Zeile. Die üblichen Ursachen sind eine fehlende
PHP-Erweiterung (Composer nennt das `ext-*`, das es braucht), ein PHP, das für die angeforderte Version zu alt ist
([3.1](#31-versionen-tags-und-branches)), oder ein Plugin, das nicht zugelassen wurde.

## 3.3 Installation unter PHP 8.0 bis 8.3

Auf der aktuellen Linie (`main`, 6.0.15) ist nichts Besonderes nötig: Ein einfaches `composer install` funktioniert
unter jedem PHP von 8.0 bis 8.5. Die Entwicklungsanforderung `"phpunit/phpunit": "^9.6 || ^10.5 || ^11.5 || ^12.0 || ^13.4"`
lässt Composer das PHPUnit wählen, das zum verwendeten PHP passt (9.6 unter 8.0, 10.5 unter 8.1, 11.5 unter 8.2, 12
unter 8.3, 13.4 unter 8.4 und 8.5). Dieser Bereich ist auch mit `--no-dev` wichtig: Das Repository liefert keine
`composer.lock` aus, und ohne Lock-Datei löst Composer auch `require-dev` auf, obwohl es diese Pakete nicht
installiert. Die eigene PHP-8.0-Prüfung des Projekts (der Job `php80` in `.github/workflows/quality.yml`) installiert
genau so, wie eine Website es tut:

```bash
composer install --no-dev --prefer-dist
```

Die Testsuite selbst verwendet Funktionen von PHPUnit 10 und läuft daher unter PHP 8.1 und neuer; unter PHP 8.0 sorgt
der Bereich nur dafür, dass sich die Installation auflösen lässt.

**Die Release-Tags `v6.0.13` und `v6.0.14` sind anders.** Sie verlangen `"phpunit/phpunit": "13.0.0"`, das PHP 8.4.1
braucht, sodass eine einfache Installation dieser Tags unter PHP 8.1 bis 8.3 mit einem PHP-Versionskonflikt bei
PHPUnit abbricht. Entfernen Sie zuerst die beiden Entwicklungsanforderungen und installieren Sie dann, was eine
Website braucht:

```bash
composer create-project --no-install se7enxweb/exponential:v6.0.14 exponential
cd exponential
composer remove --dev --no-update --no-interaction phpunit/phpunit zetacomponents/php-generator
composer install --no-dev --prefer-dist
```

`composer remove --no-update` ändert nur `composer.json` und installiert nichts; der zweite Befehl installiert. In
einem Git-Klon dieser Tags genügen die letzten beiden Befehle, und `git checkout composer.json` stellt die Datei
danach wieder her.

Auf einem Produktivserver ist `--no-dev` unter jeder PHP-Version richtig: Die Testwerkzeuge werden zum Betrieb einer
Website nicht gebraucht.

## 3.4 Das optionale Velocity-Paket

Exponential Velocity, der empfohlene Anwendungsserver, ist ein eigenes Paket,
[`se7enxweb/exponential-velocity`](https://packagist.org/packages/se7enxweb/exponential-velocity). `composer.json`
schlägt es vor, statt es zu verlangen, denn es braucht PHP 8.1 (`"php": ">=8.1"`) und die Erweiterung `sockets`
(`"ext-sockets": "*"`), während der Kernel unter 8.0 läuft. Fügen Sie es unter PHP 8.1 oder neuer mit der Version
hinzu, die der Vorschlag nennt:

```bash
composer require se7enxweb/exponential-velocity:~0.0.4.50
```

`~0.0.4.50` akzeptiert jede spätere Release `0.0.4.x` (`>=0.0.4.50 <0.0.5.0`), sodass Composer die neueste davon
installiert. Die letzte Stelle einer Velocity-Version zählt über 9 hinaus weiter (`0.0.4.9`, `0.0.4.10`, ...), daher
findet eine Versionssortierung die neueste, keine Namenssortierung.

Das Paket landet in `vendor/se7enxweb/exponential-velocity/` (sein Typ ist `project`, daher wird es nicht nach
`extension/` verschoben). Die Konsole findet es dort: `php bin/php/console exp:velocity status` meldet die Engine, und
`exp:velocity start` startet sie. Bevor das Paket installiert ist, kann die Engine `qbix` nicht starten und meldet
`velocity: no server script at ...`; die Engine `php` (der eingebaute Server von PHP) funktioniert auch ohne das
Paket:

```bash
php bin/php/console exp:velocity start --engine=php
```

Velocity wird auch als Betriebssystempakete (`.deb`, `.rpm`) und als eigenständige Binärdatei veröffentlicht; siehe
[Velocity-Pakete und -Binärdateien](../../en/us/features/6.0/velocity-packages-and-binaries.md). Bevor Sie es aktualisieren,
lesen Sie die [Upgrade-Hinweise zur Velocity-Engine](../../en/us/bc/6.0/velocity-engine-upgrade-notes.md).

## 3.5 Extensions sind Composer-Pakete

Jede Extension außer den vier mit dem Kernel ausgelieferten ist ein Composer-Paket vom Typ
`ezpublish-legacy-extension`. Das Installer-Plugin `se7enxweb/exponential-legacy-installer` (von `composer.json`
verlangt) legt ein solches Paket nach `extension/<name>`:

| Das Paket gibt an | Es wird installiert in |
|---|---|
| `"extra": { "ezpublish-legacy-extension-name": "eztags" }` (Paket `se7enxweb/eztags`) | `extension/eztags` |
| `"extra": { "ezpublish-legacy-extension-name": "explayouts_ui" }` (Paket `se7enxweb/explayouts-ui`) | `extension/explayouts_ui` |
| kein `ezpublish-legacy-extension-name` (Paket `vendor/name`) | `extension/name` |

`composer.json` entscheidet also, welche Extensions eine Installation hat, und in welchen Versionen:

```json
"se7enxweb/eztags": "~2.4.11",
"se7enxweb/cjw_newsletter": "~4.2.0",
"se7enxweb/explayouts": "~1.4.15",
```

Die Tilde-Bedingung lässt die letzte angegebene Stelle frei: `~2.4.11` bedeutet `>=2.4.11 <2.5.0`. Um eine Extension
hinzuzufügen, verlangen Sie ihr Paket, aktivieren Sie sie dann und erzeugen Sie die Class Maps neu:

```bash
composer require se7enxweb/ezfind
php bin/php/ezpgenerateautoloads.php -e
```

Dass eine Extension auf der Festplatte liegt, macht sie noch nicht aktiv: Sie muss in
`[ExtensionSettings] ActiveExtensions[]` aufgeführt sein. Die Installer aktivieren die Extensions des Site-Pakets;
andere aktivieren Sie in der Administration (**Setup > Extensions**) oder in
`settings/override/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveExtensions[]=ezfind
```

Leeren Sie danach den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und starten Sie Velocity neu, wenn es die
Website ausliefert.

Zwei Pakete aus `composer.json` kommen absichtlich inaktiv an und werden eingeschaltet, wenn Sie sie brauchen: die
Job-Warteschlange `se7enxweb/expqueue` (`extension/expqueue`, [expqueue](../../en/us/features/6.0/extensions/expqueue.md)) und
Exponential Intelligence, der Schreibassistent der Administration, `se7enxweb/expai` (`extension/expai`,
[expai](../../en/us/features/6.0/extensions/expai.md)). Beide brauchen eigene Tabellen und eine Cron-Zeile; der Assistent
außerdem einen Anbieterschlüssel in `settings/override/expai.ini.append.php`.
[Abschnitt 10.13](10-nach-der-installation.md#1013-verwaltung-von-extensions) beschreibt die Schritte.

Der Block `suggest` der `composer.json` führt weitere Extensions mit je einem Satz auf, darunter `ezsi`,
`ezscriptmonitor`, `ezfind`, `ezauthorize`, `bcurlaliaswithdash`, `sevenx_valkey`, `sevenx_valkey_cache`,
`ezownerchange`, `adminaid`, `ezpm` und `hcaptcha`. Jede Extension hat ihr eigenes Repository unter
[github.com/se7enxweb](https://github.com/se7enxweb) und trägt ihre Version in `ezinfo.php` und `extension.xml`; die
Extension-Liste der Administration zeigt sie an.

> **Führen Sie `composer update` auf einer Live-Website nicht leichtfertig aus.** Es hebt jedes Paket auf die neueste
> Version, die seine Bedingung zulässt. Aktualisieren Sie ein Paket gezielt (`composer update se7enxweb/<package>`)
> und lesen Sie vorher seine Release Notes. Klone, die Composer selbst angelegt hat (Weg A), sind sicher: Composer
> verweigert das Aktualisieren eines Klons mit nicht committeten Änderungen oder nicht gepushten Commits. Ein **von
> Hand** angelegter Klon (`git clone` nach `extension/<name>`) anstelle eines Pakets, das Composer als Archiv
> installiert hat, ist es nicht: Composer behandelt den Ordner weiterhin als Archiv und ersetzt ihn, samt Historie und
> Commits. Um ein Archiv in einen Klon umzuwandeln, überlassen Sie das Composer mit `composer reinstall <package>`,
> nachdem Sie `preferred-install` gesetzt haben ([Weg A](#a-git-clone-und-composer-install-empfohlen)).

Mehr: [Anleitung zu Extensions](../../en/us/guides/extensions.md), [Extension-Liste und Downloads](../../en/us/features/6.0/extension-list-and-downloads.md),
[Standardverteilung der Extensions](../../en/us/features/6.0/default-extension-distribution.md),
[Ladereihenfolge der Extensions](../../en/us/features/6.0/extension-loading-order.md),
[Zusätzliche Extension-Verzeichnisse](../../en/us/features/6.0/additional-extension-directories.md).

## 3.6 Ein Rundgang durch das Installationsverzeichnis

Die Einträge einer frischen Installation, in alphabetischer Reihenfolge. „Versioniert“ bedeutet, dass die Datei oder
das Verzeichnis zum Git-Repository gehört; die übrigen werden von Composer oder zur Laufzeit angelegt.

### Verzeichnisse

| Verzeichnis | Versioniert | Inhalt |
|---|---|---|
| `autoload/` | ja | `ezp_kernel.php`, die erzeugte Class Map des Kernels. Neu erzeugt mit `php bin/php/ezpgenerateautoloads.php -k`. |
| `benchmarks/` | ja | Mikro-Benchmarks von Kernel-Teilen (Templates, Hashing). |
| `bin/` | ja | Kommandozeilenwerkzeuge: `bin/php/` (die PHP-Skripte, jedes auch als Konsolenbefehl `exp:<name>`, und die Konsole selbst, `bin/php/console`), `bin/shell/` (Shell-Skripte, `shell:<name>`), `bin/mongodb/` (MongoDB-Indexskripte), `bin/startup/`, `bin/linux/`, `bin/phpcs/`, `bin/awk/`. |
| `cronjobs/` | ja | Die Cronjob-Teile, die `runcronjobs.php` ausführt (`basket_cleanup.php`, `linkcheck.php`, `audit.php`, ...). |
| `design/` | ja | Die Designs: `standard` und `base` (Rückfallebenen), `admin`, `admin3`, `admin4`, `admin4l`, `editor` sowie die Beispiele `mysite` und `plain`. Theme-Extensions fügen weitere hinzu. |
| `doc/` | ja | Die Dokumentation, einschließlich dieses Handbuchs. |
| `extension/` | teilweise | Extensions. Das Repository liefert `ezjscore`, `ezoe`, `ezformtoken` und `expservices` aus; Composer fügt die übrigen hinzu. |
| `kernel/` | ja | Der Kernel: Inhaltsmodell, Module, Datentypen, der Setup-Assistent (`kernel/setup/`), die Klassen der Konsolenbefehle (`kernel/private/classes/commands/`). |
| `lib/` | ja | Die allgemeinen Bibliotheken: Datenbanktreiber (`lib/ezdb/`), Templates (`lib/eztemplate/`), Dateien, Locales, Bilder, Sessions; `lib/version.php` enthält die Version. |
| `schemas/` | ja | XML-Schemas (das Schema der Übersetzungsdateien). |
| `settings/` | ja | Die ausgelieferten `.ini`-Dateien. `settings/siteaccess/<name>/` und `settings/override/` enthalten die Einstellungen Ihrer Installation und werden von den Installern geschrieben. |
| `share/` | ja | `db_schema.dba` und `db_data.dba` (die Datenbank, die der Installer anlegt), `locale/` und `translations/`, `codepages/` und `transformations/` (Zeichensätze), `icons/`, `maintenance.html` und `filelist.md5`, das Dateimanifest, mit dem die Upgrade-Prüfung der Administration vergleicht. |
| `support/` | ja | Die Quellen von `ezlupdate`, dem Werkzeug zum Extrahieren von Übersetzungen. |
| `templates/` | ja | Code-Vorlagen für den Klassengenerator (`.ctpl`). |
| `tests/` | ja | Die PHPUnit-Testsuite (`tests/runtests.php`, `phpunit.xml`). |
| `update/` | ja | Datenbank-Update-Dateien pro Engine und Version (`update/database/<engine>/`) und Skripte zur Datenreparatur (`update/common/scripts/`); siehe [Kapitel 11](11-aktualisieren.md). |
| `var/` | nein (nur `var/webdav/`) | Alles, was zur Laufzeit geschrieben wird: `var/cache/`, `var/log/`, `var/storage/` (hochgeladene Dateien, Pakete, die SQLite-Datenbank in `var/storage/sqlite3/`), `var/autoload/` (die Class Maps der Extensions), `var/vc/` (Laufzeitdateien und Protokolle von Velocity). Muss für den Webserver beschreibbar sein. |
| `vendor/` | nein | Die Composer-Bibliotheken: Zeta Components, das Installer-Plugin, Velocity, sofern installiert. |

### Dateien

| Datei | Was sie ist |
|---|---|
| `index.php` | Der Front-Controller: Jede Seitenanfrage läuft durch ihn. |
| `index_rest.php` | Der Front-Controller der REST-API (`/api/`). |
| `index_cluster.php` | Liefert Dateien aus dem Datenbank-Cluster aus. |
| `index_treemenu.php` | Der schnelle Endpunkt für das Inhaltsbaummenü der Administration. |
| `soap.php`, `webdav.php` | Die Einstiegspunkte für SOAP und WebDAV. |
| `runcronjobs.php` | Führt Cronjob-Teile aus: `php runcronjobs.php --list`. |
| `autoload.php` | Lädt den Autoloader von Composer und den des Kernels; jeder Einstiegspunkt beginnt damit. |
| `ezpm.php` | Der Paketmanager auf der Kommandozeile. |
| `composer.json` | Die Anforderungen: PHP, Extensions, Zeta Components. `composer.dev.json` enthält die Entwicklungswerkzeuge von `make devtools` (installiert nach `.devtools/`); `composer.json.dist` ist ein älteres Upstream-Manifest, das zur Referenz aufbewahrt wird. |
| `.htaccess_root` | Apache-Rewrite-Regeln; kopieren Sie die Datei nach `.htaccess` (`cp .htaccess_root .htaccess`). `.htaccess_root_static` ist dieselbe Datei mit den Regeln, die den statischen Cache vor der Anwendung ausliefern. |
| `kickstart.ini-dist` | Die kommentierte Vorlage für `kickstart.ini`; `kickstart.ini--example` ist ein Beispiel. |
| `config.php-RECOMMENDED` | Eine dokumentierte Liste der Einstellungen, die `config.php` vornehmen kann; benennen Sie die Datei in `config.php` um und entfernen Sie die Kommentarzeichen vor den Einstellungen, die Sie brauchen. |
| `config.env.php.example` | Kopieren Sie die Datei nach `config.env.php`, um die Umgebung des Rechners zu benennen (`EXP_ENV`); siehe [Einstellungen pro Umgebung](../../en/us/features/6.0/environment-settings.md). |
| `exponential.cron`, `ezpublish.cron` | Beispiel-Crontabs für die Cronjobs (`crontab exponential.cron`, nachdem Sie die beiden Variablen `EXPONENTIALROOT` und `PHP` gesetzt haben). |
| `sw.js`, `index.js` | Der Service Worker der Website. |
| `robots.txt`, `favicon.ico` | Wie bei jeder Website üblich. |
| `phpunit.xml`, `phpcs.xml.dist`, `phpstan.neon.dist`, `phpstan-baseline.neon`, `Makefile` | Entwicklungs- und Qualitätswerkzeuge. |
| `LICENSE`, `COPYRIGHT.md`, `README.md`, `CONTRIBUTING.md`, `SECURITY.md` | Lizenz, Copyright, die README des Projekts, wie Sie beitragen können, wie Sie eine Sicherheitslücke melden. |

Dateien, die nach einer Installation erscheinen und Ihnen gehören, die nie committet oder weitergegeben werden dürfen:
`settings/override/*` und `settings/siteaccess/*` (Datenbank-Zugangsdaten), `kickstart.ini` (das Datenbankpasswort),
`config.env.php` und `var/log/initial-admin-password`.

## 3.7 Rechte nach dem Beschaffen des Codes

Der Benutzer, unter dem der Webserver oder die Worker von Velocity laufen, muss in `design`, `extension`, `settings`,
`settings/override`, `settings/siteaccess` und `var` samt allem darunter schreiben können (die Liste, die der
Setup-Assistent prüft, ist `[directory_permissions] CheckList` in `settings/setup.ini`). Wenn Sie Composer als ein
anderer Benutzer ausgeführt haben, übergeben Sie die Dateien vor der Installation, und zwar in dieser Form (der
Setup-Assistent gibt die genauen Befehle für Ihren Rechner aus):

```bash
sudo chmod -R ug+rwx design extension settings var
sudo chown -R <web user>:<web group> design extension settings var
```

Wenn die Kommandozeile (Cronjobs, die Konsole, Composer) und der Webserver unter verschiedenen Benutzern laufen,
geben Sie beiden eine gemeinsame Gruppe, die dieselben Dateien schreiben darf, statt die Verzeichnisse für alle zu
öffnen. Ausliefern und Rechte werden ausführlich in [Kapitel 8](08-die-website-ausliefern.md) behandelt.

## Referenzen

In diesem Repository:

- [Installationsüberblick](../../en/us/INSTALL.md), Abschnitt 3.
- [Aktualisieren](../../en/us/guides/upgrading.md): eine bestehende Website auf neuen Code umstellen.
- [Unterstützung für PHP 8.0](../../en/us/bc/6.0/php-8.0-support.md): Installation mit Composer unter PHP 8.0.
- [Continuous Integration](../../en/us/specifications/6.0/continuous-integration.md) und [Qualitätsprüfungen](../../en/us/features/6.0/quality-checks.md).
- [Velocity-Engines](../../en/us/bc/6.0/velocity-engines.md), [Velocity-Pakete und -Binärdateien](../../en/us/features/6.0/velocity-packages-and-binaries.md),
  [Upgrade-Hinweise zur Velocity-Engine](../../en/us/bc/6.0/velocity-engine-upgrade-notes.md).
- [Anleitung zu Extensions](../../en/us/guides/extensions.md), [Extensions](../../en/us/features/6.0/extensions/README.md),
  [Extension-Metadaten](../../en/us/specifications/6.0/extension-metadata.md).
- [Eine Installation reparieren](../../en/us/bc/6.0/repair.md): wenn `vendor/` fehlt.
- [Konsole](../../en/us/bc/6.0/console.md).
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md) und die [Release Notes der Extensions](../../changelogs/extensions/README.md).
- Code: `composer.json`, `lib/version.php`, `.github/workflows/quality.yml` (Job `php80`),
  `vendor/se7enxweb/exponential-legacy-installer/src/eZ/Publish/Composer/` (`LegacyKernelInstaller`,
  `LegacyExtensionInstaller`).

Extern:

- Composer: [create-project](https://getcomposer.org/doc/03-cli.md#create-project),
  [install](https://getcomposer.org/doc/03-cli.md#install-i), [require](https://getcomposer.org/doc/03-cli.md#require-r),
  [remove](https://getcomposer.org/doc/03-cli.md#remove-rm-uninstall),
  [Versionsbedingungen](https://getcomposer.org/doc/articles/versions.md),
  [allow-plugins](https://getcomposer.org/doc/06-config.md#allow-plugins),
  [Skripte](https://getcomposer.org/doc/articles/scripts.md).
- Packagist: [se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential),
  [se7enxweb/exponential-velocity](https://packagist.org/packages/se7enxweb/exponential-velocity),
  [alle se7enxweb-Pakete](https://packagist.org/packages/se7enxweb/).
- GitHub: [se7enxweb/exponential](https://github.com/se7enxweb/exponential),
  [Releases](https://github.com/se7enxweb/exponential/releases),
  [se7enxweb/exponential-velocity](https://github.com/se7enxweb/exponential-velocity),
  [alle se7enxweb-Repositories](https://github.com/se7enxweb).
- PHPUnit: [unterstützte Versionen](https://phpunit.de/supported-versions.html).
- Git: [git clone](https://git-scm.com/docs/git-clone), [git tag](https://git-scm.com/docs/git-tag).

[Inhalt](README.md) · Zurück: [2. Voraussetzungen](02-voraussetzungen.md) · Weiter: [4. Eine Installationsmethode wählen](04-installationsweg-waehlen.md)
