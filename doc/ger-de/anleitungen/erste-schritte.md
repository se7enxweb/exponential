# Erste Schritte: Exponential installieren und in wenigen Minuten eine funktionierende Site haben

Diese Anleitung richtet sich an alle, die mit Exponential beginnen: an Redakteure, die eine Site zum Ausprobieren
möchten, an Entwickler und an Betreiber. Vorkenntnisse brauchen Sie keine. Am Ende haben Sie eine installierte Site,
sind an der Administrationsoberfläche angemeldet, haben einen ersten Inhalt veröffentlicht und ein Template geändert
und die Änderung auf der Site gesehen. Rechnen Sie mit zehn bis fünfzehn Minuten. Die Installation selbst dauert mit
SQLite etwa eine Minute.

> **Warnung**
> **`.htaccess` bremst jede Anfrage.** Sind `.htaccess`-Dateien erlaubt (`AllowOverride` alles außer `None`), geschweige
> denn genutzt, sucht Apache sie bei jeder Anfrage in jedem Verzeichnis des Pfads und liest und wertet sie aus, und
> die Ladezeiten der Seiten steigen drastisch ([gemessen](setup-dateien-von-der-konsole.md#gemessen-htaccess-gegen-regeln-im-virtuellen-host)). Die einzige wirkliche Abhilfe ist, die
> Funktion für den virtuellen Host abzuschalten: `AllowOverride None` im virtuellen HTTPS-Host, und die Regeln in
> der Konfiguration des virtuellen Hosts selbst als mod_rewrite-Regeln. Das ist in Geschwindigkeitstests im
> Produktivbetrieb weit schneller und die Zeit für die Optimierung einer Produktivkonfiguration unbedingt wert.
> Stellen Sie sofort um: Übernehmen Sie die Regeln des [Beispiels für einen virtuellen HTTPS-Host](../beispiele/webserver/apache-virtueller-host.md) in
> Ihren virtuellen Host (mit dem statischen Cache auch [`staticcache_offload.conf`](../../eng-us/examples/staticcache_offload.conf)), setzen Sie
> `AllowOverride None`, führen Sie `apachectl configtest` aus, laden Sie Apache neu und testen Sie die Website.
> Dieselben Regeln für andere Server: [nginx](../beispiele/webserver/nginx.md), [LiteSpeed](../beispiele/webserver/litespeed.md), [Caddy](../beispiele/webserver/caddy.md), [IIS](../beispiele/webserver/iis.md),
> [Velocity](../beispiele/webserver/velocity.md) (eingebaut); wohin Regeln für virtuelle Hosts in einem Control Panel gehören:
> [Plesk](../hilfe/hosting/control-panels/plesk.md), [cPanel](../hilfe/hosting/control-panels/cpanel.md), [DirectAdmin](../hilfe/hosting/control-panels/directadmin.md), [ISPConfig](../hilfe/hosting/control-panels/ispconfig.md).

Jeder Befehl wird im Wurzelverzeichnis der Installation ausgeführt (dem Verzeichnis, das `console`, `index.php` und
`settings/` enthält). Befehle, die mit `./console` beginnen, sind dieselben Programme wie `php bin/php/<name>.php`.

## 1. Was Sie brauchen

| Voraussetzung | Einzelheiten |
|---|---|
| PHP | 8.0 oder neuer; `composer.json` erlaubt 8.0 bis 8.8. Exponential Velocity, der eingebaute Server aus Schritt 4, läuft ebenfalls unter 8.0 (ab seinem Release 0.0.4.54). Das PHP der Kommandozeile braucht den PDO-Treiber Ihrer Datenbank (für den Anfang genügt SQLite). |
| Composer | Version 2.x, nur für die unten beschriebenen Installationswege mit Composer |
| Eine Datenbank | für den Anfang keine: Standard ist SQLite in einer Datei. MySQL oder MariaDB, PostgreSQL, MongoDB und Oracle wählen Sie mit `--db=`. |
| Ein Webserver | für den Produktivbetrieb ein beliebiger (Apache mit der mitgelieferten `.htaccess` oder PHP-FPM hinter einem Server). Um die Site sofort auszuprobieren, bringt Exponential einen eigenen mit, siehe Schritt 4. |
| `memory_limit` und `date.timezone` | setzen Sie beide in der `php.ini`; die Prüfungen des Installationsprogramms verlangen sie |

Prüfen Sie das PHP, das Sie verwenden werden:

```bash
php -v
```

Erwartet: eine Zeile, die mit `PHP 8.` beginnt.

## 2. Die Dateien beschaffen

Wählen Sie einen Weg. Alle drei ergeben dieselbe Installation.

**A. Git-Klone mit Historie** (empfohlen, wenn Sie eine Website bauen): Der Kernel und jede se7enxweb-Extension
kommen als Git-Klone an, sodass Sie sehen, was sich geändert hat, der Entwicklung folgen und Korrekturen zurückschicken können.
[Mit Git entwickeln](../hilfe/mit-git-entwickeln.md) erklärt, warum und wie.

```bash
composer config --global 'preferred-install.se7enxweb/*' source
cd /var/www
git clone https://github.com/se7enxweb/exponential.git exponential
cd exponential
git switch -c my-site
composer install
```

**B. Composer-Projekt** (der schnellste Weg zu einer Site, die Sie nur ausprobieren oder betreiben möchten):

```bash
cd /var/www
composer create-project se7enxweb/exponential exponential
cd exponential
```

**C. Pakete.** Einzelne Ergänzungen (Inhalte, Klassen, Templates, Dateien) sind `.ezpkg`-Archive, die Sie nach der
Basisinstallation mit `ezpm` installieren, siehe [ezpm](../funktionen/6.0/ezpm-paketmanager-cli.md) und
[Pakete vergleichen und importieren](../funktionen/6.0/pakete-vergleichen-und-importieren.md). Das Installationsprogramm in Schritt 3 installiert selbst
ein Site-Paket (Standard `sevenx_multisite`), für eine erste Site brauchen Sie also nichts weiter.

Prüfen Sie, ob die Konsole läuft:

```bash
./console --version
```

Erwartet: Die erste Zeile lautet `console (Exponential) 6.0.15stable` (oder die Version, die Sie installiert haben), und der Text endet
mit `Written by 7x (se7enx.com) and the Exponential contributors.`
Wenn Sie als `root` arbeiten, ergänzen Sie `--allow-root-user` bei den Skripten, die danach verlangen. Die Meldung des Skripts sagt es Ihnen.

## 3. Installieren

### 3.1 Der eine Befehl

```bash
./console exp:install --apply --random-password --url=http://localhost:8087
```

Dieser Befehl installiert das Multisite-Site-Paket auf SQLite, in `eng-US`, mit den Siteaccesses `site` (der öffentlichen Site) und
`admin`, die am ersten Teil der Adresse unterschieden werden. Das Administratorpasswort wird erzeugt (24 Zeichen), einmal am Ende
angezeigt und außerdem in `var/log/initial-admin-password` geschrieben; `--random-password` verlangt das ausdrücklich.
Ein eigenes Passwort mit `--password=...` wird so übernommen, wie Sie es angeben, wenn es mindestens 10 Zeichen hat und kein
allgemein bekanntes wie `publish` oder `admin` ist; andernfalls ersetzt es ein erzeugtes, und die Zusammenfassung weist darauf hin.
Die Installation verweigert es, über eine bestehende Installation zu installieren, sofern Sie nicht `--force` angeben, und `--help` listet alle Optionen auf.

Erwartet am Ende (die Werte weichen ab):

```text
================================================================
  Exponential is installed
================================================================
  Installed:     <date> (<seconds>s)
  Site:          http://localhost:8087/site/
  Admin login:   http://localhost:8087/admin/user/login
  Username:      admin
  Password:      <the generated password>
  ...
================================================================
```

Kopieren Sie jetzt die Zeilen Admin, Username und Password. Eine Kopie der verwendeten Konfiguration, mit maskierten Passwörtern, liegt in
`var/log/`; den Fortschritt der Installation finden Sie in `var/log/setup.log` ([was die Protokolle Ihnen sagen](../funktionen/6.0/installation-mit-einem-befehl.md#was-die-logs-ihnen-sagen)).

### 3.2 Andere Datenbanken

```bash
EXP_INSTALL_DB_PASSWORD='YOUR_PASSWORD' ./console exp:install --apply --db=mysql --db-host=127.0.0.1 \
    --db-name=mysite --db-user=root --db-action=remove --random-password --url=http://localhost:8087
```

`--db-action=remove` leert zuerst die angegebene Datenbank; verwenden Sie also eine Datenbank, die nichts enthält, was Sie noch brauchen. Die
Standardports und -namen jeder Datenbank-Engine stehen in [Installation mit einem Befehl](../funktionen/6.0/installation-mit-einem-befehl.md).

### 3.3 Aus einer Datei (wiederholbare Installationen)

Für dieselbe Installation auf mehreren Servern schreiben Sie die Antworten einmal auf und führen sie aus:

```bash
php bin/php/console exp:kickstarter ini        # writes kickstart.ini after a few questions
php bin/php/console exp:kickstarter run --dry-run
php bin/php/console exp:kickstarter run --apply --force
```

`--dry-run` prüft die Antworten und die Pakete und schreibt nichts. Siehe
[Kickstarter](../funktionen/6.0/kickstarter-cli.md) und, für jeden Schlüssel, [Kickstarter-CLI](../bc/6.0/kickstartercli.md).
Halten Sie Ihre `kickstart.ini` aus der Versionsverwaltung heraus; sie enthält das Datenbankpasswort.

### 3.4 Der Assistent im Browser

Öffnen Sie die Adresse der Site in einem Browser, bevor eine Installation existiert, und der Setup-Assistent startet. SQLite steht
an erster Stelle und die mitgelieferte Site ist vorausgewählt; wenn Sie also bei jedem Schritt **Weiter** drücken, wird die Standard-Site installiert. Siehe den
[Setup-Assistenten](../funktionen/6.0/einrichtungsassistent-und-editor-siteaccess.md) und [die Standardwerte einer Neuinstallation](../funktionen/6.0/standardwerte-einer-sauberen-installation.md).

## 4. Die Site ansehen

Sie brauchen einen Webserver, der unter der angegebenen Adresse antwortet. Für einen ersten Blick verwenden Sie den, den Exponential mitliefert:

```bash
./console exp:velocity start --engine=php
./console exp:velocity status --engine=php
```

Die Engine `php` ist der eigene Server von PHP und lauscht standardmäßig auf Port 8087 (`velocity.ini [PHPServerSettings] Port`); deshalb
hat Schritt 3 `--url=http://localhost:8087` verwendet. Öffnen Sie `http://localhost:8087/site/` in einem Browser: Sie sehen die Startseite
der installierten Site. Halten Sie den Server mit `./console exp:velocity stop --engine=php` wieder an.

Wenn Sie Schritt 3 mit einer anderen `--url` ausgeführt haben, verwenden Sie die Site-Adresse, die das Installationsprogramm ausgegeben hat.

Für den Produktivbetrieb verwenden Sie Apache mit PHP-FPM oder die eigenen Engines `qbix` oder `frankenphp` von Velocity: [Velocity-Engines](../bc/6.0/velocity-engines.md).
Apache braucht die mitgelieferten Rewrite-Regeln als `.htaccess`: `./console exp:setup:htaccess --apply` kopiert sie,
und `./console exp:setup:status` zeigt, wie es um `.htaccess` und `config.php` steht
([.htaccess und config.php von der Konsole aus einrichten](setup-dateien-von-der-konsole.md)).
Wenn eine Seite einen Fehler zeigt, verbirgt der Cache noch nichts; lesen Sie `var/log/error.log` und siehe
[Eine Installation reparieren](../bc/6.0/reparatur.md).

## 5. Erste Anmeldung

1. Öffnen Sie die Admin-Anmeldeadresse aus Schritt 3, zum Beispiel `http://localhost:8087/admin/user/login`.
2. Benutzername `admin` und das Passwort, das das Installationsprogramm ausgegeben hat.
3. Sie landen auf dem Dashboard der Administrationsoberfläche.
4. Verwenden Sie den Link **Passwort ändern** im Benutzermenü der Administration und wählen Sie ein eigenes Passwort.
   Wenn das Installationsprogramm `var/log/initial-admin-password` geschrieben hat, löschen Sie diese Datei anschließend.

Das Erste, was Sie über die Menüs wissen sollten: Eine Person sieht nur die Einträge, die ihre Rollen erlauben. `admin` ist in der
Rolle Administrator und sieht alles.

## 6. Erster Inhalt

1. Öffnen Sie in der Administration den Reiter **Inhalt**, **Inhaltsstruktur**.
2. Wählen Sie den obersten Knoten (den Ordner der Startseite) und wählen Sie **Hier erstellen** mit der Klasse **Artikel** (oder **Ordner**),
   dann **Erstellen**.
3. Füllen Sie im Editor den Titel und den Text aus und drücken Sie dann **Zur Veröffentlichung schicken**.
4. Öffnen Sie `http://localhost:8087/site/` (neu laden) und suchen Sie Ihren Eintrag. Die Adresse seiner eigenen Seite zeigt die Administration
   unter **Orte**.

Klassennamen und Felder hängen vom installierten Site-Paket ab; das Inhaltsmodell wird in
[Inhaltsmodell und Bearbeitung](inhaltsmodell-und-bearbeitung.md) erklärt.

Wenn ein Eintrag veröffentlicht ist, aber in einer Liste fehlt, leeren Sie einmal den Inhalts-Cache:

```bash
php bin/php/ezcache.php --clear-tag=content --allow-root-user
```

Der Cache-Befehl und seine Gruppen: `./console exp:cache --help`; einen Probelauf jedes Leerens erhalten Sie mit `--dry-run`.

## 7. Erste Template-Änderung

Ein Template ist eine `.tpl`-Datei. Die Site zeigt das Design, das der öffentliche Siteaccess nennt; fragen Sie Exponential, welches es ist:

```bash
./console exp:ini where site/DesignSettings/SiteDesign site
```

Erwartet: die Dateien, die es setzen, in Ladereihenfolge, und `In effect:` mit dem Namen des Designs (beim Multisite-Paket lautet die
letzte Zeile `SiteDesign=media` oder ähnlich; verwenden Sie den Namen, der Ihnen angezeigt wird).

### Über die Oberfläche (sicher, ohne Dateien)

1. Menü der Administration: **Design** > **Template-Editor**.
2. Wählen Sie ein Template Ihres Designs, zum Beispiel das für die Vollansicht eines Artikels, und öffnen Sie es.
3. Fügen Sie eine sichtbare Zeile hinzu, zum Beispiel `<p>Hello from my template</p>`, und speichern Sie.
4. Laden Sie den Eintrag auf der Site neu: Die Zeile ist da.

Wie der Editor Overrides anlegt, ordnet und bearbeitet: [Template-Editor](../funktionen/6.0/template-editor-ueberschreibungen.md).

### Über Dateien (was Sie in die Versionsverwaltung aufnehmen)

1. Suchen Sie das Template, das darstellt, was Sie ändern möchten. Schalten Sie die Template-Pfad-Kommentare ein, um es im Seitenquelltext zu sehen:
   siehe [Template-Pfad-Kommentare](../funktionen/6.0/template-pfad-kommentare.md).
2. Kopieren Sie die Datei von ihrem Ort (zum Beispiel `design/standard/templates/`) unter denselben relativen Pfad im
   Verzeichnis Ihres Designs `design/<design>/templates/` (oder dessen `override/templates/`) und bearbeiten Sie die Kopie.
3. Wenn Sie einen neuen Template-Pfad hinzugefügt statt eine bestehende Datei geändert haben, leeren Sie einmal die Template-Caches:
   `php bin/php/ezcache.php --clear-all --allow-root-user`.

In welcher Reihenfolge Designs und Overrides versucht werden, steht in [Reihenfolge der Template-Overrides](../funktionen/6.0/reihenfolge-der-template-ueberschreibungen.md)
und [Ladereihenfolge der Extensions](../funktionen/6.0/ladereihenfolge-der-extensions.md).

## 8. Wenn etwas schiefgeht

| Symptom | Prüfen |
|---|---|
| `exp:install` meldet, dass eine Installation existiert | Geben Sie `--force` nur an, wenn Sie sie wirklich ersetzen wollen. |
| Eine weiße Seite oder ein Fehler 500 | Lesen Sie `var/log/error.log`. Prüfen Sie, ob `php -v` 8.0 oder neuer zeigt und ob `vendor/` existiert (führen Sie `composer install` aus). |
| Die Site-Adresse antwortet nicht | Läuft der Server (`./console exp:velocity status --engine=php`)? Passt der Port zu `--url`? |
| Sie haben das Passwort vergessen | `exp:install` hat es einmal angezeigt. Ein Passwort, das `exp:install` oder der Kickstarter erzeugt hat (keines angegeben oder ein allgemein bekanntes), steht außerdem in `var/log/initial-admin-password`; der Setup-Assistent im Browser schreibt keine solche Datei, weil Sie das Passwort dort selbst eingeben. Setzen Sie ein neues mit `php bin/php/resetuserpassword.php --allow-root-user -u admin -g`, siehe [Benutzerpasswort zurücksetzen](../funktionen/6.0/benutzerpasswort-zuruecksetzen.md). |
| Eine Änderung wird nicht angezeigt | Leeren Sie den Inhalts-Cache (Schritt 6) und laden Sie die Seite neu. |

## Siehe auch

| Sie möchten | Lesen Sie |
|---|---|
| Klassen, Objekte und Knoten verstehen | [Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md) |
| Ändern, wie Seiten aussehen | [Templates und Design](templates-und-design.md) |
| Eine Extension hinzufügen oder schreiben | [Extensions](extensions.md), [RAD-Werkzeuge](../funktionen/6.0/rad-werkzeuge.md) |
| Die Site auf einen echten Server bringen | [Bereitstellung](bereitstellung.md) |
| Die Site gesund halten | [Betrieb einer Site](betrieb-einer-website.md), [Sicherheit und Audit](sicherheit-und-audit.md) |
| Auf eine neuere Release wechseln | [Upgrade](aktualisieren.md) |
| Einstellungen auf der Kommandozeile ändern | [exp:ini](../funktionen/6.0/exp-ini-befehl.md) |
| `.htaccess` und `config.php` anlegen, eine Einstellung in `config.php` ändern | [.htaccess und config.php von der Konsole aus einrichten](setup-dateien-von-der-konsole.md) |
| Änderungen in der Administration im Blick behalten | [Audit-Protokoll](../funktionen/6.0/pruefprotokoll.md) |
| Wiederkehrende Aufgaben ausführen | [Cronjob-Konsole](../funktionen/6.0/cronjobs-in-der-konsole.md) |
| Sehen, was sich wann geändert hat | [Chronik nach Monaten](../geschichte/README.md), [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md) |
| Wissen, was eine Installation standardmäßig tut | [Standardwerte einer Neuinstallation](../funktionen/6.0/standardwerte-einer-sauberen-installation.md), [Protokolle und Startdaten des Installationsprogramms](../spezifikationen/6.0/installer-protokolle-und-startdaten.md) |
| Einen Begriff nachschlagen | [Glossar](../glossar.md) |

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
