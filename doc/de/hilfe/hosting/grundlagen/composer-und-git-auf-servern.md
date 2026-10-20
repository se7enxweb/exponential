---
title: Composer und Git auf Servern
audience: hosting, admin
category: hosting
order: 150
summary: Wie der Code von Exponential mit Composer und Git auf einen Server kommt, warum Erweiterungen Composer-Pakete sind, welcher Installationsweg zu welchem Tarif passt, was ein Hoster für Composer bereitstellen muss und was Sie auf einer laufenden Website nie leichtfertig ausführen sollten.
keywords: Composer, Composer 2, Git, git clone, composer install, composer create-project, composer require, composer update, preferred-install, allow-plugins, exponential-legacy-installer, Erweiterungen, vendor, no-dev, Release-Tags, Packagist, ezupdate, git_manager, Autoloads, Hosting, extensions, release tags, autoloads, hosting
type: help
---
# Composer und Git auf Servern

Diese Seite ist für Systemadministratoren und Support-Mitarbeitende bei einem Hosting-Anbieter, die Exponential-Websites
installieren und aktualisieren, und für Produktmanager, die entscheiden, ob Kunden Shell-Zugang mit Composer und Git
bekommen. Sie beantwortet: wie der Code ankommt, was Composer tut, was eine einfache Kopie nicht tut, welcher
Installationsweg zu welcher Art von Tarif passt und welche Befehle auf einer laufenden Website gefährlich sind. Die
Quelle ist [Kapitel 3 des Installationsbuchs, Den Code holen](../../../../en/us/install/03-getting-the-code.md).

## Inhalt

