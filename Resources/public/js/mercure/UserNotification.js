import $ from 'jquery'

$(document).ready(function () {
    $('.dropdown ').on('show.bs.dropdown', function () {
        $.ajax({
            url: '/user/notification-read',
            dataType: 'json',
            complete: function(xhr) {
            },
        })
    })
})

export const EventSourceListener = (eventSource) => {
    eventSource.addEventListener('user_notification', function(e) {
        let data = JSON.parse(e.data)

        /*
            Notification navbar
         */
        let navbarContainer = document.querySelector('div.dropdown-menu.notification-list')
        navbarContainer.innerHTML = data.notificationNavbarHtml + navbarContainer.innerHTML

        /*
            Notification page list
         */
        let pageContainer = document.querySelector('ul.notification-list')
        if (null !== pageContainer) {
            pageContainer.innerHTML = data.notificationHtml + pageContainer.innerHTML
        }
    }, false)

    eventSource.addEventListener('user_notification_delete', function(e) {
        let data = JSON.parse(e.data)
        let elements = document.querySelectorAll('.user-notification[data-user-notification-id="' + data.id + '"]')

        elements.forEach(function(element) {
            element.remove()
        })
    }, false)

    eventSource.addEventListener('user_notification_unread_length', function(e) {
        let data = JSON.parse(e.data)
        let badges = document.querySelectorAll('.badge-user-notification-unread')

        badges.forEach(function(badge) {
            badge.innerText = data.length
        })
    }, false)
}
