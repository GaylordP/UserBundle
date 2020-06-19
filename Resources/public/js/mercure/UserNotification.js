import $ from 'jquery'

$(document).ready(() => {
    $('.dropdown ').on('show.bs.dropdown', () => {
        $.ajax({
            url: '/user/notification-read',
            dataType: 'json',
            complete: (xhr) => {
            },
        })
    })
})

export const EventSourceListener = (eventSource) => {
    eventSource.addEventListener('user_notification', (e) => {
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

    eventSource.addEventListener('user_notification_delete', (e) => {
        let data = JSON.parse(e.data)
        let elements = document.querySelectorAll('.user-notification[data-user-notification-id="' + data.id + '"]')

        elements.forEach((element) => {
            element.remove()
        })
    }, false)

    eventSource.addEventListener('user_notification_unread_length', (e) => {
        let data = JSON.parse(e.data)
        let badges = document.querySelectorAll('.badge-user-notification-unread')

        badges.forEach((badge) => {
            badge.innerText = data.length
        })
    }, false)
}