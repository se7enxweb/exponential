# HTTP-Caching für anonyme Besucher

Lesen Sie diese Seite, wenn Browser, Proxys oder ein CDN Seiten und statische Dateien für anonyme Besucher behalten
sollen, oder wenn Sie sich fragen, warum der Browser eines wiederkehrenden Besuchers jedes Stylesheet und jedes Bild
erneut anfragt. Zwei Einstellungen entscheiden, was der Browser eines Besuchers behalten darf: `[HTTPHeaderSettings]`
in der Anwendung (Seiten) und `StaticMaxAge` in Velocity (statische Dateien). Keine von beiden ändert etwas für einen
angemeldeten Benutzer.

## Kurz gesagt

| | |
|---|---|
| Was sich geändert hat | Die Header der Seiten lassen sich nur für anonyme Besucher cachebar machen; `ezpKernelWeb::run()` entfernt das Standard-`Pragma`, wenn ein konfigurierter Header `Cache-Control` überschreibt; Velocity sendet eine lange Lebensdauer für statische Dateien. |
| Wer betroffen ist | Websites, die Caching für anonyme Besucher möchten. Eine Installation, die nichts konfiguriert, sendet dieselben Header für Seiten wie bisher. |
| Wie Sie es prüfen | `curl -sI https://your-host/ \| grep -i -E "cache-control\|expires\|pragma"` als anonymer Besucher. |
| Wie Sie es beheben | Schalten Sie die Header für Seiten wie unten gezeigt ein; senken Sie `StaticMaxAge`, während Sie an einem Stylesheet arbeiten. |

## Das Problem

Jede Antwort auf eine Seite enthielt:

```
Cache-Control: no-cache, must-revalidate
Expires: Mon, 26 Jul 1997 05:00:00 GMT
Pragma: no-cache
```

und jede statische Datei enthielt:

```
Cache-Control: public, max-age=0, must-revalidate
```

Daher durfte kein Browser, Proxy oder CDN eine Seite für irgendeine Zeitspanne behalten, und jedes Stylesheet, jedes
Skript und jedes Bild musste bei jedem Aufruf neu validiert werden. Auf der Startseite sind das **41 bedingte Anfragen
pro Besuch**, jede ein Hin und Zurück, das mit `304` beantwortet wird. Die Bytes wurden gespart, die Latenz nicht, und
auf einer Seite mit Dutzenden Assets ist die Latenz der ganze Preis des ersten Renderns.

## Seiten: `[HTTPHeaderSettings]`

Der Kernel hatte diese Einstellungen schon immer; sie sind standardmäßig ausgeschaltet (`CustomHeader=disabled` in
`settings/site.ini`). Um sie einzuschalten, fügen Sie zu `settings/override/site.ini.append.php` hinzu:

```ini
[HTTPHeaderSettings]
CustomHeader=enabled
OnlyForAnonymous=enabled
OnlyForContent=enabled

HeaderList[]
HeaderList[]=Cache-Control
HeaderList[]=Expires

Cache-Control[]
Cache-Control[/]=public, max-age=60
Expires[]
Expires[/]=60
```

