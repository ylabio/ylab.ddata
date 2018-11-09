/**
 * Описание попапп окна с предварительной настройкой сущности битрикс
 */

BX.ready(function () {
    var WindowEntityPrepareForm;
    var WindowEntityPrepareFormParams;

    WindowEntityPrepareFormParams = {
        'title': BX.message('YLAB_DDATA_ENTITY_PREPARE_WINDOW_TITLE'),
        'content_url': '/bitrix/admin/ylab.ddata_entity_prepare_form.php',
        'content_post': '',
        'min_width': 600,
        'min_height': 500,
        'draggable': true,
        'resizable': true
    };
    WindowEntityPrepareForm = new BX.CDialog(WindowEntityPrepareFormParams);

    if (typeof window.YlabDdata === "undefined") {
        window.YlabDdata = {
            'WindowEntityPrepareForm': WindowEntityPrepareForm
        }
    } else {
        window.YlabDdata.WindowEntityPrepareForm = WindowEntityPrepareForm;
    }
});