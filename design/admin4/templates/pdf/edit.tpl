{* Create or edit a PDF export (pdf/edit/<id>): a draft of the export while the form is open.

   Five groups, each saying what it is for: the title; what the PDF contains (the source node, chosen with Browse,
   and whether the nodes below it of chosen classes are included); the front page; the footer; and how the PDF is
   produced (generated once into a stored file, or on the fly for every download) with the file name. An error
   names its field, is listed at the top with a link to the field and is announced.

   The field names (Title, DisplayFrontpage, IntroText, SubText, ShowFooter, FooterText, SourceNode, ExportType,
   ClassList[], DestinationType, DestinationFile), the buttons (ExportPDFBrowse, ExportPDFButton, DiscardButton) and
   the action are those the view has always read; so are the variables pdf_export, export_type,
   export_class_array, validation and set_warning. The view adds pdf_source, pdf_errors, pdf_is_new,
   pdf_draft_other, pdf_selected_classes, pdf_published_file, pdf_storage_directory and redirect_if_discarded;
   without them the form still works. The same file is in design/admin and design/admin4. This is an edit view,
   which admin4 draws without its main card; .exp-standalone gives the page its own. Works without javascript.
   Guide: doc/guides/pdf-exports.md *}
{include uri='design:pdf/exp_style.tpl'}

{def $errors = first_set( $pdf_errors, hash() )
     $source = first_set( $pdf_source, false() )
     $is_new = first_set( $pdf_is_new, false() )
     $draft_other = first_set( $pdf_draft_other, false() )
     $selected = first_set( $pdf_selected_classes, $pdf_export.export_classes_array )
     $published_file = first_set( $pdf_published_file, false() )
     $storage = first_set( $pdf_storage_directory, 'var/storage/pdf' )
     $stored = $export_type|ne( 2 )
     $tree = $pdf_export.export_structure|eq( 'tree' )}

<form action={concat( 'pdf/edit/', $pdf_export.id )|ezurl} method="post" name="ExportPDF" class="exp-lists exp-standalone exp-pdf" aria-labelledby="pdf-edit-title" novalidate="novalidate">

<div class="context-block">
<div class="box-header"><div class="box-ml">
<div class="exp-title-row">
<h1 class="context-title" id="pdf-edit-title">{if $is_new}{'New PDF export'|i18n( 'design/admin/pdf/edit' )}{else}{'%pdf_export_title [PDF export]'|i18n( 'design/admin/pdf/edit',, hash( '%pdf_export_title', $pdf_export.title ) )|wash}{/if}</h1>
<span class="exp-meta">{'ID %id'|i18n( 'design/admin/pdf/edit',, hash( '%id', $pdf_export.id ) )}</span>
</div>
</div></div>

<div class="box-bc"><div class="box-ml"><div class="box-content">

<p class="exp-intro">{'A PDF export makes one PDF document of a node of the content tree, and if wanted of the nodes below it. Saving with OK stores the export; one that is generated once also writes its file at once.'|i18n( 'design/admin/pdf/edit' )}</p>

{if $errors|count}
<div class="exp-feedback is-bad" role="alert" id="pdf-edit-errors" tabindex="-1">
    <p><strong>{'The PDF export could not be stored.'|i18n( 'design/admin/pdf/edit' )}</strong> {'Correct these fields:'|i18n( 'design/admin/pdf/edit' )}</p>
    <ul>{foreach $errors as $error}<li><a href="#pdf-field-{$error.field|wash}">{$error.message|wash}</a></li>{/foreach}</ul>
</div>
{elseif and( is_set( $validation ), $validation.processed )}
<div class="exp-feedback is-bad" role="alert">
    <p><strong>{'The PDF export could not be stored.'|i18n( 'design/admin/pdf/edit' )}</strong></p>
    <ul>{foreach $validation.placement as $placement}<li>{$placement.text|wash}</li>{/foreach}</ul>
</div>
{/if}

{if $draft_other}
<div class="exp-feedback is-warn" role="status"><p>{'%name has had this export open since %time. Saving replaces what they have not saved.'|i18n( 'design/admin/pdf/edit',, hash( '%name', cond( $draft_other.name|ne( '' ), $draft_other.name, 'Another user'|i18n( 'design/admin/pdf/edit' ) ), '%time', $draft_other.modified|l10n( shortdatetime ) ) )|wash}</p></div>
{/if}

