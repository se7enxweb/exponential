# Installationsname: Staging und Produktion nie wieder verwechseln

Diese Seite ist für Teams, die eine Entwicklungskopie, eine Staging-Kopie und eine Live-Website nebeneinander betreiben.
Ihre Browser-Tabs sehen gleich aus, und man ändert leicht die falsche. Die Funktion Installationsname setzt den Namen
der Installation dorthin, wo Sie ihn sehen: vor den Seitentitel auf jeder Installation, die keine Produktion ist, in
einen Kommentar im Seitenkopf und überall dorthin, wo ein Template danach fragt. Hinzugefügt am 7. August 2026
(`cbfee1241e`, Klassen `ExpInstallationDetailsOutputFilter` und `ExpInstallationOperator`).

## In drei Schritten einrichten

1. Benennen Sie die Installation und führen Sie die Namen auf, die als Produktion gelten, in
   `settings/override/site.ini.append.php`:

   ```ini
   [SiteSettings]
   EzInstallationName=staging
   ProductionInstallationList[]
   ProductionInstallationList[]=live
   ```

2. Schalten Sie in derselben Datei den Ausgabefilter ein:

   ```ini
   [Event]
   Listeners[]=response/output@ExpInstallationDetailsOutputFilter::filter
   ```

   Der Block `[Event]` ist eine private API, die sich ändern kann (siehe den Kommentar in `settings/site.ini`). Ohne den
   Listener funktionieren die Template-Operatoren unten trotzdem.

3. Leeren Sie den INI-Cache:

   ```bash
   php bin/php/ezcache.php --clear-tag=ini --allow-root-user
   ```

Prüfen Sie das Ergebnis auf der Staging-Kopie:

```bash
curl -s https://staging.example.com/ | grep -E '\[I\]|<title>'
```

Erwartete Ausgabe, etwa:

```
<!-- [I] staging -->
<title>[staging] Home / My site</title>
```

## Einstellungen

Die Schlüssel stehen nicht in der mitgelieferten `site.ini`; fügen Sie sie in `settings/override/site.ini.append.php`
hinzu.

| Datei | Block | Schlüssel | Standard | Geltungsbereich | Bedeutung |
|---|---|---|---|---|---|
| `settings/site.ini` | `SiteSettings` | `EzInstallationName` | nicht gesetzt | global | Der Name dieser Installation, zum Beispiel `staging`. Ist er leer oder fehlt er, tut die Funktion nichts. |
| `settings/site.ini` | `SiteSettings` | `ProductionInstallationList[]` | nicht gesetzt | global | Die Namen, die als Produktion gelten. Steht `EzInstallationName` in der Liste, ist die Installation Produktion. |
| `settings/site.ini` | `Event` | `Listeners[]` | nicht gesetzt | global | `response/output@ExpInstallationDetailsOutputFilter::filter` schaltet den Ausgabefilter ein |

## Was sie mit einer Seite macht

| Wo | Wirkung | Wann |
|---|---|---|
| Direkt nach dem ersten `<head>` | Ein unsichtbarer Kommentar `<!-- [I] staging -->` | immer, wenn der Name gesetzt ist |
| Das erste `<title>` | Wird zu `<title>[staging] ...` | nur, wenn die Installation **keine** Produktion ist |
| `<!--INSTALLATION_NAME-->` | Ersetzt durch den Namen, auf der Produktion durch nichts | überall in der Ausgabe |

Nur das erste `<head>` und das erste `<title>` werden angefasst, sodass eingebettetes SVG oder Codebeispiele, die diese
Wörter enthalten, unberührt bleiben. Der Name wird HTML-maskiert.

## Template-Operatoren

```
{installation_name()}                       {* staging *}
{installation_name( hide_on_prod=true() )}  {* empty on production *}
{if is_production_system()} ... {/if}
```

`installation_name( hide_on_prod )` gibt den konfigurierten Namen zurück, oder auf der Produktion eine leere
Zeichenkette, wenn `hide_on_prod` true ist. `is_production_system()` ist true, wenn der Name in
`ProductionInstallationList[]` steht.

Beispiel: Analytics nur auf der Produktion laden.

```
{if is_production_system()}
    {include uri='design:page_head_analytics.tpl'}
{/if}
```

## Grenzen

- Die Prüfung erfolgt nach dem Namen: Ein Tippfehler in der Liste lässt die Live-Website wie Staging aussehen.
- Eine Seite ohne `<head>` oder `<title>` (eine JSON-Antwort) wird nicht verändert.
- Der Filter läuft bei jeder Antwort, die das Ereignis `response/output` durchläuft.

## Verwandte Seiten

- [Eine saubere Installation, die Exponential sagt](standardwerte-einer-sauberen-installation.md)
- [INI-Überschreibungen für mehrere Websites](ini-ueberschreibungen-fuer-mehrere-websites.md), [Platzierungen von INI-Überschreibungen](../../spezifikationen/6.0/ini-override-platzierungen.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)
- [Chronik: August 2026](../../geschichte/2026/2026-08.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
