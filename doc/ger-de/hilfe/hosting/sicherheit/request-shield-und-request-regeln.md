---
title: request-shield und Request-Regeln
audience: hosting, admin
category: hosting
order: 50
summary: Die drei Schichten für Anfragen, die ein Hoster mit Exponential nutzen kann - Request Shield, eine Firewall, die entscheidet, bevor der Kernel startet, und seit 6.0.15 Teil der Standardinstallation ist (exp_request_shield); der Request-Klassifizierer des Kernels, der nur in security.log einordnet; und die Request-Regeln des Kernels, die entscheiden, bevor eine Modulansicht läuft. Wie Sie jede installieren und einschalten, wie sie sich unterscheiden und wie ein Hoster sie anbieten kann.
keywords: request-shield, Request Shield, exp_request_shield, Setup > Request Shield, WAF, Web Application Firewall, Request-Klassifizierer, security.log, requestclassifier.ini, Request-Regeln, requestrules.ini, ezrequestrules.php, Überwachungsmodus, enforce, EXP_REQUEST_PREPEND_FILE, Hook in config.php, PAUSED, Velocity, System-URLs, Brute Force, Scanner, web application firewall, request classifier, request rules, monitor mode, system URLs, brute force, scanners
type: help
---
# request-shield und Request-Regeln

Diese Seite ist für Systemadministratoren und Sicherheitsverantwortliche bei einem Hoster. Drei Schichten betrachten eine Anfrage an
Exponential, bevor oder während der Kernel sie bearbeitet, und sie werden oft verwechselt. Diese Seite sagt, was jede tut, wo sie läuft und
wie ein Hoster sie einschaltet. Die Fakten zu request-shield stammen aus
[Sicherheitshärtung: Ein Schutz davor](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield); zum Klassifizierer aus
[Anfrage-Logs](../../../funktionen/6.0/anfrageprotokolle.md); zu den Request-Regeln aus
[Request-Regeln](../../../bc/6.0/sicherheit-von-content-view-full.md).

## Inhalt

