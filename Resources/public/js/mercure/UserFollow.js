export const AddInList = (container, data) => {
    let list = container.querySelector('ul.user-follow-list')

    if (null === list) {
        list = document.createElement('ul')
        list.classList.add('user-follow-list')

        let alert = container.querySelector('.alert-danger')

        alert.replaceWith(list)
    }

    list.innerHTML = data.html + list.innerHTML

    RefreshListLength(container)
}

export const RemoveInList = (container, slug) => {
    let item = container.querySelector('li[data-user-slug="' + slug +'"]')

    item.remove()

    RefreshListLength(container)
}

export const RefreshListLength = (container) => {
    let length = container.querySelectorAll('li').length

    if (0 === length) {
        let alert = document.createElement('div')
        alert.classList.add('alert', 'alert-danger', 'mb-0')

        let p = document.createElement('p')
        p.classList.add('mb-0')
        p.innerText = container.getAttribute('data-empty-message')

        alert.appendChild(p)
        container.appendChild(alert)

        let ul = container.querySelector('ul')
        ul.remove()
    }

    let badge = document.querySelector('#' + container.id + '-length')

    badge.innerText = String(length)
}

export const EventSourceListener = (eventSource) => {
    eventSource.addEventListener('user_follow', (e) => {
        let data = JSON.parse(e.data)
        let elements = document.querySelectorAll('.user-follow[data-user-slug="' + data['user-slug'] + '"]')

        elements.forEach((element) => {
            if (true === data.isFollowed) {
                element.classList.replace('btn-red', 'btn-secondary')
            } else {
                element.classList.replace('btn-secondary', 'btn-red')
            }

            element.innerHTML = '<i class="' + data.ico + '"></i> ' + data.title
        })
    }, false)

    eventSource.addEventListener('user_follow_page_follower_add', (e) => {
        let data = JSON.parse(e.data)
        let userFollowFollowerContainer = document.querySelector('#user-follow-follower')

        if (null !== userFollowFollowerContainer) {
            AddInList(userFollowFollowerContainer, data)
        }
    }, false)

    eventSource.addEventListener('user_follow_page_followed_add', (e) => {
        let data = JSON.parse(e.data)
        let userFollowFollowedContainer = document.querySelector('#user-follow-followed')

        if (null !== userFollowFollowedContainer) {
            AddInList(userFollowFollowedContainer, data)
        }
    }, false)

    eventSource.addEventListener('user_follow_page_follower_remove', (e) => {
        let data = JSON.parse(e.data)
        let userFollowFollowerContainer = document.querySelector('#user-follow-follower')

        if (null !== userFollowFollowerContainer) {
            RemoveInList(userFollowFollowerContainer, data.slug)
        }
    }, false)

    eventSource.addEventListener('user_follow_page_followed_remove', (e) => {
        let data = JSON.parse(e.data)
        let userFollowFollowedContainer = document.querySelector('#user-follow-followed')

        if (null !== userFollowFollowedContainer) {
            RemoveInList(userFollowFollowedContainer, data.slug)
        }
    }, false)
}
