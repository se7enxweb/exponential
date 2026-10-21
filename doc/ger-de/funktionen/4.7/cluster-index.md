# Überarbeiteter Cluster-Index #

Der Mechanismus des Cluster-Index wurde in Exponential 4.7 fast vollständig neu geschrieben.

## Bisherige Struktur ##

Bis Exponential 4.6 wurden Binärdateien über mehrere Dateien ausgeliefert:

- eine eigene `index_cluster.php` (Standardname), die einrichtungsspezifische Konfigurationskonstanten
  enthielt
- die Datei `index_image.php`, eingebunden aus `index_cluster.php`, die
  `index_image_<specificbackend>.php` einband
- die Datei `index_image_<specificbackend>.php`, die die eigentliche Logik zum Ausliefern
  der Dateien enthielt

Diese Struktur war verwirrend und fügte im Wurzelverzeichnis eine ganze Reihe von Dateien hinzu, was
wir zu vermeiden versuchen

## Überarbeitete Struktur ##

Im Document Root gibt es jetzt eine einzige Datei: `index_cluster.php`. Diese Datei liefert
die Binärdateien aus und muss das Ziel der Rewrite-Regel sein.

Je nach Konfiguration (siehe unten) verwendet diese Datei eine Gateway-Klasse, die für
jedes Cluster-Backend eigen ist und in kernel/clustering liegt. Eigene Gateways für den Cluster-Index
lassen sich über Extensions hinzufügen.

### Konfiguration ###

Die Konfiguration erfolgt jetzt über die bereits vorhandene `config.php`. Wie immer bei dieser Datei
finden Sie die Dokumentation in der mitgelieferten Datei `config.php-RECOMMENDED`. Für diese Neufassung finden
Sie etwa ein Dutzend neuer Konstanten, alle mit dem Präfix `CLUSTER_`.
Optional können Sie diese Konstanten in einer Datei `config.cluster.php` festlegen (die angelegt werden muss).
In diesem Fall stehen die Konstanten nur in der Cluster-Umgebung zur Verfügung.

Manche davon sind Einstellungen (Datenbankhost, Datenbank, Passwort, NFS-Pfad ...), andere sind
Schalter, die sich ein- und ausschalten lassen.

## Neue Funktionen ##

Diese Änderung macht die Codestruktur deutlich sauberer und bringt zusätzliche Vorteile:

- Unterstützung der Header `eTag` und `IF-MODIFIED-SINCE`
- Unterstützung von `HTTP RANGE` für Teil-Downloads (nur für **DFS-Cluster**)
- einheitliche Fehler- und Debug-Behandlung

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, tragen zum Projekt
bei und entwickeln die Produkte mit uns weiter, und so wächst Exponential. Eine Partner-Website und ein Dashboard
sind für Mitte 2027 geplant: Werden Sie bitte schon heute Partner, damit wir schneller wachsen und neue Funktionen
früher bereitstehen. Beginnen Sie mit [Partner: hier beginnen](../../hilfe/partner/partner-hier-beginnen.md) und [Wie Sie sich bewerben](../../hilfe/partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
