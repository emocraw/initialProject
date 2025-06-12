$(document).ready(app);
async function app() {
    $('.overlay').hide();
}
$('#logInBtn').on('click', login)
async function login() {
    $('.overlay').show();
    localStorage.clear();
    const url = '../model/login.php';
    const headers = new Headers();
    headers.append('Content-Type', 'application/json');
    let username = $('#username').val();
    let password = $('#password').val();
    const requestData = {
        // Request body data
        username,
        password
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
        if (status != 200) {
            $('#error-msg').show();
            $('.overlay').hide();
            return;
        }
        switch (data.UserInfo.Type) {
            case "safety":
                localStorage.setItem('token', data.UserInfo.Token);
                localStorage.setItem('type', data.UserInfo.Type)
                localStorage.setItem('user', data.UserInfo.ID)
                window.location.href = 'vendorManagement.php';
                break;
            case "purchaser":

                localStorage.setItem('type', data.UserInfo.Type)
                window.location.href = 'joblistPage.php';
                break;
            default:
                localStorage.setItem('token', data.UserInfo.Token);
                localStorage.setItem('type', data.UserInfo.Type)
                localStorage.setItem('user', data.UserInfo.ID)
                localStorage.setItem('machine', JSON.stringify(data.UserInfo.machine))
                window.location.href = 'empManagement.php';
                break;
        }
    } catch (error) {
        // Handle any errors
        console.log('Error:', error);
    }
}