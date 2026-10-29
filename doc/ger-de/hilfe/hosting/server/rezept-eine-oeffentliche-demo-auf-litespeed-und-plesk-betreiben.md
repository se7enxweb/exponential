---
title: "Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben"
audience: hosting, admin
category: hosting
order: 130
summary: Ein Rezept Schritt für Schritt für eine öffentliche Exponential-Demo auf einem Plesk-Server mit LiteSpeed und lsphp, so wie die immer aktuelle Demo des Projekts selbst läuft - ein eigener Systembenutzer, ein SQLite-Build alle zwei Stunden aus dem aktuellen Code, exp:install im Demomodus mit dem veröffentlichten Passwort, SetEnv EXP_DEMO_MODE 1 in .htaccess, ein Austausch des Document Root und die Prüfungen, die belegen, dass der Demomodus an ist.
keywords: öffentliche Demo, Demomodus, EXP_DEMO_MODE, LiteSpeed, lsphp, LSAPI, Plesk, htaccess, SetEnv, SQLite, exp:install, Cron, Neuaufbau, Austausch, Vorladen, security.log, demo/mode, sapi=litespeed, publishing, public demo, demo mode, cron, rebuild, swap, preload
type: help
---
# Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben

Dieses Rezept ist für einen Hoster oder ein Projektmitglied, der eine **öffentliche Demo** betreibt: eine Exponential-Website, an der sich
jeder mit einem veröffentlichten Administratorpasswort anmelden darf, nach Zeitplan aus dem aktuellen Code neu aufgebaut. Es folgt der
Form der immer aktuellen Demo des Projekts selbst, die auf einem Plesk-Server mit LiteSpeed und `lsphp` läuft. Dieselben Schritte
funktionieren mit Apache und PHP-FPM; die Unterschiede sind vermerkt.

Die Demo läuft im **Demomodus** (die Umgebungsvariable `EXP_DEMO_MODE=1`): Niemand kann ein Administratorpasswort ändern oder
zurücksetzen, sodass der nächste Besucher sich weiterhin anmelden kann, und die Installer akzeptieren das veröffentlichte Passwort. Was der
Demomodus tut und was nicht: [Demomodus](../../../bc/6.0/demomodus.md).

> **Warnung:** Eine öffentliche Demo übergibt ihre Administration Fremden. Geben Sie ihr eine eigene Domain, einen eigenen Systembenutzer,
> einen eigenen PHP-Handler und eine eigene Datenbankdatei, keine Zustellung von Mail an echte Adressen, keine Zugangsdaten, die irgendwo
> anders funktionieren, und bauen Sie sie oft neu auf. Der Demomodus schützt nur das Administratorpasswort.

## Inhalt

