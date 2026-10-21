# Die Umschaltung des Administrationsdesigns: Einstellungen, Designs und Rollen

Diese Anleitung richtet sich an Administratoren und Entwickler, die die Wahl des Administrationsdesigns pro Sitzung
einrichten ([die Funktion](../../eng-us/features/6.0/ui-switch-design.md)). Sie lesen die Einstellungen, fügen der Liste ein
installiertes Design hinzu, stellen sicher, dass jedes angebotene Design dem Benutzer das Zurückschalten erlaubt, geben
einer Rolle die Berechtigung, testen das Ergebnis und lösen die üblichen Probleme. Das dauert etwa zwanzig Minuten.

Alles Folgende wurde am Code geprüft: `kernel/classes/uiswitch/expuiswitchdesignprovider.php`, das Register der
UI-Umschaltung `kernel/classes/uiswitch/expuiswitch.php` und `eZTemplateDesignResource::designBaseCachePath()` in
`kernel/common/eztemplatedesignresource.php`. Das Grundgerüst und die Sprache der Oberfläche stehen in
[Einstellungen der UI-Umschaltung](ui-umschalter-einstellungen.md).

[Anleitungen](README.md) · Verwandte Anleitungen: [Einstellungen der UI-Umschaltung](ui-umschalter-einstellungen.md),
[Templates und Design](templates-und-design.md), [Rollen und Richtlinien](rollen-und-richtlinien.md), [Caches](caches.md)

## Kurz gesagt

- Die Umschaltung des Designs ist der Provider `design` von `settings/uiswitch.ini`: `[UISwitchSettings] ProviderList[]=design`
  (nach `language`) und die Gruppe `[UISwitch_design]`.
- `AllowedValues[]` führt die Designs in der Reihenfolge der Werkzeugleiste auf; `DesignChain[<design>]` gibt jedem
  seine Fallback-Designs; `DesignLabel[<design>]` seinen Namen in der Werkzeugleiste. Ein Design ohne Kette oder ein
  nicht installiertes Design wird nicht angeboten.
- Jedes angebotene Design muss das Werkzeug `admin_ui_switch` der rechten Werkzeugleiste darstellen, sonst kann ein
  Benutzer, der es gewählt hat, nicht zurückschalten.
- Benutzer brauchen die Richtlinie `uiswitch/design`.
- Für eine Umschaltung muss kein Cache geleert werden. Nach einer Änderung der Einstellungen leeren Sie den INI-Cache
  und stellen wie gewohnt bereit.

## 1. Die Einstellungen

Bearbeiten Sie niemals `settings/uiswitch.ini`. Überschreiben Sie sie in `settings/override/uiswitch.ini.append.php`,
in `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` eines Siteaccess oder in
`settings/uiswitch.ini.append.php` einer Extension.

Die mitgelieferte Gruppe:

```ini
[UISwitchSettings]
ProviderList[]=design

[UISwitch_design]
Class=expUISwitchDesignProvider
Enabled=true
AllowedValues[]
AllowedValues[]=admin4l
AllowedValues[]=admin4
AllowedValues[]=adminui
AllowedValues[]=admin3
AllowedValues[]=admin
AllowedValues[]=admin_classic
DesignChain[]
DesignChain[admin4l]=admin4;admin3;admin2;admin
DesignChain[admin4]=admin3;admin2;admin
DesignChain[adminui]=admin4l;admin4;admin3;admin2;admin
DesignChain[admin3]=admin2;admin
DesignChain[admin]=
DesignChain[admin_classic]=admin
DesignLabel[]
DesignLabel[admin4l]=Admin 4 (layouts)
DesignLabel[admin4]=Admin 4
DesignLabel[adminui]=AdminUI
DesignLabel[admin3]=Admin 3
DesignLabel[admin]=Classic
DesignLabel[admin_classic]=Classic (server-rendered)
RememberPreference=disabled
```

