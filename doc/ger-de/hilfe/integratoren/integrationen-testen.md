---
title: Integrationen testen
audience: integrator, developer
category: integration
order: 380
summary: Eine Teststrategie für Exponential-Integrationen - Unit-Tests ohne Datenbank für Zuordnungen und Parser, Test-Doubles für das andere System, Skripttests mit geladenem Kernel, Probeläufe auf einer Kopie, der Idempotenztest, Sandboxes von Anbietern, Mail und Webhooks in Tests und Abnahmetests mit dem Kunden.
keywords: Test, testen, Unit-Test, Test ohne Datenbank, Test-Double, Mock, Vertragstest, Probelauf, Idempotenztest, Staging, Sandbox, Abnahmetest, testing, PHPUnit, unit test, DB-less test, test double, contract test, ezexec, dry run, idempotency test, sandbox, file mail transport, webhook receiver, acceptance test, CI
type: help
---
# Integrationen testen

Diese Seite richtet sich an Entwickler und Tester von Integrationen. Eine Integration hat zwei Systeme, ein Netzwerk
und Daten, die sich unter ihr ändern; die meisten ihrer Fehler lassen sich mit ein paar Arten von Tests vor dem
Go-live finden. Die Mechanik auf Seiten der Erweiterung steht unter
[Tests für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tests-fuer-erweiterungen.md).

## Inhalt

