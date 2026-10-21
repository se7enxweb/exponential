# Die gruppierte Länderliste

Jede Länderauswahl von Exponential zeigt die Länder in zwei Gruppen:

1. **Häufig gewählte Länder**: eine Rangliste, die meistgenutzten zuerst (die Vereinigten Staaten, Kanada und Mexiko
   oben).
2. **Alle Länder**: jedes Land aus `settings/country.ini`, A-Z nach seinem übersetzten Namen.

Beide sind native `<optgroup>`s eines nativen `<select>`, sodass sie mit der Tastatur, Bildschirmlesern und der eigenen
Auswahl jedes Browsers funktionieren und weder Skript noch CSS brauchen.

Die Auswahllisten, die sie verwenden:

| Wo | Template | Wert der Option |
| --- | --- | --- |
| Länderattribut (`ezcountry`), Bearbeiten von Inhalten | `content/datatype/edit/ezcountry.tpl` (standard, AdminUI) | ISO-Code alpha-2 |
| Länderattribut, Informationssammlung | `content/datatype/collect/ezcountry.tpl` | Code |
| Länderattribut, Bearbeiten der Klasse (Standardauswahl) | `class/datatype/edit/ezcountry.tpl` (standard, AdminUI) | Code |
| Kontoformular des Shops `shop/userregister` (standard, admin, admin4, admin4l, ezwebin, ezdemo, media) | `shop/country/edit.tpl` | Name |
| MwSt.-Regeln des Shops, Werkzeugleiste für das Land des Benutzers | `shop/country/edit.tpl` mit `use_country_code` | Code oder Name |

Sie alle zeichnen ihre Optionen mit einem Template, `design:country/options.tpl`, aus einem Fetch,
`fetch( 'content', 'country_groups' )` (Klasse `expCountryGroups`). Die Admin-Designs und ezwebin, ezdemo haben keine
eigene Kopie von `shop/country/edit.tpl` und verwenden die Standardkopie; das Media-Design behält seine eigene Kopie nur,
um `form-control` hinzuzufügen.

## Was gespeichert wird

An den gespeicherten Daten ändert sich nichts, daher ist keine Migration nötig:

- Das Länderattribut sendet und speichert weiterhin ISO-Codes alpha-2 (`data_text`, durch Kommas getrennt). Werte, die
  alte Versionen als Ländernamen gespeichert haben, werden weiterhin ausgewählt.
- Das Kontoformular des Shops sendet weiterhin den Namen des Landes, den das Konto-XML der Bestellung wie bisher
  speichert; mit `use_country_code` (MwSt.-Regeln) sendet es den Code.

## Automatisches Ausfüllen

Der Fehler, den das behebt: Ein Browser, der das Formular aus einer Adresse in den Vereinigten Staaten ausfüllte, wählte
*United States Minor Outlying Islands*. Wenn ein Browser eine Option nicht genau zuordnen kann, nimmt er die erste Option,
deren Text mit dem Ländernamen des Profils beginnt („United States“), und A-Z sind das die Minor Outlying Islands.

Jetzt:

- Die Vereinigten Staaten sind die erste Option der Liste, sodass die erste Option, die mit „United States“ beginnt, die
  richtige ist. Dasselbe gilt für „United States of America“, „US“ und „USA“ (ein Code wird mit dem Wert der Option
  verglichen).
- Die Auswahllisten sagen, welches Adressfeld sie sind: `autocomplete="country"`, wo der Wert ein Code ist (das
  Länderattribut), `autocomplete="country-name"`, wo er ein Name ist (der Shop). Das Kontoformular des Media-Designs
  benennt außerdem die übrigen Felder (`given-name`, `family-name`, `email`, `address-line1`, `address-line2`,
  `postal-code`, `address-level2`, `address-level1`, `tel`), sodass ein Browser die ganze Adresse ausfüllt.
- Die Auswahl des Shops beginnt mit einer leeren Option *Land wählen*, wenn noch kein Land bekannt ist, statt still das
  erste Land vorzuwählen. Das hält auch die Tastatursuche richtig: Die Eingabe von „United States“ ab der leeren Option
  landet auf den eingereihten Vereinigten Staaten; die Eingabe ab vorgewählten Vereinigten Staaten ging weiter zu den
  Minor Outlying Islands.
- Namen werden standardmäßig nicht mehr auf 20 Zeichen gekürzt (`max_len`): „United States of A...“ ist nicht der Name,
  nach dem das automatische Ausfüllen sucht.

**Wiederholen oder weglassen.** Ein Land der Ranggruppe wird in einer einfachen Auswahl in *Alle Länder* erneut
aufgeführt, sodass die Liste A-Z vollständig bleibt; nur seine erste Option lässt sich auswählen, sodass ein gespeicherter
Wert einmal zurückkommt. Automatisches Ausfüllen und Tastatursuche landen beide auf der ersten, eingereihten Option
(geprüft in Chromium und WebKit). Eine Auswahl, die mehrere Länder erlaubt, führt jedes Land einmal auf: Dort lässt die
Gruppe A-Z die eingereihten Länder weg, weil zwei Optionen desselben Landes getrennt ausgewählt werden könnten.

