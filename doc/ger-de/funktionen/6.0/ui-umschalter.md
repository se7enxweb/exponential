# Sprache der Oberfläche und der UI-Umschalter

Ein angemeldeter Benutzer der Administrationsoberfläche kann die Sprache wählen, in der die Administrationsoberfläche
selbst gezeigt wird, ohne den Siteaccess zu wechseln und ohne die Inhalte zu berühren. Die Wahl sitzt in der rechten
Werkzeugleiste, direkt nach den Lesezeichen, unter der Überschrift *Oberfläche*. Sie ist die erste Wahl eines kleinen
Frameworks, des **UI-Umschalters**, das Extensions um eigene Wahlmöglichkeiten erweitern können (siehe
[die Provider-API](../../../eng-us/bc/6.0/ui-switch.md)).

## Wo er ist

| Ort | Was Sie bekommen |
|---|---|
| admin4 und admin4l, rechte Werkzeugleiste | Der Kasten *Oberfläche* nach den Lesezeichen, mit der Wahl *Sprache der Oberfläche*: eine Schaltfläche, die die aktuelle Sprache zeigt und die Liste der Sprachen öffnet |
| Der Editor-Siteaccess | Derselbe Kasten, nach den Lesezeichen (neue Installationen; bestehende siehe [die Anleitung zu den Einstellungen](../../anleitungen/ui-umschalter-einstellungen.md)) |
| admin4l mit der rechten Spalte aus einem Layout | Der Block *Admin: interface choices (language)* in der rechten Zone des Admin-Layouts, nach den Lesezeichen |
| Seitenleiste von AdminUI | Das Dropdown *Oberfläche* nach dem Dropdown der Lesezeichen (`exp_adminui.ini [MenuPlugins] Enabled[]=uiswitch`) |
| `uiswitch/state` | JSON dessen, was der angemeldete Benutzer auf diesem Siteaccess wählen darf, für Skripte |

Der Kasten wird nicht gezeigt beim Bearbeiten oder Auswählen, auf einem öffentlichen Siteaccess, einem Benutzer ohne die
Richtlinie oder wenn der Provider weniger als zwei Sprachen anzubieten hat.

Der Umschalter funktioniert auf jedem Siteaccess, dessen Design-Liste ein Administrationsdesign enthält (`admin`,
`admin2`, `admin3`, `admin4`, `admin4l`; die Designs `editor` und `adminui` greifen auf sie zurück), oder auf den
Siteaccesses aus `uiswitch.ini [UISwitchSettings] SiteAccessList[]`. Das Template der Werkzeugleiste wird für admin4
mitgeliefert, das auch admin4l, der Editor und AdminUI verwenden.

## Verwenden

1. Öffnen Sie die Liste unter *Sprache der Oberfläche*. Sie zeigt die geltende Sprache, zum Beispiel *English (United
   Kingdom)*. Die Liste beginnt mit den meistgebrauchten Sprachen in Gruppen (*Englisch*, *Wichtige Weltsprachen*,
   *Regionale Varianten*), dann jede Sprache A–Z; das Eintippen der ersten Buchstaben eines Namens springt dorthin.
2. Wählen Sie eine Sprache. Jeder Eintrag gibt den eigenen Namen der Sprache, ihren Namen in der aktuellen Sprache der
   Oberfläche und wie vollständig die Übersetzung ist (*Deutsch (Deutschland) — German (Germany) (99%)*); eine Sprache
   ohne Übersetzung sagt *noch keine Übersetzung* und zeigt englische Texte mit ihren eigenen Datums- und Zahlenformaten.
3. Drücken Sie *Anwenden*. Die Seite, auf der Sie waren, wird in der gewählten Sprache neu geladen.

Die Liste, ihre Reihenfolge und die Prozentsätze: [die Liste der Sprachen der Oberfläche](../../../eng-us/features/6.0/ui-switch-languages.md). Eine
Installation, deren Einstellungen keine Gruppen und keine Liste A–Z haben, zeigt die Sprachen stattdessen als Liste von
Schaltflächen.

Die erneute Wahl der eigenen Sprache des Siteaccess hebt die Wahl auf.

### Was sich ändert

Die Wahl setzt `site.ini [RegionalSettings] Locale` des Siteaccess nur für Ihre Anfragen, im Speicher:

- die Übersetzungen der Administrationsoberfläche (Menüs, Schaltflächen, Meldungen, die Werkzeugleiste selbst);
- Datums-, Zeit- und Zahlenformate, die der Locale folgen;
- die Sprache der Seite: das Attribut `lang` des Elements `<html>` und den Header `Content-Language`.

### Was sich nicht ändert

- **Inhaltssprachen.** `SiteLanguageList[]`, `ContentObjectLocale` und die Sprachen, in denen Inhalte bearbeitet und
  gezeigt werden, bleiben, was der Siteaccess sagt. Ein Benutzer mit Deutsch als Sprache der Oberfläche bearbeitet
  weiterhin die englische Übersetzung eines Artikels, wenn er diese öffnet.
- Andere Benutzer. Die Wahl gehört nur Ihnen.
- Andere Siteaccesses. Eine auf `admin` getroffene Wahl wird auf `editor` nicht angewendet; jeder behält seine eigene.
- Die Einstellungsdateien. Nichts wird auf die Platte geschrieben.

### Wie lange sie gilt

Die Wahl wird in Ihrer Sitzung gehalten, für diesen Siteaccess und diesen Benutzer. Sie endet mit der Sitzung, beim
Abmelden oder wenn die Sitzung abläuft. Wenn sich ein anderer Benutzer im selben Browser anmeldet, beginnt er mit der
Sprache des Siteaccess.

