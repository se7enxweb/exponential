# owsimpleoperator: einen Template-Operator in wenigen Zeilen schreiben

Diese Seite ist für Entwickler, die einen kleinen Template-Operator brauchen oder Templates eine PHP-Funktion aufrufen
lassen wollen. Eine vollständige Operatorklasse ist oft mehr Arbeit, als der Operator verdient. `owsimpleoperator` (von
Open Wide) lässt Sie jeden Operator als kleine Klasse mit einfachen Methoden schreiben: weniger Boilerplate und
lesbarerer Code. Außerdem lässt es Templates **PHP-Funktionen und Klassenmethoden als Operatoren** aufrufen, aber nur
die, die Sie ausdrücklich aufführen.

## Was Sie bekommen

- Jede erlaubte PHP-Funktion als Template-Operator aufrufen.
- Hilfsmethoden für Ihren PHP-Code: Bearbeitung von Zeichenketten, Bearbeitung von Objektattributen, Prüfung von
  Objekttypen und Bearbeitung der Ausgabe.

Einschränkungen:

- Ein optionales Argument eines Operators muss den Standardwert `null` haben.
- Für mehr als 10 Argumente überschreiben Sie `$max_operator_parameter`.

## Eine PHP-Funktion oder Klassenmethode erlauben

Nichts ist aufrufbar, solange Sie es nicht in `owsimpleoperator.ini` aufführen. Das wird mitgeliefert:

```ini
[PHPFunctions]
PermittedFunctionList[]
PermittedFunctionList[]=time
PermittedFunctionList[]=mktime
PermittedFunctionList[]=getdate
PermittedFunctionList[]=str_replace
PermittedFunctionList[]=str_rot13
PermittedFunctionList[]=file_get_contents
[ClassOperators]
PermittedClassOperatorList[]
[OWSimpleOperatorSettings]
DebugOutput=enabled
```

| Datei | Block | Schlüssel | Standard | Bedeutung |
|---|---|---|---|---|
| `owsimpleoperator.ini` | `PHPFunctions` | `PermittedFunctionList[]` | die Liste oben | PHP-Funktionen, die Templates aufrufen dürfen |
| `owsimpleoperator.ini` | `ClassOperators` | `PermittedClassOperatorList[]` | leer | Klassenmethoden, die Templates aufrufen dürfen |
| `owsimpleoperator.ini` | `OWSimpleOperatorSettings` | `DebugOutput` | `enabled` | Debug-Ausgabe |

**Sicherheit:** `file_get_contents` lässt ein Template jede Datei lesen, die der Webserver lesen kann. Entfernen Sie es
auf Produktionswebsites, die es nicht brauchen, aus der Liste (die anderen Einträge sind harmlos). Eine Funktion, die
nicht erlaubt ist, lässt sich aus einem Template nicht aufrufen.

## Was sich in den Releases von Exponential 6 geändert hat

| Version | Datum | Änderung |
|---|---|---|
| 1.2.0 | 7. Januar 2024 | Versionen in `composer.json`, `ezinfo.php` und `extension.xml`. |
| 1.2.1 | | Der Name der Extension ist kleingeschrieben, um Fehler zu vermeiden. |
| 1.2.2 bis 1.2.5 | 27. bis 30. September 2026 | Die Extension gibt ihre Version, Lizenz und Website an; die Lizenz wird in den Metadaten genannt; die Beschreibung nennt Exponential. |

## Verwandte Seiten

- [swark](swark.md): fertige Operatoren im selben Geist
- [Template-Operatoren für Zeichenketten](../template-operatoren-fuer-zeichenketten.md)
- [Spezifikation der Extension-Metadaten](../../../spezifikationen/6.0/extension-metadaten.md)
- [Chronik](../../../geschichte/erweiterungen/owsimpleoperator.md) und [Versionshinweise](../../../../changelogs/extensions/owsimpleoperator.md)
- [Änderungsverzeichnis](../../../geschichte/aenderungsverzeichnis/owsimpleoperator.md)
- [Verhaltensänderungen der Extensions](../../../bc/6.0/verhaltensaenderungen-von-erweiterungen.md)
- Monate: [2024-01](../../../geschichte/erweiterungen/monate/2024-01.md), [2026-09](../../../geschichte/erweiterungen/monate/2026-09.md) (alle Extensions)

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