1. [Die drei Schichten](#die-drei-schichten)
2. [request-shield](#request-shield)
3. [Der Request-Klassifizierer](#der-request-klassifizierer)
4. [Request-Regeln](#request-regeln)
5. [Es Kunden anbieten](#es-kunden-anbieten)
6. [Checkliste](#checkliste)

## Die drei Schichten

| | request-shield | Request-Klassifizierer | Request-Regeln |
|---|---|---|---|
| Was es tut | **entscheidet**: weist ab, bremst, stellt eine Prüfung, sperrt eine Zeit lang oder protokolliert nur (Überwachungsmodus) | **ordnet** nur **ein**: Abtastungen, Traversal, Injection, Angriffswerkzeuge, Häufungen von 404 | **entscheidet** pro Modulansicht: Umleitung, nicht gefunden, Zugriff verweigert, erst anmelden, umschreiben, protokollieren |
| Wo es läuft | bevor der Kernel startet | im Kernel, am Ende jeder Anfrage | im Kernel, kurz bevor eine Modulansicht läuft |
| Teil von Exponential | ja, seit 6.0.15: die Erweiterung `exp_request_shield` bringt ihn mit (Bibliothek unter MIT-Lizenz, vom CJW Network), wie installiert durchsetzend | ja, standardmäßig an | ja; drei Regeln werden definiert und ausgeschaltet ausgeliefert |
| Einstellungen | seine Regeldateien in `settings/request-shield/` | `settings/requestclassifier.ini` | `settings/requestrules.ini` |
| Sein Log | `var/request-shield/log/shield.log`, gelesen in **Setup > Request Shield** | `var/log/security.log` | wie die Aktion einer Regel es bestimmt |

Eine Anfrage, die der Schutz abweist, erreicht den Kernel nie und steht also nicht in `security.log`. Im Überwachungsmodus des Schutzes
kann dieselbe Abtastung in beiden Logs stehen. Die Regelnamen für Abtastungen und Injection im Klassifizierer folgen den Namen von
request-shield in Kleinschreibung, sodass sich eine Zeile im einen Log im anderen finden lässt.

## request-shield

[Request Shield](https://request-shield.com) (Quellcode:
[cjw-network/request-shield](https://github.com/cjw-network/request-shield), von JAC Systeme GmbH / CJW Network,
MIT-Lizenz) betrachtet jede Anfrage, bevor der Kernel startet. Echte Besucher kommen in Mikrosekunden durch; eine
abgewiesene Anfrage kostet etwa 30 µs statt einer gerenderten Seite.

### Teil der Standardinstallation

Seit Exponential 6.0.15 hat eine neue Installation ihn. Die `composer.json` im Wurzelverzeichnis verlangt die
Erweiterung `exp_request_shield` (`se7enxweb/exp_request_shield`, GPL-2.0-or-later), die die Bibliothek (request-shield
0.4.0, dazu ein Build derselben Dateien für PHP 7.4; PHP 7.4 bis 8.5) und die Regeln für Exponential mitbringt. Eine
Hook-Zeile in `config.php` führt ihn vor dem Kernel und vor dem frühen Ausstieg des HTTP-Caches aus, sodass auch
zwischengespeicherte Seiten geschützt sind:

```php
if ( is_file( __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php' ) )
    require __DIR__ . '/extension/exp_request_shield/bootstrap/hook.php';
```

- **Neue Installationen** erhalten die Zeile aus `config.php-RECOMMENDED`; die 7x-Site-Pakete (`sevenx_multisite`,
  `cjw_multisite`) aktivieren außerdem die Erweiterung, die der Administration **Setup > Request Shield** hinzufügt.
- **Aktualisierte Installationen** tragen die Zeile selbst in `config.php` ein: Ein Update überschreibt `config.php`
  nie (`./console exp:config:list` zeigt, was in `config.php-RECOMMENDED` neu ist). Aktivieren Sie die Erweiterung
  (`ActiveExtensions[]=exp_request_shield`) für die Seiten in der Administration.

Derselbe Hook deckt beide Engines ab, und sie geben identische Antworten:

- **Apache oder nginx mit PHP-FPM**: `config.php` läuft bei jeder Anfrage, also führt der Hook den Schutz genau dort
  aus. `auto_prepend_file` wird nicht gebraucht, ein Hoster muss es also nicht mehr pro Pool setzen.
- **Velocity**: `config.php` läuft einmal pro Worker, daher setzt der Hook `EXP_REQUEST_PREPEND_FILE` selbst, und
  `index.php` führt den Schutz am Anfang jeder Anfrage aus. Es gibt keine Integrationsdatei, die Sie von Hand schreiben
  müssten; starten Sie Velocity neu, nachdem Sie den Hook hinzugefügt oder entfernt haben. Velocity beantwortet Seiten
  aus seinem eigenen Antwort-Cache, bevor ein Worker startet, sodass der Schutz nur sieht, was einen Worker erreicht.

### Regeln, Modi und Ausschalten

Der Schutz nutzt die eigene Regeldatei der Website, `settings/request-shield/request-shield.rules`, mit Kopien der
`exponential*.rules` der Erweiterung daneben, und die `rules/request-shield.rules` der Erweiterung, solange die Website
keine eigene hat. Wählen Sie dort die Datei für die Administration:

| Datei | Wann |
|---|---|
| `exponential.rules` | die Regeln für das Frontend; von einer der beiden folgenden eingebunden |
| `exponential-admin-uri.rules` | die Administration ist der Siteaccess `/admin` auf demselben Host |
| `exponential-admin-host.rules` | die Administration hat einen eigenen Host |

Die Standardregeln behandeln auch jede andere Administration, die über einen Pfad erreicht wird, wie `/admin`:
`/adminui`, `/editor`, `/admin2` bis `/admin4l` und `/admintest_*` behalten ihre System-URLs, Module und Formulare
(seit 0.0.1.1). Für eine Administration mit eigenem Host bindet die Hauptdatei der Website zuerst `exponential.rules`
ein, danach ihre eigenen Einstellungen (sie gewinnen) und zuletzt die Kopie von `exponential-admin-host.rules`, mit
den Admin-Hostnamen der Website in ihrer `site`-Zeile und ohne ihr eigenes `include exponential.rules`
([Konfiguration](https://github.com/se7enxweb/exp_request_shield/blob/main/doc/configuration.md)). Die eigene
JSON-API des Shields bleibt standardmäßig aus.

1. Die Regeln **setzen wie installiert durch** (`enforce`). Wechseln Sie bei einer Website mit ungewöhnlichen
   Adressen (Seiten-Aliase, die mit einem Modulnamen beginnen, Formulare eigener Erweiterungen) zu `set mode monitor`,
   während Sie das Log lesen, und dann zurück zu `enforce`.
2. Passen Sie die Zeilen an, die von der Website abhängen: URI-Siteaccesses (`EXP-MODULES`), Module von Erweiterungen,
   die Formulare annehmen (`EXP-POST`), Hostnamen und Load Balancer (`host`, `trust`).
3. Prüfen Sie nach jeder Änderung, als PHP-Benutzer der Website:

   ```bash
   php extension/exp_request_shield/bin/request-shield check settings/request-shield/request-shield.rules
   php extension/exp_request_shield/bin/request-shield test  settings/request-shield/request-shield.rules
   php extension/exp_request_shield/bin/request-shield trace settings/request-shield/request-shield.rules "GET https://www.example.com/user/login"
   ```

4. **Setup > Request Shield** (in der Gruppe System, direkt nach Logdateien; Richtlinien `requestshield/read` und
   `requestshield/manage`) zeigt das Dashboard, das Log und die Regeln und hat Schalter: pausieren, durchsetzen,
   überwachen, aus.
5. Um ihn sofort auf jedem Server auszuschalten, legen Sie `var/request-shield/PAUSED` an; entfernen Sie die Datei, um
   den Schutz wieder einzuschalten. `set mode off` in den Regeln tut dasselbe auf Dauer.

Die Beispielregeln verbergen System-URLs und Module der Administration auf der öffentlichen Website, weisen interne Pfade ab, stellen eine
Browserprüfung vor die Anmeldung und die Administration und sperren eine Adresse für eine Stunde nach zehn Abweisungen in zehn Minuten
(verifizierte Suchmaschinen werden nie gesperrt). Der Schutz kann ein richtiges Passwort nicht von einem falschen unterscheiden und sieht
keine statischen Dateien, die der Webserver selbst ausliefert: Die Kontosperre bleibt Aufgabe von Exponential
(`MaxNumberOfFailedLogin`, zweistufige Anmeldung).

Die Funktion: [Request Shield](../../../funktionen/6.0/request-shield.md); Schritt für Schritt:
[Anleitung zum Request Shield](../../../anleitungen/request-shield.md); was sich für bestehende Installationen ändert:
[Request Shield (6.0.15)](../../../bc/6.0/request-shield.md).

## Der Request-Klassifizierer

Der Kernel ordnet jede Anfrage einmal ein und schreibt, was nach einem Angriff aussieht, mit Kategorie, Regel und Konfidenz nach
`var/log/security.log`, unabhängig vom Status. Regelsätze: `probes`, `traversal`, `injection`, `scanner`, `threshold`
(`Max404PerMinute=30` innerhalb von `WindowSeconds=60`).

- Er **blockiert nie**. Blockieren ist Sache des Schutzes, des Webservers oder einer Listener-Klasse, die in
  `[ClassifierSettings] Listeners[]` registriert ist (standardmäßig keine).
- Adressen werden maskiert, der User Agent gehasht und gekürzt; Werte der Query bleiben als Beweis in `security.log`, mit maskierten
  Geheimnissen, E-Mail- und IP-Adressen.
- `Classification=disabled` schaltet ihn aus; lassen Sie einen Regelsatz in `RuleSets[]` weg, um ihn auszuschalten.

Lesen und weiterleiten: [Sicherheitslog](../betrieb/sicherheitslog.md) und [Anfrage-Logs](../betrieb/anfrage-logs.md).

## Request-Regeln

Die Request-Regeln entscheiden kurz bevor eine Modulansicht läuft: *wann* (als System-URL eingegeben, volle Ansicht, dem Benutzer fehlt
eine Richtlinie, unterhalb eines Knotens, aus einem Netzwerk) und *was* (Umleitung auf den URL-Alias, nicht gefunden, Zugriff verweigert,
erst anmelden, Umleitung, umschreiben, protokollieren). Ausnahmen sind Richtlinien und werden also im Rolleneditor vergeben. Nichts ändert
sich, bis eine Regel in `RuleList[]` aufgeführt ist.

Der häufige Fall, anonyme Besucher von `/content/view/full/<node>` fernzuhalten, während der URL-Alias weiter funktioniert, ist eine
mitgelieferte Regel, die pro öffentlichem Siteaccess eingeschaltet wird:

```ini
# settings/siteaccess/<public siteaccess>/requestrules.ini.append.php
[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_full_view_to_alias
```

Lassen Sie die Siteaccesses der Administration und der Redaktion unverändert: Redakteure nutzen dort System-URLs. Prüfen und erklären:

```bash
php bin/php/ezrequestrules.php -s <siteaccess> --list  --allow-root-user
php bin/php/ezrequestrules.php -s <siteaccess> --check --allow-root-user   # exit code 1 on problems, for CI
php bin/php/ezrequestrules.php -s <siteaccess> --uri=content/view/full/2 --allow-root-user
```

> **Warnung**
> Regeln beschränken, *wie* erlaubte Inhalte erreicht werden dürfen; sie ersetzen keine Einschränkungen von `content/read`. Eine Bedingung
> `ip` ist nur so verlässlich wie die Adresse des Besuchers: Konfigurieren Sie hinter einem Proxy zuerst `TrustedProxies[]` und
> `ClientIpByCustomHTTPHeader` ([TLS und HTTPS](tls-und-https.md#proxys-die-tls-beenden)).

## Es Kunden anbieten

Das sind Möglichkeiten, die ein Hoster wählen kann:

- Request Shield durchsetzend auf jeder Website, wie installiert, mit Überwachungsmodus in der ersten Woche auf einer Website mit
  ungewöhnlichen Adressen, als Teil eines verwalteten Tarifs;
- die Administration hinter der Browserprüfung des Schutzes oder auf das Netzwerk des Kunden beschränkt (`EXP-ADMIN-NET`);
- die Regel für System-URLs auf öffentlichen Siteaccesses als Standard der Server-Vorlage;
- ein wöchentlicher Blick in `security.log` und das Log des Schutzes, mit einem Bericht an den Kunden, wenn eine Regel stark ansteigt.

## Checkliste

- [ ] `exp_request_shield` installiert und aktiv; die Regeln der Website in `settings/request-shield/`, nie ausgeliefert.
- [ ] Die Hook-Zeile in `config.php` (eine aktualisierte Installation trägt sie selbst ein); Velocity danach neu gestartet;
      Setup > Request Shield meldet nicht, dass er für diese Seite nicht lief.
- [ ] `check` und `test` bestehen; `enforce`, oder `monitor` nur, solange das Log geprüft wird.
- [ ] `security.log` wöchentlich gelesen; der Klassifizierer bleibt an.
- [ ] Request-Regeln mit `--check` auf jedem Siteaccess geprüft, der sie nutzt.
- [ ] Proxys korrekt vertraut, bevor man sich auf eine Regel nach Adresse verlässt.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Die Administration einschränken](die-administration-einschraenken.md)
- [Sicherheitslog](../betrieb/sicherheitslog.md)
- [HTTP-Cache: Mit request-shield davor](../../../bc/6.0/httpcache.md#mit-vorgeschaltetem-request-shield)
- [Request Shield](../../../funktionen/6.0/request-shield.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
