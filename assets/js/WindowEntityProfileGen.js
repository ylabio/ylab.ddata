/**
 * Скрипт для реализации пошагового выполнения скрипта генерации элементов
 */
var bWorkFinished = false;
var bSubmit;
var clean_test_table = '<div style="height: 200px; overflow: scroll">' +
    '<table id="result_table" cellpadding="0" cellspacing="0" border="0" width="100%" class="internal">' +
    '<tr class="heading">' +
    '<td>' + BX.message('YLAB_DDATA_CURRENT_ACTION') + '</td>' +
    '<td width="1%">&nbsp;</td>' +
    '</tr>' +
    '</table>' +
    '</div>';

function set_start(val, bDetail = true) {
    document.getElementById('work_start').disabled = val ? 'disabled' : '';
    document.getElementById('work_stop').disabled = val ? '' : 'disabled';
    if (bDetail) {
        document.getElementById('progress').style.display = val ? 'block' : 'none';
    }
    iCountElements = document.getElementById('count-elements').value;
    iDuration = document.getElementById('duration').value;
    sAjaxPath = document.getElementById('ajax-path').value;

    if (val) {
        ShowWaitWindow();
        document.getElementById('result').innerHTML = clean_test_table;
        if (!bDetail) {
            document.getElementById('result').style.display = 'none';
        } else {
            document.getElementById('result').style.display = 'block';
        }
        document.getElementById('status').innerHTML = BX.message('YLAB_DDATA_RUN');

        document.getElementById('percent').innerHTML = '0%';
        document.getElementById('indicator').style.width = '0%';


        CHttpRequest.Action = work_onload;
        CHttpRequest.Send(sAjaxPath + '&count=' + iCountElements + '&duration=' + iDuration);
    } else {
        CloseWaitWindow();
    }
}

function work_onload(result) {
    try {
        eval(result);
        iPercent = CurrentStatus[0];
        strNextRequest = CurrentStatus[1];
        strCurrentAction = CurrentStatus[2];
        bError = CurrentStatus[3];
        iCountElements = document.getElementById('count-elements').value;
        iDuration = document.getElementById('duration').value;

        if (bError == true) {
            throw new Error(strCurrentAction);
        } else {
            document.getElementById('percent').innerHTML = iPercent + '%';
            document.getElementById('indicator').style.width = iPercent + '%';

            document.getElementById('status').innerHTML = BX.message('YLAB_DDATA_RUN');
            if (strCurrentAction != 'null') {
                oTable = document.getElementById('result_table');
                oRow = oTable.insertRow(-1);
                oCell = oRow.insertCell(-1);
                oCell.innerHTML = strCurrentAction;
                oCell = oRow.insertCell(-1);
                oCell.innerHTML = '';
            }

            if (strNextRequest && document.getElementById('work_start').disabled) {
                CHttpRequest.Send(sAjaxPath + strNextRequest + '&count=' + iCountElements + '&duration=' + iDuration);
            } else {
                set_start(0);
                bWorkFinished = true;
            }
        }

    }
    catch (e) {
        CloseWaitWindow();
        document.getElementById('work_start').disabled = '';
        document.getElementById('work_stop').disabled = 'disabled';
        document.getElementById('result').setAttribute('style', 'display:none');
        document.getElementById('progress').setAttribute('style', 'display:none');
        if (e == "") {
            alert(BX.message('YLAB_DDATA_ERROR_DATA'));
        } else {
            alert(e);
        }
    }
}

BX.ready(function () {
    BX.bind(BX('detail'), 'change', function () {
        if (this.checked) {
            BX('work_start').setAttribute('onclick', 'set_start(1)');
        } else {
            BX('work_start').setAttribute('onclick', 'set_start(1, false)');
        }
    });
});