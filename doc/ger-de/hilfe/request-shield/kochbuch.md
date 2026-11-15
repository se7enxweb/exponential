---
title: "Request Shield: Kochbuch"
audience: admin, hosting, developer
category: administration
order: 47
summary: Rezepte zum Kopieren für den Request Shield (exp_request_shield), jedes mit den Regelzeilen, den expect-Beispielen, die sie belegen, und wie man sie prüft - Hostnamen und Siteaccesses, die Suche, Downloads und PDFs, Hilfe-PDFs, Anmeldung und Registrierung, Formulare eigener Erweiterungen, die REST-API, die Administration, Webhooks, Überwachung und Cron, die Anfragen eigener Skripte signieren, bildreiche Seiten, Seiten-Aliase, Cloudflare, nginx, LiteSpeed und Apache davor, mehrere Server, Shared Hosting, Einführung mit dem Beobachtungsmodus, Banns, Sperrlisten, eigene Texte und Fehlerseiten, das Log lesen, testen und die Regelkopien auffrischen.
keywords: Request Shield, exp_request_shield, Kochbuch, Rezepte, Beispiele, Regeln, Suche, PDF, Downloads, exphelp, Anmeldung, Registrierung, REST-API, Webhooks, Cron, Überwachung, Uptime, exempt, X-Exp-Self, selfHeader, Vorladen, Bilder, Bild-Aliase, EXP-IMAGES, EXP-PACE, EXP-SEARCHES, Seiten-Alias, unblock, Cloudflare, CF-Connecting-IP, X-Forwarded-For, trust, nginx, LiteSpeed, mod_remoteip, Load Balancer, secret, Shared Hosting, APCu, Beobachtungsmodus, Durchsetzen, ban, deny, Fehlerseite, Log, Feinabstimmung, expect, test, trace, replay, cookbook, recipes
type: help
---
# Request Shield: Kochbuch

Rezepte für den Request Shield, jedes ein paar Zeilen zum Kopieren in die Hauptregeldatei der Website,
`settings/request-shield/request-shield.rules`, mit `expect`-Beispielen, die sie belegen. Jede Regel und jedes Beispiel
dieser Seite ist mit `request-shield test` gegen die Regeln gelaufen, die exp_request_shield 0.0.1.x mitliefert.

Jedes verwendete Wort erklärt die [Konfigurationsreferenz](konfigurationsreferenz.md). Die eigene Datei der Website und
wie man sie anlegt: [Den Request Shield einrichten](../../anleitungen/request-shield.md).

## Inhalt

