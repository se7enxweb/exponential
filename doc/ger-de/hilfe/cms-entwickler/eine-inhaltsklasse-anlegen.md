---
title: Eine Inhaltsklasse anlegen
audience: cms-developer
category: cms-development
order: 13
summary: Für CMS-Entwickler eine Aufgabe von Anfang bis Ende. Legen Sie in der Administration eine Klasse an, erstellen Sie damit Inhalte, geben Sie ihr über einen Override ein Full-View-Template, lassen Sie eine Redakteursrolle sie anlegen und übertragen Sie sie in einem Paket in die nächste Installation.
keywords: Inhaltsklasse anlegen, neue Klasse, Klasseneditor, Klassengruppe, Attribut, Full-View-Template, Override, Richtlinie, content/create, Paket, Klassenexport, Aufgabe, Tutorial, create content class, new class, class editor, full view template, policy, package, class export
type: help
---
# Eine Inhaltsklasse anlegen

Diese Seite richtet sich an Entwickler, die ein Inhaltsmodell auf dem Papier haben und jetzt die erste Klasse bauen.
Sie begleitet eine Klasse, eine Veranstaltung, durch jeden Schritt, den ein echtes Projekt braucht: die Klasse, ein
erstes Objekt, ein Template, Berechtigungen und ein Paket. Arbeiten Sie zuerst in einer Entwicklungsinstallation
([Entwicklungsumgebung](entwicklungsumgebung.md)).

## Inhalt

