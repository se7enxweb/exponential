# Query bei Weiterleitungen, Fehler in ezjscore und Schutz bei Bildaliasen (6.0.15)

## Modulweiterleitungen behalten die Query der Anfrage nur auf dem eigenen Host

Seit der Korrektur aus Pull Request 105 hängt `ezpKernelWeb::redirect()` den Query-String der aktuellen Anfrage über
`ezpKernelWeb::appendRequestQuery()` an das Ziel der Weiterleitung an.

| Ziel der Weiterleitung | Query der Anfrage |
|---|---|
| relativ (`/content/view/full/2`) | bleibt erhalten |
| absolut auf dem eigenen Host (`https://www.example.com/a`, auch mit einem Port wie `:8080`) | bleibt erhalten |
| ein anderer Host, auch ein anderer über den Host zugeordneter Siteaccess (`https://edit.example.com/...`) | entfällt |
| protokollrelativ (`//host/a`) oder ein anderes Schema (`ftp:`, `mailto:`) | entfällt, außer es nennt den eigenen Host |

Der eigene Host ist der Host der Anfrage (`eZSys::hostname()`, das hinter einem Proxy das erste `X-Forwarded-Host`
berücksichtigt, wie überall im Kernel) plus der Host aus `SiteSettings` `SiteURL`. Hosts werden ohne Beachtung der
Groß- und Kleinschreibung verglichen, ohne Port und abschließenden Punkt, und internationalisierte Namen in ihrer
Punycode-Form. Ein Ziel mit eigener Query erhält die Query der Anfrage nach einem `&`; ein `#fragment` bleibt am
Ende.

Warum: Die URL eines Zahlungsfensters ist über ihre exakte Query versiegelt, sodass ein angehängter Tracking-Parameter
(`_gl`, `gclid`) der Shop-Anfrage das Siegel ungültig macht, und eine Query darf nicht zu einem fremden Host wandern.

Was sich für vorhandenen Code geändert hat: Eine Weiterleitung auf einen anderen Host erhielt bisher die Query der
Anfrage, jetzt nicht mehr. Ein Modul, das einen Parameter auf einem fremden Ziel braucht, setzt ihn in das Ziel, das
es an `redirectTo()` übergibt. Eine Weiterleitung zwischen zwei Hosts derselben Installation (zum Beispiel dem Host
des öffentlichen und dem des Admin-Siteaccess) trägt die Query nicht mehr mit; fügen Sie die Parameter dem Ziel hinzu,
oder machen Sie den Host zum eigenen Host, indem Sie über eine relative URL weiterleiten.

## ezjscore/call

Ein PHP-`Error` (`TypeError`, ein Aufruf auf null) in einer Server-Funktion wird mit Klasse, Meldung, Datei und Zeile
protokolliert und als `error_text` "Internal error in the server function" beantwortet; die Antwort bleibt gültiges
JSON oder XML. Eine `Exception` beantwortet weiterhin ihre eigene Meldung. Die entfernten Dienste
(`expServiceBase::invoke()`) fangen ihre Fehler selbst ab und antworten weiterhin mit
`{ "ok": false, "error": { "code", "message" } }` innerhalb von `content`.

## Bildaliase

`eZImageAliasHandler::imageAlias()` gibt für ein Attribut, dessen Original keine Datei hat, null zurück, ohne den
Bildmanager zu fragen. Das verzögerte Zurückschreiben erzeugter Aliase vergleicht zuerst die gespeicherte Zeile mit dem
XML im Speicher (`eZImageAliasHandler::storedXMLSupersedesDOMTree()`: Seriennummer und Verzeichnis) und überspringt
das Schreiben, wenn die Zeile inzwischen veröffentlicht oder ersetzt wurde; die Aliase werden beim nächsten Rendern
aus dem frischen XML neu erzeugt. Ein übersprungenes Zurückschreiben protokolliert eine Fehlerzeile, die das Attribut
nennt.
