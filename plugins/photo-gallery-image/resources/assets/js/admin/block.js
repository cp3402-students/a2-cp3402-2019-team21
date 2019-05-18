(function (blocks, i18n, element, components) {
    var SelectControl = components.SelectControl;
    var blockStyle = { padding: '1px' };

    var el = element.createElement; // The wp.element.createElement() function to create elements.

    blocks.registerBlockType('photo-gallery-image/index', {
        title: 'GrandWP Gallery',

        icon: 'format-gallery',

        category: 'photo-gallery-image',

        attributes: {
            gallery_id: {
                type: 'string'
            }
        },

        edit: function (props) {


            var focus = props.focus;

            props.attributes.gallery_id =  props.attributes.gallery_id &&  props.attributes.gallery_id != '0' ?  props.attributes.gallery_id : false;

            return el(
                SelectControl,
                {
                    label: 'Select GrandWP Gallery',
                    value: props.attributes.gallery_id ? parseInt(props.attributes.gallery_id) : 0,
                    instanceId: 'gd-gallery-selector',
                    onChange: function (value) {
                        props.setAttributes({gallery_id: value});
                    },
                    options: gdphotogalleryblock.gdgallery,
                }
            );

        },

        save: function (props) {
            return el('p', {style: blockStyle}, '[gdgallery_gallery id_gallery="'+props.attributes.gallery_id+'"]');
        },
    });
})(
    window.wp.blocks,
    window.wp.i18n,
    window.wp.element,
    window.wp.components
);