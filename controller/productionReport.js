$(document).ready(app);
let scrap_data = [];
let bodyScrap;
let columnTable;
let jsonData = {};

const rowArrayChecked = [];
async function app() {

    await getReport();
    $('.overlay').hide();
    // getDoc();
    // scrap_data = await getScrapt();
    // setTable();
}
let machine = () => {
    return "M08";
    return localStorage.getItem('machine');
}

async function getReport() {
    const url = '../model/getdoc.php';
    const requestOptions = {
        method: 'GET',
    };
    $('.overlay').show();
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        let bodyTable = $('#bodyReport');
        let text = '';
        bodyTable.empty();
        console.log(data);
        if (data.length > 0) {
            let row = 1;
            data.forEach((element) => {
                let statusColor = "";
                switch (element.request_worker_doc_status) {
                    case "open":
                        statusColor = 'success'
                        break;
                    case "cancel":
                        statusColor = 'secondary'
                        break;
                    default:
                        statusColor = 'dark'
                        break;
                }
                text += `<tr>
                        <td class="text-center justify-content-center" white-space: nowrap;>${row}</td>
                            <td white-space: nowrap;>${element.request_worker_doc}</td>
                            <td white-space: nowrap;>${element.work_type}</td>
                            <td white-space: nowrap;>${element.worker_require}</td>
                            <td white-space: nowrap;>${element.work_startDate.date}</td>
                            <td white-space: nowrap;>${element.work_endDate.date}</td>
                            <td white-space: nowrap;>${element.vendor_assign}</td>
                           <td class='rounded text-light bg-${statusColor} text-center' white-space: nowrap;>${element.request_worker_doc_status.toUpperCase()}</td>
                        </tr>`;
                row++;
            });
            bodyTable.append(text);
            return;
        }
        text = `<tr>
            <td colspan="7">ไม่มีข้อมูล</td>
        </tr>`
        bodyTable.append(text);
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
    $('.overlay').hide();
}
$('#searchInput').on('keyup', function () {
    var value = $(this).val().toUpperCase();
    $('#bodyReport tr').filter(function () {
        var rowText = $(this).text().toUpperCase();
        $(this).toggle(rowText.indexOf(value) > -1);
    });
});
