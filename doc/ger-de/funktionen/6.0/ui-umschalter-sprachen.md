# Die Liste der Oberflächensprachen: jede Sprache, die meistgebrauchten zuerst

Seit 6.0.15 (#516). Erweitert die Auswahl der [Oberflächensprache](ui-umschalter.md) aus #506.

Die Auswahl *Sprache der Oberfläche* in der rechten Werkzeugleiste (admin4, admin4l, das klassische Admin-Design) und
in der Seitenleiste von AdminUI bietet jede Sprache an, für die der Kernel ein Locale hat, nicht nur die wenigen mit
einer Übersetzung. Sie ist ein Listenfeld mit Überschriften:

1. **Englisch**: die englischen Locales, die Administratoren am häufigsten verwenden.
2. **Wichtige Weltsprachen**: die 19 Sprachen, die Administratoren weltweit am häufigsten brauchen, in einer
   Rangfolge (siehe unten).
3. **Regionale Varianten**: 10 regionale Formen dieser Sprachen mit eigenen Formaten.
4. **Alle Sprachen (A–Z)**: jede Sprache aus `share/locale` (etwa 130), in alphabetischer Reihenfolge.

Jeder Eintrag nennt die Sprache in ihr selbst und in der Sprache, in der die Oberfläche gerade angezeigt wird, und
gibt an, wie vollständig die Übersetzung der Oberfläche ist:

```
Deutsch (Deutschland) — German (Germany) (99%)
Français (France) — French (France) (46%)
Kiswahili (Kenya) — Swahili (Kenya) (no translation yet)
```

Eine Sprache ohne Übersetzung wird ehrlich angeboten: Ihre Texte bleiben englisch, aber Datum, Uhrzeit und Zahlen
folgen ihrem Locale (`share/locale/<locale>.ini`). Sie zu wählen ist zugleich der erste Schritt zur Übersetzung der
Oberfläche: Die Formate stimmen von Anfang an, und jede später übersetzte Meldung erscheint sofort.

Alles wird in `settings/uiswitch.ini [UISwitch_language]` eingestellt; die Einstellungen sind unter
[Einstellungen des UI-Umschalters](../../anleitungen/ui-umschalter-einstellungen.md#uiswitch_language) beschrieben.

## Das Listenfeld

Ein natives `<select>` mit einem `<optgroup>` pro Gruppe und einer Schaltfläche **Anwenden**, in einem POST-Formular an
`uiswitch/set` (Template `design/standard/templates/uiswitch/grouped.tpl`, verwendet von jedem Administrationsdesign,
die alle auf das Design standard zurückfallen; Provider ohne Gruppen behalten ihre Liste von Schaltflächen).

- **Direktsuche**: Ist das Listenfeld fokussiert, wählt die Eingabe der ersten Buchstaben eines Namens („Kis“) den
  ersten Eintrag, der damit beginnt, wie in jedem nativen Listenfeld. Pfeiltasten, Pos1, Ende, Bild auf und Bild ab
  bewegen sich durch die Liste.
- **Nichts geschieht vor „Anwenden“**: Das Bewegen durch die Liste lädt die Seite nie neu (WCAG 3.2.2, bei Eingabe).
- **Screenreader** sagen die Beschriftung (*Sprache der Oberfläche*), den Eintrag und seine Gruppe an.
- Der wirksame Eintrag ist dort ausgewählt, wo er zuerst erscheint (eine Sprache einer Gruppe steht auch in der
  A–Z-Liste).
- Es wird kein Skript benötigt und keines hinzugefügt. Die Stile sind die gemeinsamen Komponentenklassen (`a4-input`,
  `a4-btn`, `a4-stack`, `a4-meta`) mit den Tokens des Designs und ihren dunklen Werten; admin4.css ergänzt nur, dass
  das Listenfeld nie breiter als seine Spalte ist.

## Die A–Z-Reihenfolge und die Namen

- Der **eigene Name** stammt aus der Locale-Datei (`LanguageName`). Wo eine Datei nur den englischen Namen wiederholt
  („Indonesian“, „Croatian (Hrvatski)“, „Serbian (Српски)“: Das erste Wort ist das von `InternationalLanguageName`),
  liefert stattdessen die Intl-Extension von PHP den eigenen Namen („Indonesia (Indonesia)“, „Hrvatski (Hrvatska)“).
- Der **zweite Name** ist der Name der Sprache in der aktuellen Oberflächensprache des Lesers (Intl,
  `Locale::getDisplayName()`), sonst der englische Name aus der Locale-Datei. Er entfällt, wenn beide gleich sind.
- Zwei Locales mit denselben Namen erhalten ihre Codes angehängt (`[ser-SR]`, `[srp-RS]`).
- Die Reihenfolge ist die **Sortierfolge der Oberflächensprache des Lesers** (`Collator` von Intl): Č nach C im
  Tschechischen, Ä bei A im Englischen. Ohne Intl faltet die Ersatzlösung Akzente (Unicode-Zerlegung, wenn
  `Normalizer` existiert) und Groß-/Kleinschreibung und sortiert andere Schriften nach der lateinischen, in
  Codepoint-Reihenfolge.

## Abdeckung der Übersetzung

`expUISwitchLanguageCatalogue` zählt für jedes Locale mit `share/translations/<locale>/translation.ts` die
Quellmeldungen von `share/translations/untranslated/translation.ts` (Kontext, Quelltext und Kommentar), die die Datei
übersetzt: Eine Übersetzung, die leer, `unfinished` oder `obsolete` ist, zählt nicht, ebenso wenig eine Meldung, die
der Kernel nicht mehr hat. Der Prozentsatz wird abgerundet, sodass nur eine vollständige Übersetzung 100 % angibt.
Englisch (`eng-*`) ist die Sprache der Quelltexte und immer 100 %. Gezählt wird nur die Übersetzung des Kernels, nicht
die eigene der Extensions.

Das Lesen der 25 Übersetzungsdateien (je 1 bis 3 MB) dauert 2 bis 3 Sekunden, deshalb wird das Ergebnis
zwischengespeichert: im Speicher für den Prozess (Velocity-Worker) und in
`<cache directory>/uiswitch/language-catalogue.json` (und die Namen pro Lesersprache in
`language-names-<code>.json`). Beide sind über eine Signatur aus Namen, Größen und Zeiten jeder Locale- und
Übersetzungsdatei verschlüsselt, sodass eine neue oder geänderte Datei den Katalog bei der nächsten Verwendung neu
aufbaut; nichts muss geleert werden. Mit dem Cache kostet die Liste etwa 2 ms pro Seite.

Eine regionale Variante ohne eigene Übersetzung übernimmt die, die `i18n.ini [TranslationSettings]
FallbackLanguages[]` nennt: `ger-AT` und `ger-CH` zeigen die Texte von `ger-DE`, die französischsprachigen Länder
`fre-FR`, die spanischsprachigen `esl-ES`, die arabischen `ara-SA`, sobald es existiert. Der Übersetzer schlägt das
für den Kernel und für jede Extension getrennt nach, sodass `esl-MX`, das eine Kernel-Übersetzung hat, für eine
Extension ohne `esl-MX`-Übersetzung trotzdem `esl-ES` erhält. Die Abdeckung eines solchen Locales ist die der
Übersetzung, auf die es zurückfällt.
`testShippedFallbacksGiveEveryCountryOfATranslatedLanguageItsTranslation` schlägt fehl, wenn ein Land einer
übersetzten Sprache weder eine Übersetzung noch eine Ausweichsprache hat.

Die Ausweichsprachen stammen von `expI18nFallbackResolver`, der Klasse, die der Übersetzer verwendet: Ein Locale
zählt mit der ersten Übersetzung seiner gesamten **Kette** (`esl-AR -> esl-MX -> esl-ES`), einschließlich der Regeln
der Extensions (`FallbackRules[]`) und der automatischen Ausweichsprache gleicher Sprache, und mit
`FallbackMode=merge` mit allen Übersetzungen der Kette zusammen. Jeder Eintrag hat `fallback` (die gezählte
Übersetzung, '' für die eigene) und `chain`. Die Signatur des Resolvers ist Teil der des Cache (Format 5).
**Setup > Oberflächensprachen** und `./console exp:i18n fallbacks` listen dasselbe pro Locale auf, mit der Abdeckung
der Extensions: siehe [Ausweichsprachen für Übersetzungen](../../sprachen.md#13-übersetzungs-fallbacks).

## Nicht übersetzte Sprachen: standardmäßig aufgeführt

Ausgeliefert wird `ListUntranslated=enabled`, sodass jede Sprache angeboten wird und die nicht übersetzten als solche
gekennzeichnet sind. Die Gründe: Ziel des Eigentümers ist, dass die Liste Administratoren weltweit nützt, die Formate
eines Locales sind für sich allein nützlich, und die Kennzeichnung macht den Stand klar, bevor jemand wählt.
`ListUntranslated=disabled` bietet nur Englisch und die übersetzten Sprachen an (und gilt, wenn die Einstellung
fehlt, wie in #506).

## Sicherheit

- Die Zulassungsliste besteht aus dem eigenen `Locale` des Siteaccess, den Locales der Gruppen, `AllowedValues[]` und,
  mit `AllLanguages=enabled`, jeder `share/locale/<code>.ini`, deren Name ein Locale-Code ist
  (`/^[a-z]{3}-[A-Z]{2}(@[a-z0-9]{1,16})?$/`), ohne die Formatvarianten (`eng-GB@euro`), sofern keine genannt ist.
  Mit `ListUntranslated=disabled` braucht ein Locale außerdem eine Übersetzungsdatei (oder muss Englisch sein).
- Ein Wert aus der Anfrage wird unverändert mit dieser Liste verglichen (`in_array(..., true)`): Nichts aus der
  Anfrage wird normalisiert, und nichts gelangt in einen Pfad. Pfade werden nur aus Codes gebildet, die aus der
  Verzeichnisliste oder den Einstellungen gelesen wurden, nach der Musterprüfung.
- Die Cache-Dateien enthalten nur Daten (JSON), keinen Code, und eine Datei, deren Signatur nicht passt, wird
  ignoriert.

## Warum die Standard-Rangfolge so ist, wie sie ist

Die Gruppen ordnen die Sprachen danach, wie viele Administratoren eines CMS sie voraussichtlich brauchen: die Zahl der
Menschen, die die Sprache sprechen (als Erst- oder Zweitsprache), **und** wie viel der Inhalte und Geschäfte im Web
in ihr stattfindet. Keines von beiden genügt allein: Hindi und Bengalisch haben Hunderte Millionen Sprecher, aber
wenige Web-Inhalte, und viele ihrer Administratoren arbeiten auf Englisch; Niederländisch, Polnisch und Italienisch
haben weit weniger Sprecher, aber einen großen Anteil an Websites.

| Sprache | Sprecher, Millionen (L1+L2) | Websites in der Sprache | Platz |
|---|---|---|---|
| Englisch | 1.493 | 49,5 % | Gruppe Englisch |
| Spanisch | 561 | 6,1 % | 1 |
| Chinesisch (Mandarin) | 1.183 | 1,3 % | 2 |
| Französisch | 334 | 4,5 % | 3 |
| Deutsch | 133 | 5,9 % | 4 |
| Portugiesisch | 269 | 4,1 % | 5 |
| Japanisch | 126 | 4,9 % | 6 |
| Russisch | 210 | 3,4 % | 7 |
| Arabisch (Hocharabisch) | 335 (L2) | 0,6 % | 8 |
| Hindi | 611 | < 0,1 % | 9 |
| Italienisch | 66 | 2,7 % | 10 |
| Indonesisch | 255 | 1,3 % | 11 |
| Türkisch | 94 | 1,6 % | 12 |
| Koreanisch | 82 | 0,9 % | 13 |
| Niederländisch | — | 2,1 % | 14 |
| Polnisch | — | 1,8 % | 15 |
| Vietnamesisch | 97 | 0,9 % | 16 |
| Ukrainisch | — | 0,6 % | 17 |
| Persisch | 82 | 1,0 % | 18 |
| Hebräisch | 10 | 0,4 % | 19 |

Sprecher: Ethnologue, wie in der Wikipedia-Liste *List of languages by total number of speakers* zusammengefasst
(Ausgabe 2026; „—“, wo die Zusammenfassung keine Zahl hat). Websites: W3Techs, *Usage Statistics and Market Share of
Content Languages for Websites*, Oktober 2026. Die Zahlen ändern sich jedes Jahr; es kommt auf die Reihenfolge an,
nicht auf die Ziffern.

- **Englisch** erhält eine eigene Gruppe, weil es die Quellsprache ist (immer vollständig) und die Sprache, die die
  meisten Administratoren verwenden: zuerst `eng-US` (der häufigste Standard), `eng-GB` (der eigene Standard des
  Kernels), dann `eng-AU`, `eng-CA` und `eng-IN`, die größten anderen englischsprachigen Verwaltungen mit eigenen
  Datumsformaten.
- **Wichtige Weltsprachen**: die Spitze beider Listen, zuerst Spanisch bis Russisch (in beiden groß), dann Arabisch
  und Hindi (sehr viele Sprecher), dann die Sprachen mit einem großen Anteil an Websites und Unternehmenssoftware
  (Italienisch, Türkisch, Koreanisch, Niederländisch, Polnisch) und die großen Märkte für Indonesisch, Vietnamesisch,
  Ukrainisch und Persisch, dann Hebräisch (0,4 % der Websites, mehr als Thai, und die rechts-nach-links laufende
  Sprache, die seit #521 eine Übersetzung der Oberfläche und ein Administrationslayout von rechts nach links hat).
  Bengalisch, Urdu, Thai und Suaheli stehen in der A–Z-Liste: viele Sprecher, wenige Web-Inhalte (Thai 0,3 %, die
  anderen unter 0,1 %).
- **Regionale Varianten**: die regionalen Formen mit den meisten Nutzern und eigenen Formaten: mexikanisches und
  US-amerikanisches Spanisch, kanadisches und Schweizer Französisch, österreichisches und Schweizer Deutsch,
  europäisches Portugiesisch, traditionelles Chinesisch aus Taiwan und Hongkong sowie ägyptisches Arabisch, das größte
  arabischsprachige Land.

Insgesamt 34 Einträge, innerhalb der 14 bis 35, die die Liste umfassen sollte, damit die Gruppen schnell zu
überblicken bleiben. Eine Installation mit anderen Bedürfnissen legt ihre eigene Rangfolge fest: siehe
[Einstellungen des UI-Umschalters](../../anleitungen/ui-umschalter-einstellungen.md#die-gruppen-ändern).

Quellen:

- W3Techs, Usage Statistics and Market Share of Content Languages for Websites:
  https://w3techs.com/technologies/overview/content_language
- Wikipedia, List of languages by total number of speakers (nach Ethnologue):
  https://en.wikipedia.org/wiki/List_of_languages_by_total_number_of_speakers
- Ethnologue, die 200 meistgesprochenen Sprachen: https://www.ethnologue.com/insights/ethnologue200/
- Unicode CLDR, Territory-Language Information (Sprecher jeder Sprache pro Land, aus Volkszählungsdaten und
  Ethnologue): https://www.unicode.org/cldr/charts/latest/supplemental/territory_language_information.html

## Für Entwickler

- `expUISwitchLanguageProvider::groups()`: die Gruppen, jeweils `identifier`, `label` und `values`.
  `displayLabel( $value )` ist der Text eines Eintrags, `coverage( $value )` der Prozentsatz oder null, `nativeName()`
  und `readerName()` die beiden Namen. `label()` bleibt der eigene Name (die flache Liste, `uiswitch/state`).
- `expUISwitch::toolbar()` fügt dem Eintrag eines Providers `groups` hinzu, wenn der Provider `groups()` und
  `displayLabel()` hat; die Werte jeder Gruppe sind Hashes `value`, `label`, `current`, `lang`. `uiswitch/state`
  liefert sie ebenfalls. Ein Provider einer Extension erhält dasselbe Listenfeld, indem er beide Methoden
  implementiert.
- `expUISwitchLanguageCatalogue`: `entries()`, `entry( $code )`, `readerNames( $httpCode )`, `localeCodes()`,
  `coveragePercent()`, `sortValuesByLabel( $labels, $httpCode, $useIntl )`.
- `expUISwitchLanguageCatalogue( $root, $cacheDirectory, $fallbacks, $resolver )`: `$fallbacks` (eine
  `FallbackLanguages[]`-Zuordnung ohne weitere Regeln) oder `$resolver` (ein `expI18nFallbackResolver`) ersetzen die
  Einstellungen der Installation, für Tests; `resolver()` ist der verwendete, `fallbacks()` die konfigurierte
  Zuordnung, die auf eine Übersetzung zeigt.
- Tests: `tests/tests/kernel/classes/uiswitch/expUISwitchLanguageProviderTest.php` (Gruppierung, die A–Z-Reihenfolge
  mit und ohne Intl, die Zulassungsliste, nicht übersetzte Sprachen, die Abdeckung und ihr Cache), ohne Datenbank.

### Eine Ausweichregel schreiben

Eine Extension, die besser weiß, wohin ein Locale ausweichen soll (eine Regionalsprache, deren Sprecher eine andere
lesen, eine Schriftvariante, ein eigenes Locale eines Kunden), fügt eine **Regel** hinzu: eine Klasse, die
`expI18nFallbackRuleInterface` mit einer Methode implementiert.

```php
<?php
// extension/myext/classes/mylocalefallbackrule.php (in the extension's autoload)
class myLocaleFallbackRule implements expI18nFallbackRuleInterface
{
    public function fallbackFor( $locale, expI18nFallbackResolver $resolver )
    {
        // Catalan of Andorra and Valencia read the Catalan translation, if there is one
        if ( in_array( $locale, array( 'cat-AD', 'cat-VA' ), true ) && $resolver->hasTranslation( 'cat-ES' ) )
            return 'cat-ES';
        return null; // no say: the next rule is asked
    }
}
```

```ini
# extension/myext/settings/i18n.ini.append.php
[TranslationSettings]
FallbackRules[]=myLocaleFallbackRule
```

- `fallbackFor()` gibt einen Locale-Code zurück, `'untranslated'` (oder `'eng-GB'`) für die englischen Quelltexte oder
  `null`, wenn die Regel nichts zu sagen hat. Sie wird für jeden Schritt einer Kette befragt, sodass die Rückgabe von
  `cat-ES` für `cat-AD` und von etwas anderem für `cat-ES` eine längere Kette ergibt; der Resolver unterbindet Zyklen
  und lange Ketten, und `exp:i18n check` meldet sie.
- Die Reihenfolge ist fest: zuerst `FallbackLanguages[]` (was konfiguriert ist, gewinnt immer), dann die Regeln in der
  Reihenfolge von `FallbackRules[]`, dann die automatische Ausweichsprache gleicher Sprache. Eine Regel kann keine
  konfigurierte Zeile überschreiben; ein Administrator kann eine Regel immer mit einer solchen Zeile korrigieren.
- Der Resolver stellt der Regel `hasTranslation( $locale )` (eine Kernel-Übersetzung existiert), `translations()`,
  `localeExists( $locale )` (eine Datei in `share/locale`), `configured()` und `option( $name )` bereit. Die Regel wird
  einmal pro Resolver (einmal pro Prozess, solange die Einstellungen gleich bleiben) ohne Argumente erzeugt, sodass
  sie Berechnetes behalten kann; halten Sie sie günstig, denn der Übersetzer fragt einmal pro Locale und Anfrage.
- Eine Regel, die eine Ausnahme wirft, wird protokolliert und übersprungen; ein Eintrag in `FallbackRules[]`, der
  keine solche Klasse ist, wird von `exp:i18n check` und auf **Setup > Oberflächensprachen** gemeldet und ansonsten
  ignoriert.
- Testen Sie sie so, wie es `tests/tests/lib/ezi18n/expI18nFallbackResolverTest.php` tut: `new expI18nFallbackResolver(
  $map, array( 'myLocaleFallbackRule' ), array( 'repository' => ..., 'locale_directory' => ... ) )` und
  `chain()` / `explain()`, ohne Datenbank.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