## Einstellungen: `settings/countrygroups.ini`

```ini
[CountrySettings]
GroupList[]
GroupList[]=common
RepeatInAllCountries=enabled
GroupName_common=Most used countries
GroupCountries_common[]
GroupCountries_common[]=US
GroupCountries_common[]=CA
...
```

- `GroupList[]`: die Gruppen vor *Alle Länder*, in dieser Reihenfolge. Eine leere Liste lässt nur *Alle Länder* übrig.
- `GroupName_<group>`: die Beschriftung, übersetzt im Kontext `kernel/country` (eng-US und ger-DE werden mitgeliefert).
- `GroupCountries_<group>[]`: ISO-3166-1-Codes alpha-2, in Rangfolge. Ein Code, der in `country.ini` fehlt, wird
  übersprungen; ein Code, der schon in einer früheren Gruppe aufgeführt ist, wird übersprungen.
- `RepeatInAllCountries`: `enabled` (Standard) wiederholt die eingereihten Länder in *Alle Länder* einer einfachen
  Auswahl; `disabled` lässt sie auch dort weg.

Die Einstellungen stehen nicht in `country.ini`, weil jede Gruppe dieser Datei als Land gelesen wird.

Überschreiben Sie pro Website in `settings/override/countrygroups.ini.append.php` oder pro Siteaccess, zum Beispiel für
einen Shop, der vor allem in deutschsprachige Länder verkauft:

```ini
[CountrySettings]
GroupCountries_common[]
GroupCountries_common[]=DE
GroupCountries_common[]=AT
GroupCountries_common[]=CH
```

## Die mitgelieferte Rangfolge

37 Länder, in der Reihenfolge, die der Produktverantwortliche festgelegt hat: Nordamerika, Südamerika, Europa, das
Vereinigte Königreich, Asien, Neuseeland, Australien, Afrika. Innerhalb jeder Region sind die Länder nach erwarteter
Nutzung gereiht: Online-Käufer und registrierte Benutzer pro Land (Bevölkerung mal Nutzung von Internet und E-Commerce),
was die größten Märkte nach vorn stellt.

| Region | Länder, in Reihenfolge |
| --- | --- |
| Nordamerika | Vereinigte Staaten, Kanada, Mexiko |
| Südamerika | Brasilien, Argentinien, Kolumbien, Chile, Peru |
| Europa | Deutschland, Frankreich, Italien, Spanien, Niederlande, Polen, Schweden, Belgien, Schweiz, Österreich, Irland, Portugal, Dänemark, Norwegen, Finnland |
| Vereinigtes Königreich | Vereinigtes Königreich |
| Asien | Japan, China, Indien, Südkorea, Singapur, Vereinigte Arabische Emirate |
| Ozeanien | Neuseeland, Australien |
| Afrika | Südafrika, Nigeria, Ägypten, Kenia, Marokko |

## Sortierung

*Alle Länder* wird nach dem übersetzten Namen in der Kollation der Locale des Lesers sortiert (`Collator` der Erweiterung
intl, sekundäre Stärke: Akzente zählen nach den Buchstaben, Groß- und Kleinschreibung nicht), sodass deutsche Leser
*Österreich* bei O finden und schwedische Leser nach Z. Ohne intl werden die Namen ohne Akzente und mit vereinheitlichter
Schreibung verglichen („Åland“ wird bei A einsortiert).

## Templates

```
{include uri='design:country/options.tpl'
         country_value_field='Alpha2'           {* or 'Name' *}
         country_selected=array( 'US' )         {* codes or names *}
         country_selected_map=$hash_keyed_by_code
         country_multiple=false()
         country_max_len=0}
```

Der Aufrufer zeichnet das `<select>` (Name, ID, sein `<label>`, `autocomplete`) und bindet die Optionen darin ein.

`fetch( 'content', 'country_groups', hash( 'repeat', true() ) )` gibt die Gruppen zurück: eine Liste von
`hash( 'identifier', ..., 'label', ..., 'countries', array( hash( 'Name', ..., 'Alpha2', ..., 'Alpha3', ..., 'Repeated', true() ) ) )`.

## Tests

`tests/tests/kernel/classes/datatypes/expCountryGroupsTest.php` (ohne Datenbank): die Rangfolge, unbekannte und
wiederholte Codes, die Reihenfolge A-Z mit und ohne intl, Wiederholungen und ihre Markierungen, die mitgelieferte
Rangfolge gegen `country.ini`, die erste Option, die auf ein Profil in den Vereinigten Staaten passt, und die Werte der
Optionen der Templates.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
