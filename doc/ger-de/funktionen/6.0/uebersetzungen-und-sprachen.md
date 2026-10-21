# Übersetzungen und Sprachen

Die Sprachen, in denen Inhalte geschrieben werden (Setup > Sprachen), haben eine eigene Anleitung:
[Inhaltssprachen](../../anleitungen/inhaltssprachen.md). Jede Locale und jede Übersetzung der Administrationsoberfläche,
mit Unterstützungsstufen, Vollständigkeit und wie Sie weitere hinzufügen, ist an einer Stelle aufgeführt:
[Sprachen](../../sprachen.md).

Diese Seite ist für Administratoren, die Exponential in einer anderen Sprache als Englisch betreiben, und für
Entwickler, die ihre eigenen Texte übersetzbar machen. Am 27. und 28. September 2026 wurden die Übersetzungskataloge von
Exponential auf den neuesten Stand gebracht und für Englisch (`eng-US`) und Deutsch (`ger-DE`) vervollständigt, fast
jeder sichtbare Text der Administrationsoberfläche und der Standard-Designs wurde zu einem Übersetzungstext, und jede
mitgelieferte Locale erhielt ein korrektes HTML-Sprach-Tag.

## Was jetzt übersetzt ist

| Bereich | Ergebnis |
|---|---|
| Quellen von Kernel, `lib` und Designs | Die unübersetzte Vorlage trägt 1.193 Quelltexte, die seit ihrer letzten Erzeugung hinzugekommen waren. |
| `eng-US` | Eine neue, fertige Übersetzung mit jeder Meldung der unübersetzten Vorlage, in US-Englisch; auch für die Extensions `ezoe` und `ezjscore`. `ezpI18n` schlägt für `eng-US` Übersetzungen nach, jetzt, wo es eine hat. |
| Deutsch | Alle Meldungen des Kernels sind vorhanden und fertig, einschließlich 139, die vorhanden, aber leer waren. Die deutsche Übersetzung von `ezoe` deckt jede Meldung ab, die der Editor verwendet. Falsche Begriffe wurden korrigiert („Content structure“ war als „Navigationsteil“ übersetzt und ist „Inhalts-Struktur“). |
| Designs admin und admin3 | Jeder sichtbare Text ist ein Übersetzungstext. |
| Designs standard, base, mysite und plain | Jeder sichtbare Text ist ein Übersetzungstext. |
| Von PHP erzeugte Texte | Setup-Informationen und die Cronjob-Konsole, die Seiten der RAD-Werkzeuge (Anzahlen, Hinweise), die RAD-Assistenten und die Übersicht der Erweiterungspunkte (Laufzeitmeldungen) sowie Menüeinträge (`Name` und `Tooltip` der Reiter im oberen Menü, die Einträge Wartung, Cronjobs, Seiten vorladen und oAuth admin im Setup-Menü). |
| `ezoe` und `ezjscore` | Die Dialoge des Online-Editors übersetzen ihre übrigen Texte; `ezjscore` lädt seine eigenen Übersetzungen. |
| Neue Funktionen | Paketvergleich und -import, Shop-Dashboard, Wartung, Ladereihenfolge der Extensions, Rolleneditor, Schaltflächen des Klasseneditors, Seiten Über und Copyright, die Seite für abgelaufene Formulare. |

Etwa zwei Dutzend Extensions veröffentlichten Versionen, deren sichtbare Texte Übersetzungstexte sind, mit Deutsch.

### Eine Sprache verwenden

Legen Sie die Sprache eines Administrators in den Benutzereinstellungen fest, oder geben Sie einem Siteaccess in
`site.ini [RegionalSettings]` eine andere Locale. Um zu prüfen, was für eine Sprache noch unübersetzt ist:

```bash
php bin/php/ezchecktranslation.php ger-DE --allow-root-user    # statistics of one translation
php bin/php/ezgeneratetranslationcache.php --help
```

Am 2. Oktober 2026 meldete der erste Befehl für `ger-DE`: 12.410 Elemente, 10.614 übersetzt, 47 unübersetzt, 1.749
veraltet, 99,56 Prozent fertig (`--allow-root-user` ist nur nötig, wenn Sie als `root` arbeiten).

### Einen Text in Ihrem eigenen Code übersetzbar machen

Verwenden Sie dieselben Funktionen wie der Kernel und einen Kontext, der das Template oder die Klasse nennt:
`ezpI18n::tr( 'design/admin/shop/dashboard', 'Orders today' )` in PHP und
`{'Orders today'|i18n( 'design/admin/shop/dashboard' )}` in einem Template. Ein Text, der erst zur Laufzeit gebaut wird,
muss ein Literal in einem Aufruf von `ezpI18n::tr()` sein, damit der Extraktor ihn sehen kann.

