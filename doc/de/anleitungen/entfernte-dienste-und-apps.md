# Remote-Dienste und Apps: die Administration aus Skripten, Apps und Portalen steuern

Diese Anleitung richtet sich an Entwickler, die Exponential aus Skripten, Apps oder einem anderen Frontend steuern möchten. In etwa 20 Minuten rufen Sie Exponential als HTTP-API aus der Shell und aus Python auf, erstellen ein persönliches API-Token für ein Skript, durchsuchen den Dienstkatalog und öffnen ein fertiges Portal-Frontend, das auf denselben Diensten läuft. Kein Build-Schritt, kein zusätzlicher Server.

`expservices` macht aus den Funktionen der Administration über tausend benannte Dienste (Inhalte, Benutzer, Rollen, Caches, Cronjobs, Shop, Feeds, Medien, Tags, Layouts, das Audit und mehr), bereitgestellt über die Aufrufschnittstelle von `ezjscore` unter `/ezjscore/call/`. Ein Dienst wird über seinen Namen aufgerufen, `exp<domain>::<method>`, und antwortet mit JSON.

Sie brauchen: eine Installation von Exponential 6.0.15 mit aktiver Extension `expservices` (sie gehört zur Standarddistribution; prüfen Sie es mit Schritt 1), `curl` und für Schritt 5 ein Konto, das Tokens erstellen darf. Die Beispiele verwenden `https://example.com`.

## 1. Prüfen, ob es antwortet (1 Minute)

```bash
curl -s 'https://example.com/ezjscore/call/expsession::whoami?ContentType=json'
```

Erwartet: eine Hülle, die besagt, dass Sie anonym sind:

```
{"error_text":"","content":{"ok":true,"data":{"id":10,"login":null,"name":"Anonymous User",...,"anonymous":true,"groups":[]},"meta":[]}}
```

Die äußeren Felder `error_text` und `content` stammen von der Aufrufschnittstelle von `ezjscore`; die Clients für die Shell und für Python packen sie aus. Innerhalb von `content` hat jede Antwort dieselbe Form: `{"ok":true,"data":...,"meta":{...}}` oder `{"ok":false,"error":{"code":403,"message":"..."}}`. Listen sind seitenweise aufgeteilt, und `meta` enthält `total`, `offset`, `limit`, `count` und `has_more`.

Wenn Sie bei jedem Aufruf 403 erhalten, ist der Hauptschalter aus: `./console exp:ini get expservices.ini/Services/Enabled --allow-root-user` muss `enabled` ausgeben. Fehlt die Extension, ergänzen Sie `ActiveExtensions[]=expservices` in `settings/override/site.ini.append.php` und führen dann `php bin/php/ezpgenerateautoloads.php -e` und `php bin/php/ezcache.php --clear-all --allow-root-user` aus.

## 2. Den Shell-Client verwenden (3 Minuten)

Die Extension liefert einen Client in Bash und curl mit, `extension/expservices/bin/expservices-client.sh` (er braucht `jq` oder `python3`, um JSON auszugeben).

```bash
export EXPSERVICES_URL=https://example.com
cd extension/expservices/bin
./expservices-client.sh whoami
./expservices-client.sh call expsystem::version
```

Erwartet für den zweiten Aufruf:

```
{
  "version": "6.0.15stable",
  "major": 6,
  "minor": 0,
  "release": 15,
  "state": "stable",
  "php": "8.5.11"
}
```

Lesen Sie einen Knoten und die Kinder der Inhaltswurzel (Argumente folgen auf den Dienstnamen und werden mit `::` verbunden; für `children` sind es Knoten-ID, Sortierung, Reihenfolge, Limit und Offset):

```bash
./expservices-client.sh call expnode::get 2
./expservices-client.sh call expnode::children 2 name asc 5 0
./expservices-client.sh --meta call expnode::children 2 name asc 5 0 >/dev/null   # prints total, offset, limit, has_more
```

Exit-Status: 0 in Ordnung, 1 Dienstfehler (`error 401: ...` auf stderr), 2 Übertragungsfehler.

## 3. Den passenden Dienst finden (3 Minuten)

Der Katalog listet jeden Dienst mit seiner Zusammenfassung, seiner Zugriffsregel, seinen Argumenten und einem Beispielaufruf auf.

```bash
./expservices-client.sh catalog                 # everything, about 1100 entries
./expservices-client.sh catalog session         # one domain
./expservices-client.sh catalog node | grep -E '"method"|"summary"'
```

Jeder Eintrag hat `access`: `public`, `user` (angemeldet) oder `[module, function]` (eine Richtlinie, dieselben, die die Administration prüft), und `write`: true für Dienste, die etwas ändern. In der Administration listet **Setup > RAD** (`/setup/rad`) denselben Katalog neben den anderen Erweiterungspunkten auf. Tabellen pro Bereich stehen in der Referenz: [Backend-Dienste über ezjscore](../../en/us/bc/6.0/backend_ezjscore_services.md).

