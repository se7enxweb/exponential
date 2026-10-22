---
title: Die mit Exponential mitgelieferten Erweiterungen
audience: ecosystem, admin, developer, hosting
category: ecosystem
order: 21
summary: Die Erweiterungen von se7enxweb, die die composer.json des Kernels in jede Installation bringt - was sie abdecken, welche ausgeschaltet sind, bis Sie sie aktivieren, wie Sie die aktuelle Liste sehen und wie Sie eine hinzufügen oder entfernen.
keywords: mitgelieferte Erweiterungen, Standardverteilung, composer.json, require, suggest, se7enxweb, eztags, cjw_newsletter, expai, explayouts, ezprestapi, Zwei-Faktor, inaktive Erweiterungen, bundled extensions, default extension distribution, two-factor, inactive extensions
type: help
---
# Die mit Exponential mitgelieferten Erweiterungen

"`composer.json` of this release requires 66 `se7enxweb/*` packages, so every installation brings them", die
`composer.json` dieses Releases verlangt also 66 Pakete von se7enxweb, und jede Installation bringt sie mit, gezählt am
18. Oktober 2026; "Many are inactive until you switch them on", viele sind inaktiv, bis Sie sie einschalten
([Das Ökosystem der Erweiterungen](../../wer-ist-die-exponential-foundation.md#das-ökosystem-der-erweiterungen)).

## Inhalt

1. [Die aktuelle Liste sehen](#die-aktuelle-liste-sehen)
2. [Was sie abdecken](#was-sie-abdecken)
3. [Aktiv oder nicht](#aktiv-oder-nicht)
4. [Eine hinzufügen oder entfernen](#eine-hinzufügen-oder-entfernen)

## Die aktuelle Liste sehen

```bash
grep -n '"se7enxweb/' composer.json
```

"The first block of the output is `suggest`, the second `require`. The current `require` and `suggest` sections of
`composer.json` are the authority", also: Der erste Block der Ausgabe ist `suggest`, der zweite `require`, und
maßgeblich sind die aktuellen Abschnitte `require` und `suggest` der `composer.json`
([Die Standardverteilung der Erweiterungen](../../../funktionen/6.0/standard-extension-auslieferung.md)).

## Was sie abdecken

| Bereich | Beispiele | Seite |
|---|---|---|
| Seitenbau | `explayouts` und die Pakete `explayouts-*` | [Exponential Layouts](../produkte/exponential-layouts.md) |
| Hilfe beim Schreiben | `expai` | [Exponential Intelligence](../produkte/exponential-intelligence.md) |
| Hintergrundarbeit | `expqueue` | [expqueue](../../../funktionen/6.0/erweiterungen/expqueue.md) |
| Administration | `exp_adminui`, `expui` | [AdminUI und die Admin-Designs](../produkte/adminui-und-admin-designs.md) |
| Taxonomie | `eztags` | [eztags](../../../funktionen/6.0/erweiterungen/eztags.md) |
| Newsletter | `cjw_newsletter` | [cjw_newsletter](../../../funktionen/6.0/erweiterungen/cjw_newsletter.md) |
| Sicherheit | `sevenx_authentication_2fa`, `recaptcha`, `hcaptcha` | [Zweistufige Anmeldung](../../../funktionen/6.0/erweiterungen/sevenx_authentication_2fa.md) |
| Shop | `ezpaypal` | [ezpaypal](../../../funktionen/6.0/erweiterungen/ezpaypal.md) |
| APIs | `ezprestapi` | [ezprestapi](../../../funktionen/6.0/erweiterungen/ezprestapi.md) |
| Datenbanken | `ezoracle`, `sevenx_dse` | [Datenbankunterstützung](../../datenbankunterstuetzung.md) |
| Themes | `sevenx_themes_simple`, `sevenx-themes-media` | [Themes und Designs](themes-und-designs.md) |
| Betrieb | `git_manager` | [git_manager](../../../funktionen/6.0/erweiterungen/git_manager.md) |
| SEO und Statistik | `bcgooglesitemaps`, `bcwebsitestatistics`, `xrowmetadata` | [Erweiterungen](../../../funktionen/6.0/erweiterungen/README.md) |

Die vollständige Tabelle mit je einer Zeile: [Wer ist 7x?](../../wer-ist-7x.md#das-ökosystem-der-erweiterungen).

## Aktiv oder nicht

Installiert heißt nicht aktiv. Optionale Erweiterungen, "that `composer.json` brings but leaves inactive, such as
Exponential Intelligence and the job queue, are switched on afterwards", die `composer.json` also mitbringt, aber
inaktiv lässt, wie Exponential Intelligence und die Job-Warteschlange, werden danach eingeschaltet (`README.md`,
"Installation"); das Installationsbuch sagt, wie ([Nach der Installation](../../../installation/10-nach-der-installation.md)).

## Eine hinzufügen oder entfernen

Die Schritte stehen unter [Die Standardverteilung der Erweiterungen](../../../funktionen/6.0/standard-extension-auslieferung.md).
Versionseinschränkungen verwenden `~`, das "lets the last position float", die letzte Stelle also beweglich lässt.

Jede Erweiterung hat ihr eigenes Repository, ihre eigenen Releases und ihren eigenen Changelog
([Changelogs der Erweiterungen](../../../../changelogs/extensions/README.md)).

## Verwandte Seiten

- [Pakete](README.md)
- [Erweiterungen](../../../funktionen/6.0/erweiterungen/README.md)
- [Schätzen und Umfang festlegen mit Erweiterungen](../../fuehrung/schaetzen-und-abgrenzen-mit-erweiterungen.md)
- [Kompatibilität und Abhängigkeiten](kompatibilitaet-und-abhaengigkeiten.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite dürfen Sie unter der GNU FDL 1.3 frei kopieren, übersetzen und verbessern.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