1. [Die Testpyramide einer Integration](#die-testpyramide-einer-integration)
2. [Unit-Tests ohne Datenbank](#unit-tests-ohne-datenbank)
3. [Das andere System als Test-Double](#das-andere-system-als-test-double)
4. [Tests mit geladenem Kernel](#tests-mit-geladenem-kernel)
5. [Probeläufe und der Idempotenztest](#probeläufe-und-der-idempotenztest)
6. [Mail, Webhooks und Sandboxes](#mail-webhooks-und-sandboxes)
7. [Abnahme mit dem Kunden](#abnahme-mit-dem-kunden)

## Die Testpyramide einer Integration

| Ebene | Was sie beweist | Wie oft |
|---|---|---|
| Unit-Tests (ohne Datenbank) | Zuordnung, Parsen, Validierung, Aufbau der Nutzlast, Signaturprüfungen | Bei jedem Commit, in der CI |
| Vertragstests mit einem Test-Double | Ihr Client behandelt jede Antwort des anderen Systems, auch Fehler | Bei jedem Commit |
| Skripttests mit dem Kernel | Objekte werden angelegt, über die Remote-ID gefunden und nur bei Änderung aktualisiert | Vor jedem Release, auf einer Entwicklungsinstallation |
| Probeläufe auf einer Kopie der Produktion | Echte Daten lassen sich parsen; die Zahlen sind plausibel | Vor dem Go-live und nach Änderungen am Quellformat |
| Ende zu Ende in einer Staging-Umgebung mit der Testinstanz des anderen Systems | Der ganze Fluss funktioniert, mit echten Zugangsdaten der Testsysteme | Vor dem Go-live und nach Upgrades |
| Abnahme mit dem Kunden | Das fachliche Ergebnis stimmt | Einmal, unterschrieben |

## Unit-Tests ohne Datenbank

Halten Sie die Logik, die keinen Kernel braucht, in einfachen Klassen: den CSV-Parser, die Zuordnung einer Quellzeile
zu Attributwerten, den Aufbau der Nutzlast, die Signaturprüfung. Sie laufen in Millisekunden mit dem PHPUnit aus dem
`vendor/` des Kernels:

```php
<?php
use PHPUnit\Framework\TestCase;

final class ProductMappingTest extends TestCase
{
    public function testPriceIsWrittenInThePriceDatatypeFormat(): void
    {
        $mapping = new myIntegrationProductMapping();
        $attributes = $mapping->attributes( array( 'ARTNR' => '10442', 'NAME' => ' Trail shoe ', 'PRICE' => '119.00', 'STOCK' => '7' ) );
        $this->assertSame( '119.00|1|1', $attributes['price'] );
        $this->assertSame( 'Trail shoe', $attributes['name'] );
    }

    public function testRowsWithoutAnArticleNumberAreRefused(): void
    {
        $this->assertNull( ( new myIntegrationProductMapping() )->attributes( array( 'ARTNR' => '', 'PRICE' => '1' ) ) );
    }
}
```

Führen Sie sie aus dem Verzeichnis der Erweiterung mit `php ../../vendor/bin/phpunit` und einer `phpunit.xml.dist`
wie im oben genannten Kapitel aus ([PHPUnit-Tests ohne Datenbank](../ersteller/anwendungsentwickler/phpunit-tests-ohne-datenbank.md)).

## Das andere System als Test-Double

Legen Sie das andere System hinter eine eigene Schnittstelle (`myIntegrationHubClient` mit `send()`), und geben Sie
Tests eine Attrappe, die Aufrufe aufzeichnet und beantwortet, was der Test braucht: Erfolg, `429` mit `Retry-After`,
`500`, ein Timeout, einen ungültigen Inhalt. Testen Sie dann, dass Ihr Sender Zeilen nur bei Erfolg als gesendet
markiert, bei `429` zurückweicht und eine Zeile nach dem letzten Versuch in die Dead-Letter-Ablage verschiebt. Bewahren
Sie einige aufgezeichnete echte Antworten des anderen Systems als Fixtures auf und aktualisieren Sie sie, wenn sich
dessen API ändert.

## Tests mit geladenem Kernel

Ein Testskript, das mit `php bin/php/ezexec.php <script> -s <siteaccess>` auf einer Entwicklungsinstallation läuft,
kann Objekte mit einem Testpräfix anlegen, prüfen und wieder entfernen:

```php
<?php
// extension/myintegration/tests/scripts/upsert_check.php, run with bin/php/ezexec.php on a development copy only
$importer = new myIntegrationProductImport( $cli, false );
$remoteID = 'test-erp-product-' . time();
// ... call the upsert with a test record, then:
$object = eZContentObject::fetchByRemoteID( $remoteID );
$cli->output( $object instanceof eZContentObject ? 'PASS created' : 'FAIL not created' );
if ( $object instanceof eZContentObject )
{
    $object->removeThis();   // or remove its node to the trash; development copies only
}
```

`ezexec.php` aktiviert die Testklassen im Verzeichnis `tests/` einer Erweiterung für ein Skript darin. Führen Sie
solche Skripte nie gegen die Produktion aus.

## Probeläufe und der Idempotenztest

1. Kopieren Sie die Produktion (Datenbank und `var/storage`) auf eine Testinstallation; anonymisieren Sie
   personenbezogene Daten, wenn die Kopie die Produktionsumgebung verlässt.
2. Führen Sie den Import mit `--dry-run` aus: Die Zahlen müssen plausibel sein (nicht "20 000 angelegt" auf einer
   Website, die alle schon hat).
3. Führen Sie ihn wirklich aus; prüfen Sie eine Stichprobe von Objekten.
4. Führen Sie ihn sofort noch einmal aus: Er darf nur `unchanged` melden. Jedes `updated` bedeutet, dass die
   Änderungserkennung Formate vergleicht, die sich unterscheiden.
5. Entfernen Sie einen Datensatz aus der Quelle und führen Sie ihn erneut aus: Die Regel für Entfernungen muss greifen,
   und der Sicherheitsstopp muss auslösen, wenn die Quelle absichtlich gekürzt wird.

## Mail, Webhooks und Sandboxes

- **Mail**: `site.ini [MailSettings] Transport=file` auf jeder Testinstallation, und prüfen Sie, dass
  `settings/override` keinen anderen Transport erzwingt; Testadressen auf `example.invalid`.
- **Ausgehende Webhooks**: Richten Sie sie auf einen eigenen Empfänger im Testnetzwerk und testen Sie die
  Signaturprüfung mit einem falschen Geheimnis und einem alten Zeitstempel.
- **Zahlungs- und Identitätsanbieter**: Verwenden Sie deren Sandboxes; nie echte Karten oder echte Konten.
- **API-Schlüssel**: eigene Schlüssel für Testinstallationen, mit kurzer Laufzeit.

## Abnahme mit dem Kunden

Schreiben Sie Abnahmefälle in der Sprache des Kunden, mit Daten, die er kennt: "Artikel 10442, um 14:00 im ERP
geändert, zeigt bis 14:10 den neuen Preis auf der Produktseite." Lassen Sie sie vor dem Go-live abzeichnen und
bewahren Sie sie für das nächste Upgrade auf, wenn sie zum Regressionstest werden
([Upgrades ohne defekte Integrationen](upgrades-ohne-defekte-integrationen.md)).

## Verwandte Seiten

- [Tests für Erweiterungen](../erweiterungsentwicklung/teil-3-erweiterungsentwickler/tests-fuer-erweiterungen.md)
- [Anwendungen testen](../ersteller/anwendungsentwickler/anwendungen-testen.md)
- [Qualitätstore und Abnahme](../fuehrung/qualitaetsschranken-und-abnahme.md)
- [Go-live-Checkliste für Integrationen](checkliste-fuer-den-livegang-einer-integration.md)
- [Freie Dokumentation](../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte mit uns, und so wächst Exponential. Eine Partner-Website und ein Dashboard sind für Mitte
2027 geplant: Werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen früher liefern. Beginnen
Sie mit [Partner: hier beginnen](../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