1. [Was Sie bauen](#was-sie-bauen)
2. [Schritt 1: das Abonnement](#schritt-1-das-abonnement)
3. [Schritt 2: das Build-Skript](#schritt-2-das-build-skript)
4. [Schritt 3: Demomodus für Web-Anfragen](#schritt-3-demomodus-für-web-anfragen)
5. [Schritt 4: der Zeitplan](#schritt-4-der-zeitplan)
6. [Schritt 5: prüfen](#schritt-5-prüfen)
7. [Varianten](#varianten)
8. [Verwandte Seiten](#verwandte-seiten)

## Was Sie bauen

- Ein Plesk-Abonnement für die Domain der Demo, mit eigenem Systembenutzer und PHP, das von LiteSpeed ausgeliefert wird (`lsphp`, der
  LSAPI-Handler).
- Ein Build-Skript, das dieser Benutzer alle zwei Stunden aus Cron ausführt und das:
  1. den aktuellen Code in ein neues Build-Verzeichnis neben der Live-Website klont und `composer install` ausführt;
  2. ihn mit `exp:install` auf SQLite installiert, mit für jeden Schritt exportiertem `EXP_DEMO_MODE=1` und dem veröffentlichten Passwort
     (`publishing`) unverändert übergeben;
  3. abbricht, wenn der Installer stattdessen ein Passwort erzeugt hat (das bedeutet, dass die Variable ihn nicht erreicht hat);
  4. `SetEnv EXP_DEMO_MODE 1` an die `.htaccess` des neuen Builds anhängt;
  5. den neuen Build als Document Root einsetzt und den alten entfernt;
  6. die neue Website mit dem Vorlader des Kernels aufwärmt.
- Ein gescheiterter Build wird als `failed-<date>` neben der Website behalten, mit seinem Log; die Live-Website bleibt, wie sie war.

Das allgemeine Skript ist [`install-demo-sqlite.sh`](../../../../eng-us/examples/demo-mode/install-demo-sqlite.sh); die Crontab ist
[`crontab-demo`](../../../../eng-us/examples/demo-mode/crontab-demo).

## Schritt 1: das Abonnement

1. Legen Sie das Abonnement für die Domain der Demo an. Notieren Sie seinen Systembenutzer (zum Beispiel `demo`) und sein Verzeichnis.
2. **PHP:** Wählen Sie für die Domain den PHP-Handler von LiteSpeed (`lsphp`) mit einer unterstützten PHP-Version. Für PHP-FPM siehe
   stattdessen [Varianten](#varianten).
3. **Shell und Cron:** Der Systembenutzer braucht eine Shell für die geplante Aufgabe nur, wenn der Build als Cronjob dieses Benutzers
   läuft; die **Scheduled Tasks** von Plesk können ihn ausführen, ohne dem Benutzer eine interaktive Shell zu geben.
4. **Mail:** Lassen Sie die Demo nichts an echte Adressen senden. Ihre Benutzer sind erfunden, mit Adressen in `example.invalid`, und die
   Administratoradresse der Installation ist ebenfalls eine solche.
5. Richten Sie den Document Root auf ein Verzeichnis, das das Skript austauscht (zum Beispiel `httpdocs`), und halten Sie Builds in einem
   benachbarten Verzeichnis (zum Beispiel `builds/`), das nicht ausgeliefert wird.

## Schritt 2: das Build-Skript

Kopieren Sie [`install-demo-sqlite.sh`](../../../../eng-us/examples/demo-mode/install-demo-sqlite.sh) in das Build-Verzeichnis, machen Sie es nur
für den Benutzer der Website ausführbar und setzen Sie seine Variablen: das Verzeichnis der Website, den Namen des Document Root, die URL,
den Branch, das veröffentlichte Passwort. Die wesentlichen Zeilen sind:

```sh
export EXP_DEMO_MODE=1
php bin/php/install.php --db=sqlite --url="$URL" --password="$DEMO_PASSWORD" --email=demo@example.invalid
[ -e var/log/initial-admin-password ] && fail "the installer generated an administrator password"
```

- `export` gibt den Demomodus an `exp:install` und jeden folgenden Befehl. Im Demomodus installiert der Installer ein bekanntes oder kurzes
  Administratorpasswort (`publish`, `publishing`) **so, wie es übergeben wird**; ohne ihn würde das Passwort durch ein erzeugtes ersetzt
  und nach `var/log/initial-admin-password` geschrieben. Das Skript behandelt diese Datei als gescheiterten Build, weil die veröffentlichte
  Anmeldung nicht funktionieren würde.
- Kein Zurücksetzen des Passworts nach der Installation: Es ist nicht nötig, und `exp:resetuserpassword` verweigert im Demomodus ohnehin,
  ein Administratorpasswort zu ändern.
- Das Skript enthält kein Geheimnis: Das Demo-Passwort ist absichtlich öffentlich. Halten Sie echte Zugangsdaten (einen API-Schlüssel,
  ein Mail-Relay) aus ihm und aus der Demo heraus.

## Schritt 3: Demomodus für Web-Anfragen

LiteSpeed liest `.htaccess` wie Apache, und `SetEnv` erreicht `lsphp` als Umgebungsvariable, die PHP mit `getenv()` liest. Der Build hängt
an die `.htaccess` des neuen Builds an:

```apache
<IfModule mod_env.c>
    SetEnv EXP_DEMO_MODE 1
</IfModule>
```

Die Datei ist [`htaccess-setenv.conf`](../../../../eng-us/examples/demo-mode/htaccess-setenv.conf). Weil jeder Build mit einer frischen Kopie von
`.htaccess_root` beginnt, hängt das Skript sie bei jedem Build an. Setzen Sie sie stattdessen in die **Additional Apache directives** des
Abonnements, bleibt sie außerhalb des Builds, sofern Ihr LiteSpeed diese Direktiven für die Domain liest.

## Schritt 4: der Zeitplan

Die Crontab des Benutzers der Website trägt `EXP_DEMO_MODE=1` in ihrer ersten Zeile, sodass die Cronjobs des Kernels und der Neuaufbau im
Demomodus laufen, dann die Cronjob-Zeilen von `exponential.cron` und den Neuaufbau alle zwei Stunden:

```cron
EXP_DEMO_MODE=1
15 */2 * * * /var/www/demo.example.com/builds/install-demo-sqlite.sh >> /var/www/demo.example.com/builds/install.log 2>&1
```

Die **Scheduled Tasks** von Plesk schreiben dieselbe Crontab für den Benutzer des Abonnements; fügen Sie die Zeile mit der Variablen auch
dort hinzu, oder exportieren Sie sie im Skript (das tut es).

## Schritt 5: prüfen

Nach dem ersten Build:

- **Die Anmeldung:** `admin` mit dem veröffentlichten Passwort meldet sich an `/admin` an. Die Administration zeigt auf jeder Seite das
  Banner **Demo-Modus**.
- **Die Passwortseiten:** **Passwort ändern** für `admin` sagt, dass das Passwort nicht geändert werden kann; die Seite für vergessene
  Passwörter sagt jedem Besucher, dass Administratorpasswörter nicht zurückgesetzt werden können (in den Designs des Kernels und in einem
  Website-Design, das die Seite überschreibt, sobald dessen Release mit dem Hinweis installiert ist).
- **Das Log:** `grep 'demo/mode' var/log/security.log` zeigt eine Zeile pro Prozess, mit `sapi=litespeed` für die Web-Anfragen und
  `sapi=cli` für Cron und den Build:

  ```
  [ Oct 19 2026 18:00:00 ] [203.0.x.x] demo/mode on pid=4711 sapi=litespeed EXP_DEMO_MODE=1: administrator passwords cannot be changed or reset, ...
  ```

- **Das Audit:** **Audit** in der Administration listet Ereignisse `system.demo.mode` auf.
- **Das Build-Log:** Die letzte Zeile eines guten Builds ist `PASS: demo rebuilt (<date>)`; ein gescheiterter Build wird als
  `builds/failed-<date>` mit seiner Installationszusammenfassung behalten.

Wenn eines davon fehlt: [Probleme mit dem Demomodus](../runbooks/probleme-mit-dem-demomodus.md).

## Varianten

| Server | Wohin die Variable gehört |
|---|---|
| Apache mit PHP-FPM (der FPM-Handler von Plesk) | dieselben Zeilen in `.htaccess` oder `env[EXP_DEMO_MODE] = 1` im Pool der Domain ([`php-fpm-pool-demo.conf`](../../../../eng-us/examples/demo-mode/php-fpm-pool-demo.conf)); Plesk schreibt die Pool-Datei, nutzen Sie also seine PHP-Einstellungen für die Domain, wo es sie anbietet |
| nginx mit PHP-FPM | der Pool oder `fastcgi_param EXP_DEMO_MODE 1` ([`nginx-fastcgi-demo.conf`](../../../../eng-us/examples/demo-mode/nginx-fastcgi-demo.conf)) |
| Exponential Velocity | `export EXP_DEMO_MODE=1` in den Site-Envvars der Demo `/etc/vc/sites-available/<site>.envvars`: `exp:velocity start` gibt die Variable bei jedem Start an den Server dieser Installation weiter, sodass ein Neustart oder ein Deploy aus jeder beliebigen Shell sie behält (die Umgebung der eigenen Unit der Demo, [`velocity-demo-mode.conf`](../../../../eng-us/examples/demo-mode/velocity-demo-mode.conf), funktioniert ebenfalls). Niemals die gemeinsame `/etc/vc/envvars`, die der Server jeder Installation liest und die die Worker für den Demomodus ablehnen |
| cPanel | die `.htaccess` der Domain (Apache oder LiteSpeed) und die Zeile mit der Variablen in den Cronjobs des Kontos |
| Ein Container | [`docker-demo.env`](../../../../eng-us/examples/demo-mode/docker-demo.env) nur für den Dienst der Demo |

## Verwandte Seiten

- [Demomodus: der Hinweis zum Upgrade](../../../bc/6.0/demomodus.md)
- [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md), [Probleme mit dem Demomodus](../runbooks/probleme-mit-dem-demomodus.md),
  [Häufige Fragen zum Demomodus](../../demomodus-faq.md)
- [Beispiele für den Demomodus](../../../beispiele/demomodus/README.md)
- [LiteSpeed und OpenLiteSpeed](litespeed-und-openlitespeed.md), [Plesk](../control-panels/plesk.md)
- [exp:install für Hoster](../bereitstellung/exp-install-fuer-hoster.md), [Cronjobs](../betrieb/cronjobs.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
