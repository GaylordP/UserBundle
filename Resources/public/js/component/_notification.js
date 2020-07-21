import $ from 'jquery'

$(document).ready(function () {
    $('#navbarDropdownUserNotificationContainer').on('show.bs.dropdown', function () {
        $('#navbarDropdownUserNotificationContainer').tooltip('hide')
    })
})
