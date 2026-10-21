# Das Administrationsdesign für Ihre Sitzung wählen

Diese Seite ist für Redakteure und Administratoren, die in der Administrationsoberfläche arbeiten. Ein angemeldeter
Benutzer mit der Richtlinie `uiswitch/design` kann in der rechten Werkzeugleiste wählen, in welchem
Administrationsdesign er arbeitet, zum Beispiel **Admin 4 (layouts)** (`admin4l`), **Admin 4** (`admin4`), **AdminUI**
(`adminui`), **Admin 3** (`admin3`), das Design **Classic** (`admin`) oder **Classic (server-rendered)**
(`admin_classic`). Die Wahl ändert die Oberfläche nur für diesen Benutzer und nur für den Siteaccess, auf dem sie
getroffen wurde. Seit dem 15. Oktober 2026.

Die Wahl des Designs ist nach der Sprache der Oberfläche der zweite Provider des [UI-Umschalters](ui-umschalter.md). Die
Einstellungen werden in [der Anleitung](../../anleitungen/ui-umschalter-design.md) erklärt, die Änderungen am Code in
[den Hinweisen für Entwickler](../../../eng-us/bc/6.0/ui-switch-design.md).

## Kurz gesagt

- **Wo:** in der rechten Werkzeugleiste eines Administrations-Siteaccess, im Werkzeug **Oberfläche**
  (`admin_ui_switch`), unter der Sprache der Oberfläche. Nicht auf öffentlichen Siteaccesses und nicht beim Bearbeiten
  oder beim Auswählen eines Elements.
- **Wer:** ein angemeldeter Benutzer, dessen Rolle die Richtlinie `uiswitch/design` hat. Die Berechtigung wird bei jeder
  Anfrage erneut geprüft: Ein Benutzer, der sie verliert, ist bei der nächsten Seite wieder im eigenen Design des
  Siteaccess.
- **Was sich ändert:** nur für die Anfragen dieses Benutzers werden `site.ini [DesignSettings] SiteDesign` und
  `AdditionalSiteDesignList[]` im Speicher auf das gewählte Design und seine Rückfallkette gesetzt. Templates,
  Stylesheets und Skripte folgen, weil jedes Design seine eigenen nennt.
- **Was sich nicht ändert:** andere Benutzer, die Inhalte, die Einstellungsdateien, andere Siteaccesses und die
  öffentliche Website.
- **Caches:** jedes Design bekommt eigene Caches. Niemandes Caches werden geleert, wenn ein Benutzer umschaltet.
- **Wie lange:** bis zur Abmeldung. Mit `RememberPreference=enabled` wird die Wahl auch in den Benutzereinstellungen
  gespeichert und kommt bei der nächsten Anmeldung zurück.
- **Die Wahl des eigenen Designs des Siteaccess** hebt die Umschaltung auf.

## Verwenden

1. Melden Sie sich in der Administrationsoberfläche an (zum Beispiel `/admin`).
2. Suchen Sie in der rechten Werkzeugleiste das Werkzeug **Oberfläche**. Es führt zuerst die Sprache der Oberfläche auf,
   dann **Administrationsdesign** mit einer Schaltfläche pro Design, das Sie wählen dürfen. Das aktuelle Design ist
   markiert.
3. Wählen Sie ein Design. Die Seite wird in diesem Design neu geladen, auf derselben Seite.
4. Um zurückzugehen, wählen Sie das erste Design der Liste: das eigene Design des Siteaccess. Die Umschaltung wird
   aufgehoben.

Jedes Design, das angeboten werden kann, gibt dasselbe Werkzeug in seiner rechten Werkzeugleiste aus, sodass Sie immer
wieder umschalten können: admin4 und admin4l zeigen es in der rechten Seitenleiste, das klassische Design `admin` (und
admin2 und admin3, die darauf zurückgreifen) zeigt es als klassischen Kasten, der ohne JavaScript funktioniert. Das
klassische Design zeigt es in seinem rechten Menü, das offen ist, sofern der Benutzer es nicht geschlossen hat
(`admin_right_menu_show=0`), wie in admin4, sodass der Weg zurück erreichbar bleibt.

Das Werkzeug erscheint nur, wenn es etwas zu wählen gibt: mindestens zwei Designs und eine Rolle mit `uiswitch/design`.

## Welche Designs angeboten werden

Die Liste kommt aus `uiswitch.ini [UISwitch_design] AllowedValues[]`, in dieser Reihenfolge, nach dem eigenen Design des
Siteaccess, das immer zuerst kommt. Ein Design wird nur angeboten, wenn:

- sein Name ein Design-Name ist (Kleinbuchstaben, Ziffern und `_`, mit einem Buchstaben beginnend);
- es eine Rückfallkette hat, `DesignChain[<design>]` (eine leere Kette zählt);
- das Design installiert ist: `design/<design>` existiert, oder `design/<design>` einer aktiven Design-Extension
  (`design.ini [ExtensionSettings] DesignExtensions[]`).

