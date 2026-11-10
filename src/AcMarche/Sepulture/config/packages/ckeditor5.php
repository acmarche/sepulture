<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

/*
 * CKEditor 5 (mati365/ckeditor5-symfony). Replaces the former FOSCKEditorBundle "acmarche_config".
 * Self-hosted via AssetMapper, GPL licence (no key needed); see ckeditor5:assets-mapper:install.
 */
return static function (ContainerConfigurator $containerConfigurator): void {
    $containerConfigurator->extension('ckeditor5', [
        'presets' => [
            'sepulture' => [
                'editorType' => 'classic',
                'config' => [
                    'language' => 'fr',
                    'toolbar' => [
                        'items' => [
                            'undo', 'redo', '|',
                            'heading', '|',
                            'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript', 'removeFormat', '|',
                            'fontColor', 'fontBackgroundColor', '|',
                            'bulletedList', 'numberedList', 'outdent', 'indent', 'blockQuote', 'alignment', '|',
                            'link', 'insertTable', 'horizontalLine', '|',
                            'findAndReplace', 'selectAll', 'sourceEditing',
                        ],
                        'shouldNotGroupWhenFull' => false,
                    ],
                    'plugins' => [
                        'Essentials', 'Paragraph', 'Heading', 'Autoformat', 'PasteFromOffice', 'TextTransformation',
                        'Bold', 'Italic', 'Underline', 'Strikethrough', 'Subscript', 'Superscript', 'RemoveFormat',
                        'FontColor', 'FontBackgroundColor',
                        'List', 'Indent', 'IndentBlock', 'BlockQuote', 'Alignment',
                        'Link', 'AutoLink', 'HorizontalLine',
                        'Table', 'TableToolbar', 'TableProperties', 'TableCellProperties',
                        'FindAndReplace', 'SelectAll', 'SourceEditing', 'GeneralHtmlSupport',
                    ],
                    'table' => [
                        'contentToolbar' => ['tableColumn', 'tableRow', 'mergeTableCells', 'tableProperties', 'tableCellProperties'],
                    ],
                    // Keep legacy CKEditor 4 markup (inline styles, classes) intact when editing existing pages.
                    'htmlSupport' => [
                        'allow' => [
                            ['name' => '/.*/', 'attributes' => true, 'classes' => true, 'styles' => true],
                        ],
                    ],
                ],
            ],
        ],
    ]);
};
