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
let cancelObj = {}
const rowArrayChecked = [];
async function app() {
    await setMatchine();
    await search();
    $('.vendor-select').select2();
    // company = localStorage.getItem('vendorName');
    // await getReport();
    // await getWorkerAssigned();
    // await getWorker();
    $('#table_special').hide();
    $('.overlay').hide();
    // $('#empModal').modal('show');

}


$('#search').on('click', search)
async function search() {
    console.log('search');
    let company = $('#mcname').val();
    if (!company) {
        alert('กรุณาเลือกเครื่องจักร์');
        return;
    }
    let text = '';
    $('.overlay').show();
    // let url = '../model/getnotAssigned.php';
    let url = '../model/getWaitAprove.php';
    let bodyScrap = $('#bodyScrap');
    $('#table_normal').show();
    $('#table_special').hide();
    bodyScrap.empty();
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestData = {
        company
    };
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };
    console.log(JSON.stringify(requestData));
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        if (status == 200) {
            if (Array.isArray(data)) {
                for (const item of data) {
                    // สร้างแถว HTML สำหรับแสดงข้อมูล
                    let checkin = item.check_in ? item.check_in.date.substring(0, 16) : 'ไม่พบข้อมูล';
                    let checkout = item.check_out ? item.check_out.date.substring(0, 16) : 'ไม่พบข้อมูล';
                    let jobassign = item.job_transfer ? item.job_transfer : 'ไม่พบข้อมูล';
                    let classJobassign = jobassign == 'ไม่พบข้อมูล' ? 'text-danger' : 'text-success';
                    let classcheckIn = checkin == 'ไม่พบข้อมูล' ? 'text-danger' : 'text-success';
                    let classcheckOut = checkout == 'ไม่พบข้อมูล' ? 'text-danger' : 'text-success';
                    if (item.emp_id.length < 5) {
                        alert("มีข้อมูลรหัสบัตรผิดพลาด กรุณาตรวจสอบข้อมูล");
                        continue;
                    }
                    text += `
                    <tr class="text-center">
                    <td>${item.emp_id}</td>
                        <td>${item.emp_name}</td>
                        <td>${item.mc_receive}</td>
                        <td class="${classJobassign}">${jobassign}</td>
                        <td class="${classcheckIn}">${checkin}</td>
                        <td class="${classcheckOut}">${checkout}</td>`;
                    if (item.job == 'special') {
                        text += `
                            <td>
                                <input type="checkbox" class="job-checkbox" data-checkOut="${checkout}" data-id=${item.id} data-qrcode="${item.emp_id}" data-th_name="${item.emp_name}" data-checkin="${checkin}">
                            </td>`;

                    } else {
                        text += `<td> <div class='btn btn-warning' onclick="cancelAssign('${item.cardcode}', '${item.th_name}', '${checkin}', '${jobassign}')">Reject</div> 
                        </> `;
                    }
                }
            }
        }
        bodyScrap.html(text);
        $('.job-checkbox').on('change', function () {
            const id = $(this).data('id');
            const th_name = $(this).data('th_name');
            const checkIn = $(this).data('checkin');
            const checkOut = $(this).data('checkout');
            const cardcode = $(this).data('qrcode');

            // ตรวจสอบว่ามีข้อมูลไหนเท่ากับ "ไม่พบข้อมูล"
            if (checkIn === 'ไม่พบข้อมูล' || cardcode === 'ไม่พบข้อมูล') {
                alert('ไม่สามารถ Assing งานได้เนื่องจากข้อมูลการเช็คอินไม่ครบถ้วน');
                $(this).prop('checked', false); // ยกเลิกการเช็ค
                return; // ไม่ดำเนินการต่อ
            }
            if ($(this).is(':checked')) {
                checkedIds.push({ id, th_name, checkIn, checkOut, cardcode });
            } else {
                checkedIds = checkedIds.filter(item => item.id !== id);
            }
            console.log(checkedIds);
        });
    } catch (error) {
        console.log('Error:', error);
    } finally {
        await countBody();
        $('.overlay').hide();
    }
}
$('#btnSubmit').on('click', async function () {
    $('#btnSubmit').hide(); // ซ่อนปุ่ม
    $('.overlay').show();
    const url = '../model/confirmReceiveEmp.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ checkedIds })
    };
    console.log(JSON.stringify({ checkedIds }));
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        alert(data.message)
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    } finally {
        $('.overlay').hide();
        $('#submitModal').modal('hide');
        $('#btnSubmit').show();
        await search();
    }
});
$('#btnCancel').on('click', async function () {
    $('#btnCancel').hide(); // ซ่อนปุ่ม
    $('.overlay').show();
    let mcName = $('#mcname').val();
    const url = '../model/rejectEmp.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({ checkedIds, mcName })
    };
    console.log(JSON.stringify({ checkedIds, mcName }));
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        alert(data.message)
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    } finally {
        $('.overlay').hide();
        $('#cancelModal').modal('hide');
        $('#btnCancel').show();
        await search();
    }
});
$('#shift').on('change', async function () {
    const selectedShift = $(this).val();
    $('.overlay').show();
    console.log(selectedShift);
    $('#bodyScrap tr').each(function () {
        const $row = $(this);
        // ดึงค่าจากคอลัมน์ startDate และ endDate (ตามลำดับคอลัมน์)
        const startDateText = $row.find('td').eq(4).text().trim(); // เปลี่ยน index ถ้าไม่ตรง
        const endDateText = $row.find('td').eq(5).text().trim();
        const matchedShift = matchShift(startDateText, endDateText);
        // ✅ ถ้าเลือก all ให้โชว์ทั้งหมด
        if (selectedShift === 'all' || matchedShift === selectedShift) {
            $row.show();
        } else {
            $row.hide();
        }
    });
    $('.overlay').hide();
    await countBody();
});
async function setShift() {
    let selectOptionShift = $('#shift');
    selectOptionShift.empty();
    selectOptionShift.append(`
        <option selected value="">เลือกกะ</option>
        <option value="all">ทั้งหมด</option>
        <option value="Day">Day</option>
        <option value="Night">Night</option>
        <option value="A">A</option>
        <option value="B">B</option>
        <option value="C">C</option>
        <option value="none">none</option>`);
}
function matchShift(startDateStr, endDateStr) {
    const start = new Date(startDateStr);
    const end = new Date(endDateStr);
    const startHour = start.getHours();
    const endHour = end.getHours();
    const endDay = end.getDate();
    const startDay = start.getDate();

    // A: check-in 06:00 - 10:00, check-out 16:00 - 18:59
    if (startHour >= 6 && startHour <= 10 && endHour >= 16 && endHour < 19 && endDay === startDay) {
        return 'A';
    }

    // B: check-in 15:00 - 18:00, check-out 00:00 - 08:00 (next day)
    if (startHour >= 15 && startHour <= 18 && endHour >= 0 && endHour <= 8 && endDay > startDay) {
        return 'B';
    }

    // C: check-in 23:00 - 01:00, check-out 08:00+ (next day)
    if ((startHour === 23 || startHour === 0 || startHour === 1) && endHour >= 8 && endDay > startDay) {
        return 'C';
    }

    // night: check-in 19:00 - 21:00, check-out 08:00+ (next day)
    if (startHour >= 19 && startHour <= 21 && endHour >= 8 && endDay > startDay) {
        return 'Night';
    }

    // Day: check-in 06:00 - 10:00, check-out 20:00+
    if (startHour >= 6 && startHour <= 10 && endHour >= 20 && endDay === startDay) {
        return 'Day';
    }

    return 'none';
}
$('#confirmEmp').on('click', comfirmEmp)
$('#confirmChangeEmp').on('click', comfirmChangeEmp)
$('#mcname').on('change', async function () {
    $('#bodyScrap').empty();
    $('#shift').val('');
    $('#workGroup').val('');
})

