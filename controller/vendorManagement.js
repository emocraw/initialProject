$(document).ready(app);
let scrap_data = [];
let bodyScrap;
let columnTable;
let jsonData = {};

const rowArrayChecked = [];
async function app() {
    await getReport();
    $('.overlay').hide();
    await getVendors();
    $('.vendor-select').select2();
    // getDoc();
    // scrap_data = await getScrapt();
    // setTable();
}

$("#addBtn").on("click", function () {
    // สร้างแถวใหม่
    var newRow = `
        <tr>
            <td class="text-center justify-content-center">1</td>
            <td>
                <select class="form-select" aria-label="Default select example">
                    <option selected>เลือกทักษะของพนักงาน</option>
                    <option value="1">One</option>
                    <option value="2">Two</option>
                    <option value="3">Three</option>
                </select>
            </td>
            <td>
                <input type="text" class="form-control" placeholder="ระบุตัวเลขจำนวนคน" 
                    aria-label="Recipient's username" aria-describedby="button-addon2">
            </td>
            <td>
                <input type="date" class="form-control" aria-describedby="button-addon2">
            </td>
            <td>
                <input type="date" class="form-control" aria-describedby="button-addon2">
            </td>
            <td>
                <button class="btn btn-danger btnDelete">ลบ</button>
            </td>
        </tr>
    `;
    // เพิ่มแถวใหม่ลงใน tbody
    $("#bodyScrap").append(newRow);

    // อัพเดตหมายเลขลำดับใหม่
    updateRowNumbers();
});
$('#btnSubmit').on('click', async function () {
    $('.overlay').show();
    let dataArray = [];
    const rows = document.querySelectorAll("#bodyReport tr");
    const getData = async () => {
        for (const row of rows) {
            const rowId = row.querySelector("td:nth-child(1)").textContent.trim();
            const documentNumber = row.querySelector("td:nth-child(2)").textContent.trim();
            const jobType = row.querySelector("td:nth-child(4)").textContent.trim();
            const selectElement = row.querySelector("td:nth-child(8) select"); // เลือก <select> ในคอลัมน์ที่ต้องการ
            const contractorName = selectElement ? selectElement.value : "ไม่มีการเลือก";
            if (contractorName != "เลือก Vendor") {
                dataArray.push({ rowId, documentNumber, jobType, contractorName });
                // หากมีงาน async เช่น fetch หรืออื่นๆ ใส่ await ตรงนี้ได้
                await new Promise(resolve => setTimeout(resolve, 10)); // ตัวอย่างดีเล็กน้อย
            }
        }
        // ทำงานถัดไปที่ต้องการได้เลย เช่น เรียกฟังก์ชันต่อไป
    };
    await getData();
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
        let bodyScrap = $('#bodyScrap');
        let text = '';
        let text2 = '';
        bodyTable.empty();
        bodyScrap.empty();
        console.log(data);
        if (data.length > 0) {
            data.forEach((element) => {
                if (!element.vendor_assign && element.request_worker_doc_status == 'open') {
                    text += `<tr class="text-center">
                    <td class="text-center justify-content-center" style="white-space: nowrap;">${element.id}</td>
                    <td class="text-center justify-content-center" style="white-space: nowrap;">${element.request_worker_doc}</td>
                        <td style="white-space: nowrap;">${element.work_location}</td>
                        <td style="white-space: nowrap;">${element.work_type}</td>
                        <td style="white-space: nowrap;">${element.worker_require}</td>
                        <td style="white-space: nowrap;">${element.work_startDate.date.substring(0, 16)}</td>
                        <td style="white-space: nowrap;">${element.work_endDate.date.substring(0, 16)}</td>
                        <td style="white-space: nowrap;"><select class="vendor-select" aria-label="Default select example">
                            <option selected>เลือก Vendor</option>
                        </select></td>               
                    </tr>`;
                }

                /// table ล่าง
                if (element.vendor_assign && element.request_worker_doc_status == 'open') {
                    text2 += `<tr class="text-center">
                    <td class="text-center justify-content-center" style="white-space: nowrap;">${element.id}</td>
                    <td class="text-center justify-content-center" style="white-space: nowrap;">${element.request_worker_doc}</td>
                        <td style="white-space: nowrap;">${element.work_location}</td>
                        <td style="white-space: nowrap;">${element.work_type}</td>
                        <td style="white-space: nowrap;">${element.worker_require}</td>
                        <td style="white-space: nowrap;">${element.work_startDate.date.substring(0, 16)} - ${element.work_endDate.date.substring(0, 16)}</td>
                        <td style="white-space: nowrap;">${element.vendor_assign}</td>           
                        <td style="white-space: nowrap;">
                        <button onclick="canCel('${element.id}')" class='btn btn-danger'>ยกเลิก</button>
                        <button onclick="closeDoc('${element.request_worker_doc}','${element.work_type}')" class='btn btn-secondary'>ปิดงาน</button>
                        </td>        
                        <td style="white-space: nowrap;"></td>                    
                    </tr>`;
                }
            });
            bodyTable.append(text);
            bodyScrap.append(text2);
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
async function canCel(rowId) {
    const url = '../model/cancelVendor.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify({
            rowId
        })
    };
    console.log(JSON.stringify({
        rowId,
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
async function getVendors() {
    const url = '../model/getCompVendor.php';
    const requestOptions = {
        method: 'GET',
    };
    $('.overlay').show();
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        let vendorSelec = $('.vendor-select');
        let text = '';
        if (data.length > 0) {
            for (const company of data) {
                text += `<option value="${company.company}">${company.company}</option>`;
            }
        }
        vendorSelec.each(function () {
            $(this).append(text);
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