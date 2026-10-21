---
title: "Lektion 8.2: Siteaccess-Einstellungen"
audience: trainer, developer
category: training
order: 82
summary: Die Teilnehmenden betrachten einen Siteaccess aus der Sicht eines Entwicklers, listen die Siteaccesses auf und sehen, wie Anfragen ihnen zugeordnet werden, vergleichen Einstellungen zwischen zwei Siteaccesses und entscheiden, ob eine Einstellung in den globalen Override, einen Siteaccess oder eine Erweiterung gehört. 45 Minuten.
keywords: entwickler, siteaccess, settings/siteaccess, AvailableSiteAccessList, RelatedSiteAccessList, MatchOrder, URIMatchType, ActiveAccessExtensions, siteaccess-geltungsbereich, exp:ini scopes, -s siteaccess, wohin gehört eine einstellung, matching, developers course, siteaccess scope, where does a setting belong
type: help
---
# Lektion 8.2: Siteaccess-Einstellungen

Ein Siteaccess ist ein Satz von Einstellungen, der für eine Anfrage gewählt wird: die öffentliche Website, die
Administration, der Editor, eine zweite Sprache. Für Entwickler beantwortet er zwei Fragen: welcher Siteaccess läuft
und wo eine Einstellung stehen muss, damit sie für die richtigen gilt. Diese Lektion behandelt beides; der Kurs
Multisite und Seitenpakete geht ausführlich auf das Matching und mehrere Websites ein.

## Auf einen Blick

| | |
|---|---|
| Kurs | [Entwickler](../README.md) |
| Modul | [Modul 8: Einstellungen und Siteaccesses](README.md) |
| Dauer | 45 Minuten (15 Präsentation, 30 Übung) |
| Niveau | Fortgeschrittene |
| Zielgruppe | Entwickler |
| Voraussetzungen | [Lektion 8.1](wie-einstellungen-gelesen-werden.md) |

## Lernziele

Nach dieser Lektion können die Teilnehmenden:

- die Siteaccesses einer Installation und den Ordner mit den Einstellungen jedes einzelnen auflisten;
- `MatchOrder` und die Matching-Einstellungen lesen, die einen Siteaccess für eine Anfrage wählen;
- einen Befehl mit `-s <siteaccess>` für einen bestimmten Siteaccess ausführen;
- eine Einstellung mit `exp:ini where` zwischen zwei Siteaccesses vergleichen;
- entscheiden, ob eine Einstellung in den globalen Override, einen Siteaccess-Ordner oder eine Erweiterung gehört.

## Hinweise für Trainer

### So präsentieren Sie es

Quellen: `settings/site.ini [SiteAccessSettings]`,
[Was ein Siteaccess ist](../../multisite-und-pakete/modul-01-siteaccesses-fuer-mehrere-websites/was-ein-siteaccess-ist.md)
(Kurs Multisite und Seitenpakete) und
[Siteaccess-Matching nach Host und Sprache](../../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md).

1. **Was er ist** (3 Minuten). Ein Siteaccess ist ein Ordner `settings/siteaccess/<name>/` mit Einstellungen für
   einen Zugang zur Installation, aufgeführt in `[SiteAccessSettings] AvailableSiteAccessList[]`.
2. **Matching** (4 Minuten). Standardmäßig `MatchOrder=uri;host;port`: der erste Teil des Pfads, der Hostname, der
   Port. `URIMatchType=element` mit `URIMatchElement=1` nimmt das erste Pfadelement (`/admin/...`). Eine Installation
   aus `exp:install` verwendet standardmäßig das Matching über den Pfad.
3. **Verwandte Siteaccesses** (2 Minuten). `RelatedSiteAccessList[]` nennt die Siteaccesses, die sich eine
   Inhaltsdatenbank teilen, zum Beispiel eine Website, ihre Administration und ihren Editor.
4. **Befehle** (2 Minuten). Skripte und `exp:ini` laufen für einen Siteaccess: `-s <sa>` oder das zweite Argument
   von `exp:ini where`.
5. **Wohin eine Einstellung gehört** (4 Minuten).

   | Die Einstellung gilt für | Schreiben Sie sie in |
   |---|---|
   | Jeden Siteaccess, nur auf diesem Rechner | `global` (`settings/override/`) |
   | Einen Siteaccess, nur auf diesem Rechner | `siteaccess:<sa>` |
   | Jede Installation Ihrer Erweiterung | `extension:<ext>` |
   | Einen Siteaccess jeder Installation Ihrer Erweiterung | `extension:<ext>:siteaccess:<sa>` |

### Häufige Fragen

**Von welchem Siteaccess wird eine Seite ausgeliefert?** Die Debug-Ausgabe nennt ihn; `exp:ini where ... <sa>` zeigt
seine Einstellungen.

**Kann eine Erweiterung nur für einen Siteaccess aktiv sein?** Ja, mit `ActiveAccessExtensions[]` in der
`site.ini.append.php` dieses Siteaccess (Lektion 7.3).

