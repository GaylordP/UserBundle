document.addEventListener('click', (e) => {
    let follow = e.target.closest('.user-follow')

    if (null !== follow) {
        e.preventDefault()

        let httpRequest = new XMLHttpRequest()
        httpRequest.open('GET', follow.getAttribute('href'))
        httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
        httpRequest.send()
    }
}, false)

document.addEventListener('click', (e) => {
    let follow = e.target.closest('.user-follow-page')

    if (null !== follow) {
        e.preventDefault()

        let httpRequest = new XMLHttpRequest()
        httpRequest.open('GET', follow.getAttribute('href'))
        httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
        httpRequest.send()
    }
}, false)