# Einstellungen der UI-Umschaltung: die Wahl der Sprache der Oberfläche

Diese Anleitung richtet sich an den Administrator einer Installation. Am Ende kennen Sie jede Einstellung der
UI-Umschaltung, können eine weitere Sprache der Oberfläche anbieten, die Wahl auf einige Siteaccesses beschränken oder
abschalten, den Eintrag der Werkzeugleiste in einer bestehenden Installation ergänzen und die Wahl einer Rolle geben.
Was Benutzer sehen, steht auf [der Funktionsseite](../../eng-us/features/6.0/ui-switch.md); wie Code eine eigene Wahl hinzufügt,
steht in [der Provider-API](../../eng-us/bc/6.0/ui-switch.md).

Fügen Sie `--allow-root-user` hinzu, wenn Sie einen Befehl als root ausführen.

## 1. Was das System tut, in einem Absatz

Ein angemeldeter Benutzer mit der Richtlinie `uiswitch/language` wählt die Sprache der Administrationsoberfläche in
der rechten Werkzeugleiste, nach den Lesezeichen. Die Wahl wird in der Sitzung für diesen Siteaccess und diesen
Benutzer gehalten und früh in jeder Anfrage angewendet: Sie setzt `site.ini [RegionalSettings] Locale` im Speicher,
nur für die Anfragen dieses Benutzers, sodass die Übersetzungen, die Datums- und Zahlenformate und die Sprache der
Seite ihr folgen. Inhaltssprachen werden nie geändert. Die Auswahlmöglichkeiten kommen von **Providern**, die in
`settings/uiswitch.ini` aufgeführt sind; der Sprach-Provider ist der erste.

## 2. Die Einstellungen: settings/uiswitch.ini

Bearbeiten Sie niemals `settings/uiswitch.ini` selbst. Legen Sie Änderungen in `settings/override/uiswitch.ini.append.php`
(jeder Siteaccess), in `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` (ein Siteaccess) oder in
`settings/uiswitch.ini.append.php` einer Extension ab.

