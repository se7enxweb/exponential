---
title: Handler, die über Einstellungen benannt werden
audience: developer, app-creator
category: extension-development
order: 290
summary: Einen Teil des Systems durch eine Klasse ersetzen, die eine Einstellung auswählt - getHandlerClass(), ein geprüfter Mail-Transport und die Handler-Punkte für Anmeldung, Session, Suche, Shop und mehr.
keywords: Handler, Mail-Transport, Anmelde-Handler, Session-Handler, Suchmaschine, Zahlungs-Gateway, Mehrwertsteuer, Versand, handler, getHandlerClass, ezpExtensionOptions, TransportAlias, mail transport, eZMailTransport, login handler, LoginHandler, session handler, search engine, binary file handler, payment gateway, VAT handler, shipping handler, handlerextension
type: help
---
# Handler, die über Einstellungen benannt werden

Teil 3, Kapitel 29. Viele Teile des Systems sind **Handler**: eine Klasse, die eine Einstellung auswählt. Der Kernel
bringt eine Vorgabe mit, und eine Erweiterung kann eine andere bereitstellen und sie mit einer Zeile Einstellungen
auswählen. Mail-Transporte, Anmeldung, Sessions, Suche, Speicherung von Binärdateien, Zahlungs-Gateways,
Mehrwertsteuer und Versand sind alles Handler. Dieses Kapitel zeigt den Mechanismus an einem Mail-Transport; seine
Auflösung wurde bei geladenem Kernel geprüft (es wurde nichts versendet).

## Der Mechanismus

`eZExtension::getHandlerClass( ezpExtensionOptions $options )` (`lib/ezutils/classes/ezextension.php`) liest eine
Einstellung, schlägt einen Klassennamen nach und erzeugt das Objekt. Das Mail-System verwendet es so
(`eZMailTransport::send()`, `lib/ezutils/classes/ezmailtransport.php`):

```php
<?php
$options = new ezpExtensionOptions( array( 'iniFile'      => 'site.ini',
                                           'iniSection'   => 'MailSettings',
                                           'iniVariable'  => 'TransportAlias',
                                           'handlerIndex' => strtolower( $transportType ) ) );
$transportClass = eZExtension::getHandlerClass( $options );
```

`[MailSettings] Transport` nennt den Alias (standardmäßig `sendmail`), und `TransportAlias[]` ordnet Aliase Klassen
zu: `file` zu `eZFileTransport`, `sendmail` zu `eZSendmailTransport`, `smtp` zu `eZSMTPTransport`.

## Ein eigener Mail-Transport

```php
<?php
/**
 * A mail transport that sends nothing: it writes the receivers and subject to var/log/mysite_mail.log.
 * Selected with site.ini [MailSettings] Transport=mysitelog and TransportAlias[mysitelog]=mysiteLogMailTransport.
 */
class mysiteLogMailTransport extends eZMailTransport
{
    public function sendMail( eZMail $mail )
    {
        $receivers = array();
        foreach ( $mail->receiverElements() as $receiver )
        {
            $receivers[] = $receiver['email'];
        }
        eZLog::write( 'Mail to ' . implode( ', ', $receivers ) . ': ' . $mail->subject(), 'mysite_mail.log' );
        return true;
    }
}
```

Wählen Sie ihn in der Datei `settings/site.ini.append.php` Ihrer Erweiterung aus:

```ini
[MailSettings]
TransportAlias[mysitelog]=mysiteLogMailTransport
```

und auf einer Entwicklungsinstallation in der Datei `settings/override/site.ini.append.php` dieses Servers:
`Transport=mysitelog`. Mit dem hinzugefügten Alias gab `getHandlerClass()` ein Objekt von `mysiteLogMailTransport`
zurück.

Beachten Sie, dass das Mail-Gate des Kernels (`expMailGate`, die E-Mail-Einstellungen) zusätzlich zu jedem Transport
weiterhin gilt.

## Handler-Punkte