1. [Warum Composer Teil der Plattform ist](#warum-composer-teil-der-plattform-ist)
2. [Was ein Hoster bereitstellt](#was-ein-hoster-bereitstellt)
3. [Drei Wege, den Code zu holen](#drei-wege-den-code-zu-holen)
4. [Versionen und Tags](#versionen-und-tags)
5. [Erweiterungen sind Pakete](#erweiterungen-sind-pakete)
6. [Sicher aktualisieren](#sicher-aktualisieren)
7. [Updates aus der Administration](#updates-aus-der-administration)
8. [Checkliste](#checkliste)
9. [Verwandte Seiten](#verwandte-seiten)

## Warum Composer Teil der Plattform ist

Exponential wird mit [Composer](https://getcomposer.org/) 2 installiert. Composer lädt die Zeta Components herunter,
jede Erweiterung, die `composer.json` verlangt, und auf Wunsch Velocity. Ohne Composer fehlt `vendor/`, und die Website
kann nicht starten. Nach `composer install` führt das Skript `legacy-scripts` den Befehl
`php bin/php/ezpgenerateautoloads.php` aus, der die Klassenzuordnungen erzeugt
([Kapitel 2.7](../../../../en/us/install/02-requirements.md#27-composer)).

## Was ein Hoster bereitstellt

| Element | Warum |
|---|---|
| Composer 2, aktuell | `composer.json` führt das Installer-Plugin unter `config.allow-plugins`, das Composer ab 2.2 liest |
| Das PHP der Website als PHP von Composer | Composer prüft `php` und `ext-*` gegen das PHP, mit dem es läuft; ein neueres PHP auf der Kommandozeile wählt Versionen, die das Web-PHP nicht ausführen kann |
| Ausgehendes HTTPS vom Server | Composer lädt von Packagist und GitHub |
| Git | für den empfohlenen Weg über Klone und für Updates von Klonen |
| Festplattenplatz für die Historie | ein Klon des Kernels mit seiner ganzen Historie ist etwa 370 MB groß; Klone von Erweiterungen kommen hinzu |

Auf Servern mit mehreren PHP-Versionen führen Sie Composer mit dem PHP der Website aus, zum Beispiel
`/opt/plesk/php/8.5/bin/php /usr/local/bin/composer install` (passen Sie beide Pfade an Ihren Server an).

## Drei Wege, den Code zu holen

Aus [Kapitel 3.2](../../../../en/us/install/03-getting-the-code.md#32-three-ways-to-get-the-code):

| Weg | Ergibt | Passt für |
|---|---|---|
| A. `git clone` und `composer install` (empfohlen) | den Kernel und jede Erweiterung von se7enxweb als Git-Klone mit vollständiger Historie | Kunden, die Websites bauen und ändern; Entwicklung und Staging; Managed-Websites, die ein Hoster mit Git pflegt |
| B. `composer create-project se7enxweb/exponential` | Release-Archive ohne Historie | Websites, die nur betrieben werden, schnelle Bewertungen, einfache Bereitstellung |
| C. `composer require se7enxweb/exponential` in ein leeres Verzeichnis | den Kernel als Paket des eigenen Projekts des Kunden | Installationen, die als eigenes Projekt verwaltet werden |

Sagen Sie Composer für Weg A einmal pro Benutzerkonto, Pakete `se7enxweb/*` als Klone zu installieren:

```bash
composer config --global 'preferred-install.se7enxweb/*' source
git clone https://github.com/se7enxweb/exponential.git
cd exponential
composer install
```

Für Weg B auf einem Produktivserver fügen Sie `--no-dev` hinzu, das die Testwerkzeuge weglässt:

```bash
composer create-project se7enxweb/exponential:v6.0.14 exponential --no-dev
```

> **Hinweis**
> Auf PHP 8.0 lässt sich kein veröffentlichtes Tag installieren: Die Tags `v6.0.8` bis `v6.0.14` verlangen PHP 8.1.
> Nehmen Sie die Linie 6.0.15 (`dev-main`), bis ihr Tag veröffentlicht ist. Die Tags `v6.0.13` und `v6.0.14` brauchen
> auf PHP 8.1 bis 8.3 außerdem zusätzliche Schritte wegen ihrer PHPUnit-Anforderung
> ([Kapitel 3.3](../../../../en/us/install/03-getting-the-code.md#33-installing-on-php-80-to-83)).

## Versionen und Tags

- Exponential wird als Composer-Paket `se7enxweb/exponential` aus
  [github.com/se7enxweb/exponential](https://github.com/se7enxweb/exponential) veröffentlicht. Release-Tags sind `v6.0.0`
  und später in der Linie 6.0; `main` trägt die aktuelle Entwicklung.
- **Ein Release-Tag ändert sich nie.** Eine Korrektur kommt immer als nächste Version, sodass eine fest eingestellte
  Version jedes Mal dieselben Dateien liefert: die richtige Wahl für den Produktivbetrieb.
- Sortieren Sie Versionen nach Version, nie nach Namen: Nach Namen sortiert steht `v6.0.10` vor `v6.0.8`.

  ```bash
  git tag -l 'v*' --sort=version:refname | tail -5
  ```

## Erweiterungen sind Pakete

Jede Erweiterung außer den vier, die im Repository des Kernels mitgeliefert werden (`ezjscore`, `ezoe`, `ezformtoken`,
`expservices`), ist ein Composer-Paket, das das Installer-Plugin in `extension/<name>` ablegt. `composer.json`
entscheidet also, welche Erweiterungen eine Installation hat und in welchen Versionen
([Kapitel 3.5](../../../../en/us/install/03-getting-the-code.md#35-extensions-are-composer-packages)).

Eine hinzuzufügen sind drei Schritte:

```bash
composer require se7enxweb/ezfind
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezcache.php --clear-tag=ini
```

und sie in `[ExtensionSettings] ActiveExtensions[]` zu aktivieren (in der Administration unter **Setup > Extensions**
oder in `settings/override/site.ini.append.php`); starten Sie Velocity neu, wenn es die Website ausliefert.

Der Block `suggest` von `composer.json` listet optionale Erweiterungen auf, die ein Hoster als Zusatzleistungen anbieten
kann, darunter `ezfind` (Solr-Suche), `sevenx_valkey_cache` (Cache auf Valkey oder Redis), `ezscriptmonitor` und `hcaptcha`.

## Sicher aktualisieren

> **Warnung**
> Führen Sie `composer update` auf einer laufenden Website nicht leichtfertig aus. Es hebt jedes Paket auf die neueste
> Version, die seine Einschränkung erlaubt. Aktualisieren Sie gezielt ein Paket (`composer update se7enxweb/<package>`)
> und lesen Sie zuerst seine Release Notes.

- Klone, die Composer selbst angelegt hat, sind sicher: Composer weigert sich, einen Klon mit nicht committeten
  Änderungen oder nicht gepushten Commits zu aktualisieren ([Wenn Composer sich weigert](../../../../en/us/help/develop-with-git.md#when-composer-refuses)).
- Ein **von Hand** angelegter Klon an der Stelle eines Pakets, das Composer als Archiv installiert hat, ist nicht
  sicher: Composer behandelt den Ordner weiter als Archiv und ersetzt ihn, samt Historie und Commits. Nutzen Sie
  stattdessen `composer reinstall <package>`, nachdem Sie `preferred-install` gesetzt haben.
- Den Code zu bewegen ist die Hälfte eines Upgrades; die Update-Dateien der Datenbank sind die andere Hälfte
  ([Kapitel 11](../../../../en/us/install/11-upgrading.md)).
- Sichern Sie vor jedem Update ([Was gesichert werden muss](../betrieb/backups-was-gesichert-werden-muss.md)).

Ein sicheres Update einer Website auf Basis von Klonen:

```bash
git fetch origin --tags
git merge v6.0.<n>                 # or origin/main
composer install
```

## Updates aus der Administration

Die Erweiterung `ezupdate`, die `composer.json` verlangt, ist ein Paketmanager in der Administration (**Setup >
Updates and packages**) und auf der Kommandozeile. **Aktualisieren und Installieren sind aus, bis sie eingeschaltet
werden**, in `settings/override/ezupdate.ini.append.php` (`[UpdateSettings] AllowUpdate`, `AllowInstall`), weil
Composer `vendor/` und jedes Paket, das es verwaltet, neu schreibt ([ezupdate](../../../../en/us/features/6.0/extensions/ezupdate.md)).

Für einen Hoster ist das eine Grundsatzentscheidung pro Tarif: aus lassen, wo der Hoster die Updates verwaltet, oder
einschalten für Kunden, die ihre eigenen verwalten. Siehe [Automatische Updates mit ezupdate](../betrieb/automatische-updates-mit-ezupdate.md).

## Checkliste

- [ ] Composer 2, aktuell, für den Benutzer der Website verfügbar.
- [ ] Composer läuft mit der PHP-Version der Website.
- [ ] Ausgehendes HTTPS zu Packagist und GitHub erlaubt.
- [ ] Produktivinstallationen nutzen `--no-dev`.
- [ ] Kunden-Websites fest auf Release-Tags eingestellt, es sei denn, sie folgen `main` absichtlich.
- [ ] Kein leichtfertiges `composer update` auf laufenden Websites; ein Backup vor jedem Update.
- [ ] Eine Richtlinie pro Tarif für `AllowUpdate` und `AllowInstall` von `ezupdate`.

## Verwandte Seiten

- [Kapitel 3: Den Code holen](../../../../en/us/install/03-getting-the-code.md)
- [Mit Git entwickeln](../../../../en/us/help/develop-with-git.md)
- [Kunden-Websites aktualisieren](../betrieb/kunden-websites-aktualisieren.md) und [Änderungen ausrollen](../betrieb/aenderungen-ausrollen.md)
- [Anleitung zu Erweiterungen](../../../../en/us/guides/extensions.md)
- [Freie Dokumentation](../../../../en/us/help/free-documentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
