---
title: "Lektion 1.2: Apache und nginx mit PHP-FPM"
audience: trainer, hosting, admin
category: training
order: 12
summary: Die Teilnehmenden richten für eine Schulungsinstallation einen virtuellen Apache-Host mit PHP-FPM ein, vergleichen ihn mit dem abgeleiteten nginx-Server-Block und korrigieren die Dateieigentümer, sodass alle Schreibenden einen Benutzer verwenden. 45 Minuten.
keywords: hosting und betrieb, Apache, nginx, PHP-FPM, virtueller host, htaccess, AllowOverride, mod_rewrite, mod_proxy_fcgi, Plesk, dateirechte, eigentümer, hosting and operations, virtual host, file permissions, ownership
type: help
---
# Lektion 1.2: Apache und nginx mit PHP-FPM

Viele Hoster betreiben bereits Apache oder nginx mit PHP-FPM, und viele Kunden erwarten das. Diese Lektion zeigt die
traditionelle Einrichtung so, wie Kapitel 8 sie beschreibt: die Rewrite-Regeln, den virtuellen Host, den
PHP-FPM-Handler, den abgeleiteten nginx-Block und die Regel zum Dateieigentum, die die meisten Rechteprobleme
verhindert.

> **Hinweis**
> Die Übung braucht root auf der eigenen virtuellen Maschine der Person. Üben Sie nie auf einem Server, der andere
> Websites hostet.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Hosting und Betrieb](../README.md) |
| Modul | [Modul 1: Die Website ausliefern](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Systemadministratoren und Support-Mitarbeitende im Hosting |
| Voraussetzungen | [Lektion 1.1](wie-eine-anfrage-beantwortet-wird.md); eine virtuelle Linux-Maschine mit Apache, PHP-FPM und einer Schulungsinstallation |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Rewrite-Regeln aktivieren, indem sie `.htaccess_root` nach `.htaccess` kopieren;
- einen virtuellen Host schreiben, dessen Document Root das Wurzelverzeichnis der Installation ist und dessen PHP an
  einen PHP-FPM-Pool geht;
- Apache mit `apachectl configtest` prüfen und sicher neu laden;
- erklären, wie der nginx-Server-Block aus Kapitel 8 `.htaccess_root` abbildet und warum er als abgeleitet
  gekennzeichnet ist;
- sagen, welche Verzeichnisse der PHP-Benutzer schreiben können muss und warum alle Schreibenden ein Benutzer sein
  sollten.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Kapitel 8, Apache mit PHP-FPM](../../../../installation/08-die-website-ausliefern.md#85-apache-mit-php-fpm),
[nginx mit PHP-FPM](../../../../installation/08-die-website-ausliefern.md#86-nginx-mit-php-fpm) und
[Dateirechte und Eigentum](../../../../installation/08-die-website-ausliefern.md#87-dateiberechtigungen-und-eigentümer).

1. **Die Rewrite-Regeln** (3 Minuten). `cp .htaccess_root .htaccess`: kopieren, nicht verschieben, damit ein Update eine
   neue Version mitbringen kann. `.htaccess_root_static` ist nur für einen erzeugten statischen Cache. Zeigen Sie
   auch den Weg über die Konsole: `./console exp:setup:htaccess` (ein Probelauf, der sagt, was er tun würde), dann
   `--apply`. Der Befehl ersetzt keine abweichende `.htaccess` und gibt den Diff aus; so sehen die Teilnehmenden
   auch, was ein Upgrade geändert hat
   ([.htaccess und config.php von der Konsole einrichten](../../../../anleitungen/setup-dateien-von-der-konsole.md)).
2. **Der virtuelle Host** (4 Minuten). Zeigen Sie das Beispiel `<VirtualHost *:80>` aus Abschnitt 8.5.1:
   `DocumentRoot`, `AllowOverride All`, damit die `.htaccess` gelesen wird, und den Handler `<FilesMatch \.php$>`, der
   Skripte an den PHP-FPM-Socket schickt. Module: `mod_rewrite`, `mod_proxy`, `mod_proxy_fcgi`; für HTTPS `mod_ssl`,
   für HTTP/2 `mod_http2`.
3. **nginx** (4 Minuten). Exponential liefert keine nginx-Konfiguration mit. Der Block in Abschnitt 8.6 ist eine
   Übersetzung von `.htaccess_root` und muss auf der Website getestet und nach jedem Upgrade verglichen werden. Erklären
   Sie den Vorrang der Locations bei nginx: exakte Treffer, dann `^~`-Präfixe, dann reguläre Ausdrücke in der
   Reihenfolge der Datei, dann das längste einfache Präfix. Deshalb kommen die Regeln für Punktdateien und Skripte
   zuerst.
4. **Plesk** (2 Minuten). Plesk schreibt den virtuellen Host selbst: Setzen Sie das Document Root auf das
   Wurzelverzeichnis der Installation, wählen Sie „FPM application served by Apache“, behalten Sie `.htaccess`. Der Pool
   jeder Domain wird von einer Unit wie `plesk-php85-fpm` bedient; `systemctl restart php-fpm` startet einen anderen
   Master neu (Abschnitt 8.5.3).
5. **Ein Benutzer** (2 Minuten). Rechteprobleme entstehen fast immer dadurch, dass zwei Benutzer dieselben Dateien
   schreiben. Führen Sie den Pool, die Velocity-Worker, die Cronjobs und die Konsolenskripte als Benutzer der Website
   aus (Abschnitt 8.7.2).

### Häufige Fragen

**Kann ich `.htaccess` der Geschwindigkeit wegen vermeiden?** Ja: Kopieren Sie die Regeln in den Block `<Directory>`
und setzen Sie `AllowOverride None`. Im Serverkontext braucht jedes Muster einen führenden Schrägstrich; nehmen Sie die
Regeln aus `.htaccess_root`, nicht aus der älteren `doc/eng-us/examples/ezpublish.conf`.

**Jede Seite ist ein 404. Warum?** `.htaccess` wird nicht gelesen (`AllowOverride`), oder `mod_rewrite` ist aus.

**Ein Fehler 500 nach der Umstellung?** Lesen Sie die Fehlerlogs von Apache und PHP-FPM; Kapitel 8 nennt sie als
erste Anlaufstelle.

**Brauchen Sicherheits-Header eine Apache-Konfiguration?** Der Kernel sendet sie auf seinen Seiten
(`site.ini [HTTPHeaderSettings] SecurityHeaders[]`); für statische Dateien fügen Sie sie bei Bedarf mit `mod_headers`
hinzu ([Kapitel 13](../../../../installation/13-sicherheitshaertung.md)).

### Stolperfallen

- **Unterschiede zwischen Distributionen.** Debian nennt den Dienst `apache2` und aktiviert Module mit `a2enmod`;
  Systeme im Red-Hat-Stil nennen ihn `httpd`. Bereiten Sie für einen gemischten Kurs beide Befehlslisten vor.
- **Falscher Socket-Pfad.** Die Zeile `SetHandler` muss den Socket des PHP-FPM-Pools der Person nennen; lassen Sie die
  Teilnehmenden ihn vor dem Bearbeiten in ihrer Pool-Datei nachschlagen.
- **Cache-Dateien, die root gehören.** Wer einen Konsolenbefehl als root ausgeführt hat, beschädigt die Website für den
  Pool-Benutzer. Zeigen Sie `ls -l var/` und korrigieren Sie das Eigentum wie in Schritt 10.

## Praktische Übung

Verwenden Sie Ihre eigene virtuelle Linux-Maschine mit Apache, PHP-FPM und einer Schulungsinstallation, zum Beispiel
in `/var/www/training`.

1. Wechseln Sie in das Wurzelverzeichnis der Installation und führen Sie `cp .htaccess_root .htaccess` aus, oder
   `./console exp:setup:htaccess --apply` als Benutzer der Website. Führen Sie danach `./console exp:setup:status` aus:
   Es nennt `.htaccess_root` als Vorlage.
2. Aktivieren Sie die Module (Debian: `a2enmod rewrite proxy_fcgi`), oder prüfen Sie, dass sie auf Ihrem System geladen
   sind.
3. Suchen Sie den Socket-Pfad Ihres PHP-FPM-Pools in seiner Pool-Datei (die Zeile `listen`) und notieren Sie den
   `user` des Pools.
4. Legen Sie mit dem Beispiel aus Abschnitt 8.5.1 eine Datei für den virtuellen Host an, mit Ihrem Document Root, einem
   Schulungs-Hostnamen wie `training.example.invalid` und Ihrem Socket-Pfad.
5. Führen Sie `apachectl configtest` aus und beheben Sie alles, was es meldet, bis es `Syntax OK` ausgibt.
6. Laden Sie Apache neu (`systemctl reload apache2` oder `systemctl reload httpd`).
7. Tragen Sie den Hostnamen in `/etc/hosts` der virtuellen Maschine ein, mit Verweis auf `127.0.0.1`.
8. Führen Sie die vier Prüfungen aus Lektion 1.1 gegen `http://training.example.invalid/` aus.
9. Öffnen Sie die Startseite in einem Browser auf der virtuellen Maschine (oder mit `curl -sI`).
10. Geben Sie die beschreibbaren Verzeichnisse dem Pool-Benutzer, wie Abschnitt 8.7.1 zeigt:

    ```bash
    sudo chown -R <pool-user>:<pool-user> design extension settings var
    ```

11. Lesen Sie den nginx-Block aus Abschnitt 8.6 und suchen Sie die Zeile, die der letzten `RewriteRule` von
    `.htaccess_root` entspricht.

## Erwartetes Ergebnis

- `apachectl configtest` gibt `Syntax OK` aus; das Neuladen meldet keinen Fehler.
- Die vier Prüfungen liefern dieselben Antworten wie in Lektion 1.1 (`404`, `404`, `200`, `0`).
- Die Startseite antwortet über Apache mit `200` (oder einer Weiterleitung).
- Neue Dateien unter `var/` gehören nach einigen Anfragen dem Pool-Benutzer.
- In Schritt 11 zeigt die Person auf `location /`, das einfache Präfix, das alles andere an `index.php` schickt.
- Der Trainer prüft das, indem er jede Person nach dem Ergebnis der Anfrage nach `.git/config` über Apache fragt.

## Quiz

1. Warum wird `.htaccess_root` nach `.htaccess` kopiert statt verschoben?
2. Welche Apache-Einstellung muss das Lesen der Datei `.htaccess` erlauben?
   A. `Options Indexes` B. `AllowOverride All` C. `DirectoryIndex` D. `ProxyPreserveHost`
3. Warum wendet `systemctl restart php-fpm` auf einem Plesk-Server eine Änderung an einer Website nicht an?
4. Richtig oder falsch: Das Projekt liefert in seinem Repository eine getestete nginx-Konfiguration mit.
5. Eine von `root` geschriebene Cache-Datei beschädigt die Website. Welche Regel verhindert das?

<details>
<summary>Antworten</summary>

1. Damit ein Update eine neue Version von `.htaccess_root` mitbringen kann (Abschnitt 8.5.1).
2. **B.** Ohne `AllowOverride` werden die Regeln nicht gelesen, und jede Seite ist ein 404.
3. Die Websites von Plesk laufen unter einem anderen Master, etwa `plesk-php85-fpm`; die Unit `php-fpm` verwendet keine
   Plesk-Website (Abschnitt 8.5.3).
4. **Falsch.** Der nginx-Block in Kapitel 8 ist aus `.htaccess_root` abgeleitet und muss getestet werden.
5. Alles, was `var/` schreibt (Pool, Velocity-Worker, Cronjobs, Konsole), als Benutzer der Website ausführen, oder eine
   gemeinsame Gruppe mit gruppenbeschreibbaren Verzeichnissen verwenden (Abschnitt 8.7.2).

</details>

## Weiterführende Informationen

- [Apache mit PHP-FPM](../../../hosting/server/apache-mit-php-fpm.md): die Einrichtung aus Sicht des Hosters.
- [nginx mit PHP-FPM](../../../hosting/server/nginx-mit-php-fpm.md): der abgeleitete Block und seine Vorbehalte.
- [Plesk](../../../hosting/control-panels/plesk.md): Exponential auf einem Plesk-Server.
- [.htaccess und config.php von der Konsole einrichten](../../../../anleitungen/setup-dateien-von-der-konsole.md):
  `exp:setup:htaccess`, `exp:setup:status` und welche Server `.htaccess` lesen.
- [Beschreibbare Verzeichnisse und Rechte](../../../hosting/grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md): das
  Eigentum im Detail.

## Verwandte Seiten

- [Modul 1: Die Website ausliefern](README.md)
- [Hosting und Betrieb: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
