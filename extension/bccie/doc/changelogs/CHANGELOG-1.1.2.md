Changes for 1.1.2
=================

* Added: Changelog entry doc/changelogs/CHANGELOG-1.1.2.md
* Updated: Added cie.ini setting ExportUsingDaysCalcualation disabled by default. This is a BC break but the days calc code has long been an area of confusion and problems for many users. Bugfixes to doexport module view and bccieExportUtil class method getDateConditions date calculation code
* Updated: Refactoring doexport module view code internals into bccieExportUtils class. CS improvements. Bugfix for date filters. Bugfix for parser php warnings. Added cie.ini ExportExecutionTimeLimit setting which defaults to 180 seconds (previous static limitation)
* Added: Added bccieExportUtils helper class, moved key overview and export functionality into it
* Updated: Finally refactored original export.ini ExportableDatatypes datatype identifier settings and related datatype specific settings arrays to be sorted alphabetically. Removed unnecessary export.ini.append.php settings file
* Updated: Removed all deprecated ezp3 / php4 style class include 'include_once' calls. eZ Publish 3.x support for bccie is officially removed.
* Updated: Refactor to provide for using base handler as fallback during export when custom export datatype handler does not exist. Remove php4/ez3 style class includes as deprecated. Minor CS improvements. Fix fatal error when generating headers for an excluded field
* Updated: Fix navigation parts identifier to properly match ezp navigation part naming convention requirements
* Updated: Added cie.ini setting DisplayLeaveEmptyOption to control display of export form option. Defaults to enabled
* Added: Added cp1251 export format output handler
* Updated: Handler CS improvements
* Updated: Bugfix for parser class handler include_once calls
* Added: Added additional datatype support for ezobjectrelation, owenhancedselection and smileobjectrelationlist
* Added: Converted rest of documentation to markdown, excluding older changelogs
* Added: Moved doc/README to repository root as README.md and converted to markdown
* Added: Moved doc/LICENSE to repository root
* Added: Added export format output handler system and last 3 output handler format implementations as ini configurable options
* Added: Added export format output handler default to utf8 (pure)
* Added: Added composer.json
* Added: Added export file name setting
* Added: Added export file name date time format setting
* Updated: Minor improvements to documentation
