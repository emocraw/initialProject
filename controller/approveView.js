$('document').ready(app);
let allDoc = [];
async function app() {
    allDoc = await getAllDoc(); // ดึงข้อมูลเอกสารทั้งหมดจากฐานข้อมูล
    if (allDoc) {
        ShowDoc();
    }
    // await setTable(allDoc); // กำหนดข้อมูลในตาราง
    $('.overlay').hide(); // ซ่อน overlay
}

async function getAllDoc() {
    const url = '../model/getAllDoc.php';
    const requestOptions = {
        method: 'GET',
    };
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        console.log(data);
        $('.overlay').hide();
        return data;
    } catch (error) {
        console.log('Error:', error);
        $('.overlay').hide();
    }
    return [];
}
async function ShowDoc() {
    let listDoc = $('#listDoc');
    let html = "";
    listDoc.empty();
    html += `<div class="col-12">
                <h5 class="text-secondary">Doc:<span id="docNo${allDoc[0].doc_id}">${allDoc[0].doc_id}</span><span class="text-warning">รออนุมัติ</span></h5>
            </div>`;
    console.log(allDoc);
    listDoc.append(html);
}
async function setTable(allDoc) {
    bodyDoc = $('#bodyAlldoc');
    columnTable = $('thead'); // this can be used to reference the headers but adjust within each row context
    let text = "";
    bodyDoc.empty();

    allDoc.forEach(doc => {
        let locationValue = doc.location || ''; // ค่า location จากฐานข้อมูล
        let fileName = doc.image_name.split('\\').pop(); // ดึงชื่อไฟล์จาก image_name
        let fileNameDisplay = $(`#imageNameDisplay${doc.id}`); // เลือก span ที่จะใช้แสดงชื่อไฟล์
        fileNameDisplay.text(fileName); // แสดงชื่อไฟล์ใน span
        text += `
        <tr id="row-${doc.id}">
            <td>${doc.scarp_code}</td>
            <td>${doc.scrap_name}</td>
            <td>${doc.sell_qty}</td>
            <td>${doc.unit}</td>
            <td>${doc.price}</td>
            <td>${doc.vendor_name}</td>
            <td><input class="form-check-input" type="checkbox" onclick='check(${doc.id})' id="checkbox${doc.id}"></td>
            <td><input style="width: 100px;" id='location${doc.id}' type='text' value="${locationValue}" placeholder="ระบุสถานที่" class="form-control" readonly></td>
            <td>
                <!-- input type="file" สำหรับเลือกไฟล์ใหม่ (disabled) -->
                <input style="display:block" id='image${doc.id}' type="file" accept="image/*" disabled>

                <!-- span แสดงชื่อไฟล์เก่าจากฐานข้อมูล -->
                <span id="imageNameDisplay${doc.id}">${fileName}</span>
            </td>
        </tr>
      `;
    });

    bodyDoc.append(text);
    allDoc.forEach(doc => handleImageSelection(doc.id));
}

function handleImageSelection(docId) {
    const imageInput = $(`#image${docId}`);
    const fileNameDisplay = $(`#imageNameDisplay${docId}`);

    // เมื่อเลือกไฟล์ใหม่
    imageInput.on('change', function () {
        if (this.files.length > 0) {
            // อัปเดตชื่อไฟล์ใหม่ที่แสดงใน span
            const newFileName = this.files[0].name;
            fileNameDisplay.text(newFileName).show(); // แสดงชื่อไฟล์ใหม่ใน span
            fileNameDisplay.hide();
        } else {
            // ถ้าไม่มีไฟล์ที่เลือก แสดงชื่อไฟล์เก่า
            fileNameDisplay.show();
        }
    });
}


function findRowById(rowId) {
    return $(`#row-${rowId}`); // Dynamically find the row based on rowId (unique per row)
}

let rowArrayChecked = [];