## HTML-Sprach-Tags

Seitenlayouts geben `eZLocale::httpLocaleCode()` aus, also `[HTTP] ContentLanguage` der Locale-Datei. Zwanzig Locales
lieferten einen Code aus drei Buchstaben, eine veraltete norwegische Form oder eine falsche Region, sodass Seiten
`lang="no-bokmaal"`, `swe-SE` oder `dut-NL` ausgaben. Jede mitgelieferte Locale nennt jetzt ein gültiges Tag nach BCP 47
(Sprache nach ISO 639-1, Region nach ISO 3166, wo es eine gibt):

| Locale-Datei | Vorher | Jetzt |
|---|---|---|
| `ara-SA` | `ara-SA` | `ar-SA` |
| `cat-ES` | `cat-ES` | `ca-ES` |
| `dut-NL` | `dut-NL` | `nl-NL` |
| `ell-GR` | `ell-GR` | `el-GR` |
| `epo-EO` | `epo-EO` | `eo` |
| `esl-MX` | `mx-esl` | `es-MX` |
| `fin-FI` | `fi` | `fi-FI` |
| `heb-IL` | `heb-IL` | `he-IL` |
| `hin-IN` | `hi-HI` | `hi-IN` |
| `hun-HU` | `hun-HU` | `hu-HU` |
| `ind-ID` | `ind-ID` | `id-ID` |
| `nno-NO` | `no-nynorsk` | `nn-NO` |
| `nor-NO` | `no-bokmaal` | `nb-NO` |
| `por-MZ` | `pt-PT` | `pt-MZ` |
| `ser-SR`, `srp-RS` | `sr-SR` | `sr-RS` |
| `slk-SK` | `sk` | `sk-SK` |
| `swe-SE` | `swe-SE` | `sv-SE` |
| `tur-TR` | `tur-TR` | `tr-TR` |
| `ukr-UA` | `ua-UA` | `uk-UA` |

Der Einrichtungsassistent wählt für einen Browser, der nach `no` fragt, weiterhin Norwegisch Bokmål vor. Wenn Sie CSS oder
JavaScript haben, das auf die alten `lang`-Werte passte (zum Beispiel `[lang="no-bokmaal"]`), ändern Sie den Selektor.

## Sprachen in einer neuen Installation

Siehe [Installation mit einem Befehl](installation-mit-einem-befehl.md) für die Sprachregeln des Einrichtungsassistenten und
des Kickstarters. Kurz: Die Hauptsprache ist standardmäßig `eng-US` (die Sprache der mitgelieferten Daten); andere
Hauptsprachen installieren sich sauber; der Schritt für die Paketsprache bietet `eng-US` nicht mehr zur Zuordnung an;
Siteaccesses für zwei Locales einer Sprache erhalten verschiedene Namen; das Setup-Protokoll erfasst die Sprachen und die
Zuordnung der Paketsprachen; und eine Neuinstallation über einer bestehenden Website erhält ihre Inhaltssprachen wieder.
Eine Klasse oder ein Attribut, das angelegt wird, bevor es eine Inhaltssprache gibt, wird in der Sprache der Website
benannt.

### Ein Übersetzungs-Siteaccess, der wie die Hauptwebsite aussieht

Geben Sie ihm `[SiteAccessSettings] ExtensionSettingsSiteAccess=<main siteaccess>`. Siehe
[Ladereihenfolge der Extensions](ladereihenfolge-der-extensions.md).

## Verwandte Seiten

- [Siteaccess nach Host-Präfix und die Weiterleitung auf die Sprache](siteaccess-zuordnung-nach-host-und-sprache.md): welchen Sprach-Siteaccess die Startseite und Test-Hosts erreichen
- [Sprachen](../../sprachen.md): jede Locale (138) und Übersetzung der Administrationsoberfläche (27) mit ihrer Vollständigkeit
- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md): die Anleitung zum Hinzufügen, Anzeigen, Übersetzen, Verschieben und Entfernen der Sprachen, in denen Inhalte geschrieben werden (Setup > Sprachen)
- [Übersetzungen des Paketvergleichs](pakete-vergleichen-und-importieren.md)
- [Installation mit einem Befehl](installation-mit-einem-befehl.md) (Sprachen einer neuen Installation), [Ladereihenfolge der Extensions](ladereihenfolge-der-extensions.md) (Übersetzungs-Siteaccesses)
- [Seiten Über und Copyright](info-und-paketseiten.md)
- [Verhaltensänderungen vom September 2026](../../bc/6.0/verhaltensaenderungen-2026-09b.md)
- [Chronik, 16. bis 30. September 2026](../../geschichte/2026/2026-09b.md)
- [Changelog 6.0.15](../../../changelogs/6.0/6.0.15.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
