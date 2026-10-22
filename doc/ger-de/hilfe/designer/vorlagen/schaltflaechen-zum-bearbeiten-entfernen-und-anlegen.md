---
title: Schaltflächen zum Bearbeiten, Entfernen und Anlegen auf der Website
audience: designer, developer
category: templates
order: 725
summary: Schaltflächen auf öffentlichen Seiten, die das Bearbeitungsformular öffnen, ein Element entfernen oder darunter ein neues anlegen - die Formularfelder, die sie an content/action senden, die Rechteprüfungen, die entscheiden, wann sie erscheinen, und wo der Benutzer danach landet.
keywords: Bearbeiten-Schaltfläche, Entfernen-Schaltfläche, Hier erstellen, Bearbeiten auf der Website, Rechteprüfung, Weiterleitung, edit button, remove button, create here, NewButton, EditButton, ActionRemove, content/action, can_edit, can_remove, can_create, can_create_class_list, fetch access, RedirectURIAfterPublish, RedirectIfDiscarded, RedirectURIAfterRemove, front-end editing
type: help
---
# Schaltflächen zum Bearbeiten, Entfernen und Anlegen auf der Website

Diese Seite richtet sich an Designer, die angemeldete Benutzer von den öffentlichen Seiten aus an Inhalten arbeiten lassen:
ein Link **Bearbeiten** an einem Artikel, eine Schaltfläche **Entfernen** für den eigenen Kommentar oder eine Schaltfläche
**Neues Thema** in einem Forum. Alle senden ein kleines Formular an die Ansicht `content/action`, die die Rechte des Benutzers
erneut prüft und ihn auf die richtige Seite weiterleitet.

## Inhalt

