document.addEventListener('DOMContentLoaded', function() {

    var $ = function(el) {
        return document.querySelectorAll(el);
    };

    function checkFields() {
        var arRequired = $('#entityForm [data-required="true"]');
        var arErrors = {};
        for (var i = 0; i < arRequired.length; i++) {
            editId = arRequired[i].closest('.adm-detail-content').getAttribute('id');
            tabMove = arRequired[i].closest('#entityForm').querySelector('#tabControl_tabs').querySelector('#tab_cont_' + editId);
            if (!arRequired[i].value) {
                arRequired[i].style.borderColor = 'red';
                arErrors[editId] = true;
            } else {
                arRequired[i].style.borderColor = '';
            }
            if (arErrors[editId] === true) {
                tabMove.style.borderColor = 'red';
            } else {
                tabMove.style.borderColor = '';
            }
        }
    }

    /**
     * Переключение при нажатии на кнопки Сохранить/Применить/Генерировать
     */
    function clickBtn(){

        checkFields();
    }
    $('#entityForm input[name="save"]')[0].addEventListener( "click" , clickBtn);
    $('#entityForm input[name="apply"]')[0].addEventListener( "click" , clickBtn);
    $('#entityForm input[name="generate"]')[0].addEventListener( "click" , clickBtn);
});