Regeln, die für jeden Dienst gelten: 401 ohne Anmeldung, 403 ohne die Richtlinie; ein Schreibzugriff braucht POST und ein Formular-Token und wird als `service.<domain>.<method>` im Audit erfasst; große Vorgänge werden an [Inhaltsjobs](../../en/us/features/6.0/content-jobs.md) weitergeleitet.

## 4. Anmelden und einen Schreibzugriff ausführen (5 Minuten)

Dienste, die als öffentlich markierte Inhalte lesen, funktionieren anonym; alles andere braucht einen Benutzer. Melden Sie sich einmal an (der Client hält das Sitzungscookie in `~/.cache/expservices/cookies.txt`):

```bash
./expservices-client.sh login YOUR_LOGIN        # asks for the password; or set EXPSERVICES_PASSWORD
./expservices-client.sh whoami
```

Erwartet: Ihr Benutzer, nicht mehr anonym. Schreibzugriffe laufen über `post`, das das Formular-Token abholt und es als Feld und als Header sendet; Argumente sind `name=value`-Paare:

```bash
./expservices-client.sh catalog cache | grep -E '"method"|"write"'     # find a write
./expservices-client.sh post expsession::tokenCreate name='my first script' expires_in_days=30
```

Eine nicht erfüllte Richtlinie antwortet mit `error 403: ...`, ein fehlerhaftes Argument mit `error 422` oder `error 400` samt Grund.

## 5. Ein persönliches API-Token für Skripte und Apps (3 Minuten)

Ein Sitzungscookie läuft ab und braucht für jeden Schreibzugriff ein Formular-Token. Ein API-Token nicht. Der obige Aufruf `tokenCreate` antwortet ein einziges Mal mit dem Token (`expt_` gefolgt von 48 Hexadezimalzeichen); gespeichert wird nur sein SHA-256-Hash, kopieren Sie es also jetzt.

```bash
export EXPSERVICES_TOKEN=expt_PASTE_THE_TOKEN_HERE
./expservices-client.sh whoami                  # signed in as the token's user, no login, no cookie
```

Das Token handelt als sein Benutzer mit denselben Richtlinien, jeweils für eine Anfrage. Schreibzugriffe brauchen weiterhin POST, aber kein Formular-Token. Es wird als `Authorization: Bearer <token>` gesendet oder als `X-Exp-Token: <token>`, wo ein Proxy den ersten Header nicht an PHP weiterreicht (die mitgelieferten Clients senden beide). Verwalten Sie Tokens mit `expsession::tokenList` (ID, Name, Hinweis, Erstellung, letzte Verwendung, Ablauf, niemals das Token selbst) und `expsession::tokenRevoke` (POST `id`):

```bash
./expservices-client.sh call expsession::tokenList
./expservices-client.sh post expsession::tokenRevoke id=7
```

Ein unbekanntes, widerrufenes oder abgelaufenes Token oder eines eines deaktivierten Benutzers antwortet bei jedem Dienst mit 401, damit ein Client bemerkt, dass sein Token nicht mehr gilt; ein Token kann keine Tokens erstellen. Erstellen, Widerrufen und Ablehnungen stehen im Audit-Protokoll als `access.expservices.token.create`, `.revoke` und `.failed`:

```bash
./console exp:audit tail --channel=access --name='access.expservices.*' --lines=5 --allow-root-user
```

Bewahren Sie das Token im Geheimnisspeicher Ihrer Plattform auf (Schlüsselbund, Keystore, ein CI-Secret), niemals im Quellcode oder in einer URL.

Antwortet `tokenCreate` auf einer bestehenden Installation mit einem Datenbankfehler, fehlt die Tabelle `expservices_token`; legen Sie sie aus `extension/expservices/sql/<engine>/schema.sql` (`mysql`, `postgresql` oder `sqlite`) mit Ihrem Datenbank-Client an.

## 6. Dasselbe aus Python (2 Minuten)

Der Python-Client verwendet nur die Standardbibliothek:

```bash
python3 extension/expservices/bin/expservices_client.py --url https://example.com call expsystem::version
```

Als Modul:

```python
import sys
sys.path.insert(0, "extension/expservices/bin")
from expservices_client import Client, ServiceError, TransportError

c = Client("https://example.com", api_token="expt_PASTE_THE_TOKEN_HERE")
print(c.call("expsystem::version")["version"])          # 6.0.15stable
try:
    print(c.call("expnode::get", 2)["name"])
except ServiceError as e:                                # the envelope's error: 401 login, 403 policy, 422 data
    print(e.code, e)
except TransportError as e:                              # network or JSON fault
    print("retry later:", e)
```

