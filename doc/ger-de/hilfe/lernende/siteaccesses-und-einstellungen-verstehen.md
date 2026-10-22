---
title: Siteaccesses und Einstellungen verstehen
audience: student
category: learning
order: 19
summary: Eine Erklärung für Lernende zu Siteaccesses (site, admin, editor und weitere) und INI-Einstellungen - was ein Siteaccess ist, die Einstellungsdateien und ihre Overrides .ini.append.php, die Reihenfolge, in der Geltungsbereiche gewinnen, wie Sie die Installation mit exp:ini where befragen, und die Fehler, die Lernende am häufigsten machen.
keywords: siteaccess, einstellungen, INI, site.ini, ini.append.php, override, geltungsbereich, exp:ini, where, design, konfiguration, erklärung, lernende, settings, scope, configuration, explanation, learner
type: help
---
# Siteaccesses und Einstellungen verstehen

Fast alles, was Exponential tut, wird durch eine Einstellung entschieden, und jede Anfrage kommt über einen
Siteaccess herein. Wenn eine Änderung „keine Wirkung hat“, ist meist eine dieser beiden Ideen der Grund. Diese Seite
erklärt beide für Lernende und zeigt, wie Sie die Installation selbst befragen.

## Inhalt

1. [Siteaccesses](#siteaccesses)
2. [Einstellungsdateien](#einstellungsdateien)
3. [Welche Datei gewinnt](#welche-datei-gewinnt)
4. [Die Installation befragen](#die-installation-befragen)
5. [Häufige Fehler](#häufige-fehler)
6. [Übungen](#übungen)

## Siteaccesses

Ein **Siteaccess** ist ein Zugangsweg zur Website (über URL, Host oder Port) mit eigenen Einstellungen und eigenem
Design ([Glossar](../../glossar.md#s)). Jede Installation hat drei, immer `site`, `admin` und `editor` genannt
([Exponential 6.0 installieren](../../INSTALL.md#1-was-sie-erhalten-und-der-schnellstart)):

| Siteaccess | Für | Mit `exp:install` in der Voreinstellung |
|---|---|---|
| `site` | Besucher | der Pfad `/site` |
| `admin` | Administratoren | der Pfad `/admin` |
| `editor` | Redakteure: die Administrationsoberfläche nur für Inhalte, ohne Setup, Design und Entwicklerwerkzeuge | der Pfad `/editor` |

Ein Site-Paket kann weitere hinzufügen. Das Standardpaket `sevenx_multisite` fügt für seine zweite Website `bold`
und `bold_ger` hinzu ([Site-Pakete für Lernende](site-pakete-fuer-lernende.md)). Eine mehrsprachige Website hat
meist einen öffentlichen Siteaccess pro Sprache ([Inhaltssprachen](../../anleitungen/inhaltssprachen.md#5-eine-sprache-anzeigen-sitelanguagelist-und-siteaccesses)).

Jeder Siteaccess hat seinen eigenen Ordner mit Einstellungen, `settings/siteaccess/<name>/`.

## Einstellungsdateien

- Die Voreinstellungen stehen in `settings/*.ini`: `site.ini`, `content.ini`, `design.ini`, `override.ini` und viele
  mehr.
- Sie **bearbeiten diese Dateien niemals**: Sie werden bei jedem Upgrade ersetzt. Sie legen Dateien mit demselben
  Namen und der Endung `.ini.append.php` an einem Ort an, der danach gelesen wird.
- Eine Einstellung wird nach Datei, Block und Variable benannt: `site.ini [SiteSettings] SiteName`, oder für den
  Einstellungsbefehl `site.ini/SiteSettings/SiteName`.

Quelle: [Wie INI-Einstellungen funktionieren](../erweiterungsentwicklung/teil-2-builder/wie-ini-einstellungen-funktionieren.md).

## Welche Datei gewinnt

Nach der Standarddatei liest der Kernel die anderen Orte in dieser Reihenfolge, und **eine spätere Datei gewinnt**:

1. die Siteaccess-Ordner innerhalb von Erweiterungen,
2. `settings/siteaccess/<siteaccess>/`,
3. `extension/<ext>/settings/` jeder aktiven Erweiterung,
4. `settings/override/`.

Quelle: [Geltungsbereiche von Einstellungen und die Reihenfolge, in der sie gewinnen](../erweiterungsentwicklung/teil-2-builder/einstellungsebenen-und-ihre-reihenfolge.md#die-reihenfolge).
Beachten Sie die Überraschung in Schritt 3: Ein Wert, den eine Erweiterung mitbringt, schlägt denselben Wert in
`settings/siteaccess/<sa>/`.

## Die Installation befragen

Sie müssen die Reihenfolge nicht von Hand ermitteln. `exp:ini where` zeigt jede Datei, die einen Wert setzt, in
Ladereihenfolge, und den wirksamen Wert ([Templates und Design](../../anleitungen/templates-und-design.md#2-herausfinden-welches-design-sie-verwenden)):

```bash
php bin/php/console exp:ini where site/DesignSettings/SiteDesign site
```

Die Ausgabe listet die Dateien auf, zum Beispiel `settings/site.ini` mit `SiteDesign=admin` und dann die Datei des
Siteaccess, und endet mit `In effect:` und dem verwendeten Design. Ersetzen Sie `site` am Ende durch den Siteaccess,
den Sie untersuchen. Der Befehl kann Werte auch lesen, setzen und hinzufügen; siehe [exp:ini](../../funktionen/6.0/exp-ini-befehl.md)
und [Der Befehl exp:ini](../erweiterungsentwicklung/teil-2-builder/der-befehl-exp-ini.md).

Nachdem Sie eine Einstellung geändert haben, kann der INI-Cache noch den alten Wert enthalten. Leeren Sie ihn wie in
[Caches verstehen](caches-verstehen.md) beschrieben.

## Häufige Fehler

| Fehler | Was passiert | Stattdessen |
|---|---|---|
| `settings/site.ini` bearbeiten | Beim nächsten Upgrade verloren | Eine Datei `.ini.append.php` in `settings/override/` oder einem Siteaccess-Ordner verwenden |
| Den Siteaccess `site` ändern und `bold` betrachten | Keine Änderung dort, wo Sie hinsehen | Den Siteaccess der Seite finden, die Sie betrachten |
| Einen Wert in `settings/siteaccess/<sa>/` setzen, den auch eine Erweiterung setzt | Der Wert der Erweiterung gewinnt | `settings/override/` oder der eigene Siteaccess-Ordner der Erweiterung |
| `true` schreiben, wo die Voreinstellung `enabled` verwendet | Der Schalter funktioniert nicht | Die Form verwenden, die die Standarddatei verwendet |
| Den Cache vergessen | Der alte Wert bleibt | Den INI-Cache leeren |
| Ein Passwort in eine Einstellungsdatei schreiben, die Sie committen | Das Passwort wird veröffentlicht | `settings/override/` und seine Geheimnisse aus öffentlichen Repositorys heraushalten |

## Übungen

1. Listen Sie die Ordner in `settings/siteaccess/` Ihrer Lernwebsite auf. Welche Siteaccesses gibt es?
2. Führen Sie `exp:ini where site/DesignSettings/SiteDesign` für zwei davon aus. Verwenden sie dasselbe Design?
3. Finden Sie `SiteLanguageList` für den Siteaccess `site` mit
   `exp:ini where site/RegionalSettings/SiteLanguageList site`. Welche Sprachen zeigt er, in welcher Reihenfolge?
4. Erklären Sie einer anderen lernenden Person in drei Sätzen, warum ein Wert aus einer Erweiterung einen Wert des
   Siteaccess schlagen kann.

Der Kurs der Trainer [Multisite und Site-Pakete](../trainer/multisite-und-pakete/README.md) und die Kapitel für
Website-Bauer [Siteaccess-Einstellungen](../erweiterungsentwicklung/teil-2-builder/siteaccess-einstellungen.md) gehen weiter.

## Verwandte Seiten

- [Caches verstehen](caches-verstehen.md)
- [Lernpfad: Administrator](lernpfad-administrator.md)
- [Das Inhaltsmodell verstehen](das-inhaltsmodell-verstehen.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und
  verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
