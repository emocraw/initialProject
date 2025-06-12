$(document).ready(app);
let scrap_data = [];
let bodyScrap;
let columnTable;
let jsonData = {};
let mcname = "";
let dateInput = "";
const rowArrayChecked = [];
async function app() {
    await setMatchine();
    $('.vendor-select').select2();
    $('.overlay').hide();
    // getDoc();
    // scrap_data = await getScrapt();
    // setTable();
}

$('#mcname').on('change', changeMachine)
async function changeMachine() {
    mcname = $('#mcname').val();
}
$('#dateInput').on('change', function () {
    dateInput = $(this).val();
});
$('#searchBtn').on("click", searchCheckIn);
async function setMatchine() {
    let selectOptionMc = $('#mcname');
    let machines = JSON.parse(localStorage.getItem('machine'));
    selectOptionMc.empty();
    let text = "";
    text += `<option  value="">เลือกเครื่องหน้างาน</option>`;
    machines.forEach(element => {
        switch (element) {
            case "MTN":
                text += `<option value="MTN1">MTN1</option>
                <option value="MTN2">MTN2</option>
                <option value="MNT3">MNT3</option>`;
                break;
            default:
                text += `<option value="${element}">${element}</option>`;
                break;
        }
    });
    selectOptionMc.append(text);
}
async function searchCheckIn() {
    const url = '../model/getCheckin.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    if (mcname == "" && dateInput == '') {
        alert("กรุณาระบุข้อมูลให้ครบถ้วน");
        return;
    }
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({
            mcname,
            dateInput
        })
    };
    console.log(JSON.stringify({
        mcname,
        dateInput
    }));
    $('.overlay').show();
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        console.log(data);
        let bodyTable = $('#bodyLoginLog');
        let text = '';
        bodyTable.empty();
        if (data.length > 0) {
            let row = 0;
            let comeQty = 0;
            data.forEach((element) => {
                row++;
                let checkOut = "";
                let checkIn = "";
                // let status = "ยังไม่มา";
                // let statusColor = 'danger';
                checkIn = element.timeIn ? element.timeIn.date : '';
                checkOut = element.timeOut ? element.timeOut.date : '';
                // checkOut = element.timeOut.date;
                // status = "มาแล้ว";
                // statusColor = 'success';
                comeQty++;
                let jobname = element.job ? element.job : 'ไม่ได้ระบุ';
                text += `<tr>
                    <td class="text-center justify-content-center" white-space: nowrap;>${row}</td>
                        <td white-space: nowrap;>${jobname}</td>
                        <td white-space: nowrap;>${element.th_name}</td>
                        <td white-space: nowrap;>${element.company}</td>
                        <td white-space: nowrap;>${element.department}</td>
                        <td white-space: nowrap;>${checkIn}</td>
                        <td white-space: nowrap;>${checkOut}</td>               
                    </tr>`;
            });
            bodyTable.append(text);
            $('.overlay').hide();
            $('#reqQty').text(row);
            $('#comeQty').text(comeQty);
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

// ลบแถวเมื่อกดปุ่มลบ
$(document).on("click", ".btnDelete", function () {
    $(this).closest("tr").remove();
    updateRowNumbers();
});

// ฟังก์ชันอัพเดตหมายเลขลำดับ
function updateRowNumbers() {
    $("#bodyScrap tr").each(function (index) {
        $(this).find("td:first").text(index + 1);
    });
}


async function deleteDetail(id, doc, qty) {
    const url = '../model/deleteDetail.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({
            id,
            doc,
            qty
        })
    };
    console.log(JSON.stringify({
        id,
        doc,
        qty
    }));
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        if (status == 200) {
            location.reload();
        }
        alert(data.message);
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
}