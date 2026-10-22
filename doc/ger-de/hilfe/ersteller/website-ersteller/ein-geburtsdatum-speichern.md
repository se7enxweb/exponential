---
title: Ein Geburtsdatum mit dem Datentyp Geburtstag speichern
audience: website-creator, developer
category: website-creation
order: 195
summary: Den Datentyp Geburtstag zu einer Klasse hinzufügen, was Redakteure sehen, was gespeichert wird und wie Sie das Datum, das Alter und das Sternzeichen in einem Template ausgeben.
keywords: Geburtstag, Geburtsdatum, Alter, Sternzeichen, Tierkreiszeichen, Datentyp, Website-Ersteller, birthday, ezbirthday, date of birth, age, zodiac, datatype, website creator
type: help
---
# Ein Geburtsdatum mit dem Datentyp Geburtstag speichern

Diese Seite richtet sich an Website-Ersteller, deren Inhalte das Geburtsdatum einer Person enthalten. Die Erweiterung
`birthday` stellt den Datentyp **Geburtstag** (`ezbirthday`) bereit. Verwenden Sie ihn statt eines Datumsattributs, wenn Sie
auch das Alter oder das Sternzeichen brauchen oder wenn das Jahr weit in der Vergangenheit liegen kann.

## Zu einer Klasse hinzufügen

1. Stellen Sie sicher, dass die Erweiterung aktiv ist (`ActiveExtensions[]=birthday`).
2. Bearbeiten Sie unter **Setup > Klassen** Ihre Klasse und fügen Sie ein Attribut des Datentyps **Geburtstag** hinzu.
3. Wählen Sie den Standardwert: **Leer** oder **Aktuelles Datum**. Setzen Sie **Erforderlich**, wenn jedes Objekt ein Datum
   braucht.

## Was Redakteure sehen

Drei beschriftete Auswahllisten: Jahr (vom aktuellen Jahr 100 Jahre zurück), Monat und Tag. Ein Datum, bei dem ein Teil
fehlt, oder eines, das es nicht gibt, etwa der 30. Februar, wird mit einer Meldung abgewiesen. Ein optionales Attribut kann
ganz leer bleiben.

## Was gespeichert wird

Das Datum als `YYYY-MM-DD` in der Textspalte des Attributs; das ist zugleich der Sortierschlüssel und der Suchtext.

## Anzeigen

```
{def $b=$node.data_map.birthday.content}
{if $b.is_valid}
    {$b.day}.{$b.month}.{$b.year}, {$b.age} years, {$b.zodiac_name}
{/if}
```

Die verfügbaren Werte sind `year`, `month`, `day`, `is_valid`, `age`, `days_on_earth`, `zodiac_no` und `zodiac_name`.

Mehr Einzelheiten, die Einstellungen und die Hinweise zum Upgrade: [birthday](../../../funktionen/6.0/erweiterungen/birthday.md)
und die Datei `doc/birthday.md` der Erweiterung. Siehe auch [Datentypen wählen](datentypen-waehlen.md).
