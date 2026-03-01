<?php /* #?ini charset="utf8"?

[General]
ExportableDatatypes[]
ExportableDatatypes[]=eztext
ExportableDatatypes[]=ezinteger
ExportableDatatypes[]=ezstring
ExportableDatatypes[]=ezurl
ExportableDatatypes[]=ezxmltext
ExportableDatatypes[]=ezboolean
ExportableDatatypes[]=ezemail
ExportableDatatypes[]=ezfloat
ExportableDatatypes[]=ezidentifier
ExportableDatatypes[]=ezenhancedobjectrelation
ExportableDatatypes[]=ezselection
ExportableDatatypes[]=ezoption
ExportableDatatypes[]=ezcountry
ExportableDatatypes[]=eztime
ExportableDatatypes[]=ezdate
ExportableDatatypes[]=ezdatetime
ExportableDatatypes[]=smileobjectrelationlist
ExportableDatatypes[]=ezbirthday


# All handlerfiles are sought in the base directory,
# extension/bccie/modules/bccie/,
# if you want to place them some place down the path
# from there, add the path from there.

[ezstring]
HandlerFile=ezstringhandler.php
HandlerClass=eZStringHandler

[ezinteger]
HandlerFile=ezintegerhandler.php
HandlerClass=eZIntegerHandler

[ezxmltext]
HandlerFile=ezxmltexthandler.php
HandlerClass=eZXMLTextHandler

[ezboolean]
HandlerFile=ezbooleanhandler.php
HandlerClass=eZBooleanHandler

[ezidentifier]
HandlerFile=ezidentifierhandler.php
HandlerClass=eZIdentifierHandler

[ezfloat]
HandlerFile=ezfloathandler.php
HandlerClass=eZFloatHandler

[ezemail]
HandlerFile=ezemailhandler.php
HandlerClass=eZEmailHandler

[ezurl]
HandlerFile=ezurlhandler.php
HandlerClass=eZURLHandler

[eztext]
HandlerFile=eztexthandler.php
HandlerClass=eZTextHandler

[ezenhancedobjectrelation]
HandlerFile=ezenhancedobjectrelationhandler.php
HandlerClass=ezenhancedobjectrelationHandler

# false will simply output the IDs of the related objects
OutputRelatedObjectNames=true

[ezselection]
HandlerFile=ezselectionhandler.php
HandlerClass=eZSelectionHandler

[ezoption]
HandlerFile=ezoptionhandler.php
HandlerClass=eZOptionHandler

[ezcountry]
HandlerFile=ezcountrtyhandler.php
HandlerClass=eZCountryHandler

[ezdatetime]
HandlerFile=ezdatetimehandler.php
HandlerClass=eZDateTimeHandler

[eztime]
HandlerFile=eztimehandler.php
HandlerClass=eZTimeHandler

[ezdate]
HandlerFile=ezdatehandler.php
HandlerClass=eZDateHandler

[smileobjectrelationlist]
HandlerFile=smileobjectrelationlisthandler.php
HandlerClass=smileobjectrelationlistHandler

[ezbirthday]
HandlerFile=ezbirthdayexporthandler.php
HandlerClass=eZBirthdayExportHandler

*/ ?>