Ein Administrator kann die Wahl bei der nächsten Anmeldung zurückkommen lassen: Mit `RememberPreference=enabled` wird die
Wahl auch in den Benutzereinstellungen gehalten. Standardmäßig ist das aus.

## Wer ihn bekommt

Die Wahl wird Benutzern gezeigt, die die Richtlinie **`uiswitch/language`** haben. Sie hat keine Einschränkungen.

- **Administrator** hat sie durch ihre Richtlinie auf jedes Modul (`*/*`).
- **Editor** erhält sie bei neuen Installationen: Die sauberen Daten und die Multisite-Installer geben der Rolle
  `uiswitch/language`.
- Jede andere Rolle: Gewähren Sie `uiswitch/language` unter *Rollen und Richtlinien* (siehe
  [die Anleitung zu den Einstellungen](../../anleitungen/ui-umschalter-einstellungen.md#5-richtlinien)).

Die Richtlinie wird bei jeder Anfrage erneut geprüft. Wird sie entzogen, wird die gespeicherte Wahl des Benutzers nicht
mehr angewendet und aus der Sitzung entfernt.

## Tastatur

Die Schaltfläche folgt den Menüs im Kopfbereich von admin4:

| Taste | Auf der Schaltfläche | In der Liste |
|---|---|---|
| Eingabe oder Leertaste | öffnet oder schließt die Liste | wählt die fokussierte Sprache |
| Pfeil nach unten | öffnet die Liste und geht zur ersten Sprache | geht zur nächsten Sprache |
| Pfeil nach oben | | geht zur vorigen Sprache; von der ersten zurück zur Schaltfläche |
| Pos1, Ende | | erste, letzte Sprache |
| Escape | schließt die Liste | schließt die Liste und gibt den Fokus an die Schaltfläche zurück |
| Tab | | das Verlassen der Liste schließt sie |

Es ist immer nur ein Menü des Kopfbereichs oder eine Liste der Oberfläche offen. Die aktuelle Sprache trägt
`aria-current="true"`, die Schaltfläche benennt die Liste mit `aria-expanded` und `aria-controls`, und der Name jeder
Sprache hat sein eigenes `lang`, sodass ein Bildschirmleser ihn richtig ausspricht.

## Ohne JavaScript

Die Schaltfläche bleibt verborgen, und die Liste der Sprachen wird geöffnet gezeigt. Jede Sprache ist eine
Absende-Schaltfläche eines gewöhnlichen Formulars, sodass die Wahl genauso funktioniert.

## Sicherheit

- Eine Änderung wird nur als **POST** an `uiswitch/set` angenommen (ein GET wird mit 405 beantwortet).
- Der Kernel prüft das **Formular-Token** jedes POST, bevor die Ansicht läuft.
- Eine Anfrage, die der Browser als von einer anderen Website kommend kennzeichnet (`Sec-Fetch-Site: cross-site`), wird
  mit 403 abgelehnt.
- Der Wert muss genau einer aus der **Freigabeliste** des Providers sein (kein Kürzen, kein Angleichen der Schreibung),
  sonst wird die Anfrage mit 400 abgelehnt. Ein Provider, den der Benutzer nicht verwenden darf, wird mit 403 abgelehnt,
  ein Siteaccess ohne den Umschalter mit 404.
- Nach der Änderung kehrt der Browser nur zu einem **lokalen Pfad** der Website zurück (`/content/view/full/2`); alles
  andere (`//host`, ein Schema, ein Backslash, Segmente `..`, Steuerzeichen) geht zum Dashboard.
- Nichts aus der Anfrage erreicht einen Dateinamen, einen Pfad oder den Namen einer Einstellung, bevor die Freigabeliste
  es angenommen hat.
- Jede Änderung wird im Prüfprotokoll als **`access.uiswitch.change`** erfasst (Kanal `access`, Schweregrad info), mit
  Wert und Siteaccess vorher und nachher. Siehe [den Audit-Katalog](../../../eng-us/bc/6.0/audit.md).
- Eine mit einer Wahl ausgegebene Seite wird nie im rollenbewussten HTTP-Seitencache gespeichert, und der
  Inhaltsansichts-Cache, `{cache-block}` und kompilierte Templates halten sie getrennt von den Seiten der Benutzer ohne
  Wahl.

## Exponential Velocity

Velocity-Worker bedienen viele Anfragen in einem Prozess. Die Wahl wird zu Beginn jeder Anfrage angewendet, und alles, was
sie berührt hat (der Übersetzer, die Standard-Locale, der Übersetzungs-Cache), wird zu Beginn der nächsten zurückgesetzt,
sodass ein Worker nie die Sprache eines Benutzers in die Seite eines anderen trägt. Ein Worker, der vor dem Ausbringen der
Funktion gestartet wurde, zeigt einfach keine Wahl, bis `./console exp:velocity restart` (Teil von
`exp:velocity deploy`) läuft.

## Verwandte Seiten

- [Einstellungen des UI-Umschalters](../../anleitungen/ui-umschalter-einstellungen.md): jede Einstellung, eine Sprache hinzufügen, die Werkzeugleiste auf bestehenden Installationen.
- [Die Provider-API des UI-Umschalters](../../../eng-us/bc/6.0/ui-switch.md): wie eine Extension ihre eigene Wahl hinzufügt.
- [Inhaltssprachen](../../anleitungen/inhaltssprachen.md): die Sprachen, in denen Inhalte geschrieben werden.
- [Rollen und Richtlinien](../../anleitungen/rollen-und-richtlinien.md).

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
