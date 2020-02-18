/**
 * TODO: Add transform option from shortcode
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-registration/#transforms-optional
 */
(() => {
    'use strict';

    const args = window.__shortberg_args__

    const {Fragment} = window.React
    const {blocks, components, element, editor, serverSideRender: SSR} = window.wp;
    const {createElement: el} = element;
    const {registerBlockType} = blocks;
    const {InspectorControls, InnerBlocks, RichText} = editor;

    const registeredBlocks = args.blocks.map(({render_callback, editor_style, style, ...block}) => block);


    registeredBlocks.forEach(({name, attributes, fields = [], children = null, richText = null, ...configs}) =>

        registerBlockType(name, {
            attributes,
            ...configs,
            edit({attributes, setAttributes, className}) {
                return el('div', {className},
                    // richText && el(RichText, {
                    //     value: attributes.richTextContent,
                    //     onChange: richTextContent => setAttributes({richTextContent}),
                    //     selector: 'p',
                    //     ...richText
                    // }),
                    el(SSR, {block: name, attributes}),
                    children && el(InnerBlocks, typeof children === 'object' ? children : {}),
                    el(InspectorControls, {},
                        fields.map(
                            ({attribute, component = 'TextControl', valueProp = 'value', changeEvent = 'onChange', ...settings}, key) =>
                                el(editor[component] || components[component] || component, {
                                    key,
                                    [valueProp]: attributes[attribute],
                                    [changeEvent]: value => setAttributes({[attribute]: value}),
                                    ...settings
                                })),
                    )
                );
            },
            save({attributes}) {
                return el(Fragment, {},
                    children && el(InnerBlocks.Content, {key: 'InnerBlocks'}),
                    // richText && el(RichText.Content, {
                    //     key: 'RichText',
                    //     value: attributes.richTextContent,
                    //     ...richText
                    // }),
                )
            },
        }));


})();