Leeren Sie dann den INI-Cache:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
```

- **`OnlyForAnonymous` macht das sicher.** `eZHTTPHeader::enabled()` löst es zu `!eZUser::isCurrentUserRegistered()`
  auf, sodass ein angemeldeter Redakteur immer die ursprünglichen No-Cache-Header erhält und eine personalisierte Seite
  nie an einen gemeinsamen Cache übergeben wird.
- `Expires` wird in Sekunden angegeben; der Kernel wandelt es in ein Datum um.
- **Der Preis ist Aktualität.** Ein anonymer Besucher sieht eine Seite nach einer Bearbeitung womöglich bis zu
  `max-age` Sekunden alt. Sechzig Sekunden sind kurz genug, dass es beim Veröffentlichen niemand bemerkt, und lang
  genug, um einen Ansturm abzufangen.
- Um zurückzukehren, setzen Sie `CustomHeader=disabled`.

### So prüfen Sie es

Prüfen Sie beide Richtungen: Die anonyme Antwort muss cachebar sein, und eine Antwort an angemeldete Benutzer darf es
nie sein.

```bash
curl -sI https://your-host/ | grep -i -E "cache-control|expires|pragma"
```

Erwartet für einen anonymen Besucher mit den obigen Einstellungen: `Cache-Control: public, max-age=60`, ein
`Expires`-Datum 60 Sekunden in der Zukunft und kein `Pragma: no-cache`. Wiederholen Sie die Anfrage mit dem
Session-Cookie eines angemeldeten Benutzers (kopieren Sie es aus dem Browser): Die Antwort muss
`Cache-Control: no-cache, must-revalidate` zeigen.

### Die Änderung im Kernel dahinter

`Pragma: no-cache` ist die HTTP/1.0-Schreibweise von `Cache-Control` und kann „cachebar“ nicht ausdrücken. Neben
`public, max-age=60` stehen gelassen, widersprach sich die Antwort selbst. Browser lösen das zugunsten von
`Cache-Control` auf, aber ein Proxy kann das `Pragma` lesen und die Seite nicht speichern, was den Zweck des Headers
zunichtemacht.

`ezpKernelWeb::run()` entfernt jetzt das Standard-`Pragma`, wenn, und nur wenn, ein konfigurierter Header
`Cache-Control` überschrieben hat. Eine Antwort, die das Standard-`Cache-Control` behält, behält auch ihr passendes
`Pragma`, sodass eine Installation, die nichts konfiguriert hat, nicht betroffen ist.

## Statische Dateien: `StaticMaxAge` (Velocity)

| Datei | Block | Schlüssel | Standard | Geltungsbereich |
|---|---|---|---|---|
| `settings/velocity.ini` | `ServerSettings` | `StaticMaxAge` | `31536000` (ein Jahr, in Sekunden) | Installation |

Velocity schreibt den Wert als `Q.web.static.maxAge` in die Konfiguration des Servers. `0` belässt den eigenen
Standard des Servers, der Caching erlaubt und den Client dann trotzdem neu validieren lässt.

Ein Jahr ist das, was der andere Webserver vor dieser Installation für dieselben Dateien bereits sendet. Der Preis
ist, dass eine an Ort und Stelle ersetzte Datei erst bemerkt wird, wenn die Lebensdauer abläuft: Leeren Sie den
Browser-Cache oder senken Sie die Zahl, während Sie an einem Stylesheet arbeiten.

## Was es bringt

Gezählt pro Asset auf der Startseite:

| | Assets | Ohne Anfrage wiederverwendbar | Müssen neu validiert werden |
|---|---|---|---|
| Vorher | 41 | **0** | **41** |
| Nachher | 41 | **41** | **0** |
| nginx, zum Vergleich | 41 | 41 | 0 |

Das macht einen ersten Besuch nicht schneller. Es macht jeden späteren Besuch und jede Navigation innerhalb der
Website um 41 Hin-und-Zurück ärmer, was ein wiederkehrender Besucher so erlebt, dass die Seite sofort erscheint.

## Was das nicht ist

- **Nicht der Antwort-Cache des Servers**, der gerenderte Antworten im Elternprozess speichert; das ist eine eigene
  Funktion, standardmäßig ausgeschaltet ([Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)).
- **Nicht der statische Cache des Kernels** (`StaticCache` in `site.ini`), der flache Dateien schreibt.
- **Nicht der rollenabhängige HTTP-Cache**, der ganze Seiten auch für angemeldete Besucher behält und sie ausliefert,
  bevor der Kernel startet: siehe [HTTP-Cache](httpcache.md).
- **Keine Verringerung der Arbeit beim Rendern.** Die Startseite kostet etwa 529 Datenbankabfragen, und kein
  Caching-Header ändert das für einen Besucher, der den Cache verfehlt.

## Verwandte Seiten

- [HTTP-Cache (rollenabhängig)](httpcache.md)
- [Velocity: Exponential in einem Webserver mit persistenten Workern betreiben](../../../eng-us/features/6.0/velocity-persistent-worker-server.md)
- [Antwort-Cache von Velocity](../../../eng-us/features/6.0/velocity-response-cache.md)
- [Cache-Leerungen, die Verzeichnisse beiseiteschieben](../../../eng-us/features/6.0/cache-clear-rename-aside.md)
- [Verhaltensänderungen vom 16. bis 30. September 2026](verhaltensaenderungen-2026-09b.md)
- [Chronik, 16. bis 30. September 2026](../../../eng-us/history/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