AdminUI wird auf einem Siteaccess also nur angeboten, wenn die Extension `exp_adminui` dort aktiv ist
(`ActiveAccessExtensions[]=exp_adminui`). Auf einer Standardinstallation ist das nur der Siteaccess `adminui`, auf dem
AdminUI das Standard-Design ist und die anderen Designs angeboten werden. Ein Administrator, der AdminUI auf den
Siteaccesses `admin` und `editor` anbieten möchte, aktiviert die Extension dort (siehe
[die Anleitung](../../anleitungen/ui-umschalter-design.md#adminui-in-anderen-siteaccesses-anbieten)). Ebenso wird `admin_classic` nur
dort angeboten, wo `sevenx_themes_admin_classic` aktiv ist. Ein später deinstalliertes Design verschwindet aus der Liste,
und ein Benutzer, der es gewählt hatte, ist bei der nächsten Anfrage wieder im eigenen Design des Siteaccess.

Die mitgelieferte Liste, mit den in der Werkzeugleiste gezeigten Beschriftungen:

| Design | Beschriftung | Rückfallkette |
|---|---|---|
| `admin4l` | Admin 4 (layouts) | admin4, admin3, admin2, admin |
| `admin4` | Admin 4 | admin3, admin2, admin |
| `adminui` | AdminUI | admin4l, admin4, admin3, admin2, admin |
| `admin3` | Admin 3 | admin2, admin |
| `admin` | Classic | keine (das klassische Design und das Standard-Design) |
| `admin_classic` | Classic (server-rendered) | admin (braucht `sevenx_themes_admin_classic`) |

Die Beschriftungen werden im Kontext `kernel/uiswitch` übersetzt. Ein Design ohne `DesignLabel[]` zeigt seinen Namen.

## Was sich genau ändert

Hat der Benutzer ein Design gewählt, prüft der Kernel früh in jeder Anfrage (nach dem Start der Sitzung, bevor
Übersetzungen, Locale, Design und irgendein Template eingerichtet werden):

1. ob der Benutzer angemeldet ist, noch `uiswitch/design` hat und das gespeicherte Design noch angeboten wird;
2. setzt `SiteDesign` auf das gewählte Design und `AdditionalSiteDesignList[]` auf seine Kette, in der eigenen Kopie der
   `site.ini` der Anfrage. Es wird keine Einstellungsdatei geschrieben und nichts geändert, was länger lebt als die
   Anfrage;
3. vergisst die Design-Informationen, die er vielleicht schon im Speicher aufgebaut hat, sodass durchgehend die neue
   Liste verwendet wird.

Alles andere bleibt, wie es für den Siteaccess konfiguriert ist: seine Inhaltssprachen, seine Werkzeugleisten
(`toolbar.ini`), seine Richtlinien, seine Menüs, seine Moduleinstellungen. Eine auf einem Administrations-Siteaccess
getroffene Wahl gilt nicht auf einem anderen; die Sitzung hält eine Menge von Wahlen pro Siteaccess.

Der Einrichtungsassistent wird nie umgeschaltet.

## Caches

Jedes Design gibt anderes Markup aus, daher hat jedes eigene Caches: den Cache der Design-Orte, kompilierte Templates,
Inhalte von `{cache-block}`, den Inhaltsansichts-Cache und den Cache der Template-Überschreibungen. Benutzer, die nicht
umgeschaltet haben, verwenden genau die Cache-Dateien, die sie vorher verwendeten; für sie wird nichts geleert.

Eine mit einer Umschaltung ausgegebene Seite wird nicht im rollenbewussten HTTP-Seitencache oder im Antwort-Cache von
Velocity gespeichert, sodass ein Kollege nie eine Seite im Design eines anderen erhält.

Die Extension `sevenx_themes_admin_classic` vor 0.2.5 leerte bei jeder Umschaltung die Template- und Template-Block-Caches
der ganzen Installation. Das ist weg.

## Sicherheit und das Prüfprotokoll

- Eine Umschaltung ist ein POST an `uiswitch/set` mit dem Formular-Token. Eine Anfrage von einer anderen Website, ein
  Design, das nicht angeboten wird (400), oder ein Benutzer ohne die Richtlinie (403) wird abgelehnt. Der Seite, zu der
  zurückgekehrt wird, wird nur gefolgt, wenn sie ein lokaler Pfad ist.
- Jede Änderung wird in das [Prüfprotokoll](../../../eng-us/features/6.0/audit-trail.md) als `access.uiswitch.change` geschrieben, mit dem Provider
  `design`, dem Wert vorher und nachher und dem Siteaccess.

## Abschalten

- Für alle: in `settings/override/uiswitch.ini.append.php`

  ```ini
  [UISwitch_design]
  Enabled=false
  ```

  Kein Design wird angeboten oder angewendet, und jede Seite und jede Cache-Datei ist so, wie sie ohne die Funktion war.
  Die Sprache der Oberfläche bleibt verfügbar. `[UISwitchSettings] Enabled=false` schaltet den ganzen UI-Umschalter ab.
- Für eine Gruppe von Benutzern: Geben Sie ihrer Rolle nicht `uiswitch/design`.
- Für ein Design: Lassen Sie es aus `AllowedValues[]` heraus oder entfernen Sie seinen Eintrag `DesignChain[]`.

Siehe [die Anleitung](../../anleitungen/ui-umschalter-design.md#7-aus--und-einschalten) für die Einzelheiten.

## Verwandte Seiten

- [UI-Umschalter](ui-umschalter.md): das Framework und die Sprache der Oberfläche
- [Anleitung: der Umschalter des Administrationsdesigns](../../anleitungen/ui-umschalter-design.md)
- [Hinweise für Entwickler: der Umschalter des Administrationsdesigns](../../../eng-us/bc/6.0/ui-switch-design.md)
- [Das Design admin4](../../../eng-us/features/6.0/admin4-design.md), [admin4l](admin4l.md), [Exponential Admin UI](exp-adminui.md)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
