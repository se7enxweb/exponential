---
title: "Request Shield: Fehlersuche"
audience: admin, hosting, developer
category: administration
order: 70
summary: Symptome des Request Shields mit Ursache und Abhilfe - für diese Seite nicht gelaufen, Regeln nicht lesbar, alles geht durch, Dateien von root, Redakteure bei der Browserprüfung, 429 für echte Besucher, 405 für Formulare und die API, Vorwärmer und eigene Skripte, ein Proxy oder CDN davor, Shared Hosting, veraltete Regelkopien, abgewiesene Seiten-Aliase, Testfehler, ein leeres Diagramm, 403 auf /rs.
keywords: Request Shield, Fehlersuche, Probleme, 405, Methode, Vorladen, X-Exp-Self, Cloudflare, Proxy, trust, X-Forwarded-For, Shared Hosting, open_basedir, Stufe, Crawler, PDF, replace, Regelkopien, lief für diese Seite nicht, Regeln können nicht gelesen werden, lässt durch, shield failed, Berechtigungen, Eigentümer, root, Browserprüfung, 429, Seiten-Alias, EXP-MODULES, EXP-SYSVIEW, EXP-ORIGIN, www.example.org, leeres Diagramm, log-level, /rs, SITE-DASH, PAUSED, trace, troubleshooting
type: help
---
# Request Shield: Fehlersuche

## Inhalt