{* ---- Title ---- *}
<section class="exp-group" aria-labelledby="pdf-g-title">
    <h2 class="exp-h2" id="pdf-g-title">{'Title'|i18n( 'design/admin/pdf/edit' )}</h2>
    <p class="exp-help">{'The name of the export in this list. It is not printed in the PDF; the front page and the headings come from the content.'|i18n( 'design/admin/pdf/edit' )}</p>
    <div class="exp-field" id="pdf-field-Title">
        <label for="pdfTitle">{'Title'|i18n( 'design/admin/pdf/edit' )}</label>
        <input id="pdfTitle" type="text" name="Title" value="{$pdf_export.title|wash}" required="required" maxlength="255"{if is_set( $errors.Title )} aria-invalid="true" aria-describedby="pdfTitle-error"{/if} />
        {if is_set( $errors.Title )}<span class="exp-field-error" id="pdfTitle-error">{$errors.Title.message|wash}</span>{/if}
    </div>
</section>

{* ---- Content ---- *}
<section class="exp-group" aria-labelledby="pdf-g-content">
    <h2 class="exp-h2" id="pdf-g-content">{'Content'|i18n( 'design/admin/pdf/edit' )}</h2>
    <p class="exp-help">{'The PDF starts with the source node: its name as the first heading, then its attributes. A tree adds the nodes below it, level by level, but only those of the classes ticked below; the others and everything under them are left out.'|i18n( 'design/admin/pdf/edit' )}</p>

    <div class="exp-form-fields exp-form-wide">
    <div class="exp-field exp-field-wide" id="pdf-field-SourceNode">
        <span class="exp-label" id="pdf-source-label">{'Source node'|i18n( 'design/admin/pdf/edit' )}</span>
        {if and( $source, $source.exists )}
        <div class="exp-source" aria-labelledby="pdf-source-label">
            <div class="exp-source-text">
                <strong>{$source.name|wash}</strong>
                <span class="exp-meta">{$source.path|wash}</span>
                <span class="exp-meta">{'%class, section %section, node %id, %children direct children'|i18n( 'design/admin/pdf/edit',, hash( '%class', $source.class_name, '%section', cond( $source.section|ne( '' ), $source.section, '?' ), '%id', $source.node_id, '%children', $source.children ) )|wash}</span>
            </div>
            <input class="exp-btn" type="submit" name="ExportPDFBrowse" value="{'Browse'|i18n( 'design/admin/pdf/edit' )}" title="{'Choose another source node. What is typed in the form is kept.'|i18n( 'design/admin/pdf/edit' )}" />
        </div>
        {elseif and( $source, $source.exists|not )}
        <div class="exp-source is-bad" aria-labelledby="pdf-source-label">
            <div class="exp-source-text"><strong>{'Node %id no longer exists.'|i18n( 'design/admin/pdf/edit',, hash( '%id', $source.node_id ) )}</strong> {'Choose another source node.'|i18n( 'design/admin/pdf/edit' )}</div>
            <input class="exp-btn" type="submit" name="ExportPDFBrowse" value="{'Browse'|i18n( 'design/admin/pdf/edit' )}" />
        </div>
        {elseif and( is_set( $pdf_source )|not, $pdf_export.source_node )}
        <div class="exp-source" aria-labelledby="pdf-source-label">
            <div class="exp-source-text"><strong>{$pdf_export.source_node.name|wash}</strong> <span class="exp-meta">{$pdf_export.source_node.class_name|wash}</span></div>
            <input class="exp-btn" type="submit" name="ExportPDFBrowse" value="{'Browse'|i18n( 'design/admin/pdf/edit' )}" />
        </div>
        {else}
        <div class="exp-source" aria-labelledby="pdf-source-label">
            <div class="exp-source-text">{'There is no source node.'|i18n( 'design/admin/pdf/edit' )} {'Choose the node the PDF starts from.'|i18n( 'design/admin/pdf/edit' )}</div>
            <input class="exp-btn exp-btn-primary" type="submit" name="ExportPDFBrowse" value="{'Browse'|i18n( 'design/admin/pdf/edit' )}" />
        </div>
        {/if}
        {if is_set( $errors.SourceNode )}<span class="exp-field-error" id="pdfSource-error">{$errors.SourceNode.message|wash}</span>{/if}
        <input type="hidden" name="SourceNode" value="{$pdf_export.source_node_id|wash}" />
    </div>

    <fieldset class="exp-field exp-field-wide" id="pdf-field-ExportType">
        <legend>{'Export structure'|i18n( 'design/admin/pdf/edit' )}</legend>
        <div class="exp-choices">
            <label class="exp-choice"><input type="radio" name="ExportType" value="node"{if $tree|not} checked="checked"{/if} />
                <span><strong>{'Node'|i18n( 'design/admin/pdf/edit' )}</strong><span class="exp-help">{'The source node only.'|i18n( 'design/admin/pdf/edit' )}</span></span></label>
            <label class="exp-choice"><input type="radio" name="ExportType" value="tree"{if $tree} checked="checked"{/if} />
                <span><strong>{'Tree'|i18n( 'design/admin/pdf/edit' )}</strong><span class="exp-help">{'The source node and the nodes below it of the classes ticked below, at every level.'|i18n( 'design/admin/pdf/edit' )}</span></span></label>
        </div>
    </fieldset>

    <fieldset class="exp-field exp-field-wide" id="pdf-field-ClassList">
        <legend>{'Export classes (if exporting a tree)'|i18n( 'design/admin/pdf/edit' )}</legend>
        <div class="exp-classes" role="group" aria-describedby="{if is_set( $errors.ClassList )}pdfClasses-error {/if}pdfClasses-help"{if is_set( $errors.ClassList )} aria-invalid="true"{/if}>
        {foreach $export_class_array as $class}
            <label><input type="checkbox" name="ClassList[]" value="{$class.id}"{if $selected|contains( $class.id )} checked="checked"{/if} /> {$class.name|wash}</label>
        {/foreach}
        </div>
        {if is_set( $errors.ClassList )}<span class="exp-field-error" id="pdfClasses-error">{$errors.ClassList.message|wash}</span>{/if}
        <span class="exp-help" id="pdfClasses-help">{'Only read for a tree. A node of another class is left out together with everything below it, so tick the folders that hold the content as well.'|i18n( 'design/admin/pdf/edit' )}</span>
    </fieldset>
    </div>
