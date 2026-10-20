---
title: request-shield und Request-Regeln
audience: hosting, admin
category: hosting
order: 50
summary: Die drei Schichten für Anfragen, die ein Hoster mit Exponential nutzen kann - request-shield, eine Firewall, die entscheidet, bevor der Kernel startet; der Request-Klassifizierer des Kernels, der nur in security.log einordnet; und die Request-Regeln des Kernels, die entscheiden, bevor eine Modulansicht läuft. Wie Sie jede installieren und einschalten, wie sie sich unterscheiden und wie ein Hoster sie anbieten kann.
keywords: request-shield, WAF, Web Application Firewall, Request-Klassifizierer, security.log, requestclassifier.ini, Request-Regeln, requestrules.ini, ezrequestrules.php, Überwachungsmodus, enforce, EXP_REQUEST_PREPEND_FILE, auto_prepend_file, Velocity, System-URLs, Brute Force, Scanner, web application firewall, request classifier, request rules, monitor mode, system URLs, brute force, scanners
type: help
---
# request-shield und Request-Regeln

Diese Seite ist für Systemadministratoren und Sicherheitsverantwortliche bei einem Hoster. Drei Schichten betrachten eine Anfrage an
Exponential, bevor oder während der Kernel sie bearbeitet, und sie werden oft verwechselt. Diese Seite sagt, was jede tut, wo sie läuft und
wie ein Hoster sie einschaltet. Die Fakten zu request-shield stammen aus
[Sicherheitshärtung: Ein Schutz davor](../../sicherheitshaertung.md#ein-vorgeschalteter-schutzschild-request-shield); zum Klassifizierer aus
[Anfrage-Logs](../../../../eng-us/features/6.0/request-logs.md); zu den Request-Regeln aus
[Request-Regeln](../../../../eng-us/bc/6.0/view_full_security.md).

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
| Teil von Exponential | nein; vom Hoster oder der Website hinzugefügt (MIT-Lizenz, vom CJW Network) | ja, standardmäßig an | ja; drei Regeln werden definiert und ausgeschaltet ausgeliefert |
| Einstellungen | eigene Regeldateien | `settings/requestclassifier.ini` | `settings/requestrules.ini` |
| Sein Log | ein eigenes Log und eine Live-Ansicht unter `/rs/` | `var/log/security.log` | wie die Aktion einer Regel es bestimmt |

Eine Anfrage, die der Schutz abweist, erreicht den Kernel nie und steht also nicht in `security.log`. Im Überwachungsmodus des Schutzes
kann dieselbe Abtastung in beiden Logs stehen. Die Regelnamen für Abtastungen und Injection im Klassifizierer folgen den Namen von
request-shield in Kleinschreibung, sodass sich eine Zeile im einen Log im anderen finden lässt.

## request-shield

[request-shield](https://request-shield.com) (Quellcode:
[cjw-network/request-shield](https://github.com/cjw-network/request-shield)) betrachtet jede Anfrage, bevor der Kernel startet. Echte
Besucher kommen in Mikrosekunden durch; eine abgewiesene Anfrage kostet etwa 30 µs statt einer gerenderten Seite.

### Installieren

Es ist nicht Teil der `composer.json` von Exponential. Entweder:

```bash
composer require cjw-network/request-shield
```

oder ein Checkout oder Release der Bibliothek in einem Verzeichnis **außerhalb** des Document Root. Kopieren Sie dann die Beispielregeln
für Exponential aus dem Verzeichnis `examples/exponential/` der Bibliothek in ein Verzeichnis, das der Webserver nie ausliefert (die
Bibliothek schlägt `settings/request-shield/` vor), und wählen Sie die Hauptdatei:

| Datei | Wann |
|---|---|
| `exponential.rules` | die Regeln für das Frontend; von einer der beiden folgenden eingebunden |
| `exponential-admin-uri.rules` | die Administration ist der Siteaccess `/admin` auf demselben Host |
| `exponential-admin-host.rules` | die Administration hat einen eigenen Host |

### Einschalten

Der Schutz muss am Anfang **jeder** Anfrage laufen:

- **Apache oder nginx mit PHP-FPM**: ein Aufruf `Shield::protectFile()` oben in `config.php` oder `auto_prepend_file` von PHP, das auf
  `bootstrap.php` der Bibliothek zeigt. Mit `auto_prepend_file` in der Konfiguration des Pools kann ein Hoster es pro Kunde einschalten,
  ohne die Dateien des Kunden anzufassen.
- **Velocity**: `config.php` läuft einmal pro Worker, nicht pro Anfrage. Seit 6.0.15 führt `index.php` am Anfang jeder Anfrage die Datei
  aus, die `config.php` in der Konstante `EXP_REQUEST_PREPEND_FILE` nennt; `config.php-RECOMMENDED` zeigt das Muster. Velocity
  beantwortet zwischengespeicherte Seiten, bevor ein Worker startet, sodass der Schutz nur sieht, was einen Worker erreicht.

Dann, in dieser Reihenfolge:

1. Beginnen Sie im **Überwachungsmodus** (`set mode monitor`, wie die Beispiele ausgeliefert werden) und lesen Sie sein Log einige Tage
   lang, während Redakteure arbeiten.
2. Passen Sie die Zeilen an, die von der Website abhängen: URI-Siteaccesses (`EXP-MODULES`), Module von Erweiterungen, die Formulare
   annehmen (`EXP-POST`), Hostnamen und Load Balancer (`host`, `trust`).
3. Prüfen Sie nach jeder Änderung:

   ```bash
   bin/request-shield check <main file>
   bin/request-shield test <main file>
   bin/request-shield trace <main file> "GET https://www.example.com/user/login"
   ```

4. Wechseln Sie zu `set mode enforce`.
5. Um ihn auszuschalten, nutzen Sie `set mode off`, statt das Einbinden zu entfernen; unter Velocity würde ein persistenter Worker sonst
   die Markierungen der letzten Anfrage behalten.

Die Beispielregeln verbergen System-URLs und Module der Administration auf der öffentlichen Website, weisen interne Pfade ab, stellen eine
Browserprüfung vor die Anmeldung und die Administration und sperren eine Adresse für eine Stunde nach zehn Abweisungen in zehn Minuten
(verifizierte Suchmaschinen werden nie gesperrt). Der Schutz kann ein richtiges Passwort nicht von einem falschen unterscheiden und sieht
keine statischen Dateien, die der Webserver selbst ausliefert: Die Kontosperre bleibt Aufgabe von Exponential
(`MaxNumberOfFailedLogin`, zweistufige Anmeldung).

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

- request-shield im Überwachungsmodus auf jeder neuen Website, mit Durchsetzung nach der ersten Woche, als Teil eines verwalteten Tarifs;
- die Administration hinter der Browserprüfung des Schutzes oder auf das Netzwerk des Kunden beschränkt (`EXP-ADMIN-NET`);
- die Regel für System-URLs auf öffentlichen Siteaccesses als Standard der Server-Vorlage;
- ein wöchentlicher Blick in `security.log` und das Log des Schutzes, mit einem Bericht an den Kunden, wenn eine Regel stark ansteigt.

## Checkliste

- [ ] request-shield außerhalb des Document Root installiert, Regeln in einem Verzeichnis, das nie ausgeliefert wird.
- [ ] In jede Anfrage eingehängt: `config.php` oder `auto_prepend_file` (PHP-FPM), `EXP_REQUEST_PREPEND_FILE` (Velocity).
- [ ] Zuerst Überwachungsmodus; `check` und `test` bestehen; dann `enforce`.
- [ ] `security.log` wöchentlich gelesen; der Klassifizierer bleibt an.
- [ ] Request-Regeln mit `--check` auf jedem Siteaccess geprüft, der sie nutzt.
- [ ] Proxys korrekt vertraut, bevor man sich auf eine Regel nach Adresse verlässt.

## Verwandte Seiten

- [Sicherheit für Hoster: Überblick](README.md)
- [Sicherheitshärtung](../../sicherheitshaertung.md)
- [Die Administration einschränken](die-administration-einschraenken.md)
- [Sicherheitslog](../betrieb/sicherheitslog.md)
- [HTTP-Cache: Mit request-shield davor](../../../../eng-us/bc/6.0/httpcache.md#with-request-shield-in-front)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.
