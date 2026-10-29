# 1. Einführung: Was Sie installieren

Dieses Kapitel erklärt, was Exponential ist und woraus eine Installation besteht, bevor Sie eine einrichten: der
Kernel, die Extensions, die drei Siteaccesses `site`, `admin` und `editor`, die Designs, die Einstellungsdateien und
das Site-Paket. Anschließend beschreibt es, wie dieses Handbuch aufgebaut ist, welche Konventionen jedes Kapitel
befolgt (wo Befehle ausgeführt werden, wie Einstellungen geschrieben werden, was zu tun ist, wenn ein Skript die
Ausführung als `root` verweigert), und schließt mit einem Glossar der Begriffe, die die Installationskapitel
verwenden. Lesen Sie es einmal, bevor Sie beginnen; die späteren Kapitel setzen das hier eingeführte Vokabular voraus,
und viele Installationsprobleme erweisen sich eher als Missverständnis eines dieser Begriffe denn als Fehler.

[Inhalt](README.md) · Weiter: [2. Voraussetzungen](02-voraussetzungen.md)

## 1.1 Was Exponential ist

Exponential ist ein Open-Source-Content-Management-System und Anwendungsframework, geschrieben in PHP. Sein
bemerkenswertestes Merkmal ist ein Inhaltsmodell, das Sie selbst definieren: Sie beschreiben Arten von Inhalten
(Inhaltsklassen wie Artikel oder Produkt) als Listen typisierter Felder (Attribute), und jeder Inhalt ist ein Objekt
einer solchen Klasse, das mit seinen Versionen und Übersetzungen gespeichert und in einem Inhaltsbaum platziert wird.
Standardfunktionen eines CMS wie Nachrichtenveröffentlichung, E-Commerce und Foren sind eingebaut, zusammen mit einer
Benutzerverwaltung, einem Berechtigungssystem aus Rollen und Richtlinien, mehrsprachigen Inhalten und einer
Template-Sprache für die Ausgabe.

Exponential 6 setzt eine langlebige Codebasis fort (ihre Copyright-Zeilen beginnen 1998). Die aktuelle 6.0-Linie
läuft auf PHP 8.0 bis 8.5, wird mit Composer installiert und speichert ihre Inhalte in SQLite, MySQL oder MariaDB,
PostgreSQL, MongoDB oder Oracle. Seit 6.0.15 bringt sie außerdem ihren eigenen Anwendungsserver mit, **Exponential
Velocity**, der in jeder Phase der empfohlene Weg ist, sie auszuliefern, vom Laptop eines Entwicklers bis zur
Produktion. Apache oder nginx mit PHP-FPM werden weiterhin vollständig unterstützt, und FrankenPHP ist eine
produktionsreife Alternative.

**Ab 6.0.15 ist der Velocity Internet / Web Application Server Teil von Exponential.** Die Root-`composer.json`
verlangt `se7enxweb/exponential-velocity ~0.0.4.57`, statt es nur vorzuschlagen, sodass jede mit Composer angelegte
Installation ihn hat, unter jedem PHP, das der Kernel unterstützt, PHP 8.0 eingeschlossen. Das ist ein Wendepunkt für
das Projekt, ein Grundstein und ein Schlussstein: eine neue Stufe der Integration des Anwendungssystems, die im
Kernprodukt standardmäßig noch mehr Funktionen von Haus aus bietet. Exponential ist eine ganze, funktionierende Einheit
statt eines Frameworks, das man erst vervollständigen muss: Eine Installation ist ein Stack für die schnelle
Entwicklung von Internetanwendungen (RAD), ein CMS, eine Digital-Experience-Plattform und ein E-Commerce-System, vom
Server bis zur gerenderten Seite. Siehe
[Velocity ist Teil von Exponential 6.0.15](../hilfe/velocity-ist-teil-von-exponential-6-0-15.md).