| Einstellung | Bedeutung |
|---|---|
| `Class` | Die Provider-Klasse. Lassen Sie sie unverändert. |
| `Enabled` | `true` (Standard) oder `false`. `false` (auch `disabled` oder `0`) schaltet den Design-Provider ab; die Sprache der Oberfläche bleibt. |
| `AllowedValues[]` | Die Designs, die ein Benutzer wählen darf, in der Reihenfolge der Werkzeugleiste. Das eigene `SiteDesign` des Siteaccess wird immer angeboten, und zwar zuerst, ob es aufgeführt ist oder nicht; wer es wählt, hebt die Umschaltung auf. Jedes Design wird einmal aufgeführt. |
| `DesignChain[<design>]` | Die Fallback-Designs, mit denen die Wahl läuft, getrennt durch `;`, das speziellste zuerst. Sie werden für die Anfragen des Benutzers zu `AdditionalSiteDesignList[]`. Das Design selbst und Namen, die keine Designnamen sind, werden ausgelassen, und ein doppelt aufgeführter Name zählt einmal. `DesignChain[admin]=` (leer) ist eine gültige Kette: nur das Design plus das Standard-Design. **Ein Design ohne Eintrag `DesignChain[]` wird nicht angeboten.** |
| `DesignLabel[<design>]` | Der Name in der Werkzeugleiste, übersetzt im Kontext `kernel/uiswitch`. Ohne ihn zeigt die Werkzeugleiste den Designnamen. Legen Sie auch einen für das eigene Design Ihres Siteaccess an (zum Beispiel `DesignLabel[editor]=Editor`), da es immer die erste Wahl ist. |
| `RememberPreference` | `disabled` (Standard): Die Wahl endet mit der Sitzung, beim Abmelden. `enabled`: Sie wird außerdem in den Voreinstellungen des Benutzers gespeichert (`exp_uiswitch_design`) und bei der nächsten Anmeldung angewendet, solange die Sitzung keine Wahl enthält. |

Wo die Umschaltung überhaupt wirkt (in welchen Siteaccesses), legt `[UISwitchSettings] SiteAccessList[]` fest; leer
bedeutet jeder Siteaccess, dessen Designliste ein Administrationsdesign enthält. Siehe
[Einstellungen der UI-Umschaltung](ui-umschalter-einstellungen.md).

Ein Design wird nur angeboten, wenn alle diese Bedingungen erfüllt sind:

1. Es ist in `AllowedValues[]` aufgeführt (oder es ist das eigene Design des Siteaccess);
2. sein Name passt zu `[a-z][a-z0-9_]*` (höchstens 64 Zeichen);
3. es hat einen Eintrag `DesignChain[]`;
4. `design/<design>` existiert, oder `design/<design>` einer Extension, die in
   `design.ini [ExtensionSettings] DesignExtensions[]` aufgeführt ist.

Nichts, was ein Benutzer sendet, wird als Pfad verwendet: Ein Wert wird nur angenommen, wenn er genau einem der
angebotenen Designs entspricht.

## 2. Ein installiertes Design hinzufügen

Als Beispiel dient das klassische, serverseitig gerenderte Design `admin_classic` der Extension
`sevenx_themes_admin_classic` (0.2.5 oder neuer). Sein Eintrag steht bereits in den obigen Standardwerten des Kernels
(`AllowedValues[]=admin_classic`, `DesignChain[admin_classic]=admin`,
`DesignLabel[admin_classic]=Classic (server-rendered)`), daher wird das Design angeboten, sobald sein Verzeichnis
existiert: Die Extension registriert es in `settings/design.ini.append.php`
(`DesignExtensions[]=sevenx_themes_admin_classic`). Es genügt, die Extension zu aktivieren. Die eigene
`settings/uiswitch.ini.append.php` der Extension wiederholt dieselben drei Werte; das schadet nicht, ein doppelt
aufgeführtes Design wird einmal angeboten.

### AdminUI in anderen Siteaccesses anbieten

`adminui` wird in einem Siteaccess nur angeboten, wenn dort die Extension `exp_adminui` aktiv ist, denn ihr
Designverzeichnis wird von dieser Extension registriert. In einer Standardinstallation ist das nur der Siteaccess
`adminui` (`ActiveAccessExtensions[]=exp_adminui`), in dem AdminUI das Standarddesign ist und die anderen Designs
angeboten werden.

