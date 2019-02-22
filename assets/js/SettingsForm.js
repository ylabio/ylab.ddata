BX.ready(function () {
    BX.namespace('Ylab.Settings');
    BX.Ylab.Settings = function (sPropertyName, sGeneratorID) {
        var inputOptions = BX.findChild(
            BX(document),
            {
                attribute: {
                    'name': sPropertyName + '[' + sGeneratorID + ']'
                }
            },
            true,
            true
        )[0];
        if (inputOptions) {
            var optionsValue = JSON.parse(inputOptions.value);
        }
        if (inputOptions != undefined) {
            Object.keys(optionsValue).forEach(function (key, item) {

                var optionsForm = BX.findChild(
                    BX('WindowEntityDataForm'),
                    {
                        attribute: {
                            'name': 'option[' + key + ']'
                        }
                    },
                    true,
                    true
                )[0];
                if (optionsForm) {
                    optionsForm.value = optionsValue[key];
                }

                var optionsFormMultiple = BX.findChild(
                    BX('WindowEntityDataForm'),
                    {
                        attribute: {
                            'name': 'option[' + key + '][]'
                        }
                    },
                    true,
                    true
                )[0];
                if (optionsFormMultiple) {
                    var optionsForms = optionsFormMultiple.options;
                    for (var i = 0; i < optionsForms.length; i++) {
                        for (var j = 0; j < optionsValue[key].length; j++) {
                            if (optionsForms[i].value == optionsValue[key][j]) {
                                optionsForms[i].selected = true;
                            }
                        }
                    }
                }
            });
        }
    }
});