1. [Was Sie brauchen](#was-sie-brauchen)
2. [Schritt 1: die Klasse anlegen](#schritt-1-die-klasse-anlegen)
3. [Schritt 2: ein Objekt erstellen](#schritt-2-ein-objekt-erstellen)
4. [Schritt 3: ihr ein Full-View-Template geben](#schritt-3-ihr-ein-full-view-template-geben)
5. [Schritt 4: Redakteure sie anlegen lassen](#schritt-4-redakteure-sie-anlegen-lassen)
6. [Schritt 5: die Klasse in andere Installationen übertragen](#schritt-5-die-klasse-in-andere-installationen-übertragen)
7. [Prüfungen](#prüfungen)
8. [Wenn etwas schiefgeht](#wenn-etwas-schiefgeht)

## Was Sie brauchen

- Ein Administratorkonto in einer Entwicklungsinstallation.
- Eine eigene Design-Erweiterung für Templates, zum Beispiel `extension/exp_mysite`
  ([Ihre eigene Design-Erweiterung](../zwei-wege-zum-bauen/eigene-design-erweiterung.md)).
- Die aufgeschriebene Klasse: Bezeichner `exp_event`, Attribute `title` (Textzeile, erforderlich), `intro`
  (Textabschnitt), `body` (XML Block), `start_date` (Datum und Zeit, erforderlich), `image` (Bild).

## Schritt 1: die Klasse anlegen

1. Öffnen Sie in der Administration **Setup** > **Klassen** (`/class/grouplist`).
2. Öffnen Sie die Klassengruppe **Content** und drücken Sie **Neue Klasse**.
3. Geben Sie den Namen `Event` und den Bezeichner `exp_event` ein.
4. Setzen Sie **Objekt Namensschema** auf `<title>`. Lassen Sie **URL Alias Namensschema** leer: Die URL wird aus dem
   Namen gebildet.
5. Lassen Sie **Behälter** ohne Häkchen: Veranstaltungen haben keine Unterelemente.
6. Wählen Sie unter der Attributliste **Textzeile** und drücken Sie **Attribut hinzufügen**. Nennen Sie es `Title`,
   Bezeichner `title`, und setzen Sie die Häkchen bei **Erforderlich** und **Durchsuchbar**.
7. Fügen Sie die übrigen Attribute ebenso hinzu: `Intro` (Textabschnitt, `intro`), `Body` (XML Block, `body`),
   `Start date` (Datum und Zeit, `start_date`, erforderlich), `Image` (Bild, `image`).
8. Drücken Sie **OK**. Die Klasse wird gespeichert und erscheint in der Liste der Gruppe.

Bis Sie **OK** drücken, ist die Klasse eine temporäre Version, die niemand sonst sieht; **Abbrechen** verwirft sie.
[Das Inhaltsmodell und das Bearbeiten von Inhalten, Abschnitt 2](../../anleitungen/inhaltsmodell-und-bearbeitung.md#2-eine-klasse-anlegen)
enthält dieselben Schritte mit Bildschirmfotos der Felder.

## Schritt 2: ein Objekt erstellen

1. Öffnen Sie **Inhaltsstruktur**, wählen Sie den Ordner, in den die Veranstaltungen kommen, wählen Sie im Menü zum
   Anlegen **Event** und drücken Sie **Hier erstellen**.
2. Füllen Sie die Felder aus und drücken Sie **Zur Veröffentlichung schicken**.
3. Öffnen Sie die neue Seite auf dem öffentlichen Siteaccess. Ohne eigenes Template zeichnet sie die allgemeine Full
   View des Designs, die die Attribute auflistet.

Legen Sie jetzt ein paar Veranstaltungen mit echten Inhalten an: Sie sind Ihre Testdaten für Templates und Fetches.

## Schritt 3: ihr ein Full-View-Template geben

Fügen Sie Ihrer Design-Erweiterung eine Override-Regel hinzu, nur für den öffentlichen Siteaccess. In
`extension/exp_mysite/settings/siteaccess/<public siteaccess>/override.ini.append.php`:

```ini
<?php /* #?ini charset="utf-8"?

[exp_mysite_full_event]
Source=node/view/full.tpl
MatchFile=full/exp_event.tpl
Subdir=templates
Match[class_identifier]=exp_event
Priority=10
*/ ?>
```

Dann das Template, `extension/exp_mysite/design/<your design>/override/templates/full/exp_event.tpl`:

```smarty
<article class="exp-event">
    <h1>{$node.name|wash()}</h1>
    <p class="exp-event__date">
        {$node.data_map.start_date.content.timestamp|l10n( 'shortdatetime' )}
    </p>
    {if $node.data_map.image.has_content}
        {attribute_view_gui attribute=$node.data_map.image image_class='large'}
    {/if}
    {if $node.data_map.intro.has_content}
        <p class="exp-event__intro">{$node.data_map.intro.content|wash()}</p>
    {/if}
    {attribute_view_gui attribute=$node.data_map.body}
</article>
```

Styles gehören in das Stylesheet Ihres Designs, geladen über `[StylesheetSettings]`, nie in das Template
([Kein Inline-CSS](../designer/theme-design/kein-inline-css.md)). Leeren Sie die Caches, die Einstellungen und
Override-Zuordnungen halten, und laden Sie dann die Seite neu:

```bash
php bin/php/ezcache.php --clear-tag=ini --allow-root-user
php bin/php/ezcache.php --clear-id=template-override,content --allow-root-user
```

Mehr zu Regeln: [Templates und Overrides für CMS-Entwickler](templates-und-overrides-fuer-cms-entwickler.md).

## Schritt 4: Redakteure sie anlegen lassen

1. Öffnen Sie **Benutzer** > **Rollen und Richtlinien** und bearbeiten Sie die Rolle der Redakteure (oder legen Sie
   eine an).
2. **Neue Richtlinie**: Modul `content`, Funktion `create`. Schränken Sie **Klasse** auf `Event` und **Teilbaum**
   (oder **Knoten**) auf den Veranstaltungsordner ein.
3. Fügen Sie `content/edit` hinzu, eingeschränkt auf `Event` und **Eigentümer**, wenn Redakteure nur ihre eigenen
   Veranstaltungen ändern sollen.
4. Speichern Sie und weisen Sie die Rolle der Benutzergruppe der Redakteure zu.

Melden Sie sich als Testredakteur an (mit einer Adresse unter `example.invalid`) und prüfen Sie, dass **Event** im
Veranstaltungsordner angeboten wird und sonst nirgends. Details:
[Rollen, Richtlinien und nicht aktivierte Benutzer](../../anleitungen/rollen-und-richtlinien.md).

## Schritt 5: die Klasse in andere Installationen übertragen

Die Klasse existiert jetzt nur in dieser Datenbank. Bevor Sie weiterbauen, bringen Sie sie in Dateien:

1. **Setup** > **Pakete** > **Neues Paket erstellen** > **Export von Inhaltsklassen**.
2. Wählen Sie `Event` und geben Sie dann Name, Zusammenfassung, Version und Lizenz ein. Das Paket wird in das lokale
   Repository geschrieben.
3. Exportieren Sie die Paketdatei und committen Sie sie in Ihr Projekt, oder bauen Sie ein Seitenpaket, das sie
   installiert ([Seitenpakete und Installer](seitenpakete-und-installer.md)).

Templates und Einstellungen sind bereits Dateien in Ihrer Erweiterung; mit dem Paket erhält eine neue Installation die
ganze Funktion aus Ihrem Repository. [Pakete](../../anleitungen/pakete.md) erklärt Installation und Aktualisierung von
Paketen.

## Prüfungen

- [ ] Die Klasse hat den geplanten Bezeichner; jeder Attributbezeichner ist kleingeschrieben und dort
      wiederverwendet, wo er es sein soll.
- [ ] Ein Objekt lässt sich veröffentlichen, und seine Seite zeigt Ihr Template (mit eingeschaltetem
      `ShowUsedTemplates` listet die Debug-Ausgabe `full/exp_event.tpl`;
      [Debug-Ausgabe und Logs](../erweiterungsentwicklung/teil-1-vom-benutzer-zum-entwickler/debug-ausgabe-und-logs-fuer-entwickler.md)).
- [ ] Ein Testredakteur kann Veranstaltungen nur im richtigen Ordner anlegen.
- [ ] Die Klasse liegt in einem Paket in Ihrem Repository.

## Wenn etwas schiefgeht

| Symptom | Ursache und Abhilfe |
|---|---|
| Das neue Template wird nicht verwendet | die Regel steht in der Datei eines anderen Siteaccess, der INI-Cache wurde nicht geleert, oder eine Regel mit niedrigerer `Priority` passt zuerst ([Reihenfolge der Overrides](../designer/vorlagen/override-reihenfolge.md)) |
| Die Seite ist leer, nachdem das Template hinzugefügt wurde | der Template-Override-Cache: leeren Sie `template-override` und `content` |
| "Event" fehlt im Menü zum Anlegen | der übergeordnete Knoten ist kein Behälter, oder die Rolle hat dort kein `content/create` für die Klasse |
| Ein Bildattribut zeigt nichts | die Bildvariante in `image_class` existiert in `image.ini` nicht ([Bilder und Bildvarianten](bilder-und-bildvarianten.md)) |

## Verwandte Seiten

- [Inhaltsklassen und Attribute](inhaltsklassen-und-attribute.md)
- [Ansichtsmodi](ansichtsmodi.md)
- [Tutorial: eine Full View überschreiben](../designer/theme-design/tutorial-eine-vollansicht-ueberschreiben.md): die Fassung für Designer
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
