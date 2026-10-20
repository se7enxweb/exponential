---
title: "Runbook: Der Demomodus zeigt sich nicht, oder der Aufbau der Demo scheitert mit einem erzeugten Passwort"
audience: hosting, admin, developer
category: hosting
order: 130
summary: Was Sie prüfen, wenn eine öffentliche Demo ohne Demomodus läuft (kein Banner, ein Administratorpasswort ließ sich ändern, keine Zeile demo/mode in security.log), wenn ihr Aufbau scheitert, weil der Installer ein Administratorpasswort erzeugt hat, und wenn der Demomodus auf einer Website erscheint, auf der er nicht sein darf.
keywords: Runbook, Fehlersuche, Demomodus, EXP_DEMO_MODE, Banner fehlt, erzeugtes Passwort, initial-admin-password, exp:install, SetEnv, htaccess, PHP-FPM env, clear_env, Neustart von Velocity, Cron, security.log, demo/mode, system.demo.mode, Produktion, runbook, troubleshooting, demo mode, banner missing, generated password, Velocity restart, cron, production
type: help
---
# Runbook: Der Demomodus zeigt sich nicht, oder der Aufbau der Demo scheitert mit einem erzeugten Passwort

Für alle, die eine öffentliche Demo von Exponential betreuen, und für alle, die den Demomodus dort finden, wo er nicht sein sollte. Der
Demomodus ist in einem Prozess genau dann an, wenn dieser Prozess die Umgebungsvariable `EXP_DEMO_MODE` mit dem Wert `1` hat (gelesen mit
`getenv()`, dann `$_SERVER`); es gibt nirgendwo sonst eine Einstellung dafür. Jedes Problem unten ist ein Prozess, der die Variable hat
oder nicht hat, wenn er sie haben sollte. Hintergrund: [Demomodus](../../../../eng-us/bc/6.0/demo-mode.md).

## Inhalt

