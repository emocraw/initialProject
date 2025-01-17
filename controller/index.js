$(document).ready(app);
let scrap_data = [];
let bodyScrap;
let columnTable;
let jsonData = {};
const rowArrayChecked = [];
async function app() {
    getDoc();
    scrap_data = await getScrapt();
    setTable();
}
async function toBase64(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => resolve(reader.result.split(',')[1]);
        reader.onerror = error => reject(error);
    });
}
$("#btnSubmit").click(async function () {
    $("#btnSubmit").prop('disabled', true);
    $('.overlay').show();
    for (let element of rowArrayChecked) {
        let location = $(`#location${element.Code}`).val();
        let imageFileElement = $(`#image${element.Code}`)[0];
        console.log(element.Code);
        let imageFile = imageFileElement ? imageFileElement.files[0] : null;
        let base64Image = "";
        if (!imageFile) {
            alert("ไม่มีไฟล์รูปภาพ");
            $("#btnSubmit").prop('disabled', false);
            $('#submitModal').modal('hide');
            $(`#image${element.Code}`).css('border', '2px solid red');
            $('.overlay').hide();
            return;
        }
        base64Image = imageFile ? await toBase64(imageFile) : "";
        if (location === "") {
            alert("กรุณาระบุสถาณที่");
            $("#btnSubmit").prop('disabled', false);
            $('#submitModal').modal('hide');
            $(`#image${element.Code}`).css('border', '2px solid red');
            $('.overlay').hide();
            return;
        }
        element.location = location;
        element.image = base64Image;
        element.docNo = $('#docNo').text();
    }
    console.log(rowArrayChecked);
    if (rowArrayChecked.length > 0) {
        const url = '../model/insertSellRequest.php';
        const headers = new Headers();
        headers.append('Content-Type', 'application/json');
        const requestOptions = {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(rowArrayChecked)
        };
        console.log(JSON.stringify(rowArrayChecked));
        try {
            // Disable the button to prevent double-clicking
            const response = await fetch(url, requestOptions);
            const data = await response.json();
            // Process the received data
            if (response.ok) {
                alert("บันทึกสำเร็จ");
                window.location.reload();
                $('#submitModal').modal('hide');
            } else {
                alert("Error: " + data.message);
            }
        } catch (error) {
            // Handle any errors
            alert("Error: " + error.message);
        } finally {
            // Re-enable the button
            $("#btnSubmit").prop('disabled', false);
            $('.overlay').hide();
        }
    }
});
async function getScrapt() {
    const url = '../model/get_scrap.php';
    const requestOptions = {
        method: 'GET',
    };
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        console.log(data);
        // Process the received data
        $('.overlay').hide();
        return data;
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
        $('.overlay').hide();
    }
    return;
}
async function setTable() {
    bodyScrap = $('#bodyScrap');
    columnTable = $('thead');
    let text = "";
    bodyScrap.empty();
    scrap_data.forEach(scrap => {
        text += `
        <tr>
            <td>${scrap.Id}</td>
            <td>${scrap.Description}</td>
            <td>${scrap.Sell_qty}</td>
            <td>${scrap.unit}</td>
            <td>${scrap.Price_unit}</td>
            <td>${scrap.Vendor}</td>
            <td><input class="form-check-input" type="checkbox" onclick='check(${scrap.Id})' id="checkbox${scrap.Id}"></td>
            <td><input style="width: 100px; display:none" id='location${scrap.Id}' type='text' placeholder="ระบุสถาณที่" class="form-control"></input></id>
            <td><input style="display:none" id='image${scrap.Id}' type="file" id="upload${scrap.Id}" accept="image/*"></td>
        </tr>
      `;
    });
    bodyScrap.append(text);
}
function findRowByFirstColumn(value) {
    let foundRow = null;
    // Iterate through all rows in the table body
    $('table tbody tr').each(function () {
        // Check the content of the first column (assumed to be <th>)
        let firstColumnText = $(this).find('td').eq(0).text().trim();
        if (firstColumnText === value.toString()) {
            foundRow = $(this); // Store the row if it matches
            return false; // Exit the loop
        }
    });

    return foundRow; // Return the matched row or null if not found
}
function check(rowId) {
    // Usage example
    let valueCheck = $(`#checkbox${rowId}`);
    const locationInput = $(`#location${rowId}`);
    const image = $(`#image${rowId}`);
    console.log(valueCheck);
    if (valueCheck.prop('checked')) {
        locationInput.show();
        image.show();
        let row = findRowByFirstColumn(rowId);
        if (row) {
            jsonData = {};
            row.find('td').each(function (index) {
                let columnName = columnTable.find('th').eq(index).text().trim();
                jsonData[columnName] = $(this).text().trim();
            });
            rowArrayChecked.push(jsonData);
        } else {
            console.log("Row not found");
        }
        console.log(rowArrayChecked);
    } else {
        locationInput.hide();
        image.hide();
        let rowIndex = rowArrayChecked.findIndex(row => row.รหัส == rowId);
        if (rowIndex !== -1) {
            rowArrayChecked.splice(rowIndex, 1);
        }
    }
}
async function getDoc() {
    const url = '../model/getdoc.php';
    const requestOptions = {
        method: 'GET',
    };
    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();
        // Process the received data
        $('#docNo').text(data);
        console.log(data);
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }

}