### [UISwitchSettings]

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Enabled` | `true` | `true` oder `false`. Aus: Nirgends wird eine Wahl angeboten oder angewendet, und gespeicherte Wahlen werden ignoriert. |
| `SiteAccessList[]` | leer | Die Siteaccesses, in denen die Auswahl wirkt. Leer: jeder Siteaccess, dessen Designliste (`SiteDesign`, `AdditionalSiteDesignList[]`) ein Design enthält, dessen Name mit `admin` beginnt (admin, admin2, admin3, admin4, admin4l; die Siteaccesses editor und adminui führen sie als Fallbacks). Eine öffentliche Site kommt standardmäßig nie in Frage. |
| `ProviderList[]` | `language` | Die Provider, in der Reihenfolge der Werkzeugleiste. Jeder braucht eine Gruppe `[UISwitch_<identifier>]` mit `Class=` und erhält die Richtlinienfunktion `uiswitch/<identifier>`. |

### [UISwitch_language]

| Einstellung | Standard | Bedeutung |
|---|---|---|
| `Class` | `expUISwitchLanguageProvider` | Die Provider-Klasse. |
| `Enabled` | `true` | `false` schaltet diesen Provider ab und lässt die anderen arbeiten. |
| `GroupList[]` | `english`, `major`, `regional` | Die Gruppen der Liste, in Reihenfolge. Ein Gruppenbezeichner ist `[a-z][a-z0-9_]*`; eine Gruppe, die nichts anzubieten hat, wird ausgelassen. Leer, mit `AllLanguages=disabled`: keine Gruppen, die Auswahl ist die Liste von Schaltflächen aus #506. |
| `GroupLabel[<group>]` | *Englisch*, *Wichtige Weltsprachen*, *Regionale Varianten* | Die Überschrift einer Gruppe, übersetzt im Kontext `kernel/uiswitch/language`. Fehlt sie: der Bezeichner. |
| `GroupValues_<group>[]` | 5, 19 und 10 Locales | Die Locales einer Gruppe, in der angezeigten Reihenfolge. Warum die mitgelieferten so sind, wie sie sind: [die Funktionsseite](../../eng-us/features/6.0/ui-switch-languages.md#why-the-default-ranking-is-what-it-is). |
| `AllLanguages` | `enabled` | `enabled`: Jede Locale aus `share/locale` (ohne Formatvarianten wie `eng-GB@euro`) wird angeboten und nach den Gruppen von A bis Z als *Alle Sprachen (A–Z)* aufgeführt. `disabled`: nur die Gruppen und `AllowedValues[]`; die angebotenen Locales, die keine Gruppe nennt, werden von A bis Z als *Weitere Sprachen (A–Z)* aufgeführt. Fehlt die Einstellung: `disabled`. |
| `ListUntranslated` | `enabled` | `enabled`: Locales ohne Übersetzung der Oberfläche (`share/translations/<locale>/translation.ts`) werden ebenfalls angeboten, markiert mit *noch keine Übersetzung*: englische Texte, eigene Datums- und Zahlenformate. `disabled`: nur Englisch (`eng-*`) und die übersetzten Sprachen. Fehlt die Einstellung: `disabled`. |
| `AllowedValues[]` | leer | Weitere Locales, die außerhalb der Gruppen angeboten werden: eine Formatvariante (`eng-GB@euro`), oder die ganze Liste, wenn es keine Gruppen gibt und `AllLanguages=disabled` gilt. |
| `RememberPreference` | `disabled` | `enabled`: Die Wahl wird außerdem in den Voreinstellungen des Benutzers gehalten (`exp_uiswitch_language`) und kommt bei der nächsten Anmeldung zurück. `disabled`: Sie endet mit der Sitzung, beim Abmelden. |
| `Title` | (keiner) | Wird vom Sprach-Provider nicht verwendet, dessen Titel immer *Sprache der Oberfläche* ist; andere Provider zeigen ihn als Namen ihrer Wahl. |

Eine Locale wird nur angeboten, wenn ihr Code ein Locale-Code ist (`eng-GB`, `nor-NO@intl`) und
`share/locale/<locale>.ini` existiert (mit `ListUntranslated=disabled` außerdem eine Übersetzung, außer für Englisch);
alles andere in den Einstellungen wird stillschweigend ausgelassen. Die eigene `Locale` des Siteaccess wird immer
angeboten, und wer sie wählt, hebt die Wahl auf. Das sind auch die einzigen Werte, die `uiswitch/set` annimmt. Ein
Provider mit weniger als zwei anzubietenden Werten erscheint nicht in der Werkzeugleiste.

Mit Gruppen oder `AllLanguages=enabled` ist die Auswahl ein Listenfeld mit einer Überschrift pro Gruppe und einer
Schaltfläche *Übernehmen*; jeder Eintrag zeigt den eigenen Namen der Sprache, ihren Namen in der aktuellen Sprache der
Oberfläche und wie vollständig ihre Übersetzung ist („Deutsch (Deutschland) — German (Germany) (99%)“,
„Kiswahili (Kenya) — Swahili (Kenya) (no translation yet)“). Wie die Namen, die Reihenfolge von A bis Z und die
Prozentwerte entstehen: [die Funktionsseite](../../eng-us/features/6.0/ui-switch-languages.md).

## 3. Beispiele

### Nur in einigen Siteaccesses

```ini
<?php /* #?ini charset="utf-8"?

[UISwitchSettings]
SiteAccessList[]
SiteAccessList[]=admin
SiteAccessList[]=editor
*/ ?>
```

Die leere Zeile `SiteAccessList[]` leert die Liste zuerst, bevor die Einträge hinzugefügt werden.

### Die Gruppen ändern

Eine Liste in einem Override ersetzt die Standardliste, wenn sie mit einer leeren Zeile beginnt
(`GroupValues_major[]`). Ein Unternehmen, das in Europa arbeitet, könnte seine eigenen Sprachen an den Anfang stellen:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
GroupList[]
GroupList[]=ours
GroupList[]=english
GroupLabel[ours]=Our languages
GroupValues_ours[]
GroupValues_ours[]=ger-DE
GroupValues_ours[]=fre-FR
GroupValues_ours[]=ita-IT
*/ ?>
```

Eine neue Überschrift braucht ihre Übersetzung im Kontext `kernel/uiswitch/language` (in `translations/` einer
Extension), sonst wird sie so angezeigt, wie sie geschrieben ist. Ohne die leeren Zeilen werden die Einträge zu den
Standardeinträgen hinzugefügt.

### Nur wenige Sprachen anbieten

