# Inhaltssprachen: hinzufügen, anzeigen, übersetzen und entfernen

Diese Anleitung erklärt alles über die Sprachen, in denen Inhalte geschrieben werden: was eine Inhaltssprache ist und
wie sie sich von der Sprache der Oberfläche unterscheidet, wie Sie eine hinzufügen, wie ein Siteaccess sie anzeigt,
wie Redakteure in sie übersetzen, was „immer verfügbar“ und „Hauptsprache“ bedeuten, wie Sie Inhalte von einer
Sprache in eine andere verschieben und wie Sie eine Übersetzung oder eine ganze Sprache entfernen, ohne etwas zu
verlieren, das Sie behalten wollten. Am Ende stehen vollständige Abläufe, vom Hinzufügen von Deutsch zu einer
englischen Site bis zum Ausmustern einer versehentlich verwendeten Sprache, sowie die Probleme, auf die man stößt.

Sie richtet sich an Administratoren und Site-Entwickler. Lesen Sie zuerst die Abschnitte 1 und 2; danach steht jeder
Abschnitt für sich. Jede Einstellung, jede Schaltfläche und jeder Befehl unten wurde am Code von Exponential 6.0
geprüft, und jede genannte Zahl ist echt: Sie wurde am 5. Oktober 2026 auf dem Demonstrationsserver abgelesen. Nichts
in dieser Anleitung wurde durch Hinzufügen oder Entfernen einer Sprache auf diesem Server ausprobiert, denn das
Entfernen einer Sprache übt man nicht an Inhalten, die Menschen nutzen; Abschnitt 9 zeigt, wie Sie es gefahrlos
ausprobieren.

