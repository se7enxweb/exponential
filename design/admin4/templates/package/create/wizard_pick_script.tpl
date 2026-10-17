{* The script of the pickers of the package creation wizard (.a4-package-pick: the classes, the extensions): it keeps
   "%count of %total selected" (.a4-package-pick-count, data-template) up to date and shows the filter
   (.a4-package-pick-filter, hidden without the script), which hides the rows whose data-filter does not hold the
   text typed. It posts nothing and changes no checkbox: a row hidden by the filter keeps its choice. Without the
   script the list is complete and the count is the one the page was drawn with. The code is page/package_wizard_pick.js.
   The same files are in design/admin and design/admin4. Guide: doc/features/6.0/package-wizard.md *}
{ezscript( array( 'page/package_wizard_pick.js' ) )}