Um AdminUI auch in den Siteaccesses `admin` und `editor` anzubieten, aktivieren Sie die Extension jeweils in
`settings/siteaccess/<siteaccess>/site.ini.append.php`:

```ini
[ExtensionSettings]
ActiveAccessExtensions[]=exp_adminui
```

Damit gelangen auch die globalen Einstellungen der Extension in diesen Siteaccess: `design.ini [ExtensionSettings]
DesignExtensions[]=exp_adminui`, `exp_adminui.ini` und die Klassensymbole ihrer `settings/override/icon.ini.append.php`
(die eigenen Symbolzuordnungen anderer Extensions haben weiterhin Vorrang). Ihre Einstellungen für den Siteaccess
`adminui` (`settings/siteaccess/adminui/`: Übersetzungen, Menüs und der Rest) gelten nicht. Leeren Sie anschließend
den INI-Cache und stellen Sie bereit (Abschnitt 6).

Für ein eigenes Design:

1. Legen Sie es in `design/<name>` ab oder in `extension/<extension>/design/<name>`, wobei die Extension in
   `DesignExtensions[]` steht.
2. Fügen Sie `AllowedValues[]=<name>`, `DesignChain[<name>]=...` und `DesignLabel[<name>]=...` in einem Override oder
   in der `settings/uiswitch.ini.append.php` Ihrer Extension hinzu. Beenden Sie die Kette mit einem Design, das das
   Werkzeug zum Umschalten hat (Abschnitt 3); `admin` beendet jede mitgelieferte Kette.
3. Nehmen Sie die Bezeichnung im Kontext `kernel/uiswitch` in Ihre Übersetzungen auf, wenn sie übersetzt werden soll.
4. Leeren Sie den INI-Cache und stellen Sie bereit (Abschnitt 6).

Schreiben Sie in einer Extension kein `AllowedValues[]` ohne Wert: Eine leere Zeile `AllowedValues[]` leert die zuvor
gelesene Liste, sodass die Designs des Kernels verschwinden würden.

## 3. Jedes angebotene Design braucht das Werkzeug zum Umschalten

Ein Benutzer, der zu einem Design wechselt, muss die Umschaltung in diesem Design wiederfinden, sonst kann er nicht
zurück, bis er sich abmeldet. Das Werkzeug ist `admin_ui_switch` der Werkzeugleiste, Template
`toolbar/full/admin_ui_switch.tpl`:

| Design | Woher das Template kommt |
|---|---|
| `admin4`, `admin4l` | `design/admin4/templates/toolbar/full/admin_ui_switch.tpl` (rechte Seitenleiste) |
| `adminui` | über seine Kette (`admin4l`, `admin4`) |
| `admin`, sowie `admin2`, `admin3`, `admin_classic` über ihre Ketten | `design/admin/templates/toolbar/full/admin_ui_switch.tpl` mit `design/admin/templates/uiswitch/choices.tpl`: ein Formular pro Wahl, funktioniert ohne JavaScript |
| ein Design einer Extension | über seine Kette; jede mitgelieferte Kette endet mit `admin` |

Das Pagelayout des Designs muss außerdem die rechte Werkzeugleiste darstellen (`{tool_bar name='admin_right' view='full'}`;
das klassische Pagelayout tut das, solange das rechte Menü angezeigt wird). Das klassische Pagelayout
(`design/admin/templates/pagelayout.tpl`) zeigt das rechte Menü, sofern der Benutzer es nicht geschlossen hat
(`admin_right_menu_show=0`), wie es auch admin4 tut, sodass die Umschaltung erreichbar bleibt. Die `toolbar.ini` des
Siteaccess muss das Werkzeug dort platzieren:

```ini
[Tool]
AvailableToolArray[]=admin_ui_switch

[Toolbar_admin_right]
Tool[]=admin_ui_switch
```

Die mitgelieferten Siteaccesses admin, editor und adminui haben es. Ein Siteaccess, dessen eigenes Design das
klassische ist (zum Beispiel einer mit `SiteDesign=admin`), braucht diese Zeilen in seiner
`settings/siteaccess/<siteaccess>/toolbar.ini.append.php`, um die Umschaltung anzuzeigen. Die Werkzeugleiste kommt aus
dem Siteaccess, nicht aus dem gewählten Design, daher genügt ein Eintrag für jedes Design.

