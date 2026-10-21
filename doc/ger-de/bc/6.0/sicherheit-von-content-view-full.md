# Anfrageregeln: `content/view/full` und jede andere Modul-View absichern

Exponential 6.0.15. Einstellungen in `settings/requestrules.ini`. Code in
`kernel/private/classes/requestrules/`. Befehl `bin/php/ezrequestrules.php`.
Tests in `tests/tests/kernel/classes/requestrules/`.

**Die Anfrageregeln** entscheiden, was mit einer Anfrage geschieht, unmittelbar bevor ihre Modul-View läuft. Eine
Regel sagt, *wann* (Bedingungen wie „als System-URL eingegeben“, „Vollansicht“, „dem Benutzer fehlt eine
Richtlinie“, „unter Knoten 864“, „aus diesem Netzwerk“) und *was* (Weiterleitung auf den URL-Alias, nicht gefunden,
Zugriff verweigert, zuerst anmelden, Weiterleitung, Umschreiben, Protokollieren). Regeln werden in INI geschrieben,
lassen sich pro Siteaccess einschalten, können von jeder Erweiterung in PHP erweitert werden und sind an
Rollenrichtlinien gebunden, sodass im Rolleneditor entschieden wird, wer ausgenommen ist, nicht in einer Datei.

Sie entstanden für eine Frage, die Einstellungen bisher nicht beantworten konnten:

> *Wie halte ich anonyme Besucher davon ab, Seiten über ihre System-URL `/content/view/full/123` zu öffnen, während
> dieselbe Seite unter ihrem URL-Alias `/Fit-Healthy/Some-Article` für alle weiter funktioniert?*

Die Antwort ist eine Regel, einsatzbereit ausgeliefert und mit zwei Zeilen eingeschaltet.

