# Konfigurierbares DFS-Cluster-Backend

Der DFS-Cluster verwendet zwei Handler: DB und FS. Bisher war der FS-Handler fest einprogrammiert und ließ sich nicht
ändern. Ab 5.4 kann er in einer INI-Datei festgelegt werden.

## Konfiguration

Eine neue INI-Einstellung, `eZDFSClusteringSettings.DFSBackend`, wird eingeführt. Sie erwartet den Namen einer Klasse,
die `eZDFSFileHandlerDFSBackendInterface` implementiert:

```ini
[eZDFSClusteringSettings]
DFSBackend=MyDFSBackend
```

## Initialisierung der Backend-Klasse
Standardmäßig wird das konfigurierte Backend mit dem Schlüsselwort `new` ohne Argument erzeugt.

Sind aufwendigere Initialisierungsschritte nötig, können Backends `eZDFSFileHandlerDFSBackendFactoryInterface`
implementieren. Diese Schnittstelle hat eine statische Methode `build()`. In der Implementierung lassen sich
Abhängigkeiten auflösen und Konfiguration laden.

## Einschränkungen
Wegen der Reihenfolge, in der die Einstellungen gelesen werden, müssen clusterbezogene Einstellungen in der globalen
Überschreibung von `file.ini` stehen und nicht in einer Extension oder einem Siteaccess.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
