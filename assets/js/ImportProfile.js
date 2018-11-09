/**
 * Выбор файла для загрузки
 */

function loadFile() {
    var btn = document.getElementById('import_profile');
    var input = btn.querySelector('input');

    btn.addEventListener("click", function () {

        this.querySelector('input').click();
    });

    input.addEventListener("change", function () {

        this.closest('form').submit();
    });
}

BX.ready(function () {

    loadFile();
});

BX.addCustomEvent('onAjaxSuccess', function () {

    loadFile();
});