**Welches Design hat der Admin-Siteaccess?** Sein eigenes `[DesignSettings] SiteDesign`, bei neuen Installationen in
der Regel `admin4`.

### Stolperfallen

- **Einstellungen für eine Website im globalen Override.** Sie gelten dann auch für die Administration.
- **Ein neuer Siteaccess-Ordner, der nicht aufgeführt ist.** Ein Ordner ohne Eintrag in `AvailableSiteAccessList[]`
  wird nie gewählt.
- **Mit dem falschen Siteaccess testen.** Ein Skript ohne `-s` läuft mit dem Standard-Siteaccess, und das ist
  womöglich nicht der, den Sie geändert haben.

## Praktische Übung

Auf der Entwicklungsinstallation.

1. Listen Sie die Siteaccess-Ordner auf: `ls settings/siteaccess/`.
2. Listen Sie die wirksamen verfügbaren Siteaccesses auf:
   `php bin/php/console exp:ini get site.ini/SiteAccessSettings/AvailableSiteAccessList`.
3. Lesen Sie das Matching: `php bin/php/console exp:ini where site.ini/SiteAccessSettings/MatchOrder`.
4. Listen Sie die Geltungsbereiche auf, die Ihre Installation anbietet: `php bin/php/console exp:ini scopes`.
5. Vergleichen Sie das Design zweier Siteaccesses:
   `php bin/php/console exp:ini where site.ini/DesignSettings/SiteDesign <sa>` und dasselbe mit `admin`.
6. Führen Sie den Spielplatz für zwei Siteaccesses aus und vergleichen Sie einen Link: Fügen Sie
   `{'content/view/full/2'|ezurl}` zu `play.tpl` hinzu und führen Sie
   `php bin/php/ezexec.php -s <sa> extension/mysite_hello/scripts/play.php` aus, dann mit `-s admin`.
7. Schreiben Sie für jede dieser Einstellungen den Geltungsbereich auf, den Sie wählen würden: den Namen der
   Website, die Debug-Ausgabe auf Ihrem Laptop, die Operator-Registrierung Ihrer Erweiterung, einen Template-Override
   nur für die öffentliche Website.
8. Prüfen Sie Ihre Antworten anhand der Tabelle in den Hinweisen für Trainer.

## Erwartetes Ergebnis

- Schritt 2: die Namen der Siteaccesses, zum Beispiel `site`, `admin` und `editor` bei einer Installation mit
  `exp:install`.
- Schritt 3: `MatchOrder` mit seinem wirksamen Wert.
- Schritt 5: verschiedene Designs, zum Beispiel das Design der Website und `admin4`.
- Schritt 6: Die Links unterscheiden sich in ihrem Siteaccess-Teil, wenn das Matching über den Pfad erfolgt.
- Schritt 7: Siteaccess (oder der Siteaccess-Geltungsbereich einer Erweiterung), global, `extension:mysite_hello`,
  `extension:mysite_hello:siteaccess:<sa>`.
- Der Trainer bespricht Schritt 7 mit der Gruppe.

## Quiz

1. Welche Einstellung listet die Siteaccesses auf, die eine Installation wählen darf?
2. Wie lautet die Standard-`MatchOrder`?
   A. `host;uri;port` B. `uri;host;port` C. `port;host;uri` D. `uri`
3. Wie führen Sie ein Skript für den Siteaccess `admin` aus?
4. In welchen Geltungsbereich gehört ein Template-Override für die öffentliche Website jeder Installation Ihrer
   Erweiterung?
5. Warum ist der globale Override ein schlechter Ort für Einstellungen, die nur für die öffentliche Website gelten?

<details>
<summary>Antworten</summary>

1. `[SiteAccessSettings] AvailableSiteAccessList[]`.
2. **B.** `uri;host;port`, in `settings/site.ini`.
3. Mit `-s admin`, zum Beispiel `php bin/php/ezexec.php -s admin <script>`.
4. `extension:<ext>:siteaccess:<sa>`, der Ordner `settings/siteaccess/<sa>/` der Erweiterung.
5. Er gilt für jeden Siteaccess, einschließlich Administration und Editor.

</details>

## Weiterführende Literatur

- [Was ein Siteaccess ist](../../multisite-und-pakete/modul-01-siteaccesses-fuer-mehrere-websites/was-ein-siteaccess-ist.md):
  der Kurs Multisite und Seitenpakete.
- [Siteaccess-Matching nach Host und Sprache](../../../../funktionen/6.0/siteaccess-zuordnung-nach-host-und-sprache.md).
- [INI-Overrides für mehrere Websites](../../../../funktionen/6.0/ini-ueberschreibungen-fuer-mehrere-websites.md): Einstellungen pro
  Website innerhalb von Erweiterungen.

## Verwandte Seiten

- [Modul 8: Einstellungen und Siteaccesses](README.md)
- [Entwickler: Kursüberblick](../README.md)
- [Trainer: hier beginnen](../../trainer-hier-beginnen.md)
- [Freie Dokumentation](../../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