Keine Gruppen und keine Liste aller Locales von A bis Z: Die Auswahl ist die Liste von Schaltflächen, mit der eigenen
Sprache des Siteaccess und den genannten Sprachen:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
GroupList[]
AllLanguages=disabled
AllowedValues[]
AllowedValues[]=eng-GB
AllowedValues[]=ger-DE
*/ ?>
```

### Nur übersetzte Sprachen

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
ListUntranslated=disabled
*/ ?>
```

Die Gruppen zeigen dann nur Englisch und die Sprachen mit `share/translations/<locale>/translation.ts`.

### Eine Sprache hinzufügen

Französisch braucht zwei Dateien, die beide mitgeliefert werden:

- `share/locale/fre-FR.ini` (die Locale: Datums- und Zahlenformate, der Name *Français (France)*);
- `share/translations/fre-FR/translation.ts` (die Übersetzung der Oberfläche).

Mit den mitgelieferten Einstellungen wird Französisch bereits angeboten (*Wichtige Weltsprachen* und von A bis Z).
Eine Locale, die `share/locale` hat, aber keine Gruppe nennt, wird über `AllLanguages=enabled` angeboten; um sie
einzuordnen, fügen Sie sie einer Gruppe hinzu:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
GroupValues_major[]=swe-SE
*/ ?>
```

Eine neue Übersetzung (`share/translations/<locale>/translation.ts`) zeigt ihren Prozentwert beim nächsten
Seitenaufruf: Der Katalog der Namen und Prozentwerte wird im Cache-Verzeichnis (`uiswitch/`) zwischengespeichert und
von selbst neu aufgebaut, wenn sich eine Locale- oder Übersetzungsdatei ändert.

### Die Wahl über das Abmelden hinaus behalten

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
RememberPreference=enabled
*/ ?>
```

Die Wahl wird dann auch in den Voreinstellungen des Benutzers gespeichert und erneut angewendet, wenn in der Sitzung
nichts gespeichert ist. Wer die eigene Sprache des Siteaccess wählt, löscht auch den gemerkten Wert.

### Abschalten

Überall:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitchSettings]
Enabled=false
*/ ?>
```

Nur den Sprach-Provider, wobei jeder Provider, den eine Extension hinzufügt, erhalten bleibt:

```ini
<?php /* #?ini charset="utf-8"?

[UISwitch_language]
Enabled=false
*/ ?>
```

Nur in einem Siteaccess: dieselben Zeilen in `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php`.

Leeren Sie nach jeder Änderung von `uiswitch.ini` den INI-Cache (`php bin/php/ezcache.php --clear-tag=ini`) und laden
Sie PHP-FPM neu oder starten Sie Velocity neu.

## 4. Die Werkzeugleiste in einer bestehenden Installation

Neue Installationen erhalten den Eintrag der Werkzeugleiste vom Installer (Siteaccesses admin, editor und adminui).
Eine zuvor eingerichtete Installation muss ihn einmal hinzufügen.

### admin4, der Siteaccess editor und AdminUI: toolbar.ini

Fügen Sie in `settings/siteaccess/<admin siteaccess>/toolbar.ini.append.php` (und ebenso für die Siteaccesses editor
und adminui) das Werkzeug der Liste der verfügbaren Werkzeuge hinzu und setzen Sie es in `[Toolbar_admin_right]`
direkt nach `Tool[]=admin_bookmarks`:

```ini
[Tool]
AvailableToolArray[]
AvailableToolArray[]=setup_link
AvailableToolArray[]=admin_current_user
AvailableToolArray[]=admin_bookmarks
AvailableToolArray[]=admin_ui_switch
AvailableToolArray[]=admin_clear_cache
AvailableToolArray[]=admin_quick_settings