// ลบแถวเมื่อกดปุ่มลบ
$(document).on("click", ".btnDelete", function () {
    $(this).closest("tr").remove();
    updateRowNumbers();
});
async function cancelAssign(cardcode, th_name, checkin, jobassign) {
    cancelObj = {
        cardcode, th_name, checkin, jobassign, isSpecial: true
    }
    $('#cancelModal').modal('show');
}
async function setMatchine() {
    let selectOptionMc = $('#mcname');
    selectOptionMc.empty();
    let text = "";
    const searchParams = new URLSearchParams(window.location.search);
    let machinePr = searchParams.get('machine'); // price_descending
    text += `<option value="${machinePr}">${machinePr}</option>`;
    selectOptionMc.append(text);
}
async function comfirmChangeEmp() {
    let url = `../ model / UpdateWorkerToDoc.php`;
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
    let empsList = checkedIds;
    $('.overlay').show();
    let url = `../ model / insertWorkerToDoc.php`;
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
    const url = `../ model / getDocByVendor.php ? vendor = ${company} `;
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
                    text += `< tr tr class="text-center" >
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
                    </ > `;
                }
            });
            if (row == 0) {
                text = `< tr tr >
                    <td colspan="7">ไม่มีข้อมูล</td>
            </ > `
            }
            bodyTable.append(text);
            return;
        }
        text = `< tr tr >
                    <td colspan="7">ไม่มีข้อมูล</td>
        </ > `
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

                text2 += `< tr tr class="text-center" >
                    <td class="text-center justify-content-center" style="white-space: nowrap;">${element.emp_id}</td>
                        <td style="white-space: nowrap;">${element.emp_name}</td>
                        <td style="white-space: nowrap;">${element.work_location}</td>
                        <td style="white-space: nowrap;">${element.work_type}</td>
                        <td style="white-space: nowrap;">${element.dateStart.date.substring(0, 16)}</td>
                        <td style="white-space: nowrap;">${element.dateStop.date.substring(0, 16)}</td>  
                        <td style="white-space: nowrap;"><button onclick='ChangeEmp(${element.id})' class='btn btn-warning'>เปลี่ยนพนักงาน</button></td>     
                    </ > `;
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
                text += `< div div class="form-check" >
                    <input class="form-check-input" type="checkbox" value="" id="${worker.cardcode}">
                        <label class="form-check-label" for="${worker.cardcode}">
                            ${worker.th_name}
                        </label>
                    </>`;


                text2 += `< div div class="form-check" >
                    <input class="form-check-input" type="radio" name="exampleRadios" id="new${worker.cardcode}" value="${worker.cardcode}" checked>
                        <label class="form-check-label" for="new${worker.cardcode}">
                            ${worker.th_name}
                        </label>
                    </>`
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
            newEmp = $(`input[name = "exampleRadios"]: checked`).val();
            newEmpName = $(`input[name = "exampleRadios"]: checked`).next('label').text();
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
async function getJobType() {
    const url = '../model/getJobsByGroup.php';
    const headers = new Headers();
    const workGroup = $('#mcname').val();
    let selectWorkGroup = $('#workGroup');
    selectWorkGroup.empty();
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
        if (status == 200) {
            let text = '';
            data.forEach((element) => {
                text += `<option value="${element.job_id}">${element.work_name}</option>`;
            });
            selectWorkGroup.append(text);
        }
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
}
async function getAllJob() {
    const url = '../model/getJobs.php';
    const headers = new Headers();
    let selectWorkGroup = $('#workGroup');
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'GET',
    };
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        if (status == 200) {
            let text = '';
            data.forEach((element) => {
                text += `<option value="${element.job_id}">${element.work_name}</option>`;
            });
            selectWorkGroup.append(text);
        }
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
}
async function countBody() {
    // ✅ นับจำนวนแถวที่แสดงอยู่
    console.log("Counting rows...");
    const visibleRowCount = $('#bodyScrap tr:visible').length;
    $('#rowCount').text(visibleRowCount);
}