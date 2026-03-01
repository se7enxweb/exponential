CREDITS
=======

Primary development and maintenance by Brookins Consulting

Extension originally written and created by Mathias VITALIS

Released under the GNU General Public License v2 (or any later version)

* Project, [http://projects.ez.no/cie](http://projects.ez.no/cie)

* GitHub, [http://github.com/brookinsconsulting/bccie](http://github.com/brookinsconsulting/bccie)

* Composer Package, [https://packagist.org/packages/brookinsconsulting/bccie](https://packagist.org/packages/brookinsconsulting/bccie)


# Contributions

* Inspired from the csv export module from Gabriel Ambuehl

* Support for the ezoption and ezcountry datatype and csv header export from github.com/gerhardsletten

* Improved support for the ezselection datatype from Guillaume Kulakowski, [http://projects.ez.no/cie/forum/general/bug_with_ezselection_and_cie_1_0_4](http://projects.ez.no/cie/forum/general/bug_with_ezselection_and_cie_1_0_4)

* Support for the eztime, ezdate, ezdatetime and ezboolean datatypes from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* Parser improvements from github.com/Open-Wide

* Fix UTF-8 BOM for Excel from github.com/matthewkp

* Code Standards improvements from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* Modulelist ini setting addition from github.com/Open-Wide

* Empty attribute export behavior change from github.com/Open-Wide

* Empty attribute ini setting to control empty attribute export behavior change from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* Implemented export format output handler system change from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* Converted documentation into markdown format change from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* cp1251 export format output handler idea from github.com/Open-Wide, implementation from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* cie.ini setting DisplayLeaveEmptyOption idea from github.com/Open-Wide, implementation from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* Various bugfixes and improvements from github.com/Open-Wide

* Removed all deprecated ezp3 / php4 style class include 'include_once' calls. eZ Publish 3.x support for bccie is officially removed. From github.com/Open-Wide and [brookinsconsulting.com](http://brookinsconsulting.com)

* Refactor to provide for using base handler as fallback during export when custom export datatype handler does not exist. From github.com/Open-Wide

* Refactored original export.ini ExportableDatatypes datatype identifier settings and related datatype specific settings arrays to be sorted alphabetically. Idea from github.com/Open-Wide, implementation from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* Added bccieExportUtils helper class, moved key overview and export functionality into it. From github.com/Open-Wide

* Added cie.ini ExportExecutionTimeLimit setting which defaults to 180 seconds (previous static limitation). Implementation from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* Added cie.ini setting ExportUsingDaysCalcualation disabled by default. Idea from github.com/Open-Wide, implementation from Brookins Consulting, [brookinsconsulting.com](http://brookinsconsulting.com)

* Added support for ezbirthday datatype enabled by default. Implementation from github.com/punknroll