Das Werkzeug ist beim Bearbeiten und Durchsuchen ausgeblendet, und es ist leer für einen Benutzer ohne jede
Richtlinie `uiswitch/*`.

## 4. Die Berechtigung vergeben

1. Gehen Sie zu **Benutzer > Rollen und Richtlinien** und öffnen Sie die Rolle (zum Beispiel **Editor**).
2. **Bearbeiten**, **Neue Richtlinie**, Modul **uiswitch**, Funktion **design**, und speichern.
3. Weisen Sie die Rolle wie gewohnt zu.

Die Funktion `uiswitch/design` existiert, weil `design` in `ProviderList[]` steht; sie nimmt keine Einschränkungen an.
Eine Rolle mit `uiswitch/*` (oder `*/*`, wie Administrator sie hat) enthält sie bereits. Die Berechtigung wird bei
jeder Anfrage geprüft, daher wirkt das Entfernen der Richtlinie bei der nächsten Seite des Benutzers.

## 5. Testen

1. Melden Sie sich mit einem Testbenutzer an, der `uiswitch/design` besitzt (nicht mit dem gemeinsam genutzten
   Administratorkonto, dessen Voreinstellungen die Arbeitsumgebung eines anderen sind).
2. In der rechten Werkzeugleiste, **Oberfläche**, **Administrationsdesign**: Wählen Sie **Admin 4**. Die Seite wird in
   admin4 neu geladen.
3. Wählen Sie **Klassisch**. Die Seite wird im klassischen Design neu geladen, und der Kasten **Oberfläche** steht in
   dessen rechtem Menü.
4. Wählen Sie das erste Design (das eigene des Siteaccess). Die Umschaltung ist aufgehoben.
5. Melden Sie sich in einem zweiten Browser als anderer Benutzer an: Die Oberfläche ist durchgehend das eigene Design
   des Siteaccess.
6. Öffnen Sie den Reiter **Audit** (`/audit/dashboard`) und suchen Sie nach `access.uiswitch.change`: ein Eintrag pro
   Änderung, mit dem Provider `design` und den Werten davor und danach.
7. Melden Sie sich ab und wieder an: das eigene Design des Siteaccess (außer bei `RememberPreference=enabled`).

In einer Shell liefert `uiswitch/state` (GET, angemeldet) die Provider, die der Benutzer verwenden darf, ihre Designs
und das aktuelle Design als JSON.

Die Entwicklertests laufen ohne Datenbank:

```bash
php vendor/bin/phpunit tests/tests/kernel/classes/uiswitch/expUISwitchDesignProviderTest.php
```

## 6. Eine Änderung der Einstellungen bereitstellen

Eine Umschaltung selbst erfordert kein Leeren von Caches: Jedes Design hat eigene Caches. Nach einer Änderung von
`uiswitch.ini` oder `toolbar.ini` leeren Sie den INI-Cache und stellen bereit:

```bash
./console exp:velocity deploy --allow-root-user
```

oder von Hand mit `php bin/php/ezcache.php --clear-tag=ini --allow-root-user`, einem Reload des PHP-FPM, der die Site
ausliefert, und `exp:velocity restart` für Velocity. Eine neue Provider-Klasse braucht neu erzeugte Autoloads, bevor
die Einstellungen sie nennen (`deploy --kernel` für eine Kernel-Klasse). Ein globales Leeren der Caches ist nicht nötig.

## 7. Aus- und einschalten

| Ziel | Einstellung |
|---|---|
| Nirgends eine Umschaltung des Designs | `[UISwitch_design] Enabled=false` in `settings/override/uiswitch.ini.append.php` |
| Keine Umschaltung des Designs in einem Siteaccess | dasselbe in `settings/siteaccess/<siteaccess>/uiswitch.ini.append.php` |
| Überhaupt keine UI-Umschaltung (auch keine Sprache) | `[UISwitchSettings] Enabled=false` |
| Nicht für bestimmte Benutzer | geben Sie ihrer Rolle nicht `uiswitch/design` |
| Ein Design weniger | lassen Sie es in `AllowedValues[]` weg (schreiben Sie die Liste neu, beginnend mit einem leeren `AllowedValues[]`) |

