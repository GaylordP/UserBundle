let follows = document.querySelectorAll('.user-follow')

follows.forEach(function(element) {
    let _this = element

    element.onclick = (link) => {
        link.preventDefault()

        let httpRequest = new XMLHttpRequest()
        httpRequest.open('GET', _this.getAttribute('href'))
        httpRequest.setRequestHeader('X-Requested-With', 'XMLHttpRequest')
        httpRequest.send()
    }
})
