---
title: Einstellungsebenen und die Reihenfolge, in der sie gewinnen
audience: developer, designer, app-creator
category: extension-development
order: 20
summary: Die Orte, an denen eine Einstellungsdatei liegen kann - Standard, Erweiterung, Siteaccess, die Ordner je Website in Erweiterungen und das globale Override -, die genaue Reihenfolge, in der sie gelesen werden, und wie Sie für eine Änderung den richtigen wählen.
keywords: Reihenfolge der Einstellungen, Ebene, Override, Siteaccess, Erweiterung, sa-extension, override__group, Platzierung, Priorität, welche Datei gewinnt, exp:ini where, settings order, scope, extension, placement, priority, which file wins
type: help
---
# Einstellungsebenen und die Reihenfolge, in der sie gewinnen

Teil 2, Kapitel 2. Das vorige Kapitel hat gezeigt, dass viele Dateien einen Wert ergeben. Dieses Kapitel nennt die Orte,
an denen diese Dateien liegen, die Reihenfolge, in der der Kernel sie liest, und wie Sie für eine Änderung den richtigen
Ort wählen. Die Fakten stammen aus `eZINI::overrideDirs()` in `lib/ezutils/classes/ezini.php` und aus der Spezifikation
[INI-Override-Verzeichnisse und Platzierungen](../../../spezifikationen/6.0/ini-override-platzierungen.md).

## Die Orte

| Ebene | Verzeichnis | Gelesen für | Was dorthin gehört |
|---|---|---|---|
| default | `settings/` | alles | die Standardwerte des Kernels; werden nie bearbeitet |
| Erweiterungs-Siteaccess (`sa-extension`) | `extension/<ext>/settings/siteaccess/<sa>/`, `extension/<ext>/settings/override__<group>/`, `extension/<ext>/settings/override/` | die Siteaccesses, die einer Erweiterung gehören | Einstellungen je Website innerhalb einer Erweiterung |
| siteaccess | `settings/siteaccess/<sa>/` | einen Siteaccess | die Einstellungen einer Website oder der Administration |
| extension | `extension/<ext>/settings/` | jede Anfrage, für aktive Erweiterungen | die Standardwerte, die eine Erweiterung mitbringt |
| override | `settings/override/` | jede Anfrage | die eigenen globalen Einstellungen dieser Installation |

## Die Reihenfolge

Nach der Standarddatei liest der Kernel die Override-Verzeichnisse in dieser Reihenfolge, und **eine spätere Datei
gewinnt**:

1. die Erweiterungs-Siteaccess-Verzeichnisse (`sa-extension`),
2. `settings/siteaccess/<sa>/` (`siteaccess`),
3. `extension/<ext>/settings/` jeder aktiven Erweiterung (`extension`),
4. `settings/override/` (`override`).

Der Code ist eine Zeile:

```php
return array_merge( $dirs['sa-extension'], $dirs['siteaccess'], $dirs['extension'], $dirs['override'] );
```

Innerhalb einer Ebene steht ein Verzeichnis, das später mit `eZINI::prependOverrideDir()` registriert wird, weiter vorn
in der Liste und hat deshalb eine niedrigere Priorität. Für Erweiterungen heißt das: **Eine Erweiterung, die in
`ActiveExtensions[]` früher steht, gewinnt** gegen eine spätere; deshalb steht die eigene Einstellungserweiterung einer
Installation meist an erster Stelle.

> **Hinweis**
> Beachten Sie, dass die Ebene `extension` **nach** der Ebene `siteaccess` gelesen wird. Ein Wert, den eine Erweiterung
> in `extension/<ext>/settings/` mitbringt, schlägt denselben Wert in `settings/siteaccess/<sa>/`. Braucht eine Website
> einen anderen Wert als den Standard einer Erweiterung, legen Sie ihn in `settings/override/` oder in den eigenen
> Siteaccess-Ordner der Erweiterung, nicht in `settings/siteaccess/<sa>/`.

## Die Ordner je Website in einer Erweiterung

Eine Erweiterung kann Einstellungen je Siteaccess enthalten, sodass eine ganze Website mit allen ihren Siteaccesses ein
einziger Ordner ist:

| Verzeichnis | Gilt für |
|---|---|
| `extension/<ext>/settings/siteaccess/<sa>/` | einen Siteaccess |
| `extension/<ext>/settings/override__<group>/` | jeden Siteaccess, dessen Name mit `<group>__` beginnt (zum Beispiel `shop__en`, `shop__de` für die Gruppe `shop`) |
| `extension/<ext>/settings/override/` | jeden Siteaccess, der der Erweiterung gehört |

