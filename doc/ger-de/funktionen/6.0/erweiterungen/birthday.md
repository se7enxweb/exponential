# birthday: ein Datentyp für Geburtstage

Diese Seite ist für Website-Entwickler, die Geburtsdaten von Personen speichern. `birthday` („eZ Birthday“) stellt den
Datentyp `ezbirthday` bereit. Er speichert ein Geburtsdatum so, dass es sich sortieren und durchsuchen lässt, und
bringt die üblichen Templates zum Bearbeiten und Anzeigen von Klassen und Objekten mit.

Außerdem registriert er in `csv.ini` einen Export-Handler, `eZBirthdayCsvHandler`. Das Exportwerkzeug
[xrowextract](xrowextract.md) verwendet ihn.

## Verwenden

1. Aktivieren Sie die Extension.
2. Bearbeiten Sie eine Inhaltsklasse (**Setup > Klassen**) und fügen Sie ein Attribut des Geburtstags-Datentyps hinzu.
3. Bearbeiten Sie ein Objekt dieser Klasse und geben Sie ein Geburtsdatum ein.

## Was sich geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.3.0 | 28. Januar 2024 | Paketanbieter. |
| 1.3.1 | 14. September 2026 | Der **CSV-Export-Handler beendet keine Anfrage mehr, die ihn lädt** (siehe unten). |
| 1.3.2 | 2. Oktober 2026 | `ezinfo.php`, das der Extension fehlte, wurde hinzugefügt und `extension.xml` korrigiert, sodass die Info-Seite sie aufführt. |

Zu 1.3.1: `eZBirthdayCsvHandler` erweiterte den Basis-Handler von `bccie`, der
`exportAttribute( &$attribute, $separationChar )` deklariert, während das Exportwerkzeug `exportAttribute( $attribute )`
mit einem Argument aufruft. Die Deklaration war mit der ihrer Elternklasse unvereinbar, und PHP wies die Klasse in dem
Moment zurück, in dem etwas sie per Autoload lud: Die Anfrage endete. Die Klasse erweitert jetzt `XrowBaseHandler`, wie
jeder andere Handler, der über diese INI-Datei registriert ist. Der Wert kommt weiterhin aus `data_text`, weil
`objectAttributeContent()` ein Objekt `eZBirthday` zurückgibt.

## Sprachen

Die Extension enthält Übersetzungsdateien in `translations/<locale>/translation.ts`: fre-FR, ger-DE und nor-NO. Die
deutsche Datei enthält 24 Meldungen (zählen Sie `<message` in `translations/ger-DE/translation.ts`). Die Texte werden in
den Kontexten `design/standard/class/datatype`, `design/standard/content/datatype` und `kernel/classes/datatypes`
nachgeschlagen.

Nach dem Bearbeiten einer Übersetzungsdatei:

1. Erneuern Sie den kompilierten Übersetzungs-Cache: `./console exp:ezgeneratetranslationcache`.
2. Leeren Sie die Template- und Inhalts-Caches.

`./console exp:ezchecktranslation ger-DE` gibt Statistiken zur Datei `share/translations/ger-DE/translation.ts` des
Kernels aus, nicht zur Datei dieser Extension.

## Verwandte Seiten

- [xrowextract](xrowextract.md), [bccie](bccie.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/birthday.md) und [Versionshinweise](../../../../changelogs/extensions/birthday.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/birthday.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md), [2026-10](../../../geschichte/erweiterungen/monate/2026-10.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