async function check(rowId) {
    let valueCheck = $(`#checkbox${rowId}`);
    const locationInput = $(`#location${rowId}`);
    const image = $(`#image${rowId}`);
    const imageinput = $(`#imageNameDisplay${rowId}`);
    if (valueCheck.prop('checked')) {
        // Enable editing when checked
        locationInput.prop('readonly', false);
        image.prop('disabled', false);
        image.show();

        let row = findRowById(rowId); // Use the new method to find the row based on rowId
        if (row.length > 0) { // Check if row is found
            let jsonData = {};
            jsonData["Location"] = locationInput.val().trim(); // Adding locationInput value
            jsonData["Image"] = imageinput.text().trim();
            jsonData["Id"] = rowId;
            jsonData["Base64Image"] = "";

            // Check if the row is already in the array before adding it
            if (!rowArrayChecked.some(row => row["id"] === rowId)) {
                rowArrayChecked.push(jsonData); // Add row data to array
            }
        } else {
            console.log("Row not found");
        }

    } else {
        // Disable editing when unchecked
        locationInput.prop('readonly', true);
        image.prop('disabled', true);

        // Find the index of the row with the given rowId
        let rowIndex = rowArrayChecked.findIndex(row => row["id"] === rowId);
        if (rowIndex !== -1) {
            // Remove the row from the array
            rowArrayChecked.splice(rowIndex, 1);
        }
    }

    // Log the updated array once after each check or uncheck
    console.log('RowArrayChecked:', rowArrayChecked);
}


async function getDoc() {
    const url = '../model/getdoc.php';
    const requestOptions = {
        method: 'GET',
    };
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        $('#docNo').text(data);
        console.log(data);
    } catch (error) {
        console.log('Error:', error);
    }
}

async function toBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result.split(',')[1]);
        reader.onerror = error => reject(error);
    });
}

// Update data function
async function updateData() {
    $("#btnSubmit").prop('disabled', true);
    $('.overlay').show();
    for (let element of rowArrayChecked) {
        let location = $(`#location${element.id}`).val();
        let imageFileElement = $(`#image${element.id}`)[0];
        let imageFile = imageFileElement ? imageFileElement.files[0] : null;
        let base64Image = "";

        // ถ้ามีไฟล์ให้แปลงเป็น Base64
        base64Image = imageFile ? await toBase64(imageFile) : "";

        // ถ้ามีไฟล์ให้เก็บชื่อไฟล์ใน element.Image
        if (imageFile) {
            element.Image = imageFile.name; // เก็บชื่อไฟล์ใน element.Image
        }

        // ตรวจสอบสถานที่
        if (location === "") {
            alert("กรุณาระบุสถาณที่");
            $("#btnSubmit").prop('disabled', false);
            $('#submitModal').modal('hide');
            $('.overlay').hide();
            return;
        }

        element.Location = location;
        element.base64Image = base64Image;
    }
    console.log(rowArrayChecked);
    $('.overlay').hide();


    // ส่งข้อมูลไปที่เซิร์ฟเวอร์
    // if (rowArrayChecked.length > 0) {
    //     const url = '../model/updateSellRequest.php';
    //     const headers = new Headers();
    //     headers.append('Content-Type', 'application/json');
    //     const requestOptions = {
    //         method: 'POST',
    //         headers: headers,
    //         body: JSON.stringify(rowArrayChecked)
    //     };

    //     try {
    //         const response = await fetch(url, requestOptions);
    //         const data = await response.json();
    //         if (response.ok) {
    //             alert("อัพเดตสำเร็จ");
    //             window.location.reload();
    //             $('#submitModal').modal('hide');
    //         } else {
    //             alert("Error: " + data.message);
    //         }
    //     } catch (error) {
    //         alert("Error: " + error.message);
    //     } finally {
    //         $("#btnSubmit").prop('disabled', false);
    //         $('.overlay').hide();
    //     }
    // }
}

// Add an event listener to the update button
$("#btnSubmit").click(async function () {
    updateData();
});

