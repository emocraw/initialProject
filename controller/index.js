$(document).ready(app);
let scrap_data = [];
let bodyScrap;
let columnTable;
let jsonData = {};
let mcname = "";
const rowArrayChecked = [];
async function app() {
    $('.vendor-select').select2();
    await setMatchine();
    await changeMachine();
    $('.overlay').hide();
    // getDoc();
    // scrap_data = await getScrapt();
    // setTable();
}

$('#mcname').on('change', changeMachine)
async function changeMachine() {
    mcname = $('#mcname').val();
    let workType = await getWorkType();
    await getReport();
    $("#bodyScrap").empty(); // ล้างข้อมูลใน tbody
    console.log(workType);
    if (!workType || workType.length == 0) {
        alert("ไม่พบข้อมูลทักษะงาน หรือ การจ้างงานในเครื่องจักรนี้");
        $('.overlay').hide();
        return;
    }
    addRow(workType);
    $('.overlay').hide();
}
$("#addBtn").on("click", addRow);



function addRow(workType) {
    // สร้างแถวใหม่
    var newRow = `
        <tr>
            <td class="text-center justify-content-center">1</td>
            <td>
                <select class="form-select" aria-label="Default select example">
                    <option selected>เลือกทักษะของพนักงาน</option>
                    `;
    workType.forEach(work => {
        newRow += `<option value="${work.work_name}">${work.work_name}</option>`;
    });
    newRow += `</select>
            </td>
            <td>
                <input type="text" class="form-control" placeholder="ระบุตัวเลขจำนวนคน" 
                    aria-label="Recipient's username" aria-describedby="button-addon2">
            </td>
            <td>
             <div class="row">
                <div class="col-6">
                    <input type="date" class="form-control" aria-describedby="button-addon2">
                </div>
                <div class="col-6">
                <select id='start_time' class="form-select time" aria-label="Default select example">
                    <option value="00:00">00:00</option>
                    <option value="08:00">08:00</option>
                    <option value="16:00">16:00</option>
                    <option value="20:00">20:00</option>
                </select>
                </div>
            </div>              
            </td>
            <td>
            <div class="row">
               <div class="col-6">
                    <input type="date" class="form-control" aria-describedby="button-addon2">
                </div>
                <div class="col-6">
                <select class="form-select time" aria-label="Default select example">
                    <option value="00:00">00:00</option>
                    <option value="08:00">08:00</option>
                    <option value="16:00">16:00</option>
                    <option value="20:00">20:00</option>
                </select>
                </div>
                </div>  
            </td>
            <td>
                <button class="btn btn-danger btnDelete">ลบ</button>
            </td>
        </tr>
        `;

    // เพิ่มแถวใหม่ลงใน tbody
    $("#bodyScrap").append(newRow);
    // updateSelect();
    // อัพเดตหมายเลขลำดับใหม่
    updateRowNumbers();
}
function updateSelect() {
    $('#bodyReport tr').each(function () {
        const $select = $(this).find('select');
        if ($select.length) {
            $select.empty(); // ล้าง option เดิม
            workType.forEach(opt => {
                $select.append($('<option>', {
                    value: opt.work_type,
                    text: opt.work_type
                }));
            });
        }
    });
}
async function getWorkType() {
    const url = '../model/getJobsByGroup.php';
    const headers = new Headers();
    const workGroup = $('#mcname').val();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ workGroup })
    };

    console.log(JSON.stringify({ workGroup }));

    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        if (status === 200) {
            return data;
        } else {
            return []; // กรณี status ไม่ใช่ 200
        }
    } catch (error) {
        console.log('Error:', error);
        return []; // 🔄 ย้ายมาไว้ที่นี่
    } finally {
        $('.overlay').hide(); // ✅ ใช้แค่เพื่อแสดง/ซ่อน loading ไม่ควร return ที่นี่
    }
}