Sie werden nur für einen Siteaccess registriert, der seinen Ordner `extension/<ext>/settings/siteaccess/<sa>/` hat;
dieser Ordner macht die Erweiterung zu seiner Eigentümerin. Einzelheiten und ein Beispiel stehen in
[Einstellungen je Website in Erweiterungen](../../../../eng-us/features/6.0/multi-site-ini-overrides.md).

## Platzierungen: Woher kommt ein Wert?

`exp:ini where` und die Einstellungsseite nennen die **Platzierung** jeder Datei:

| Platzierung | Datei |
|---|---|
| `default` | `settings/site.ini` |
| `override` | `settings/override/site.ini.append.php` |
| `extension:<ext>` | `extension/<ext>/settings/site.ini.append.php` |
| `siteaccess` | `settings/siteaccess/<sa>/site.ini.append.php` |
| `ext-siteaccess:<ext>` | `extension/<ext>/settings/siteaccess/<sa>/site.ini.append.php` |
| `ext-siteaccess-override:<ext>` | `extension/<ext>/settings/override/site.ini.append.php` |
| `ext-siteaccess-override__<group>:<ext>` | `extension/<ext>/settings/override__<group>/site.ini.append.php` |
| `injected` | zur Laufzeit von Code gesetzt |

Probieren Sie es mit einer Einstellung, die viele Dateien berühren:

```bash
./console exp:ini where design.ini/ExtensionSettings/DesignExtensions
./console exp:ini where site.ini/DesignSettings/SiteDesign -s <your siteaccess>
```

`-s <siteaccess>` zeigt das Ergebnis so, wie dieser Siteaccess es sieht.

## Den Ort für eine Änderung wählen

| Sie möchten ändern | Legen Sie es ab in | Ebenenwort für `exp:ini` |
|---|---|---|
| Etwas für die ganze Installation, das sich je Server unterscheidet (Datenbank, Mailserver, Pfade) | `settings/override/` | `global` |
| Aussehen oder Sprache einer Website | den Siteaccess oder besser das `settings/siteaccess/<sa>/` Ihrer Website-Erweiterung | `siteaccess:<sa>` oder `extension:<ext>:siteaccess:<sa>` |
| Was Ihre eigene Erweiterung überall braucht | `extension/<yours>/settings/` | `extension:<yours>` |
| Das Verhalten der Erweiterung eines anderen | `settings/override/` oder Ihre eigene Erweiterung, die vor ihr steht | `global` oder `extension:<yours>` |

Zwei Regeln halten das überschaubar:

- **Serverspezifische und geheime Werte** (Datenbankpasswörter, Hostnamen) gehören in `settings/override/`, das nie
  committet wird. Alles andere kann in Erweiterungen unter Versionskontrolle liegen.
- **Bearbeiten Sie nie die Einstellungsdateien einer anderen Erweiterung.** Sie werden ersetzt, wenn diese Erweiterung
  aktualisiert wird. Überschreiben Sie sie.

## Einstellungen in eine Erweiterung verschieben

`exp:ini` kann Einstellungen für Sie zwischen Ebenen verschieben und behält dabei den wirksamen Wert bei:

```bash
./console exp:ini move site.ini/DesignSettings siteaccess:mysite extension:mysite_settings:siteaccess:mysite --dry-run
./console exp:ini move-all siteaccess:mysite extension:mysite_settings:siteaccess:mysite --create-extension --dry-run
```

`move-all` mit `--create-extension` legt die Zielerweiterung (`extension.xml`, `ezinfo.php`, `settings/`) an, wenn sie
noch nicht existiert, und `--activate` schaltet sie ein. Entfernen Sie `--dry-run`, wenn das angezeigte Ergebnis dem
entspricht, was Sie wollen. Die nächsten Kapitel bauen damit eine Website-Erweiterung.

## Verwandte Seiten

- Zurück: [Wie INI-Einstellungen funktionieren](wie-ini-einstellungen-funktionieren.md)
- Weiter: [Siteaccess-Einstellungen](siteaccess-einstellungen.md)
- [INI-Override-Verzeichnisse und Platzierungen](../../../spezifikationen/6.0/ini-override-platzierungen.md)
- [Ladereihenfolge der Erweiterungen](../../../../eng-us/features/6.0/extension-loading-order.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
