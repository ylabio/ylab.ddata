/**
 * Описание попапп окна с предварительной настройкой сущности битрикс
 */

BX.ready(function () {
    var DataFieldButtons = BX.findChild(
        document,
        {
            className: 'DataFieldButton'
        },
        true,
        true
    );

    for (var i = 0; i < DataFieldButtons.length; i++) {
        BX.bind(DataFieldButtons[i], 'click', function () {
            var buttonParent = BX.findParent(
                this,
                {}
            );
            var optionsParent = BX.findPreviousSibling(
                buttonParent,
                {}
            );
            var optionSelected = BX.findChild(
                optionsParent,
                {}
            );
            var generatorID = optionSelected[optionSelected.selectedIndex].value;
            var propertyName = this.getAttribute('data-name');
            var hiddenInput = BX.findChild(
                document,
                {
                    attribute: {
                        'name': propertyName + '[' + generatorID + ']'
                    }
                },
                true,
                false
            );
            var propertyCode = this.getAttribute('data-code');
            var propertyProfile = this.getAttribute('data-profile');
            var WindowEntityDataFormParams = {
                'title': BX.message('YLAB_DDATA_ENTITY_DATA_WINDOW_TITLE'),
                'content_url': '/bitrix/admin/ylab.ddata_entity_data_form.php?generator=' + generatorID + '&property-name=' + propertyName + '&property-code=' + propertyCode + '&profile_id=' + propertyProfile + '&' + window.location.search.substr(1),
                'content_post': hiddenInput.value != null ? JSON.parse(hiddenInput.value) : '',
                'min_width': 600,
                'min_height': 500,
                'draggable': true,
                'resizable': true
            };
            if (generatorID != 0 && generatorID) {
                var WindowEntityDataForm = new BX.CDialog(WindowEntityDataFormParams);
                WindowEntityDataForm.Show();
            }
            if (typeof window.YlabDdata === "undefined") {
                window.YlabDdata = {
                    'WindowEntityDataForm': WindowEntityDataForm
                }
            } else {
                window.YlabDdata.WindowEntityDataForm = WindowEntityDataForm;
            }
        });
    }

    var selectOptions = BX.findChild(
        BX(document),
        {
            className: 'data-options-select'
        },
        true,
        true
    );
    for (var k = 0; k < selectOptions.length; k++) {
        BX.bind(selectOptions[k], 'change', function () {
            var tdParent = BX.findParent(
                this,
                {}
            );
            var inputParent = BX.findNextSibling(
                tdParent,
                {}
            );
            var inputHidden = BX.findChild(
                inputParent,
                {
                    className: 'options-input'
                }
            );
            var inputButton = BX.findChild(
                BX(document),
                {
                    className: 'DataFieldButton',
                    attribute: {
                        'data-name': this.getAttribute('name')
                    }
                },
                true,
                true
            )[0];
            var multiPropertiesCount = BX.findChild(
                BX(document),
                {
                    className: 'properties-count',
                    attribute: {
                        'data-name': this.getAttribute('name')
                    }
                },
                true,
                true
            )[0];
            var selectName = this.getAttribute('name') + '[' + this.value + ']';
            if (inputHidden) {
                BX.remove(inputHidden);
            }
            if (this.value) {
                BX.prepend(BX.create('input', {
                    attrs: {
                        'type': 'hidden',
                        'class': 'options-input',
                        'name': selectName,
                        'value': '{}',
                        'id': this.value
                    }
                }), BX.findParent(inputButton, {}));
                if (multiPropertiesCount) {
                    BX.style(multiPropertiesCount, 'display', 'table-cell');
                }
            } else {
                if (multiPropertiesCount) {
                    BX.style(multiPropertiesCount, 'display', 'none');
                    multiPropertiesCount.value = '';
                }
            }
        });
    }

    var saveBtn = BX.findChild(
        document,
        {
            attribute: {
                'name': 'save'
            }
        },
        true,
        true
    )[0];

    var applyBtn = BX.findChild(
        document,
        {
            attribute: {
                'name': 'apply'
            }
        },
        true,
        true
    )[0];

    var generateBtn = BX.findChild(
        document,
        {
            attribute: {
                'name': 'generate'
            }
        },
        true,
        true
    )[0];

    saveBtn.onclick = handlerClickBtnSettings;
    applyBtn.onclick = handlerClickBtnSettings;
    generateBtn.onclick = handlerClickBtnSettings;

    function handlerClickBtnSettings() {

        var _this = this;

        var required = BX.findChild(
            document,
            {
                attribute: {
                    'data-required': 'true'
                }
            },
            true,
            true
        );

        for (var i = 0; i < required.length; i++) {

            if (!required[i].value) {

                setTimeout(function (event) {

                    var loader = BX.findChild(
                        document,
                        {
                            class: 'adm-btn-load-img-green'
                        },
                        true,
                        true
                    )[0];
                    BX.remove(loader);

                    var loader = BX.findChild(
                        document,
                        {
                            class: 'adm-btn-load-img'
                        },
                        true,
                        true
                    )[0];
                    BX.remove(loader);

                    BX.removeClass(_this, 'adm-btn-load');
                    _this.removeAttribute('disabled');
                }, 100);
                event.preventDefault();
                break;
            }
        }
    };
});