$('#btnSubmit').on('click', async function () {
    $('.overlay').show();
    var dataArray = [];
    var rows = $("#bodyScrap tr").toArray(); // แปลง NodeList เป็น Array
    // ใช้ for...of ในการวนลูปแต่ละแถว

    for (let row of rows) {
        var $row = $(row);
        // ดึงข้อมูลจากแต่ละช่อง
        var skill = $row.find("select option:selected").eq(0).val();
        var peopleCount = $row.find("input[type='text']").eq(0).val();
        var startDate = $row.find("input[type='date']").eq(0).val();
        var endDate = $row.find("input[type='date']").eq(1).val();
        let startTime = $row.find("select option:selected").eq(1).val()
        let endTime = $row.find("select option:selected").eq(2).val()
        console.log(skill);
        console.log(peopleCount);
        console.log(startDate);
        console.log(endDate);
        console.log(startTime);
        console.log(endTime);
        if (!skill || !peopleCount || !startDate || !endDate || !mcname || !startTime || !endTime) {
            alert("กรุณากรอกข้อมูลให้ครบ");
            $('#submitModal').modal('hide');
            $('.overlay').hide();
            return;
        }

        // ตรวจสอบว่า startDate มากกว่า endDate หรือไม่
        if (startDate + " " + startTime && endDate + " " + endTime && new Date(startDate + " " + startTime) > new Date(endDate + " " + endTime)) {
            alert("วันที่เริ่มต้นต้องไม่มากกว่าวันที่สิ้นสุด");
            $('#submitModal').modal('hide');
            $('.overlay').hide();
            return;
        }
        // สร้าง Object และเก็บลง Array
        var rowData = {
            skill: skill,
            peopleCount: peopleCount,
            startDate: `${startDate} ${startTime}`,
            endDate: `${endDate} ${endTime}`,
            machine: mcname
        };
        dataArray.push(rowData);
    }
    console.log(dataArray);
    $('#btnSubmit').hide();
    const url = '../model/insertRequestWorker.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(dataArray)
    };
    console.log(JSON.stringify(dataArray));
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        if (status == 200) {
            location.reload();
        }
        alert(data.message);
        $('#submitModal').modal('hide');
        $('#btnSubmit').show();
        $('.overlay').hide();
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
        $('#submitModal').modal('hide');
        $('#btnSubmit').show();
        $('.overlay').hide();
    }
});
async function setMatchine() {
    let selectOptionMc = $('#mcname');
    selectOptionMc.empty();
    let machines = JSON.parse(localStorage.getItem('machine'));
    console.log(machines);
    let text = "";
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
            let row = 0;
            data.forEach((element) => {
                let assigned = element.worker_received ? element.worker_received : 0;
                if (element.request_worker_doc_status == 'open' && element.work_location == mcname) {
                    row++;
                    text += `<tr>
                    <td class="text-center justify-content-center" white-space: nowrap;>${row}</td>
                        <td white-space: nowrap;>${element.request_worker_doc}</td>
                        <td white-space: nowrap;>${element.work_type}</td>
                        <td white-space: nowrap;>${element.worker_require}</td>
                        <td white-space: nowrap;>${element.work_startDate.date}</td>
                        <td white-space: nowrap;>${element.work_endDate.date}</td>`;
                    if (assigned > 0) {
                        text += `<td class='text-success' white-space: nowrap;>Vedor Assigned</td>`;
                    } else {
                        text += `<td white-space: nowrap;><button onclick="deleteDetail('${element.id}','${element.request_worker_doc}','${element.worker_require}');" class="btn btn-danger m-1 btnDeleteInfo">ลบ</button></td> `;
                    }
                    text += `</tr > `;
                }
            });
            if (row == 0) {
                text = `< tr >
                        <td colspan="7">ไม่มีข้อมูล</td>
            </ > `
            }
            bodyTable.append(text);
            return;
        }
        text = `< tr >
                        <td colspan="7">ไม่มีข้อมูล</td>
        </ > `
        bodyTable.append(text);
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
    $('.overlay').hide();
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