Das Produkt wird von [7x](https://se7enx.com) gepflegt. Der Quellcode liegt auf GitHub unter
[github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential), und der Kernel sowie jede Extension
sind Composer-Pakete auf [Packagist](https://packagist.org/packages/se7enxweb/exponential). Die Lizenz ist die GNU
General Public License, Version 2 oder höher (`composer.json`: `"license": "GPL-2.0-or-later"`).

> **Ein Hinweis zu Namen.** Exponential ist aus einem Produkt mit einem älteren Namen hervorgegangen. Einige
> Bezeichner bewahren diese Geschichte absichtlich: Kernel-Klassen beginnen mit `eZ` (`eZINI`, `eZDB`,
> `eZContentObject`), viele Extensions beginnen mit `ez` (`ezjscore`, `ezoe`, `eztags`), die Composer-Pakettypen
> heißen `ezpublish-legacy` und `ezpublish-legacy-extension`, und die Datenbankzeilen, die die Version enthalten,
> heißen `ezpublish-version` und `ezpublish-release`. Das sind Namen von Code, nicht des Produkts, und dieses
> Handbuch schreibt sie genau so wie der Code, denn ein Befehl oder eine Einstellung funktioniert nur, wenn sie so
> geschrieben ist, wie der Code es erwartet. Das Produkt selbst heißt immer Exponential.
> [Umbenennung in Exponential](../funktionen/6.0/umbenennung-in-exponential.md) erzählt die Geschichte.

## 1.2 Die Bestandteile einer Installation

Eine Installation ist ein Verzeichnis auf der Festplatte, das **Installationsverzeichnis**: das Verzeichnis, das
`index.php`, `autoload.php`, `bin/`, `kernel/` und `settings/` enthält. Alles, was Exponential braucht, liegt
darunter, und das Document Root des Webservers (bzw. von Velocity) ist ebenfalls dieses Verzeichnis. Wenn Sie die
Bestandteile kennen, wissen Sie, was Sie frei ersetzen können (den Code), was Sie behalten müssen (Ihre Einstellungen
und `var/`) und was eine Neuinstallation überschreibt.

```
installation root
├── kernel/, lib/          the kernel: the code every installation runs
├── autoload/              the kernel's class map (autoload/ezp_kernel.php)
├── extension/             extensions: the four kernel-shipped ones and those Composer installs
├── design/                designs: templates, stylesheets, images
├── settings/              shipped settings; settings/siteaccess/ and settings/override/ hold yours
├── var/                   everything written at run time: cache, logs, storage, the SQLite database
├── vendor/                the libraries Composer installs (Zeta Components and others)
├── bin/                   command line tools; bin/php/console runs all of them
├── cronjobs/              the jobs runcronjobs.php runs
├── share/                 the database schema and data, locales, translations, the file manifest
├── update/                database update files and scripts for upgrades
├── doc/                   the documentation, this book included
└── index.php              the front controller every page request goes through
```

[Kapitel 3](03-den-code-beschaffen.md#36-ein-rundgang-durch-das-installationsverzeichnis) stellt jeden Eintrag der obersten Ebene im
Detail vor. Die Kurzfassung für die Planung von Backups: `settings/override/`, `settings/siteaccess/`, `var/` und die
Datenbank gehören der Installation selbst; alles andere lässt sich aus Git und Composer wiederherstellen.

### Der Kernel

Der Kernel ist der Code in `kernel/` und `lib/`. `lib/` enthält die allgemeinen Bibliotheken (Datenbanktreiber in
`lib/ezdb/`, die Template-Engine in `lib/eztemplate/`, Dateien, Locales, Bilder, Sitzungen); `kernel/` enthält das
Inhaltsmodell, die Module, die auf URLs antworten (`content/view`, `user/login`, `setup/...`), den Setup-Assistenten
(`kernel/setup/`), die Konsolenbefehle (`kernel/private/classes/commands/`) und die Datentypen. Die Version des
Kernels ist in `lib/version.php` definiert (`VERSION_MAJOR`, `VERSION_MINOR`, `VERSION_RELEASE`, `VERSION_STATE`);
die Befehlskonsole gibt sie aus:

```bash
php bin/php/console --version
```

Die erste Zeile der Ausgabe nennt die Version:

```text
console (Exponential) 6.0.15stable
```

`6.0.15` sind Major-, Minor- und Release-Nummer; `stable` ist der Status. Der Status ist das, was der Code über sich
selbst aussagt, keine Aussage darüber, dass eine Release mit dieser Nummer veröffentlicht wurde: siehe
[1.5](#15-konventionen), **Versionen**.

Der Kernel hängt von den **Zeta Components** ab (`zetacomponents/*` in `composer.json`), einer Sammlung von
PHP-Bibliotheken für Caching, Konfiguration, Mail, Archive, Bildkonvertierung und mehr. Ihretwegen ist Composer
erforderlich: Ohne `vendor/` kann der Kernel nicht starten, und jede Seite und jeder Befehl schlägt fehl, bevor er
etwas Sinnvolles tut (siehe [Fehlerbehebung](12-fehlerbehebung.md)).

### Extensions

Eine Extension ist ein Verzeichnis unter `extension/`, das Einstellungen, Klassen, Module, Templates, Designs oder
Kommandozeilenskripte hinzufügt, ohne den Kernel zu verändern. Eine Extension wird nur verwendet, wenn sie aktiv ist:
Sie ist in `[ExtensionSettings] ActiveExtensions[]` der `site.ini` (für jeden Siteaccess) oder in
`ActiveAccessExtensions[]` (für einen Siteaccess) eingetragen. Eine Extension, die auf der Festplatte liegt, aber
nicht aktiv ist, tut überhaupt nichts, und das ist die übliche Antwort auf „Ich habe sie installiert, und nichts hat
sich geändert“.

Extensions kommen auf zwei Wegen:

| Weg | Welche | Woher sie kommen |
|---|---|---|
| Im Kernel-Repository mitgeliefert | `ezjscore` (Skripte, Styles, jQuery, Serverfunktionen), `ezoe` (der Online-Editor), `ezformtoken` (Formularschutz), `expservices` (entfernte Dienste) | das Verzeichnis `extension/` des Repositorys selbst |
| Von Composer installiert | alles andere, was `composer.json` verlangt, zum Beispiel `eztags`, `ezflow`, `ezwebin`, `ezoracle`, `cjw_newsletter`, die Pakete `explayouts*` und `expsite*`, `sevenx_dse`, `sevenx_themes_simple` und `sevenx_themes_media` (Paket `se7enxweb/sevenx-themes-media`) | Packagist; jede ist ein Composer-Paket vom Typ `ezpublish-legacy-extension` |

Das Composer-Plugin `se7enxweb/exponential-legacy-installer` legt jedes Paket vom Typ `ezpublish-legacy-extension`
in `extension/<name>` ab, wobei `<name>` der Wert `extra.ezpublish-legacy-extension-name` des Pakets ist oder, wenn
dieser nicht gesetzt ist, der Teil des Paketnamens nach dem Schrägstrich. Deshalb landet `se7enxweb/explayouts-ui`
in `extension/explayouts_ui`: Das Paket benennt sein Verzeichnis ausdrücklich.
[Kapitel 3](03-den-code-beschaffen.md#35-extensions-sind-composer-pakete) erklärt das im Detail. Die Extensions und
ihre jeweilige Aufgabe sind unter [Extensions](../funktionen/6.0/erweiterungen/README.md) aufgeführt; die Entwicklung
eigener Extensions behandelt die [Anleitung zu Extensions](../anleitungen/extensions.md).

### Siteaccesses

Ein **Siteaccess** ist ein Zugang zur Installation, mit eigenen Einstellungen und eigenem Design. Jede Anfrage wird
genau einem Siteaccess zugeordnet, anhand des ersten Elements des URL-Pfads, des Hostnamens oder des Ports, in der
Reihenfolge, die `[SiteAccessSettings] MatchOrder` vorgibt (`uri;host;port` in der mitgelieferten
`settings/site.ini`; eine Installation schreibt die eine Methode, die sie verwendet). Die Einstellungen eines
Siteaccess liegen in `settings/siteaccess/<name>/`.

Jede Installation, die mit einer der drei Installationsmethoden erstellt wird, hat dieselben drei Siteaccesses, und
ihre **Namen lauten immer `site`, `admin` und `editor`**:

| Siteaccess | Name | Für | Design |
|---|---|---|---|
| öffentliche Site | `site` | Besucher | das Design des Site-Pakets: mit dem Standard-Site-Paket `media`, mit Rückfall auf `ezwebin`, `standard` und `base` |
| Administration | `admin` | Administratoren | mit dem Standard-Site-Paket `admin4l`, mit Rückfall auf `admin4`, `admin3`, `admin2` und `admin` |
| Redaktion | `editor` | Redakteure: die Administration nur für Inhalte | `editor`, mit Rückfall auf `admin4l`, `admin4`, `admin3`, `admin2` und `admin` |

Was Sie während der Installation wählen, ist nicht der Name, sondern der **Match-Wert**: der URL-Pfad, der Hostname
oder der Port, der zum jeweiligen Siteaccess führt. Im URL-Modus mit dem Match-Wert `www` liefert die Adresse
`https://example.com/www/` den Siteaccess `site` aus; einen Siteaccess namens `www` gibt es nicht. Wer das
auseinanderhält, vermeidet die häufigste Verwirrung beim Lesen der erzeugten Einstellungen: Die Verzeichnisse sind
immer `settings/siteaccess/site/`, `settings/siteaccess/admin/` und `settings/siteaccess/editor/`.

Der Redaktions-Siteaccess wird am Ende der Installation aus dem Admin-Siteaccess erstellt (eine Kopie seiner
Einstellungen, die dann geändert wird). Er zeigt nur die Inhaltsreiter (Dashboard, Inhaltsstruktur, Medien,
Benutzer, Shop, Tags, Newsletter), hat keine Entwickler-Werkzeugleiste und keine Schaltfläche „Cache leeren“, und die
Module hinter den ausgeblendeten Reitern (`setup`, `visual`, `explayouts_ui`, `explayouts_ui_api`, `git_manager`,
`xrowextract`, `bccie`) sind dort über `[SiteAccessRules]` abgeschaltet. Geben Sie Redakteuren diese Adresse statt
der Admin-Adresse: Sie behalten alles, was sie brauchen, und können die Einstellungen, das Design und die
Entwicklerwerkzeuge nicht erreichen. Siehe
[Der Setup-Assistent und der Redaktions-Siteaccess](../funktionen/6.0/einrichtungsassistent-und-editor-siteaccess.md).

Wie die Siteaccesses unterschieden werden, wird während der Installation gewählt. Mit `exp:install` und seinen
Standard-Match-Werten (die Hostnamen sind die, die Sie mit `--host` und `--admin-host` angeben) sehen die drei
Zugriffsarten so aus:

| Zugriffsart | Die Site | Die Administration | Die Redaktion |
|---|---|---|---|
| URL-Pfad (`url`, der Standard) | `https://example.com/site/` | `https://example.com/admin/` | `https://example.com/editor/` |
| Hostname (`hostname`; `exp:install --access=host`) | `www.example.com` | `admin.example.com` | `edit.example.com` |
| Port (`port`) | `https://example.com:8080/` | `https://example.com:8081/` | `https://example.com:8082/` |

Die Standard-Match-Werte unterscheiden sich je nach Methode:

| Methode | URL-Pfad | Port | Hostname |
|---|---|---|---|
| `exp:install` | `site`, `admin`, `editor` | `8080`, `8081` und der öffentliche Port + 2 für die Redaktion | `--host`, `--admin-host` (beide erforderlich) und `edit.<--host ohne www.>` für die Redaktion |
| Setup-Assistent | der Paketbezeichner (`sevenx_multisite`), *Bezeichner*`_admin`, `editor` | `8080`, `8081`, `8082` | *Bezeichner*`.`*Host*, *Bezeichner*`-admin.`*Host*, `edit.`*Host* (*Host* ist der Name, den der Browser verwendet hat) |
| Kickstarter | was `kickstart.ini [site_details]` angibt; fehlt ein Schlüssel, die Standardwerte des Assistenten, außer dass der Port der Redaktion der öffentliche Port + 2 ist und ihr Host `edit.<öffentlicher Host ohne www.>` | | |

Die Regeln stehen in `kernel/setup/steps/ezstep_site_access.php` und `ezstep_site_details.php`; der Wert der
Redaktion wird immer so gewählt, dass er nie dem öffentlichen oder dem Admin-Wert entspricht, denn zwei Siteaccesses
auf demselben Pfad, Port oder Host würden einen von ihnen unerreichbar machen. Der Setup-Assistent lehnt außerdem
`admin` und `user` als Match-Werte ab, sodass im Assistenten der Admin-Pfad nicht `/admin` sein kann; auf der
Kommandozeile kann er es.

Was hier schiefgehen kann:

- **Port- und Hostnamen-Matching brauchen auch die Serverseite.** Ein Port-Wert funktioniert nur, wenn der Webserver
  oder Velocity auf diesem Port lauscht, und ein Hostname nur, wenn DNS und die Host-Konfiguration des Servers
  diesen Namen an die Installation weiterleiten ([Kapitel 8](08-die-website-ausliefern.md)). Beachten Sie, dass auch der
  eigene Standard-HTTPS-Port von Velocity 8080 ist (`settings/velocity.ini`, `[ServerSettings] HTTPSPort`); legen Sie
  beim Port-Matching vor der Installation fest, welches Programm welchen Port belegt.
- **Die `SiteURL` ist wichtig.** Jeder Siteaccess speichert seine Adresse ohne Schema in
  `[SiteSettings] SiteURL`. Ist die Adresse, die Sie während der Installation angeben, falsch (der Standard auf der
  Kommandozeile ist `http://localhost`), zeigen Links in Mails und in der Administration an die falsche Stelle.
  Geben Sie immer die tatsächliche Adresse an.

### Designs

Ein **Design** ist eine Sammlung von Templates (`.tpl`), Stylesheets, Skripten und Bildern unter `design/<name>/`
(oder `extension/<ext>/design/<name>/`). Ein Siteaccess benennt sein eigenes Design in
`[DesignSettings] SiteDesign` und fällt über `AdditionalSiteDesignList[]` auf `StandardDesign` (`standard`) zurück.
Wird ein Template benötigt, werden die Designs in dieser Reihenfolge durchsucht, und das erste, das es enthält,
gewinnt; ein Site-Design braucht daher nur die Templates, die von den Standard-Templates abweichen. Dieser Rückfall
ist auch der Grund, warum ein fehlerhafter Override eines einzelnen Templates eine Seite verändern kann, während der
Rest der Site normal aussieht.

Das Kernel-Repository liefert die Designs `standard` und `base` (die Rückfall-Designs), die Administrationsdesigns
`admin`, `admin3`, `admin4` und `admin4l`, das Design `editor` und die Beispieldesigns `mysite` und `plain` mit.
`admin2` existiert nur innerhalb von Extensions (`eztags`, `cjw_newsletter` und andere legen ihre Admin-Ansichten
dort ab), weshalb es in der Rückfallliste der Administration bleibt. Das Design der öffentlichen Site kommt mit dem
Site-Paket oder mit einer Theme-Extension. Wie Sie ein Template finden und überschreiben, steht in
[Templates und Design](../anleitungen/templates-und-design.md).

### Einstellungsdateien

Einstellungen sind INI-Dateien (`site.ini`, `content.ini`, `image.ini`, ...) aus Blöcken (`[DatabaseSettings]`) mit
Zeilen der Form `Key=Value`; ein Schlüssel, der auf `[]` endet, ist eine Liste. Die mitgelieferten Standardwerte
liegen in `settings/` und werden nie bearbeitet: Ein Upgrade ersetzt sie. Die Werte Ihrer Installation werden als
Overrides geschrieben, die nach den Standardwerten gelesen werden und Vorrang haben:

1. `settings/<file>.ini`: die mitgelieferten Standardwerte.
2. Das Verzeichnis `settings/` jeder aktiven Extension.
3. `settings/siteaccess/<siteaccess>/<file>.ini.append.php`: die Werte eines Siteaccess.
4. `settings/override/<file>.ini.append.php`: die Werte der gesamten Installation, zuletzt gelesen.

Die Installer schreiben sowohl `settings/siteaccess/` als auch `settings/override/`. Wenn ein Wert nicht wirkt,
fragen Sie die Konsole, woher er stammt. Zum Beispiel auf einer installierten Site:

```bash
php bin/php/console exp:ini where site.ini/SiteAccessSettings/CheckValidity
```

gibt jede Datei aus, die den Schlüssel setzt, in Ladereihenfolge, sowie den wirksamen Wert:

```text
site.ini/SiteAccessSettings/CheckValidity (load order of siteaccess site)
 1. settings/site.ini                                            scope default
      CheckValidity=true
 2. settings/override/site.ini.append.php                        scope global
      CheckValidity=false
In effect:
      CheckValidity=false
```

Einstellungen werden in `var/cache/ini/` zwischengespeichert; leeren Sie nach dem Bearbeiten einer Datei von Hand
diesen Cache mit `php bin/php/ezcache.php --clear-tag=ini`, und starten Sie Velocity neu, falls er die Site
ausliefert, denn seine Worker halten die Einstellungen im Speicher. Die Platzierungsregeln sind in
[Platzierung von INI-Overrides](../spezifikationen/6.0/ini-override-platzierungen.md) spezifiziert; der Befehl, der
Einstellungen in jedem Geltungsbereich liest und schreibt, ist [exp:ini](../funktionen/6.0/exp-ini-befehl.md).

### Das Site-Paket und die Datenbank

Die Inhalte, die Inhaltsklassen, die Benutzer und Rollen und oft auch das Design einer neuen Site kommen als
**Site-Paket**: ein Archiv aus Inhalten, Klassen und Dateien, das am Ende der Installation installiert wird. Der
Standard ist `sevenx_multisite`, das Exponential-Multisite-Paket; es benötigt zwei weitere Pakete, `sevenx_classes`
(die Inhaltsklassen) und `sevenx_multisite_democontent` (die Demo-Inhalte). Der Installer lädt sie aus dem
Paketindex herunter, der in `settings/package.ini`, `[RepositorySettings] RemotePackagesIndexURL`, angegeben ist,
sofern sie nicht bereits unter `var/storage/packages/` importiert sind
([Kapitel 4](04-installationsweg-waehlen.md#43-site-pakete-was-installiert-wird)).

Bevor das Paket installiert wird, wird die Datenbank aus dem Schema und den Daten in `share/db_schema.dba` und
`share/db_data.dba` erstellt, die der Installer mit seinem Schema-Handler liest und im Dialekt der gewählten
Datenbank schreibt. Dieselben zwei Dateien dienen SQLite, MySQL, MariaDB, PostgreSQL, MongoDB und Oracle; deshalb
ändert die Wahl der Datenbank sonst nichts an der Installation.

### Velocity

Exponential Velocity ist der Anwendungsserver, der zusammen mit Exponential entwickelt wird (Paket
`se7enxweb/exponential-velocity`, das ab dem Release 0.0.4.54 PHP 8.0 oder höher und die Erweiterung `sockets` benötigt). Er lädt die
Anwendung einmal, hält sie in persistenten Workern, hat einen eigenen Antwort-Cache und liefert HTTP und HTTPS selbst
aus, mit eigenen Zertifikaten. Gesteuert wird er über den Konsolenbefehl `exp:velocity`, der auch zwei weitere
Engines betreibt: den eingebauten Server von PHP (`php`, für die Entwicklung) und FrankenPHP (`frankenphp`, eine
produktionsreife Alternative). Velocity ist bei der Installation optional, aber für den Betrieb empfohlen; die Site
läuft auch ohne es hinter Apache oder nginx. Weil die Worker den Code und die Einstellungen im Speicher halten,
erreicht eine Änderung an einer PHP-Klasse oder einer INI-Datei eine von Velocity ausgelieferte Site erst nach
`exp:velocity restart` (oder `exp:velocity deploy`). Siehe [Velocity-Engines](../bc/6.0/velocity-engines.md) und das
[README](../../../README.md#exponential-velocity--the-fast-option) des Projekts.

## 1.3 Die drei Installationsmethoden in je einem Absatz

Alle drei Methoden führen dieselben Setup-Schritte aus (`kernel/setup/steps/`) und erzeugen dieselbe Art von
Installation. Sie unterscheiden sich darin, woher die Antworten kommen, und das entscheidet, welche zu Ihnen passt.

- **Der Setup-Assistent** läuft im Browser. Eine frische Installation hat `[SiteAccessSettings] CheckValidity=true`
  in `settings/site.ini`, wodurch jede Anfrage an den Assistenten geht, bis sein letzter Schritt
  `CheckValidity=false` in `settings/override/site.ini.append.php` schreibt. Sie beantworten eine Seite pro Schritt,
  jede mit einer Erklärung. Er benötigt zuerst einen funktionierenden Webserver. Am besten geeignet für eine erste
  Installation und für Hosts ohne Shell-Zugang.
- **Der Kickstarter** (`php bin/php/console exp:kickstarter run --apply --force`) führt dieselben Schritte auf der
  Kommandozeile aus und liest jede Antwort aus `kickstart.ini` im Installationsverzeichnis. Die Datei ist die
  Spezifikation der Installation: Sie kann geprüft, aufbewahrt und wiederverwendet werden. Am besten geeignet für
  wiederholbare und unbeaufsichtigte Installationen.
- **Die Konsoleninstallation** (`php bin/php/console exp:install --apply`) erstellt die Kickstart-Konfiguration aus
  Kommandozeilenoptionen, von denen jede einen Standardwert hat, und führt damit den Kickstarter aus. Am besten
  geeignet für die schnellste Installation und für Skripte, CI und Container.

[Kapitel 4](04-installationsweg-waehlen.md) vergleicht sie im Detail und hilft Ihnen bei der Wahl.

## 1.4 Wie Sie dieses Handbuch lesen

Das Handbuch folgt der Reihenfolge, in der Sie eine Site installieren:

| Kapitel | Lesen Sie es, wenn |
|---|---|
| 1. Einführung (dieses Kapitel) | Sie neu bei Exponential sind |
| [2. Voraussetzungen](02-voraussetzungen.md) | Sie einen Rechner vorbereiten |
| [3. Den Code beschaffen](03-den-code-beschaffen.md) | Sie Exponential herunterladen |
| [4. Eine Installationsmethode wählen](04-installationsweg-waehlen.md) | Sie zwischen Assistent, Kickstarter und Konsoleninstallation entscheiden |
| [5. Der Setup-Assistent](05-setup-assistent.md), [6. Der Kickstarter](06-kickstarter.md), [7. Die Installation über die Konsole](07-installation-ueber-die-konsole.md) | Sie mit der gewählten Methode installieren |
| [8. Die Site ausliefern](08-die-website-ausliefern.md) | Sie Velocity, Apache oder nginx, HTTPS und Berechtigungen einrichten |
| [9. Datenbanken](09-datenbanken.md) | Sie SQLite, MySQL oder MariaDB, PostgreSQL, MongoDB oder Oracle wählen oder abstimmen |
| [10. Nach der Installation](10-nach-der-installation.md) | die Site installiert ist und Sie Cronjobs, Caches und Backups einrichten |
| [11. Upgrade](11-aktualisieren.md) | Sie bereits eine ältere Version betreiben |
| [12. Fehlerbehebung](12-fehlerbehebung.md) | etwas nicht funktioniert |
| [13. Sicherheitshärtung](13-sicherheitshaertung.md) | bevor die Site öffentlich wird |
| [14](14-migration-von-4x.md) bis [17](17-migrationsreferenz.md): Migration | Sie eine Site von einer älteren Produktlinie zu Exponential umziehen |

Das [Inhaltsverzeichnis](README.md) verlinkt jedes Kapitel und schlägt je nach Situation einen Lesepfad vor. Wenn Sie
nur den kürzesten Weg möchten, finden Sie in der [Installationsübersicht](../INSTALL.md) den gesamten Ablauf auf einer
Seite; dieses Handbuch erklärt jeden Schritt ausführlich, mit den Gründen, den Alternativen und den Referenzen.

Jedes Kapitel beginnt mit einer Zusammenfassung, verlinkt oben und unten seine Nachbarkapitel und endet mit einem
Abschnitt **Referenzen**, der die Dokumente dieses Repositorys und die externen Handbücher verlinkt, auf die sich
das Kapitel stützt.

## 1.5 Konventionen

**Wo Befehle ausgeführt werden.** Jeder Befehl wird im Installationsverzeichnis ausgeführt, sofern der Text nichts
anderes sagt:

```bash
cd /var/www/example.com
php bin/php/ezcache.php --clear-tag=ini
```

Ein Skript aus einem anderen Verzeichnis auszuführen, scheitert meist an fehlenden Dateien, weil die Skripte
`settings/`, `var/` und `autoload.php` relativ zum aktuellen Verzeichnis suchen.

**Die Konsole.** `bin/php/console` findet und startet jeden Befehl einer Installation. Ein Skript
`bin/php/<name>.php` ist der Konsolenbefehl `exp:<name>`, sodass diese beiden Zeilen dasselbe Programm ausführen:

```bash
php bin/php/console exp:kickstarter run --dry-run
php bin/php/kickstarter.php run --dry-run
```

Shell-Skripte in `bin/shell/` sind die Befehle `shell:<name>`, und Extensions fügen eigene unter
`ext:<extension>:` hinzu. Viele Installationen legen eine Verknüpfung im Wurzelverzeichnis an
(`ln -s bin/php/console console`), wonach `./console` dasselbe Programm ist. `php bin/php/console list` listet jeden
Befehl auf; `php bin/php/console help <command>` erklärt einen. Siehe [Konsole](../bc/6.0/konsole.md).

**Ausführen als root.** Die meisten Kommandozeilenskripte verweigern die Ausführung als Betriebssystembenutzer
`root`. Sie brechen mit dem Exit-Status 1 ab und geben aus:

```text
Running scripts as root may be dangerous.
If you think you know what you are doing, you can run this script with the
root account by appending the parameter --allow-root-user.
```

Führen Sie sie als der Benutzer aus, dem die Installation gehört, oder fügen Sie `--allow-root-user` hinzu, wenn Sie
sie als root ausführen müssen. Die Verweigerung hat einen praktischen Grund: Dateien, die ein root-Prozess anlegt,
gehören root, und der Webserver kann sie dann nicht ändern; die Site scheitert später an Berechtigungsfehlern, die
scheinbar nichts damit zu tun haben (siehe
[Fehlerbehebung](12-fehlerbehebung.md#123-berechtigungen-und-eigentümerschaft)). Die beiden Installer `exp:kickstarter` und
`exp:install` verweigern root nicht, akzeptieren aber `--allow-root-user`, damit dieselbe Befehlszeile überall
funktioniert; der Hinweis zu den Eigentumsverhältnissen gilt für sie ebenso.

**Einstellungen im Text.** Eine Einstellung wird als `[Block] Key` zusammen mit der Datei geschrieben, in der sie
steht, zum Beispiel `settings/site.ini`, `[SiteAccessSettings] CheckValidity`. Um sie zu ändern, schreiben Sie den
Block und den Schlüssel in die im Text genannte Override-Datei, niemals in die mitgelieferte Datei:

```ini
# settings/override/site.ini.append.php
[DebugSettings]
DebugOutput=enabled
```

**Platzhalter.** Text in spitzen Klammern ersetzen Sie durch Ihre Werte: `<siteaccess>`, `<web user>`, `<version>`.
`example.com` steht für Ihre Domain.

**Tabellen.** Das meiste Referenzmaterial steht in Tabellen; eine Zeile, deren erste Spalte ein Symptom, eine
Einstellung oder eine Option ist, ist dafür gedacht, durch Suchen auf der Seite gefunden zu werden.

**Versionen.** Das Handbuch beschreibt die 6.0-Linie mit dem Stand 6.0.15. Das neueste veröffentlichte Release-Tag
ist `v6.0.14`; 6.0.15 ist der Branch `main`, bis sein Tag veröffentlicht ist, auch wenn `lib/version.php` ihn bereits
als `6.0.15` mit dem Status `stable` benennt. Wo sich eine frühere 6.0.x anders verhält, sagt der Text es; die
vollständige Geschichte steht in den [Changelogs](../../changelogs/6.0/6.0.15.md).
[Kapitel 3](03-den-code-beschaffen.md#31-versionen-tags-und-branches) zeigt, wie Sie sehen, welche Tags existieren.

## 1.6 Glossar

Die folgenden Begriffe sind die, die die Installationskapitel verwenden. Das [vollständige Glossar](../glossar.md)
enthält jeden Begriff der Dokumentation.

| Begriff | Bedeutung |
|---|---|
| Installationsverzeichnis (installation root) | Das Verzeichnis, das `index.php`, `autoload.php`, `bin/` und `settings/` enthält; das Document Root des Webservers. |
| Kernel | Der Kerncode in `kernel/` und `lib/`. |
| Extension | Ein Verzeichnis unter `extension/`, das Funktionalität hinzufügt; wird verwendet, wenn es in `ActiveExtensions[]` eingetragen ist. |
| Siteaccess | Ein Zugang zur Installation mit eigenen Einstellungen und eigenem Design. Eine Installation hat drei: `site`, `admin` und `editor`. |
| Match-Wert (match value) | Was einen Siteaccess auswählt: ein URL-Pfad, ein Port oder ein Hostname. Wird während der Installation gewählt; die Namen der Siteaccesses bleiben `site`, `admin` und `editor`. |
| Design | Eine Sammlung von Templates, Styles und Bildern; ein Siteaccess wählt, welche Designs in welcher Reihenfolge durchsucht werden. |
| Site-Paket | Das Archiv aus Inhalten, Klassen und Design, das beim Setup installiert wird; standardmäßig `sevenx_multisite`. |
| INI-Datei | Eine Einstellungsdatei aus `[Block]`- und `Key=Value`-Zeilen; gelesen in der Reihenfolge, eine spätere Datei gewinnt: Standardwerte, Siteaccess, Extensions, Override. |
| Override (Einstellungen) | Eine Datei in `settings/override/` oder `settings/siteaccess/<name>/`, deren Werte die Standardwerte ersetzen. |
| Setup-Assistent | Der Browser-Installer, aktiv solange `CheckValidity=true` gilt. |
| Kickstarter | Der Kommandozeilen-Installer, der die Schritte des Assistenten aus `kickstart.ini` beantwortet. |
| `exp:install` | Der Konsolen-Installer mit einem einzigen Befehl; er schreibt eine temporäre `kickstart.ini` und führt den Kickstarter aus. |
| Datenbankaktion (database action) | Was die Installation mit einer Datenbank macht, die bereits Daten enthält: `remove`, `ignore` oder `skip`. |
| Konsole | `bin/php/console`, das jeden Befehl ausführt (`exp:<name>` für `bin/php/<name>.php`). |
| Velocity | Der Anwendungsserver von Exponential, gestartet mit `exp:velocity`; die Engine `qbix`. |
| Engine | Das Programm, das die Site ausliefert: `qbix` (Velocity), `php` (der eingebaute Server von PHP) oder `frankenphp`. |
| PHP-FPM | Der Prozessmanager von PHP, der PHP hinter Apache oder nginx ausliefert. |
| Composer | Der PHP-Paketmanager, der die Bibliotheken und Extensions installiert, die `composer.json` aufführt. |
| Packagist | Das Paket-Repository, das Composer standardmäßig liest. |
| Zeta Components | Die PHP-Bibliotheken (`zetacomponents/*`), auf denen der Kernel aufbaut. |
| Autoload | Die erzeugten Klassenzuordnungen (`autoload/ezp_kernel.php`, `var/autoload/ezp_extension.php`), die PHP mitteilen, wo jede Klasse liegt. |
| Cache | Gespeicherte Ergebnisse, die Seiten schneller machen (INI, Template, Inhaltsansicht, Antwort-Cache von Velocity); geleert mit `ezcache.php`. |
| Wartungsmodus | Die Site beantwortet jede Seite mit einem Hinweis (HTTP 503), während sie installiert oder gewartet wird; aktiv, solange `var/maintenance.json` existiert. |
| `var/` | Das Verzeichnis für alles, was Exponential schreibt: Caches, Logs, hochgeladene Dateien, die SQLite-Datenbank. |

## Referenzen

In diesem Repository:

- [Installationsübersicht](../INSTALL.md): die gesamte Installation auf einer Seite.
- [Projekt-README](../../../README.md): was Exponential ist und wie es ausgeliefert wird.
- [Glossar](../glossar.md): jeder Begriff der Dokumentation.
- [Erste Schritte](../anleitungen/erste-schritte.md): die erste Stunde nach einer Installation.
- [Der Setup-Assistent und der Redaktions-Siteaccess](../funktionen/6.0/einrichtungsassistent-und-editor-siteaccess.md).
- [Standardwerte einer sauberen Installation](../funktionen/6.0/standardwerte-einer-sauberen-installation.md).
- [Extensions](../funktionen/6.0/erweiterungen/README.md) und die [Anleitung zu Extensions](../anleitungen/extensions.md).
- [Templates und Design](../anleitungen/templates-und-design.md).
- [Platzierung von INI-Overrides](../spezifikationen/6.0/ini-override-platzierungen.md) und
  [exp:ini](../funktionen/6.0/exp-ini-befehl.md).
- [Konsole](../bc/6.0/konsole.md).
- [Velocity-Engines](../bc/6.0/velocity-engines.md).
- [Umbenennung in Exponential](../funktionen/6.0/umbenennung-in-exponential.md).
- [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md).
- Code: `lib/version.php`, `settings/site.ini` (`[SiteAccessSettings]`, `[ExtensionSettings]`),
  `kernel/setup/steps/ezstep_site_access.php`, `kernel/setup/steps/ezstep_site_details.php`,
  `kernel/setup/steps/ezstep_create_sites.php` (`createEditorSiteAccess()`), `settings/package.ini`.

Extern:

- Quellcode: [github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential).
- Composer-Paket: [packagist.org/packages/se7enxweb/exponential](https://packagist.org/packages/se7enxweb/exponential).
- Online-Handbuch: [Installation, technisches Handbuch 6.x](https://exponential.doc.exponential.earth/Exponential/Technical-manual/6.x/Installation.html).
- PHP-Handbuch: [php.net/manual](https://www.php.net/manual/en/).
- Composer-Dokumentation: [getcomposer.org/doc](https://getcomposer.org/doc/).
- 7x: [se7enx.com](https://se7enx.com).

[Inhalt](README.md) · Weiter: [2. Voraussetzungen](02-voraussetzungen.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