1. [Erste Hilfe](#erste-hilfe)
2. [Symptome, Ursachen und Abhilfe](#symptome-ursachen-und-abhilfe)
3. [Echte Besucher bekommen 429](#echte-besucher-bekommen-429)
4. [Formulare, die API oder der Layout-Editor bekommen 405](#formulare-die-api-oder-der-layout-editor-bekommen-405)
5. [Vorwärmer und eigene Skripte](#vorwärmer-und-eigene-skripte)
6. [Ein Proxy, CDN oder zweiter Webserver davor](#ein-proxy-cdn-oder-zweiter-webserver-davor)
7. [Shared Hosting](#shared-hosting)
8. [Herausfinden, warum eine Anfrage so entschieden wurde](#herausfinden-warum-eine-anfrage-so-entschieden-wurde)

## Erste Hilfe

Wenn Sie den Verdacht haben, dass der Shield echte Besucher abweist, und die Website **sofort** funktionieren muss:
Klicken Sie **Pausieren** auf der Übersicht, oder legen Sie auf dem Server die Datei `var/request-shield/PAUSED` an.
Der Shield entscheidet dann ab der nächsten Anfrage nichts mehr, auf jedem Server. Finden Sie die Ursache im
Protokoll, beheben Sie sie, dann **Einschalten**.

## Symptome, Ursachen und Abhilfe

| Symptom | Ursache | Abhilfe |
|---|---|---|
| Übersicht: „Der Shield lief für diese Seite nicht“ | der Hook steht nicht in `config.php`, oder Velocity wurde nach dem Hinzufügen nicht neu gestartet, oder Velocity verwendet ein `index.php` älter als 6.0.15 | den Hook hinzufügen ([der Leitfaden](../../anleitungen/request-shield.md#1-auf-einer-bestehenden-website-installieren)); `./console exp:velocity restart` |
| „Die Regeln können nicht gelesen werden“ | ein Fehler in einer Regeldatei | `bin/request-shield check <Hauptregeldatei>` nennt Datei und Zeile |
| Im Modus enforce geht alles durch; das Fehlerlog von PHP meldet, der Shield sei gescheitert und habe die Anfrage durchgelassen | der Shield lässt im Fehlerfall durch: ein Speicher- oder Cache-Verzeichnis, das er nicht beschreiben kann | `var/request-shield/` für den PHP-Benutzer der Website beschreibbar machen |
| Dateien in `var/request-shield/` gehören root, die Website kann sie nicht lesen | die Kommandozeile lief als root | `bin/request-shield` als Webbenutzer ausführen; den Eigentümer korrigieren |
| Eine Redakteurin oder ein Redakteur erhält in der Administration die Seite der Browserprüfung | die Administration verlangt die Browserprüfung einmal pro Pass | JavaScript und Cookies erlauben; für eine feste Büroadresse `exempt <Adresse>` in der Regeldatei |
| Hintergrundanfragen in der Administration scheitern mit 429 | eine Hintergrundanfrage ohne Pass erhält die Prüfung als JSON | die Seite lädt sich selbst neu; weitere Hintergrundpfade mit `api-path` nennen |
| Der Layout-Editor meldet „Something went wrong“, wenn Sie einen Entwurf verwerfen oder speichern (405 durch die Regel `methods` im Log) | die Kopie der Admin-Regeln der Website ist älter als exp_request_shield 0.0.1.4 und erlaubt nur GET, HEAD, POST und OPTIONS | `EXP-ADMIN-SPA` (oder `EXP-AH-SPA` bei einem eigenen Admin-Host) samt ihrer `method`-Zeile aus `rules/exponential-admin-uri.rules` (oder `-host.rules`) der Extension in die Kopie der Website übernehmen |
| Setup > Preload sites bricht nach etwa 120 Seiten mit 429 ab, oder die Prüfung nach `exp:upgrade` scheitert an `/user/login` mit 429 | das Tempolimit oder die Browserprüfung der Anmeldung hat die eigenen Anfragen des Servers beantwortet | exp_request_shield auf 0.0.1.5 oder neuer aktualisieren: der Kernel signiert seine eigenen Anfragen (`X-Exp-Self`), und der Shield lässt sie durch |
| Echte Besucher bekommen 429 | die Seite der Browserprüfung (Status 429, normal: der Browser löst sie), oder ein Budget, das sich viele Besucher teilen | [Echte Besucher bekommen 429](#echte-besucher-bekommen-429) |
| Ein Formular, die REST-API oder der Layout-Editor antwortet mit 405 | die Methode ist dort nicht erlaubt (`EXP-POST`, `method`) | [Formulare, die API oder der Layout-Editor bekommen 405](#formulare-die-api-oder-der-layout-editor-bekommen-405) |
| Fast jede Logzeile zeigt dasselbe Netz; oder es wird nie etwas gezählt | ein Proxy oder CDN davor, dem die Regeln nicht vertrauen (`trust`) | [Ein Proxy, CDN oder zweiter Webserver davor](#ein-proxy-cdn-oder-zweiter-webserver-davor) |
| Crawler bekommen bei PDFs oder der Suche die Browserprüfung | ein Crawler, der nur den Namen einer Suchmaschine sendet, wird wie jeder Besucher behandelt; bestätigte Crawler bekommen nie die Prüfung | nichts zu beheben; einen eigenen Crawler begrüßen Sie mit `crawler <Art> ua /<Muster>/ dns <Suffixe>` |
| Nach `replace [EXP-SEARCHES] ...` zählt jede Seite auf das Suchbudget | die mitgelieferte Regel steht in einem `match`-Block; der Ersatz wurde ohne `at` geschrieben | `at regex (?i)/content/(advanced)?search(/\|$)` ergänzen ([Kochbuch](kochbuch.md#die-suche-schützen)) |
| `test` lässt nach einem `replace` mitgelieferte Beispiele scheitern (`expected uncached, got check by EXP-SEARCHES`) | die mitgelieferten Beispiele gelten den alten Zahlen | so gewollt; es zählen Ihre Beispiele neben dem `replace` |
| Eine Regel aus einer neuen Version der Erweiterung fehlt auf der Website (etwa `EXP-ADMIN-SPA`, `EXP-IMAGES`) | die Kopien in `settings/request-shield/` werden einmal geschrieben und von keinem Update geändert | mit `extension/exp_request_shield/rules/` vergleichen ([Kochbuch](kochbuch.md#nach-einem-update-der-erweiterung)) |
| Ein Seiten-Alias wird abgewiesen (404 durch `EXP-MODULES` oder `EXP-SYSVIEW`) | der Alias beginnt mit einem Modulnamen | den Alias als Ausnahme mit einem `expect`-Beispiel aufnehmen ([Beobachten und durchsetzen](beobachten-und-durchsetzen.md)) |
| `bin/request-shield test` lässt nach dem Eintragen Ihrer Hosts fünf Beispiele mit `Origin: https://www.example.org` scheitern (403 durch `EXP-ORIGIN`) | die mitgelieferten Beispiele verwenden die Dokumentationsdomain `www.example.org` | `host www.example.org` hinzufügen: eine reservierte Domain, nie die eines echten Besuchers |
| Das Diagramm der Übersicht ist leer | die Regeln nennen keine Protokolldatei, oder `log-level off` | `set log ...`; `set log-level flag` |
| `/rs/...` antwortet mit 403 | die vorgegebene Regel `SITE-DASH` sperrt die eigenen Seiten der Bibliothek für alle außer dem Server | sie für Ihre Adressen oder mit einem Token öffnen ([dashboard.md der Extension](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/dashboard.md), auf Englisch) |
| Eine Modus-Schaltfläche ist deaktiviert | der Webserver kann die Regeldatei oder ihr Verzeichnis nicht beschreiben | beschreibbar machen, oder `set mode` auf dem Server ändern |
| Unter Velocity `X-RS: allow-uncached shield error` bei Abweisungen | eine Bibliothek mit geänderten inneren Schritten: die Extension ist auf den einfachen Aufruf zurückgefallen | die Extension aktualisieren |

## Echte Besucher bekommen 429

Sehen Sie zuerst die Logzeile an (Setup > Request Shield > Log, oder `grep` nach dem Netz der Adresse in
`var/request-shield/log/shield.log`):

| Logzeile | Was geschah | Was zu tun ist |
|---|---|---|
| `challenge 429 "always" rule=EXP-LOGIN` (oder `EXP-FORMS`, `EXP-ADMIN-CHECK`) | die Seite der Browserprüfung wurde gesendet; ihr Status ist 429. Ein Browser mit JavaScript und Cookies löst sie in einer Sekunde und geht weiter | nichts. Bleibt der Besucher auf der Seite: JavaScript oder Cookies sind blockiert (ein Datenschutz-Add-on), was die Seite auch sagt |
| `challenge 429 "pages" rule=EXP-PACE` | mehr als 120 Seiten pro Minute von einer Adresse: oft ein Büro, eine Schule oder ein Mobilfunknetz hinter einer IPv4-Adresse | die Adresse des Büros mit `exempt` ausnehmen, oder ein schnelleres Tempo ([Kochbuch](kochbuch.md#bildreiche-seiten-und-belebte-intranets)) |
| `challenge 429 "images" rule=EXP-IMAGES` | eine Seite mit Tausenden neuer Bilder (bei Bedarf erzeugte Aliase) | ein größeres Bildbudget ([Kochbuch](kochbuch.md#bildreiche-seiten-und-belebte-intranets)) |
| `challenge 429 "searches" rule=EXP-SEARCHES` | mehr als zehn Suchen pro Minute | für einen Menschen nur beim schnellen Blättern normal; erhöhen Sie das Budget, wenn die Suche billig ist |
| `throttle 429 "banned"` | ein Bann (`ban after ...`) | der Besucher hat vorher viele Abweisungen verursacht; suchen Sie diese Zeilen. Ein Bann endet von selbst (spätestens nach `ban-max`); die Listenseite der Bibliothek (`/rs/waf/lists`) kann ihn früher aufheben |
| derselbe Besucher wird immer wieder geprüft | der Pass ist an den Browser gebunden (`bind-user-agent on`), oder das Geheimnis unterscheidet sich zwischen den Servern | ein Geheimnis auf jedem Server ([mehrere Webserver](kochbuch.md#mehrere-webserver)) |
| alle bekommen gleichzeitig 429 | alle Besucher teilen sich eine Adresse: einem Proxy davor wird nicht vertraut | [ein Proxy davor](#ein-proxy-cdn-oder-zweiter-webserver-davor) |

Während Sie suchen, lässt **Beobachten** auf der Übersicht alle herein und protokolliert trotzdem, was entschieden
worden wäre.

## Formulare, die API oder der Layout-Editor bekommen 405

Das Log sagt `reject 405 "method"`. Der Shield erlaubt POST nur dort, wo Exponential Formulare annimmt (`EXP-POST`),
und PUT, PATCH und DELETE nur für die API des Layout-Editors (`EXP-ADMIN-SPA`).

- Ein Formular einer eigenen Erweiterung: `allow POST **/<Modul>/**` ([Kochbuch](kochbuch.md#formulare-eigener-erweiterungen)).
- Die REST-API: `method ... PUT PATCH DELETE` und `allow PUT PATCH DELETE /api/**` ([Kochbuch](kochbuch.md#die-rest-api)).
- „Entwurf verwerfen“ oder „Speichern“ im Layout-Editor meldet „Something went wrong“: die Kopie der Admin-Regeln der
  Website ist älter als exp_request_shield 0.0.1.4; übernehmen Sie `EXP-ADMIN-SPA` und seine `method`-Zeile aus
  `rules/exponential-admin-uri.rules` der Erweiterung (oder `EXP-AH-SPA` aus `-host.rules`).

## Vorwärmer und eigene Skripte

Das Tempo erlaubt 120 Seiten pro Minute und Adresse, und die Anmeldung verlangt immer die Browserprüfung, die kein
Skript lösen kann. Ein eigener Crawler wird also nach 120 Seiten geprüft:

- **Setup > Vorladen, die Adressprüfung nach dem Upgrade, statisches Veröffentlichen**: sie signieren ihre Anfragen
  (`X-Exp-Self`) seit exp_request_shield 0.0.1.5 mit dem Kernel ab dem 14.11.2026. Aktualisieren Sie beides.
- **Ein eigenes Skript auf derselben Installation**: signieren Sie es mit `expRequestShield::selfHeader()`
  ([Kochbuch](kochbuch.md#die-anfragen-eigener-skripte-signieren)).
- **Ein Werkzeug auf einem anderen Rechner** (das Vorwärmen eines CDN, ein SEO-Crawler):
  `exempt <seine Adresse> until <Tag>`.
- Eine signierte Anfrage bekommt trotzdem 429: die Signatur ist älter als zwei Minuten (eine pro Anfrage erzeugen),
  wurde für eine andere Adresse oder Methode erzeugt, oder das Skript kann `var/request-shield/cache/self.key` nicht
  lesen (als Benutzer der Website ausführen).

## Ein Proxy, CDN oder zweiter Webserver davor

| Aufbau | Symptom ohne Abhilfe | Abhilfe |
|---|---|---|
| Cloudflare oder ein anderes CDN | jeder Besucher scheint vom CDN zu kommen: alle teilen ein Budget und bekommen gemeinsam 429 | den Netzen des CDN mit `trust` vertrauen, oder den Webserver `REMOTE_ADDR` setzen lassen (`mod_remoteip`, nginx `real_ip`) ([Kochbuch](kochbuch.md#hinter-cloudflare-einem-cdn-oder-einem-load-balancer)) |
| nginx vor Apache auf demselben Rechner | jede Anfrage kommt von `127.0.0.1`, das ausgenommen ist: es wird nie etwas gezählt oder geprüft | `trust 127.0.0.1 ::1` und `X-Forwarded-For` von nginx ([Kochbuch](kochbuch.md#nginx-apache-litespeed-und-velocity-davor)) |
| ein Load Balancer und zwei Webserver | Besucher werden auf dem anderen Server erneut geprüft; Budgets zählen doppelt so hoch | ein `secret` für alle, `store file` in einem gemeinsamen Verzeichnis ([Kochbuch](kochbuch.md#mehrere-webserver)) |
| LiteSpeed mit Cloudflare | wie bei Cloudflare | „Use Client IP in Header“ für vertrauenswürdige Adressen, oder `trust` |

Der Shield glaubt `X-Forwarded-For` nur von einer vertrauenswürdigen Adresse; ein Besucher kann sich also nicht mit dem
Header als jemand anderes ausgeben.

## Shared Hosting

| Symptom | Ursache | Abhilfe |
|---|---|---|
| `check` meldet Stufe S0: Budgets, Banns und das Pass-Cookie sind aus | kein beschreibbares Verzeichnis für den Speicher | `var/request-shield/` für den PHP-Benutzer der Website beschreibbar machen |
| `check` meldet Stufe S1 | kein APCu: Zähler in Dateien | nichts zu beheben; etwa 25 Mikrosekunden pro Anfrage |
| Das Fehlerlog von PHP meldet `open_basedir restriction` vom Shield | `store-dir` oder `log` außerhalb der erlaubten Verzeichnisse | beide innerhalb der Installation halten (`var/request-shield/...`) |
| Der Benutzer für das Deployment und der des Webservers können die Dateien des anderen nicht lesen | der Shield schreibt `0600` | `set file-mode 0640`, `set dir-mode 02750`, eine gemeinsame Gruppe |

## Herausfinden, warum eine Anfrage so entschieden wurde

Das Protokoll nennt die Regel. Um die Entscheidung Schritt für Schritt zu sehen, auf dem Server, als Webbenutzer:

```bash
php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.org/the/path"
```

Mit `set debug-header on` in der Regeldatei trägt jede Antwort einen Header `X-RS` mit der Entscheidung; schalten Sie
ihn danach wieder aus.
