---
title: LiteSpeed und OpenLiteSpeed
audience: hosting
category: hosting
order: 60
summary: Was die Quellen von Exponential über Hosting mit LiteSpeed und OpenLiteSpeed sagen - die mitgelieferten Apache-Regeln, die SAPI LSAPI, open_basedir, was Sie vor dem Verkauf eines Tarifs testen und wo Velocity hineinpasst.
keywords: LiteSpeed, OpenLiteSpeed, LSAPI, SAPI litespeed, htaccess, .htaccess_root, Shared Hosting, open_basedir, Rewrite, Stufe 3, litespeed SAPI, shared hosting, rewrite, tier 3
type: help
---
# LiteSpeed und OpenLiteSpeed

Diese Seite ist für Hosting-Unternehmen, deren Shared oder Managed Hosting auf LiteSpeed oder OpenLiteSpeed läuft. Sie beantwortet: ob
Exponential dort läuft, welche Regeln Sie nutzen, was der Code über LiteSpeed weiß und was Sie testen, bevor ein Tarif in den Verkauf geht.
Die eigenen Menüs und Einstellungen von LiteSpeed werden hier nicht beschrieben; sehen Sie dafür in Ihrer LiteSpeed-Dokumentation nach.

## Inhalt

1. [Was die Quellen sagen](#was-die-quellen-sagen)
2. [Eine Website einrichten](#eine-website-einrichten)
3. [PHP unter LiteSpeed](#php-unter-litespeed)
4. [Was Sie vor dem Verkauf eines Tarifs testen](#was-sie-vor-dem-verkauf-eines-tarifs-testen)
5. [Velocity auf einem LiteSpeed-Host](#velocity-auf-einem-litespeed-host)
6. [Checkliste](#checkliste)

## Was die Quellen sagen

| Quelle | Aussage |
|---|---|
| `README.md`, Abschnitt „The traditional options“ | LiteSpeed und OpenLiteSpeed haben eine mit `.htaccess` kompatible Rewrite-Engine, die Apache-Regeln in den meisten Fällen direkt liest |
| `README.md`, Abschnitt darüber, wann ein klassischer Server noch die richtige Antwort ist | wo das Hosting keinen langlebigen Prozess erlaubt, ist Apache oder LiteSpeed mit `.htaccess` die Antwort |
| [Unterstützung von Betriebssystemen, Webserver](../../betriebssystemunterstuetzung.md#webserver) | LiteSpeed und OpenLiteSpeed: **Stufe 3**, sollte funktionieren; liest die Rewrite-Regeln von Apache in den meisten Fällen |
| [Unterstützung von Betriebssystemen, PHP-SAPIs](../../betriebssystemunterstuetzung.md#php-sapis) | LiteSpeed (`litespeed`, LSAPI): die eigene SAPI von LiteSpeed |
| `lib/ezutils/classes/ezexecution.php` | unter LiteSpeed (lsapi) läuft die Shutdown-Funktion im Verzeichnis der PHP-Binärdatei, das `open_basedir` nicht erlaubt; der Kernel kommt damit zurecht |

Stufe 3 bedeutet eine begründete Erwartung, kein Testergebnis ([Die vier Stufen](../../betriebssystemunterstuetzung.md#die-vier-stufen)): Das Projekt hat
Exponential in seinen eigenen Tests nicht auf LiteSpeed laufen sehen. „In den meisten Fällen“ ist die ehrliche Formulierung; testen Sie
Ihre eigene LiteSpeed-Version.

## Eine Website einrichten

1. **Document Root** ist das Wurzelverzeichnis der Installation, das Verzeichnis mit `index.php`.
2. **Rewrite-Regeln:** Kopieren Sie die mitgelieferten Regeln, wie bei Apache:

   ```bash
   cp .htaccess_root .htaccess
   ```

   oder, als Benutzer der Website, `./console exp:setup:htaccess --apply`
   ([.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)).
   LiteSpeed Enterprise liest die Datei bei der nächsten Anfrage. OpenLiteSpeed liest nur die Rewrite-Regeln, nur mit
   eingeschaltetem `autoLoadHtaccess` und erst nach einem Neustart; setzen Sie die Regeln dort besser in den
   virtuellen Host ([LiteSpeed-Beispiel](../../../beispiele/webserver/litespeed.md)).
   Nutzen Sie stattdessen `.htaccess_root_static` (`--static`) nur, wenn die Website einen erzeugten statischen Cache
   ausliefert ([Statischer Cache](../leistung/statischer-cache.md)).
3. **PHP:** 8.0 oder neuer mit den erforderlichen Erweiterungen ([Kapitel 2](../../../installation/02-voraussetzungen.md#23-php-extensions)),
   `memory_limit` mindestens 64M (die Untergrenze des Setup-Assistenten; geben Sie der Produktion mehr), `date.timezone` gesetzt,
   `file_uploads` an ([Kapitel 2, Einstellungen der php.ini](../../../installation/02-voraussetzungen.md#25-einstellungen-in-der-phpini)).
4. **Beschreibbare Verzeichnisse:** `var/` und die Verzeichnisse unter `settings/`, von einem Benutzer geschrieben
   ([Beschreibbare Verzeichnisse und Berechtigungen](../grundlagen/beschreibbare-verzeichnisse-und-berechtigungen.md)).
5. **Installieren** Sie mit dem Setup-Assistenten im Browser, wenn der Kunde keine Shell hat, oder mit `exp:install` oder dem Kickstarter,
   wo eine Shell verfügbar ist ([Ein-Klick-Installer](../bereitstellung/ein-klick-installer-fuer-exponential.md)).

## PHP unter LiteSpeed

- Die SAPI ist `litespeed` (LSAPI). Das PHP für das Web und das PHP für die Kommandozeile werden getrennt konfiguriert und sind oft
  verschiedene Builds: Geben Sie beiden dieselbe Version und dieselben Erweiterungen
  ([Kapitel 2, PHP für Kommandozeile und Web](../../../installation/02-voraussetzungen.md#php-auf-der-kommandozeile-und-im-web)).
- Der optionale Test des Setup-Assistenten möchte `open_basedir` leer. Shared Hosting setzt es oft; der Kernel kommt bereits mit dem
  Shutdown-Verzeichnis von LSAPI unter `open_basedir` zurecht, aber andere Funktionen (zum Beispiel der Vorlade-Lauf im Hintergrund, der
  `proc_open`, `setsid` und die PHP-Binärdatei braucht) können dadurch verweigert werden
  ([Caches vorladen, Abschnitt 7](../../../anleitungen/caches-vorwaermen.md#7-wenn-sich-etwas-nicht-wie-erwartet-verhält)).
- Exponential erkennt den Server, unter dem es läuft: Der Systembericht (`expSystemReport::serverKind()`) ordnet eine SAPI von LiteSpeed
  als `other` ein, und die Debug-Leiste nennt mit `Served by` den Server, die SAPI und den Port sowie `not by Velocity`, sodass
  Support-Mitarbeitende auf einen Blick sehen, was geantwortet hat.

## Was Sie vor dem Verkauf eines Tarifs testen

Führen Sie zuerst die vier Prüfungen des Routings aus [Webserver und Engines](README.md#was-jede-einrichtung-gemeinsam-hat) aus. Dann:

| Test | Warum |
|---|---|
| Die Startseite, eine Inhaltsseite und `/user/login` antworten | das Rewrite auf `index.php` funktioniert |
| `/api/` erreicht `index_rest.php` und `/content/treemenu` erreicht `index_treemenu.php` | die beiden anderen Front-Controller von `.htaccess_root` |
| Ein hochgeladenes Bild wird angezeigt, eine hochgeladene Datei lässt sich herunterladen | Speicherpfade und `content/download` |
| `.php` unterhalb der Wurzel antwortet mit 404 | keine PHP-Datei läuft, weil ihr Pfad angefragt wurde ([Kapitel 8](../../../installation/08-die-website-ausliefern.md#82-wie-eine-anfrage-beantwortet-wird)) |
| Ein Cronjob läuft aus der Crontab des Kunden | `runcronjobs.php` unter dem PHP der Kommandozeile ([Cronjobs](../betrieb/cronjobs.md)) |
| Ein Leeren des Caches unter **Setup > Caches** funktioniert | `var/` ist für das Web-PHP beschreibbar |

## Velocity auf einem LiteSpeed-Host

Velocity ist ein langlebiger Prozess; auf Shared Hosting mit LiteSpeed ist das meist nicht erlaubt. Auf einem LiteSpeed-Server, den Sie
kontrollieren, kann Velocity daneben auf eigenen Ports laufen, und der LiteSpeed-Server kann als Reverse Proxy dienen, in derselben Form
wie mit Apache oder nginx ([Velocity neben Apache oder nginx](velocity-neben-apache-oder-nginx.md)). Dem Proxy muss vertraut werden, und er
muss `X-Forwarded-Proto` setzen ([Kapitel 8, Hinter einem Reverse Proxy](../../../installation/08-die-website-ausliefern.md#88-hinter-einem-reverse-proxy)).

## Checkliste

- [ ] `.htaccess` aus `.htaccess_root` kopiert; Document Root ist das Wurzelverzeichnis der Installation.
- [ ] PHP für Web und Kommandozeile hat dieselbe Version mit denselben Erweiterungen.
- [ ] Die Prüfungen des Routings und die Tests oben bestehen auf einem Staging-Konto.
- [ ] Die Auswirkungen von `open_basedir` auf das Vorladen und andere Läufe im Hintergrund sind bekannt und für den Support dokumentiert.
- [ ] Nach jedem Upgrade wird `.htaccess` aus der neuen `.htaccess_root` aufgefrischt (`./console exp:setup:htaccess` zeigt den Diff; `--force --apply` kopiert nach einer Sicherung).

**Demomodus auf LiteSpeed.** LiteSpeed liest `SetEnv` in `.htaccess` wie Apache und gibt es an `lsphp` weiter: `SetEnv EXP_DEMO_MODE 1` in der `.htaccess` einer öffentlichen Demo schaltet den Demomodus für ihre Web-Anfragen ein, und `security.log` zeigt dann `demo/mode on ... sapi=litespeed`. [Rezept: eine öffentliche Demo auf LiteSpeed und Plesk betreiben](rezept-eine-oeffentliche-demo-auf-litespeed-und-plesk-betreiben.md).

## Verwandte Seiten

- [Apache mit PHP-FPM](apache-mit-php-fpm.md)
- [Shared Hosting](../modelle/shared-hosting.md)
- [Einen Webserver wählen](einen-webserver-waehlen.md)
- [Unterstützung von Betriebssystemen](../../betriebssystemunterstuetzung.md)
- [.htaccess und config.php von der Konsole einrichten](../../../anleitungen/setup-dateien-von-der-konsole.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