Clients für Kotlin und Android, Swift und iOS, Objective-C, Qt und GTK sind in der Referenz ausgeschrieben: [Kapitel zu den Clients](../../en/us/bc/6.0/backend_ezjscore_services.md#clients).

## 7. Ein Portal öffnen, das auf den Diensten aufbaut (3 Minuten)

Vier Beispiel-Frontends liegen in `extension/expservices/design/` und stellen Nachrichten, Shop, Foren, Medien, Feeds, Suche und Anmeldung im Browser allein über die Dienste dar:

| Design | Siteaccess | Adresse | Was es ist |
|---|---|---|---|
| `expportal_jquery` | `portaljq` | `/portaljq/` | Die Referenz: kleine Module, jQuery 4, ein Skript pro Funktion |
| `expportal_reactive` | `portalreactive` | `/portalreactive/` | Dieselben Funktionen mit Komponenten, einem Store und einseitigem Datenfluss |
| `expportal_react` | `portalreact` | `/portalreact/` | React 18 mit React Bootstrap, Hash-Routen |
| `expportal_wireframe` | `portalwireframe` | `/portalwireframe/` | Graue Platzhalterbildschirme, die für jeden Block den Dienst nennen, der ihn speist |

Wenn Ihre Installation diese Siteaccesses hat (prüfen Sie es mit `ls settings/siteaccess`), öffnen Sie `https://example.com/portaljq/` und klicken Sie sich durch News, Shop und Forums. Erwartet: Die Seiten werden ohne Build-Schritt dargestellt; der Netzwerk-Reiter des Browsers zeigt Aufrufe von `/ezjscore/call/exp...`.

Um einen hinzuzufügen, legen Sie `settings/siteaccess/portaljq/site.ini.append.php` an mit `[DesignSettings] SiteDesign=expportal_jquery`, `[SiteSettings] DefaultPage=user/login` und `IndexPage=user/login` (das Modul antwortet anonymen Besuchern mit 200, und das Pagelayout ersetzt seine Ausgabe) sowie `[SiteAccessSettings] RequireUserLogin=false`; registrieren Sie dann den Siteaccess und leeren Sie den INI-Cache:

```bash
./console exp:ini add 'site.ini/SiteAccessSettings/AvailableSiteAccessList[]' portaljq override --allow-root-user
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

Machen Sie es mit `exp:ini rem` für denselben Eintrag und durch Entfernen des Verzeichnisses rückgängig. Ein Velocity-Server braucht einen Neustart, um einen neuen Siteaccess zu sehen; Apache mit PHP-FPM nicht.

Richten Sie die Portale in `extension/expservices/settings/expportal.ini`, Block `[Portal]`, auf Ihre Inhaltswurzeln aus: `NewsNode`, `ShopNode`, `ForumsNode` (Standard `2`), `MediaNode` (Standard `43`), `PageSize` (Standard `12`), `SiteTitle`.

## Einstellungen

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `expservices.ini` | `Services` | `Enabled` | `enabled` (`disabled`: jeder Dienst antwortet mit 403) | Installation |
| `expservices.ini` | `Paging` | `DefaultLimit`, `MaxLimit` | `25`, `200` | Installation |
| `expservices.ini` | `Writes` | `RequireToken` | `enabled` (nur für Tests abschalten) | Installation |
| `expservices.ini` | `Writes` | `Audit` | `enabled` | Installation |
| `expservices.ini` | `Community` | `ForumClass`, `ForumContainerClass`, `TopicClass`, `ReplyClass`, `CommentClass`, `PollClass`, `ReviewClass`, `TopicStickyAttribute` | `forum`, `forums`, `forum_topic`, `forum_reply`, `comment`, `poll`, `review`, `sticky` | Installation |

Ändern Sie eine Einstellung mit `./console exp:ini set expservices.ini/Paging/DefaultLimit 50 override --allow-root-user` und leeren Sie den INI-Cache.

## Siehe auch

- Einen eigenen Dienst schreiben: eine Klasse, die `expServiceBase` erweitert, ein Block `[ezjscServer_exp<domain>]`, und er erscheint im Katalog und unter Setup > RAD. Siehe [Remote-Dienste (Funktionsseite)](../../en/us/features/6.0/remote-services-expservices.md) und [die Spezifikation der Dienste](../../en/us/specifications/6.0/expservices.md).
- Absichern, was Sie freigeben: Rollen, Richtlinien, Audit und Debugging: [Sicherheit und Audit](sicherheit-und-audit.md).
- Alles über die Aufrufschnittstelle, Bereich für Bereich: [Backend-Dienste über ezjscore](../../en/us/bc/6.0/backend_ezjscore_services.md); die Extension, die sie trägt: [ezjscore](../../en/us/features/6.0/extensions/ezjscore.md).
- Wie es entstanden ist: [Chronik Oktober 2026](../../en/us/history/2026/2026-10.md), [Changelog 6.0.15](../../changelogs/6.0/6.0.15.md).
- Weitere Anleitungen: [Extensions](extensions.md) (die Extension bauen, die Ihren Dienst trägt), [Glossar](../../en/us/glossary.md).