1. [Herausfinden, welche Prozesse sie haben](#herausfinden-welche-prozesse-sie-haben)
2. [Kein Banner in der Administration](#kein-banner-in-der-administration)
3. [Der Aufbau scheitert: Der Installer hat ein Passwort erzeugt](#der-aufbau-scheitert-der-installer-hat-ein-passwort-erzeugt)
4. [Der Demomodus war an und ist nach einem Neustart weg](#der-demomodus-war-an-und-ist-nach-einem-neustart-weg)
5. [Die Seite zum vergessenen Passwort zeigt keinen Hinweis](#die-seite-zum-vergessenen-passwort-zeigt-keinen-hinweis)
6. [Der Demomodus erscheint auf einer Website, auf der er nicht sein darf](#der-demomodus-erscheint-auf-einer-website-auf-der-er-nicht-sein-darf)
7. [Verwandte Seiten](#verwandte-seiten)

## Herausfinden, welche Prozesse sie haben

1. `grep 'demo/mode' var/log/security.log | tail`: eine Zeile pro Prozess, der mit Demomodus gestartet ist, mit seiner PID und seiner SAPI.
   `sapi=litespeed`, `sapi=fpm-fcgi` oder `sapi=apache2handler` sind Web-Anfragen, `sapi=cli` ist Cron, die Konsole oder ein Aufbau. (Die
   Worker von Velocity sind PHP-Prozesse der Kommandozeile und schreiben ebenfalls `sapi=cli`.) Keine Zeile mit der SAPI des Webservers
   bedeutet, dass die Web-Anfragen die Variable nicht haben.
2. **Audit:** Ereignisse `system.demo.mode`, eines pro Prozess.
3. Eine Prüfung in der Konsole, in der Shell, die Sie nutzen: `echo "$EXP_DEMO_MODE"` muss `1` ausgeben.

Denken Sie daran, dass nur der genaue Wert `1` zählt (Leerzeichen darum werden entfernt): `true`, `yes`, `on` und `01` lassen ihn aus.

## Kein Banner in der Administration

| Ursache | Prüfen | Abhilfe |
|---|---|---|
| Die Variable ist nur für Cron oder die Konsole gesetzt | keine Zeile `demo/mode` mit der SAPI des Webs | setzen Sie sie auch für den Webserver: `SetEnv` in `.htaccess` oder im virtuellen Host, im PHP-FPM-Pool, `fastcgi_param` oder in der Unit von Velocity ([Beispiele](../../../../eng-us/examples/demo-mode/README.md)) |
| `SetEnv` in einer `.htaccess`, die der Server nicht liest | der Server hat `AllowOverride None`, oder die Zeile steht in der `.htaccess` eines anderen Verzeichnisses | setzen Sie sie in den virtuellen Host, oder erlauben Sie Overrides für den Document Root; mit LiteSpeed prüfen Sie, dass `.htaccess` für die Domain gelesen wird |
| `mod_env` ist nicht geladen | der Block `<IfModule mod_env.c>` wird übersprungen | aktivieren Sie `mod_env`, oder setzen Sie die Variable im PHP-FPM-Pool |
| PHP-FPM verwirft die Umgebung | der Master hat sie, der Pool hat `clear_env = yes` (der Standard) | `env[EXP_DEMO_MODE] = 1` im Pool, dann den Master dieses Pools neu laden |
| Der falsche Pool oder Master wurde geändert | Plesk und cPanel betreiben mehrere PHP-Versionen, jede mit eigenem Master | ändern Sie den Pool der PHP-Version der Domain und laden Sie diesen neu |
| Ein anderer Wert als `1` | `true`, `yes`, `on` | schreiben Sie `1` |
| Eine veraltete Seite | die Seiten der Administration werden pro Benutzer zwischengespeichert | das Banner wird außerhalb der Cache-Blöcke gezeichnet; laden Sie neu, und leeren Sie den Template-Cache, wenn ein eigenes Design es innerhalb eines einbindet |
| Ein eigenes Design | das Pagelayout bindet das Banner nicht ein | `{include uri='design:parts/exp_demo_mode_banner.tpl'}` außerhalb jedes `{cache-block}` |

## Der Aufbau scheitert: Der Installer hat ein Passwort erzeugt

Der Aufbau der Demo hält mit der Meldung an, dass der Installer ein Administratorpasswort erzeugt hat (oder `var/log/initial-admin-password`
existiert nach `exp:install`). Der Installer ersetzt ein Administratorpasswort, das er nicht installieren darf, durch ein zufälliges: ein
leeres immer, und ein bekanntes (wie `publishing`) oder eines, das kürzer als `MinPasswordLength` ist (wie `publish`), sofern der Demomodus
nicht an ist. Der Installer hatte die Variable also nicht.

| Ursache | Abhilfe |
|---|---|
| Das Skript setzt die Variable nach dem Installationsbefehl, oder ohne `export` | `export EXP_DEMO_MODE=1` vor dem ersten Befehl |
| `EXP_DEMO_MODE=1 cmd1 && cmd2` gibt sie nur `cmd1` | exportieren Sie sie, oder stellen Sie sie vor den Installationsbefehl selbst |
| `sudo` oder `su` hat die Umgebung geleert | `sudo --preserve-env=EXP_DEMO_MODE`, oder setzen Sie sie in dem Befehl, den `su -c` ausführt |
| Der Wert ist nicht genau `1` | schreiben Sie `1` |
| Das Passwort ist leer | ein leeres Passwort wird nie installiert, auch nicht im Demomodus; übergeben Sie `--password=` mit dem veröffentlichten Wert |
| Der Kickstarter läuft als Kindprozess | er gibt seine eigene Umgebung weiter; setzen Sie die Variable für den Befehl des Kickstarters |

Der Setup-Assistent in einem Browser nimmt die Variable aus dem PHP des Webservers: Dort zeigt die Seite **Administrator der Seite** einen
Hinweis **Demo-Modus**, wenn er an ist. Ohne den Hinweis setzen Sie die Variable zuerst für die Web-Anfragen.

## Der Demomodus war an und ist nach einem Neustart weg

- **Exponential Velocity:** `exp:velocity restart` und `exp:velocity deploy` starten den neuen Server mit der Umgebung der Shell, in der
  sie laufen. Setzen Sie die Variable in der systemd-Unit
  ([`velocity-demo-mode.conf`](../../../../eng-us/examples/demo-mode/velocity-demo-mode.conf)) und starten Sie über die Unit neu, oder
  `export EXP_DEMO_MODE=1` vor einem Neustart aus einer Shell. Die Datei `/etc/vc/envvars` von Velocity gibt sie den Workern nicht.
- **PHP-FPM:** Eine Pool-Datei, die das Control Panel (Plesk, cPanel) neu schreibt, verliert Zeilen, die es nicht verwaltet. Setzen Sie
  sie über die PHP-Einstellungen des Panels für die Domain, oder nutzen Sie stattdessen `.htaccess` oder den virtuellen Host.
- **Ein Neuaufbau:** Eine frische `.htaccess` aus `.htaccess_root` hat kein `SetEnv`; der Aufbau muss es jedes Mal anhängen.

## Die Seite zum vergessenen Passwort zeigt keinen Hinweis

Der Kernel übergibt den Hinweis an `user/forgotpassword.tpl` als `demo_mode_text` (und an `user/password.tpl` als `demo_mode_locked` und
`demo_mode_text`). Die Designs des Kernels zeigen ihn. Ein Design einer Website, das diese Templates überschreibt, zeigt ihn, sobald sein
eigenes Release mit dem Hinweis installiert ist; ein eigenes Design braucht:

```smarty
{if first_set( $demo_mode_text, '' )|ne( '' )}<div class="warning"><p>{$demo_mode_text|wash}</p></div>{/if}
```

Die Ablehnung selbst hängt nicht vom Template ab: Im Demomodus geht so oder so keine Mail zum Zurücksetzen an einen Administrator.

## Der Demomodus erscheint auf einer Website, auf der er nicht sein darf

Ein Banner **Demo-Modus**, eine Zeile `demo/mode` in `security.log` oder ein Audit-Ereignis `system.demo.mode` auf einer Produktions-Website
bedeutet, dass ein Prozess dieser Website `EXP_DEMO_MODE=1` hat. Behandeln Sie es als Vorfall: Das Administratorpasswort lässt sich nicht
mehr ändern, und die Installer würden ein bekanntes akzeptieren.

1. Finden Sie heraus, woher es kommt: `grep -r EXP_DEMO_MODE` über die PHP-FPM-Pools, die virtuellen Hosts, die `.htaccess`-Dateien, die
   Dienst-Units und Drop-ins, die Crontabs, `/etc/environment`, die Container-Definitionen und die Vorlagen für das Ausrollen.
2. Entfernen Sie es dort, laden Sie diesen Dienst neu oder starten Sie ihn neu, und prüfen Sie, dass neue Zeilen `demo/mode` aufhören.
3. Fragen Sie, wer es hinzugefügt hat und warum; prüfen Sie im Audit die Änderungen an Administratoren, solange es gesetzt war.

## Verwandte Seiten

- [Demomodus: der Hinweis zum Upgrade](../../../../eng-us/bc/6.0/demo-mode.md)
- [Demomodus für Hoster](../sicherheit/demomodus-fuer-hoster.md), [Häufige Fragen zum Demomodus](../../demomodus-faq.md)
- [Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](../server/rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md)
- [Beispiele für den Demomodus](../../../../eng-us/examples/demo-mode/README.md)
- [Runbooks](README.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