[Anleitungen](README.md) · Alle Sprachen im Überblick: [Sprachen](../../eng-us/languages.md) · Funktionsreferenz:
[Übersetzungen und Sprachen](../../eng-us/features/6.0/translations-and-languages.md)
· Im Installationshandbuch: [10.14 Mehrsprachige Sites](../installation/10-nach-der-installation.md#1014-mehrsprachige-sites)

## Kurz gesagt

- **Setup > Sprachen** (`content/translations`) listet die Inhaltssprachen auf: für jede, wie viele Objekte und
  Klassen eine Übersetzung in ihr haben, welche Siteaccesses sie zeigen, ob sie entfernt werden kann, und einen
  Hinweis, wenn nur wenige Objekte sie nutzen oder kein Siteaccess sie führt.
- Eine Inhaltssprache ist nicht die Oberflächensprache. Inhaltssprachen enthalten Ihre Artikel; die
  Oberflächensprache (`Locale`) und die `.ts`-Übersetzungsdateien bestimmen die Wörter auf Schaltflächen und in Menüs.
- Das Hinzufügen einer Sprache übersetzt nichts und ändert keinen Siteaccess. Übersetzen Sie Inhalte im Editor und
  tragen Sie die Sprache dann in `site.ini [RegionalSettings] SiteLanguageList` jedes Siteaccess ein, der sie zeigen
  soll.
- Jeder Siteaccess zeigt ein Objekt in der ersten Sprache seiner `SiteLanguageList`, die das Objekt hat. Objekte in
  keiner davon sind verborgen, es sei denn, sie sind **immer verfügbar**.
- Eine Sprache kann nur entfernt werden, wenn kein Objekt und keine Klasse eine Übersetzung in ihr hat, ungespeicherte
  Klassenbearbeitungen eingeschlossen. Alles andere lehnt der Kernel ab, daher löscht das Entfernen einer Sprache nie
  Inhalte.
- Das Entfernen der **Übersetzung** eines Objekts löscht dagegen Inhalte, und zwar endgültig: alle seine Felder in
  dieser Sprache, in jeder Version. Übersetzen Sie zuerst, machen Sie die neue Sprache zur Hauptsprache und entfernen
  Sie dann die alte Übersetzung.
- Eine Installation fasst höchstens 62 Inhaltssprachen (unter 64-Bit-PHP). Jede ist ein Bit; ein Objekt hält alle
  seine Übersetzungen in einer Zahl fest.

## Inhalt

- [1. Inhaltssprachen und Oberflächensprachen](#1-inhaltssprachen-und-oberflächensprachen)
- [2. Wie Sprachen gespeichert werden](#2-wie-sprachen-gespeichert-werden)
- [3. Die Seite der Inhaltssprachen](#3-die-seite-der-inhaltssprachen)
- [4. Eine Sprache hinzufügen](#4-eine-sprache-hinzufügen)
- [5. Eine Sprache anzeigen: SiteLanguageList und Siteaccesses](#5-eine-sprache-anzeigen-sitelanguagelist-und-siteaccesses)
- [6. Hauptsprache und immer verfügbar](#6-hauptsprache-und-immer-verfügbar)
- [7. Inhalte im Editor übersetzen](#7-inhalte-im-editor-übersetzen)
- [8. Inhalte von einer Sprache in eine andere verschieben](#8-inhalte-von-einer-sprache-in-eine-andere-verschieben)
- [9. Übersetzungen und Sprachen sicher entfernen](#9-übersetzungen-und-sprachen-sicher-entfernen)
- [10. Befehle für die Shell](#10-befehle-für-die-shell)
- [11. Abläufe Schritt für Schritt](#11-abläufe-schritt-für-schritt)
- [12. Fehlerbehebung](#12-fehlerbehebung)
- [Quellen](#quellen)

## 1. Inhaltssprachen und Oberflächensprachen

Exponential kennt zwei Arten von Sprache, und sie werden an verschiedenen Stellen eingestellt.

| | Inhaltssprache | Oberflächensprache |
|---|---|---|
| Was sie ist | Eine Sprache, in der Objekte (Artikel, Ordner, Produkte, Benutzer) geschrieben werden können | Die Sprache der Texte der Administration und der Templates: Schaltflächen, Menüs, Meldungen |
| Wo die Texte liegen | In der Datenbank, eine Übersetzung je Objekt und Sprache | In Übersetzungsdateien: `share/translations/<locale>/translation.ts` und der Ordner `translations/` jeder Extension |
| Wo sie verwaltet wird | **Setup > Sprachen** (`content/translations`), diese Anleitung | `site.ini [RegionalSettings] Locale` je Siteaccess und die Benutzereinstellung eines Administrators |
| Was sie für eine Seite auswählt | `SiteLanguageList` des Siteaccess | `Locale` des Siteaccess (mit `TextTranslation=enabled`) |
| Wie Sie sie prüfen | Die Seite der Inhaltssprachen | `php bin/php/ezchecktranslation.php ger-DE` |

Die beiden sind voneinander unabhängig. Ein Siteaccess kann deutsche Inhalte mit englischer Oberfläche zeigen und
umgekehrt. Auf dem Demonstrationsserver tut der Test-Siteaccess `admintest_ger` genau das: Seine `Locale` ist
`ger-DE`, die Administration spricht also Deutsch, während seine `SiteLanguageList` `eng-US` ist, die Inhalte, die er
auflistet, also die englischen sind.

Diese Anleitung behandelt Inhaltssprachen. Zur Oberfläche siehe
[Übersetzungen und Sprachen](../../eng-us/features/6.0/translations-and-languages.md).

## 2. Wie Sprachen gespeichert werden

Eine Inhaltssprache ist eine Zeile der Tabelle `ezcontent_language`: eine ID, ein Locale-Code wie `ger-DE` und ein
Name.

**Die ID ist ein einzelnes Bit.** Die erste Sprache erhält die ID 2, die nächste 4, dann 8, 16 und so weiter; die ID 1
ist reserviert. Wird eine Sprache hinzugefügt, erhält sie die niedrigste Zweierpotenz, die keine andere Sprache hat.
Der Demonstrationsserver hat:

| ID | Bit | Locale | Name |
|---|---|---|---|
| 2 | 1 | `eng-US` | English (American) |
| 4 | 2 | `ger-DE` | German |

Bis zum 5. Oktober 2026 hatte er außerdem `eng-GB` (English (United Kingdom)) mit der ID 8, Bit 3; sie wurde an
diesem Tag entfernt (Abschnitt 3). Eine als Nächstes hinzugefügte Sprache würde wieder die ID 8 erhalten, das
niedrigste freie Bit.

**Ein Objekt hält seine Sprachen in einer Zahl fest**, seiner *Sprachmaske*: der Summe der IDs der Sprachen, in denen
es eine Übersetzung hat. Ein Objekt in amerikanischem Englisch und Deutsch hat die Maske 2 + 4 = 6. Bit 0 (der Wert 1)
ist das Kennzeichen *immer verfügbar* (Abschnitt 6), daher hat ein immer verfügbares Objekt nur in amerikanischem
Englisch die Maske 3. Auch eine Klasse hat eine Sprachmaske, für die Übersetzungen ihres Namens und ihrer
Beschreibung.

**Jedes Objekt hat eine Hauptsprache** (seine *Ausgangssprache*): die, in der es angelegt wurde, oder die, die ein
Redakteur später gewählt hat. Sie wird gezeigt, wo es keine bessere Übersetzung gibt, und sie kann nicht aus dem
Objekt entfernt werden, solange sie die Hauptsprache ist.

**Höchstens 62 Sprachen** passen hinein: Die Ganzzahlen von PHP haben 64 Bit, eines davon ist das Vorzeichen und
eines das Kennzeichen für „immer verfügbar“ (`eZContentLanguage::maxCount()`, 30 unter 32-Bit-PHP). Die Seite sagt
Ihnen, wie viele noch hinzugefügt werden können.

Weil Sprachen Bits sind, beantwortet der Kernel die Frage „Welche Objekte sind auf Deutsch?“ mit einem Bit-Test je
Zeile (`language_mask & 4 > 0`), auf jeder Datenbank-Engine, die er unterstützt.

## 3. Die Seite der Inhaltssprachen

Öffnen Sie **Setup > Sprachen** in der Administration (`/content/translations`). Die Seite erfordert die Richtlinie
`content/translations`; Administratoren haben sie.

### Die Übersicht

Oben stehen fünf Kennzahlen:

- **Inhaltssprachen**: wie viele es gibt.
- **Können noch hinzugefügt werden**: wie viele der 62 noch frei sind.
- **Brauchen einen Blick**: Sprachen, die nur wenige Objekte nutzen (weniger als 10) oder die kein Siteaccess führt.
  Das sind die Fälle, die einen zweiten Blick lohnen; Abschnitt 8 erklärt, was dann zu tun ist.
- **Unbenutzt, entfernbar**: Sprachen, die kein Objekt und keine Klasse hat.
- **Standard für neue Inhalte in diesem Siteaccess**: `ContentObjectLocale` des Siteaccess, in dem Sie sich befinden,
  die Sprache, in der neue Inhalte angelegt werden, wenn nichts anderes gewählt ist.

### Eine Karte je Sprache

Jede Sprache hat eine Karte mit Flagge, Name und Locale sowie Plaketten:

| Plakette | Bedeutung |
|---|---|
| Standard für neue Inhalte | Sie ist `ContentObjectLocale` dieses Siteaccess |
| Zuerst gezeigt von N | N Siteaccesses führen sie an erster Stelle ihrer `SiteLanguageList`: Sie ist deren Hauptsprache |
| In keiner SiteLanguageList | Kein Siteaccess führt sie, daher sieht kein Besucher Inhalte in ihr (außer immer verfügbaren Objekten) |
| Nur N Objekte | Weniger als 10 Objekte haben eine Übersetzung in ihr |
| In Gebrauch / Unbenutzt | Ob irgendein Objekt oder eine Klasse eine Übersetzung in ihr hat |

Unter den Plaketten stehen vier Angaben: die Zahl der **Objekte** mit einer Übersetzung in der Sprache und davon, wie
viele sie als Hauptsprache haben und wie viele nichts anderes; die Zahl der **Klassen** mit einem Namen in ihr; die
**Sprach-ID** und ihr Bit; und die **Siteaccesses**, die sie zeigen, wobei die, die sie zuerst zeigen, hervorgehoben
sind.

Braucht eine Sprache einen Blick, sagt die Karte, warum und was zu tun ist, mit einem Link auf den passenden Teil der
eigenen Seite der Sprache. Die letzte Zeile jeder Karte sagt, ob die Sprache entfernt werden kann, und falls nicht,
was im Weg steht.

Das Kontrollkästchen **Zum Entfernen auswählen** ist nur bei Sprachen aktiv, die nichts nutzt. **Ausgewählte
entfernen** entfernt die ausgewählten Sprachen nach einer Rückfrage (bei eingeschaltetem JavaScript), die die
Siteaccesses nennt, die sie noch führen. **Sprache hinzufügen** öffnet das Formular aus Abschnitt 4.

### Siteaccesses und ihre Sprachen

Unter der Liste steht jeder Siteaccess aus `site.ini [SiteAccessSettings] AvailableSiteAccessList` mit seiner
`SiteLanguageList` in Reihenfolge, so wie dieser Siteaccess sie mit seinen eigenen Overrides liest. Die erste Sprache
jedes Siteaccess ist hervorgehoben. Eine Locale, die ein Siteaccess führt, die aber keine Inhaltssprache ist, ist rot
markiert: Dieser Siteaccess verlangt eine Sprache, die es nicht gibt, und zeigt in ihr nichts.

Nur Siteaccesses aus `AvailableSiteAccessList` werden gezählt. Ein Einstellungsordner `settings/siteaccess/<name>/`,
der dort nicht eingetragen ist, liefert keine Seite aus, daher zeigt auch seine `SiteLanguageList` nichts.

### Die Seite einer Sprache

Klicken Sie auf den Namen einer Sprache (`/content/translations/<id>`). Die Seite zeigt dieselben Kennzahlen größer,
die Siteaccesses, die die Sprache zeigen, einen Abschnitt **Diese Sprache entfernen**, der sagt, ob sie entfernt
werden kann, und falls nicht, die Schritte, um sie außer Gebrauch zu nehmen, dann drei aufklappbare Abschnitte:

- **Objekte mit einer Übersetzung darin**: bis zu 25, die zuletzt geänderten zuerst, jeweils mit Klasse,
  Hauptsprache, allen Sprachen und der Angabe, ob das Objekt immer verfügbar ist. Die Objektnamen verlinken auf die
  Objekte.
- **Klassen mit einem Namen darin**: jede Klasse, die die Sprache hat, einschließlich begonnener und nie gespeicherter
  Klassenbearbeitungen (markiert als *ungespeicherte Bearbeitung*); auch diese zählen für den Kernel.
- **Die Locale**: wie Datum, Zahlen und Geldbeträge in dieser Sprache geschrieben werden, aus
  `share/locale/<code>.ini`.

Die Schaltfläche **Entfernen** auf dieser Seite ist deaktiviert, solange irgendetwas die Sprache nutzt, und ebenso
für die Sprache, in der die Administrationsoberfläche selbst läuft.

### Durchgerechnetes Beispiel: die Übersicht auf dem Demonstrationsserver

Am 5. Oktober 2026 zeigte die Seite nach der unten beschriebenen Bereinigung:

| Sprache | Objekte (Haupt-, nur) | Klassen | Gezeigt von | Entfernbar |
|---|---|---|---|---|
| English (American), `eng-US` | 367 (367, 324) | 81 (74 gespeichert, 7 ungespeicherte Bearbeitungen) | 14 Siteaccesses, zuerst in 13 | Nein |
| German, `ger-DE` | 53 (10, 10) | 6 | `bold_ger`, dort zuerst | Nein |

Früher an diesem Tag gab es eine dritte Karte, English (United Kingdom), `eng-GB`: genau der Fall, für den diese Seite
neu gestaltet wurde, damit er sofort auffällt. Die Klassenpakete der Newsletter-Extension nannten `eng-GB`, daher
hatte ihr Import die Sprache angelegt (Abschnitt 12), und sechs Klassen hatten einen Namen in ihr, während kein
Siteaccess aus `AvailableSiteAccessList` sie führte (nur der ungenutzte Einstellungsordner `settings/siteaccess/eng`
tat das). Ihre Karte zeigte *In keiner SiteLanguageList* und *Von keinem Siteaccess gezeigt* und verlinkte auf die
sechs Klassen. Nachdem deren `eng-GB`-Übersetzungen nach `eng-US` verschoben und die Reste aus Abschnitt 9 bereinigt
waren, zeigte die Karte *Unbenutzt*, und die Sprache wurde entfernt.

## 4. Eine Sprache hinzufügen

1. Öffnen Sie **Setup > Sprachen** und klicken Sie auf **Sprache hinzufügen**.
2. Geben Sie unter **Sprache und Land** einen Teil des Namens, den Code oder das Land in **Sprache suchen** ein
   (`german`, `ger`, `austria`, `deutsch` funktionieren alle); die Liste wird beim Tippen enger, und die Eingabetaste
   wählt den ersten Treffer. Ohne JavaScript ist die Liste ein einfaches Listenfeld, nach Namen sortiert. Die Liste
   enthält die Locales aus `share/locale` ohne ihre Varianten (132 bei einer Standardinstallation seit 6.0.15; die
   vollständige Liste mit Namen in der Landessprache, Regionen und Unterstützungsgrad steht unter
   [Sprachen](../../eng-us/languages.md)); bereits hinzugefügte Sprachen werden angezeigt, können aber nicht gewählt werden.
   Jeder Eintrag lautet englischer Name · Name in der Landessprache · Land · Code, zum Beispiel
   `Persian · فارسی · Iran · fas-IR`; das Land entfällt, wo der Name es bereits nennt
   (`German (Austria) · Deutsch (Österreich) · ger-AT`), und die Suche findet es trotzdem.
3. Klicken Sie auf **Sprache hinzufügen**.

Die Seite kehrt mit der Meldung *German (ger-DE) wurde hinzugefügt* zur Übersicht zurück. Die Sprache erhält das
nächste freie Bit, ihr Name wird aus der Locale übernommen (`German`), und der Inhalts-View-Cache wird geleert, weil
sich Listen ändern, die nach Sprache filtern.

### Eine eigene Sprache

Für eine Sprache, die nicht in der Liste steht, wählen Sie **Eigene** oben in der Liste und füllen aus:

- **Name der spezifischen Übersetzung**: der Name, den die Administration anzeigt, etwa `Deutsch (Schweiz)`.
- **Örtlichkeit der spezifischen Übersetzung**: drei Buchstaben für die Sprache, ein Bindestrich, zwei für das
  Land: `ger-CH`.

Für diesen Code muss eine Datei `share/locale/<code>.ini` vorhanden sein, denn die Locale bestimmt auch, wie Datum
und Zahlen geschrieben werden. Fehlt sie, kommt das Formular mit *In share/locale gibt es keine Locale ger-CH* zurück
und behält Ihre Eingaben. Kopieren Sie die nächstliegende Locale-Datei (`ger-DE.ini`) unter dem neuen Namen, passen
Sie sie an und versuchen Sie es erneut. Ein Code, der nicht die Form `xxx-YY` hat, kommt mit einer Erklärung der
Form zurück.

### Was das Hinzufügen nicht tut

Es übersetzt nichts und ändert keinen Siteaccess: Noch sieht kein Besucher etwas Neues. Das Formular sagt das und
nennt die nächsten Schritte: Inhalte übersetzen (Abschnitt 7) und die Sprache in `SiteLanguageList` eintragen
(Abschnitt 5).

Das Hinzufügen einer bereits vorhandenen Sprache bewirkt nichts und meldet *German (ger-DE) ist bereits eine
Inhaltssprache*. Sind alle 62 Bits belegt, sagt die Seite das und fügt nichts hinzu.

## 5. Eine Sprache anzeigen: SiteLanguageList und Siteaccesses

Was ein Besucher sieht, wird je Siteaccess entschieden, in `settings/siteaccess/<siteaccess>/site.ini.append.php`,
Gruppe `[RegionalSettings]`:

| Einstellung | Was sie bewirkt |
|---|---|
| `SiteLanguageList[]` | Die Inhaltssprachen, die dieser Siteaccess zeigt, die bevorzugte zuerst. Ein Objekt wird in der ersten davon gezeigt, die es hat. |
| `ShowUntranslatedObjects` | `disabled` (Auslieferungszustand): Objekte in keiner dieser Sprachen sind verborgen, außer sie sind immer verfügbar. `enabled`: Sie werden in ihrer Hauptsprache gezeigt. |
| `ContentObjectLocale` | Die Standardsprache für neue Inhalte und der Rückfall, wenn `SiteLanguageList` leer ist; ausgeliefert als `eng-US`. Ändern Sie sie auf einer Site mit Inhalten nicht, ohne die Warnung in `settings/site.ini` gelesen zu haben. |
| `ContentObjectFallbackLocale` | Der letzte Ausweg, wenn keine Inhaltssprache priorisiert und keine `ContentObjectLocale` gesetzt ist, zum Beispiel während der Setup-Assistent oder ein Skript Klassen anlegt, bevor die Site Sprachen hat; ausgeliefert als `eng-US` (früher war es fest kodiert `eng-GB`). Siehe [Rückfallsprache für Inhalte](../../eng-us/bc/6.0/content-object-fallback-locale.md). |
| `Locale` | Die Oberflächensprache und die Locale für Datum und Zahlen. |
| `TextTranslation` | `enabled`, um Oberflächentexte in die `Locale` zu übersetzen. |

Ein Siteaccess für deutsche Inhalte mit Englisch als Rückfall:

```ini
# settings/siteaccess/site_de/site.ini.append.php
[RegionalSettings]
Locale=ger-DE
ContentObjectLocale=ger-DE
SiteLanguageList[]
SiteLanguageList[]=ger-DE
SiteLanguageList[]=eng-US
ShowUntranslatedObjects=disabled
TextTranslation=enabled
```

Die leere Zeile `SiteLanguageList[]` am Anfang leert, was `settings/site.ini` oder ein Override der Liste gegeben hat,
sodass dieser Siteaccess genau die aufgeführten Sprachen hat. Hier sieht ein deutscher Besucher die deutsche
Übersetzung, wo es eine gibt, und sonst die amerikanisch-englische; ein Objekt nur in britischem Englisch ist
verborgen.

Eine mehrsprachige Site hat üblicherweise einen öffentlichen Siteaccess je Sprache, erkannt an einem URL-Element
(`/de/`) oder einem Hostnamen, die alle eine Datenbank teilen und in `site.ini [SiteAccessSettings]
RelatedSiteAccessList[]` eingetragen sind. Wie Sie die Erkennung einrichten, steht im Installationshandbuch,
[10.2 Siteaccesses und Site-Adressen](../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site), und
die Einstellungen für Mehrsprachigkeit in [10.14](../installation/10-nach-der-installation.md#1014-mehrsprachige-sites). Für
Links von einer Sprachfassung einer Seite zu einer anderen nennt `[RegionalSettings] TranslationSA[]` die
Übersetzungs-Siteaccesses, und der Template-Operator `language_switcher` erzeugt zusammen mit dem Modul
`switchlanguage/to` die Links.

Leeren Sie nach einer Änderung an einer `SiteLanguageList` den INI- und den Inhalts-Cache (Abschnitt 10). Die Seite
der Inhaltssprachen liest die Listen bei jedem Aufruf neu, daher zeigt ihre Siteaccess-Tabelle die Änderung sofort.

## 6. Hauptsprache und immer verfügbar

**Die Hauptsprache** eines Objekts ist seine Ausgangssprache: die Sprache, in der es angelegt wurde, oder die, die
ein Redakteur später im Fenster **Übersetzungen** des Objekts gewählt hat (Optionsfeld in der Spalte **Haupt**, dann
**Als Haupt setzen**). Die Hauptübersetzung kann nicht aus einem Objekt entfernt werden; zuerst muss eine andere
Übersetzung zur Hauptübersetzung werden. Auf der Seite der Inhaltssprachen zählt die Angabe *darin als Hauptsprache*
die Objekte, deren Hauptsprache diese Sprache ist.

**Immer verfügbar** ist ein Kennzeichen je Objekt, Bit 0 seiner Sprachmaske. Ein immer verfügbares Objekt wird auf
einem Siteaccess, der keine seiner Sprachen führt, in seiner Hauptsprache gezeigt, wo es sonst verborgen wäre.
Ordner, Benutzer, Benutzergruppen und Medien sind üblicherweise immer verfügbar, damit eine deutsche Site trotzdem den
Benutzer findet, der einen Artikel geschrieben hat, oder den Bilderordner. Sie setzen es im Fenster **Übersetzungen**
des Objekts: **Die Hauptsprache verwenden, wenn keine vorrangige Übersetzung vorhanden ist.**, dann **Aktualisieren**.
Eine Klasse hat einen Standardwert für neue Objekte (*Immer verfügbar* in der Klassenbearbeitung).

Auf der eigenen Seite einer Sprache sind immer verfügbare Objekte in der Objektliste markiert.

## 7. Inhalte im Editor übersetzen

Redakteure brauchen die Richtlinien `content/edit` (oder `content/translate`) und `content/create` für die Sprache;
alle drei lassen sich im Rolleneditor mit **Sprache** einschränken, sodass ein Übersetzer zum Beispiel Deutsch und
sonst nichts bearbeiten darf.

So übersetzen Sie ein Objekt:

1. Öffnen Sie das Objekt und klicken Sie auf **Bearbeiten**. Lässt es sich in mehr als einer Sprache bearbeiten,
   öffnet sich die Seite *&lt;Name&gt; bearbeiten*.
2. Wählen Sie unter **Neue Übersetzung** die hinzuzufügende Sprache. Wählen Sie unter **Übersetzung basiert auf** die
   vorhandene Übersetzung, von der Sie ausgehen (ihre Felder werden in die neue kopiert), oder **Keine**, um leer zu
   beginnen.
3. Klicken Sie auf **OK**, übersetzen Sie die Felder und klicken Sie auf **Veröffentlichen**.

Das Objekt hat nun eine Sprache mehr; die Seite der Inhaltssprachen zählt sie sofort.

Das Fenster **Übersetzungen** jedes Objekts (in der vollständigen Ansicht der Administration) listet seine Sprachen
mit markierter Hauptsprache und bietet **Ausgewählte entfernen** für Übersetzungen, **Als Haupt setzen** für die
Hauptsprache und die Einstellung für „immer verfügbar“. Klassen haben dasselbe Fenster auf ihrer Seite unter
**Setup > Klassen**, für die Übersetzungen des Klassennamens und der Beschreibung.

## 8. Inhalte von einer Sprache in eine andere verschieben

So gehen Sie vor, wenn Inhalte in der falschen Sprache angelegt wurden, zum Beispiel eine Reihe von
Newsletter-Objekten in britischem Englisch (`eng-GB`) auf einer Site, die amerikanisches Englisch (`eng-US`) zeigt,
sodass die Objekte in keiner `SiteLanguageList` stehen und nie gezeigt werden. Die Seite der Inhaltssprachen weist auf
eine solche Sprache hin: *In keiner SiteLanguageList*, *Von keinem Siteaccess gezeigt* oder *Nur N Objekte*, mit einem
Link auf die Objektliste auf der Seite der Sprache.

Für jedes Objekt in dieser Liste:

1. **Übersetzen Sie es in die richtige Sprache.** Bearbeiten, **Neue Übersetzung** `eng-US` wählen, **Übersetzung
   basiert auf** `eng-GB`, den Text bei Bedarf anpassen, **Veröffentlichen**. Nichts geht verloren: Beide
   Übersetzungen existieren jetzt.
2. **Machen Sie die richtige Sprache zur Hauptsprache.** Wählen Sie im Fenster **Übersetzungen** des Objekts `eng-US`
   in der Spalte **Haupt** und klicken Sie auf **Als Haupt setzen**.
3. **Entfernen Sie die falsche Übersetzung.** Wählen Sie im selben Fenster `eng-GB`, klicken Sie auf **Ausgewählte
   entfernen** und bestätigen Sie. Das löscht die `eng-GB`-Felder des Objekts in jeder Version (Abschnitt 9), und
   genau das wollen Sie, nachdem Schritt 1 sie kopiert hat.

Dann die Klassen: Öffnen Sie jede Klasse auf der Seite der Sprache unter **Setup > Klassen**; machen Sie in ihrem
Fenster **Übersetzungen** eine andere Sprache zur Hauptsprache, falls `eng-GB` es ist (**Als Haupt setzen**), und
entfernen Sie dann die Übersetzung `eng-GB`. Eine als *ungespeicherte Bearbeitung* markierte Klasse ist eine begonnene
und nie gespeicherte Klassenbearbeitung: Öffnen Sie die Klasse, klicken Sie auf **Bearbeiten** und dann auf
**Speichern** oder **Abbrechen**; bis dahin zählt sie als Klasse in dieser Sprache.

Zeigt die Seite der Sprache 0 Objekte und 0 Klassen, kann sie entfernt (Abschnitt 9) oder für später behalten werden.

Kein mitgelieferter Konsolenbefehl verschiebt Übersetzungen in großen Mengen; die Schritte oben sind der unterstützte
Weg, ein Objekt nach dem anderen, wobei die Rechte und Workflows des Redakteurs gelten. Für Hunderte von Objekten
schreiben Sie ein Skript gegen die Kernel-API, probieren es an einer Kopie der Datenbank aus und halten eine
Sicherung bereit
([10.9 Sicherung und Wiederherstellung](../installation/10-nach-der-installation.md#109-sicherung-und-wiederherstellung)).

Der andere Ausweg, wenn die Inhalte richtig waren und nur die Einstellungen fehlten, ist, die Sprache in die
`SiteLanguageList` des Siteaccess einzutragen, der sie zeigen soll (Abschnitt 5).

## 9. Übersetzungen und Sprachen sicher entfernen

### Was Inhalte entfernt und was nicht

| Aktion | Wo | Was sie löscht | Lässt es sich rückgängig machen? |
|---|---|---|---|
| Eine **Sprache** entfernen | Setup > Sprachen | Nur die Zeile der Sprache. Der Kernel lehnt ab, solange irgendein Objekt oder eine Klasse eine Übersetzung in ihr hat. | Erneut hinzufügen; sie erhält eventuell ein anderes Bit, was unproblematisch ist, weil nichts das alte genutzt hat |
| Die **Übersetzung** eines Objekts entfernen | Das Fenster Übersetzungen des Objekts | Alle Felder und Namen des Objekts in dieser Sprache, in jeder Version, und jede Version, die in dieser Sprache begonnen wurde | Nein, nur aus einer Sicherung |
| Die **Übersetzung** einer Klasse entfernen | Das Fenster Übersetzungen der Klasse | Name und Beschreibung der Klasse in dieser Sprache | Die Klasse erneut übersetzen |

Das Entfernen einer Sprache löscht also nie Inhalte: Es wird abgelehnt, bis nichts die Sprache mehr nutzt. Die
Inhalte, auf die es ankommt, werden Übersetzung für Übersetzung entfernt, im eigenen Fenster jedes Objekts, nach
einer Bestätigungsseite; die Hauptübersetzung lässt sich überhaupt nicht entfernen.

### Was das Entfernen einer Sprache blockiert

- **Objekte** mit einer Übersetzung in ihr (in jedem Status: veröffentlicht, Entwurf, archiviert). Die Karte zeigt, wie
  viele.
- **Klassen** mit einem Namen in ihr, einschließlich **ungespeicherter Klassenbearbeitungen**. Die Seite der Sprache
  listet sie auf.

Was der Kernel **nicht** zählt, was aber trotzdem das Bit oder die Locale der Sprache in der Datenbank trägt:

- **Klassentexte**, die je Locale gespeichert sind (serialisierte Namenslisten von Klassen, Klassenattributen,
  Klassengruppen und Zuständen), zum Beispiel eine leere `eng-GB`-Beschreibung neben der `eng-US`-Beschreibung.
- **Namenszeilen von Entwurfsversionen** von Objekten, deren veröffentlichte Version keine Übersetzung in der Sprache
  hat.
- **URL-Aliase**, deren Sprachmaske das Bit noch enthält, zum Beispiel Aliase entfernter Knoten.

Konvertieren oder bereinigen Sie diese, bevor Sie eine Sprache entfernen, sonst erbt eine später mit derselben ID
hinzugefügte Sprache sie. Wie das geht, samt der Liste der danach zu leerenden Caches, steht unter
[Rückfallsprache für Inhalte](../../eng-us/bc/6.0/content-object-fallback-locale.md#removing-eng-gb-from-an-existing-site).

Wovor die Seite warnt, was sie aber zulässt:

- **Siteaccesses, die die Sprache noch führen.** Nach dem Entfernen würden sie eine Sprache verlangen, die es nicht
  gibt; die Seite nennt sie, und die Rückfrage bittet Sie, die Sprache auch aus deren `SiteLanguageList` zu nehmen.
- **Die Oberflächensprache.** Die eigene Seite einer Sprache bietet das Entfernen für die Sprache, in der die
  Administration läuft, nicht an.

### Eine Sprache entfernen

1. Prüfen Sie unter **Setup > Sprachen**, dass die Karte *Unbenutzt* und *Kann entfernt werden: kein Objekt und keine
   Klasse hat eine Übersetzung darin.* zeigt.
2. Setzen Sie das Häkchen bei **Zum Entfernen auswählen** und klicken Sie auf **Ausgewählte entfernen**. Bei
   eingeschaltetem JavaScript listet eine Rückfrage die Sprachen und die Siteaccesses auf, die sie noch führen.
3. Die Seite meldet *English (United Kingdom) (eng-GB) wurde entfernt.* und nennt, falls Siteaccesses die Sprache
   noch führen, diese.
4. Nehmen Sie die Locale aus der `SiteLanguageList` dieser Siteaccesses heraus, leeren Sie den INI-Cache und den Cache `content_language` (Abschnitt 10) und leeren Sie auch den Antwort-Cache von Velocity: Sowohl Apache als auch Velocity halten die Liste der Sprachen vor.

Hat doch etwas die Sprache genutzt (ein Objekt wurde inzwischen übersetzt), meldet die Seite *wurde nicht entfernt:
N Objekte und M Klassen haben noch eine Übersetzung darin.*, und nichts ändert sich.

### Gefahrlos ausprobieren

Um die Formulare zum Hinzufügen und Entfernen in Aktion zu sehen, nehmen Sie eine Sprache, die kein Inhalt hat, und
entfernen Sie sie sofort wieder: Fügen Sie zum Beispiel `epo-EO` (Esperanto) hinzu, prüfen Sie, dass ihre Karte
*Unbenutzt* und *In keiner SiteLanguageList* zeigt, wählen Sie sie aus und entfernen Sie sie. Vergleichen Sie die
Liste der Sprachen vorher und nachher; sonst ändert sich nichts. Üben Sie nie an einer Sprache, die Ihre Inhalte
nutzen.

Um Übersetzungen selbst auszuprobieren, verwenden Sie die Testsprache **Lorem Ipsum** (`qlo-ZZ`): Sie wird genau dafür
mitgeliefert, ist nirgends installiert, und `exp:loremipsum` füllt eine Übersetzung in ihr mit Lorem-ipsum-Text. Siehe
[Die Testsprache Lorem Ipsum](testsprache-lorem-ipsum.md).

## 10. Befehle für die Shell

Sprachen werden auf der Seite hinzugefügt und entfernt; kein mitgelieferter Konsolenbefehl fügt eine Inhaltssprache
hinzu, entfernt sie oder verschiebt Übersetzungen. Die Shell ist für Caches, URL-Aliase und die
Oberflächenübersetzungen da. Führen Sie die Befehle im Wurzelverzeichnis der Installation aus; fügen Sie
`--allow-root-user` hinzu, wenn Sie als root arbeiten.

| Befehl | Wann |
|---|---|
| `php bin/php/ezcache.php --clear-id=content_language` | Die zwischengespeicherte Liste der Inhaltssprachen: nach dem Entfernen einer Sprache und nachdem die Tabelle von Hand oder durch ein Skript geändert wurde; danach `./console exp:velocity cache clear`, damit auch die Seiten auf Port 8080 sie verwerfen |
| `php bin/php/ezcache.php --clear-tag=ini` | Nach einer Änderung an einer `SiteLanguageList` oder einer beliebigen Einstellung in `[RegionalSettings]` |
| `php bin/php/ezcache.php --clear-tag=content` | Nach einer Änderung an Sprachen oder Übersetzungen, damit Listen und Seiten neu gezeichnet werden (das Hinzufügen oder Entfernen einer Sprache über die Seite erledigt das selbst) |
| `./console exp:updateniceurls --update-nodes` oder `php bin/php/updateniceurls.php -s admin` | Die URL-Aliase aller Knoten neu aufbauen, zum Beispiel nachdem ein Skript viele Übersetzungen hinzugefügt hat; Aliase werden je Sprache geführt |
| `./console exp:verify_aliases` | Die Tabelle der URL-Aliase prüfen; `--fix` repariert, was sich gefahrlos reparieren lässt, `--sql` zeigt die Abfragen |
| `php bin/php/ezchecktranslation.php ger-DE` | Wie vollständig die **Oberflächen**übersetzung einer Locale ist (nicht die Inhalte) |
| `php bin/php/ezgeneratetranslationcache.php` | Den Cache der Oberflächen-Übersetzungsdateien aufbauen; `--ts-list` beschränkt ihn auf einige Locales |
| `php bin/php/ezcache.php --clear-tag=i18n` | Nach einer Änderung an einer `.ts`-Datei der Oberflächenübersetzung |
| `./console exp:loremipsum <object id> --dry-run` | Die Übersetzung von Objekten in der Testsprache Lorem Ipsum mit Lorem-ipsum-Text füllen, sobald diese hinzugefügt ist; [Anleitung](testsprache-lorem-ipsum.md) |

Velocity (der Anwendungsserver auf Port 8080) hält einen eigenen Antwort-Cache: Führen Sie nach jedem dieser Befehle
auch `./console exp:velocity cache clear` aus.

## 11. Abläufe Schritt für Schritt

### Deutsch zu einer englischen Site hinzufügen

Die Site hat einen öffentlichen Siteaccess, `site`, mit `SiteLanguageList[]=eng-US`.

1. **Fügen Sie die Sprache hinzu.** Setup > Sprachen > Sprache hinzufügen, `german` eingeben, *German · Deutsch ·
   Germany · ger-DE* wählen, auf Sprache hinzufügen klicken. Die Übersicht zeigt eine neue Karte *German, ger-DE*,
   *Noch nicht verwendet*, *In keiner SiteLanguageList*.
2. **Geben Sie Deutsch einen Siteaccess.** Kopieren Sie `settings/siteaccess/site` nach `settings/siteaccess/site_de`
   und setzen Sie in dessen `site.ini.append.php` die `[RegionalSettings]` aus Abschnitt 5 (`Locale=ger-DE`,
   `SiteLanguageList[]=ger-DE`, dann `eng-US`). Tragen Sie `site_de` in `AvailableSiteAccessList[]` und
   `RelatedSiteAccessList[]` in `settings/override/site.ini.append.php` ein und richten Sie die Erkennung ein (ein
   URL-Element `/de` oder einen Hostnamen, siehe
   [10.2](../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site)).
3. **Leeren Sie die Caches**: `php bin/php/ezcache.php --clear-tag=ini,content`, dann
   `./console exp:velocity cache clear`. Die deutsche Karte zeigt jetzt *Zuerst gezeigt von 1* und den Chip
   `site_de`.
4. **Übersetzen Sie.** Beginnen Sie mit der Startseite und den Ordnern des Hauptmenüs (Abschnitt 7). Solange eine
   Seite nicht übersetzt ist, zeigt `site_de` ihre englische Übersetzung, weil `eng-US` die zweite Sprache seiner
   Liste ist.
5. **Markieren Sie die gemeinsam genutzten Objekte als immer verfügbar**, wo sie es noch nicht sind (Benutzer,
   Medienordner), damit sie auf `site_de` auch ohne deutsche Übersetzung erscheinen.
6. **Geben Sie Übersetzern eine Rolle** mit `content/translate` und `content/edit`, beschränkt auf die Sprache
   `German`.
7. **Prüfen Sie**: Öffnen Sie `/de/` (oder den deutschen Host) und sehen Sie sich die Objektzahl auf der deutschen
   Karte der Sprachenseite an.

### Eine versehentlich verwendete Sprache ausmustern

Der Fall aus Abschnitt 8: eine Sprache mit wenigen Objekten oder in keiner `SiteLanguageList`.

1. Öffnen Sie die Seite der Sprache über ihre Karte. Notieren Sie die aufgeführten Objekte und Klassen.
2. Verschieben Sie jedes Objekt (Abschnitt 8, Schritte 1 bis 3) und jede Klasse in die richtige Sprache. Speichern
   oder verwerfen Sie jede Klasse mit *ungespeicherter Bearbeitung*.
3. Laden Sie die Seite neu, bis sie *Kein Objekt und keine Klasse hat eine Übersetzung in ..., daher kann die Sprache
   entfernt werden.* sagt.
4. Entfernen Sie die Sprache in der Übersicht (Abschnitt 9) und nehmen Sie sie aus jeder `SiteLanguageList` heraus.
5. Leeren Sie den INI- und den Inhalts-Cache.

### Eine verborgene Sprache sichtbar machen

Wenn die Objekte richtig sind und nur kein Siteaccess ihre Sprache führt: Tragen Sie die Locale in die
`SiteLanguageList` des Siteaccess ein, der sie zeigen soll, an der gewünschten Position (zuerst heißt bevorzugt),
leeren Sie den INI- und den Inhalts-Cache und prüfen Sie, dass die Karte jetzt diesen Siteaccess zeigt.

## 12. Fehlerbehebung

**Ein Objekt ist nach einer Sprachänderung von einer Site verschwunden.** Der Siteaccess führt keine der Sprachen des
Objekts, und das Objekt ist nicht immer verfügbar. Sehen Sie sich die Sprachen des Objekts in seinem Fenster
Übersetzungen und die Siteaccess-Tabelle der Sprachenseite an. Fügen Sie eine Übersetzung hinzu, machen Sie das Objekt
immer verfügbar oder tragen Sie die Sprache in die `SiteLanguageList` ein.

**„Ausgewählte entfernen“ bewirkt bei einer Sprache nichts.** Ihr Kontrollkästchen ist deaktiviert, weil Objekte oder
Klassen sie noch haben; die letzte Zeile der Karte sagt, wie viele. Sagt die Seite 0 Objekte, aber einige Klassen,
suchen Sie auf der Seite der Sprache nach Klassen mit *ungespeicherter Bearbeitung*: Eine nie gespeicherte
Klassenbearbeitung zählt mit.

**Die Klassenzahl auf der Karte ist höher als die Zahl der Klassen unter Setup > Klassen.** Der Unterschied sind
ungespeicherte Klassenbearbeitungen, die der Kernel mitzählt. Die Seite der Sprache listet sie, markiert als
*ungespeicherte Bearbeitung*.

**Die Seite sagt, eine Sprache stehe in keiner SiteLanguageList, aber ein Einstellungsordner führt sie.** Nur
Siteaccesses aus `AvailableSiteAccessList` liefern Seiten aus, daher zählen nur sie. Tragen Sie den Siteaccess dort ein
oder betrachten Sie die Sprache als nicht gezeigt.

**Ein Siteaccess führt eine Locale, die als „keine Inhaltssprache“ markiert ist.** Diese Sprache wurde nie
hinzugefügt oder wurde entfernt. Fügen Sie sie auf der Seite hinzu oder nehmen Sie sie aus der Liste heraus.

**Das Formular zum Hinzufügen meldet, die Locale sei nicht in share/locale.** Eigene Sprachen brauchen eine
Locale-Datei; kopieren Sie die nächstliegende (Abschnitt 4).

**„Es wurde keine Sprache hinzugefügt: Diese Installation hat bereits die höchste Zahl an Sprachen.“** Alle 62 Bits
sind belegt. Entfernen Sie zuerst eine Sprache, die nichts nutzt.

**Eine Sprache ist aufgetaucht, die niemand hinzugefügt hat.** Der Import eines Klassenpakets (der Installationsschritt
einer Extension oder **Setup > Pakete**) legt jede Sprache an, die seine Klassendefinitionen nennen. So kam `eng-GB`
auf den Demonstrationsserver: Die Klassenpakete von `cjw_newsletter` vor Version 4.2.1 nannten sie. Eine alte
`kickstart.ini` mit `Languages[]=eng-GB` oder eine Site, die angelegt wurde, bevor der Rückfall `eng-US` wurde
([Rückfallsprache für Inhalte](../../eng-us/bc/6.0/content-object-fallback-locale.md)), bewirkt dasselbe. Die Karte der Sprache
zeigt, was sie nutzt; verschieben Sie diese Inhalte (Abschnitt 8) und entfernen Sie die Sprache (Abschnitt 9).

**Nach dem Hinzufügen einer Sprache bietet der Editor sie nicht an.** Der Editor bietet nur Sprachen an, die der
Benutzer verwenden darf: Prüfen Sie die Einschränkung **Sprache** der Richtlinien `content/edit`, `content/translate`
und `content/create` des Benutzers.

**Deutsche Texte in der Oberfläche, englische Inhalte (oder umgekehrt).** Das ist die Trennung aus Abschnitt 1:
Setzen Sie `Locale` für die Oberfläche und `SiteLanguageList` für die Inhalte.

**Die Seite sieht auf Port 8080 anders aus als über Apache.** Velocity speichert Antworten zwischen und hält
PHP-Klassen geladen: Führen Sie `./console exp:velocity cache clear` aus und nach einem Update des Kernels
`./console exp:velocity deploy`.

## Quellen

- [Sprachen](../../eng-us/languages.md): jede Locale und jede Übersetzung der Administration, Unterstützungsgrade, Hinzufügen
  einer Locale oder einer Übersetzung, Stand der Rechts-nach-links-Unterstützung.
- [Übersetzungen und Sprachen](../../eng-us/features/6.0/translations-and-languages.md): Oberflächenübersetzungen,
  HTML-Sprachauszeichnungen, Sprachen in einer neuen Installation.
- [10.14 Mehrsprachige Sites](../installation/10-nach-der-installation.md#1014-mehrsprachige-sites) und
  [10.2 Siteaccesses und Site-Adressen](../installation/10-nach-der-installation.md#102-siteaccesses-und-adressen-der-site) im
  Installationshandbuch.
- [Das Inhaltsmodell und das Bearbeiten von Inhalten](inhaltsmodell-und-bearbeitung.md): Objekte, Versionen und der Editor.
- [Betrieb einer Site](betrieb-einer-website.md): welche Caches wann zu leeren sind.
- [Blättern in Admin-Listen](../../eng-us/features/6.0/admin-list-paging.md): die Seitengröße der Sprachenliste.
- Einstellungen: `settings/site.ini [RegionalSettings]` (jede Einstellung mit ihrem Kommentar), `[SiteAccessSettings]
  AvailableSiteAccessList` und `RelatedSiteAccessList`.
- Code: `kernel/classes/ezcontentlanguage.php` (Hinzufügen, Entfernen, die Zählungen, die Masken, `maxCount()`),
  `kernel/private/classes/views/content/translations.php` (die Seite: Zählungen je Sprache, Siteaccess-Listen, die
  Hinweise), `kernel/classes/ezcontentobject.php` (`removeTranslation()`), Templates
  `design/admin4/templates/content/translations.tpl`, `translationview.tpl` und `translationnew.tpl` (dieselben in
  `design/admin`), Tests `tests/tests/kernel/classes/expContentLanguagesOverviewTest.php` (ohne Datenbank) und
  `expContentLanguagesCountsLiveTest.php` (nur lesend, auf einer Installation).