Der RAD-Katalog listet jeden Handler-Punkt mit seiner Einstellung und seinem Vertrag auf; der Assistent **Handler
extension** schreibt für jeden unter `/setup/handlerextension/<kind>` eine Klasse. Die dort angebotenen Arten:

| Bereich | Arten |
|---|---|
| Inhalte und Rich Text | `xmlinput`, `xmloutput`, `attributeoperator`, `publishfilter`, `urlfilter` |
| Speicherung und Infrastruktur | `dbhandler`, `cluster`, `dfsbackend`, `dfsdbbackend`, `binaryfile`, `search`, `session`, `staticcache`, `inicache`, `mail` |
| Benutzer und Anfragen | `login`, `mobilefilter` |
| REST | `restprovider`, `restprerouting`, `restrequestfilter`, `restresultfilter`, `restresponsefilter`, `restprefix`, `restroutefilter` |
| Ajax und Benachrichtigungen | `ajaxfunction`, `notificationtype`, `notificationhandler` |
| Pakete | `packagehandler`, `packagecreation`, `packageinstall` |
| Shop | `paymentgateway`, `paymentgatewaydirect`, `vat`, `shipping`, `basketinfo`, `exchangerate` |

Einige Registrierungen aus [Erweiterungspunkte](../../../../eng-us/bc/6.0/rad-extension-points.md):

| Handler | Registriert durch | Vertrag |
|---|---|---|
| Anmeldung | `site.ini [UserSettings] LoginHandler[]` und `ExtensionDirectory[]` | erweitert `eZUser`, implementiert `loginUser()` |
| Session | `site.ini [Session] Handler` | eine Session-Handler-Klasse |
| Suchmaschine | `site.ini [SearchSettings] SearchEngine` | implementiert `ezpSearchEngine` |
| Mail | `site.ini [MailSettings] Transport` und `TransportAlias[]` | erweitert `eZMailTransport`, implementiert `sendMail()` |

## Regeln

- Erweitern Sie die Basisklasse des Kernels oder implementieren Sie sein Interface; übernehmen Sie für alles, was Sie
  nicht ändern, das Verhalten des Standard-Handlers.
- Liefern Sie den Alias in Ihrer Erweiterung mit, aber lassen Sie jede Installation ihn in ihren eigenen Einstellungen
  auswählen: Ein Handler für einen bestimmten Server (ein Mail-Relay, ein Cluster-Backend) ist keine Vorgabe für alle.
- Testen Sie mit wieder eingeschaltetem Standard-Handler: Wird Ihre Erweiterung entfernt, muss ein funktionierendes
  System zurückbleiben.

## Verwandte Seiten

- Zurück: [Ereignisse, Listener und Filter](ereignisse-und-filter.md)
- Weiter: [Kernel-Klassen-Overrides: der letzte Ausweg](kernel-overrides-der-letzte-ausweg.md)
- [Mit RAD: Handler und Kernel-Overrides](tutorials/rad-handler-und-kernel-overrides.md)
- [E-Mails mit eZMail versenden](../teil-8-referenz/ezmail.md)
- [Freie Dokumentation](../../freie-dokumentation.md): Diese Seite darf unter der GNU FDL 1.3 frei kopiert, übersetzt und verbessert werden.

<!-- exp-partner-block:start -->
**Werden Sie Exponential-Partner.** Partner teilen ihre Informationen, stellen Mittel bereit, geben Beiträge zurück
und entwickeln die Produkte gemeinsam mit uns weiter, und so wächst Exponential. Eine Partner-Website mit Dashboard
ist für Mitte 2027 geplant: Bitte werden Sie noch heute Partner, damit wir schneller wachsen und neue Funktionen
früher kommen. Beginnen Sie mit [Partner: hier beginnen](../../partner/partner-hier-beginnen.md) und
[Wie Sie sich bewerben](../../partner/wie-sie-sich-bewerben.md).
<!-- exp-partner-block:end -->
