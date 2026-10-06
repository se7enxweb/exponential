{* Why a PDF export could not be generated, in words: the keys of expPDFExportGenerator (no_source, source_missing,
   bad_name, not_stored, not_generated, failed). Parameter: problem. Used by design:pdf/list.tpl.
   The same file is in design/admin and design/admin4. *}
{switch match=$problem}
{case match='no_source'}{'No source node is chosen. Edit the export and choose one with Browse.'|i18n( 'design/admin/pdf/list' )}{/case}
{case match='source_missing'}{'Its source node no longer exists. Edit the export and choose another one.'|i18n( 'design/admin/pdf/list' )}{/case}
{case match='bad_name'}{'Its file name cannot be used for a stored file. Edit the export and give it a name of letters, digits, dots, dashes and underscores.'|i18n( 'design/admin/pdf/list' )}{/case}
{case match='not_generated'}{'Its file has not been generated yet. Regenerate it first.'|i18n( 'design/admin/pdf/list' )}{/case}
{case match='not_stored'}{'It is generated on the fly and has no stored file.'|i18n( 'design/admin/pdf/list' )}{/case}
{case}{'The PDF templates wrote no file. The error log says why.'|i18n( 'design/admin/pdf/list' )}{/case}
{/switch}
