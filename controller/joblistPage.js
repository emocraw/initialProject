
$(document).ready(app);
let groupMachine;
async function app() {
    // อัพเดทสี Navbar ตาม ID 
    let url = window.location.href;
    let pageName = url.split('/').pop().split('.').shift();
    await job();
    let groupMachine = await group();
    $(`#${pageName}`).addClass('active');


    // Action when the user clicks on the button with class 'btn-outline-warning'
    $('#bodyJoblists').on('click', '.btn-outline-warning', function () {
        console.log('Button clicked!');
        // Get the closest row and access any relevant data
        const row = $(this).closest('tr');
        // Optional: Remove highlight from all rows first (if you only want one highlighted at a time)
        $('#bodyJoblists tr').removeClass('table-success');
        // Add success highlight to the clicked row
        row.addClass('table-success');

        const jobId = row.find('td').eq(0).text(); // example: get work_name from the first td
        const workName = row.find('td').eq(1).text(); // example: get work_name from the first td
        const price = row.find('td').eq(2).text();
        const group = row.find('td').eq(3).text();
        let selectGroup = $('#groupMachineModal');
        selectGroup.empty(); // Clear previous options
        let text = '';
        for (const element of groupMachine) {
            if (group === element.group_name) {
                text += `<option value="${element.group_id}" selected>${element.group_name}</option>`;
            } else {
                text += `<option value="${element.group_id}">${element.group_name}</option>`;
            }
        }
        // Append the default option
        selectGroup.append(text);
        // Set the values in the modal inputs
        $('#jobIdModal').text(jobId);
        $('#jobdescModal').val(workName);
        $('#priceModal').val(price);

        // Open edit job modal
        $('#editJobModal').modal('show');
        // Perform any action you want with the data
    });
    // Action when the user clicks on the button with id 'editJobBtn'
    $('#editJobBtn').on('click', async function () {
        const jobId = $('#jobIdModal').text();
        const workName = $('#jobdescModal').val();
        const price = $('#priceModal').val();
        const group_id = $('#groupMachineModal').val();
        const group_text = $('#groupMachineModal option:selected').text();
        // const group = $('#groupMachine').val();
        const url = '../model/updateJobPrice.php';
        const headers = new Headers();
        headers.append('Content-Type', 'application/json');
        const requestData = {
            // Request body data
            jobId,
            workName,
            price,
            group_id,
            group_text
        };
        const requestOptions = {
            method: 'POST',
            headers: headers,
            body: JSON.stringify(requestData)
        };
        try {
            const response = await fetch(url, requestOptions);
            const status = response.status;
            const data = await response.json();
            if (status == 200) {
                alert(data.message);
                $('#editJobModal').modal('hide');
                await job();
            } else {
                alert(data.message);
            }
        } catch (error) {
            // Handle any errors
            console.log('Error:', error);
        } finally {
            $('.overlay').hide();
        }
    });
    $('.overlay').hide();
}
const job = async (params) => {
    $('.overlay').show();
    const url = '../model/getJobs.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'GET',
    };
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        if (status == 200) {
            let html = '';
            data.forEach((item) => {
                html += `<tr>
                <td>${item.job_id}</td>
                    <td>${item.work_name}</td>
                    <td>${item.Prices_manpower}</td>
                    <td>${item.group_machine ? item.group_machine : 'ไม่ได้กำหนด'}</td>
                    <td>${item.create_date.date.substring(0, 16)}</td>
                    <td>${item.updateTime ? item.updateTime.date.substring(0, 16) : item.create_date.date.substring(0, 16)}</td>
                    <td><button class='btn btn-outline-warning'>Update</button></td>
                    </tr>`;
            });
            $('#bodyJoblists').html(html);
            return;
        } else {
            alert(data.message);
            return;
        }
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    } finally {
        $('.overlay').hide();
    }
    return data;
}
const group = async (params) => {
    $('.overlay').show();
    const url = '../model/getGroup.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    const requestOptions = {
        method: 'GET',
    };
    try {
        const response = await fetch(url, requestOptions);
        const status = response.status;
        const data = await response.json();
        console.log(data);
        if (status == 200) {
            return data;
        } else {
            alert(data.message);
            return;
        }
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    } finally {
        $('.overlay').hide();
    }
    return data;
}