- [Eine Schaltfläche nur Benutzern zeigen, die sie verwenden dürfen](#eine-schaltfläche-nur-benutzern-zeigen-die-sie-verwenden-dürfen)
- [Bearbeiten](#bearbeiten)
- [Ein neues Element unter diesem anlegen](#ein-neues-element-unter-diesem-anlegen)
- [Entfernen](#entfernen)
- [Wo der Benutzer danach landet](#wo-der-benutzer-danach-landet)
- [Was Sie beachten sollten](#was-sie-beachten-sollten)

## Eine Schaltfläche nur Benutzern zeigen, die sie verwenden dürfen

Das Inhaltsobjekt beantwortet die üblichen Fragen für den aktuellen Benutzer:

| Attribut | Wahr, wenn der Benutzer |
|---|---|
| `$node.object.can_edit` | das Objekt bearbeiten darf |
| `$node.object.can_remove` | es entfernen darf |
| `$node.object.can_create` | darunter etwas anlegen darf |
| `$node.object.can_create_class_list` | die Liste der Klassen, die er darunter anlegen darf (jeder Eintrag mit `id` und `name`) |

Für eine Frage zu einer Klasse oder zu einem Objekt, das Sie nicht als Knoten geladen haben, fragen Sie die Fetch-Funktion
`access`:

```
{if fetch( 'content', 'access', hash( 'access', 'create',
                                      'contentobject', $node,
                                      'contentclass_id', 'comment' ) )}
    ... show the comment form ...
{/if}
```

`access` nimmt den Namen einer Inhaltsfunktion (`read`, `edit`, `create`, `remove`, `translate` und die anderen des Moduls
content); `contentclass_id` nimmt eine ID oder einen Klassenbezeichner. Eine Schaltfläche auszublenden dient nur der
Bequemlichkeit: Die Aktion selbst prüft die Rechte erneut, ein Benutzer, der das Formular fälscht, erhält also eine Seite
„Zugriff verweigert“.

## Bearbeiten

```
{if $node.object.can_edit}
<form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
    <input type="hidden" name="ContentObjectLanguageCode" value="{$node.object.current_language|wash}" />
    <button type="submit" name="EditButton" value="1">Edit</button>
</form>
{/if}
```

`content/action` macht aus `EditButton` die Adresse `content/edit/<Objekt-ID>/f/<Sprache>`: einen neuen Entwurf in dieser
Sprache oder den vorhandenen Entwurf des Benutzers. Ohne `ContentObjectLanguageCode` entscheidet die Bearbeitungsansicht
selbst über die Sprache. Ein einfacher Link funktioniert ebenso: `{concat( 'content/edit/', $node.contentobject_id )|ezurl}`.

## Ein neues Element unter diesem anlegen

```
{if $node.object.can_create}
<form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="NodeID" value="{$node.node_id}" />
    <input type="hidden" name="ClassIdentifier" value="forum_topic" />
    <input type="hidden" name="ContentLanguageCode" value="{$node.object.current_language|wash}" />
    <button type="submit" name="NewButton" value="1">New topic</button>
</form>
{/if}
```

- `NodeID` ist das übergeordnete Element des neuen Elements.
- `ClassIdentifier` nennt die Klasse; `ClassID` mit der numerischen ID funktioniert ebenfalls und hat Vorrang, wenn beide
  gesendet werden. Um den Benutzer wählen zu lassen, geben Sie ein `<select name="ClassID">` mit den Einträgen von
  `$node.object.can_create_class_list` aus.
- `ContentLanguageCode` legt die Sprache des neuen Elements fest. Ohne das Feld zeigt eine Website mit mehr als einer Sprache
  zuerst eine Seite, auf der der Benutzer die Sprache wählt.

Das Objekt wird als Entwurf angelegt, und das Bearbeitungsformular öffnet sich. Verwirft der Benutzer es, bleibt nichts zurück.

## Entfernen

```
{if $node.object.can_remove}
<form method="post" action={'content/action'|ezurl}>
    <input type="hidden" name="ContentNodeID" value="{$node.node_id}" />
    <input type="hidden" name="ContentObjectID" value="{$node.contentobject_id}" />
    <button type="submit" name="ActionRemove" value="1">Remove</button>
</form>
{/if}
```

`ActionRemove` entfernt diesen Ort (`ContentNodeID`) nach einer Bestätigungsseite. Fügen Sie
`<input type="hidden" name="HideRemoveConfirmation" value="1" />` hinzu, um die Bestätigung zu überspringen, was auf einer
öffentlichen Seite selten eine gute Idee ist.

## Wo der Benutzer danach landet

Drei optionale Felder in diesen Formularen wählen die Seite nach der Aktion:

| Feld | In | Der Benutzer gelangt zu |
|---|---|---|
| `RedirectURIAfterPublish` | Bearbeitungs- und Anlegeformularen | dieser Adresse nach **Zur Veröffentlichung schicken** |
| `RedirectIfDiscarded` | Bearbeitungs- und Anlegeformularen | dieser Adresse, nachdem der Entwurf verworfen wurde |
| `RedirectURIAfterRemove` | dem Entfernen-Formular | dieser Adresse nach dem Entfernen |
| `RedirectIfCancel` | dem Entfernen-Formular | dieser Adresse, wenn die Bestätigung abgebrochen wird |

`content/action` hält die Werte in der Sitzung, und das Bearbeitungsformular trägt sie weiter, sie überstehen also
Prüffehler und Vorschauen. Ohne sie verwendet das Bearbeitungsformular die letzte Seite, die der Benutzer vor dem Bearbeiten
angesehen hat. Geben Sie eine Adresse auf derselben Website an, zum Beispiel den eigenen Alias des Knotens:

```
<input type="hidden" name="RedirectURIAfterPublish" value="{$node.url_alias|wash}" />
```

## Was Sie beachten sollten

- **Nur für angemeldete Benutzer.** Der anonyme Benutzer hat normalerweise keine Rechte zum Bearbeiten oder Entfernen, die
  Schaltflächen bleiben für Besucher also ohne zusätzliche Prüfung verborgen.
- **Das Formular-Token wird für Sie hinzugefügt.** Die Erweiterung `ezformtoken` setzt ihr verstecktes Feld in jedes
  POST-Formular einer HTML-Seite und weist einen POST ohne es ab. Ein Formular, das Sie in JavaScript bauen, muss das Token
  selbst hinzufügen ([ezformtoken und CSRF](../../erweiterungsentwicklung/teil-8-referenz/ezformtoken-und-csrf.md)).
- **Caching.** Diese Schaltflächen unterscheiden sich je Benutzer. Der View-Cache eines Knotens wird je Kombination aus
  Rollen und eingeschränkten Zuweisungen gehalten, Benutzer mit denselben Rollen teilen ihn also; eine Schaltfläche, die vom
  Eigentum abhängt (`can_edit` mit einer Einschränkung auf Owner), kann für die falsche Person gecacht werden. Legen Sie
  solche Schaltflächen in einen Teil der Seite, der nicht gecacht wird, oder prüfen Sie das Eigentum in einem Ajax-Aufruf.
- **Verwenden Sie zum Entfernen Schaltflächen, keine Links.** Einem Link kann ein Crawler oder ein vorausladender Browser
  folgen. Ein POST-Formular ist die sichere Wahl für alles, was Inhalte ändert.

## Verwandte Seiten

- [Formulare in Templates](formulare-in-templates.md)
- [Eigene Bearbeitungstemplates](eigene-bearbeitungstemplates.md)
- [Operatoren für Rollen und Richtlinien](rollen-und-richtlinien-operatoren.md)
- [Referenz der Fetch-Funktionen](../../erweiterungsentwicklung/teil-8-referenz/referenz-der-fetch-funktionen.md)
- [Häufige Template-Aufgaben](../../buch-lernen/haeufige-aufgaben-mit-templates.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
