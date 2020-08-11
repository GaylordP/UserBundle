import $ from 'jquery'

$(document).ready(() => {
    $('#navbarDropdownUserNotificationContainer ').on('show.bs.dropdown', () => {
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
        let length = data.length
        let title = document.querySelector('title')
        let lastUnreadLength = parseInt(title.getAttribute('data-notification-unread-length')) + parseInt(title.getAttribute('data-message-unread-length'))

        title.setAttribute('data-notification-unread-length', String(length))

        badges.forEach((badge) => {
            if (0 === length) {
                if (badge.classList.contains('badge-red')) {
                    badge.classList.remove('badge-red')
                    badge.classList.add('badge-secondary')
                }
            } else {
                if (badge.classList.contains('badge-secondary')) {
                    badge.classList.remove('badge-secondary')
                    badge.classList.add('badge-red')
                }
            }

            badge.innerText = length
        })

        let nowUnreadLength = parseInt(title.getAttribute('data-notification-unread-length')) + parseInt(title.getAttribute('data-message-unread-length'))

        if (0 === nowUnreadLength) {
            if (0 !== lastUnreadLength) {
                title.innerHTML = title.innerHTML.replace('(' + String(lastUnreadLength) + ') ', '')
            }
        } else {
            if (0 === lastUnreadLength) {
                title.innerHTML = '(' + String(nowUnreadLength) + ') ' + title.innerHTML
            } else {
                title.innerHTML = title.innerHTML.replace('(' + String(lastUnreadLength) + ')', '(' + String(nowUnreadLength) + ')')
            }
        }
    }, false)
}