</section>

{* ---- Front page ---- *}
<section class="exp-group" aria-labelledby="pdf-g-front">
    <h2 class="exp-h2" id="pdf-g-front">{'Frontpage'|i18n( 'design/admin/pdf/edit' )}</h2>
    <p class="exp-help">{'An optional first page with two lines of text, centred, before the table of contents.'|i18n( 'design/admin/pdf/edit' )}</p>
    <div class="exp-form-fields exp-form-wide">
        <div class="exp-field exp-field-wide">
            <label class="exp-check"><input type="checkbox" name="DisplayFrontpage"{if $pdf_export.show_frontpage|eq( 1 )} checked="checked"{/if} /> {'Display frontpage'|i18n( 'design/admin/pdf/edit' )}</label>
        </div>
        <div class="exp-field">
            <label for="pdfIntroText">{'Intro text'|i18n( 'design/admin/pdf/edit' )}</label>
            <textarea id="pdfIntroText" name="IntroText" cols="64" rows="3" aria-describedby="pdfIntroText-help">{$pdf_export.intro_text|wash}</textarea>
            <span class="exp-help" id="pdfIntroText-help">{'The large line, such as the name of the document.'|i18n( 'design/admin/pdf/edit' )}</span>
        </div>
        <div class="exp-field">
            <label for="pdfSubText">{'Sub text'|i18n( 'design/admin/pdf/edit' )}</label>
            <textarea id="pdfSubText" name="SubText" cols="64" rows="3" aria-describedby="pdfSubText-help">{$pdf_export.sub_text|wash}</textarea>
            <span class="exp-help" id="pdfSubText-help">{'The smaller line below it, such as a date or an edition.'|i18n( 'design/admin/pdf/edit' )}</span>
        </div>
    </div>
</section>

{* ---- Footer. The line that used to read "Exponential PDF export" on every page of every export, because it was
   written into a template. ---- *}
<section class="exp-group" aria-labelledby="pdf-g-footer">
    <h2 class="exp-h2" id="pdf-g-footer">{'Footer text'|i18n( 'design/admin/pdf/edit' )}</h2>
    <p class="exp-help" id="pdfFooter-help">{'Shown at the foot of every page, beside the page number. Leave the box empty for the default wording, or clear the tick for no text at all.'|i18n( 'design/admin/pdf/edit' )}</p>
    <div class="exp-form-fields exp-form-wide">
        <div class="exp-field exp-field-wide">
            <label class="exp-check"><input type="checkbox" name="ShowFooter"{if $pdf_export.show_footer|eq( 1 )} checked="checked"{/if} /> {'Print a footer line'|i18n( 'design/admin/pdf/edit' )}</label>
        </div>
        <div class="exp-field exp-field-wide" id="pdf-field-FooterText">
            <label for="pdfFooterText">{'Footer text'|i18n( 'design/admin/pdf/edit' )}</label>
            <input id="pdfFooterText" type="text" name="FooterText" maxlength="255" value="{$pdf_export.footer_text|wash}" aria-describedby="{if is_set( $errors.FooterText )}pdfFooterText-error {/if}pdfFooter-help"{if is_set( $errors.FooterText )} aria-invalid="true"{/if} />
            {if is_set( $errors.FooterText )}<span class="exp-field-error" id="pdfFooterText-error">{$errors.FooterText.message|wash}</span>{/if}
        </div>
    </div>
</section>