Lesen Sie diese Seite, wenn Sie Besucher von System-URLs fernhalten, eine Sektion schützen oder vor einer Modul-View
eine beliebige Prüfung ausführen möchten; und vor dem Upgrade, wenn Sie sich auf `[SiteAccessRules]` oder auf
`PolicyOmitList[]` verlassen.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Neue Engine für Anfrageregeln (`settings/requestrules.ini`, `kernel/private/classes/requestrules/`), die vor jeder Modul-View gefragt wird. Neue Richtlinienfunktion `content/view_system_url`. Neuer Befehl `bin/php/ezrequestrules.php` (auflisten, prüfen, erklären). Drei Regeln werden definiert und ausgeschaltet ausgeliefert. |
| Wer betroffen ist | Niemand, solange keine Regel in `RuleList[]` aufgeführt ist. Dann: Jede Modul-View wird gefragt, auch Views aus `PolicyOmitList[]`. `[SiteAccessRules]` funktioniert weiter und wird zuerst geprüft. Rollen mit `content/*` schließen die neue Richtlinienfunktion ein. |
| Wie Sie es prüfen | `php bin/php/ezrequestrules.php -s <siteaccess> --check` |
| Wie Sie es beheben | Nichts zu beheben. Um sie zu verwenden, folgen Sie dem [Schnellstart](#1-schnellstart); entfernen Sie jede Weiterleitung, die Sie zum selben Zweck in `full.tpl` eingebaut haben. |


## Inhalt

1. [Schnellstart](#1-schnellstart)
2. [Warum Einstellungen und Richtlinien allein es nicht konnten](#2-warum-einstellungen-und-richtlinien-allein-es-nicht-konnten)
3. [Wie über eine Anfrage entschieden wird](#3-wie-über-eine-anfrage-entschieden-wird)
4. [Regeln schreiben](#4-regeln-schreiben)
5. [Referenz: Bedingungen](#5-referenz-bedingungen)
6. [Referenz: Aktionen](#6-referenz-aktionen)
7. [Referenz: Fakten](#7-referenz-fakten)
8. [Die Richtlinienfunktion `content/view_system_url`](#8-die-richtlinienfunktion-contentview_system_url)
9. [Rezepte](#9-rezepte)
10. [Die Regeln in PHP erweitern](#10-die-regeln-in-php-erweitern)
11. [Die Engine aus PHP verwenden](#11-die-engine-aus-php-verwenden)
12. [Prüfen, erklären und testen](#12-prüfen-erklären-und-testen)
13. [Caches](#13-caches)
14. [Exponential Velocity und persistente Worker](#14-exponential-velocity-und-persistente-worker)
15. [Leistung](#15-leistung)
16. [Hinweise zur Sicherheit](#16-hinweise-zur-sicherheit)
17. [Was die Regeln nicht sehen](#17-was-die-regeln-nicht-sehen)
18. [Kompatibilität und Upgrade](#18-kompatibilität-und-upgrade)
19. [Fehlerbehebung](#19-fehlerbehebung)
20. [Dateien](#20-dateien)


## 1. Schnellstart

**Ziel:** Anonyme Besucher (und alle anderen ohne eine bestimmte Richtlinie), die `/content/view/full/<node>`
eingeben, werden mit einem `301` auf den URL-Alias des Knotens geschickt. Ein Knoten ohne eigenen Alias antwortet mit
`404`. Administratoren und alle, denen Sie die Richtlinie geben, verwenden weiterhin System-URLs.

**Schritt 1.** Schalten Sie die ausgelieferte Regel für jeden öffentlichen Siteaccess ein, in
`settings/siteaccess/<siteaccess>/requestrules.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_full_view_to_alias

*/ ?>
```

Lassen Sie die Siteaccesses für Administration und Editor unverändert: Redakteure verwenden dort absichtlich
System-URLs.

**Schritt 2.** Geben Sie die Richtlinie `content` / `view_system_url` den Rollen, die auf den öffentlichen
Siteaccesses System-URLs behalten sollen, etwa einer Rolle Editor (siehe
[Abschnitt 8](#8-die-richtlinienfunktion-contentview_system_url)). Administratoren haben sie bereits über
`content/*` oder `*/*`.

**Schritt 3.** Stellen Sie bereit und prüfen Sie:

```bash
./console exp:velocity deploy --no-autoload --allow-root-user   # clears the INI and page caches, restarts Velocity
php bin/php/ezrequestrules.php -s site --check --allow-root-user
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2 --allow-root-user
curl -sk -o /dev/null -w '%{http_code} %{redirect_url}\n' https://www.example.com/content/view/full/2
```

Ohne Velocity müssen die INI- und Seiten-Caches trotzdem geleert werden:
`php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, dann `--clear-id=content,exphttpcache`.

So sieht es auf einer Referenzinstallation aus (2026-10-02), unter Apache und Velocity gleichermaßen:

| Anfrage | Anonym | Administrator |
|---|---|---|
| `/content/view/full/2` | `301` auf `/websites`, `Cache-Control: private, no-store` | `200` |
| `/content/view/full/2/(offset)/10?x=1` | `301` auf `/websites/(offset)/10?x=1` | `200` |
| `/fit-healthy` (der Alias) | `200`, wie bisher gecacht | `200` |
| `/` (Startseite, `IndexPage=/content/view/full/89`) | `200` | `200` |
| `/content/view/line/2` (ein anderer View-Modus) | `200` | `200` |
| `/content/view/full/999999` (kein solcher Knoten) | `404` | `404` |

Mehr brauchen die meisten Websites nicht. Der Rest dieser Anleitung erklärt den Mechanismus vollständig: was sich wie
entscheiden lässt, wie man ihn erweitert und wie man erkennt, was er tut.


## 2. Warum Einstellungen und Richtlinien allein es nicht konnten

Bevor es die Anfrageregeln gab, gab es vier Werkzeuge, und keines konnte „diese View, auf diesem Weg erreicht, für
diese Benutzer“ ausdrücken:

| Werkzeug | Was es tut | Warum es nicht die Antwort ist |
|---|---|---|
| Rollenrichtlinie `content/read`, eingeschränkt nach Klasse, Sektion, Besitzer, Gruppe, Knoten, Teilbaum, Status | Entscheidet, **welche Inhalte** ein Benutzer lesen darf | Nicht, **welchen View-Modus** oder **welche URL**. Nehmen Sie `content/read` weg, verschwinden die Inhalte überall: unter ihrem Alias, in Listen, in Fetches auf anderen Seiten. |
| `site.ini [SiteAccessRules] Rules[]` (`access;disable`, `module;content/view`) | Schaltet ein Modul oder ein Modul/eine View für einen Siteaccess aus | Es gilt für **jeden Benutzer**. Es wird **nach** der Übersetzung des URL-Alias geprüft, daher nimmt das Ausschalten von `content/view` jeden Alias mit: Die ganze Website antwortet mit `404`. Und es kann `full` nicht von `line` unterscheiden. |
| `site.ini [SiteAccessSettings] RequireUserLogin` + `AnonymousAccessList[]` | Anmeldung für einen ganzen Siteaccess, mit Ausnahmen | Alles oder nichts pro Siteaccess. |
| Eine Weiterleitung in einem Template `full.tpl` | Funktioniert in einem Design | Die Vollansicht ist bereits gebaut und vielleicht gecacht, wenn das Template läuft. Sie gehört zu einem Design, nicht zum System. |

Die entscheidende Tatsache ist, dass ein URL-Alias `content/view/full/<node>` **ist**: Der Kernel übersetzt
`/Fit-Healthy` in `content/view/full/89`, bevor irgendetwas den Zugriff prüft. Der Kernel wusste, was der Besucher
eingegeben hatte (er hält die Adresse vor der Übersetzung fest), aber nichts nutzte das. Die Anfrageregeln nutzen es:
Der Fakt `requested_via` ist `system`, `alias`, `wildcard` oder `index`.


## 3. Wie über eine Anfrage entschieden wird

```
 request
   |
   |-- served from a page cache?  (role-aware HTTP cache early exit, Velocity response cache)
   |       yes -> the stored page. Rule-dependent pages are never stored: section 13
   |
   v  the kernel (ezpKernelWeb::dispatchLoop)
   1. address typed kept                 typed_uri, e.g. "Fit-Healthy" or "content/view/full/123"
   2. URL alias / wildcard translation   "Fit-Healthy" -> "content/view/full/89"; requested_via = alias
      a moved alias                      -> 301 to its new address (no rule is asked)
   3. [SiteAccessRules]                  module or view switched off -> not found
   4. the module and view exist?         no -> not found
   5. role policies                      not allowed -> access denied / sign in
   6. REQUEST RULES                      the first rule whose conditions all match decides
   7. the module view runs               (unless step 6 decided otherwise)
```

Was aus dieser Reihenfolge folgt:

- **Regeln nehmen immer nur Zugriff weg oder schicken den Besucher woandershin.** Sie werden gefragt, nachdem die
  Richtlinien die View erlaubt haben, daher kann keine Regel eine View öffnen, die eine Richtlinie geschlossen hat.
  Selbst `Action=allow` bedeutet nur „keine weiteren Regeln fragen“.
- **Regeln sehen die übersetzte Anfrage.** Eine Regel zu `content/view` gilt auch für Aliase. Verwenden Sie
  `requested_via`, um sie zu unterscheiden.
- **Die erste Regel, die entscheidet, entscheidet.** Die Reihenfolge zählt. Stellen Sie Ausnahmen (`allow`) vor die
  weiter gefassten Regeln, zu denen sie Ausnahmen sind.
- **Eine Regel, die passt, aber nicht handeln kann, gibt weiter.** `redirect_to_alias` kann bei einem Knoten ohne
  Alias nicht handeln: Die `FallbackAction` der Regel läuft, und ohne eine solche wird die nächste Regel gefragt.
- **Keine Regel hat gepasst? Die Anfrage läuft wie immer.** Eine leere `RuleList` kostet etwa 2,6 µs pro Anfrage
  ([Abschnitt 15](#15-leistung)).
- **Jede Antwort einer Regel außer `allow` wird mit `Cache-Control: private, no-store` gesendet.** Sie hängt davon
  ab, wer gefragt hat, daher darf kein Browser und kein gemeinsamer Cache sie behalten. Ein Browser, der ein `301`
  behielte, würde einen Besucher auch nach seiner Anmeldung weiter umleiten.


## 4. Regeln schreiben

### 4.1 Wohin die Einstellungen gehören

Bearbeiten Sie nie `settings/requestrules.ini`. Überschreiben Sie sie wie jede INI-Datei:

| Datei | Geltungsbereich |
|---|---|
| `settings/override/requestrules.ini.append.php` | jeder Siteaccess |
| `settings/siteaccess/<name>/requestrules.ini.append.php` | ein Siteaccess (überschreibt das Obige) |
| `extension/<ext>/settings/requestrules.ini.append.php` | aus einer aktiven Erweiterung (die Regeln, Handler und Provider der Erweiterung) |

`RuleList[]` allein auf einer Zeile **leert die Liste** vor den folgenden Einträgen, sodass ein Siteaccess die
geerbte Liste ersetzen kann. Lassen Sie diese Zeile weg, um zu ergänzen.

### 4.2 Aufbau einer Regel

```ini
[RequestRuleSettings]
RuleList[]=members_area                    # switched on: asked, in this order

[Rule-members_area]                        # the rule: [Rule-<name>]
Description=Anonymous visitors of the members area sign in first.
Conditions[subtree]=864                    # ALL conditions must match
Conditions[user]=anonymous
Action=login                               # what happens then
ActionArgs[...]=...                        # arguments of the action, if it has any
FallbackAction=notfound                    # optional: when the action cannot act
FallbackArgs[...]=...
```

Eine Regelgruppe, die existiert, aber nicht in `RuleList[]` steht (oder von einem Regel-Provider geliefert wird),
ist **ausgeschaltet**. Die ausgelieferten Regeln sind so geschrieben: definiert und ausgeschaltet, bis ein Siteaccess
sie aufführt.

### 4.3 Werte von Bedingungen

| Form | Bedeutung | Beispiel |
|---|---|---|
| `value` | passt auf diesen Wert | `Conditions[view_mode]=full` |
| `a,b,c` | passt auf **einen beliebigen** davon (Leerzeichen um Kommas werden ignoriert) | `Conditions[siteaccess]=site, bold` |
| `!a,b` | passt auf **keinen** davon: die ganze Bedingung umgekehrt | `Conditions[policy]=!content/view_system_url` |
| `pat*` | ein Shell-Muster, wo die Bedingung Muster annimmt (`*`, `?`, `[a-z]`) | `Conditions[uri]=content/view/*` |
| `name:argument` im Schlüssel | eine Bedingung, die einen Namen braucht | `Conditions[param:NodeID]=2`, `Conditions[header:X-Requested-With]=XMLHttpRequest` |

Der Abgleich unterscheidet nicht zwischen Groß- und Kleinschreibung. Eine Bedingung mit leerem Wert (oder nur `!`)
passt nie, und `--check` meldet sie.

### 4.4 Mehrere Regeln

```ini
RuleList[]
RuleList[]=robots_may_see_node_2      # 1. exception first
RuleList[]=system_url_full_view_to_alias   # 2. the general rule

[Rule-robots_may_see_node_2]
Conditions[node]=2
Conditions[requested_via]=system
Action=allow
```

Die Regeln werden in der Reihenfolge von `RuleList[]` gefragt, gefolgt von den Regeln aus `RuleProviders[]`, in deren
Reihenfolge.

### 4.5 Alles ausschalten

```ini
[RequestRuleSettings]
Enabled=false
```

Keine Regel wird gefragt, und der Kernel verhält sich genau so wie vor dieser Funktion.


## 5. Referenz: Bedingungen

**Stabil** bedeutet, dass die Antwort für jede Anfrage gleich ist, die eine gespeicherte Seite beantwortet: Die
Cache-Schlüssel enthalten, was die Bedingung liest. Eine Regel mit einer nicht stabilen Bedingung hält die Seiten, über
die sie entscheiden könnte, aus den gemeinsamen Caches heraus ([Abschnitt 13](#13-caches)). Bevorzugen Sie stabile
Bedingungen, wo Sie können.

| Bedingung | Liest (Fakt) | Werte | Muster | Stabil | Beispiel |
|---|---|---|---|---|---|
| `requested_via` | `requested_via` | `system` (als Modul/View eingegeben), `alias` (ein URL-Alias), `wildcard` (ein URL-Wildcard), `index` (die Startseite, `IndexPage`) | nein | ja | `Conditions[requested_via]=system` |
| `uri` | `typed_uri`: die eingegebene Adresse, ohne Siteaccess, Schrägstriche oder Query | Adressen | ja | ja | `Conditions[uri]=content/view/*` |
| `resolved_uri` | `resolved_uri`: die Systemadresse, die ausgeführt wird | Adressen | ja | ja | `Conditions[resolved_uri]=content/view/full/89` |
| `module` | `module` | Modulnamen | nein | ja | `Conditions[module]=content,shop` |
| `module_view` | `module`/`view` | Modul/View | ja | ja | `Conditions[module_view]=content/*` |
| `view_mode` | `view_mode`: der `ViewMode` von `content/view` | View-Modi | nein | ja | `Conditions[view_mode]=full` |
| `param:<Name>` | `params[<Name>]`: ein View-Parameter, über seinen Namen in der `module.php` des Moduls | Werte | ja | ja | `Conditions[param:NodeID]=2,43` |
| `node` | `node_id`: der Knoten von `content/view` | Knoten-IDs | nein | ja | `Conditions[node]=2` |
| `subtree` | `path_ids`: der Knoten und seine Vorfahren | Knoten-IDs | nein | ja | `Conditions[subtree]=864` |
| `class` | `class_identifier` des Knotens | Klassenbezeichner | nein | ja | `Conditions[class]=article,blog_post` |
| `section` | `section_id`, `section_identifier` des Objekts | IDs oder Bezeichner | nein | ja | `Conditions[section]=members` |
| `state` | `state_identifiers`: `group/state` des Objekts | Bezeichner | ja | ja | `Conditions[state]=ez_lock/locked` |
| `user` | `is_anonymous` | `anonymous`, `logged_in` | nein | ja | `Conditions[user]=anonymous` |
| `role` | `role_names`: dem Benutzer oder seinen Gruppen zugewiesene Rollen | Rollennamen | nein | ja | `Conditions[role]=Partner` |
| `policy` | `has_access`: `eZUser::hasAccessTo()`; eingeschränkter Zugriff zählt | Modul/Funktion | nein | ja | `Conditions[policy]=!content/view_system_url` |
| `siteaccess` | `siteaccess` | Namen von Siteaccesses | ja | ja | `Conditions[siteaccess]=site,bold*` |
| `method` | `method` | `GET`, `POST`, `HEAD`, ... | nein | ja | `Conditions[method]=!POST` |
| `scheme` | `scheme` | `http`, `https` | nein | ja | `Conditions[scheme]=http` |
| `host` | `host`: der Hostname, ohne Port | Hostnamen | ja | ja | `Conditions[host]=staging.*` |
| `user_id` | `user_id`: die Objekt-ID des Benutzers | IDs | nein | **nein** | `Conditions[user_id]=14` |
| `user_login` | `user_login` | Anmeldenamen | ja | **nein** | `Conditions[user_login]=svc-*` |
| `group` | `group_ids`: Objekt-IDs der Benutzergruppen des Benutzers | IDs | nein | **nein** | `Conditions[group]=12` |
| `header:<Name>` | ein Anfrage-Header; `*` = überhaupt gesendet | Werte | ja | **nein** | `Conditions[header:X-Requested-With]=XMLHttpRequest` |
| `ip` | `client_ip` | Adressen und CIDR-Netze, IPv4 und IPv6 | nein | **nein** | `Conditions[ip]=10.0.0.0/8, 2001:db8::/32` |
| `fact:<name>` | jeder Fakt, auch einer einer Erweiterung | Werte | ja | **nein** | `Conditions[fact:customer_tier]=gold` |

`user_id`, `user_login` und `group` sind nicht stabil, weil der rollenabhängige HTTP-Cache eine gespeicherte Seite
zwischen allen Benutzern mit denselben Rollen teilt.

Bedingungen zum Knoten (`node`, `subtree`, `class`, `section`, `state`) passen nie auf eine Anfrage, die keinen
Knoten betrifft. Ihre Fakten sind dann `null`, und eine verneinte Bedingung (`!article`) passt ebenfalls nicht.


## 6. Referenz: Aktionen

| Aktion | Antwort | Argumente | Kann nicht handeln (fällt zurück), wenn |
|---|---|---|---|
| `allow` | die View läuft; **keine spätere Regel wird gefragt** | keine | nie |
| `notfound` | die Seite „nicht gefunden“, HTTP `404` (Kernel-Fehler 3). Verrät nicht, ob die Seite existiert | keine | nie |
| `forbidden` | die Seite „Zugriff verweigert“, HTTP `403` (Kernel-Fehler 1), wie bei einer fehlenden Richtlinie | keine | nie |
| `login` | anonym: `302` auf `user/login` und nach der Anmeldung zurück zur Seite. Angemeldet: „Zugriff verweigert“ | keine | die Anfrage ist selbst eine Anmelde-View (`user/login`, `logout`, `register`, `activate`, `success`, `forgotpassword`, `password`), sodass keine Regel eine Schleife erzeugen kann |
| `redirect` | HTTP-Weiterleitung | `to` (erforderlich; [Platzhalter](#platzhalter)), `status` `301`, `302` (Standard), `303`, `307`, `308` | `to` ist leer |
| `redirect_to_alias` | HTTP-Weiterleitung auf den URL-Alias des Knotens, mit den Benutzerparametern der View (`/(offset)/10`); der Kernel behält den Query-String | `status` (Standard `301`) | betrifft keinen Knoten, oder der Knoten hat keinen eigenen Alias |
| `rewrite` | der Kernel führt stattdessen eine andere Adresse aus, ohne Weiterleitung; der Besucher behält die eingegebene Adresse. Die Regeln werden für die neue Adresse erneut gefragt, höchstens `MaxRewrites` (3) Mal, danach „nicht gefunden“ | `to` (erforderlich; Platzhalter) | `to` ist leer |
| `log` | schreibt eine Zeile nach `var/log/requestrules.log`; **gibt immer weiter** (an den Fallback oder die nächste Regel) | `message` (Platzhalter; Standard: die Adresse) | immer: dafür ist sie da |

Ein Weiterleitungsziel kann ein Pfad innerhalb des Siteaccess sein (`/about-us`), eine Systemadresse
(`/content/view/full/2`, als Alias dieses Knotens gesendet, wie jede Weiterleitung des Kernels) oder eine absolute
URL. Eine absolute URL auf einem anderen Host muss in `site.ini [SiteSettings] AllowedRedirectHosts[]` aufgeführt
sein; sonst lehnt der Kernel sie mit `403` ab.

### Platzhalter

`to` und `message` können Fakten verwenden: `{node_id}`, `{typed_uri}`, `{view_mode}`, `{siteaccess}`,
`{user_login}`, jeden skalaren Fakt `{name}`, einen View-Parameter `{param:NodeID}`, einen Header `{header:Host}`. Ein
unbekannter Platzhalter oder einer, dessen Fakt kein einzelner Wert ist, wird leer.

### `log` mit einem Fallback: erst protokollieren, dann handeln

```ini
Action=log
ActionArgs[message]=refused {typed_uri} for {user_login}
FallbackAction=notfound
```

Das schreibt die Zeile und antwortet dann mit „nicht gefunden“: Eine Regel protokolliert und lehnt zugleich ab.


## 7. Referenz: Fakten

Fakten sind das, was Bedingungen und Aktionen lesen. Der Kernel definiert sie für jede Anfrage
(`ezpRequestRuleKernel::context()`). Die meisten werden **erst aufgelöst, wenn eine Regel sie liest**: Eine Regel zur
Adresse lädt nie den Knoten, und der Knoten wird höchstens einmal geladen, egal wie viele Regeln fragen.

| Fakt | Typ | Beispiel | Kosten |
|---|---|---|---|
| `typed_uri` | string | `content/view/full/2`, `fit-healthy`, `` (Startseite) | frei |
| `requested_via` | string | `system`, `alias`, `wildcard`, `index` | frei |
| `resolved_uri` | string | `content/view/full/89` | frei |
| `module`, `view` | string | `content`, `view` | frei |
| `params` | array | `{"ViewMode":"full","NodeID":"89"}` | frei |
| `view_mode` | string\|null | `full` | frei |
| `node_id` | int\|null | `89` | frei |
| `user_parameters` | array | `{"offset":"10"}` | frei |
| `user_parameters_uri` | string | `/(offset)/10` | frei |
| `rewrites` | int | bisherige Umschreibungen in dieser Anfrage | frei |
| `siteaccess`, `method` | string | `site`, `GET` | frei |
| `scheme`, `host`, `client_ip`, `headers` | string / array | `https`, `www.example.com`, `10.1.2.3`, Namen in Kleinbuchstaben | bei Verwendung |
| `user` | eZUser | der aktuelle Benutzer | frei |
| `user_id` | int | `10` (anonym) | frei |
| `is_anonymous`, `user_login`, `group_ids`, `role_names` | | | bei Verwendung |
| `has_access` | callable( module, function ) : bool | | bei Verwendung |
| `node`, `object` | eZContentObjectTreeNode / eZContentObject | | bei Verwendung, ein Fetch |
| `url_alias`, `path_ids`, `class_identifier` | aus dem Knoten | `fit-healthy`, `[1,2,89]`, `frontpage` | bei Verwendung |
| `section_id`, `section_identifier`, `state_identifiers` | aus dem Objekt | `1`, `standard`, `["ez_lock/not_locked"]` | bei Verwendung |
| `rules_vary_by_request` | bool | von der Engine gesetzt ([Abschnitt 13](#13-caches)) | |

Eine Erweiterung fügt Fakten mit einem Fakt-Provider hinzu ([10.3](#103-ein-fakt-provider)). Ein Resolver, der eine
Ausnahme wirft, wird in die Debug-Ausgabe geschrieben und ergibt `null`, sodass ein defekter Fakt nie eine Seite
zerstört.


## 8. Die Richtlinienfunktion `content/view_system_url`

Eine neue Funktion des Moduls `content`. Sie hat keine Einschränkungen und wird von keiner View geprüft. Es gibt sie,
damit die Frage, **wer** von einer Regel für System-URLs **ausgenommen ist**, eine Entscheidung über Rollen ist:

```ini
Conditions[policy]=!content/view_system_url     # the rule applies to users WITHOUT it
```

- **Administratoren** haben sie über `content/*` (alle Funktionen des Moduls content) oder `*/*`.
- **Anonym** hat sie nicht, sofern Sie sie nicht hinzufügen; lassen Sie sie also weg.
- **Redakteure**, die auf dem öffentlichen Siteaccess mit System-URLs arbeiten, brauchen sie: Öffnen Sie in der
  Administration *Rollen und Richtlinien*, dann die Rolle (z. B. *Editor*), *Neue Richtlinie*, Modul *content*,
  Funktion *view_system_url*, *Vollen Zugriff gewähren*.

Dasselbe Muster funktioniert mit jeder Richtlinie, auch mit den Funktionen Ihres eigenen Moduls:
`Conditions[policy]=!mymodule/premium`.


## 9. Rezepte

Jedes Rezept ist vollständig: Setzen Sie die Gruppe in `requestrules.ini.append.php` und fügen Sie den Namen zu
`RuleList[]` hinzu. Knoten-IDs, Klassenbezeichner und Adressen sind Beispiele. Prüfen Sie Ihre mit
`bin/php/ezrequestrules.php`.

### 9.1 System-URLs auf den Alias (ausgeliefert: `system_url_full_view_to_alias`)

```ini
[Rule-system_url_full_view_to_alias]
Conditions[requested_via]=system
Conditions[module_view]=content/view
Conditions[view_mode]=full
Conditions[policy]=!content/view_system_url
Action=redirect_to_alias
ActionArgs[status]=301
FallbackAction=notfound
```

### 9.2 Streng: keine System-URL in irgendeinem View-Modus (ausgeliefert: `system_url_content_view_notfound`)

```ini
[Rule-system_url_content_view_notfound]
Conditions[requested_via]=system
Conditions[module_view]=content/view
Conditions[policy]=!content/view_system_url
Action=notfound
```

Achten Sie auf die Views, die Ihr Design über eine URL abruft (zum Beispiel ein AJAX-Aufruf von
`content/view/line/<node>`). Nehmen Sie sie zuerst aus
([9.11](#911-eine-ausnahme-vor-einer-weiter-gefassten-regel)), oder bleiben Sie bei 9.1.

### 9.3 Erst protokollieren, dann durchsetzen (ausgeliefert: `system_url_full_view_audit`)

Setzen Sie die Protokollregel **vor** die Regel, die sie vorab zeigt. Sie protokolliert und gibt weiter:

```ini
RuleList[]
RuleList[]=system_url_full_view_audit
```

```
$ tail var/log/requestrules.log
[ Oct 02 2026 06:01:12 ] [] [system_url_full_view_audit] GET system URL content/view/full/2 (node 2) by anonymous ip=10.1.2.3
```

Wenn das Log nur zeigt, was Sie erwarten, fügen Sie die durchsetzende Regel dahinter hinzu oder ersetzen Sie die
Protokollregel durch sie.

### 9.4 Ein Mitgliederbereich: zuerst anmelden

```ini
[Rule-members_area]
Description=Below node 864, anonymous visitors sign in first and come back.
Conditions[subtree]=864
Conditions[user]=anonymous
Action=login
```

Was Mitglieder lesen dürfen, entscheiden weiterhin die Richtlinien; diese Regel schickt anonyme Besucher nur auf die
Anmeldeseite statt auf ein bloßes „Zugriff verweigert“.

### 9.5 Eine Intranet-Sektion, nur aus den Büronetzen

```ini
[Rule-intranet_from_office_only]
Conditions[section]=intranet
Conditions[ip]=!10.0.0.0/8, 192.168.0.0/16, 2001:db8:42::/48
Action=notfound
```

Hinter einem Load Balancer setzen Sie `site.ini [HTTPHeaderSettings] ClientIpByCustomHTTPHeader=X-Forwarded-For`,
damit `client_ip` der Besucher ist und nicht der Load Balancer. Da `ip` sich pro Anfrage unterscheidet, werden
Intranet-Seiten nicht in den gemeinsamen Caches gespeichert.

### 9.6 Eine Klasse nie anonymen Besuchern zeigen

```ini
[Rule-internal_memos_hidden]
Conditions[class]=internal_memo
Conditions[user]=anonymous
Action=notfound
```

### 9.7 Einen ganzen Zweig verschieben: alte Adressen auf neue

```ini
[Rule-old_news_to_archive]
Conditions[subtree]=1200
Conditions[requested_via]=alias
Action=redirect
ActionArgs[to]=/archive/node/{node_id}
ActionArgs[status]=308
```

Für Adressen, die sich auf keinen Knoten mehr auflösen, verwenden Sie URL-Wildcards (`content/urlwildcards`). Regeln
sehen nur Adressen, die eine Modul-View erreichen ([Abschnitt 17](#17-was-die-regeln-nicht-sehen)).

### 9.8 Wartung: Alle außer den Administratoren sehen die Wartungsseite

```ini
RuleList[]
RuleList[]=maintenance_admins_through
RuleList[]=maintenance_page

[Rule-maintenance_admins_through]
Conditions[role]=Administrator
Conditions[module]=content,user,ezjscore
Action=allow

[Rule-maintenance_page]
Conditions[module]=content
Conditions[node]=!555
Action=rewrite
ActionArgs[to]=content/view/full/555
```

Knoten 555 ist die Wartungsseite. `user/login` funktioniert weiter, sodass Administratoren sich weiterhin anmelden
können.

### 9.9 Ein View-Modus nur für Skripte (AJAX)

```ini
[Rule-ajax_view_only_by_xhr]
Conditions[view_mode]=ajax
Conditions[header:X-Requested-With]=!XMLHttpRequest
Action=notfound
```

Ein Header ist nie eine Authentifizierung. Das hält Gelegenheitsbesucher und Crawler von einer Fragment-View fern; es
schützt keine Daten.

### 9.10 Eine View, die gepostet, nie geöffnet werden muss

```ini
[Rule-action_only_by_post]
Conditions[module_view]=content/action
Conditions[method]=!POST
Action=notfound
```

### 9.11 Eine Ausnahme vor einer weiter gefassten Regel

```ini
RuleList[]
RuleList[]=line_view_for_the_menu_script
RuleList[]=system_url_content_view_notfound

[Rule-line_view_for_the_menu_script]
Conditions[module_view]=content/view
Conditions[view_mode]=line
Conditions[header:X-Requested-With]=XMLHttpRequest
Action=allow
```

### 9.12 Ein Staging-Host hinter einer Anmeldung

```ini
[Rule-staging_requires_login]
Conditions[host]=staging.*
Conditions[user]=anonymous
Conditions[module]=!user
Action=login
```

### 9.13 Partnerseiten für Partner, ein Teaser für alle anderen

```ini
[Rule-partner_pages]
Conditions[subtree]=900
Conditions[role]=!Partner,Administrator
Action=redirect
ActionArgs[to]=/become-a-partner
ActionArgs[status]=302
```

### 9.14 Eine Seite stilllegen, aber ihre Adresse behalten

```ini
[Rule-retired_offer_shows_successor]
Conditions[node]=123
Action=rewrite
ActionArgs[to]=content/view/full/456
```

Der Besucher bleibt auf der alten Adresse und sieht Knoten 456.

### 9.15 Unterschiedliche Regeln pro Siteaccess

`settings/siteaccess/site/requestrules.ini.append.php`:

```ini
[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_full_view_to_alias
```

`settings/siteaccess/bold_ger/requestrules.ini.append.php`:

```ini
[RequestRuleSettings]
RuleList[]
RuleList[]=system_url_content_view_notfound
```

Dieselben Regeln können auch in einer Datei mit `Conditions[siteaccess]=...` stehen.

### 9.16 HTTPS nur für angemeldete Benutzer

```ini
[Rule-signed_in_over_https]
Conditions[scheme]=http
Conditions[user]=logged_in
Action=redirect
ActionArgs[to]=https://{host}/{typed_uri}
ActionArgs[status]=308
```

(Fügen Sie den Host zu `AllowedRedirectHosts[]` hinzu, wenn es nicht der aktuelle ist; hier ist er es.)


## 10. Die Regeln in PHP erweitern

Eine Erweiterung fügt **Bedingungen**, **Aktionen**, **Fakten** und **Regeln** hinzu. Jedes davon ist eine kleine
Klasse, die in der `extension/<ext>/settings/requestrules.ini.append.php` der Erweiterung genannt und vom Autoloader
gefunden wird. Führen Sie nach dem Hinzufügen der Klassen `php bin/php/ezpgenerateautoloads.php -e` aus.

Die Beispiele bauen eine Erweiterung, `myshop`, die Premium-Artikel zahlenden Kunden vorbehält.

### 10.1 Eine Bedingung

```php
<?php
// extension/myshop/classes/myshopcustomertiercondition.php
/**
 * customer_tier: the tier of the signed-in customer, from the fact
 * 'customer_tier' (myShopFactProvider): Conditions[customer_tier]=gold,silver
 */
class myShopCustomerTierCondition implements ezpRequestRuleCondition
{
    public function matches( ezpRequestContext $context, array $values, $argument )
    {
        $tier = $context->get( 'customer_tier' );     // resolved on first use
        foreach ( $values as $value )
        {
            if ( strcasecmp( $value, (string)$tier ) === 0 )
                return true;
        }
        return false;
    }
}
```

Regeln für eine Bedingung:

- **Lesen Sie nur Fakten.** Greifen Sie in `matches()` nie direkt auf die Datenbank zu. Legen Sie das in einen Fakt,
  damit es einmal und erst bei Bedarf geladen wird und in Tests vorgegeben werden kann.
- **Ändern Sie nie etwas.** Eine Bedingung kann mehrmals oder gar nicht gefragt werden.
- **`$values` ist nie leer, und das „!“ ist bereits behandelt.** Beantworten Sie „passt irgendein Wert?“.
- **Stabil?** Fügen Sie `implements ezpRequestRuleCondition, ezpRequestRuleStableCondition` nur hinzu, wenn die
  Antwort von nichts als den Cache-Schlüsseln abhängt (Adresse, Siteaccess, Host, Schema, der Knoten, die Rollen und
  Richtlinien des Benutzers). Eine Kundenstufe gilt pro Benutzer und ist daher nicht stabil: Lassen Sie es weg, und
  Seiten, über die sie entscheiden könnte, bleiben aus den gemeinsamen Caches heraus.

Eine Bedingung, die einen Fakt vergleicht, kann einfach so aussehen:

```php
class myShopRegionCondition extends ezpRequestFactCondition { protected $fact = 'region'; protected $patterns = true; }
```

### 10.2 Eine Aktion

```php
<?php
// extension/myshop/classes/myshoppaywallaction.php
/**
 * paywall: shows the paywall page (design:myshop/paywall.tpl) in place of the
 * article, with HTTP 402. ActionArgs[template]=design:myshop/paywall.tpl
 */
class myShopPaywallAction implements ezpRequestRuleAction
{
    public function decide( ezpRequestContext $context, array $arguments, ezpRequestRule $rule )
    {
        $node = $context->get( 'node' );
        if ( !$node instanceof eZContentObjectTreeNode )
            return null;                                   // cannot act: fallback / next rule

        $tpl = eZTemplate::factory();
        $tpl->setVariable( 'node', $node );
        header( 'HTTP/1.1 402 Payment Required' );
        return ezpRequestRuleResult::moduleResult( array(
            'content' => $tpl->fetch( isset( $arguments['template'] ) ? $arguments['template'] : 'design:myshop/paywall.tpl' ),
            'path' => array( array( 'text' => $node->attribute( 'name' ), 'url' => false ) ),
            'node_id' => $node->attribute( 'node_id' ),
        ) );
    }
}
```

Eine Aktion **entscheidet**. Sie gibt eine der Antworten von `ezpRequestRuleResult` zurück, und der Kernel sendet
sie:

| Konstruktor | Antwort |
|---|---|
| `ezpRequestRuleResult::allow()` | die View ausführen; keine weitere Regel fragen |
| `::notFound()`, `::forbidden()`, `::login()` | wie die eingebauten Aktionen |
| `::redirect( $location, $status )` | Weiterleitung; `$status` 301/302/303/307/308 |
| `::rewrite( $uri )` | stattdessen `$uri` ausführen |
| `::moduleResult( array )` | dieses Modulergebnis **ist** die Seite, innerhalb des Pagelayouts |
| `null` | kann hier nicht handeln: Der Fallback läuft, oder die nächste Regel |

### 10.3 Ein Fakt-Provider

```php
<?php
// extension/myshop/classes/myshopfactprovider.php
class myShopFactProvider implements ezpRequestFactProvider
{
    public function defineFacts( ezpRequestContext $context )
    {
        // A resolver: runs only if a rule reads 'customer_tier', at most once
        $context->define( 'customer_tier', function ( ezpRequestContext $c )
        {
            $user = $c->get( 'user' );
            return $user && !$c->get( 'is_anonymous' ) ? myShopCustomer::tierOf( $user->id() ) : 'none';
        } );
        $context->define( 'is_premium', function ( ezpRequestContext $c )
        {
            $object = $c->get( 'object' );
            return $object ? myShopPremium::isPremium( $object->attribute( 'id' ) ) : false;
        } );
    }
}
```

Fakten können auch von eingebauten Bedingungen gelesen werden: `Conditions[fact:is_premium]=1`.

### 10.4 Ein Regel-Provider (Regeln aus einer Datenbank, einer anderen Datei, einer API)

```php
<?php
// extension/myshop/classes/myshoprulesprovider.php
class myShopRulesProvider implements ezpRequestRuleProvider
{
    public function rules()
    {
        $rules = array();
        foreach ( myShopEmbargo::fetchActive() as $embargo )        // e.g. rows of a table
        {
            $rules[] = new ezpRequestRule(
                'embargo_' . $embargo->attribute( 'id' ),
                array( 'node' => (string)$embargo->attribute( 'node_id' ), 'policy' => '!myshop/embargo_bypass' ),
                'notfound',
                array(), null, array(),
                'Embargoed until ' . date( 'c', $embargo->attribute( 'until' ) )
            );
        }
        return $rules;
    }
}
```

Provider laufen einmal pro Siteaccess und Prozess (die Engine wird behalten; siehe
[Abschnitt 14](#14-exponential-velocity-und-persistente-worker)). Regeln, die sich im Laufe des Tages ändern, gehören
in einen **Fakt** (`Conditions[fact:embargoed]=1`, pro Anfrage gelesen), nicht in einen Provider.

### 10.5 Alles registrieren

```ini
<?php /* #?ini charset="utf-8"?
# extension/myshop/settings/requestrules.ini.append.php

[RequestRuleSettings]
ConditionHandlers[customer_tier]=myShopCustomerTierCondition
ActionHandlers[paywall]=myShopPaywallAction
FactProviders[]=myShopFactProvider
RuleProviders[]=myShopRulesProvider
RuleList[]=premium_paywall

[Rule-premium_paywall]
Description=Premium articles for gold and silver customers; everyone else sees the paywall.
Conditions[class]=article
Conditions[fact:is_premium]=1
Conditions[customer_tier]=!gold,silver
Action=paywall
ActionArgs[template]=design:myshop/paywall.tpl
*/ ?>
```

```bash
php bin/php/ezpgenerateautoloads.php -e
php bin/php/ezrequestrules.php -s site --check --allow-root-user
```

### 10.6 Die Regeln Ihrer Erweiterung ohne Datenbank testen

Die Engine nimmt ihre Einstellungen als Array, und ein Kontext besteht nur aus Fakten:

```php
$engine = new ezpRequestRuleEngine( array(
    'rules' => array( 'premium_paywall' => array(
        'Conditions' => array( 'class' => 'article', 'fact:is_premium' => '1', 'customer_tier' => '!gold,silver' ),
        'Action' => 'paywall' ) ),
    'condition_handlers' => array( 'customer_tier' => 'myShopCustomerTierCondition' ),
    'action_handlers' => array( 'paywall' => new myShopPaywallActionStub() ),
) );
$result = $engine->evaluate( new ezpRequestContext( array(
    'class_identifier' => 'article', 'is_premium' => true, 'customer_tier' => 'free',
) ) );
$this->assertSame( ezpRequestRuleResult::MODULE_RESULT, $result->type );
$this->assertSame( array(), $engine->validate() );
```

`tests/tests/kernel/classes/requestrules/ezpRequestRuleEngineTest.php` zeigt jede eingebaute Bedingung und Aktion auf
diese Weise getestet.


## 11. Die Engine aus PHP verwenden

```php
$engine = ezpRequestRuleEngine::instance();     // the current siteaccess's rules

// Add a rule at run time (idempotent by name: adding it again replaces it)
$engine->addRule( new ezpRequestRule( 'tmp_block', array( 'node' => '123' ), 'notfound' ) );
$engine->addRule( new ezpRequestRule( 'tmp_exception', array( 'node' => '123', 'role' => 'Editor' ), 'allow' ), 'tmp_block' );  // before tmp_block
$engine->removeRule( 'tmp_block' );

// Decide about any request, e.g. in a custom module or a script
$context = ezpRequestRuleKernel::context( 'content', 'view', array( 'full', '123' ),
                                          array( 'typed_uri' => 'content/view/full/123', 'via' => 'system' ),
                                          eZUser::currentUser() );
$result = $engine->evaluate( $context );       // ezpRequestRuleResult, or null: no rule decides
if ( $result ) echo $result->rule, ': ', $result->describe();

// Why
foreach ( $engine->explain( $context ) as $row )
    printf( "%s %s %s\n", $row['decides'] ? 'DECIDES' : ( $row['matched'] ? 'matched' : 'no' ), $row['rule'], json_encode( $row['conditions'] ) );

// Problems in the settings, as --check prints them
print_r( $engine->validate() );

ezpRequestRuleEngine::resetInstance();          // re-read requestrules.ini on the next instance()
```

Mit `addRule()` hinzugefügte Regeln leben so lange wie die Engine: eine Anfrage unter PHP-FPM oder die Lebensdauer
eines Velocity-Workers. Verwenden Sie sie für Tests und Skripte. Regeln, die immer gelten müssen, gehören in die
Einstellungen oder in einen Regel-Provider.


## 12. Prüfen, erklären und testen

### 12.1 `bin/php/ezrequestrules.php`

```bash
php bin/php/ezrequestrules.php -s site --list   --allow-root-user   # the rules, in order
php bin/php/ezrequestrules.php -s site --check  --allow-root-user   # problems; exit code 1 when any (for CI)
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2 --allow-root-user
php bin/php/ezrequestrules.php -s site --uri=content/view/full/2 --user=admin --allow-root-user
php bin/php/ezrequestrules.php -s site --uri=fit-healthy --ip=10.1.2.3 --header=X-Requested-With:XMLHttpRequest --method=POST --allow-root-user
```

`--uri` übersetzt die Adresse so, wie es der Kernel tut (Alias, Wildcard, Startseite), sodass die Erklärung die ist,
die eine echte Anfrage erhält. Sie sagt außerdem, wenn die Richtlinien die View ablehnen würden, bevor eine Regel
gefragt wird.

Echte Ausgabe (Referenzinstallation, 2026-10-02):

```
Address  /content/view/full/2  ->  content/view/full/2  (requested via system)
User     anonymous (anonymous)  siteaccess site

DECIDES system_url_full_view_to_alias
        yes  requested_via = system
        yes  module_view = content/view
        yes  view_mode = full
        yes  policy = !content/view_system_url
        -> redirect 301 to /websites

Result   decided by the rule marked DECIDES
Facts the rules read:
  node_id              2
  requested_via        'system'
  typed_uri            'content/view/full/2'
  url_alias            'websites'
  ...
```

`--check` findet:

- eine Regel in `RuleList[]` ohne ihre Gruppe;
- eine Regel ohne Bedingungen oder Aktion;
- unbekannte Namen von Bedingungen und Aktionen;
- leere Werte;
- `param`, `header` und `fact` ohne ihr `:name`;
- Richtlinienwerte, die nicht Modul/Funktion sind;
- `redirect` und `rewrite` ohne `to`;
- Statuswerte, die keine Weiterleitungen sind;
- Klassen von Handlern und Providern, die nicht existieren oder die falsche Schnittstelle implementieren.

**Führen Sie es in Ihrer Deployment-Pipeline aus.** Eine Regel mit einem Tippfehler passt nie; sie bringt die Website
nicht zum Scheitern, daher ist `--check` der Weg, einen Tippfehler zu finden.

### 12.2 Zur Laufzeit

- **Debug-Ausgabe** (`DebugOutput=enabled`): Jede Entscheidung ist ein Hinweis,
  `Request rule 'name': redirect 301 to /x for content/view/full/2`. Unbekannte Bedingungen und Aktionen sind Fehler.
- **`$GLOBALS['ezpRequestRuleDecision']`:** das `ezpRequestRuleResult`, das über diese Anfrage entschieden hat, oder
  `null`. Verwenden Sie es in einem Operator des Pagelayouts oder in einem Response-Listener.
- **`Action=log`:** `var/log/requestrules.log`.

### 12.3 Tests

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/requestrules/    # 21 tests: every condition, action, the shipped file
```

Dieselben 21 Tests laufen mit den übrigen Kernel-Tests (`--testsuite kernel-classes`).

Durch Benutzung, über HTTP, gegen eine laufende Website: anonym und als Administrator, jede Aktion, Apache und
Velocity. Überprüft am 2026-10-02 auf einer Referenzinstallation, alles PASS auf beiden Servern:

| Prüfung | Apache | Velocity |
|---|---|---|
| anonyme System-URL → 301 auf den Alias, `private, no-store` | PASS | PASS |
| Benutzerparameter und Query bleiben erhalten | PASS | PASS |
| Alias, Startseite, Listenansicht unberührt | PASS | PASS |
| Administrator behält die System-URL (200) | PASS | PASS |
| `allow` vor der ausgelieferten Regel | PASS | PASS |
| `forbidden` 403, `notfound` 404 | PASS | PASS |
| `redirect` 307 auf ein Systemziel, als dessen Alias gesendet | PASS | PASS |
| `rewrite` behält die Adresse (200) | PASS | PASS |
| eine Umschreibschleife endet nach `MaxRewrites` (404) | PASS | PASS |
| `log` schreibt seine Zeile und gibt weiter | PASS | PASS |
| `login` → 302 auf user/login und nach der Anmeldung zurück zur Seite | PASS | PASS |
| nicht betroffene Seiten werden weiter aus den Seiten-Caches ausgeliefert (`X-Exp-Cache: HIT`) | PASS | PASS |


## 13. Caches

Zwei Caches beantworten Anfragen, **bevor der Kernel läuft**, also bevor eine Regel gefragt wird: der rollenabhängige
HTTP-Cache (sein früher Ausstieg in `config.php`) und der Antwort-Cache von Velocity. Die Regeln sind mit ihnen sicher,
dank dreier Zusagen:

1. **Keine Antwort einer Regel außer `allow` wird gespeichert.** Sie wird mit `Cache-Control: private, no-store`
   gesendet, was Velocity, Proxys und Browser beachten, und der HTTP-Cache lehnt sie ab
   (`X-Exp-Cache: BYPASS (request rule)`). Eine Weiterleitung für anonyme Besucher kann daher nie an einen Redakteur
   ausgeliefert werden, und ein Browser behält kein `301`, das nach der Anmeldung noch gelten würde.
2. **Eine Seite, deren Regeln nur stabile Bedingungen lesen, wird wie bisher gecacht.** Zwei Anfragen, die eine
   gespeicherte Seite beantwortet, haben dieselbe Adresse, denselben Siteaccess, Host, dasselbe Schema und denselben
   Berechtigungskontext, sodass die Regeln für beide gleich entscheiden. Die ausgelieferten Regeln sind alle stabil:
   Sie verringern das Caching überhaupt nicht (auf der Referenzinstallation bleibt `/fit-healthy` ein `HIT`).
3. **Eine Seite, über die eine Regel mit einer pro Anfrage veränderlichen Bedingung entscheiden könnte, wird nie
   gespeichert.** Passt jede stabile Bedingung einer solchen Regel, könnte eine andere Anfrage für dieselbe Seite mit
   einem anderen Header, einer anderen Adresse oder einem anderen Benutzer anders entschieden werden. Daher wird die
   Seite mit `private, no-store` gesendet, auch wenn die View läuft, und der Kontextfakt `rules_vary_by_request` ist
   `true`. Stabile Bedingungen werden zuerst gefragt, sodass eine Regel wie
   `module_view=content/view` + `header:X-Test=deny` nur Seiten von `content/view` betrifft.

**Leeren Sie nach dem Ändern von Regeln die Seiten-Caches.** Eine Seite, die gespeichert wurde, bevor eine Regel
existierte, wird ausgeliefert, ohne sie zu fragen. `exp:velocity deploy` tut das; andernfalls führen Sie
`php bin/php/ezcache.php --clear-id=content,exphttpcache --allow-root-user` und `./console exp:velocity cache clear`
aus.

Der **View-Cache** der Inhalte (`ViewCaching`) speichert die gerenderte Ansicht eines Knotens, nicht die Antwort. Er
läuft nach den Regeln und ist von ihnen nicht betroffen.


## 14. Exponential Velocity und persistente Worker

- **Die Engine wird pro Siteaccess** für die Lebensdauer des Workers **behalten**: `requestrules.ini` wird einmal pro
  Siteaccess gelesen, Regel-Provider laufen einmal, und Handler werden einmal erzeugt. Der Kontext ist für jede
  Anfrage neu.
- **Der Zustand pro Anfrage wird vom Kernel** zu Beginn jeder Anfrage **zurückgesetzt**
  (`$GLOBALS['ezpRequestRuleNoStore']`, `$GLOBALS['ezpRequestRuleDecision']`, der Umschreibzähler). Die Entscheidung
  einer Anfrage kann nie in die nächste gelangen.
- **`addRule()` ist pro Name idempotent**, sodass Code, der bei jeder Anfrage eine Regel hinzufügt, in einem lange
  laufenden Worker keine Regeln anhäuft.
- **Eine Änderung der Einstellungen erreicht Velocity erst nach einem Neustart.** Worker behalten die INI, die sie
  geladen haben. Stellen Sie mit `./console exp:velocity deploy --no-autoload --allow-root-user` bereit, das den
  INI-Cache leert, Velocity neu startet und die Seiten-Caches leert, in dieser Reihenfolge. PHP-FPM liest die
  Einstellungen erneut, sobald der INI-Cache geleert ist.
- **Neue Klassen für Bedingungen, Aktionen oder Provider** brauchen neu erzeugte Autoloads der Erweiterungen
  (`exp:velocity deploy` erledigt das), und Kernel-Klassen brauchen `--kernel`.


## 15. Leistung

Gemessen auf einer Referenzinstallation (PHP 8.5, je 20 000 Auswertungen, ohne den Kernel; die Kosten schließen den
Aufbau des Kontexts ein):

| Regeln | µs pro Anfrage |
|---|---|
| keine (der Standard) | 2,6 |
| die ausgelieferte Regel, ein Alias, auf den sie nicht zutrifft | 9,7 |
| die ausgelieferte Regel entscheidet (System-URL, liest den Alias des Knotens) | 23,3 |
| 20 Regeln, keine trifft zu | 110,5 |

Eine gerenderte Seite kostet auf demselben Server etwa 280 ms, daher machen selbst 20 Regeln 0,04 % davon aus. Eine
aus einem Cache ausgelieferte Seite erreicht die Regeln überhaupt nicht. Die Zahlen stammen aus der Auswertung jedes
Falls 20 000 Mal in einer Schleife mit einem vorbereiteten Kontext, so wie die Unit-Tests einen aufbauen.

Halten Sie Regeln günstig, indem Sie die Bedingungen innerhalb einer Regel von günstig nach teuer ordnen. Jede
Bedingung muss passen, und die Auswertung endet bei der ersten, die nicht passt. Bedingungen zu Adresse, Modul, View
und Benutzer kosten nichts; Bedingungen zum Knoten laden den Knoten einmal.


## 16. Hinweise zur Sicherheit

- **Regeln fügen Einschränkungen hinzu; Richtlinien gewähren Zugriff.** Verlassen Sie sich nie auf eine Regel, um
  Daten zu schützen, die eine Richtlinie erlaubt. Die Regel kann ausgeschaltet, im falschen Siteaccess aufgeführt oder
  von einer View umgangen werden, an die niemand gedacht hat (Abschnitt 17). Schützen Sie Daten mit Einschränkungen
  von `content/read`; verwenden Sie Regeln dafür, *wie* erlaubte Inhalte erreicht werden dürfen.
- **Sicher für die Website, laut für den Betreiber.** Eine unbekannte Bedingung passt nie, und eine unbekannte Aktion
  entscheidet nie, daher kann ein Tippfehler die Website nicht lahmlegen. Führen Sie `--check` aus, um Tippfehler zu
  finden.
- **Header und Client-Adressen kommen vom Client.** `header:*` ist eine Bequemlichkeit, nie eine Authentifizierung.
  `ip` ist nur so vertrauenswürdig wie `ClientIpByCustomHTTPHeader`: Setzen Sie es nur hinter einem Proxy, der diesen
  Header überschreibt.
- **Weiterleitungen bleiben auf erlaubten Hosts.** Ein absolutes Ziel auf einem anderen Host muss in
  `AllowedRedirectHosts[]` stehen; alles andere lehnt der Kernel ab. Platzhalter können mitbringen, was der Besucher
  gesendet hat (`{typed_uri}`, `{host}`, `{header:...}`); die Prüfung der erlaubten Hosts gilt trotzdem für das
  Ergebnis, sodass ein daraus gebautes Ziel Ihre Hosts nicht verlassen kann.
- **`notfound` verrät nichts.** Es ist dieselbe Seite wie für einen Knoten, der nicht existiert. Verwenden Sie es statt
  `forbidden`, wenn schon die Existenz einer Seite vertraulich ist.
- **Keine Antwort, die davon abhängt, wer gefragt hat, wird gecacht** (Abschnitt 13).


## 17. Was die Regeln nicht sehen

- **Adressen, die nie eine Modul-View erreichen:**
  - ein Modul, das nicht existiert („nicht gefunden“ vor jeder Regel);
  - ein verschobener URL-Alias (der Kernel leitet zuerst auf die neue Adresse weiter);
  - durch `[SiteAccessRules]` ausgeschaltete Views;
  - Views, die die Richtlinien ablehnen.

  Um Adressen abzufangen, die nicht existieren, verwenden Sie URL-Wildcards.
- **Aus einem Seiten-Cache ausgelieferte Seiten.** So gewollt, wie Abschnitt 13 erklärt.
- **Der Query-String.** Kein Fakt enthält ihn (verwenden Sie einen Fakt-Provider, wenn Sie ihn brauchen).
  Benutzerparameter (`/(offset)/10`) sind Fakten.
- **Andere Einstiegspunkte:** der Kernel der REST-API, `index_treemenu.php`, statische Dateien und Bild-Aliase, die
  der Webserver ausliefert, und CLI-Skripte.
- **Templates.** Ein in eine andere Seite eingebetteter Knoten (`node_view_gui`, `fetch`) ist keine Anfrage;
  `content/read` regelt ihn.


## 18. Kompatibilität und Upgrade

- **Nichts ändert sich, bis eine Regel aufgeführt ist.** `RuleList[]` ist in `settings/requestrules.ini` leer; die
  drei ausgelieferten Regeln sind definiert und ausgeschaltet.
- **`[SiteAccessRules]` funktioniert genau wie bisher** und wird vor den Anfrageregeln geprüft (Abschnitt 3,
  Schritt 3).
- **Views aus `PolicyOmitList[]` werden ebenfalls gefragt.** Die Regeln laufen für jede Modul-View, die läuft, ob ihre
  Richtlinie geprüft oder ausgelassen wird.
- **Die Richtlinienfunktion `content/view_system_url`** erscheint im Rolleneditor; bestehende Rollen sind nicht
  betroffen. Rollen mit `content/*` schließen sie ein.
- **Klassennamen beginnen mit `ezp`**, wie bei den anderen privaten Klassen des Kernels; sie sind ab 6.0.15 stabile
  API: `ezpRequestRuleEngine`, `ezpRequestRule`, `ezpRequestRuleResult`, `ezpRequestContext`,
  `ezpRequestRuleCondition`, `ezpRequestRuleStableCondition`, `ezpRequestRuleAction`, `ezpRequestRuleProvider`,
  `ezpRequestFactProvider`, `ezpRequestFactCondition`, `ezpRequestRuleKernel`. Eingebaute Klassen für Bedingungen und
  Aktionen können Geschwister erhalten; ihre Namen und ihr Verhalten bleiben.
- **Der Umweg über eine Weiterleitung im Template kann entfallen.** Entfernen Sie eine Weiterleitung, die Sie zu diesem
  Zweck in `full.tpl` gesetzt haben, sobald die Regel eingeschaltet ist.


## 19. Fehlerbehebung

| Symptom | Ursache | Lösung |
|---|---|---|
| Die Regel bewirkt nichts | nicht in `RuleList[]` für diesen Siteaccess; INI-Cache; Velocity nicht neu gestartet; Seite aus einem Cache ausgeliefert, der vor der Regel gespeichert wurde | `--list -s <sa>`; `exp:velocity deploy --no-autoload`; `content,exphttpcache` leeren |
| `--uri` meldet DECIDES, der Browser 200 | der Browser oder ein Cache hat eine ältere Antwort behalten | versuchen Sie `curl -sk -o /dev/null -w '%{http_code}'`; leeren Sie die Seiten-Caches |
| Die ganze Website antwortet mit 404 | eine Regel ohne eingrenzende Bedingung (`module_view` / `requested_via`), z. B. nur `Conditions[policy]=!...` | `--uri=` auf einen Alias zeigt, welche Regel entscheidet; grenzen Sie sie ein |
| Redakteure werden auf der öffentlichen Website von System-URLs weggeleitet | ihrer Rolle fehlt `content/view_system_url` | gewähren Sie sie (Abschnitt 8) |
| `ERR_TOO_MANY_REDIRECTS` | ein `redirect`, dessen Ziel auf dieselbe Regel passt (zum Beispiel `to` dieselbe Adresse) | fügen Sie eine Bedingung hinzu, die das Ziel nicht erfüllt, oder verwenden Sie `redirect_to_alias`, das nie auf sich selbst weiterleitet |
| Umschreibungen enden in 404 | die umgeschriebene Adresse hat erneut gepasst; `MaxRewrites` erreicht | schließen Sie das Ziel aus (`Conditions[node]=!555`) |
| Seiten einer Sektion werden nicht mehr gecacht | eine Regel dort verwendet `header`, `ip`, `user_id`, `user_login`, `group`, `fact` oder eine eigene Bedingung ohne Stabil-Markierung | erwartet (Abschnitt 13); stellen Sie wenn möglich auf eine stabile Bedingung um |
| `--check`: "is not a class implementing" | die Klasse fehlt in den Autoloads oder implementiert die falsche Schnittstelle | `php bin/php/ezpgenerateautoloads.php -e` |
| Eine Entscheidung steht nicht in `var/log/requestrules.log` | nur `Action=log` schreibt dorthin | schalten Sie die Debug-Ausgabe ein oder setzen Sie eine Protokollregel davor |


## 20. Dateien

| Datei | Was |
|---|---|
| `settings/requestrules.ini` | Einstellungen, die drei ausgelieferten Regeln (ausgeschaltet) |
| `kernel/private/classes/requestrules/ezprequestruleengine.php` | die Engine: Regeln, Handler, `evaluate()`, `explain()`, `validate()` |
| `kernel/private/classes/requestrules/ezprequestrule.php` | eine Regel; das Parsen von Schlüsseln und Werten |
| `kernel/private/classes/requestrules/ezprequestcontext.php` | die Fakten einer Anfrage, bei Bedarf aufgelöst |
| `kernel/private/classes/requestrules/ezprequestruleresult.php` | die Entscheidungen |
| `kernel/private/classes/requestrules/ezprequestruleinterfaces.php` | `ezpRequestRuleCondition`, `ezpRequestRuleStableCondition`, `ezpRequestRuleAction`, `ezpRequestRuleProvider`, `ezpRequestFactProvider` |
| `kernel/private/classes/requestrules/ezprequestruleconditions.php` | die eingebauten Bedingungen |
| `kernel/private/classes/requestrules/ezprequestruleactions.php` | die eingebauten Aktionen |
| `kernel/private/classes/requestrules/ezprequestrulekernel.php` | die Seite des Kernels: Fakten einer echten Anfrage, die Antwort auf eine Entscheidung |
| `kernel/private/classes/ezpkernelweb.php` | hält fest, wie die Anfrage erreicht wurde; fragt die Regeln, bevor die View läuft |
| `kernel/private/classes/httpcache/ezphttpcachelistener.php` | speichert keine Seite, die die Regeln aus gemeinsamen Caches heraushalten |
| `kernel/content/module.php` | die Richtlinienfunktion `content/view_system_url` |
| `bin/php/ezrequestrules.php` | auflisten, prüfen, erklären |
| `tests/tests/kernel/classes/requestrules/ezpRequestRuleEngineTest.php` | 21 Unit-Tests |

## Verwandte Seiten

- [Anfrageregeln (Funktion)](../../funktionen/6.0/anfrageregeln.md)
- [Verhaltensänderungen vom 1. und 2. Oktober 2026](verhaltensaenderungen-2026-10.md)
- [HTTP-Cache](httpcache.md)
- [Sicherheitshärtung](haertung.md)
- [Anleitung zu Sicherheit und Audit](../../anleitungen/sicherheit-und-audit.md)
