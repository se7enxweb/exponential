---
title: "Lektion 3.2: Einstellungen für jeden Siteaccess einer Website"
audience: trainer, admin, developer
category: training
order: 32
summary: Die Teilnehmenden legen Einstellungen, die für alle Siteaccesses einer Website gelten, in das Verzeichnis settings/override der Website-Erweiterung, lernen die Override-Verzeichnisse für Gruppen von Siteaccesses kennen und weisen mit exp:ini where nach, dass die anderen Websites nicht betroffen sind. 45 Minuten.
keywords: multisite und seitenpakete, ini-overrides pro website, website-erweiterung, ini-cache, multisite and site packages, multi-site INI overrides, extension settings/override, override__group, ext-siteaccess-override, site extension, eZINI_CONFIG_CACHE_DIR, EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST, INI cache
type: help
---
# Lektion 3.2: Einstellungen für jeden Siteaccess einer Website

Eine Website mit drei Sprachen hat drei Siteaccesses, und die meisten ihrer Einstellungen sind für alle gleich. Sie
dreimal zu schreiben, lädt zu Fehlern ein; sie im globalen Override zu schreiben, ändert auch jede andere Website.
Eine Website-Erweiterung kann ein Verzeichnis `override` mitbringen, dessen Einstellungen nur gelten, während einer
ihrer eigenen Siteaccesses läuft. Diese Lektion verwendet es für die Website, die in Lektion 3.1 nach `site_clubb`
verschoben wurde.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Multisite und Seitenpakete](../README.md) |
| Modul | [Modul 3: Einstellungen, Logs und Caches pro Website](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Experten |
| Zielgruppe | Administratoren und Entwickler |
| Voraussetzungen | [Lektion 3.1](eine-website-in-einer-eigenen-erweiterung.md): `site_clubb` enthält die Siteaccesses `site_b` und `site_b_ger` |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die drei Einstellungsverzeichnisse pro Website einer Website-Erweiterung und ihre Priorität nennen;
- eine Einstellung schreiben, die für jeden Siteaccess einer Website gilt und für keinen anderen;
- erklären, wann Verzeichnisse `override__<group>` gelten;
- die Platzierung `ext-siteaccess-override:<extension>` mit `exp:ini where` in der Ladereihenfolge finden.

## Hinweise für Trainer

### So präsentieren Sie es

Quelle: [Einstellungen pro Website in Erweiterungen (Multi-Site-Hosting)](../../../../../eng-us/features/6.0/multi-site-ini-overrides.md).

1. **Die drei Verzeichnisse** (6 Minuten). Für eine Erweiterung `site_example`, gelesen während einer **ihrer**
   Siteaccesses aktiv ist, höchste Priorität zuerst:

   | Verzeichnis | Gilt für |
   |---|---|
   | `extension/site_example/settings/override/` | jeden Siteaccess der Erweiterung |
   | `extension/site_example/settings/override__<group>/` | jeden Siteaccess, dessen Name mit `<group>__` beginnt |
   | `extension/site_example/settings/siteaccess/<siteaccess>/` | nur diesen Siteaccess |

   "Ihre" Siteaccesses sind die, für die die Erweiterung ein Verzeichnis `settings/siteaccess/<siteaccess>/` hat; die
   neuen Verzeichnisse werden nur dann gesucht, sodass Erweiterungen, die nichts damit zu tun haben, nichts kosten. Das
   eigene `settings/override/` der Installation hat über alle drei weiterhin das letzte Wort.
2. **Gruppen** (3 Minuten). Siteaccesses namens `example__eng` und `example__ger` bilden die Gruppe `example`;
   `override__example/` gilt für beide. Die Namen der Übungsumgebung verwenden keinen doppelten Unterstrich, deshalb
   verwendet die Übung `override/`.
3. **Ein INI-Cache für alle Projekte** (3 Minuten). Ein Multi-Site-Wrapper kann vor dem Bootstrapping
   `$GLOBALS['eZINI_CONFIG_CACHE_DIR']` setzen, sodass alle Projekte einen INI-Cache teilen und ein einmaliges Leeren
   ihn für alle leert. Ohne ihn ändert sich nichts. Das Gegenteil, ein INI-Cache pro Website, ist Lektion 3.4.
4. **Viele Website-Erweiterungen** (3 Minuten). Die Konstante `EXP_SITE_STRUCTURE_EZ_INI_OVERRIDE_DIR_LIST`, auf
   `true` gesetzt (zum Beispiel in `config.php`), lässt für die aktuelle Anfrage die Override-Verzeichnisse der anderen
   Erweiterungen `extension/site_*` weg. Lassen Sie sie aus, sofern Sie nicht viele Websites hosten.

### Häufige Fragen

**Schlägt eine Einstellung im `override/` der Erweiterung das globale Override?** Nein. Das globale
`settings/override/` hat weiterhin das letzte Wort.

**Schlägt sie das eigene Siteaccess-Verzeichnis der Erweiterung?** Ja: `override/` hat eine höhere Priorität als
`settings/siteaccess/<siteaccess>/` derselben Erweiterung.

**Kann `exp:ini` in das Verzeichnis `override/` der Erweiterung schreiben?** Seine Scopes listet `exp:ini scopes`
auf; auf einer Installation, auf der kein Scope dieses Verzeichnis nennt, schreiben Sie die Datei von Hand, wie in der
Übung.

**Wie sehe ich diese Dateien auf einer Seite?** Mit eingeschaltetem INI-Debugging listet die Debug-Leiste die
geladenen Dateien von `site.ini`; der Platzierungsname einer solchen Einstellung ist
`ext-siteaccess-override:<extension>`.

### Stolperfallen

- **Kein Siteaccess-Verzeichnis in der Erweiterung.** Ohne `settings/siteaccess/<siteaccess>/` für einen Siteaccess
  wird ihr `override/` für ihn nie gelesen. Lektion 3.1 hat beides angelegt.
- **Der Dateirahmen.** Eine Datei `.ini.append.php` beginnt mit `<?php /*` und endet mit `*/ ?>`, wie die Dateien in
  `settings/override/`: Die Einstellungen stehen in einem PHP-Kommentar, sodass die Datei nichts anzeigt, falls sie je
  aus dem Web angefragt wird. Kopieren Sie den Rahmen genau.
- **Der INI-Cache.** Leeren Sie ihn nach dem Schreiben von Hand (`exp:ini` erledigt das selbst).

## Praktische Übung

Verwenden Sie Ihre Schulungsinstallation aus Lektion 3.1.

1. Prüfen Sie die Scopes, die die Installation für die Erweiterung anbietet: `php bin/php/console exp:ini scopes | grep site_clubb`.
2. Legen Sie das Verzeichnis und die Datei von Hand an:

   ```bash
   mkdir -p extension/site_clubb/settings/override
   ```

   Schreiben Sie `extension/site_clubb/settings/override/site.ini.append.php` mit diesem Inhalt:

   ```ini
   <?php /* #?ini charset="utf-8"?

   [SiteSettings]
   MetaDataArray[author]=Club B

   */ ?>
   ```

3. Leeren Sie den INI-Cache: `php bin/php/ezcache.php --clear-tag=ini` (starten Sie Velocity neu, wenn es die
   Übungsumgebung ausliefert).
4. Lesen Sie den Wert für jeden Siteaccess:

   ```bash
   php bin/php/console exp:ini where 'site.ini/SiteSettings/MetaDataArray[author]' site_b
   php bin/php/console exp:ini where 'site.ini/SiteSettings/MetaDataArray[author]' site_b_ger
   php bin/php/console exp:ini where 'site.ini/SiteSettings/MetaDataArray[author]' site
   ```

5. Vergleichen Sie die drei Ausgaben: Welche Dateien werden für `site_b` aufgeführt und welche für `site`?
6. Prüfen Sie die Seite: `curl -s http://127.0.0.1:8087/site_b | grep -i 'name="author"'` und dasselbe für `/site`.
7. Fügen Sie jetzt `SiteName=Club B, all languages` zum Block `[SiteSettings]` derselben Datei hinzu, leeren Sie den
   INI-Cache und öffnen Sie `/site_b_ger`. Erklären Sie, warum der deutsche Name aus Lektion 1.4 verschwunden ist.
8. Entfernen Sie die Zeile `SiteName` wieder und leeren Sie den INI-Cache.

## Erwartetes Ergebnis

- Schritt 4: Für `site_b` und `site_b_ger` enthält die Liste `extension/site_clubb/settings/override/site.ini.append.php`,
  und der wirksame Wert ist `Club B`; für `site` wird die Datei nicht geladen, und der Wert ist der, den die
  Hauptwebsite hatte.
- Schritt 6: Das Meta-Tag `author` lautet nur auf `/site_b` `Club B` (sofern das Design `MetaDataArray` ausgibt;
  andernfalls verlassen Sie sich auf Schritt 4).
- Schritt 7: `/site_b_ger` zeigt *Club B, all languages*, weil das `override/` der Erweiterung ihr
  Siteaccess-Verzeichnis schlägt.
- Der Trainer prüft Schritt 4 in einer Übungsumgebung und fragt nach der Erklärung für Schritt 7.

## Quiz

1. Ordnen Sie nach Priorität: das `settings/siteaccess/site_b/` der Erweiterung, das `settings/override/` der
   Erweiterung, das globale `settings/override/`.
2. Welche Siteaccesses lesen `extension/site_example/settings/override__shop/`?
   A. Alle B. Die, deren Namen mit `shop__` beginnen C. Nur `shop` D. Keiner
3. Warum wird das `override/` der Erweiterung für den Siteaccess `site` nicht gelesen?
4. Wie lautet der Platzierungsname einer solchen Einstellung in der Debug-Ausgabe?
5. Was bewirkt das Setzen von `$GLOBALS['eZINI_CONFIG_CACHE_DIR']` vor dem Bootstrapping?

<details>
<summary>Antworten</summary>

1. Zuerst das globale `settings/override/`, dann das `override/` der Erweiterung, dann ihr `siteaccess/site_b/`
   ([Einstellungen pro Website in Erweiterungen](../../../../../eng-us/features/6.0/multi-site-ini-overrides.md#where-the-files-go)).
2. **B.**
3. Die Erweiterung hat kein Verzeichnis `settings/siteaccess/site/`, deshalb ist `site` keiner ihrer Siteaccesses.
4. `ext-siteaccess-override:<extension>`.
5. Jedes Projekt der Installation verwendet dasselbe INI-Cache-Verzeichnis, sodass ein einmaliges Leeren ihn für alle
   leert.

</details>

## Weiterführende Informationen

- [Einstellungen pro Website in Erweiterungen (Multi-Site-Hosting)](../../../../../eng-us/features/6.0/multi-site-ini-overrides.md).
- [Spezifikation: Platzierungen von INI-Overrides](../../../../spezifikationen/6.0/ini-override-platzierungen.md).
- [Erweiterungen in mehr als einem Ordner](../../../../../eng-us/features/6.0/additional-extension-directories.md).
- [Ihre eigene INI-Datei](../../../../../eng-us/help/designers/extensions/your-own-ini-file.md).

## Verwandte Seiten

- [Modul 3: Einstellungen, Logs und Caches pro Website](README.md)
- [Multisite und Seitenpakete: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../../../../eng-us/help/trainers/trainers-start-here.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge
zurück und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website und
ein Partner-Dashboard sind für Mitte 2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und
neue Funktionen früher bereitstellen können. Beginnen Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