1. [Wie man ein Rezept verwendet](#wie-man-ein-rezept-verwendet)
2. [Die Hosts und Siteaccesses der Website nennen](#die-hosts-und-siteaccesses-der-website-nennen)
3. [Die Suche schützen](#die-suche-schützen)
4. [Downloads und PDFs](#downloads-und-pdfs)
5. [Hilfe-PDFs und die Hilfesuche](#hilfe-pdfs-und-die-hilfesuche)
6. [Anmeldung und Registrierung](#anmeldung-und-registrierung)
7. [Formulare eigener Erweiterungen](#formulare-eigener-erweiterungen)
8. [Die REST-API](#die-rest-api)
9. [Die Administration](#die-administration)
10. [Webhooks](#webhooks)
11. [Cronjobs, Überwachung und Uptime-Prüfungen](#cronjobs-überwachung-und-uptime-prüfungen)
12. [Die Anfragen eigener Skripte signieren](#die-anfragen-eigener-skripte-signieren)
13. [Bildreiche Seiten und belebte Intranets](#bildreiche-seiten-und-belebte-intranets)
14. [Seiten-Aliase, die wie Systempfade aussehen](#seiten-aliase-die-wie-systempfade-aussehen)
15. [Drucklayouts und Schlagwortlisten](#drucklayouts-und-schlagwortlisten)
16. [Query-Parameter einer Erweiterung](#query-parameter-einer-erweiterung)
17. [Hinter Cloudflare, einem CDN oder einem Load Balancer](#hinter-cloudflare-einem-cdn-oder-einem-load-balancer)
18. [nginx, Apache, LiteSpeed und Velocity davor](#nginx-apache-litespeed-und-velocity-davor)
19. [Mehrere Webserver](#mehrere-webserver)
20. [Shared Hosting](#shared-hosting)
21. [Einführung mit dem Beobachtungsmodus](#einführung-mit-dem-beobachtungsmodus)
22. [Scanner für eine Stunde bannen](#scanner-für-eine-stunde-bannen)
23. [Eine Adresse aussperren oder hereinlassen](#eine-adresse-aussperren-oder-hereinlassen)
24. [Eigene Texte und Fehlerseiten](#eigene-texte-und-fehlerseiten)
25. [Das Log lesen und nachjustieren](#das-log-lesen-und-nachjustieren)
26. [Regeln mit expect-Zeilen testen](#regeln-mit-expect-zeilen-testen)
27. [Nach einem Update der Erweiterung](#nach-einem-update-der-erweiterung)

## Wie man ein Rezept verwendet

1. Kopieren Sie die Zeilen in `settings/request-shield/request-shield.rules`, **nach** die Zeile
   `include exponential-admin-uri.rules` (oder `-host.rules`) und vor jeden `site`-Block. Ersetzen Sie die
   Dokumentationsadressen (`192.0.2.0/24`, `203.0.113.10`) und Namen (`www.example.org`) durch Ihre eigenen. Behalten
   Sie die Zeilen, die die Datei aus der Vorgabe der Erweiterung bekommen hat, vor allem
   `[SITE-DASH] restrict /rs/** to 127.0.0.1 ::1` und `set api off`, die die eigenen Seiten der Bibliothek unter `/rs`
   geschlossen halten.
2. Geben Sie jeder Regel eine ID (`[SITE-...]`), damit Log und Übersicht sie nennen, und behalten Sie die
   `expect`-Zeilen: sie belegen, dass die Regel tut, was sie sagt, jetzt und nach dem nächsten Update.
3. Prüfen und testen, als der Benutzer, unter dem das PHP der Website läuft:

   ```bash
   php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
   php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
   ```

4. Nichts neu zu starten: jeder Server liest die geänderte Datei innerhalb von 10 Sekunden. Beobachten Sie eine Weile
   **Setup > Request Shield > Log**.

Ein Rezept, das eine mitgelieferte Regel **ersetzt** (`replace [EXP-...]`), ändert deren Zahlen; die mitgelieferten
Beispiele für die alten Zahlen schlagen dann in `test` fehl, etwa `expected uncached, got check by EXP-SEARCHES`. Das
ist so gewollt: es zählen die Beispiele neben Ihrer `replace`-Zeile. `test --only=<ID>` testet eine einzelne Regel.

## Die Hosts und Siteaccesses der Website nennen

Das erste Rezept für jede Website: nur die eigenen Namen werden beantwortet, und die Admin-Module werden hinter jedem
URI-Siteaccess der Website abgewiesen (die mitgelieferte Regel kennt nur `/ger` und `/eng`).

```
host        www.example.org example.org admin.example.org
replace     [EXP-MODULES] block regex (?i)^(/(site|ger|eng|fre))?/(setup|class|section|role|state|workflow|trigger|visual|package|infocollector|url|ezinfo|collaboration|pdf)(/|$)   # Admin-Module im Frontend: 404, hinter jedem URI-Siteaccess dieser Website
expect GET /fre/setup/info                         404 by EXP-MODULES
expect GET /shop/package                           answered   # ein Seiten-Alias, der nur ein Modul nennt
```

Behalten Sie `www.example.org` in der `host`-Zeile, auch wenn es nicht Ihr Name ist: die mitgelieferten Beispiele
senden ihn als `Origin`, und es ist eine reservierte Dokumentationsdomain, die kein Besucher verwendet.

## Die Suche schützen

Eine Suche ist die teuerste Seite überhaupt. Das mitgelieferte Budget erlaubt zehn Suchen pro Minute und Besucher,
danach die Browserprüfung (`EXP-SEARCHES`). Für einen langsameren Suchserver fünf pro Minute:

```
replace     [EXP-SEARCHES] limit searches 5/min on-exceeded challenge at regex (?i)/content/(advanced)?search(/|$)   # fünf Suchen pro Minute, dann die Prüfung
expect GET /content/search?SearchText=yoga  times 5   uncached
expect GET /content/search?SearchText=yoga  times 6   check by EXP-SEARCHES
expect GET /news  times 40   answered   # Seiten lesen verbraucht es nicht
```

Schreiben Sie den Teil `at regex ...` mit: die mitgelieferte Regel steht in einem `match`-Block, und ein `replace` ohne
`at` würde **jede** Anfrage auf das Suchbudget anrechnen.

Solange die Suche von vielen Adressen zugleich angegriffen wird, prüfen Sie jeden Suchenden einmal pro Stunde:

```
[SITE-SEARCH-CHECK] challenge **/content/search **/content/advancedsearch max-age 1h   # unter Angriff: jede Suche geprüft
expect GET /content/search?SearchText=yoga                 check
expect GET /content/search?SearchText=yoga with pass       uncached
```

Solange die Regel in der Datei steht, meldet `test` die mitgelieferten Suchbeispiele als `check`. Nehmen Sie die
Regel wieder heraus, wenn der Angriff vorbei ist.

## Downloads und PDFs

Dateien, die `content/download` sendet (PDFs, Office-Dateien, Archive), liefert PHP aus, und sie ziehen Crawler an,
die jede einzelne abrufen. Ein eigenes Budget, danach die Prüfung:

```
match /content/download/** {
  [SITE-DOWNLOADS] limit downloads 30/min on-exceeded challenge             # Downloads: 30 pro Minute und Besucher, dann die Prüfung
  expect GET /content/download/12/34/file/report.zip  times 30   answered
  expect GET /content/download/12/34/file/report.zip  times 31   check
}
expect GET /news  times 40   answered
```

Oder die Browserprüfung vor jedem PDF-Download, einmal pro Stunde (ein echter Browser löst sie in etwa einer Sekunde;
ein Crawler, der nur den Namen einer Suchmaschine sendet, nicht):

```
[SITE-PDF-CHECK] challenge **/content/download/**/*.pdf max-age 1h          # jeder PDF-Download: zuerst die Browserprüfung
expect GET /content/download/12/34/file/report.pdf               check
expect GET /content/download/12/34/file/report.pdf with pass     answered
expect GET /content/download/12/34/file/photo.zip                answered
expect GET /content/download/12/34/file/report.pdf ua "Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)" from 66.249.66.1   answered   # der echte Googlebot: nie die Prüfung
```

Ein Crawler, der durch die veröffentlichten Adressen seines Betreibers bestätigt ist, bekommt nie die Prüfung
(`crawlers ... allow`); über einem Budget bekommt er eine Pause (429 mit `Retry-After`), die Crawler verstehen.
Dateien in `var/storage/`, die der Webserver selbst ausliefert, erreichen PHP nie; keine Regel sieht sie.

## Hilfe-PDFs und die Hilfesuche

Der PDF-Export und die Suche der Hilfe (`/exphelp/pdf/...`, `/exphelp/search`) bauen ihre Antwort für jede Anfrage neu.
Ist die Hilfe ohne Anmeldung lesbar, geben Sie ihnen ein Budget:

```
match regex (?i)^(/[a-z0-9_]+)?/exphelp/(pdf|search)(/|$) {
  [SITE-HELP-HEAVY] limit helpheavy 10/min on-exceeded challenge            # Hilfe-PDFs und Hilfesuche: 10 pro Minute, dann die Prüfung
  expect GET /exphelp/pdf/guides  times 10   answered
  expect GET /exphelp/pdf/guides  times 11   check
}
```

## Anmeldung und Registrierung

Das mitgelieferte `EXP-LOGIN` verlangt die Browserprüfung vor Anmeldung, Registrierung und den Passwortformularen und
nimmt einen Pass aus den letzten 15 Minuten an. Fünf Minuten:

```
replace     [EXP-LOGIN] challenge **/user/login **/user/register **/user/forgotpassword **/user/password/** max-age 5m   # ein Pass aus den letzten fünf Minuten
expect GET /user/login                            check by EXP-LOGIN
expect GET /user/login with pass                  answered
```

Zehn Registrierungen pro Stunde und Besucher, danach wieder die Prüfung:

```
match /user/register/** {
  [SITE-REGISTER] limit registrations 10/hour on-exceeded challenge         # das Registrierungsformular: zehn pro Stunde und Besucher
  expect POST /user/register header Origin:https://www.example.org with pass times 10   answered
  expect POST /user/register header Origin:https://www.example.org with pass times 11   check
}
```

Die eigene Sperre von Exponential nach fehlgeschlagenen Anmeldungen (`site.ini [UserSettings] MaxNumberOfFailedLogin`,
vorgegeben aus) wirkt hinter dem Shield wie bisher; der Shield hält Bots ganz vom Formular fern.

## Formulare eigener Erweiterungen

`EXP-POST` erlaubt POST nur dort, wo Exponential Formulare annimmt; ein Formular Ihrer eigenen Erweiterung unter
`/myforms/send` antwortet mit 405. Erlauben Sie ihr Modul, halten Sie Formulare anderer Websites fern (das
mitgelieferte `EXP-ORIGIN` tut das schon) und verlangen Sie die Prüfung einmal pro halbe Stunde:

```
[SITE-MYFORMS-POST]  allow POST **/myforms/**                               # die Formulare der Erweiterung myforms
[SITE-MYFORMS-CHECK] challenge POST **/myforms/** max-age 30m               # ein gesendetes Formular: zuerst die Browserprüfung, einmal pro halbe Stunde
expect POST /myforms/send header Origin:https://www.example.org with pass   answered
expect POST /myforms/send header Origin:https://evil.example                403 by EXP-ORIGIN
expect POST /news/2026/fit-and-healthy                                      405 by EXP-POST
```

Ein Formular, das ein Besucher ohne Pass sendet, bekommt die Prüfseite und wird danach von selbst erneut gesendet;
nichts Getipptes geht verloren. Ein Formular mit Datei-Uploads braucht stattdessen die Prüfung im Formular
(`set widget-path`, siehe die Bibliothek:
[browser check in the form](https://github.com/cjw-network/request-shield/blob/main/docs/features/RSF03-03-browser-check-in-the-form.md),
auf Englisch).

## Die REST-API

Die REST-API (`/api/ezp/v2/...`) ändert Inhalte mit PUT, PATCH und DELETE, und ihre Clients sind Programme ohne
JavaScript. Erlauben Sie die Methoden nur dort, schicken Sie nie die Browserprüfung (ein Client über seinem Budget
bekommt 429 mit `Retry-After`) und geben Sie der API ein eigenes Budget:

```
method      GET HEAD POST OPTIONS PUT PATCH DELETE
[SITE-REST-METHODS] allow PUT PATCH DELETE /api/**                          # die REST-API ändert Inhalte mit PUT, PATCH und DELETE
expect DELETE /api/ezp/v2/content/objects/57                     answered
expect DELETE /news/2026/fit-and-healthy                         405 by EXP-ADMIN-SPA   # anderswo weiter 405 (genannt wird die erste allow-Regel für DELETE)
[SITE-REST-NOCHECK] challenge-exempt /api/**                                 # API-Clients führen kein JavaScript aus: nie die Prüfung, nur die Pause
[SITE-REST-BUDGET]  limit api 300/min at /api/**                            # die API: 300 Anfragen pro Minute und Client, dann 429
expect GET /api/ezp/v2/content/objects/57  times 300   answered
expect GET /api/ezp/v2/content/objects/57  times 301   429
```

Die eigene Authentifizierung der API (OAuth2, Sitzung, Basic) bleibt unverändert; der Shield entscheidet davor. Der
Server eines Partners mit fester Adresse lässt sich mit `exempt <Adresse>` vom Budget ausnehmen.

## Die Administration

**Nur aus dem Büro oder dem VPN** (403 für alle anderen):

```
[SITE-ADMIN-NET] restrict /admin/** to 192.0.2.0/24 2001:db8:10::/48 198.51.100.7   # die Administration: Büro, VPN und die Testadresse der Beispiele
expect GET /admin/dashboard from 203.0.113.9          403
expect GET /admin/dashboard from 192.0.2.44 with pass answered
```

`198.51.100.7` ist die Adresse, von der `test` seine Beispiele sendet (ein Dokumentationsbereich, nie die eines echten
Besuchers); fehlt sie in der Liste, schlagen die mitgelieferten Admin-Beispiele mit 403 fehl. Im Beobachtungsmodus
weist ein `restrict` niemanden ab. Hat die Administration einen eigenen Host, schreiben Sie `restrict /** to ...` in
ihren `site`-Block.

**Nie die Browserprüfung für das Büro** (für blockierte Pfade und Angriffsmuster wird es weiter abgewiesen):

```
exempt      192.0.2.0/24                                                     # das Büro: nie gezählt, nie geprüft
```

**Eine Prüfung pro Arbeitstag** für Redakteure: `set pass-ttl 8h` (auf einem gemeinsamen Host auch für Besucher; in
einem `site`-Block nur für den Admin-Host, wie `exponential-admin-host.rules` es tut).

**Andere Admin-Siteaccesses** (`/adminui`, `/editor`, `/admin4` ...) decken die `SITE-ADMIN-*`-Regeln der vorgegebenen
Hauptdatei ab. Ein Admin-Siteaccess mit einem anderen Namen braucht denselben Block:

```
match regex ^/(backoffice)(/|$) {
  [SITE-BO-SYS]   unblock [EXP-SYSVIEW] [EXP-MODULES]                       # System-URLs und Admin-Module
  expect GET /backoffice/content/view/full/2 with pass   answered
  [SITE-BO-POST]  allow POST                                                # Redakteure speichern überall
  expect POST /backoffice/content/edit/87/1/eng-GB with pass   answered
  [SITE-BO-CHECK] challenge                                                 # die Browserprüfung einmal pro Pass
  expect GET /backoffice/dashboard                       check
  [SITE-BO-Q]     query * text                                              # jeder Parameter bekannt, freier Text geprüft
  expect GET /backoffice/content/edit/87?anything=goes with pass   answered
}
```

## Webhooks

Ein Zahlungsanbieter oder ein Repository sendet von seinen Servern an die Website: keine Seite der Website, kein
Browser.

```
[SITE-HOOK-POST]    allow POST /webhook/**                                  # Webhooks von Zahlungsanbietern und Repositorys senden hierher
[SITE-HOOK-ORIGIN]  post-origin except /webhook/**                          # von einem Server gesendet, nicht von einer Seite der Website
[SITE-HOOK-NOCHECK] challenge-exempt /webhook/**                            # ein Server löst keine Browserprüfung
expect POST /webhook/payments header Origin:https://pay.example.net   answered
expect POST /webhook/payments                                         answered
```

Prüfen Sie die Signatur des Webhooks wie bisher im empfangenden Modul; der Shield lässt die Anfrage nur dorthin durch.

## Cronjobs, Überwachung und Uptime-Prüfungen

- **Cronjobs** (`runcronjobs.php`, `ezcache.php`, `exp:`-Konsolenbefehle) laufen auf der Kommandozeile und passieren
  den Shield nie. Nichts zu konfigurieren.
- **Ein Uptime-Monitor**, der jede Minute von einer festen Adresse eine Seite abruft: nehmen Sie ihn aus, mit einem
  Enddatum, damit es nicht für immer bleibt:

  ```
  exempt      203.0.113.10 until 2027-12-31                                  # der Uptime-Monitor: nie gezählt, nie geprüft
  expect GET /news  from 203.0.113.10 times 500                        answered
  expect GET /settings/site.ini  from 203.0.113.10                     404 by EXP-INTERNAL   # interne Dateien weiter abgewiesen
  ```

- **Ein Monitor ohne feste Adressen** (ein Cloud-Dienst): lassen Sie ihn höchstens einmal pro Minute eine einfache Seite
  mit `HEAD` oder `GET` abrufen; das bleibt weit unter dem Tempo. Richten Sie ihn nie auf die Anmeldung oder die Suche.
- **Der Server selbst** (`127.0.0.1`, `::1`) ist vorgegeben ausgenommen.

## Die Anfragen eigener Skripte signieren

Der Vorwärmer der Installation, die Prüfung nach einem Upgrade und das statische Veröffentlichen signieren ihre
Anfragen (`X-Exp-Self`) und passieren den Shield. Ein eigenes Skript, das auf derselben Installation läuft, kann es
genauso tun, zum Beispiel ein Sitemap-Vorwärmer:

```php
<?php
// php bin/php/ezexec.php extension/mysite/scripts/warm.php
$url = 'https://www.example.org/news';
$header = expRequestShield::selfHeader( $url, 'GET' );   // "X-Exp-Self: <Zeit>.<Signatur>", oder null
$ch = curl_init( $url );
curl_setopt_array( $ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => $header ? array( $header ) : array(),
) );
$body = curl_exec( $ch );
```

- Erzeugen Sie für jede Anfrage einen neuen Header: er gilt zwei Minuten und nur für diese Methode und Adresse (Pfad
  und Query).
- Außerhalb des Kernels zuerst `require 'extension/exp_request_shield/classes/exprequestshield.php';`; die Klasse
  braucht sonst nichts.
- Der Schlüssel ist `var/request-shield/cache/self.key`, nur für den Benutzer der Website lesbar. Lassen Sie das Skript
  als dieser Benutzer laufen.
- Ein Werkzeug auf einem anderen Rechner kann nicht signieren (der Schlüssel verlässt den Server nie): nehmen Sie
  stattdessen seine Adresse aus.

Auf einer laufenden Website gemessen: die Anmeldung ohne den Header bekam die Browserprüfung (429, `rule=EXP-LOGIN`),
dieselbe Anfrage mit ihm 200.

## Bildreiche Seiten und belebte Intranets

Exponential 6.0.15 erzeugt Bild-Aliase bei Bedarf: eine Seite mit Hunderten neuer Bilder ruft eine Adresse pro Bild
ab. Das mitgelieferte `EXP-IMAGES` erlaubt 3000 pro Minute und Besucher; für eine Galerie-Website mit Seiten aus
Tausenden neuer Bilder:

```
replace     [EXP-IMAGES] limit images 6000/min on-exceeded challenge at regex ^((/[a-z0-9_]+)?/content/image/|/index_image\.php|/var/([^/]+/)?storage/images(-versioned)?/)   # eine Galerie-Website: 6000 Bildadressen pro Minute
expect GET /content/image/abc/x.jpg  times 5000     answered
```

Ein Intranet, in dem sich ein ganzes Büro eine Adresse teilt, braucht ein schnelleres Tempo (das mitgelieferte
`EXP-PACE`: die Prüfung ab 120 Seiten pro Minute):

```
replace     [EXP-PACE] limit pages 600/min challenge-at 240 on-exceeded challenge at regex ^(?!(/[a-z0-9_]+)?/content/image/|/index_image\.php|/var/([^/]+/)?storage/images(-versioned)?/)   # ein Intranet: das doppelte Tempo
expect GET /news  times 240                        answered
expect GET /news  times 241                        check by EXP-PACE
```

Behalten Sie bei beiden das `at regex ...`, damit Bilder und Seiten getrennt zählen. Für ein Büro ist `exempt` mit
seiner Adresse noch besser.

## Seiten-Aliase, die wie Systempfade aussehen

Ein Seiten-Alias, der wie ein Verzeichnis oder Modul beginnt, das der Shield abweist (`/doc/...`, `/url/...`), bekommt
404. Heben Sie die Sperre genau für diesen Pfad auf, mit einem Beispiel, dass sie anderswo bestehen bleibt:

```
[SITE-DOC]   unblock [EXP-INTERNAL] at /doc /doc/**                          # /doc ist hier ein Inhaltsbereich, nicht das Verzeichnis doc/ der Installation
expect GET /doc/getting-started                      answered
expect GET /settings/site.ini                        404 by EXP-INTERNAL
[SITE-ALIAS] unblock [EXP-MODULES] at /url /url/**                           # der Bereich "url" (ein Seiten-Alias), nicht das Modul url
expect GET /url/how-short-links-work                 answered
expect GET /setup/info                               404 by EXP-MODULES
```

`check` warnt dann, dass blockierte Pfade dort für alle offen sind; für einen Inhaltsbereich ist das gewollt.

## Drucklayouts und Schlagwortlisten

Zwei Regeln von einer laufenden Exponential-Website. Scanner verpacken ihre Pfade in `/layout/set/print/...`, und jeder
kostete eine ganze Seite bis zu seinem 404; Leser nutzen das Drucklayout selten:

```
[SITE-LAYOUT-SET] challenge **/layout/set/** max-age 12h                     # ein anderes Layout einer Seite: zuerst die Browserprüfung
expect GET /layout/set/print/news/article            check
expect GET /layout/set/print/news/article with pass  answered
```

Schlagwortseiten (`/content/keyword/<Wort>`) sind Listen ohne Cache, die Crawler durchblättern:

```
match regex (?i)/content/keyword(/|$) {
  [SITE-KEYWORDS] limit keywords 20/min on-exceeded challenge               # Schlagwortlisten: 20 pro Minute und Besucher, dann die Prüfung
  expect GET /content/keyword/Demo%20Website  times 20   answered
  expect GET /content/keyword/Demo%20Website  times 21   check
}
```

## Query-Parameter einer Erweiterung

Das mitgelieferte `EXP-STRICT` beobachtet unbekannte Query-Parameter (`monitor query strict`): das Log zeigt
`monitor-reject 404 "unknown parameter"`. Nennen Sie die Parameter Ihrer Erweiterung mit ihren Typen, dann werden ihre
Werte auch nicht mehr als freier Text geprüft:

```
[SITE-SHOP-Q] query sort word  page int  color list at /shop/**              # die Filterparameter des Shops
expect GET /shop/shirts?sort=price&page=2&color=red,blue   answered
expect GET /shop/shirts?page=abc                            404 by EXP-STRICT   # eine Seite, die keine Zahl ist
```

Typen: `int`, `number`, `word`, `id`, `list`, `text`, `any` oder `/Ausdruck/`. Wenn das Log keine
`unknown parameter`-Zeilen echter Besucher mehr zeigt, setzen Sie es durch: `replace [EXP-STRICT] query strict`.

## Hinter Cloudflare, einem CDN oder einem Load Balancer

Hinter einem Proxy scheint jede Anfrage von der Adresse des Proxys zu kommen: alle Besucher teilen sich ein Budget, das
Log zeigt ein einziges Netz, und das Tempo weist alle zugleich ab. Nennen Sie den Proxy mit `trust`; der Shield liest
die Adresse des Besuchers dann aus `X-Forwarded-For`, und nur von dort (der Header anderer wird entfernt):

```
# Die Netze von Cloudflare, aus https://www.cloudflare.com/ips/ (gelegentlich prüfen)
trust       173.245.48.0/20 103.21.244.0/22 103.22.200.0/22 103.31.4.0/22 141.101.64.0/18 108.162.192.0/18 190.93.240.0/20 188.114.96.0/20 197.234.240.0/22 198.41.128.0/17 162.158.0.0/15 104.16.0.0/13 104.24.0.0/14 172.64.0.0/13 131.0.72.0/22
trust       2400:cb00::/32 2606:4700::/32 2803:f800::/32 2405:b500::/32 2405:8100::/32 2a06:98c0::/29 2c0f:f248::/32
[SITE-ADMIN-NET] restrict /admin/** to 192.0.2.0/24 198.51.100.7
expect GET /admin/dashboard from 162.158.1.1 header X-Forwarded-For:192.0.2.44   check by EXP-ADMIN-CHECK   # über Cloudflare, aus dem Büro: weiter zur Prüfung
expect GET /admin/dashboard from 162.158.1.1 header X-Forwarded-For:203.0.113.9  403        # über Cloudflare, von anderswo
expect GET /admin/dashboard from 198.51.100.50 header X-Forwarded-For:192.0.2.44 403        # der Header von jemand anderem: nicht geglaubt
```

- Ein eigener Load Balancer: `trust 10.0.0.0/8` (sein Netz), sonst nichts.
- Setzt der Webserver die Adresse des Besuchers schon in `REMOTE_ADDR` (Apache `mod_remoteip`, nginx `real_ip`,
  LiteSpeed „Use Client IP in Header“), sieht der Shield den Besucher direkt und braucht keine `trust`-Zeile.
  Konfigurieren Sie nur eines von beiden.
- **Woran man es erkennt:** Lesen Sie die zweite Spalte des Logs. Zeigt fast jede Zeile dasselbe Netz, ist der Proxy
  noch nicht vertrauenswürdig.

## nginx, Apache, LiteSpeed und Velocity davor

- **nginx vor Apache auf demselben Server**: jede Anfrage kommt von `127.0.0.1`, und das ist vorgegeben **ausgenommen**;
  es wird also gar nichts gezählt oder geprüft. Vertrauen Sie ihm:

  ```
  trust       127.0.0.1 ::1                                                   # nginx davor, auf demselben Server
  expect GET /news  from 127.0.0.1 header X-Forwarded-For:203.0.113.9 times 121   check by EXP-PACE   # der Besucher zählt, nicht der Proxy
  expect GET /news  from 127.0.0.1 times 500                                  answered   # der Proxy selbst bleibt ausgenommen
  ```

  und lassen Sie nginx `proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;` senden.
- **nginx hinter Cloudflare**, mit der Adresse von nginx selbst aufgelöst: `set_real_ip_from <jedes Cloudflare-Netz>;`
  und `real_ip_header CF-Connecting-IP;` im `http`-Block; dann keine `trust`-Zeile.
- **Apache hinter Cloudflare**: `RemoteIPHeader CF-Connecting-IP` und je Cloudflare-Netz eine
  `RemoteIPTrustedProxy <Netz>` (`mod_remoteip`); dann keine `trust`-Zeile.
- **LiteSpeed und OpenLiteSpeed**: „Use Client IP in Header“ auf „Trusted IP Only“, mit den Adressen des Proxys als
  vertrauenswürdig in der Zugriffskontrolle des Servers. Auf geteiltem LiteSpeed-Hosting fragen Sie den Hoster, was
  gilt.
- **Exponential Velocity** beendet TLS selbst und gibt die Adresse des Clients an PHP weiter; keine `trust`-Zeile,
  außer ein weiterer Proxy steht davor. Regeländerungen brauchen keinen Neustart; das Hinzufügen des Hooks schon
  (`./console exp:velocity restart`). Verwenden Sie `set store file`, damit alle Worker einen Zähler teilen.

## Mehrere Webserver

Hinter einem Load Balancer mit zwei oder mehr Webservern:

- **Ein Geheimnis** für das Pass-Cookie, sonst wird ein Besucher, der die Prüfung auf einem Server gelöst hat, auf dem
  nächsten erneut geprüft: `set secret ${SHIELD_SECRET}` mit demselben Wert (mindestens 32 Zeichen) in der Umgebung
  jedes Servers, oder ein `store-dir` auf einem gemeinsamen Dateisystem.
- **Zähler** gelten mit APCu (`store auto`) pro Server: ein Besucher, der auf zwei Server verteilt wird, bekommt das
  doppelte Budget. Halbieren Sie die Zahlen, oder `set store file` mit `store-dir` auf einem gemeinsamen Dateisystem.
- **Regeldateien** auf jedem Server gleich (mit dem Code ausrollen); jeder Server übersetzt seine eigene Kopie.

## Shared Hosting

Der Shield läuft auf jedem Hosting mit PHP 7.4 oder neuer:

- Ohne APCu führt er seine Zähler in Dateien (Stufe S1, etwa 25 Mikrosekunden pro Anfrage). `check` nennt die Stufe.
- Halten Sie `store-dir` und `log` innerhalb der Installation (`var/request-shield/...`), damit `open_basedir` sie
  erlaubt; die mitgelieferten Pfade tun das schon.
- Ohne beschreibbares Verzeichnis (Stufe S0) schalten sich Budgets, Banns und das Pass-Cookie ab; die Pfad- und
  Angriffsregeln wirken weiter. `check` sagt, was aus ist.
- Unterscheiden sich der Benutzer für das Deployment und der des Webservers, teilen Sie eine Gruppe:
  `set file-mode 0640`, `set dir-mode 02750`.

## Einführung mit dem Beobachtungsmodus

Auf einer Website mit vielen eigenen Adressen beginnen Sie mit Beobachten:

1. `set mode monitor` in der Hauptdatei der Website (oder **Beobachten** auf der Übersicht). Jede Entscheidung wird
   protokolliert, wie sie getroffen würde (`monitor-reject`, `monitor-challenge` ...); niemand wird abgewiesen.
2. Lesen Sie einige Tage lang, während Redakteure arbeiten und die Suche genutzt wird, **Setup > Request Shield > Log**,
   Gruppe **Beobachtet (Monitor)**. Jede Zeile nennt ihre Regel.
3. Für jede Zeile, die ein echter Besucher oder Redakteur war, ergänzen Sie eine Ausnahme mit einem `expect`-Beispiel
   (die Rezepte oben), dann `check` und `test`.
4. `set mode enforce` (oder **Durchsetzen**). Lesen Sie noch einen Tag lang das Log.

Eine einzelne neue Regel lässt sich beobachten, während alles andere durchgesetzt wird: setzen Sie `monitor` davor.

```
[SITE-OLDAPI] monitor block /old-api/**                                      # die alte API: zuerst beobachtet
expect GET /old-api/v1/items                         404
```

`test` prüft beobachtete Regeln, als wären sie durchgesetzt, sodass das Beispiel für den Tag bereit ist, an dem
`monitor` wegfällt; `test --as-written` testet sie so, wie sie jetzt sind.

## Scanner für eine Stunde bannen

Das mitgelieferte `EXP-BAN` (10 Abweisungen in 10 Minuten, eine Stunde Sperre) beobachtet nur. Ein eigener Bann, der
durchgesetzt wird:

```
[SITE-BAN] ban after 20 refusals in 10m for 1h                               # durchgesetzt: ein Scanner nach 20 Abweisungen eine Stunde von der Website fern
expect GET /settings/site.ini  times 20            404 by EXP-INTERNAL
expect GET /settings/site.ini  times 21            429 by SITE-BAN
```

Testen Sie ihn mit `test --only=SITE-BAN --as-written`: ohne `--as-written` setzt `test` auch das beobachtete `EXP-BAN`
durch, und die beiden Banns zählen jede Abweisung doppelt. Wiederholte Banns innerhalb eines Tages wachsen
(`set ban-growth 2`, `set ban-max 1d`).

## Eine Adresse aussperren oder hereinlassen

Von der Kommandozeile, mit Enddatum und Grund, ohne die Regeldatei zu bearbeiten (die Listen liegen in
`var/request-shield/store/lists/`, und jeder Server liest sie innerhalb seines Recheck-Intervalls):

```bash
php extension/exp_request_shield/bin/request-shield deny   settings/request-shield/request-shield.rules 203.0.113.0/24 --for=7d --reason="form spam"
php extension/exp_request_shield/bin/request-shield allow  settings/request-shield/request-shield.rules 192.0.2.10 --until=2026-12-31 --reason="partner import"
php extension/exp_request_shield/bin/request-shield lists  settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield unlist settings/request-shield/request-shield.rules 203.0.113.0/24
```

`deny` antwortet mit 403 vor jeder anderen Prüfung; `allow` schreibt eine `exempt`-Zeile (nie gezählt, nie geprüft).
In einer Regeldatei heißt dasselbe `deny 203.0.113.0/24 until 2026-12-31`.

## Eigene Texte und Fehlerseiten

```
set         text.en.title One moment, please                                 # der Titel der Prüfseite
set         text.de.title Einen Moment, bitte
set         error-page 404 errors/4xx.html                                   # eine eigene Seite der Website für das 404 des Shields
```

`errors/4xx.html` liegt neben der Regeldatei, bis 64 KB, gelesen beim Übersetzen der Regeln, mit den Platzhaltern
`{status}`, `{title}`, `{text}`, `{wait}`, `{home}`, `{lang}` und `{reference}`:

```html
<!doctype html><html lang="{lang}"><title>{title}</title>
<h1>{title}</h1><p>{text}</p><p><a href="{home}">Startseite</a> {reference}</p></html>
```

## Das Log lesen und nachjustieren

Das Log ist `var/request-shield/log/shield.log`, eine Zeile pro Entscheidung
([die Felder](konfigurationsreferenz.md#die-logzeile)). **Setup > Request Shield > Log** zeigt es nach Zeitraum und
Gruppe. Auf dem Server:

```bash
# welche Regeln heute am häufigsten entschieden haben
grep "^$(date +%F)" var/request-shield/log/shield.log | grep -o 'rule=[A-Z0-9_.-]*' | sort | uniq -c | sort -rn | head
# welche Netze eine Regel am häufigsten gestoppt hat
grep ' rule=EXP-PACE ' var/request-shield/log/shield.log | awk '{print $2}' | sort | uniq -c | sort -rn | head
# was die beobachteten Regeln abgewiesen hätten
grep ' monitor-' var/request-shield/log/shield.log | tail -20
# live mitlesen
tail -f var/request-shield/log/shield.log
```

Warum eine Anfrage so entschieden wurde, Schritt für Schritt:

```bash
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/content/view/full/2"
```

```
  ✓ Website name                      "www.example.org" is one of this site's names
  ✕ Addresses only attackers ask for  refused: /content/view/…: pages by node number (only the admin needs them)  [EXP-SYSVIEW]
  ...
This visitor gets "not found" (404) — the site never sees it. Decided by EXP-SYSVIEW.
```

`--ip=<Adresse>` und `--ua="<User-Agent>"` verfolgen die Anfrage als anderer Besucher. Mit `set debug-header on` trägt
jede Antwort `X-RS: <Entscheidung>; rule=<ID>`, sichtbar im Netzwerk-Tab des Browsers; schalten Sie es danach wieder
aus.

Nachjustieren in Kürze: eine Regel, die echte Besucher abweist, bekommt eine Ausnahme (`unblock ... at`, `allow`,
`query`); ein Budget, das echte Besucher prüft, bekommt mit `replace` größere Zahlen; eine Adresse, die nie geprüft
werden soll, bekommt `exempt`. Jede Änderung mit einem `expect`-Beispiel.

## Regeln mit expect-Zeilen testen

Jede Regel sollte mindestens ein Beispiel haben, das sie entscheidet, und einen knappen Fall, der durchgehen muss:

```
[SITE-OLD] block /old/**                         # das Verzeichnis der alten Website
expect GET /old/page                 404         # was sie abweist
expect GET /oldies                   answered    # knapp daneben: muss durchgehen
```

```bash
php extension/exp_request_shield/bin/request-shield test settings/request-shield/request-shield.rules
php extension/exp_request_shield/bin/request-shield test settings/request-shield/request-shield.rules --only=SITE-OLD
php extension/exp_request_shield/bin/request-shield test settings/request-shield/request-shield.rules --junit=var/tmp/rules.xml
```

`test` nennt am Ende die Regeln ohne Beispiel. Bevor Sie eine Änderung durchsetzen, schicken Sie echten Verkehr durch
die Regeln: das Zugriffslog des Webservers (nur dessen 2xx- und 3xx-Zeilen zählen) oder eine im Netzwerk-Tab des
Browsers als HAR gespeicherte Sitzung:

```bash
php extension/exp_request_shield/bin/request-shield replay settings/request-shield/request-shield.rules /var/log/httpd/access_log --ip=log
php extension/exp_request_shield/bin/request-shield replay settings/request-shield/request-shield.rules session.har
```

Es nennt jede gute Anfrage, die die Regeln abweisen würden, mit der Regel.

## Nach einem Update der Erweiterung

Die Kopien von `exponential.rules` und `exponential-admin-*.rules` in `settings/request-shield/` werden **einmal**
geschrieben, wenn die Datei der Website entsteht, und ein Update der Erweiterung ändert sie nie. Nach jedem Update:

```bash
diff -u settings/request-shield/exponential.rules           extension/exp_request_shield/rules/exponential.rules
diff -u settings/request-shield/exponential-admin-uri.rules extension/exp_request_shield/rules/exponential-admin-uri.rules
```

Sind die Kopien unverändert (alle Änderungen der Website stehen in der Hauptdatei, wie empfohlen), kopieren Sie die
neuen Versionen darüber; sonst übernehmen Sie die Änderungen von Hand. Dann `check` und `test`. Das Änderungsprotokoll
der Erweiterung sagt, wann sich eine mitgelieferte Regel geändert hat, etwa das Bildbudget und die Methoden des
Layout-Editors in 0.0.1.4.

## Verwandte Seiten

- [Request Shield: Konfigurationsreferenz](konfigurationsreferenz.md)
- [Request Shield: Fehlersuche](fehlersuche.md)
- [Request Shield: beobachten und durchsetzen](beobachten-und-durchsetzen.md)
- [Den Request Shield einrichten](../../anleitungen/request-shield.md)
- Der [Exponential-Anwendungsfall](https://github.com/cjw-network/request-shield/blob/main/docs/use-cases/exponential.md)
  der Bibliothek, mit dem Grund für jede mitgelieferte Regel (auf Englisch)
