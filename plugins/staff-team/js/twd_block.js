(function( blocks, element ) {
    var twd_gutenberg_block = function () {
        registerAllPluginBlocks();

        function registerAllPluginBlocks() {
            var twPluginsData = window['tw_gb_twd'];
            if (!twPluginsData) {
                return;
            }

            for (var pluginId in twPluginsData) {
                if (!twPluginsData.hasOwnProperty(pluginId)) {
                    continue;
                }

                if (!twPluginsData[pluginId].inited) {
                    twPluginsData[pluginId].inited = true;
                    registerPluginBlock(blocks, element, pluginId, twPluginsData[pluginId]);
                }
            }
        }

        function registerPluginBlock(blocks, element, pluginId, pluginData) {
            var el = element.createElement;

            var isPopup = pluginData.isPopup;

            var iconEl = el('img', {
                width: pluginData.iconSvg.width,
                height: pluginData.iconSvg.height,
                src: pluginData.iconSvg.src
            });

            blocks.registerBlockType(pluginId, {
                title: pluginData.title,
                icon: iconEl,
                category: 'common',
                attributes: {
                    shortcode: {
                        type: 'string'
                    },
                    popupOpened: {
                        type: 'boolean',
                        value: true
                    },
                    notInitial: {
                        type: 'boolean'
                    },
                    shortcode_id: {
                        type: 'string'
                    }
                },

                edit: function (props) {
                    if (!props.attributes.notInitial) {
                        props.setAttributes({
                            notInitial: true,
                            popupOpened: true
                        });

                        return el('p');
                    }

                    if (props.attributes.popupOpened) {
                        if (isPopup) {
                            return showPopup(props.attributes.shortcode_id);
                        }
                        else {
                            return showShortcodeList(props.attributes.shortcode);
                        }
                    }

                    if (props.attributes.shortcode) {
                        return showShortcode();
                    }
                    else {
                        return showShortcodePlaceholder();
                    }


                    function showPopup(shortcode_id) {
                        var shortcodeCbName = generateUniqueCbName(pluginId);
                        window[shortcodeCbName] = function (shortcode, shortcode_id) {
                            delete window[shortcodeCbName];

                            if (props) {
                                if (!shortcode_id) {
                                    props.setAttributes({shortcode: shortcode, shortcode_id: shortcode, popupOpened: false});
                                }
                                else {
                                    props.setAttributes({shortcode: shortcode, shortcode_id: shortcode_id, popupOpened: false});
                                }
                            }
                        };
                        props.setAttributes({popupOpened: true});
                        var elem = el('form', {className: 'tw-container'}, el('div', {className: "twd_tw-container-wrap"}, el('span', {
                            className: "media-modal-close",
                            onClick: close
                        }, el("span", {className: "media-modal-icon"})), el('iframe', {src: pluginData.data.shortcodeUrl + '&callback=' + shortcodeCbName + '&edit=' + shortcode_id})));
                        return elem;
                    }

                    function showShortcodeList(shortcode) {
                        props.setAttributes({popupOpened: true});
                        var children = [];
                        var shortcodeList = JSON.parse(pluginData.data);
                        shortcodeList.inputs.forEach(function (inputItem) {
                            if (inputItem.type === 'select') {
                                children.push(el('option', null, '- Select -'));
                                if (inputItem.options.length) {
                                    inputItem.options.forEach(function (optionItem) {
                                        var shortcode = '[' + shortcodeList.shortcode_prefix + ' ' + inputItem.shortcode_attibute_name + '="' + optionItem.id + '"]';
                                        children.push(
                                            el('option', {value: shortcode, dataId: optionItem.id}, optionItem.name)
                                        );
                                    })
                                }
                            }
                        });

                        if (shortcodeList.shortcodes) {
                            shortcodeList.shortcodes.forEach(function (shortcodeItem) {
                                children.push(
                                    el('option', {value: shortcodeItem.shortcode, dataId: shortcodeItem.id}, shortcodeItem.name)
                                );
                            });
                        }


                        return el('form', {onSubmit: chooseFromList}, el('div', {}, `Select ${pluginData.title}`), el('select', {
                            value: shortcode,
                            onChange: chooseFromList
                        }, children));
                    }

                    function showShortcodePlaceholder() {
                        props.setAttributes({popupOpened: false});
                        return el('p', {
                            style: {
                                'cursor': "pointer"
                            },

                            onClick: function () {
                                props.setAttributes({popupOpened: true});
                            }.bind(this)
                        }, "You're not select any team. Click to choose team");
                    }

                    function showShortcode() {
                        return el('img', {
                            src: pluginData.iconUrl,
                            alt: pluginData.title,
                            style: {
                                'height': "36px",
                                'width': "36px"
                            },
                            onClick: function () {
                                props.setAttributes({popupOpened: true});
                            }.bind(this)
                        });
                    }

                    function close() {
                        props.setAttributes({popupOpened: false});
                    }

                    function chooseFromList(shortcode_id) {

                        var selected = event.target.querySelector('option:checked');
                        props.setAttributes({shortcode: selected.value, shortcode_id: selected.dataId, popupOpened: false});
                        event.preventDefault();
                    }
                },

                save: function (props) {
                    return props.attributes.shortcode;
                }
            });
        }

        function generateUniqueCbName(pluginId) {
            return 'wdg_cb_' + pluginId;
        }
    }
    new twd_gutenberg_block();
} )(
    window.wp.blocks,
    window.wp.element
);