[Toolbar_admin_right]
Tool[]
Tool[]=admin_clear_cache
Tool[]=admin_bookmarks
Tool[]=admin_ui_switch
Tool[]=admin_current_user
Tool[]=admin_preferences
```

Lassen Sie die übrigen Zeilen Ihrer Datei, wie sie sind; nur die beiden Zeilen mit `admin_ui_switch` sind neu. Das
Template des Werkzeugs ist `design/admin4/templates/toolbar/full/admin_ui_switch.tpl`, daher muss die Designliste des
Siteaccess admin4 enthalten (admin4l, editor und adminui führen es als Fallback). Ein Siteaccess, der allein auf den
klassischen Designs läuft (admin, admin2, admin3), hat kein Template für das Werkzeug; lassen Sie es dort weg.

### admin4l mit der rechten Spalte aus einem Layout

Wenn der Admin-Siteaccess seine rechte Spalte aus dem Admin-Layout von explayouts bezieht, wird die Liste der
Werkzeugleiste nicht verwendet. Öffnen Sie das Admin-Layout im Layout-Editor, fügen Sie den Block
**Admin: Auswahl der Oberfläche (Sprache)** (`admin_ui_switch`) nach dem Lesezeichen-Block in die rechte Zone ein und
veröffentlichen Sie das Layout. Neue Installationen erhalten ihn aus dem Fragment der Admin-Layouts von explayouts.

### Seitenleiste von AdminUI

Die Seitenleiste von AdminUI zeigt das Dropdown *Oberfläche*, wenn `exp_adminui.ini` das Plugin aufführt, wie es
standardmäßig der Fall ist:

```ini
[MenuPlugins]
Enabled[]=uiswitch
```

Eine Installation, deren Override `Enabled[]` ersetzt, muss die Zeile dort nach `Enabled[]=bookmarks` hinzufügen.

### Die Caches leeren

Die Liste der Werkzeuge der Werkzeugleiste wird in das kompilierte Template des Pagelayouts kompiliert, daher wird
eine Änderung von `toolbar.ini` erst sichtbar, nachdem die kompilierten Templates neu erzeugt wurden:

```bash
php bin/php/ezcache.php --clear-tag=ini
php bin/php/ezcache.php --clear-tag=template
```

Laden Sie dann PHP-FPM neu, oder führen Sie unter Velocity `./console exp:velocity deploy` aus.

## 5. Richtlinien

Die Auswahl wird nur einem Benutzer angezeigt, der `uiswitch/<provider>` besitzt, für die Sprache
`uiswitch/language`. Die Richtlinie hat keine Einschränkungen: Eine Rolle hat sie oder nicht.

- **Administrator** hat sie über seine Richtlinie für jedes Modul.
- **Editor** hat sie in neuen Installationen (saubere Daten und die Multi-Site-Installer).

Um sie einer Rolle zu geben:

1. Öffnen Sie *Benutzerkonten > Rollen und Richtlinien* und die Rolle, dann *Bearbeiten*.
2. *Neue Richtlinie*, Modul **uiswitch**, Funktion **language**.
3. Es gibt keine Einschränkungen zu wählen; speichern Sie die Richtlinie und die Rolle.

Die Richtlinie wird bei jeder Anfrage geprüft; wird sie entfernt, wirkt die gespeicherte Wahl eines Benutzers ab der
nächsten Seite nicht mehr und wird aus der Sitzung entfernt. Ein Provider, den eine Extension hinzufügt, erhält eine
eigene Funktion im selben Modul (`uiswitch/<identifier>`), die aufgeführt wird, sobald `ProviderList[]` ihn nennt.

## 6. Prüfen

- Melden Sie sich als Benutzer mit der Richtlinie an und öffnen Sie eine Seite des Admin-Siteaccess: Der Kasten
  *Oberfläche* steht nach den Lesezeichen.
- `<admin url>/uiswitch/state` antwortet mit JSON: dem Siteaccess und, für jeden Provider, den der Benutzer verwenden
  darf, dem Bezeichner, dem Titel, dem aktuellen Wert und den Werten mit ihren Bezeichnungen.
- Nach einer Änderung führt die Audit-Konsole ein Ereignis `access.uiswitch.change` auf.

## Siehe auch

- [Die Sprache der Oberfläche](../../eng-us/features/6.0/ui-switch.md): was Benutzer sehen.
- [Die Liste der Oberflächensprachen](../../eng-us/features/6.0/ui-switch-languages.md): die Gruppen, die Reihenfolge von A bis Z,
  die Abdeckung der Übersetzungen und warum die Standardrangfolge so ist, wie sie ist.
- [Die Provider-API der UI-Umschaltung](../../eng-us/bc/6.0/ui-switch.md): eine Wahl aus einer Extension hinzufügen.
- [Rollen und Richtlinien](rollen-und-richtlinien.md), [Caches](caches.md), [Inhaltssprachen](inhaltssprachen.md).