{* ---- Output ---- *}
<section class="exp-group" aria-labelledby="pdf-g-output">
    <h2 class="exp-h2" id="pdf-g-output">{'Export type'|i18n( 'design/admin/pdf/edit' )}</h2>
    <p class="exp-help">{'Whether the PDF is written once and kept, or made anew whenever somebody downloads it.'|i18n( 'design/admin/pdf/edit' )}</p>
    <div class="exp-form-fields exp-form-wide">
    <fieldset class="exp-field exp-field-wide">
        <legend class="exp-sr">{'Export type'|i18n( 'design/admin/pdf/edit' )}</legend>
        <div class="exp-choices">
            <label class="exp-choice"><input type="radio" name="DestinationType" value="url"{if $stored} checked="checked"{/if} />
                <span><strong>{'Generate once'|i18n( 'design/admin/pdf/edit' )}</strong><span class="exp-help">{'Written when the export is saved, and again with Regenerate on the list. The file is kept and served at a public address anyone can download from: fast, but it shows the content as it was then.'|i18n( 'design/admin/pdf/edit' )}</span></span></label>
            <label class="exp-choice"><input type="radio" name="DestinationType" value="download"{if $stored|not} checked="checked"{/if} />
                <span><strong>{'Generate on the fly'|i18n( 'design/admin/pdf/edit' )}</strong><span class="exp-help">{'Made for every download from the list, always current. Nothing is stored; a large tree takes a while every time.'|i18n( 'design/admin/pdf/edit' )}</span></span></label>
        </div>
    </fieldset>
    <div class="exp-field exp-field-wide" id="pdf-field-DestinationFile">
        <label for="pdfDestinationFile">{'File name'|i18n( 'design/admin/pdf/edit' )}</label>
        <input id="pdfDestinationFile" type="text" name="DestinationFile" value="{$pdf_export.pdf_filename|wash}" maxlength="100" spellcheck="false" autocomplete="off"
               aria-describedby="{if is_set( $errors.DestinationFile )}pdfDestinationFile-error {/if}pdfDestinationFile-help"{if is_set( $errors.DestinationFile )} aria-invalid="true"{/if} />
        {if is_set( $errors.DestinationFile )}<span class="exp-field-error" id="pdfDestinationFile-error">{$errors.DestinationFile.message|wash}</span>{/if}
        <span class="exp-help" id="pdfDestinationFile-help">{'Letters, digits, dots, dashes and underscores; ".pdf" is added when it is missing. Generated once, the file is written to %directory under this name, which no other stored export may use. Generated on the fly, it is the name the download is offered under.'|i18n( 'design/admin/pdf/edit',, hash( '%directory', concat( $storage, '/' ) ) )|wash}</span>
        {if and( $published_file, $published_file.exists )}
        <span class="exp-help">{'The stored file %name has %size and was generated %date. Saving with OK writes it anew; another name or generating on the fly deletes it.'|i18n( 'design/admin/pdf/edit',, hash( '%name', $published_file.name, '%size', $published_file.size|si( byte, auto ), '%date', $published_file.mtime|l10n( shortdatetime ) ) )|wash}</span>
        {/if}
    </div>
    </div>
</section>

<div class="exp-bottombar">
    <div class="exp-actions">
        <button type="submit" class="exp-btn exp-btn-primary" name="ExportPDFButton" value="{'OK'|i18n( 'design/admin/pdf/edit' )}">{'OK'|i18n( 'design/admin/pdf/edit' )}</button>
        <button type="submit" class="exp-btn" name="DiscardButton" value="{'Cancel'|i18n( 'design/admin/pdf/edit' )}" formnovalidate="formnovalidate">{'Cancel'|i18n( 'design/admin/pdf/edit' )}</button>
    </div>
    <p class="exp-meta">{'OK stores the export and, when it is generated once, writes its file; that can take a while for a large tree. Cancel throws away the changes made here.'|i18n( 'design/admin/pdf/edit' )}</p>
</div>

</div></div></div>
</div>

{if and( is_set( $redirect_if_discarded ), $redirect_if_discarded )}<input type="hidden" name="RedirectIfDiscarded" value="{$redirect_if_discarded|wash}" />{/if}
</form>

<script type="text/javascript">
var expPdfEditError = {if $errors|count}true{else}false{/if};
{literal}
(function () {
    var box = document.getElementById( 'pdf-edit-errors' );
    if ( expPdfEditError && box ) {
        var first = document.querySelector( '.exp-pdf [aria-invalid="true"]' );
        ( first && first.focus ? first : box ).focus();
        return;
    }
    var title = document.getElementById( 'pdfTitle' );
    if ( title ) { title.focus(); title.select(); }
})();
{/literal}
</script>
{undef $errors $source $is_new $draft_other $selected $published_file $storage $stored $tree}
