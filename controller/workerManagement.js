$(document).ready(app);
let scrap_data = [];
let bodyScrap;
let columnTable;
let jsonData = {};
let docCurrent = "";
let startDateCurrent = "";
let stopDateCurrent = '';
let currentInfoId = '';
let row = "";
let newEmp = '';
let newEmpName = '';
let workTypeCurrent = "";
let workLocation = "";
// เปลี่ยนบริษัทตามการ Login
let company = '';
let checkedIds = [];
const rowArrayChecked = [];
async function app() {
    $('.vendor-select').select2();
    company = localStorage.getItem('vendorName');
    await getReport();
    await getWorkerAssigned();
    await getWorker();
    $('.overlay').hide();
    // $('#empModal').modal('show');
}


$('#btnSubmit').on('click', async function () {
    $('.overlay').show();
    let dataArray = [];
    const rows = document.querySelectorAll("#bodyReport tr");
    const getData = async () => {
        for (const row of rows) {
            const documentNumber = row.querySelector("td:nth-child(1)").textContent.trim();
            const jobType = row.querySelector("td:nth-child(3)").textContent.trim();
            const selectElement = row.querySelector("td:nth-child(7) select"); // เลือก <select> ในคอลัมน์ที่ต้องการ
            const contractorName = selectElement ? selectElement.value : "ไม่มีการเลือก";
            if (contractorName != "เลือก Vendor") {
                dataArray.push({ documentNumber, jobType, contractorName });
                // หากมีงาน async เช่น fetch หรืออื่นๆ ใส่ await ตรงนี้ได้
                await new Promise(resolve => setTimeout(resolve, 10)); // ตัวอย่างดีเล็กน้อย
            }
        }
        // ทำงานถัดไปที่ต้องการได้เลย เช่น เรียกฟังก์ชันต่อไป
    };
    getData();
    $('#btnSubmit').hide();
    const url = '../model/insertVendorToDoc.php';
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
            return;
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
$('#confirmEmp').on('click', comfirmEmp)
$('#confirmChangeEmp').on('click', comfirmChangeEmp)

// ลบแถวเมื่อกดปุ่มลบ
$(document).on("click", ".btnDelete", function () {
    $(this).closest("tr").remove();
    updateRowNumbers();
});


async function comfirmChangeEmp() {
    let url = `../model/UpdateWorkerToDoc.php`;
    let headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestData = {
        row, newEmp, newEmpName
    }
    const requestOption = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };

    console.log(JSON.stringify(requestData));
    $('.overlay').show();
    try {
        const response = await fetch(url, requestOption);
        const status = response.status;
        const data = await response.json();
        // Listen for change events on checkboxes within #divEmps
        $('.overlay').hide();
        if (status == 200) {
            location.reload();
        }
        alert(data.message);
    } catch (error) {
        // Handle any errors
        $('.overlay').hide();
        console.log('Error:', error);
    }
}
async function comfirmEmp() {
    let doc = docCurrent;
    let work_type = workTypeCurrent;
    let empsList = checkedIds;
    let url = `../model/insertWorkerToDoc.php`;
    let headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestData = {
        doc, work_type, empsList, workLocation, company, startDateCurrent, stopDateCurrent, currentInfoId
    }
    const requestOption = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };
    console.log(JSON.stringify(requestData));
    $('.overlay').show();
    try {
        const response = await fetch(url, requestOption);
        const status = response.status;
        const data = await response.json();
        // Listen for change events on checkboxes within #divEmps
        $('.overlay').hide();
        if (status == 200) {
            location.reload();
        }
        alert(data.message);

    } catch (error) {
        // Handle any errors
        $('.overlay').hide();
        console.log('Error:', error);
    }
}
async function getReport() {
    const url = `../model/getDocByVendor.php?vendor=${company}`;
    console.log(url);
    const requestOptions = {
        method: 'GET',
    };
    $('.overlay').show();
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        let bodyTable = $('#bodyReport');
        let text = '';
        let row = 0;
        bodyTable.empty();
        if (data.length > 0) {
            data.forEach((element) => {
                if (element.vendor_assign && element.request_worker_doc_status == 'open') {
                    row++;
                    text += `<tr class="text-center">
                    <td class="text-center justify-content-center" style="white-space: nowrap;">${element.request_worker_doc}</td>
                        <td style="white-space: nowrap;">${element.work_location}</td>
                        <td style="white-space: nowrap;">${element.work_type}</td>
                        <td style="white-space: nowrap;">${element.worker_require}</td>
                        <td style="white-space: nowrap;">${!element.worker_received ? 0 : element.worker_received}</td>
                        <td style="white-space: nowrap;">${element.work_startDate.date.substring(0, 16)}</td>
                        <td style="white-space: nowrap;">${element.work_endDate.date.substring(0, 16)}</td>
                        <td style="white-space: nowrap;">${element.vendor_assign}</td>        
                        <td style="white-space: nowrap;"><button  onclick="showModalEmp('${element.work_location}','${element.worker_require}','${element.request_worker_doc}'
                        ,'${element.work_type}','${!element.worker_received ? 0 : element.worker_received}','${element.work_startDate.date.substring(0, 16)}','${element.work_endDate.date.substring(0, 16)}','${element.id}')" class='btn btn-secondary'>เลือกพนักงาน</button></td>     
                    </tr>`;
                }
            });
            if (row == 0) {
                text = `<tr>
                <td colspan="7">ไม่มีข้อมูล</td>
            </tr>`
            }
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
async function ChangeEmp(rowId) {
    row = rowId;
    $('#changeempModal').modal('show');
}
async function getWorkerAssigned() {
    const url = '../model/getWorkerAssigned.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestData = {
        company
    }
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };
    $('.overlay').show();
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        let bodyScrap = $('#bodyScrap');
        let text2 = '';
        bodyScrap.empty();
        console.log(data);
        if (data.length > 0) {
            data.forEach((element) => {

                text2 += `<tr class="text-center">
                    <td class="text-center justify-content-center" style="white-space: nowrap;">${element.emp_id}</td>
                        <td style="white-space: nowrap;">${element.emp_name}</td>
                        <td style="white-space: nowrap;">${element.work_location}</td>
                        <td style="white-space: nowrap;">${element.work_type}</td>
                        <td style="white-space: nowrap;">${element.dateStart.date.substring(0, 16)}</td>
                        <td style="white-space: nowrap;">${element.dateStop.date.substring(0, 16)}</td>  
                        <td style="white-space: nowrap;"><button onclick='ChangeEmp(${element.id})' class='btn btn-warning'>เปลี่ยนพนักงาน</button></td>     
                    </tr>`;
            });
            bodyScrap.append(text2);
            return;
        }
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
    $('.overlay').hide();
}
async function showModalEmp(workLocation_, $requireQty, doc, work_type, receive, startDate, stopDate, rowID) {
    docCurrent = doc;
    startDateCurrent = startDate;
    stopDateCurrent = stopDate;
    workTypeCurrent = work_type;
    currentInfoId = rowID;
    workLocation = workLocation_;
    if (!receive || receive == 'null') {
        receive = 0;
    }
    $('#machineShera').text(workLocation);
    $('#workType').text(work_type);
    $('#qty_pick').text(receive ? receive : '0');
    $('#request_qty').text($requireQty);
    $('#doc').text(doc);
    $('#empModal').modal('show');

}
async function closeDoc(doc, worktype) {
    $('.overlay').show();
    const url = '../model/closeInfoDoc.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ doc, worktype })
    };
    console.log(JSON.stringify({ doc, worktype }));
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        if (status == 200) {
            location.reload();
            return;
        }
        alert(data.message);
        $('.overlay').hide();
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
        $('.overlay').hide();
    }
}
async function getWorker() {
    const url = '../model/getWorker.php';
    const headers = new Headers();
    let divEmp = $('#divEmps');
    let ChangedivEmps = $('#ChangedivEmps');
    divEmp.empty();
    headers.append('Content-Type', 'application/json');
    const requestData = {
        // Request body data
        company
    };

    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };
    console.log(JSON.stringify(requestData));
    $('.overlay').show();
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        console.log(data);
        let text = '';
        let text2 = '';
        if (data.length > 0) {
            for (const worker of data) {
                text += `<div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="${worker.cardcode}">
                                <label class="form-check-label" for="${worker.cardcode}">
                                ${worker.th_name}
                                </label>
                        </div>`;


                text2 += `<div class="form-check">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="new${worker.cardcode}" value="${worker.cardcode}" checked>
                            <label class="form-check-label" for="new${worker.cardcode}">
                            ${worker.th_name}
                            </label>
                        </div>`
            }
        }
        divEmp.append(text);
        ChangedivEmps.append(text2);
        // Listen for change events on checkboxes within #divEmps
        $('#divEmps input[type="checkbox"]').on('change', function () {
            let id = $(this).attr('id');
            let currentPick = parseInt($('#qty_pick').text(), 10); // Make sure it's an integer
            let request_qty = parseInt($('#request_qty').text(), 10); // Make sure it's an integer
            console.log(currentPick);
            console.log(request_qty);
            // Check if currentPick is greater than or equal to request_qty before allowing the checkbox to be checked
            if (currentPick >= request_qty) {
                // Prevent checking the checkbox if the condition is met
                if ($(this).is(':checked')) {
                    alert('คุณเลือกเกินจำนวนที่ร้องขอ');
                    $(this).prop('checked', false); // Ensure the checkbox remains unchecked
                    return; // Stop the function here
                }
            }

            if ($(this).is(':checked')) {
                // Add to array if checked and not already in the array
                if (!checkedIds.includes(id)) {
                    checkedIds.push(id);
                }
            } else {
                // Remove from array if unchecked
                checkedIds = checkedIds.filter(item => item !== id);
            }

            $('#qty_pick').text(checkedIds.length);
            console.log(checkedIds);
        });

        $('input[name="exampleRadios"]').on('change', function () {
            newEmp = $(`input[name="exampleRadios"]:checked`).val();
            newEmpName = $(`input[name="exampleRadios"]:checked`).next('label').text();
        });
        $('.overlay').hide();

    } catch (error) {
        // Handle any errors
        $('.overlay').hide();
        console.log('Error:', error);
    }
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