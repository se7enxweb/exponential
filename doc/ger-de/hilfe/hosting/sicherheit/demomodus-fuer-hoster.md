---
title: Demomodus für Hoster
audience: hosting, admin
category: hosting
order: 150
summary: Wie ein Hoster eine öffentliche Exponential-Demo mit der Umgebungsvariablen EXP_DEMO_MODE=1 betreibt - sie für PHP-FPM, Apache, Exponential Velocity, Cron und Container setzen, das veröffentlichte Demo-Passwort installieren, es prüfen und sie von jeder Produktions-Website fernhalten.
keywords: Demomodus, EXP_DEMO_MODE, öffentliche Demo, Demonstrations-Website, Umgebungsvariable, PHP-FPM env, SetEnv, Velocity, systemd, Crontab, Container, exp:install, Kickstarter, security.log, system.demo.mode, Bereitstellung, demo mode, public demo, demonstration site, environment variable, container, provisioning
type: help
---
# Demomodus für Hoster

Diese Seite ist für die Systemadministratoren eines Hosters, der eine öffentliche Demonstrations-Website von Exponential betreibt, eine,
deren Anmeldung als Administrator veröffentlicht ist. Der Demomodus wird durch die Umgebungsvariable **`EXP_DEMO_MODE=1`** eingeschaltet.
Solange er an ist, kann niemand das Passwort eines Administrators ändern oder zurücksetzen (damit der nächste Besucher sich weiterhin
anmelden kann), die Installer akzeptieren das veröffentlichte Demo-Passwort, und die Administration zeigt ein Banner **Demo-Modus**.

> **Warnung:** Der Demomodus sagt der Welt, dass das Administratorpasswort bekannt ist. Setzen Sie ihn nur für eine Website, deren
> Inhalte, Benutzer und Einstellungen alle wegwerfbar sind, mit eigenem Systembenutzer, eigenem Pool und eigener Datenbank, und nie für
> einen geteilten Pool, eine serverweite Umgebung oder eine Vorlage, aus der andere Websites gebaut werden.

## Inhalt