Im ausgeschalteten Zustand wird keine Einstellung im Speicher geändert, keine Cache-Variante verwendet, und jeder
Name einer Cache-Datei ist derselbe wie ohne die Funktion. Eine gespeicherte Wahl wird ignoriert; mit `[UISwitch_design] Enabled=false` wird sie bei der nächsten Anfrage außerdem aus der Sitzung entfernt.

## 8. Fehlerbehebung

| Problem | Ursache und Lösung |
|---|---|
| Ein Design wird nicht angeboten | Es hat keinen Eintrag `DesignChain[<design>]`, sein Verzeichnis fehlt (`design/<design>` oder `design/<design>` einer Extension in `DesignExtensions[]`), die Extension ist in diesem Siteaccess nicht aktiv (`adminui` braucht `exp_adminui`, siehe Abschnitt 2), oder der Name enthält Großbuchstaben oder andere Zeichen als `[a-z0-9_]`. Prüfen Sie die aufgelösten Einstellungen unter **Setup > Ini Einstellungen** (`uiswitch.ini`, `design.ini`). |
| Die Auswahl **Administrationsdesign** fehlt ganz | Dem Benutzer fehlt `uiswitch/design`; es werden weniger als zwei Designs angeboten; `design` steht nicht in `ProviderList[]`; `Enabled=false`; der Siteaccess ist kein Siteaccess mit Umschaltung; oder die Klasse fehlt in den Autoloads (Debug-Warnung `UI switch provider 'design': class 'expUISwitchDesignProvider' not found`). |
| Das Werkzeug **Oberfläche** fehlt | `admin_ui_switch` steht nicht in `[Toolbar_admin_right] Tool[]` des Siteaccess, oder das rechte Menü wurde im klassischen Design geschlossen (`admin_right_menu_show=0`; öffnen Sie es mit dem Steuerelement zum Ein- und Ausblenden), oder Sie bearbeiten oder durchsuchen gerade. |
| Ein Benutzer kann nicht zurückschalten | Das gewählte Design hat in seiner Kette kein `toolbar/full/admin_ui_switch.tpl`. Fügen Sie `admin` ans Ende seiner `DesignChain[]` an. Um dem Benutzer zu helfen: abmelden (die Wahl endet mit der Sitzung), oder das Design aus `AllowedValues[]` entfernen (die gespeicherte Wahl entfällt bei der nächsten Anfrage). Mit `RememberPreference=enabled` wählen Sie nach der Anmeldung das Standarddesign. |
| Eine Seite sieht nach einer Änderung der Einstellungen halb alt, halb neu aus | Veralteter INI-Cache oder ein PHP-Prozess, der die alten Einstellungen behalten hat: Stellen Sie wie in Abschnitt 6 bereit. Ein globales Leeren der Caches ist nicht nötig. |
| Die Umschaltung liefert 400 | Das Design wird nicht angeboten (siehe erste Zeile). 403: Dem Benutzer fehlt die Richtlinie, das Formular-Token fehlt, oder die Anfrage kam von einer anderen Site. 404: Der Siteaccess hat keine Umschaltung. |
| Eine Seite im falschen Design bei einem Kollegen | Sollte nicht vorkommen: Seiten, die mit einer Umschaltung gerendert wurden, werden weder im HTTP-Seiten-Cache noch im Antwort-Cache von Velocity gespeichert, und jeder andere Cache ist je Design getrennt. Melden Sie es mit der URL und den Designs beider Benutzer. |

## Siehe auch

- [Funktion: das Administrationsdesign wählen](../../eng-us/features/6.0/ui-switch-design.md)
- [Hinweise für Entwickler: die Umschaltung des Administrationsdesigns](../../eng-us/bc/6.0/ui-switch-design.md)
- [UI-Umschaltung](../../eng-us/features/6.0/ui-switch.md) und [ihre Einstellungen](ui-umschalter-einstellungen.md)
- [Rollen und Richtlinien](rollen-und-richtlinien.md), [Caches](caches.md), [Templates und Design](templates-und-design.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
