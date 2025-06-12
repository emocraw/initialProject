
$(document).ready(app);
async function app() {
    await getWorkGroup();
    $('.overlay').hide();
    $('#submitBtn').on('click', async function () {
        $('#submitModal').modal('show');
    });
    $('#btnSubmit').on('click', async function () {
        $('.overlay').show();
    });
}
async function getWorkGroup() {
    $('.overlay').show();
    let selectOptionWorkGroup = $('#workGroup');
    let text = "";
    selectOptionWorkGroup.empty();
    const url = '../model/getWorkGroup.php';
    const requestOptions = {
        method: 'GET',
    };
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        // Process the received data
        if (status == 200) {
            text += `<option selected value="">เลือกกลุ่มงาน</option>`;
            for (const element of data) {
                text += `
                <option value="${element.group_name}">${element.group_name}</option>
                `;
            }
            selectOptionWorkGroup.append(text);
        } else {
            selectOptionWorkGroup.append(`<option selected value="">ไม่พบข้อมูล</option>`);
        }
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    } finally {
        $('.overlay').hide();
    }
}
const insertJob = async () => {
    let workGroup = $('#workGroup').val();
    let workName = $('#jobName').val();
    let price = $('#price').val();
    const url = '..model/insertJob.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestData = {
        // Request body data
        workGroup,
        workName,
        price
    };

    const requestOptions = {
        method: 'POST',
        headers: headers,
        body: JSON.stringify(requestData)
    };

    try {
        const response = await fetch(url, requestOptions);
        const data = await response.json();

        // Process the received data
        console.log(data);



    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }


}