1. [Die Regeln der Variablen](#die-regeln-der-variablen)
2. [Sie für jeden Server setzen](#sie-für-jeden-server-setzen)
3. [Eine Demo installieren](#eine-demo-installieren)
4. [Sie prüfen](#sie-prüfen)
5. [Sie von der Produktion fernhalten](#sie-von-der-produktion-fernhalten)
6. [Risiken, die Sie dem Eigentümer der Demo nennen](#risiken-die-sie-dem-eigentümer-der-demo-nennen)
7. [Verwandte Seiten](#verwandte-seiten)

## Die Regeln der Variablen

- **Nur Umgebung.** Keine INI-Einstellung, kein Anfrageparameter und keine Seite der Administration kann ihn einschalten, sodass eine
  Einstellungsdatei oder eine Datenbank, die von einer Demo kopiert wird, ihn nicht auf eine andere Website trägt.
- **Nur der genaue Wert `1`.** `0`, `true`, `yes`, `on` und ein leerer Wert lassen ihn aus.
- **Pro Prozess.** PHP liest ihn bei jedem Aufruf zuerst mit `getenv()`, dann aus `$_SERVER`. Jeder Prozess, der die Demo ausliefert oder
  pflegt, braucht ihn: das PHP des Webservers, Exponential Velocity, wenn es die Website ausliefert, Cronjobs und Konsolenbefehle. Ein
  Prozess ohne ihn läuft mit ausgeschaltetem Demomodus.

## Sie für jeden Server setzen

| Wo PHP läuft | Wie Sie sie setzen, nur für die Demo |
|---|---|
| PHP-FPM | in der eigenen Pool-Datei der Demo: `env[EXP_DEMO_MODE] = 1`, dann den Master des Pools neu laden |
| Apache (mod_php oder PHP-FPM über mod_proxy_fcgi) | im virtuellen Host der Demo: `SetEnv EXP_DEMO_MODE 1` |
| LiteSpeed / OpenLiteSpeed (lsphp) | im virtuellen Host der Demo oder in der Umgebung der externen Anwendung: `EXP_DEMO_MODE=1` |
| Exponential Velocity | die Umgebung des Prozesses, der den Server startet: `Environment=EXP_DEMO_MODE=1` in seiner systemd-Unit oder einem Drop-in, `/etc/default/exponential-velocity` für die paketierte Unit, oder `export EXP_DEMO_MODE=1` vor `./console exp:velocity start`. Die Worker erben sie |
| FrankenPHP oder ein anderer Anwendungsserver | die Umgebung des Serverprozesses |
| Cron | eine Zeile `EXP_DEMO_MODE=1` oben in der Crontab des Benutzers der Demo |
| Konsole | `export EXP_DEMO_MODE=1` im Shell-Profil des Benutzers der Demo oder vor jedem Befehl |
| Container | `ENV EXP_DEMO_MODE=1` im Image der Demo oder `-e EXP_DEMO_MODE=1` / `environment:` nur für den Dienst der Demo |

**Neustarts von Velocity.** `exp:velocity restart` und `exp:velocity deploy` starten den neuen Server mit der Umgebung der Shell, in der
sie laufen. Setzen Sie die Variable dort, wo der Dienstmanager sie hält (die Unit), sonst verwirft ein Neustart aus einer anderen Shell
stillschweigend den Demomodus. Die eigene Datei `/etc/vc/envvars` von Velocity wird für die eigenen Einstellungen des Servers gelesen,
nicht an die PHP-Worker exportiert; sie ist also nicht der Ort für `EXP_DEMO_MODE`.

## Eine Demo installieren

Setzen Sie die Variable auch für die Installation. Die Installer (Setup-Assistent, Kickstarter, `exp:install`) akzeptieren dann das
veröffentlichte Administratorpasswort, obwohl es ein bekanntes Wort und kürzer als `MinPasswordLength` ist, statt es durch ein zufälliges
zu ersetzen:

```sh
EXP_DEMO_MODE=1 ./console exp:install --db=sqlite --password='<the published demo password>' ...
```

Jedes nicht leere Passwort funktioniert, also sowohl `publish` (sieben Zeichen) als auch `publishing`; ein leeres wird weiterhin
abgelehnt. Nur das Administratorpasswort der neuen Installation wird gelockert: Jedes andere Konto behält `[UserSettings] MinPasswordLength`
(10 in der Auslieferung) und die Liste bekannter Passwörter. Setzen Sie das Passwort nach der Installation nicht mit
`exp:resetuserpassword` zurück: Im Demomodus wird das verweigert, und es ist nicht mehr nötig.

Eine Demo, die nach Zeitplan neu aufgebaut wird (alle paar Stunden, aus dem aktuellen Code), tut das in ihrem Skript für den Aufbau: mit
der Variablen installieren, die neue Website einsetzen, die Variable in der PHP-Umgebung und der Crontab der laufenden Website behalten.
Das Skript darf die Variable nicht in `config.php`, `settings/` oder irgendetwas schreiben, das auf andere Websites kopiert wird.

## Sie prüfen

- Die Administration zeigt auf jeder Seite das Banner **Demo-Modus**.
- `grep 'demo/mode' var/log/security.log` listet jeden Prozess auf, der damit gestartet ist (eine Zeile pro Prozess, mit seiner PID und
  SAPI); **Setup > Logdateien** gruppiert sie als `demo/mode`.
- Das Audit hat ein Ereignis `system.demo.mode` pro Prozess (immer festgehalten).
- Ein Konsolenbefehl gibt `Demo mode is on (EXP_DEMO_MODE=1) ...` auf der Standardfehlerausgabe aus.

## Sie von der Produktion fernhalten

- Setzen Sie sie nie serverweit (`/etc/environment`, ein globales `SetEnv`, der Standard-Pool, ein Basis-Image) oder in einer Vorlage für
  die Bereitstellung.
- Lösen Sie darauf einen Alarm aus: Eine Zeile `demo/mode` in `security.log` einer Produktions-Website oder ein Audit-Ereignis
  `system.demo.mode` bedeutet, dass die Variable einen Prozess erreicht hat, den sie nicht hätte erreichen dürfen.
- Nehmen Sie sie in die Sicherheitsprüfung auf: `grep -r EXP_DEMO_MODE` über die PHP-FPM-Pools, die virtuellen Hosts, die Dienst-Units und
  die Crontabs von Produktionsmaschinen darf nichts finden.

## Risiken, die Sie dem Eigentümer der Demo nennen

- Der Demomodus schützt nur Administratorpasswörter. Ein als Administrator angemeldeter Besucher kann weiterhin Konten anlegen, Rollen und
  Einstellungen ändern, Pakete installieren und Dateien hochladen: Bauen Sie die Demo nach Zeitplan neu auf.
- Halten Sie echte Daten, echte Mailadressen und Geheimnisse aus der Demo heraus, und schicken Sie ihre Mail in eine Datei oder eine Senke.
- Andere veröffentlichte Anmeldungen (ein Redakteur, ein Mitglied) sind nicht geschützt.
- Wenn die Variable verloren geht (ein Neustart aus einer Shell ohne sie), ist der Demomodus aus, ohne ein anderes Zeichen als das
  fehlende Banner.

## Verwandte Seiten

- [Demomodus](../../demomodus.md) (für Administratoren), [Häufige Fragen zum Demomodus](../../demomodus-faq.md)
- [Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md),
  [Probleme mit dem Demomodus](../runbooks/probleme-mit-dem-demomodus.md), [Beispiele für den Demomodus](../../../../eng-us/examples/demo-mode/README.md)
- [Demomodus: der Hinweis zum Upgrade](../../../../eng-us/bc/6.0/demo-mode.md)
- [Erste Anmeldung und Standardpasswörter](erste-anmeldung-und-standardpasswoerter.md)
- [Härtung für Hoster](haertung-fuer-hoster.md), [Sicherheit für Hoster: Überblick](README.md)
- [Eine Demo-Installation einrichten](../../presse/eine-demo-installation-einrichten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
